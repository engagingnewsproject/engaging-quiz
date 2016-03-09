<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/quiz-create
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version,
 * and registers & enqueues quiz create scripts and styles
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Create {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once(WP_CONTENT_DIR.'/enp-quiz-config.php');
		
		// load take quiz styles
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		add_action('init', array($this, 'add_quiztemplate_rewrite_tag'));
		add_action('template_redirect', array($this, 'quiztemplate_rewrite_catch' ));
	}

	/**
	 * Register and enqueue the stylesheets for quiz create.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {


		wp_register_style( $this->plugin_name.'-quiz-create', plugin_dir_url( __FILE__ ) . 'css/enp_quiz-create.min.css', array(), $this->version );
 	  	wp_enqueue_style( $this->plugin_name.'-quiz-create' );

	}

	/**
	 * Register and enqueue the JavaScript for quiz create.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name.'-quiz-create', plugin_dir_url( __FILE__ ) . 'js/enp_quiz-create.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name.'-quiz-create' );

	}
	/*
	*	Adds a quiztemplate parameter for WordPress to look for
	*   ?quiztemplate=dashboard
	*/
	public function add_quiztemplate_rewrite_tag(){
		add_rewrite_tag( '%quiztemplate%', '([^/]+)' );
	}

	/*
	* If we find a quiztemplate parameter, process it
	* and use the right template file
	* This deletes the_title and the_content and replaces it
	* with our own HTML so we can use their default template, but
	* use our own content injected into their template.
	*
	* @since    0.0.1
	*/
	public function quiztemplate_rewrite_catch() {
		global $wp_query;
		// see if quiztemplate is one of the query_vars posted
		if ( array_key_exists( 'quiztemplate', $wp_query->query_vars ) ) {
			// if it's there, then see what the value is
			$this->template = $wp_query->get( 'quiztemplate' );
			$this->template_file = ENP_QUIZ_CREATE_TEMPLATES_PATH.'/'.$this->template.'.php';
			// make sure there's something there
			if(!empty($this->template)) {
				// remove the title of the page so we can use ours
				add_filter( 'the_title', array($this, 'remove_title' ));
				// load the template
				$this->load_template();
			}

		}
	}

	/*
	* Load the requested template file.
	* If it's not found, show the dashboard instead
	* @since    0.0.1
	*/
	public function load_template() {
		// check to make sure the template file exists
		if(file_exists($this->template_file)) {
			// set our classname to load (ie - load_dashboard)
			$load_template = 'load_'.$this->template;
			// load the template dynamically based on the template name
			$this->$load_template();
		} else {
			// if we can't find it, fallback to the dashboard
			$this->load_dashboard();
		}
	}

	/*
	* Erases the title of the page so we have more control
	* @since    0.0.1
	*/
	public function remove_title() {
		return '';
	}

	public function load_abTest() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-abTest.php');
		new Enp_quiz_abTest();
	}

	public function load_abResults() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-abResults.php');
		new Enp_quiz_abResults();
	}

	public function load_dashboard() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-dashboard.php');
		new Enp_quiz_Dashboard();
	}

	public function load_quizCreate() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quizCreate.php');
		new Enp_quiz_quizCreate();
	}

	public function load_quizPreview() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quizPreview.php');
		new Enp_quiz_quizPreview();
	}

	public function load_quizPublish() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quizPublish.php');
		new Enp_quiz_quizPublish();
	}

	public function load_quizResults() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quizResults.php');
		new Enp_quiz_quizResults();
	}
}
