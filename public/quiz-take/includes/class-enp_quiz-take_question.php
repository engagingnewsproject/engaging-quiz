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
class Enp_quiz_Take_Question extends Enp_quiz_Take {
	public $question;
	/**
	* This is a big constructor. We require our files, check for $_POST submission,
	* set states, and all other details we're sure to need for our templating
	*
	*/
	public function __construct($qt_parent) {
		$this->parent = $qt_parent;
		// set question
		$this->set_question();
		// save a question view if we're on a question
		if($this->parent->state === 'question') {
			$this->save_question_view();
		}
		// set random vars if necessary
		if($this->parent->state === 'question_explanation') {
			$this->set_question_explanation_vars();
		}
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
		// if we have a question id, get the question data for it
		if(!empty($this->parent->current_question_id)) {
			$question = new Enp_quiz_Question($this->parent->current_question_id);
		}

		$this->question = $question;
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

	public function set_question_explanation_title() {
		$title = 'incorrect';
		if($this->parent->response->response_correct === '1') {
			$title = 'correct';
		}
		$this->question_explanation_title = $title;
	}

	public function set_question_explanation_percentage() {
		if($this->parent->response->response_correct === '1') {
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

	public function question_js_templates() {
		// clone the object so we don't reset its own values
		$qt_question = clone $this;
		foreach($qt_question->question as $key => $value) {
			if($key === 'question_image') {
				// question_image should be blank
				// because it messes up the templating for srcset
				// and src when it thinks it has a value
				$qt_question->question->$key = '';
			} else {
				$qt_question->question->$key = '{{'.$key.'}}';
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
		$qt_question = clone $this;

		foreach($qt_question->question as $key => $value) {
			if($key === 'question_image') {
				// set it to a bogus image value that matches at least
			}
			$qt_question->question->$key = '{{'.$key.'}}';
		}

		foreach($qt_question as $key => $value) {
			// we don't want to unset our question object
			if($key !== 'question') {
				$qt_question->$key = '{{'.$key.'}}';
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
