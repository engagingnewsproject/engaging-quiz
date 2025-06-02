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
function enp_quiz_csp_header() {
    // Get the referer domain
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $referer_domain = parse_url($referer, PHP_URL_HOST);
    
    // Get whitelisted domains from options
    $whitelisted_domains = get_option('enp_quiz_whitelisted_domains', array());
    
    // Always allow your own domain
    $whitelisted_domains[] = parse_url(get_site_url(), PHP_URL_HOST);
    
    // Check if the referer domain is whitelisted
    $is_allowed = false;
    foreach ($whitelisted_domains as $domain) {
        if ($referer_domain !== null && strpos($referer_domain, $domain) !== false) {
            $is_allowed = true;
            break;
        }
    }
    
    if ($is_allowed) {
        // Allow embedding from whitelisted domains
        header('Content-Security-Policy: frame-ancestors ' . implode(' ', array_map(function($domain) {
            return "'self' *." . $domain;
        }, $whitelisted_domains)));
    } else {
        // Block embedding from unauthorized domains
        header('Content-Security-Policy: frame-ancestors \'none\'');
    }
}
add_action('init', 'enp_quiz_csp_header');

// Add admin menu for managing whitelisted domains
function enp_quiz_add_whitelist_menu() {
    add_submenu_page(
        'edit.php?post_type=enp_quiz',
        'Embed Domain Whitelist',
        'Embed Whitelist',
        'manage_options',
        'enp-quiz-whitelist',
        'enp_quiz_whitelist_page'
    );
}
add_action('admin_menu', 'enp_quiz_add_whitelist_menu');

