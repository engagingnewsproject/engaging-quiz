<?
/*
*   Enqueue all styles and scripts
*
*
*/


function enqueue_self_service_quiz_scripts () {
  // this function is defined in enp-quiz.php
  $plugin_root_url = get_root_plugin_url();
  $plugin_root_path = get_root_plugin_path();


  //require_once($plugin_root_path . "include/quiz-shortcodes.php");

  // BUILT WITH LESS, so add bootstrap to a wrapper to apply styles
  wp_enqueue_style( 'main-css', $plugin_root_url . 'css/main.css');
  wp_enqueue_style( 'bootstrap', $plugin_root_url . 'css/bootstrap-prefix.css');
  wp_enqueue_style( 'slider', $plugin_root_url . 'css/slider.css');
  wp_enqueue_style( 'jqplot', $plugin_root_url . 'css/jquery.jqplot.min.css');
  wp_enqueue_script('quiz-custom', $plugin_root_url . 'js/quiz-custom.js', array('jquery'), '1.0', true);
  wp_enqueue_script('bootstrap-js', $plugin_root_url . 'js/vendor/bootstrap.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('validate', $plugin_root_url . 'js/vendor/jquery.validate.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('slider', $plugin_root_url . 'js/vendor/bootstrap-slider.js', array('jquery'), '1.0', true);
  wp_enqueue_script('jqplot', $plugin_root_url . 'js/vendor/jquery.jqplot.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('excanvas', $plugin_root_url . 'js/vendor/excanvas.min.js', array('jquery'), '1.0', true);
  //<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->

  wp_enqueue_script('jqplotpie', $plugin_root_url . 'js/vendor/jqplot.pieRenderer.min.js', array('jquery'), '1.0', true);
  wp_enqueue_script('formhelper-number', $plugin_root_url . 'js/vendor/bootstrap-formhelpers-number.js', array('jquery'), '1.0', true);
  wp_enqueue_script('placeholder', $plugin_root_url . 'js/vendor/jquery.placeholder.js', array('jquery'), '1.0', true);

  wp_enqueue_script( 'jquery-ui-sortable' );
  wp_enqueue_script('jquery-ui-touch-punch' , $plugin_root_url . 'js/vendor/jquery.ui.touch-punch.js', Array('jquery'), '', true);

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


//plugins_url( $path, $plugin );

function plugin_path($content) {
  $path = get_root_plugin_path();
  echo $path;
  echo '<br/>';
  $url = get_root_plugin_url();
  echo $url;
  echo '<br/>';
  $plugin_url = $url.'css/bootstrap-prefix.css';
  echo $plugin_url;
  echo '<br/>';
  $basename = plugin_basename( __FILE__ );
  echo $basename;
  echo '<br/>';

  $file = dirname(__FILE__) . '/enp-quiz.php';
  $plugin_url_dir = plugin_dir_url($file);
  echo $plugin_url_dir;
  echo '<br/>';
  // Output something like: http://example.com/wp-content/plugins/your-plugin/
  $plugin_path_dir = plugin_dir_path($file);
  echo $plugin_path_dir;
  echo '<br/>';

}
add_filter( 'the_content', 'plugin_path' );

?>
