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
        // Other page classes will not need to do this
        add_filter( 'the_content', array($this, 'load_content' ));
        // load take quiz styles
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

    }

    public function load_content($content) {

        ob_start();
        $quiz = $this->load_quiz();
        include_once( ENP_QUIZ_CREATE_TEMPLATES_PATH.'/quiz-create.php' );
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

        wp_register_script( $this->plugin_name.'-accordion', plugin_dir_url( __FILE__ ) . '../js/utilities/accordion.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-accordion' );

        wp_register_script( $this->plugin_name.'-quiz-create', plugin_dir_url( __FILE__ ) . '../js/quiz-create.min.js', array( 'jquery', $this->plugin_name.'-accordion' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-quiz-create' );

	}

}
