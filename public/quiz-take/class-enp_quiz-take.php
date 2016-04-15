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


	public function __construct( ) {
		// hello!
		$this->load_files();
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

	public function load_svg() {
		$svg = file_get_contents(ENP_QUIZ_PLUGIN_URL.'/public/quiz-take/svg/symbol-defs.svg');
	    return $svg;
	}

	public function load_quiz_styles($styles) {
		return '<style tyle="text/css">
#enp-quiz .enp-quiz__container {
    background-color: '.$styles["quiz_bg_color"].';
    color: '.$styles["quiz_text_color"].';
}
#enp-quiz .enp-quiz__title,
#enp-quiz .enp-question__question,
#enp-quiz .enp-option__label,
#enp-quiz .enp-question__helper {
    color: '.$styles["quiz_text_color"].';
}
</style>';
	}
	/**
	* Save quiz responses
	*/
	public function save_response() {
		// get the posted data
		if(isset($_POST['enp-question-id'])) {
			$question_id = $_POST['enp-question-id'];
		}

		if(isset($_POST['enp-question-type'])) {
			$question_type = $_POST['enp-question-type'];
		}

		if(isset($_POST['enp-question-response'])) {
			$question_response = $_POST['enp-question-response'];
			// find out if their response is correct or not
			if($question_type === 'mc') {
				// validate that this ID is attached to this question
				$question = new Enp_quiz_Question($question_id);
				$question_mc_options = $question->get_mc_options();
				if(in_array($question_response, $question_mc_options)) {
					// it's legit! see if it's right...
					$mc = new Enp_quiz_MC_option($question_response);
					// will return 0 if wrong, 1 if right. We don't care if
					// it's right or not, just that we KNOW if it's right or not
					$response_correct = $mc->get_mc_option_correct();
				} else {
					// not a legit response
					var_dump('Selected option not allowed.');
				}

			}
		}

		// set the date_time to pass
		$date_time = date("Y-m-d H:i:s");

		$response = array(
						'question_id' => $question_id,
						'question_type' => $question_type,
						'question_response' => $question_response,
						'response_correct'  => $response_correct,
						'response_created_at' => $date_time,
						);

		// save the response
		$save_response = new Enp_quiz_Save_response();
		$response_response = $save_response->insert_response($response);
		$response_response = json_encode($response_response);
		var_dump($response_response);
		return $response_response;
	}

}
