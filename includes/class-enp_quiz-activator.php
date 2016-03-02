<?php

/**
 * Fired during plugin activation
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    Enp_quiz
 * @subpackage Enp_quiz/includes
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		// add our rewrite rules to htaccess
		$this->add_rewrite_rules();

		// hard flush on rewrite rules so it regenerates the htaccess file
		flush_rewrite_rules();
	}

	public function add_rewrite_rules() {
		add_rewrite_rule('quiz','wp-content/plugins/enp-quiz/public/quiz-take/templates/quiz.php','top');
	}


}
