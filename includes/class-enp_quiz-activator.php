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

		// set enp-database-quiz-config file path
		$this->enp_database_config_path = $this->get_enp_database_config_path();

		$database_config_file_exists = $this->check_database_config_file();
		// if doesn't exist, create it
		if($database_config_file_exists === false) {
			//create the config file
			$this->create_database_config_file();
		}

		// set enp-quiz-config file path
		$this->enp_config_path = WP_CONTENT_DIR.'/enp-quiz-config.php';

		$config_file_exists = $this->check_config_file();
		// if doesn't exist, create it
		if($config_file_exists === false) {
			//create the config file
			$this->create_config_file();
		}

	}

	protected function add_rewrite_rules() {
		add_rewrite_rule('quiz','wp-content/plugins/enp-quiz/public/quiz-take/templates/quiz.php','top');
	}

	protected function create_tables($wpdb) {

		$charset_collate = $wpdb->get_charset_collate();
		// quiz table name
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
					quiz_is_archived BOOLEAN DEFAULT 0,
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
					question_is_archived BOOLEAN DEFAULT 0,
					PRIMARY KEY  (question_id),
					FOREIGN KEY  (quiz_id) REFERENCES $quiz_table_name (quiz_id)
				) $charset_collate;";

		$mc_option_table_name = $wpdb->prefix . 'enp_question_mc_option';
		$mc_option_sql = "CREATE TABLE $mc_option_table_name (
					mc_option_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					question_id BIGINT(20) NOT NULL,
					mc_option_content VARCHAR(255) NOT NULL,
					mc_option_correct BOOLEAN NOT NULL,
					mc_option_order BIGINT(3) NOT NULL,
					mc_option_is_archived BOOLEAN DEFAULT 0,
					PRIMARY KEY  (mc_option_id),
					FOREIGN KEY  (question_id) REFERENCES $question_table_name (question_id)
				) $charset_collate;";

		$slider_table_name = $wpdb->prefix . 'enp_question_slider';
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
					slider_is_archived BOOLEAN DEFAULT 0,
					PRIMARY KEY  (slider_id),
					FOREIGN KEY  (question_id) REFERENCES $question_table_name (question_id)
				) $charset_collate;";

		$response_table_name = $wpdb->prefix . 'enp_response';
		$response_sql = "CREATE TABLE $response_table_name (
					response_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					question_id BIGINT(20) NOT NULL,
					response_correct BOOLEAN NOT NULL,
					response_created_on DATETIME NOT NULL,
					response_is_archived BOOLEAN DEFAULT 0,
					PRIMARY KEY  (response_id),
					FOREIGN KEY  (question_id) REFERENCES $question_table_name (question_id)
				) $charset_collate;";

		$response_mc_table_name = $wpdb->prefix . 'enp_response_mc';
		$response_mc_sql = "CREATE TABLE $response_mc_table_name (
					response_mc_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					response_id BIGINT(20) NOT NULL,
					mc_option_id BIGINT(20) NOT NULL,
					PRIMARY KEY  (response_mc_id),
					FOREIGN KEY  (response_id) REFERENCES $response_table_name (response_id),
					FOREIGN KEY  (mc_option_id) REFERENCES $mc_option_table_name (mc_option_id)
				) $charset_collate;";

		$response_slider_table_name = $wpdb->prefix . 'enp_response_slider';
		$response_slider_sql = "CREATE TABLE $response_slider_table_name (
					response_slider_id BIGINT(20) NOT NULL AUTO_INCREMENT,
					response_id BIGINT(20) NOT NULL,
					response_slider BIGINT(20) NOT NULL,
					PRIMARY KEY  (response_slider_id),
					FOREIGN KEY  (response_id) REFERENCES $response_table_name (response_id)
				) $charset_collate;";

		// create a tables array,
		// store all the table names and queries
		$tables = array(
					array(
						'name'=>$quiz_table_name,
		 				'sql'=>$quiz_sql
					),
					array(
						'name'=>$question_table_name,
		 				'sql'=>$question_sql
					),
					array(
						'name'=>$mc_option_table_name,
		 				'sql'=>$mc_option_sql
					),
					array(
						'name'=>$slider_table_name,
		 				'sql'=>$slider_sql
					),
					array(
						'name'=>$response_table_name,
		 				'sql'=>$response_sql
					),
					array(
						'name'=>$response_mc_table_name,
		 				'sql'=>$response_mc_sql
					),
					array(
						'name'=>$response_slider_table_name,
		 				'sql'=>$response_slider_sql
					),
				);

		// require file that allows table creation
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// loop through all of our tables and
		// create them if they haven't already been created
		foreach($tables as $table) {
			$table_name = $table['name'];
			$table_sql = $table['sql'];
			// see if the table exists or not
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				// it doesn't exist, so
				// create the table
				dbDelta($table_sql);
			}
		}
	}

	protected function get_enp_database_config_path() {
		return $_SERVER["DOCUMENT_ROOT"].'/enp-quiz-database-config.php';
	}

	protected function check_database_config_file() {
		// see if the file exists
		if(file_exists($this->enp_database_config_path)) {
			// if the file exists, return true
			return true;
		}
		// if the file doesn't exist, return false
		return false;
	}

	protected function create_database_config_file() {
		// creates and opens the file for writing
		$database_config_file = fopen($this->enp_database_config_path, "w");

$database_connection = '<?php
// Modify these to match your Quiz Database credentials
$enp_db_name = "'.DB_NAME.'";
$enp_db_user = "'.DB_USER.'";
$enp_db_password = "'.DB_PASSWORD.'";
$enp_db_host = "'.DB_HOST.'";
;?>';

		// write to the file
		fwrite($database_config_file, $database_connection);
		// close the file
		fclose($database_config_file);
		return true;
	}

	protected function check_config_file() {
		// see if the file exists
		if(file_exists($this->enp_config_path)) {
			// if the file exists, return true
			return true;
		}
		// if the file doesn't exist, return false
		return false;
	}

	protected function create_config_file() {
		// creates and opens the file for writing
		$config_file = fopen($this->enp_config_path, "w");

$config_contents =
'<?php
include("'.$this->enp_database_config_path.'");
define("ENP_QUIZ_CREATE_TEMPLATES_PATH", "'.ENP_QUIZ_ROOT.'public/quiz-create/templates/");
define("ENP_QUIZ_TAKE_TEMPLATES_PATH", "'.ENP_QUIZ_ROOT.'public/quiz-take/templates/");
?>';

		// write to the file
		fwrite($config_file, $config_contents);
		// close the file
		fclose($config_file);
		return true;
	}

}