// Whitelist management page
function enp_quiz_whitelist_page() {
    if (isset($_POST['enp_quiz_whitelist_domains'])) {
        $domains = array_map('trim', explode("\n", $_POST['enp_quiz_whitelist_domains']));
        $domains = array_filter($domains); // Remove empty lines
        update_option('enp_quiz_whitelisted_domains', $domains);
        echo '<div class="notice notice-success"><p>Whitelist updated successfully.</p></div>';
    }
    
    $whitelisted_domains = get_option('enp_quiz_whitelisted_domains', array());
    ?>
    <div class="wrap">
        <h1>Quiz Embed Domain Whitelist</h1>
        <form method="post">
            <p>Enter one domain per line. These domains will be allowed to embed your quizzes.</p>
            <textarea name="enp_quiz_whitelist_domains" rows="10" cols="50"><?php echo esc_textarea(implode("\n", $whitelisted_domains)); ?></textarea>
            <p class="submit">
                <input type="submit" class="button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

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

// Track and validate embedding domains
function enp_quiz_track_embed_domain() {
    // Only track on quiz embed pages
    // if (!is_singular('enp_quiz')) {
    //     return;
    // }

    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    if (empty($referer)) {
        return;
    }

    $referer_domain = parse_url($referer, PHP_URL_HOST);
    
    // Skip if it's our own domain
    if ($referer_domain === parse_url(get_site_url(), PHP_URL_HOST)) {
        return;
    }

    // Get or initialize the domains tracking option
    $tracked_domains = get_option('enp_quiz_tracked_domains', array());
    
    // Initialize domain tracking if it doesn't exist
    if (!isset($tracked_domains[$referer_domain])) {
        $tracked_domains[$referer_domain] = array(
            'embed_count' => 0,
            'first_seen' => current_time('mysql'),
            'last_seen' => current_time('mysql'),
            'is_blocked' => false,
            'reports' => 0
        );
    }

    // Update tracking data
    $tracked_domains[$referer_domain]['embed_count']++;
    $tracked_domains[$referer_domain]['last_seen'] = current_time('mysql');

    // Check for suspicious patterns
    $is_suspicious = false;
    
    // Check for high embed count in short time
    if ($tracked_domains[$referer_domain]['embed_count'] > 100) {
        $is_suspicious = true;
    }
    
    // Check for known spam patterns in domain
    $spam_patterns = array(
        '/^[a-z0-9]{8,}\./', // Random-looking subdomains
        '/\.(xyz|top|loan|click|work)$/i', // Known spam TLDs
        '/[0-9]{4,}/', // Lots of numbers
        '/[a-z]{1,2}[0-9]{1,2}[a-z]{1,2}/i' // Alternating letters and numbers
    );
    
    foreach ($spam_patterns as $pattern) {
        if (preg_match($pattern, $referer_domain)) {
            $is_suspicious = true;
            break;
        }
    }

    // If suspicious, increment reports
    if ($is_suspicious) {
        $tracked_domains[$referer_domain]['reports']++;
        
        // Block if too many reports
        if ($tracked_domains[$referer_domain]['reports'] >= 3) {
            $tracked_domains[$referer_domain]['is_blocked'] = true;
        }
    }

    update_option('enp_quiz_tracked_domains', $tracked_domains);

    // Block if domain is marked as blocked
    if ($tracked_domains[$referer_domain]['is_blocked']) {
        header('Content-Security-Policy: frame-ancestors \'none\'');
        die('This domain has been blocked due to suspicious activity.');
    }
}
add_action('wp', 'enp_quiz_track_embed_domain');

// Add admin menu for managing tracked domains
function enp_quiz_add_domain_tracking_menu() {
    add_menu_page(
        'Embed Domain Tracking',
        'Quiz Tracking',
        'manage_options',
        'enp-quiz-tracking',
        'enp_quiz_tracking_page',
        'dashicons-visibility',
        101
    );
}
add_action('admin_menu', 'enp_quiz_add_domain_tracking_menu');

// Domain tracking management page
function enp_quiz_tracking_page() {
    if (isset($_POST['action']) && isset($_POST['domain'])) {
        $tracked_domains = get_option('enp_quiz_tracked_domains', array());
        $domain = sanitize_text_field($_POST['domain']);
        
        if ($_POST['action'] === 'block' && isset($tracked_domains[$domain])) {
            $tracked_domains[$domain]['is_blocked'] = true;
            update_option('enp_quiz_tracked_domains', $tracked_domains);
            echo '<div class="notice notice-success"><p>Domain blocked successfully.</p></div>';
        } elseif ($_POST['action'] === 'unblock' && isset($tracked_domains[$domain])) {
            $tracked_domains[$domain]['is_blocked'] = false;
            $tracked_domains[$domain]['reports'] = 0;
            update_option('enp_quiz_tracked_domains', $tracked_domains);
            echo '<div class="notice notice-success"><p>Domain unblocked successfully.</p></div>';
        }
    }
    
    $tracked_domains = get_option('enp_quiz_tracked_domains', array());
    ?>
    <div class="wrap">
        <h1>Quiz Embed Domain Tracking</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Domain</th>
                    <th>Embed Count</th>
                    <th>First Seen</th>
                    <th>Last Seen</th>
                    <th>Reports</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tracked_domains as $domain => $data): ?>
                <tr>
                    <td><?php echo esc_html($domain); ?></td>
                    <td><?php echo esc_html($data['embed_count']); ?></td>
                    <td><?php echo esc_html($data['first_seen']); ?></td>
                    <td><?php echo esc_html($data['last_seen']); ?></td>
                    <td><?php echo esc_html($data['reports']); ?></td>
                    <td><?php echo $data['is_blocked'] ? 'Blocked' : 'Active'; ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="domain" value="<?php echo esc_attr($domain); ?>">
                            <?php if ($data['is_blocked']): ?>
                                <input type="hidden" name="action" value="unblock">
                                <button type="submit" class="button">Unblock</button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="block">
                                <button type="submit" class="button">Block</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function enp_quiz_add_test_menu() {
    add_menu_page(
        'Test Page',
        'Test Menu',
        'manage_options',
        'enp-quiz-test',
        'enp_quiz_test_page'
    );
}
add_action('admin_menu', 'enp_quiz_add_test_menu');

function enp_quiz_test_page() {
    echo '<div class="wrap"><h1>Test Page</h1></div>';
}

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
