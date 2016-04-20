<?php
/**
 * Save a question view
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

class Enp_quiz_Save_question_view extends Enp_quiz_Save_take_quiz {
    public static $question;

    public function __construct() {

    }

    public function save_question_view($response) {
        $response = $this->validate_question_view_data($response);
        // everything looks good! insert it and return the response
        $return = $this->insert_response($response);

        return $return;
    }

    protected function validate_question_view_data($response) {
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
                $response_mc = new Enp_quiz_Save_response_MC();
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
