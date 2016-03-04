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

		global $wpdb;
		$this->create_tables($wpdb);

	}

	protected function add_rewrite_rules() {
		add_rewrite_rule('quiz','wp-content/plugins/enp-quiz/public/quiz-take/templates/quiz.php','top');
	}

	protected function create_tables($wpdb) {

		$charset_collate = $wpdb->get_charset_collate();

		$quiz_table_name = $wpdb->prefix . 'enp_quiz';
		$quiz_sql = "CREATE TABLE $quiz_table_name (
					quiz_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					quiz_title VARCHAR(255) NOT NULL,
					quiz_status VARCHAR(11) NOT NULL,
					quiz_finish_message VARCHAR(510) NOT NULL,
					quiz_color_bg VARCHAR(7) NOT NULL,
					quiz_color_text VARCHAR(7) NOT NULL,
					quiz_color_border VARCHAR(7) NOT NULL,
					quiz_owner BIGINT(20) NOT NULL,
					quiz_created_by BIGINT(20) NOT NULL,
					quiz_created_on DATETIME NOT NULL,
					quiz_updated_by BIGINT(20) NOT NULL,
					quiz_updated_on DATETIME NOT NULL,
					quiz_views BIGINT(20) NOT NULL DEFAULT '0',
					quiz_starts BIGINT(20) NOT NULL DEFAULT '0',
					quiz_finishes BIGINT(20) NOT NULL DEFAULT '0',
					quiz_score_average FLOAT(3) NOT NULL DEFAULT '0',
					quiz_time_spent BIGINT(40) NOT NULL DEFAULT '0',
					quiz_time_spent_average BIGINT(20) NOT NULL DEFAULT '0',
					PRIMARY KEY  (quiz_id)
				) $charset_collate;";

		$question_table_name = $wpdb->prefix . 'enp_question';
		$question_sql = "CREATE TABLE $question_table_name (
					question_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					quiz_id BIGINT(20) NOT NULL,
					question_title VARCHAR(255) NOT NULL,
					question_order BIGINT(3) NOT NULL,
					question_type VARCHAR(20) NOT NULL,
					question_explanation VARCHAR(510) NOT NULL,
					question_views BIGINT(20) NOT NULL DEFAULT '0',
					question_responses BIGINT(20) NOT NULL DEFAULT '0',
					question_responses_correct BIGINT(20) NOT NULL DEFAULT '0',
					question_responses_incorrect BIGINT(20) NOT NULL DEFAULT '0',
					question_responses_correct_percentage FLOAT(3) NOT NULL DEFAULT '0',
					question_responses_incorrect_percentage FLOAT(3) NOT NULL DEFAULT '0',
					question_score_average BIGINT(20) NOT NULL,
					question_time_spent BIGINT(40) NOT NULL,
					question_time_spent_average BIGINT(20) NOT NULL,
					PRIMARY KEY  (question_id),
					FOREIGN KEY  (quiz_id) REFERENCES $quiz_table_name (quiz_id)
				) $charset_collate;";


		$mc_option_table_name = $wpdb->prefix . 'enp_mc_option';
		$mc_option_sql = "CREATE TABLE $mc_option_table_name (
					mc_option_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					question_id BIGINT(20) NOT NULL,
					mc_option_content VARCHAR(255) NOT NULL,
					mc_option_correct BOOLEAN NOT NULL,
					mc_option_order BIGINT(3) NOT NULL,
					PRIMARY KEY  (mc_option_id),
					FOREIGN KEY  (question_id) REFERENCES $question_table_name (question_id)
				) $charset_collate;";


		$slider_table_name = $wpdb->prefix . 'enp_slider';
		$slider_sql = "CREATE TABLE $slider_table_name (
					slider_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					question_id BIGINT(20) NOT NULL,
					slider_range_high BIGINT(20) NOT NULL,
					slider_range_low BIGINT(20) NOT NULL,
					slider_answer_high BIGINT(20) NOT NULL,
					slider_answer_low BIGINT(20) NOT NULL,
					slider_increment BIGINT(20) NOT NULL,
					slider_prefix VARCHAR(70) NOT NULL,
					slider_suffix VARCHAR(70) NOT NULL,
					PRIMARY KEY  (slider_id),
					FOREIGN KEY  (question_id) REFERENCES $question_table_name (question_id)
				) $charset_collate;";

		$response_table_name = $wpdb->prefix . 'enp_response';
		$response_sql = "CREATE TABLE $response_table_name (
					response_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					question_id BIGINT(20) NOT NULL,
					response_correct BOOLEAN NOT NULL,
					PRIMARY KEY  (response_id),
					FOREIGN KEY  (question_id) REFERENCES $question_table_name (question_id)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

				dbDelta($quiz_sql);
				dbDelta($question_sql);
				dbDelta($mc_option_sql);
				dbDelta($slider_sql);
				dbDelta($response_sql);


	}





}
