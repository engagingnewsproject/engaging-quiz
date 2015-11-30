<?php
// include("include/quiz-shortcodes.php");

// THIS IS NOT A GOOD SOLUTION, but we get an error message otherwise
// Fatal error: Call to undefined function wp_get_current_user() in /wp-includes/capabilities.php on line 1441
require_once(ABSPATH . 'wp-includes/pluggable.php');


function iframe_quiz_hide_admin_bar () {
  global $post;
  if( is_page('iframe-quiz') ) {
    show_admin_bar( false );
  }
}
add_action( 'wp', 'iframe_quiz_hide_admin_bar' );

//add_action('get_template_part_self-service-quiz/quiz-form','enqueue_admin_self_service_quiz_scripts');




// if using a custom function, you need this
//global $wpdb

/* enter the full name you want displayed alongside the email address */
/* from http://miloguide.com/filter-hooks/wp_mail_from_name/ */
function enp_filter_wp_mail_from_name($from_name){
    return "Engaging News Project";
}
add_filter("wp_mail_from_name", "enp_filter_wp_mail_from_name");

// insert custom arrangment of the post-add-edit form boxes
// for every single user upon registered
function set_user_metaboxes($user_id) {

    // order
    // $meta_key = 'meta-box-order_post';
    // $meta_value = array(
    //     'side' => 'submitdiv,formatdiv,categorydiv,postimagediv',
    //     'normal' => 'postexcerpt,trackbacksdiv,tagsdiv-post_tag,postcustom,commentstatusdiv,commentsdiv,slugdiv,authordiv,revisionsdiv',
    //     'advanced' => '',
    // );
    // update_user_meta( $user_id, $meta_key, $meta_value );

    // hiddens
    $meta_key = 'metaboxhidden_quiz';
    $meta_value = array('wpseo_meta', 'sharing_meta');
    update_user_meta( $user_id, $meta_key, $meta_value );

}
add_action('user_register', 'set_user_metaboxes');

