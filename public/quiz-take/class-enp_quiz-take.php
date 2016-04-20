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
		   $current_question_number,
		   $current_score,
		   $question_explanation_title,
		   $question_explanation_percentage,
		   $response = array();

	/**
	* This is a big constructor. We require our files, check for $_POST submission,
	* set states, and all other details we're sure to need for our templating
	*
	*/
	public function __construct($quiz_id = false) {
		// require files
		$this->load_files();
		// check if we have a posted var
		if(isset($_POST['enp-question-submit'])) {
            $response = $this->save_quiz_take();
            // parse the JSON response
            $this->response = json_decode($response);
			//var_dump($this->response);
        }
		// get our quiz
		$this->quiz = new Enp_quiz_Quiz($quiz_id);
		// make sure a quiz got loaded
		$this->validate_quiz();

		// set our state
		$this->set_state();
		// set question
		$this->set_question();

		// save a question view if we're on a question
		if($this->state === 'question') {
			$this->save_question_view();
		}

		if($this->state === 'question_explanation') {
			$this->set_question_explanation_vars();
		}

		// set random vars we'll need
		$this->set_total_questions();
		$this->set_current_question_number();

		// set cookies we'll need on reload or correct/incorrect amounts
		$this->set_cookies();

		// more random vars
		// set the score if we're at the end
		if($this->state === 'quiz_end') {
			// figure out their score
			$this->set_current_score();
		}

	}

	/**
	 * Stylesheets for quiz take
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
	 * JavaScript for quiz take
	 *
	 * @since    0.0.1
	 */
	public function scripts() {
		$scripts = array(ENP_QUIZ_PLUGIN_URL.'public/quiz-take/js/enp_quiz-take.js');
		foreach($scripts as $src) {
			echo '<script src="'.$src.'"></script>';
		}
	}

	/**
	* Require all the files we'll need. This is loaded outside of WP, so we need
	* to require everything we need on our own.
	*/
	public function load_files() {
		// require the necessary files
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-quiz.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-question.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-mc_option.php';
        // Database
        require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_db.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take_question_view.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take_response.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take_response_mc.php';
	}

	/**
	* Quick check to see if we have a valid quiz before moving on
	*/
	public function validate_quiz() {
		if(empty($this->quiz->get_quiz_id())) {
            echo 'Quiz not found';
            exit;
        } else {
			return true;
		}
	}

	/**
	* Add all of our SVG to the DOM
	*/
	public function load_svg() {
		$svg = file_get_contents(ENP_QUIZ_PLUGIN_URL.'/public/quiz-take/svg/symbol-defs.svg');
	    return $svg;
	}

	/**
	* Quiz Option styles that we need to override our own CSS
	*/
	public function load_quiz_styles() {
		// figure out the width of our progress bar
		$progress_bar_width = $this->current_question_number/$this->total_questions;
		// reduce the number a little if we're at the very end so it still looks like there's more to go
		if($this->state !== 'quiz_end' && $progress_bar_width === 1) {
			$progress_bar_width = .9;
		}
		$progress_bar_width = number_format( $progress_bar_width * 100, 2 ) . '%';

		return '<style tyle="text/css">
#enp-quiz .enp-quiz__container {
    background-color: '.$this->quiz->get_quiz_bg_color().';
    color: '.$this->quiz->get_quiz_text_color().';
}
#enp-quiz .enp-quiz__title,
#enp-quiz .enp-question__question,
#enp-quiz .enp-option__label,
#enp-quiz .enp-question__helper {
    color: '.$this->quiz->get_quiz_text_color().';
}
#enp-quiz .enp-quiz__progress__bar {
	width: '.$progress_bar_width.';
}
</style>';

	}

	public function save_quiz_take() {
		$response = false;
		$save_data = array();
		// get user action
		if(isset($_POST['enp-question-submit'])) {
			$save_data['user_action'] = $_POST['enp-question-submit'];
		}
		// get the posted data
		if(isset($_POST['enp-quiz-id'])) {
			$save_data['quiz_id'] = $_POST['enp-quiz-id'];
		}

		if($save_data['user_action'] === 'enp-question-submit') {
			// build the data array
			$save_data = $this->build_response_data($save_data);
		} elseif($save_data['user_action'] === 'enp-next-question') {
			// build the data array
			$save_data = $this->build_moving_on_data($save_data);
		}

		// save the response
		$save_quiz_take = new Enp_quiz_Save_quiz_take();
		$response = $save_quiz_take->save_quiz_take($save_data);

		return $response;
	}

	public function build_response_data($response_array) {

		// set defaults
		$response_data = array(
						'quiz_id'	=> '',
						'question_id' => '',
						'question_type' => '',
						'question_response' => '',
						'response_created_at' => date("Y-m-d H:i:s"),
						'user_action' => '',
						);
		// merge the passed values with our defaults
		$response_data = array_merge($response_data, $response_array);

		if(isset($_POST['enp-question-id'])) {
			$response_data['question_id'] = $_POST['enp-question-id'];
		}

		if(isset($_POST['enp-question-type'])) {
			$response_data['question_type'] = $_POST['enp-question-type'];
		}

		if(isset($_POST['enp-question-response'])) {
			$response_data['question_response'] = $_POST['enp-question-response'];
		}

		return $response_data;
	}


	public function build_moving_on_data($moving_on_array) {

		// set defaults
		$moving_on_data = array(
						'quiz_id'	=> '',
						'question_id' => '',
						'moving_on_created_at' => date("Y-m-d H:i:s"),
						'user_action' => '',
						);
		// merge the passed values with our defaults
		$moving_on_data = array_merge($moving_on_data, $moving_on_array);

		if(isset($_POST['enp-question-id'])) {
			$moving_on_data['question_id'] = $_POST['enp-question-id'];
		}

		return $moving_on_data;
	}

	public function set_total_questions() {
		$this->total_questions = count($this->quiz->get_questions());
	}

	public function set_current_question_number() {
		// if we're at the end, the current question number is the total of the questions
		if($this->state === 'quiz_end') {
			$current_number = $this->total_questions;
		} else {
			$current_number = $this->question->question_order + 1;
		}
		$this->current_question_number = $current_number;

	}

	/**
	* Get the person's score (what the % of their score is)
	* @param cookies
	* @return score (int)
	*/
	public function set_current_score() {
		$quiz_id = $this->quiz->get_quiz_id();
		$question_ids = $this->quiz->get_questions();
		$correct = 0;
		// loop through all questions and see if there are cookies set
		foreach($question_ids as $question_id) {
			// build cookie name
			$cookie_name = 'enp_take_quiz_'.$quiz_id.'_'.$question_id;
			if(isset($_COOKIE[$cookie_name])) {
				if($_COOKIE[$cookie_name] === '1') {
					$correct++;
				}
			}
		}

		// calculate the score
		$this->current_score = ($correct / $this->total_questions) * 100;

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
		$question_id_cookie_name = 'enp_take_quiz_'.$this->quiz->get_quiz_id().'_question_id';
		if(isset($this->response) && !empty($this->response)) {
			// see what we should do
			if($this->state === 'question_explanation') {
				// show the question explanation template
				// we'll still need this question so we can get the explanation
				$question_id = $this->response->question_id;
			}
			// if a state is set(meaning, we have a response) & the state is 'question', that means we're moving on, so get the next_question response
			elseif($this->state === 'question') {
				$question_id = $this->response->next_question->question_id;
			}
		}
		// check for cookies to see if we're on a page reload or something
		elseif(isset($_COOKIE[$question_id_cookie_name])) {
			$question_id = $_COOKIE[$question_id_cookie_name];
		}
		// probably new pageload. just get the first question of the quiz
		else {
			$question_ids = $this->quiz->get_questions();
			// set the first question off of the question_ids from the quiz
			$question_id = $question_ids[0];
		}

		// if we have a question id, get the question data for it
		if(!empty($question_id)) {
			$question = new Enp_quiz_Question($question_id);
		}

		$this->question = $question;
		return $question;
	}

	/**
	* Everytime a quiz take is loaded, save a view to the DB
	*/
	public function save_question_view() {
		$save_question_view = new Enp_quiz_Save_quiz_take_Question_view($this->question->question_id);
	}

	public function set_question_explanation_vars() {
		$this->set_question_explanation_title();
		$this->set_question_explanation_percentage();
	}

	public function set_state() {
		$quiz_state_cookie_name = 'enp_take_quiz_'.$this->quiz->get_quiz_id().'_state';
		// set state off response, if it's there
		if(isset($this->response) && !empty($this->response)) {
			$this->state = $this->response->state;
		}
		// try to set the state from the cookie
		elseif(isset($_COOKIE[$quiz_state_cookie_name])) {
			$this->state = $_COOKIE[$quiz_state_cookie_name];
		}
		// probably a new quiz
		else {
			$this->state = 'question';
		}
	}


	// getters
	public function get_state() {
		return $this->state;
	}

	public function get_total_questions() {
		return $this->total_questions;
	}

	public function get_current_question_number() {
		return $this->current_question_number;
	}

	public function get_current_score() {
		return $this->current_score;
	}

	public function get_score_circle_dashoffset() {
		$dashoffset = 0;
		if(!empty($this->current_score)) {
			// calculate the score dashoffset
            $r = 90;
            $c = M_PI*($r*2);
            $dashoffset = ((100-$this->get_current_score())/100)*$c;
		}
		return $dashoffset;
	}

	public function set_question_explanation_title() {
		$title = 'incorrect';
		if($this->response->response_correct === '1') {
			$title = 'correct';
		}
		$this->question_explanation_title = $title;
	}

	public function set_question_explanation_percentage() {
		if($this->response->response_correct === '1') {
			$percentage = $this->question->get_question_responses_correct_percentage();
		} else {
			$percentage = $this->question->get_question_responses_incorrect_percentage();
		}
		$this->question_explanation_percentage = $percentage;
	}

	public function get_question_explanation_title() {
		return $this->question_explanation_title;
	}

	public function get_question_explanation_percentage() {
		// build this off the response
		return $this->question_explanation_percentage;
	}

	/**
	* We need cookies for quiz state and how they're doing score wise
	* On each page load we'll save cookies as a snapshot of the current state
	*/
	public function set_cookies() {
		$week = time() + (86400 * 7);
		$quiz_id = $this->quiz->get_quiz_id();

		// quiz state
		if(!empty($this->state)) {
			setcookie('enp_take_quiz_'.$quiz_id.'_state', $this->state, $week);
		} else {
			return false;
		}

		// question number
		if($this->state === 'question') {
			setcookie('enp_take_quiz_'.$quiz_id.'_question_id', $this->question->get_question_id(), $week);
		}
		// if we're on a question explanation, how'd they do for that question?
		// next question
		elseif($this->state === 'question_explanation') {
			setcookie('enp_take_quiz_'.$quiz_id.'_'.$this->question->get_question_id(), $this->response->response_correct, $week);
		}


	}


	public function question_js_templates() {
		// clone the object so we don't reset its own values
		$qt = clone $this;
		foreach($qt->question as $key => $value) {
			if($key === 'question_image') {
				// question_image should be blank
				// because it messes up the templating for srcset
				// and src when it thinks it has a value
				$qt->question->$key = '';
			} else {
				$qt->question->$key = '{{'.$key.'}}';
			}
		}

		// image template
		$template = '<script type="text/template" id="question_image_template">';
		ob_start();
		include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'partials/question-image.php');
		$template .= ob_get_clean();
		$template .= '</script>';
		$template .= '<script type="text/template" id="question_template">';
		ob_start();
		include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'partials/question.php');
		$template .= ob_get_clean();
		$template .= '</script>';



		return $template;
	}
	/**
	* I can't think of a better way to do this right now, but I think this is OK
	* It loops all keys in the object and sets the values as handlebar style strings
	* and injects it into the template
	*/
	public function question_explanation_js_template() {
		// clone the object so we don't reset its own values
		$qt = clone $this;

		foreach($qt->question as $key => $value) {
			if($key === 'question_image') {
				// set it to a bogus image value that matches at least
			}
			$qt->question->$key = '{{'.$key.'}}';
		}

		foreach($qt as $key => $value) {
			// we don't want to unset our question object
			if($key !== 'question') {
				$qt->$key = '{{'.$key.'}}';
			}
		}

		$template = '<script type="text/template" id="question_explanation_template">';
		ob_start();
		include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'partials/question-explanation.php');
		$template .= ob_get_clean();
		$template .= '</script>';

		return $template;
	}


	/**
	* I can't think of a better way to do this right now, but I think this is OK
	* It loops all keys in the object and sets the values as handlebar style strings
	* and injects it into the template
	*/
	public function mc_option_js_template() {
		$mc_option = new Enp_quiz_MC_option(0);
		foreach($mc_option as $key => $value) {
			$mc_option->$key = '{{'.$key.'}}';
		}
		$template = '<script type="text/template" id="mc_option_template">';
		ob_start();
		include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/mc-option.php');
		$template .= ob_get_clean();
		$template .= '</script>';

		return $template;
	}

}
