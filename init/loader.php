<?
/*
*   Enqueue all styles and scripts
*
*
*/

define('child_template_directory', get_stylesheet_directory_uri() );

function enqueue_self_service_quiz_scripts () {


  require_once(TEMPLATEPATH."/self-service-quiz/include/quiz-shortcodes.php");

  // BUILT WITH LESS, so add bootstrap to a wrapper to apply styles
  wp_enqueue_style( 'main-css', child_template_directory . '/self-service-quiz/css/main.css');
  wp_enqueue_style( 'bootstrap', child_template_directory . '/self-service-quiz/css/bootstrap-prefix.css');
  wp_enqueue_style( 'slider', child_template_directory . '/self-service-quiz/css/slider.css');
  wp_enqueue_style( 'jqplot', child_template_directory . '/self-service-quiz/css/jquery.jqplot.min.css');
  wp_enqueue_script('quiz-custom', child_template_directory . '/self-service-quiz/js/quiz-custom.js', array('jquery'), '1.0', true);
  wp_enqueue_script('bootstrap-js', child_template_directory . '/self-service-quiz/js/vendor/bootstrap.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('validate', child_template_directory . '/self-service-quiz/js/vendor/jquery.validate.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('slider', child_template_directory . '/self-service-quiz/js/vendor/bootstrap-slider.js', array('jquery'), '1.0', true);
  wp_enqueue_script('jqplot', child_template_directory . '/self-service-quiz/js/vendor/jquery.jqplot.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('excanvas', child_template_directory . '/self-service-quiz/js/vendor/excanvas.min.js', array('jquery'), '1.0', true);
  //<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->

  wp_enqueue_script('jqplotpie', child_template_directory . '/self-service-quiz/js/vendor/jqplot.pieRenderer.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('formhelper-number', child_template_directory . '/self-service-quiz/js/vendor/bootstrap-formhelpers-number.js', array('jquery'), '1.0', true);
  wp_enqueue_script('placeholder', child_template_directory . '/self-service-quiz/js/vendor/jquery.placeholder.js', array('jquery'), '1.0', true);

  wp_enqueue_script( 'jquery-ui-sortable' );
  wp_enqueue_script('jquery-ui-touch-punch' , child_template_directory . '/self-service-quiz/js/vendor/jquery.ui.touch-punch.js', Array('jquery'), '', true);

}
add_action('wp_enqueue_scripts', 'enqueue_self_service_quiz_scripts');

//add_action('wp_print_scripts','include_jquery_form_plugin');
function include_jquery_form_plugin(){
    if (is_page('configure-quiz')){ // only add this on the page that allows the upload
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-form',array('jquery'),false,true );
    }
}
function add_media_upload_scripts() {
    if ( is_admin() ) {
         return;
       }
    wp_enqueue_media();
}
add_action('wp_enqueue_scripts', 'add_media_upload_scripts');

?>
