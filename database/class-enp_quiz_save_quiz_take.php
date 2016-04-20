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

class Enp_quiz_Save_quiz_take {
    public static $return = array('error'=>array()),
                  $quiz,
                  $next_question = array(),
                  $quiz_end = array();

    public function __construct() {

    }

    public function save_quiz_take($data) {
        $valid = $this->validate_save_quiz_take($data);
        if($valid === false) {
            return self::$return;
        }

        self::$quiz = new Enp_quiz_Quiz($data['quiz_id']);

        // decide what we're saving based on the user_action
        if($data['user_action'] === 'enp-question-submit') {
            $save_response = new Enp_quiz_Save_quiz_take_Response();
            $save_response = $save_response->save_response($data);
        } elseif($data['user_action'] === 'enp-next-question') {

        }

        // check to make sure whatever we saved returned a response
        if(!empty($save_response)) {
            self::$return = array_merge(self::$return, $save_response);
        }

        // build our next question (if applicable)
        self::$return = $this->set_next_question(self::$return);
        // build what state we'll be in on the response (what should load for the user NOW?)
        self::$return = $this->build_state(self::$return);
        // build what to do next (for JS to pre-load) (AFTER this state)
        self::$return = $this->build_next_state(self::$return);
        // convert to JSON and return it
        self::$return = json_encode(self::$return);
        return self::$return;
    }

    protected function validate_save_quiz_take($data) {
        $valid = false;
        // check to see if we have data
        if(empty($data)) {
            self::$return['error'][] = 'No Data to save.';
        }
        // check if we have a user action set
        if(empty($data['user_action'])) {
            self::$return['error'][] = 'No user action set.';
        }
        // check to make sure we have a quiz id
        if(empty($data['quiz_id'])) {
            self::$return['error'][] = 'No Quiz ID set.';
        }
        if(empty($return['error'])) {
            $valid = true;
        }
        return $valid;
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
