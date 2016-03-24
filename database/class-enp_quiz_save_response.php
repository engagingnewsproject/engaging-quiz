<?/**
 * Response messages for saving quizzes
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/includes
 *
 * Called by Enp_quiz_Quiz_save
 *
 * This class defines all code for processing responses and success/error messages
 * The quiz that get passed here will already have been sanitized
 *
 * @since      0.0.1
 * @package    Enp_quiz
 * @subpackage Enp_quiz/database
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Save_response extends Enp_quiz_Save_quiz {
    public $quiz_id,
           $status,
           $action,
           $message = array(),
           $question = array(),
           $user_action = array();

    public function __construct() {

    }

    /**
    * Sets a quiz_id on our response array
    * @param string = quiz_id you want to set
    * @return response object array
    */
    public function set_quiz_id($quiz_id) {
        $this->quiz_id = $quiz_id;
    }

    /**
    * Sets an action on our response array
    * @param string = action you want to set
    * @return response object array
    */
    public function set_action($action) {
        $this->action = $action;
    }

    /**
    * Sets a status on our response array
    * @param string = status you want to set
    * @return response object array
    */
    public function set_status($status) {
        $this->status = $status;
    }

    /**
    * Loops through all passed responses from the save class and and assigns them
    * to our response object
    *
    * @param $question_response = array() of values like 'action', 'status', and 'mc_option_id'
    * @param $question = the question array that was being saved
    */
    public function set_question_response($question_response, $question) {
        $question_number = $question['question_order'];
        // sets the key/value for each item passed in the response
        foreach($question_response as $key => $value) {
            // set the question array with our response values
            $this->question[$question_number][$key] = $value;
        }
    }

    /**
    * Loops through all passed responses from the save class and and assigns them
    * to our response object
    *
    * @param $mc_option_response = array() of values like 'action', 'status', and 'mc_option_id'
    * @param $question = the question array that was being saved
    * @param $mc_option = the mc_option array that was being saved
    */
    public function set_mc_option_response($mc_option_response, $question, $mc_option) {
        $question_number = $question['question_order'];
        $mc_option_number = $mc_option['mc_option_order'];
        // sets the key/value for each item passed in the response
        foreach($mc_option_response as $key => $value) {
            // set the question/mc_option array with our response values
            $this->question[$question_number]['mc_option'][$mc_option_number][$key] = $value;
        }
    }

    /**
    * Sets a new error to our error response array
    * @param string = message you want to add
    * @return response object array
    */
    public function add_error($error) {
        $this->message['error'][] = $error;
    }

    /**
    * Sets a new success to our success response array
    * @param string = message you want to add
    * @return response object array
    */
    public function add_success($success) {
        $this->message['success'][] = $success;
    }


    /**
    * Build a user_action response array so enp_quiz-create class knows
    * what to do next, like go to preview page, add another question, etc
    */
    public function set_user_action_response() {
        $action = null;
        $element = null;
        $details = array();

        // if they want to preview, then see if they're allowed to go on
        if(parent::$quiz['user_action'] === 'quiz-preview') {
            $action = 'next';
            $element = 'preview';
        }
        // if they want to publish, then see if they're allowed to go on
        elseif(parent::$quiz['user_action'] === 'quiz-publish') {
            $action = 'next';
            $element = 'publish';
        } elseif(parent::$quiz['user_action'] === 'add-question') {
            // what else do we want to do?
            $action = 'add';
            $element = 'question';
        }

        $this->user_action = array(
                                    'action' => $action,
                                    'element' => $element,
                                    'details' => $details,
                                );
    }


    /**
    * Runs all checks to build error messages on quiz form
    * All the functions it runs either return false or
    * add to the response object
    * @return false
    */
    public function build_messages() {
        // check to see if they need to add questions
        if($this->check_for_questions_message() === 'has_questions') {
            // we have a question title and explanation in the first question,
            // so let's check more in depth. This checks for all errors
            // in all questions
            $this->check_question_errors();
        }

        // we don't need to return anything since the functions
        // themselves are building the response messages
        return false;
    }

    /**
    * Checks to see if the first question is empty. If it is, add an error
    * @return 'has_questions' if question found, false if there are questions
    *
    */
    public function check_for_questions_message() {
        if(empty(parent::$quiz['question'][0]['question_title']) && empty(parent::$quiz['question'][0]['question_explanation'])) {
            $this->message['error'][] = 'You need to add a question to your quiz';
            return false;
        }
        return 'has_questions';
    }

    /**
    * Loop through questions and check for errors
    */
    public function check_question_errors() {
        $i = 1;
        // this is weird to set it as OK initially, but...
        $return_message = 'no_errors';
        // loop through all questions and check for titles, answer explanations, etc
        foreach(parent::$quiz['question'] as $question) {
            // checks if the title is empty or not
            $check_title = $this->check_question_title($question['question_title'], $i);
            if($check_title === 'no_title') {
                $return_message = 'has_errors';
            }
            // checks if the answer explanation is empty or not
            $check_explanation = $this->check_question_question_explanation($question['question_explanation'], $i);
            if($check_explanation === 'no_question_explanation') {
                $return_message = 'has_errors';
            }

            // check to see if the question is a slider or mc choice
            if($question['question_type'] === 'mc') {
                //TODO
                // add mc_options if mc question type
                $this->check_question_mc_options($question['mc_option'], $i);
            } elseif($question['question_type'] === 'slider') {
                // TODO: create sliders...
                $this->message['error'][] = 'Question '.$i.' does not have a complete slider because that functionality does not exist yet.';
            } else {
                // should never happen...
                $this->message['error'][] = 'Question '.$i.' does not have a question type (multiple choice, slider, etc).';
            }
            $i++;
        }

        return $return_message;
    }


    /**
    * Checks questions for titles
    * @return true if no question, false if there are questions
    *
    */
    public function check_question_title($question_title, $question_number) {
        $return_message = 'has_title';
        if(empty($question_title)) {
            $this->message['error'][] = 'Question '.$question_number.' is missing an actual question.';
            $return_message = 'no_title';
        }

        return $return_message;
    }

    /**
    * Checks questions for answer explanation
    * @return string 'has_question_explanation' if found, 'no_question_explanation' if not found
    *
    */
    public function check_question_question_explanation($question_explanation, $question_number) {
        $return_message = 'has_question_explanation';
        if(empty($question_explanation)) {
            $this->message['error'][] = 'Question '.$question_number.' is missing an answer explanation.';
            $return_message = 'no_question_explanation';
        }

        return $return_message;
    }


    /**
    * Checks questions for mc_options (if it should have them)
    * @return string 'has_mc_options' if found, 'no_mc_options' if not found
    *
    */
    public function check_question_mc_options($mc_options, $question_number) {
        $return_message = 'no_mc_options';
        if(empty($mc_options)) {
            $this->message['error'][] = 'Question '.$question_number.' is missing multiple choice options.';
            $return_message = 'no_mc_options';
            return $return_message;
        }

        if(count($mc_options) === 1) {
            $this->message['error'][] = 'Question '.$question_number.' does not have enough multiple choice options.';
        } else {
            foreach($mc_options as $option) {
                // check to see if one has been chosen
                if($option['correct']) {
                    // we have a correct option! yay! Everything is good.
                    $return_message = 'has_mc_options';
                }
            }
        }

        return $return_message;
    }

}
