<?php
// STARTUP
// display errors
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header('Content-type: text/html; charset=utf-8');


// Check if we've loaded the config file yet. If we haven't guess to load it
if (!defined('ENP_QUIZ_URL')) {
	// set enp-quiz-config file path (eehhhh... could be better to not use relative path stuff)
	require_once '../../../../../enp-quiz-config.php';
}

require_once ENP_QUIZ_PLUGIN_DIR . 'public/quiz-take/class-enp_quiz-take.php';

// create the new object if it hasn't already been created
if (isset($qt) && is_object($qt)) {
	// do nothing, we already got it!
} else {
	// we need the $qt instance, so let's load it up
	// load up quiz_take class (requires all the files)
	$qt = new Enp_quiz_Take();
}
// get the quiz ID we need
if (isset($quiz_id)) {
	// do nothing, already set
} else {
	// set our quiz id
	$quiz_id = $qt->get_init_quiz_id();
}

//check to make sure one was found
if ($quiz_id === false) {
	echo 'No quiz requested';
	exit;
}

// load the quiz
$qt->load_quiz($quiz_id);
// get the state
$state = $qt->get_state();
// check the state
if ($state !== 'quiz_end') {
	$qt_question = new Enp_quiz_Take_Question($qt);
}
// create the quiz end object (so we have a template for it for the JS)
$qt_end = new Enp_quiz_Take_Quiz_end($qt->quiz, $qt->get_correctly_answered());
// var_dump($qt->quiz);

?>
<!DOCTYPE html>
<html lang="en-US">

<head>
	<!-- Sentry -->
	<!-- <script
	src="https://js.sentry-cdn.com/9e18cfe96456d768f05663d51ae48577.min.js"
	crossorigin="anonymous"
	></script>
	<script src="https://cdn.lr-intake.com/LogRocket.min.js" crossorigin="anonymous"></script>
<script>window.LogRocket && window.LogRocket.init('kjijq4/cme-quiz');</script>
<script>
	LogRocket.getSessionURL(sessionURL => {
  Sentry.configureScope(scope => {
    scope.setExtra("sessionURL", sessionURL);
  });
});
	</script> -->
	<?php
	// forces IE to load in Standards mode instead of Quirks mode (which messes things up) 
	?>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">

	<title><?php echo $qt->quiz->get_quiz_title(); ?></title>
	<?php
	// load meta
	$qt->meta_tags();
	// load styles
	$qt->styles();
	// IE8 conditional
	?>

	<!--[if lt IE 9]>
	   <link rel="stylesheet" type="text/css" href="<?php echo ENP_QUIZ_PLUGIN_URL; ?>public/quiz-take/css/ie8.css" />
	<![endif]-->

	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php echo ENP_QUIZ_PLUGIN_URL; ?>public/quiz-take/css/ie9.css" />
	<![endif]-->

	<?php
	// echo styles
	echo $qt->load_quiz_styles(); ?>
</head>

<body id="enp-quiz">
	<?php //add in our SVG
	echo $qt->load_svg();
	?>
	<div id="enp-quiz-container" class="enp-quiz__container">
		<header class="enp-quiz__header" role="banner">
			<h3 class="enp-quiz__title <?php echo 'enp-quiz__title--' . $qt->quiz->get_quiz_title_display(); ?>"><?php echo $qt->quiz->get_quiz_title(); ?></h3>
			<div class="enp-quiz__progress">
				<?php echo $qt->get_progress_bar(); ?>
			</div>
		</header>

		<?php
		// check for errors
		echo $qt->get_error_messages(); ?>

		<main class="enp-question__container <?php echo $qt->get_question_container_class(); ?>" role="main" aria-live="polite" aria-relevant="additions text">
			<form id="quiz" class="enp-question__form" method="post" action="<?php echo $qt->get_quiz_form_action(); ?>">
				<?php $qt->nonce->outputKey(); ?>
				<input type="hidden" name="enp-quiz-id" value="<?php echo $qt->quiz->get_quiz_id(); ?>" />
				<input type="hidden" name="enp-user-id" value="<?php echo $qt->get_user_id(); ?>" />
				<input type="hidden" name="enp-response-quiz-id" value="<?php echo $qt->get_response_quiz_id(); ?>" />
				<input id="correctly-answered" type="hidden" name="enp-quiz-correctly-answered" value="<?php echo $qt->get_correctly_answered(); ?>" />
				<?php
				if ($state === 'question' || $state === 'question_explanation') {
					include(ENP_QUIZ_TAKE_TEMPLATES_PATH . '/partials/question.php');
				} elseif ($state === 'quiz_end') {
					include(ENP_QUIZ_TAKE_TEMPLATES_PATH . '/partials/quiz-end.php');
				} ?>

			</form>



		</main>
	</div>
	<?php $current_url = new Enp_quiz_Current_URL(); ?>
	<footer id="enp-quiz-footer" class="enp-callout">
		<a class="enp-callout__link" href="<?php echo $current_url->get_root(); ?>/quiz-creator/?iframe_referral=true&amp;quiz_id=<?php echo $qt_end->quiz->get_quiz_id(); ?>" target="_blank">Powered by the Center for Media Engagement<span class="enp-screen-reader-text"> Link opens in a new window</span></a>
		<p>Quizzes and users are not monitored or endorsed by the Center for Media Engagement.</p>
	</footer>



	<?php
	echo $qt->get_init_json();
	echo $qt_end->get_init_json();
	if (isset($qt_question) && is_object($qt_question)) {
		echo $qt_question->get_init_json();
		echo $qt_question->question_js_templates();
		echo $qt_question->question_explanation_js_template();
		echo $qt_question->mc_option_js_template();
		echo $qt_question->slider_js_template();
	}
	echo $qt_end->quiz_end_template();
	echo $qt->error_message_js_template();

	// load scripts
	$qt->scripts();
	// if we're on prod, include GA Tracking code
	if (
		(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'engagingnewsproject.org') ||
		(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'mediaengagement.org')
	) {
		$ga_id = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'mediaengagement.org' ? 'UA-52471115-4' : 'UA-52471115-1');
	?>
		<script>
			(function(i, s, o, g, r, a, m) {
				i['GoogleAnalyticsObject'] = r;
				i[r] = i[r] || function() {
					(i[r].q = i[r].q || []).push(arguments)
				}, i[r].l = 1 * new Date();
				a = s.createElement(o),
					m = s.getElementsByTagName(o)[0];
				a.async = 1;
				a.src = g;
				m.parentNode.insertBefore(a, m)
			})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');


			ga('create', '<?php echo $ga_id; ?>', 'auto');
			ga('send', 'pageview');
		</script>
	<?php } ?>

</body>

</html>