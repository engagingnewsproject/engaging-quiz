<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://engagingnewsproject.org
 * @since             0.0.1
 * @package           Enp_quiz
 *
 * @wordpress-plugin
 * Plugin Name:       Engaging Quiz Creator
 * Plugin URI:        http://engagingnewsproject.org/quiz-tool
 * Description:       Create quizzes for embedding on websites
 * Version:           0.0.1
 * Author:            Engaging News Project
 * Author URI:        http://engagingnewsproject.org
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       enp_quiz
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define a Plugin Root File constant
define( 'ENP_QUIZ_ROOT', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-enp_quiz-activator.php
 */
function activate_enp_quiz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-activator.php';

	new Enp_quiz_Activator();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-enp_quiz-deactivator.php
 */
function deactivate_enp_quiz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-deactivator.php';
	new Enp_quiz_Deactivator();
}

register_activation_hook( __FILE__, 'activate_enp_quiz' );
register_deactivation_hook( __FILE__, 'deactivate_enp_quiz' );

/**
 * The core plugin class that is used to choose which
 * classes to run
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz.php';

/**
 * Begins execution of the plugin.
 *
 * @since    0.0.1
 */
function run_enp_quiz() {
	$plugin = new Enp_quiz();
}

/* For DEBUGGING
*  creates log file with error output. Good for using on
* The plugin generated xxxx characters of unexpected output messages

add_action('activated_plugin','enp_log_error');
function enp_log_error(){
	file_put_contents(plugin_dir_path( __FILE__ ).'/error.txt', ob_get_contents());
}*/

run_enp_quiz();
