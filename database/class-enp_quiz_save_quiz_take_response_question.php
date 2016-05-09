<?php
/**
 * Save responses from people taking quizzes
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/includes
 *
 *
 * @since      0.0.1
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */

class Enp_quiz_Save_quiz_take_Response_question extends Enp_quiz_Save_quiz_take {
    public static $response;

    public function __construct() {

    }


    protected function validate_response_data($response) {
        // find out if their response is correct or not
        if($response['question_type'] === 'mc') {
            $valid = $this->validate_mc_option_response($response);
            if($valid === true) {
                // see if the mc option is correct or not
                $response['response_correct'] = $this->is_mc_option_response_correct($response);
            }
        }
        return $response;
    }

    protected function validate_mc_option_response($response) {
        $valid = false;
        // validate that this ID is attached to this question
        $question = new Enp_quiz_Question($response['question_id']);
        $question_mc_options = $question->get_mc_options();
        // see if the id of the mc option they submitted is actually IN that question's mc option ids
        if(in_array($response['question_response'], $question_mc_options)) {
            $valid = true;
        } else {
            // TODO Handle errors?
            // not a legit mc option response
            var_dump('Selected option not allowed.');
        }
        return $valid;
    }

    protected function is_mc_option_response_correct($response) {
        // it's legit! see if it's right...
        $mc = new Enp_quiz_MC_option($response['question_response']);
        // will return 0 if wrong, 1 if right. We don't care if
        // it's right or not, just that we KNOW if it's right or not
        $response_correct = $mc->get_mc_option_correct();

        return $response_correct;
    }

    /**
    * Connects to DB and inserts the user response.
    * @param $response (array) data we'll be saving to the response table
    * @return builds and returns a response message
    */
    public function insert_response_question($response) {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':response_quiz_id'      => $response['response_quiz_id'],
                        ':question_id'      => $response['question_id'],
                        ':question_viewed'      => 1,
                        ':response_question_created_at'=> $response['response_quiz_updated_at'],
                        ':response_question_updated_at'=> $response['response_quiz_updated_at'],
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->response_question_table." (
                                            response_quiz_id,
                                            question_id,
                                            question_viewed,
                                            response_question_created_at,
                                            response_question_updated_at
                                        )
                                        VALUES(
                                            :response_quiz_id,
                                            :question_id,
                                            :question_viewed,
                                            :response_question_created_at,
                                            :response_question_updated_at
                                        )";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // add our response ID to the array we're working with
            $response['response_question_id'] = $pdo->lastInsertId();
            // set-up our response array
            $return = array(
                                        'response_question_id' => $response['response_question_id'],
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );

            // merge the response arrays
            $return = array_merge($response, $return);

        } else {
            // handle errors
            $return['error'] = 'Save response failed.';
        }
        // return response
        return $return;
    }


    public function update_response_question($response) {
        $response = $this->validate_response_data($response);
        // Select the response we need to update
        $response_question_id = $this->get_response_question_id($response);

        // Get our Parameters ready
        $params = array(':response_question_id'=> $response_question_id,
                        ':question_responded'=> '1',
                        ':response_correct'  => $response['response_correct'],
                        ':response_question_updated_at'=> $response['response_quiz_updated_at'],
                    );

        // connect to PDO
        $pdo = new enp_quiz_Db();

        // write our SQL statement
        $sql = "UPDATE ".$pdo->response_question_table."
                   SET  question_responded = :question_responded,
                        response_correct = :response_correct,
                        response_question_updated_at = :response_question_updated_at
                 WHERE  response_question_id = :response_question_id";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // set-up our response array
            $return = array(
                                        'response_question_id' =>$response_question_id,
                                        'status'       => 'success',
                                        'action'       => 'update'
                                );

            // merge the response arrays
            $return = array_merge($response, $return);
            // see what type of question we're working on and save that response
            if($response['question_type'] === 'mc') {
                // save the mc option response
                $response_mc = new Enp_quiz_Save_quiz_take_Response_MC();
                $return_mc_response = $response_mc->insert_response_mc($return);
                // increase the count on the mc option responses
                $response_mc->increase_mc_option_responses($response['question_response']);
                // merge the response arrays
                $return = array_merge($return, $return_mc_response);
            } elseif($response['question_type'] === 'slider') {
                // TODO: Build slider save response
            }
            // update question response data
            $this->update_question_response_data($response);


        } else {
            // handle errors
            $return['error'] = 'Save response failed.';
        }
        // return response
        return $return;
    }

    /**
    * Connects to DB and updates the question response data.
    * @param $response (array) data we'll be using to update the question table
    * @return only returns errors
    */
    protected function update_question_response_data($response) {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // setup our SQL statement variables so we don't need to have a correct query, incorrect query, and a rebuild % query. A little convoluted, but fast.
        $question_responses = 'question_responses';
        if($response['response_correct'] === '1') {
            $question_response_state = 'question_responses_correct';

        } else {
            $question_response_state = 'question_responses_incorrect';

        }
        // Get our Parameters ready
        $params = array(':question_id' => $response['question_id']);
        // write our SQL statement
        /**
        * IMPORTANT: Interestingly, the (question_responses + 1)
        * and (correct or incorrect + 1) statements update BEFORE the
        * percentage math happens, so we don't need to add the + 1
        * into those queries. The math will be correct with the updated values.
        */
        $sql = "UPDATE ".$pdo->question_table."
                   SET  question_responses = question_responses + 1,
                        ".$question_response_state." = ".$question_response_state." + 1,
                        question_responses_correct_percentage = question_responses_correct/question_responses,
                        question_responses_incorrect_percentage = question_responses_incorrect/question_responses
                 WHERE  question_id = :question_id";
        // update the question view the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // rebuild the percentages now
            $this->update_question_response_data_percentages($response);
        } else {
            // handle errors
            self::$return['error'][] = 'Update question response data failed.';
        }

        // return response
        return self::$return;
    }

    protected function update_question_response_data_percentages($response) {

    }

    protected function get_response_question_id($response) {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':response_quiz_id' => $response['response_quiz_id'],
                        ':question_id'       => $response['question_id']
                    );

        $sql = "SELECT response_question_id from ".$pdo->response_question_table." WHERE
                response_quiz_id = :response_quiz_id
                AND question_id = :question_id";
        $stmt = $pdo->query($sql, $params);
        $result = $stmt->fetch();
        return $result['response_question_id'];
    }
}
