<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://engagingnewsproject.org
 * @since      0.0.1
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version,
 * and registers & enqueues admin scripts and styles
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/admin
 * @author     Engaging News Project <jones.jeremydavid@gmail.com>
 */
class Enp_quiz_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// load take quiz styles
		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
		// load take quiz scripts
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {

		 wp_register_style( $this->plugin_name.'-admin', plugin_dir_url( __FILE__ ) . 'css/enp_quiz-admin.css', array(), $this->version);

		wp_enqueue_style(  $this->plugin_name.'-admin' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name.'-admin', plugin_dir_url( __FILE__ ) . 'js/enp_quiz-admin.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( $this->plugin_name.'-admin' );

	}

}
