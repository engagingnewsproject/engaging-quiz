<?php

/**
 * Loads and generates the Quiz_create
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/Quiz_create
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version,
 * and registers & enqueues quiz create scripts and styles
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/Quiz_create
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Quiz_create extends Enp_quiz_Create {
    public function __construct() {
        // we're including this as a fallback for the other pages.
        // process save, if necessary
        add_action('template_redirect', array($this, 'save_quiz'), 1);
        // Other page classes will not need to do this
        add_filter( 'the_content', array($this, 'load_template' ));
        // runs after load_template because load_template clears out the content
        add_filter( 'the_content', array($this, 'display_errors' ), 11);
        // load take quiz styles
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

    }

    public function load_template() {
        include_once( ENP_QUIZ_CREATE_TEMPLATES_PATH.'/quiz-create.php' );
    }

    public function display_errors($content) {
        $errors = '';
        if(!empty($this->errors)) {
            $errors = '<section class="enp-quiz-errors enp-container">
                        <h2 class="enp-quiz-errors__title">Form Errors</h2>
                        <ul class="enp-errors__list">';
                foreach($this->errors as $error) {
                    $errors .= '<li class="enp-errors__item">'.$error.'</li>';
                }
            $errors .='</ul>
                    </section>';
        }
        $content = $content . $errors;
        return $content;
    }

    public function enqueue_styles() {

	}

	/**
	 * Register and enqueue the JavaScript for quiz create.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

        wp_register_script( $this->plugin_name.'-accordion', plugin_dir_url( __FILE__ ) . '../js/utilities/accordion.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-accordion' );

        wp_register_script( $this->plugin_name.'-quiz-create', plugin_dir_url( __FILE__ ) . '../js/quiz-create.min.js', array( 'jquery', $this->plugin_name.'-accordion' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-quiz-create' );

	}

    public function save_quiz() {
        // make sure they're logged in
        $this->validate_user();

        // get access to wpdb
        global $wpdb;

        // start an empty errors array. return the errors array at the end if they exist
        $this->errors = array('No Title');



        if(!empty($this->errors)) {
            return false;
        }

        $quiz_table_name = $wpdb->prefix . 'enp_quiz';
        $questions_table_name = $wpdb->prefix . 'enp_questions';
        $user_id = get_current_user_id();

        $db = new enp_quiz_Db();
        if(isset($_POST['save_type']) && $_POST['save_type'] === 'insert') {
            // process quiz
            $quiz = array(
                'quiz_title' => $this->set_title(),
                'quiz_status'=> 'draft',
                'quiz_owner' => $user_id,
                'quiz_created_by' => $user_id,
            );

            $db->insert($quiz_table_name, $quiz);
        } elseif(isset($_POST['save_type']) && $_POST['save_type'] === 'update') {
            // get the current quiz from the database

            // check to make sure the current user matches the quiz_owner

            // update the quiz entry

            // update or insert the question entry

                // update or insert mc or slider


            $quiz = array(
                'quiz_title' => 'Wuteverz Save Updated',
                'quiz_status'=> 'draft',
            );

            $bind = array(
                ":quiz_id" => 1,
                ":quiz_owner" => $user_id
            );

            $db->update($quiz_table_name, $quiz);
        }

    }

    public function process_string($posted_string, $default) {
        $string = $default;
        if(isset($posted_string)) {
            $posted_string = sanitize_text_field($posted_string);
            if(!empty($posted_string)) {
                $string = $posted_string;
            }
        }
        return $string;
    }

    public function validate_user() {
        if(is_user_logged_in() === false) {
            wp_redirect( home_url( '/login/' ) );
            exit();
        }
    }

}
