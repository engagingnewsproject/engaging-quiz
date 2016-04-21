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
class Enp_quiz_Take_Quiz_end extends Enp_quiz_Take {
	public $score,
		   $dashoffset,
		   $encouragement,
		   $description;

	/**
	* This is a big constructor. We require our files, check for $_POST submission,
	* set states, and all other details we're sure to need for our templating
	*
	*/
	public function __construct($quiz_id = false) {

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

	public function get_current_score() {
		return $this->current_score;
	}


	/**
	* I can't think of a better way to do this right now, but I think this is OK
	* It loops all keys in the object and sets the values as handlebar style strings
	* and injects it into the template
	*/
	public function quiz_results_template() {
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

		$template = '<script type="text/template" id="quiz_end_template">';
		ob_start();
		include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'partials/quiz-results.php');
		$template .= ob_get_clean();
		$template .= '</script>';

		return $template;
	}




}
