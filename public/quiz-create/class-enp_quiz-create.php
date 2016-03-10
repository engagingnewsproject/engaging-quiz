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

		add_action('init', array($this, 'add_enp_quiz_template_rewrite_tag'));
		add_action('template_redirect', array($this, 'enp_quiz_template_rewrite_catch' ));
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
	*	Adds a enp_quiz_template parameter for WordPress to look for
	*   ?enp_quiz_template=dashboard
	*/
	public function add_enp_quiz_template_rewrite_tag(){
		add_rewrite_tag( '%enp_quiz_template%', '([^/]+)' );
	}

	/*
	* If we find a enp_quiz_template parameter, process it
	* and use the right template file
	* This deletes the_title and the_content and replaces it
	* with our own HTML so we can use their default template, but
	* use our own content injected into their template.
	*
	* @since    0.0.1
	*/
	public function enp_quiz_template_rewrite_catch() {
		global $wp_query;
		// see if enp_quiz_template is one of the query_vars posted
		if ( array_key_exists( 'enp_quiz_template', $wp_query->query_vars ) ) {
			// if it's there, then see what the value is
			$this->template = $wp_query->get( 'enp_quiz_template' );
			$this->template_file = ENP_QUIZ_CREATE_TEMPLATES_PATH.'/'.$this->template.'.php';
			// make sure there's something there
			if(!empty($this->template)) {
				// convert the dashes (-) to underscores (_) so it will match a function
				$this->template_underscored = str_replace('-','_',$this->template);
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
		// load our default page template instead of their theme template
		add_filter('template_include', array($this, 'load_page_template'), 1, 1);
		// add enp-quiz class to the body
		add_filter('body_class', array($this, 'add_enp_quiz_body_class'));
		// check to make sure the template file exists
		if(file_exists($this->template_file)) {
			// set our classname to load (ie - load_dashboard)
			$load_template = 'load_'.$this->template_underscored;
			// load the template dynamically based on the template name
			$this->$load_template();
		} else {
			// if we can't find it, fallback to the dashboard
			$this->load_dashboard();
		}
	}

	public function add_enp_quiz_body_class($classes) {
		$classes[] = 'enp-quiz';
		$classes[] = 'enp-'.$this->template;
		return $classes;
	}

	public function load_page_template() {
		return ENP_QUIZ_CREATE_TEMPLATES_PATH.'/enp-quiz-page.php';
	}

	public function load_ab_test() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-ab_test.php');
		new Enp_quiz_AB_test();
	}

	public function load_ab_results() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-ab_results.php');
		new Enp_quiz_AB_results();
	}

	public function load_dashboard() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-dashboard.php');
		new Enp_quiz_Dashboard();
	}

	public function load_quiz_create() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quiz_create.php');
		new Enp_quiz_Quiz_create();
	}

	public function load_quiz_preview() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quiz_preview.php');
		new Enp_quiz_Quiz_preview();
	}

	public function load_quiz_publish() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quiz_publish.php');
		new Enp_quiz_Quiz_publish();
	}

	public function load_quiz_results() {
		include_once(dirname(__FILE__).'/includes/class-enp_quiz-quiz_results.php');
		new Enp_quiz_Quiz_results();
	}
}
