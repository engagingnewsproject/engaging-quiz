<?

/*
*
*   Redirect users to the iframe template when url matches /iframe-quiz/
*
*/
function prefix_url_rewrite_templates() {
    $url = $_SERVER['REQUEST_URI'];
    $find   = '/iframe-quiz/';
    $pos = strpos($url, $find);

    if ( $pos ) {
        add_filter( 'template_include', function() {
            enp_quiz_get_template_part('iframe', 'quiz');
        });
    }
}

add_action( 'template_redirect', 'prefix_url_rewrite_templates' );



/**
 * Template loader for ENP Quiz.
 *
 * @author  Gary Jones (mostly)
 * @author  Edited by Jerry Jones
 */

if( ! class_exists( 'Gamajo_Template_Loader' ) ) {
    require get_root_plugin_path() . 'init/template-loader.php';
}

class ENP_Quiz_Template_Loader extends Gamajo_Template_Loader {

    /**
     * Prefix for filter names.
     *
     * @since 1.0.0
     * @type string
     */
    protected $filter_prefix = 'enp_quiz_templates';

    protected $plugin_template_directory = 'templates';

    /**
     * Directory name where custom templates for this plugin should be found in the theme.
     *
     * @since 1.0.0
     * @type string
     */
    protected $theme_template_directory = 'enp-quiz';

    /**
     * Reference to the root directory path of this plugin.
     *
     * Can either be a defined constant, or a relative reference from where the subclass lives.
     *
     * In this case, `MEAL_PLANNER_PLUGIN_DIR` would be defined in the root plugin file as:
     *
     * ~~~
     * define( 'ENP_QUIZ_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
     * ~~~
     *
     * @since 1.0.0
     * @type string
     */
    protected $plugin_directory = ENP_QUIZ_PLUGIN_PATH;

}

// Template loader instantiated elsewhere, such as the main plugin file
$enp_quiz_template_loader = new ENP_Quiz_Template_Loader;


/*
*   get template function
*   enp_quiz_get_template_part( 'iframe', 'quiz' );
*   // Searches theme enp-quiz folder for a match with iframe-quiz.php
*   // If none found, searches plugin templates folder for iframe-quiz.php
*/
function enp_quiz_get_template_part( $slug, $name = null, $load = true ) {
    global $enp_quiz_template_loader;
    $enp_quiz_template_loader->get_template_part( $slug, $name, $load );
}

?>
