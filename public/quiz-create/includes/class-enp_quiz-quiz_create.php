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
        // if the enp-quiz-submit is posted, then they probably want to try to
        // save the quiz. Be nice, try to save the quiz.
        if(isset($_POST['enp-quiz-submit'])) {
            add_action('template_redirect', array($this, 'save_quiz'), 1);
        }
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
        // make sure they're logged in. returns current_user_id
        $user_id = $this->validate_user();

        // get access to wpdb
        global $wpdb;

        // start an empty errors array. return the errors array at the end if they exist
        $this->errors = array();

        $quiz_save = new Enp_quiz_Quiz_save();
        // extract values
        $quiz_id = $quiz_save->process_int('enp-quiz-id', 0);
        $quiz_title = $quiz_save->process_string('enp-quiz-title', 'Untitled');
        $question_title = $quiz_save->process_string('enp-question[0]["question_title"]', 'Untitled');

        $date_time = date("Y-m-d H:i:s");
        // build our array to save
        $quiz = array(
            'quiz_id' => $quiz_id,
            'quiz_title' => $quiz_title,
            'questions' => array(
                                array(
                                    'question_title' => $question_title,
                                )
                            ),
            'quiz_updated_by' => $user_id,
            'quiz_updated_on' => $date_time,
        );

        $this->quiz_save_response = $quiz_save->save_quiz($quiz);
        $this->errors = $this->quiz_save_response['errors'];
        // check to see if there are errors
        if(!empty($this->errors)) {
            // exits the process and returns them to the same page
            return false;
        } else {
            // figure out where they want to go
            if(isset($_POST['enp-quiz-submit'])) {
                // get the value of the button they clicked
                $button_clicked = $_POST['enp-quiz-submit'];
                // if it = preview, send them to the preview page
                if($button_clicked === 'quiz-preview') {
                    wp_redirect( site_url( '/enp-quiz/quiz-preview' ) );
                    exit;
                }

            }

        }

    }


    /**
	 * Validate that the user is allowed to be doing this
	 * @return   get_current_user_id(); OR Redirect to login page
	 * @since    0.0.1
	 */
    public function validate_user() {
        if(is_user_logged_in() === false) {
            wp_redirect( home_url( '/login/' ) );
            exit;
        } else {
            return get_current_user_id();
        }
    }

}
