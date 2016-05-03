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
		   $state = '',
		   $total_questions,
		   $current_question_id,
		   $next_question_id,
		   $current_question_number,
		   $nonce,
		   $response = array();

	/**
	* This is a big constructor. We require our files, check for $_POST submission,
	* set states, and all other details we're sure to need for our templating
	*
	*/
	public function __construct($quiz_id = false) {
		// require files
		$this->load_files();
		// set nonce
		$this->set_nonce($quiz_id);
		// check if we have a posted var
		if(isset($_POST['enp-question-submit'])) {
			// sets $this->response;
            $this->save_quiz_take();
        }

		// get our quiz
		$this->quiz = new Enp_quiz_Quiz($quiz_id);
		// make sure a quiz got loaded
		$this->validate_quiz();

		if(isset($_POST['enp-quiz-restart'])) {
			// sets $this->response;
            $this->quiz_restart();
        }

		// set our state
		$this->set_state();

		// set random vars we'll need
		$this->set_total_questions();
		$this->set_current_question_id();
		$this->set_current_question_number();

		// set cookies we'll need on reload or correct/incorrect amounts
		$this->set_cookies();

		// if it's the first question, we need to save the initial quiz view
		// and question view
		$this->save_initial_view_data();

		// if we're doing AJAX, echo the response back to the server
		// this is echo-ed after everything else is done so we don't get "header already sent" errors from PHP
		if(isset($_POST['doing_ajax'])) {
			header('Content-type: application/json');
			echo json_encode($this->response);
			// don't produce anymore HTML or render anything else
			// otherwise the server keeps going and sends us all
			// the HTML of the page too, but we just want the JSON data
			die();
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
		$scripts = array(
						//"https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js",
						ENP_QUIZ_PLUGIN_URL.'public/quiz-take/js/dist/jquery.js',
						ENP_QUIZ_PLUGIN_URL.'public/quiz-take/js/dist/underscore.min.js',
						ENP_QUIZ_PLUGIN_URL.'public/quiz-take/js/dist/quiz-take.js'
					);
		foreach($scripts as $src) {
			echo '<script src="'.$src.'"></script>';
		}
	}

	public function get_init_json() {
		$json = clone $this;
		// just output the quiz level json
		echo '<script type="text/javascript">';
		// print this whole object as js global vars in json
			echo 'var quiz_json = '.json_encode($json->quiz).';';
		echo '</script>';
		// remove quiz from the object so we don't print it again
		unset($json->quiz);
		echo '<script type="text/javascript">';
		// print this whole object as js global vars in json
			echo 'var qt_json = '.json_encode($json).';';
		echo '</script>';
		// unset the cloned object
		unset($json);

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
		require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-nonce.php';
		// Quiz Take Classes
		require ENP_QUIZ_PLUGIN_DIR . 'public/quiz-take/includes/class-enp_quiz-take_quiz_end.php';
		require ENP_QUIZ_PLUGIN_DIR . 'public/quiz-take/includes/class-enp_quiz-take_question.php';
        // Database
        require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_db.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take_question_view.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take_response.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take_response_mc.php';
		require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_save_quiz_take_quiz_data.php';

	}

	public function set_nonce($quiz_id) {
		//Start the session
	   session_start();
	   //Start the class
	   $this->nonce = new Enp_quiz_Nonce('enp_quiz_take_'.$quiz_id.'_nonce');
	}

	/**
	* Quick check to see if we have a valid quiz before moving on
	*/
	public function validate_quiz() {
		$quiz_id = $this->quiz->get_quiz_id();
		if(empty($quiz_id)) {
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
#enp-quiz .enp-quiz__container,
#enp-quiz .enp-option__label,
#enp-quiz .enp-explanation {
    background-color: '.$this->quiz->get_quiz_bg_color().';
    color: '.$this->quiz->get_quiz_text_color().';
}
#enp-quiz .enp-quiz__title,
#enp-quiz .enp-question__question,
#enp-quiz .enp-option__label,
#enp-quiz .enp-question__helper,
#enp-quiz .enp-results__score__title,
#enp-quiz .enp-results__encouragement,
#enp-quiz .enp-results__description,
#enp-quiz .enp-results__share-title,
#enp-quiz .enp-explanation__title,
#enp-quiz .enp-explanation__explanation,
#enp-quiz .enp-explanation__percentage {
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

		// get the posted id
		if(isset($_POST['enp-quiz-id'])) {
			$save_data['quiz_id'] = $_POST['enp-quiz-id'];
		}
		// validate nonce
		$nonce_name = 'enp_quiz_take_'.$save_data['quiz_id'].'_nonce';
		if(isset($_POST[$nonce_name])) {
			$posted_nonce = $_POST[$nonce_name];
		}

	    //Is it a POST request?
 	    if($_SERVER['REQUEST_METHOD'] === 'POST') {

 		   //Validate the form key
 		   if(!isset($posted_nonce) || !$this->nonce->validate($posted_nonce)) {
 			   // Form key is invalid,
			   // return them to the page (they're probably refreshing the page)
			   return false;
 		   }
 	    }
		// get the posted data
		// get user action
		if(isset($_POST['enp-question-submit'])) {
			$save_data['user_action'] = $_POST['enp-question-submit'];
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

		// parse the JSON response
		$this->response = json_decode($response);
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

	public function quiz_restart() {
		// clear the cookies and send them back to the beginning of the quiz
		$this->unset_cookies();
	}

	public function set_total_questions() {
		$this->total_questions = count($this->quiz->get_questions());
	}

	public function set_current_question_id() {
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
			// if a state is set (meaning, we have a response) & the state is 'question', that means we're moving on, so get the next_question response
			elseif($this->state === 'question') {
				$question_id = $this->response->next_question->question_id;
			}
		}
		// check if we're resetting the quiz
		// restarting a quiz
		elseif(isset($_POST['enp-quiz-restart'])) {
			$question_ids = $this->quiz->get_questions();
			// set the first question off of the question_ids from the quiz
			$question_id = $question_ids[0];
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

		$this->current_question_id = $question_id;
	}

	public function set_current_question_number() {
		// if we're at the end, the current question number is the total of the questions
		$current_question_number = 0;
		// check state
		if($this->state === 'quiz_end') {
			$this->current_question_number = $this->total_questions;
		} else {
			// find it off of the quiz array
			$question_ids = $this->quiz->get_questions();
			// set counter at 1 because we want the first question to be 1 not 0
			$i = 1;
			// loop question ids
			foreach($question_ids as $question_id) {
				// if current question id matches the array question id, set the counter as the current question number
				if((int)$question_id === (int)$this->current_question_id) {
					$current_question_number = $i;
					// we got it! break out
					break;
				} else {
					// didn't find it yet, increase the counter
					$i++;
				}
			}

		}

		$this->current_question_number = $current_question_number;
	}

	public function set_state() {
		$quiz_state_cookie_name = 'enp_take_quiz_'.$this->quiz->get_quiz_id().'_state';
		// set state off response, if it's there

		if(isset($this->response) && !empty($this->response)) {
			$this->state = $this->response->state;
		}
		// restarting a quiz
		elseif(isset($_POST['enp-quiz-restart'])) {
			$this->state = 'question';
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

	public function get_question_container_class() {
		$class = '';
		if($this->state === 'question') {
			$class = 'enp-question__container--unanswered';
		} elseif($this->state === 'question_explanation') {
			$class = 'enp-question__container--explanation';
		} elseif($this->state === 'quiz_end') {
			$class = 'enp-quiz-end';
		}
		return $class;
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
			setcookie('enp_take_quiz_'.$quiz_id.'_state', $this->state, $week, '/');
		} else {
			return false;
		}

		// question number
		if($this->state === 'question') {
			setcookie('enp_take_quiz_'.$quiz_id.'_question_id', $this->current_question_id, $week, '/');
		}
		// if we're on a question explanation, how'd they do for that question?
		// next question
		elseif($this->state === 'question_explanation' && !empty($this->response)) {
			setcookie('enp_take_quiz_'.$quiz_id.'_'.$this->current_question_id, $this->response->response_correct, $week, '/');
		}
	}

	public function unset_cookies() {
		$quiz_id = $this->quiz->get_quiz_id();
		$question_ids = $this->quiz->get_questions();

		// loop through all questions and unset their cookie
		foreach($question_ids as $question_id) {
			// build cookie name
			$cookie_name = 'enp_take_quiz_'.$quiz_id.'_'.$question_id;
			setcookie($cookie_name, '', time() - 3600, '/');
		}

	}

	/**
	* if it's the first question, we need to save the initial quiz view
	* and question view. All other quiz and question view data is handled
	* by the response class
	* @param quiz object
	* @param question_id (to see if we're on the first question)
	*/
	protected function save_initial_view_data() {
		if($this->state === 'question') {
			// we're on a question. It might be the first one, let's find out!
			$question_ids = $this->quiz->get_questions();
			if((int) $this->current_question_id === (int) $question_ids[0]) {
				// we're on the first question of the first quiz in the question state, so we can update the quiz views and first question view
				// save quiz view
				$quiz_data = new Enp_quiz_Save_quiz_take_Quiz_data($this->quiz);
				$quiz_data->update_quiz_views();
				// save question view
				$save_question_view = new Enp_quiz_Save_quiz_take_Question_view($this->current_question_id);
			}
		}
	}

}
