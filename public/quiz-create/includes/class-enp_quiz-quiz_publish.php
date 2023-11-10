<?php

/**
 * Loads and generates the Quiz_publish
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/Quiz_publish
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version,
 * and registers & enqueues quiz create scripts and styles
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/Quiz_publish
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 * 
 * Constructor: 
 * The constructor sets up the class. It loads the quiz object using the load_quiz method, 
 * checks if the quiz is valid, and redirects the user to the quiz create page if it's not. 
 * It then includes a content filter and enqueues styles and scripts.
 * 
 * load_quiz: 
 * This method is called in the constructor to load the quiz object. 
 * The var_dump($this->quiz->quiz_status) statement indicates that the class is checking and displaying the quiz status.
 * 
 * validate_quiz_redirect: 
 * This method is used to validate the quiz and redirect the user to the quiz create page if it's not valid. 
 * The specific validation logic is not shown in this code snippet.
 * 
 * load_content: 
 * This method includes the template file quiz-publish.php and captures its output. 
 * The template likely contains the HTML structure for displaying the published quiz.
 * 
 * enqueue_styles: 
 * This method is intended for enqueuing styles but is currently empty. 
 * Styles related to quiz publishing could be added here in the future.
 * 
 * enqueue_scripts: 
 * This method registers and enqueues the JavaScript file quiz-publish.js. 
 * This script is enqueued with a dependency on jQuery and is meant for handling 
 * JavaScript functionality related to quiz publishing.
 * 
 * 
 */
class Enp_quiz_Quiz_publish extends Enp_quiz_Create {
    public $quiz; // object

    public function __construct() {
        // set the quiz object
        $this->quiz = $this->load_quiz();
        do_action( 'qm/debug', $this->quiz->quiz_status );
        // check if it's valid
        // if it's not, they'll get redirected to the quiz create page
        $this->validate_quiz_redirect($this->quiz, 'publish');
        // we're including this as a fallback for the other pages.
        // Other page classes will not need to do this
        add_filter( 'the_content', array($this, 'load_content' ));
        // load take quiz styles
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function load_content($content) {
        ob_start();
        $quiz = $this->quiz;
        $enp_current_page = 'publish';
        include_once( ENP_QUIZ_CREATE_TEMPLATES_PATH.'/quiz-publish.php' );
        $content = ob_get_contents();
        ob_end_clean();

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

		wp_register_script( $this->plugin_name.'-quiz-publish', plugin_dir_url( __FILE__ ) . '../js/quiz-publish.js', array( 'jquery' ), ENP_QUIZ_VERSION, true );
		wp_enqueue_script( $this->plugin_name.'-quiz-publish' );

	}


}
