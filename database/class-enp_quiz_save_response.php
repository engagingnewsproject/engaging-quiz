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

class Enp_quiz_Save_response {
    public static $response,
                  $return = array(),
                  $quiz,
                  $next_question = array(),
                  $quiz_end = array();

    public function __construct() {

    }

    public function save_response($response) {
        $response = $this->validate_response_data($response);
        self::$quiz = new Enp_quiz_Quiz($response['quiz_id']);
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
                $response_mc = new Enp_quiz_Save_response_MC();
                $return_mc_response = $response_mc->insert_response_mc($response);
                // merge the response arrays
                $return = array_merge($return, $return_mc_response);
            } elseif($response['question_type'] === 'slider') {
                // TODO: Build slider save response
            }

            // build our next question (if applicable)
            $return = $this->set_next_question($return);
            // build what should happen now
            $return = $this->build_state($return);
            // build what to do next (for JS to pre-load)
            $return = $this->build_next_state($return);
            // return response
            return $return;
        } else {
            // handle errors
            return false;
        }
    }

    /**
    * Our API should give all the info of what to do
    * when it returns. So let's build that into our JSON response.
    *
    * @param $response (array) our current response to be sent to the browser
    * @return $response (array) appended with our next actions
    */
    protected function build_state($return) {
        if($return['user_action'] === 'enp-question-submit') {
            // if  they submitted a question, the next state will always be showing
            // the question explanation
            $state = 'question_explanation';
        } elseif($return['user_action'] === 'enp-next-question') {
            // Might be next question, might be the end of the quiz
            if(empty(self::$next_question)) {
                // we're at the quiz end if the next question array is empty
                $state = 'quiz_end';
            } else {
                // we have a question to display! Set it to the question state
                $state = 'question';
            }
        } else {
            // TODO: Error
            $state = false;
        }

        $return['state'] = $state;

        return $return;
    }

    /**
    * If the JS wants to, it can use this response to preload content
    *
    * @param $response (array) our current response to be sent to the browser
    * @return $response (array) appended with our next actions
    */
    protected function build_next_state($return) {
        if(!empty(self::$next_question)) {
            // we have another question!
            // get the JSON

        } else {
            // no question next, so we're at the end
            // build the final page with their data
            $return['next_state'] = 'quiz_end';
            $return['quiz_end'] = array('some data');
        }

        return $return;
    }

    /**
    * Finding and setting the next question up in the series
    * Used to preload and see where we're at in the question series
    *
    */
    protected function set_next_question($return) {
        // get the questions for this quiz
        $question_ids = self::$quiz->get_questions();

        // see where we're at in the question cycle
        if(!empty($question_ids)) {
            $i = 0;
            foreach($question_ids as $question_id) {
                if($question_id === intval($return['question_id'])) {
                    // this is the current one, so we need the NEXT one, if there is one
                    $i++;
                    if(isset($question_ids[$i]) && is_int($question_ids[$i])) {

                        // this is the next one!
                        // generate the question object JSON
                        self::$next_question = new Enp_quiz_Question($question_ids[$i]);
                        $return['next_question'] = self::$next_question->get_take_question_array();
                        // no need to loop anymore
                        break;
                    }
                }
                $i++;
            }
        }

        return $return;
    }
}
?>
