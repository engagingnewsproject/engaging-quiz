<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/quiz-take
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version,
 * and registers & enqueues quiz take scripts and styles
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Take {
	public $quiz,
		   $question,
		   $state = '',
		   $total_questions,
		   $current_question,
		   $response = array();

	public function __construct($quiz_id = false) {
		// hello!
		$this->load_files();
		// check if we have a posted var
		if(isset($_POST['enp-question-submit'])) {
            $response = $this->save_response();
            // parse the JSON response
            $this->response = json_decode($response);
			// var_dump($this->response);
        }
		// get our quiz
		$this->quiz = $this->load_quiz($quiz_id);
		// make sure a quiz got loaded
		$this->validate_quiz();
		// set-up Vars for quizzes
		foreach($this->quiz as $key=>$value ) {
			// $$key will be what the key of the array is
			// if $key = 'quiz_title', then $$key will be available as $quiz_title
			$this->$key = $value;
		}
		// set our state
		$this->set_state();
		// set question
		$this->set_question();
		// set random vars we'll need
		$this->set_total_questions();
		$this->set_current_question_number();
	}

	/**
	 * Register and enqueue the stylesheets for quiz take
	 *
	 * @since    0.0.1
	 */
	public function styles() {
		$styles = array(ENP_QUIZ_PLUGIN_URL.'public/quiz-take/css/enp_quiz-take.min.css');
		foreach($styles as $href) {
			echo '<link rel="stylesheet" type="text/css" href="'.$href.'" media="all" />';
		}
	}

	/**
	 * Register and enqueue the JavaScript for quiz take
	 *
	 * @since    0.0.1
	 */
	public function scripts() {
		$scripts = array(ENP_QUIZ_PLUGIN_URL.'public/quiz-take/js/enp_quiz-take.js');
		foreach($scripts as $src) {
			echo '<script src="'.$src.'"></script>';
		}
	}

	public function load_files() {
		// require the necessary files
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-quiz.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-question.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-mc_option.php';
        // Database
        require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_db.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_response.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_response_mc.php';
	}

	public function load_quiz($quiz_id) {
		// set-up our quiz
        $quiz = new Enp_quiz_Quiz($quiz_id);
        $quiz = $quiz->get_quiz_json();
        return json_decode($quiz);
	}

	public function validate_quiz() {
		if(empty($this->quiz->quiz_id)) {
            echo 'Quiz not found';
            exit;
        } else {
			return true;
		}
	}

	public function load_svg() {
		$svg = file_get_contents(ENP_QUIZ_PLUGIN_URL.'/public/quiz-take/svg/symbol-defs.svg');
	    return $svg;
	}

	public function load_quiz_styles() {
		return '<style tyle="text/css">
#enp-quiz .enp-quiz__container {
    background-color: '.$this->quiz->quiz_bg_color.';
    color: '.$this->quiz->quiz_text_color.';
}
#enp-quiz .enp-quiz__title,
#enp-quiz .enp-question__question,
#enp-quiz .enp-option__label,
#enp-quiz .enp-question__helper {
    color: '.$this->quiz->quiz_text_color.';
}
</style>';
	}
	/**
	* Save quiz responses
	*/
	public function save_response() {
		// get the posted data
		if(isset($_POST['enp-quiz-id'])) {
			$quiz_id = $_POST['enp-quiz-id'];
		}

		if(isset($_POST['enp-question-id'])) {
			$question_id = $_POST['enp-question-id'];
		}

		if(isset($_POST['enp-question-type'])) {
			$question_type = $_POST['enp-question-type'];
		}

		if(isset($_POST['enp-question-response'])) {
			$question_response = $_POST['enp-question-response'];
		}

		// get user action
		if(isset($_POST['enp-question-submit'])) {
			$button_clicked = $_POST['enp-question-submit'];
		}

		// set the date_time to pass
		$date_time = date("Y-m-d H:i:s");

		$response = array(
						'quiz_id'	=> $quiz_id,
						'question_id' => $question_id,
						'question_type' => $question_type,
						'question_response' => $question_response,
						'response_created_at' => $date_time,
						'user_action' => $button_clicked,
						);

		// save the response
		$save_response = new Enp_quiz_Save_response();
		$return_response = $save_response->save_response($response);
		// encode to JSON to send to browser
		$return_response = json_encode($return_response);
		// set it as our response object
		$this->response = $return_response;
		return $return_response;
	}

	public function set_total_questions() {
		$this->total_questions = count($this->quiz->questions);
	}

	public function set_current_question_number() {
		$this->current_question_number = $this->question->question_order + 1;
	}

	/**
	* Set the data context for the question.
	* Decide which question we need based on current quiz state.
	*
	* @param $response (array) response from server, if present
	* @param $quiz (object) Enp_quiz_Quiz()
	* @return $question (array) The question we need to display
	*/
	public function set_question() {
		$question = array();
		$question_id = '';
		if(isset($this->response) && !empty($this->response)) {
			// see what we should do
			if($this->state === 'question_explanation') {
				// show the question explanation template
				// we'll still need this question so we can get the explanation
				$question_id = $this->response->question_id;
			}
			elseif($this->state === 'question') {
				$question = $this->response->next_question;
			}
		}
		// elseif(check cookies?) {}
		else {
			// set the first question off of the question_ids from the quiz
			$question_id = $this->quiz->questions[0];
		}

		// if we have a question id, get the question data for it
		if(!empty($question_id)) {
			$question = new Enp_quiz_Question($question_id);
			$question = $question->get_take_question_json();
			$question = json_decode($question);
		}

		$this->question = $question;
		return $question;
	}

	public function set_state() {
		if(isset($this->response) && !empty($this->response)) {
			$this->state = $this->response->state;
		}
	}

	// TODO: Load correct template with vars
	// TODO: Set question vars
	// TODO: Create JS Templates

}
