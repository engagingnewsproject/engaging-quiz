<?php

/**
 * Loads and generates the AB_results
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/AB_results
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version,
 * and registers & enqueues quiz create scripts and styles
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/AB_results
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_AB_results extends Enp_quiz_Quiz_results {
    public $ab_test,
           $quiz_a,
           $quiz_b;

    public function __construct() {
        $this->ab_test = $this->load_ab_test_object();
        $this->quiz_a = new Enp_quiz_Quiz_AB_test_result($this->ab_test->get_quiz_id_a(),$this->ab_test->get_ab_test_id());
        $this->quiz_b = new Enp_quiz_Quiz_AB_test_result($this->ab_test->get_quiz_id_b(), $this->ab_test->get_ab_test_id());
        // we're including this as a fallback for the other pages.
        // Other page classes will not need to do this
        add_filter( 'the_content', array($this, 'load_template' ));
        // load take quiz styles
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // add in json data for scores
        add_action('wp_footer', array($this, 'quiz_results_json'));
    }

    public function load_template() {
        ob_start();
        //Start the class
        $ab_test = $this->ab_test;
        $quiz_a = $this->quiz_a;
        $quiz_b = $this->quiz_b;
        include_once( ENP_QUIZ_CREATE_TEMPLATES_PATH.'/ab-results.php' );
        $content = ob_get_contents();
        if (ob_get_length()) ob_end_clean();

        return $content;

    }

    public function quiz_results_json() {
        $all_quiz_scores = $this->quiz_a->get_quiz_scores_group_count();
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

        echo '<script type="text/javascript">';
		    // print this whole object as js global vars in json
			echo 'var quiz_a_results_json = '.json_encode($quiz_results).';';
		echo '</script>';

        $all_quiz_scores = $this->quiz_b->get_quiz_scores_group_count();
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

        echo '<script type="text/javascript">';
		    // print this whole object as js global vars in json
			echo 'var quiz_b_results_json = '.json_encode($quiz_results).';';
		echo '</script>';
    }

    public function enqueue_styles() {

	}

	/**
	 * Register and enqueue the JavaScript for quiz create.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {
        wp_register_script( $this->plugin_name.'-charts', plugin_dir_url( __FILE__ ) . '../js/utilities/Chart.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-charts' );

        wp_register_script( $this->plugin_name.'-accordion', plugin_dir_url( __FILE__ ) . '../js/utilities/accordion.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-accordion' );
        // general scripts
		wp_register_script( $this->plugin_name.'-ab-results', plugin_dir_url( __FILE__ ) . '../js/ab-results.js', array( 'jquery', 'underscore', $this->plugin_name.'-accordion' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-ab-results' );

	}



}
