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
	public static $message,
				  $nonce,
				  $saved_quiz_id,
				  $user_action;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		// set-up class
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		include_once(WP_CONTENT_DIR.'/enp-quiz-config.php');
		// load take quiz styles
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('init', array($this, 'set_enp_quiz_nonce'), 1);
		add_action('init', array($this, 'add_enp_quiz_rewrite_tags'));

		// redirect to the page they should go to
		add_action('template_redirect', array($this, 'enp_quiz_template_rewrite_catch' ));

		// we're including this as a fallback for the other pages.
        // process save, if necessary
        // if the enp-quiz-submit is posted, then they probably want to try to
        // save the quiz. Be nice, try to save the quiz.
        if(isset($_POST['enp-quiz-submit'])) {
            add_action('template_redirect', array($this, 'save_quiz'), 1);
        }
		// custom action hook for displaying messages
        add_action( 'enp_quiz_display_messages', array($this, 'display_messages' ));
	}

	public function set_enp_quiz_nonce() {
		//Start the session
	   session_start();
	   //Start the class
	   self::$nonce = new Enp_quiz_Nonce();
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
	public function add_enp_quiz_rewrite_tags(){
		add_rewrite_tag( '%enp_quiz_template%', '([^/]+)' );
		add_rewrite_tag( '%enp_quiz_id%', '([^/]+)' );
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
		// make sure we have a user
		$this->validate_user();
		// validated
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

	/**
	* Get the requested quiz_id from the URL
	* @return	quiz_id if found, else false
	* @since    0.0.1
	**/
	public function enp_quiz_id_rewrite_catch() {
		global $wp_query;
		$quiz_id = false;
		// see if enp_quiz_template is one of the query_vars posted
		if ( array_key_exists( 'enp_quiz_id', $wp_query->query_vars ) ) {
			// if it's there, then see what the value is
			$quiz_id = $wp_query->get( 'enp_quiz_id' );
		}

		return $quiz_id;
	}

	/**
	* Get the requested ab_test_id from the URL
	* @return	quiz_id if found, else false
	* @since    0.0.1
	**/
	public function enp_ab_test_id_rewrite_catch() {
		// same as the quiz_id request right now
		return enp_quiz_id_rewrite_catch();
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

	public function load_quiz() {
        // prepare the quiz object
        $quiz_id = $this->enp_quiz_id_rewrite_catch();
		// set-up variables
        $quiz = new Enp_quiz_Quiz($quiz_id);
        return $quiz;
    }

	/*
	* for child classes to set the variable on the user_action
	*/
	public function load_user_action() {
		if(!empty(self::$user_action)) {
			return self::$user_action;
		} elseif(isset($_GET['enp_user_action'])) {
			return $_GET['enp_user_action'];
		} else {
			return false;
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


	public function save_quiz() {
		// make sure they're logged in. returns current_user_id
		$user_id = $this->validate_user();

	   //Is it a POST request?
 	   if($_SERVER['REQUEST_METHOD'] === 'POST') {

 		   //Validate the form key
 		   if(!isset($_POST['enp_quiz_nonce']) || !self::$nonce->validate()) {
 			   // Form key is invalid,
			   // return them to the page (they're probably refreshing the page)
			   self::$message['error'][] = 'Quiz was not resaved';

			   return false;
 		   }
 	   }

		// get access to wpdb
		global $wpdb;
		// set-up an empty array for our quiz submission
		$quiz = array();

		// extract values
		// set the date_time to pass
		$date_time = date("Y-m-d H:i:s");
		// build our array to save
		if(isset($_POST['enp_quiz'])) {
			$quiz = array(
						'quiz' => $_POST['enp_quiz'],
						'quiz_updated_by' => $user_id,
						'quiz_updated_at' => $date_time,
					);
		}

		if(isset($_POST['enp_question'])) {
			$quiz['question'] = $_POST['enp_question'];
		}

		if(isset($_POST['enp-quiz-submit'])) {
			// get the value of the button they clicked
			$button_clicked = $_POST['enp-quiz-submit'];
			$quiz['user_action'] = $button_clicked;
		} else {
			// no submit button clicked? Should never happen
			self::$message['error'][] = 'The form was not submitted right. Please contact our support and let them know how you reached this error';
			return false;
		}

		// initiate the save_quiz object
		$save_quiz = new Enp_quiz_Save_quiz();
		// save the quiz by passing our $quiz array to the save function
		$response = $save_quiz->save($quiz);

		// set it as our messages to return to the user
		self::$message = $response->message;

		// get the ID of the quiz that was just created (if there)
		self::$saved_quiz_id = $response->quiz_id;
		// set-up vars for our next steps
		$save_action = $response->action;
		// set the user_action so we know what the user was wanting to do
		self::$user_action = $response->user_action;

		// check to see if we have a successful save response from the save class
		// REMEMBER: A successful save can still have an error message
		// such as "Quiz Updated. Hey! You don't have any questions though!"
		if($response->status !== 'success') {
			// No successful save, so return them to the same page and display error messages
			return false;
		}
		  //*************************//
		 //  SUCCESS! Now what...?  //
		//*************************//

		// if they want to go to the preview page AND there are no errors,
		// let them move on to the preview page
		if(self::$user_action['action'] === 'next' && self::$user_action['element'] === 'preview' && empty(self::$message['error'])) {
			$this->redirect_to_quiz_preview();
		}
		// if they want to move on to the quiz-publish page and there are no errors, let them
		elseif(self::$user_action['action'] === 'next' && self::$user_action['element'] === 'publish' && empty(self::$message['error'])) {
			$this->redirect_to_quiz_publish();
		}
		// catch if we're just creating the new quiz, send them to the new quiz page
		elseif($save_action === 'insert') {
			// they don't want to move on yet, but they're inserting,
			// so we need to send them to their newly created quiz create page
			$this->redirect_to_quiz_create();
		}
		// we're just updating the same page, return false to send them back
	 	else {
			// we have errors! Oh no! Send them back to fix it
			return false;
		}
	}

	protected function redirect_to_quiz_create() {
		// set a messages array to pass to url on redirect
		$url_query = http_build_query(array('enp_messages' => self::$message, 'enp_user_action'=> self::$user_action));
		// they just created a new page (quiz) so we need to redirect them to it and post our messages
		wp_redirect( site_url( '/enp-quiz/quiz-create/'.self::$saved_quiz_id.'/?'.$url_query ) );
		exit;
	}

	protected function redirect_to_quiz_preview() {
		wp_redirect( site_url( '/enp-quiz/quiz-preview/'.self::$saved_quiz_id.'/' ) );
		exit;
	}

	protected function redirect_to_quiz_publish() {
		wp_redirect( site_url( '/enp-quiz/quiz-publish/'.self::$saved_quiz_id.'/' ) );
		exit;
	}

	/**
	* Process any error/success messages and output
	* them to the browser.
	* @return false if message, HTML output with messages if found
	* @usage Display in templates using an action hook
	*   	 do_action('enp_quiz_display_messages');
	*		 To set error messages from child classes, add
	*		 parent::$messages['error'][] = 'error message';
	*/
	public function display_messages() {
		// try to get self::$message first bc they might
		// have reloaded a page with a $_GET variable or something
		// and we want our self::$message ones to override that
		if(!empty(self::$message)) {
			// check for self first
			$messages = self::$message;
		} elseif(isset($_GET['enp_messages'])) {
			// check URL second
			$messages = $_GET['enp_messages'];
		} else {
			// no messages. Fail.
			return false;
		}

        $message_content = '';
        if(!empty($messages['error'])) {
            $message_type = 'error';
			$message_content .= $this->display_message_html($messages['error'], $message_type);
        }
		if(!empty($messages['success'])) {
            $message_type = 'success';
			$message_content .= $this->display_message_html($messages['success'], $message_type);
        }

        if(!empty($message_content)) {
            echo $message_content;
        } else {
			return false;
		}

    }

	public function display_message_html($messages, $message_type) {
		$message_html = '';
		if(!empty($messages) && !empty($message_type)) {
			$message_html .= '<section class="enp-quiz-message enp-quiz-message--'.$message_type.' enp-container">
						<h2 class="enp-quiz-message__title enp-quiz-message__title--'.$message_type.'"> '.$message_type.'</h2>
						<ul class="enp-message__list enp-message__list--'.$message_type.'">';
				foreach($messages as $message) {
					$message_html .= '<li class="enp-message__item enp-message__item--'.$message_type.'">'.$message.'</li>';
				}
			$message_html .='</ul>
					</section>';
		}

		return $message_html;
	}


	/**
	 * Validate that the user is allowed to be doing this
	 * @return   get_current_user_id(); OR Redirect to login page
	 * @since    0.0.1
	 */
	public function validate_user() {
		if(is_user_logged_in() === false) {
			auth_redirect();
		} else {
			return get_current_user_id();
		}
	}

}
