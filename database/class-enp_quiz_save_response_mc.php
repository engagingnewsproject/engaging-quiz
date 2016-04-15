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

class Enp_quiz_Save_response_MC extends Enp_quiz_Save_response {

    public function __construct() {

    }

    /**
    * Connects to DB and inserts the user response.
    * @param $response (array) data we'll be saving to the response table
    * @return builds and returns a response message
    */
    protected function insert_response_mc($response) {
        // connect to PDO
        $pdo = new enp_quiz_Db();
        // Get our Parameters ready
        $params = array(':response_id'      => $response['response_id'],
                        ':mc_option_id'=> $response['question_response']
                    );
        // write our SQL statement
        $sql = "INSERT INTO ".$pdo->response_mc_table." (
                                            response_id,
                                            mc_option_id
                                        )
                                        VALUES(
                                            :response_id,
                                            :mc_option_id
                                        )";
        // insert the mc_option into the database
        $stmt = $pdo->query($sql, $params);

        // success!
        if($stmt !== false) {
            // add our response ID to the array we're working with
            $response['response_mc_id'] = $pdo->lastInsertId();
            // set-up our response array
            $response_response = array(
                                        'response_mc_id' => $response['response_id'],
                                        'status'       => 'success',
                                        'action'       => 'insert'
                                );
            // see what type of question we're working on and save that response
            if($response['question_type'] === 'mc') {
                // we added a mc_option successfully, let them know!
                return $response;
            }
        } else {
            // handle errors
            return false;
        }
    }
}
?>
