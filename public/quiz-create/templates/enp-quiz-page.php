<?php
/**
 * The template for displaying Quiz Create Pages
 *
 * This is a wrapper page for all Quiz Create Pages
 * If you want to override this, copy the entire
 * /enp-quiz/public/quiz-create/templates directory into your own theme
 * and edit the ENP_QUIZ_CREATE_TEMPLATES_PATH path of
 * the wp-content/enp-quiz-config.php file to match your template directory.
 *
 * @package    Enp_quiz
 * @subpackage Enp_quiz/public/quiz-create/templates
 * @since      v0.0.1
 */

get_header();
// get all of our SVG files
include( ENP_QUIZ_ROOT.'/public/quiz-create/svg/symbol-defs.svg');?>

<main id="enp-quiz" class="enp-quiz__main" role="main">
<?php
// this will include our template files
    // Get the current post
    $current_post = get_post();
    
    // Check if we have a post
    if ($current_post) {
        // Set up post data for template tags
        setup_postdata($current_post);

        // Output the content
        the_content();

        // Reset post data
        wp_reset_postdata();
    }
?>
</main>

<?php // This is usually better without the sidebar
get_footer(); ?>