function posts_for_current_author($query) {
	global $user_level;

    // Editor roles equates to levels 5 through 7, so anything lower then 5 is lower then an editor role...
    //http://codex.wordpress.org/Roles_and_Capabilities#User_Level_to_Role_Conversion
	if($query->is_admin && $user_level < 5) {
		global $user_ID;
		$query->set('author',  $user_ID);
		unset($user_ID);
	}
	unset($user_level);

	return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

//http://wordpress.stackexchange.com/questions/3578/change-the-text-on-the-publish-button
add_action( 'admin_print_footer_scripts', 'remove_save_button' );
function remove_save_button()
{
?>
<script>
if ( jQuery('body').hasClass('post-type-quiz') ) {
  jQuery(document).ready(function($){$('#publish').val("Create Quiz");});
}
</script><?php
}

function my_columns_filter( $columns ) {
   unset($columns['wpseo-score']);
   unset($columns['wpseo-title']);
   unset($columns['wpseo-metadesc']);
   unset($columns['wpseo-focuskw']);
   return $columns;
}

// Custom Post Type
add_filter( 'manage_edit-quiz_columns', 'my_columns_filter',10, 1 );

function redirect_to_front_page() {
global $redirect_to;
  // if (!isset($_GET['redirect_to'])) {
  //   $redirect_to = get_option('siteurl');
  // }

  $redirect_to = get_permalink( get_page_by_path( 'create-a-quiz' ) );
}
add_action('login_form', 'redirect_to_front_page');

// Only admins see admin bar
if ( ! current_user_can( 'manage_options' ) ) {
    show_admin_bar( false );
}

// Add text to the registration page
// https://codex.wordpress.org/Customizing_the_Registration_Form
add_action('register_form','myplugin_register_form');


function myplugin_register_form (){
    $first_name = ( isset( $_POST['first_name'] ) ) ? $_POST['first_name']: '';
    $terms_conditions_url = 'http://' . $_SERVER['SERVER_NAME'] . '/terms-and-conditions';
    ?>
    <p>Please note that this software is a free service and should be taken as it comes.  Thanks!</p>
    <br>
    <input type="checkbox" name="login_accept" id="login_accept" />I agree to the <a href="<?php echo $terms_conditions_url; ?>" target="_blank">terms and conditions</a>.
    <br><br>
    <?php
}

function myplugin_check_fields($errors, $sanitized_user_login, $user_email) {

  // See if the checkbox #login_accept was checked
  if ( isset( $_REQUEST['login_accept'] ) && $_REQUEST['login_accept'] == 'on' ) {
      // Checkbox on, allow login
      // return $user;
  } else {
      // Did NOT check the box, do not allow login
      $errors->add( 'login_accept', __('<strong>ERROR</strong>: Terms and conditions must be accepted to proceed.', get_site_url()) );
  }

    return $errors;

}

add_filter('registration_errors', 'myplugin_check_fields', 10, 3);

// JS hack to require Terms and Conditions acceptance on OA Social login usage

function enp_require_tac_script () {
  ?>
  <script>

  jQuery('.oneall_social_login').on('click', function() {
    // if checkbox is selected
    if( jQuery('#login_accept').is(':checked') ) {
      jQuery(this).addClass('active');
      jQuery('#login_error').hide();
    } else {
    // else
      jQuery(this).removeClass('active');
      var log_err = jQuery('#login_error');
      if( log_err.length !== 0 ) {
        log_err.show();
      } else {
        var html = '<div id="login_error"><strong>ERROR</strong>: Terms and conditions must be accepted to proceed.</div>';
        jQuery(html).insertAfter('p.message.register');
      }
    }

  });

  jQuery('#login_accept').on('click', function() {
    jQuery('.oneall_social_login').trigger('click');
  });
  //console.log('enp_require_tac_script!');

  </script>
  <?php
}

add_action('register_form', 'enp_require_tac_script');


//Custom Theme Settings
add_action('admin_menu', 'add_gcf_interface');

function add_gcf_interface() {
	add_options_page('Global Custom Fields', 'Global Custom Fields', 'edit_pages', 'functions', 'editglobalcustomfields');
}

// Create settings fields.
add_action( 'admin_init', 'gcf_data' );
function gcf_data() {
    register_setting( 'gcf_data_settings', 'mc_correct_answer_message' );
    register_setting( 'gcf_data_settings', 'mc_incorrect_answer_message' );
    register_setting( 'gcf_data_settings', 'slider_correct_answer_message' );
    register_setting( 'gcf_data_settings', 'slider_incorrect_answer_message' );
    register_setting( 'gcf_data_settings', 'slider_range_correct_answer_message' );
    register_setting( 'gcf_data_settings', 'slider_range_incorrect_answer_message' );
}

function editglobalcustomfields() {
	?>
	<div class='wrap'>
	<h2>Global Custom Fields</h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>

	<p><strong>Multiple Choice Correct Answer Message</strong><br />
	<textarea class="form-control" rows="4" cols="50" name="mc_correct_answer_message" id="mc-correct-answer-message" placeholder="Enter Correct Answer Message"><?php echo get_option('mc_correct_answer_message') ? get_option('mc_correct_answer_message') : "Your answer of [user_answer] is correct!"; ?></textarea></p>

	<p><strong>Multiple Choice Incorrect Answer Message</strong><br />
	<textarea class="form-control" rows="4" cols="50" name="mc_incorrect_answer_message" id="mc-correct-answer-message" placeholder="Enter Correct Answer Message"><?php echo get_option('mc_incorrect_answer_message') ? get_option('mc_incorrect_answer_message') : "Your answer is [user_answer], but the correct answer is [correct_value]."; ?></textarea></p>

	<p><strong>Slider Correct Answer Message</strong><br />
	<textarea class="form-control" rows="4" cols="50" name="slider_correct_answer_message" id="slider-correct-answer-message" placeholder="Enter Correct Answer Message"><?php echo get_option('slider_correct_answer_message') ? get_option('slider_correct_answer_message') : "Your answer of [user_answer] is correct!"; ?></textarea></p>

	<p><strong>Slider Incorrect Answer Message</strong><br />
	<textarea class="form-control" rows="4" cols="50" name="slider_incorrect_answer_message" id="slider-correct-answer-message" placeholder="Enter Correct Answer Message"><?php echo get_option('slider_incorrect_answer_message') ? get_option('slider_incorrect_answer_message') : "Your answer is [user_answer], but the correct answer is [correct_value]."; ?></textarea></p>

	<p><strong>Slider Range Correct Answer Message</strong><br />
	<textarea class="form-control" rows="4" cols="50" name="slider_range_correct_answer_message" id="slider-range-correct-answer-message" placeholder="Enter Correct Answer Message"><?php echo get_option('slider_range_correct_answer_message') ? get_option('slider_range_correct_answer_message') : "Your answer of [user_answer] is correct!"; ?></textarea></p>

	<p><strong>Slider Range Incorrect Answer Message</strong><br />
	<textarea class="form-control" rows="4" cols="50" name="slider_range_incorrect_answer_message" id="slider-range-correct-answer-message" placeholder="Enter Correct Answer Message"><?php echo get_option('slider_range_incorrect_answer_message') ? get_option('slider_range_incorrect_answer_message') : "Your answer is [user_answer], but the correct answer is [correct_value]."; ?></textarea></p>

    <?php settings_fields( 'gcf_data_settings' ); ?>
    <?php do_settings_sections( 'gcf_data_settings' ); ?>

    <?php submit_button(); ?>
    <? /*
    	<p><input type="submit" name="Submit" value="Update Options" /></p>

    	<input type="hidden" name="action" value="update" />
    	<input type="hidden" name="page_options" value="mc_correct_answer_message,mc_incorrect_answer_message,slider_correct_answer_message,slider_incorrect_answer_message,slider_range_correct_answer_message,slider_range_incorrect_answer_message" />
    */?>

	</form>
	</div>
	<?php
}

function oa_social_login_html() {
  ob_start();
  do_action('oa_social_login');
  $social_login_html = ob_get_contents();
  ob_end_clean();
  return $social_login_html;
}

function display_login_form_shortcode() {
	if ( is_user_logged_in() )
		return '';

  //$social_login_html = oa_social_login_html();

  $login_html  =
  '<div class="enp-login bootstrap">
    <h2 class="widget_title">Log In</h2>
    <p><b>Please Login or <a href="' . get_site_url() . '/wp-login.php?action=register">Register</a> to Create your Quiz!</b></p>
    <div class="members-login-form">
  	  <form name="loginform" id="loginform" action="' . get_site_url() . '/wp-login.php" method="post">

  			<p class="login-username">
  				<label for="user_login">Username</label>
  				<input type="text" name="log" id="user_login" class="form-control" value="">
  			</p>
  			<p class="login-password">
  				<label for="user_pass">Password</label>
  				<input type="password" name="pwd" id="user_pass" class="form-control" value="">
  			</p>

  			<p class="login-remember"><label><input name="rememberme" type="checkbox" id="wp-submit" value="forever"> Remember Me</label></p>
  			<p class="login-submit">
  				<input type="submit" name="wp-submit" id="1" class="btn btn-primary form-control" value="Login Now">
  				<input type="hidden" name="redirect_to" value="' . get_site_url() . '/create-a-quiz/">
  			</p>

  		  </form>
      </div>
      <div class="social-login-custom">' . oa_social_login_html() . '</div>
    </div>';

	return  $login_html;
}

function hioweb_add_shortcodes() {
	add_shortcode( 'display-login-form', 'display_login_form_shortcode' );
}

add_action( 'init', 'hioweb_add_shortcodes' );

add_filter( 'wp_mail_from_name', 'custom_wp_mail_from_name' );
function custom_wp_mail_from_name( $original_email_from )
{
	return 'Engaging News Project';
}

add_filter( 'wp_mail_from', 'custom_wp_mail_from' );
function custom_wp_mail_from( $original_email_address )
{
	//Make sure the email is from the same domain
	//as your website to avoid being marked as spam.
  // return 'donotreply@engagingnewsproject.org';
	return 'donotreply@engagingnewsproject.org';

}

function get_user_ip () {
  $ip = $_SERVER['REMOTE_ADDR'];
  if(filter_var($ip, FILTER_VALIDATE_IP))
  {
    return $ip;
  }
  return false;
}

function add_capability() {
    // gets the author role
    $role = get_role( 'administrator' );

    // This only works, because it accesses the class instance.
    $role->add_cap( 'read_all_quizzes' );

    //echo '<h1>ADDING CAP</h1>';
}
add_action( 'admin_init', 'add_capability');

function get_quiz_response ( $response_id ) {

  global $wpdb;

  $quiz_response = $wpdb->get_row(
    $wpdb->prepare(
      "SELECT * FROM enp_quiz_responses WHERE ID = %d",
      $response_id
    )
  );

  if( strpos($quiz_response->correct_option_value,' to ') !== false ) {
    $answer_array = explode(' to ', $quiz_response->correct_option_value);
    $quiz_response->correct_value = $answer_array[0];
  } else {
    $quiz_response->correct_value = $quiz_response->correct_option_value;
  }

  return $quiz_response;

}

function render_answer_response_message ( $quiz_type, $q_response, $q_options ) {

  /* $vars = array(
    '[user_answer]',
    '[correct_value]',
    '[slider_label]',
    '[lower_range]',
    '[upper_range]',
  ); */

  // determine which message template
  if( $q_response->is_correct )
    $msg = $q_options->correct_answer_message;
  else
    $msg = $q_options->incorrect_answer_message;

  // replace message variables

  $user_answer = render_label( $q_response->quiz_option_value, $q_options->slider_label );
  $correct_value = render_label( $q_response->correct_value, $q_options->slider_label );

  $msg = str_replace(
    '[user_answer]',
    $user_answer,
    $msg
  );

  $msg = str_replace(
    '[correct_value]',
    $correct_value,
    $msg
  );

  // '[slider_label]' template variable is deprecated, so remove vestiges
  $msg = remove_label_variable( $msg );

  // slider ranges
  $msg = str_replace('[lower_range]', $q_options->slider_low_answer, $msg);
  $msg = str_replace('[upper_range]', $q_options->slider_high_answer, $msg);

  return $msg;

}

function render_label ( $value = '', $label = '' ) {
  if( strpos($label,'{%V%}') !== false )
    return str_replace('{%V%}', $value, $label);
  if( strpos($label,'%') !== false )
    return $value . $label;
  if( !empty($label) )
    return $value . ' ' . $label;
  return $value;
}

// [slider_label] template variable is deprecated. This function removes the label variable
function remove_label_variable ( $msg = '' ) {
  $msg = str_replace(' [slider_label]', '', $msg);
  $msg = str_replace('[slider_label]', '', $msg);
  return $msg;
}

function get_quiz_option ( $quiz_id, $option ) {
  global $wpdb;
  return $wpdb->get_var(
    $wpdb->prepare("
        SELECT value FROM enp_quiz_options
        WHERE field = %s AND quiz_id = %d LIMIT 1", $option, $quiz_id
    )
  );
}

add_action('init', 'allow_subscriber_uploads');
function allow_subscriber_uploads() {
    $subscriber = get_role('subscriber');
    $subscriber->add_cap('upload_files');
}

//add_action('init', 'no_mo_dashboard');
function no_mo_dashboard() {
  if (!current_user_can('manage_options') && $_SERVER['DOING_AJAX'] != '/wp-admin/admin-ajax.php') {
  wp_redirect(home_url()); exit;
  }
}

// get custom quiz styles
function get_quiz_styles($quiz_style_ID) {
  global $wpdb;

  $quiz_background_color = $wpdb->get_var( "
      SELECT value FROM enp_quiz_options
      WHERE field = 'quiz_background_color' AND quiz_id = " . $quiz_style_ID );

  $quiz_text_color = $wpdb->get_var( "
    SELECT value FROM enp_quiz_options
    WHERE field = 'quiz_text_color' AND quiz_id = " . $quiz_style_ID );

  $quiz_display_width = $wpdb->get_var( "
    SELECT value FROM enp_quiz_options
    WHERE field = 'quiz_display_width' AND quiz_id = " . $quiz_style_ID );

  $quiz_display_height = $wpdb->get_var("
    SELECT value FROM enp_quiz_options
    WHERE field = 'quiz_display_height' AND quiz_id = " . $quiz_style_ID);

  $quiz_display_padding = $wpdb->get_var( "
    SELECT value FROM enp_quiz_options
    WHERE field = 'quiz_display_padding' AND quiz_id = " . $quiz_style_ID );

  $quiz_display_css = $wpdb->get_var("
    SELECT value FROM enp_quiz_options
    WHERE field = 'quiz_display_css' AND quiz_id = " . $quiz_style_ID);

  // Compile quiz styles
  $quiz_styles = 'box-sizing: border-box;
                  background: '.$quiz_background_color.';
                  color: '.$quiz_text_color.';
                  width: '.$quiz_display_width.';
                  height: '.$quiz_display_height.';
                  padding: 10px 0;';
  // append custom styles
  $quiz_styles .= (!empty($quiz_display_css) ? $quiz_display_css : '');

  return $quiz_styles;
}


// Remove admin bar for logged in users if its a quiz iframe template
function remove_iframe_admin_bar(){
  if(is_user_logged_in()) {
    // check if we're displaying an iframe template
    if(is_page_template( 'self-service-quiz/page-quiz-answer.php' ) || is_page_template( 'self-service-quiz/page-iframe-quiz.php' )) {
      return false;
    } else {
      // logged in and no iframe template, so show the admin bar
      return true;
    }
  }
}
add_filter( 'show_admin_bar' , 'remove_iframe_admin_bar');
