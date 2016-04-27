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
class Enp_quiz_Take_Quiz_end {
	public $quiz, // Enp_quiz_Quiz Object
		   $score,
		   $score_total_correct,
		   $score_circle_dashoffset,
		   $quiz_end_title,
		   $quiz_end_content;

	/**
	* This is a big constructor. We require our files, check for $_POST submission,
	* set states, and all other details we're sure to need for our templating
	*
	*/
	public function __construct($quiz) {
		$this->quiz = $quiz;
		// set the score
		$this->set_score_total_correct();
		// set the score
		$this->set_score();
		// set the title based on the score
		$this->set_quiz_end_title();
		// set the content based on the score
		$this->set_quiz_end_content();
		// set score circle dashoffset for SVG animation
		$this->set_score_circle_dashoffset();
	}


	/**
	* Get the person's score (what the % of their score is)
	* @param cookies
	* @return score (int)
	*/
	public function set_score_total_correct() {
		$quiz_id = $this->quiz->quiz_id;
		$question_ids = $this->quiz->questions;
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
		$this->score_total_correct = $correct;
	}

	public function set_score() {
		$question_ids = $this->quiz->questions;
		$total_questions = count($question_ids);
		// calculate the score
		$this->score = ($this->score_total_correct / $total_questions) * 100;
	}

	/**
	* Give them a title based on how well they did
	* @param score
	*/
	public function set_quiz_end_title() {

		if(intval($this->score) < 50) {
			$title = "Ouch!";
		} elseif(intval($this->score) < 70) {
			$title = "Not Bad!";
		} elseif (intval($this->score) < 85) {
			$title = "Nice Job!";
		}
		elseif (intval($this->score) < 100) {
			$title = "Fantastic!";
		}
		elseif (intval($this->score) === 100) {
			$title = "Perfect!";
		}
		$this->quiz_end_title = $title;
	}

	public function get_quiz_end_title() {
		return $this->quiz_end_title;
	}

	/**
	* Give them a title based on how well they did
	* @param score
	*/
	public function set_quiz_end_content() {
		// Not so good. Default.

		$content = "We bet you could do better. Why don't you try taking the quiz again?";
		if(intval($this->score) < 70) {
			$content = "We bet you could do better. Why don't you try taking the quiz again?";
		}
		elseif (intval($this->score) < 85) {
			$content = "You did pretty well! Take the quiz again and see if you can get a perfect score this time.";
		}
		elseif (intval($this->score) < 100) {
			$content = "Fantastic!";
		}
		elseif (intval($this->score) === 100) {
			$content = "Can't do any better than that! Go ahead, share this quiz and brag about it.";
		}
		$this->quiz_end_content = $content;
	}

	public function get_quiz_end_content() {
		return $this->quiz_end_content;
	}

	public function get_score() {
		return $this->score;
	}

	public function set_score_circle_dashoffset() {
		$dashoffset = 0;
		if(!empty($this->score)) {
			// calculate the score dashoffset
            $r = 90;
            $c = M_PI*($r*2);
            $dashoffset = ((100-$this->get_score())/100)*$c;
		}
		$this->score_circle_dashoffset = $dashoffset;
	}

	public function get_score_circle_dashoffset() {
		return $this->score_circle_dashoffset;
	}

	public function get_init_json() {
		$quiz_end = clone $this;
		// we already have the quiz
		unset($quiz_end->quiz);
		echo '<script type="text/javascript">';
		// print this whole object as js global vars in json
			echo 'var quiz_end_json = '.json_encode($quiz_end).';';
		echo '</script>';
		// remove the cloned object
		unset($quiz_end);
	}

	/**
	* I can't think of a better way to do this right now, but I think this is OK
	* It loops all keys in the object and sets the values as handlebar style strings
	* and injects it into the template
	*/
	public function quiz_end_template() {
		// clone the object so we don't reset its own values
		$qt_end = clone $this;

		foreach($qt_end as $key => $value) {
			// we don't want to unset our question object
			$qt_end->$key = '{{'.$key.'}}';
		}

		$template = '<script type="text/template" id="quiz_end_template">';
		ob_start();
		include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'partials/quiz-end.php');
		$template .= ob_get_clean();
		$template .= '</script>';

		return $template;
	}

}
