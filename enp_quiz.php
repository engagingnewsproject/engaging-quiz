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
 * Version:           0.0.2
 * Author:            Engaging News Project
 * Author URI:        http://engagingnewsproject.org
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       enp_quiz
 * Domain Path:       /languages
 */

// Cross-Origin Resource Sharing (CORS) headers to 
// explicitly allow your domain to be embedded in iframes. 
// function enp_quiz_csp_header() {
//     header('Content-Security-Policy: frame-ancestors "self" mediaengagement.org');
// }
// add_action('init', 'enp_quiz_csp_header');

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define a Plugin Root File constant
if(!defined('ENP_QUIZ_ROOT')) {
    define( 'ENP_QUIZ_ROOT', plugin_dir_path( __FILE__ ) );
}
if(!defined('ENP_QUIZ_ROOT_URL')) {
    define( 'ENP_QUIZ_ROOT_URL', plugins_url('enp-quiz') );
}

// Define Version
if(!defined('ENP_QUIZ_VERSION')) {
    // also defined in public/class-enp_quiz-take.php for the Quiz Take side of things
    define('ENP_QUIZ_VERSION', '1.1.1');
    // add_option to WP options table so we can track it
    // don't update it, because that'll be handled by the upgrade code
    add_option('enp_quiz_version', ENP_QUIZ_VERSION);
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-enp_quiz-activator.php
 */
function activate_enp_quiz() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-activator.php';
    $activate = new Enp_quiz_Activator();
    $activate->run_activation();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-enp_quiz-deactivator.php
 */
function deactivate_enp_quiz() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-deactivator.php';
    new Enp_quiz_Deactivator();
}

/**
 * The code that runs on init to add in any necessary rewrite rules
 */
function add_enp_quiz_rewrite_rules($hard = false) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-activator.php';
    $activate = new Enp_quiz_Activator();
    $activate->add_rewrite_rules($hard);
}


/**
* Check version numbers to see if we need to run an upgrade process
*/
function check_for_enp_quiz_upgrade() {
    // check for upgrades
    $stored_version = get_option('enp_quiz_version');
    if($stored_version !== ENP_QUIZ_VERSION) {
        // run upgrade code
        require_once('upgrade.php');
        $upgrade = new Enp_quiz_Upgrade($stored_version);
    }
}

/**
* Wordpress updates override our htaccess additions and permalink updates. 
* This check wordpress version number against the previously stored version number
* to see if we need to add our rules back in.
*/
function enp_quiz_check_for_wordpress_upgrade() {
    global $wp_version;
    // check for upgrades
    $stored_version = get_option('enp_quiz_wordpress_core_version');
    if($stored_version !== $wp_version) {
        // run upgrade code
        $hard = true;
        add_enp_quiz_rewrite_rules($hard);
        // set the new worpdress core version
        update_option('enp_quiz_wordpress_core_version', $wp_version);
    }
}

register_activation_hook( __FILE__, 'activate_enp_quiz' );
register_deactivation_hook( __FILE__, 'deactivate_enp_quiz' );
add_action('init', 'enp_quiz_check_for_wordpress_upgrade');
add_action('init', 'check_for_enp_quiz_upgrade');
add_action('init', 'add_enp_quiz_rewrite_rules');

/**
 * The core plugin class that is used to choose which
 * classes to run
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-slugify.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-quiz.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-question.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-mc_option.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-slider.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-slider-result.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-slider-ab_result.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-user.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-nonce.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-cookies.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-search_quizzes.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-paginate.php';
require_once plugin_dir_path( __FILE__ ) . 'public/quiz-take/includes/class-enp_quiz-cookies_quiz_take.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-ab_test.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-quiz_ab_test_result.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-question_ab_test_result.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_quiz-mc_option_ab_test_result.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_embed-domain.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_embed-site.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_embed-site-type.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_embed-site-bridge.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-enp_embed-quiz.php';

// Database
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_db.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_quiz.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_quiz_option.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_question.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_mc_option.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_slider.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_quiz_response.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_ab_test.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_embed_quiz.php';
require plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_embed_site.php';


// Database for Quiz Take side (only need it to reset data)
require_once plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_quiz_take.php';
require_once plugin_dir_path( __FILE__ ) . 'database/class-enp_quiz_save_quiz_take_quiz_data.php';

// API
require plugin_dir_path( __FILE__ ) . 'api/routes.php';

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
*/
add_action('activated_plugin','enp_log_error');
function enp_log_error(){
    file_put_contents(plugin_dir_path( __FILE__ ).'/error.txt', ob_get_contents());
}

run_enp_quiz();
