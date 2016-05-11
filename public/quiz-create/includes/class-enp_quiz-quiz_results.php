<?php

/**
 * Loads and generates the Quiz_results
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/Quiz_results
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version,
 * and registers & enqueues quiz create scripts and styles
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/Quiz_results
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Quiz_results extends Enp_quiz_Create {
    public $quiz;
    public function __construct() {
        // load the quiz
        $this->quiz = $this->load_quiz();
        // we're including this as a fallback for the other pages.
        // Other page classes will not need to do this
        add_filter( 'the_content', array($this, 'load_template' ));
        // load take quiz styles
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function load_template() {
        ob_start();
        //Start the class
        $quiz = $this->quiz;
        include_once( ENP_QUIZ_CREATE_TEMPLATES_PATH.'quiz-results.php' );
        $content = ob_get_contents();
        if (ob_get_length()) ob_end_clean();

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
        // Register the script
        wp_register_script('quiz_results', '/');
        $all_quiz_scores = $this->quiz->get_quiz_scores_group_count();
        $quiz_scores_labels = array();
        $quiz_scores = array();
        foreach($all_quiz_scores as $key => $val) {
            $quiz_scores_labels[] = $key.'%';
            $quiz_scores[] = $val;
        }

        $quiz_results = array(
            'quiz_scores' => $quiz_scores,
            'quiz_scores_labels' => $quiz_scores_labels,
        );
        wp_localize_script( 'quiz_results', 'quiz_results', $quiz_results );
        var_dump($quiz_results);
        // Enqueued script with localized data.
        wp_enqueue_script( 'quiz_results' );
        // charts
        wp_register_script( $this->plugin_name.'-charts', plugin_dir_url( __FILE__ ) . '../js/utilities/Chart.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-charts' );
        // accordion
        wp_register_script( $this->plugin_name.'-accordion', plugin_dir_url( __FILE__ ) . '../js/utilities/accordion.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-accordion' );
        // general scripts
		wp_register_script( $this->plugin_name.'-quiz-results', plugin_dir_url( __FILE__ ) . '../js/quiz-results.js', array( 'jquery', $this->plugin_name.'-accordion', $this->plugin_name.'-charts' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-quiz-results' );

	}

    public function mc_option_correct_icon($correct) {
        if($correct === '1') {
            $svg = '<svg class="enp-icon enp-icon--close enp-results-question__option__icon enp-results-question__option__icon--correct">
                <use xlink:href="#icon-check" />
            </svg>';
        } else {
            $svg = '<svg class="enp-icon enp-icon--close enp-results-question__option__icon enp-results-question__option__icon--incorrect">
                <use xlink:href="#icon-close" />
            </svg>';
        }

        return $svg;
    }


}
