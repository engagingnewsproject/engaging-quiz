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

class Enp_quiz_Save_quiz_take_Response extends Enp_quiz_Save_quiz_take {
    public static $response;

    public function __construct() {

    }

    public function save_response($response) {
        $response = $this->validate_response_data($response);
        // everything looks good! insert it and return the response
        $return = $this->insert_response($response);

        return $return;
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
    protected function insert_response($response) {

        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':question_id'      => $response['question_id'],
                        ':response_correct'=> $response['response_correct'],
                        ':response_created_at'=> $response['response_created_at'],
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->response_table." (
                                            question_id,
                                            response_correct,
                                            response_created_at
                                        )
                                        VALUES(
                                            :question_id,
                                            :response_correct,
                                            :response_created_at
                                        )";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // add our response ID to the array we're working with
            $response['response_id'] = $pdo->lastInsertId();
            // set-up our response array
            $return = array(
                                        'response_id' => $response['response_id'],
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );

            // merge the response arrays
            $return = array_merge($response, $return);
            // see what type of question we're working on and save that response
            if($response['question_type'] === 'mc') {
                // save the mc option response
                $response_mc = new Enp_quiz_Save_quiz_take_Response_MC();
                $return_mc_response = $response_mc->insert_response_mc($response);
                // merge the response arrays
                $return = array_merge($return, $return_mc_response);
            } elseif($response['question_type'] === 'slider') {
                // TODO: Build slider save response
            }



        } else {
            // handle errors
            $return['error'] = 'Save response failed.';
        }
        // return response
        return $return;
    }
}
