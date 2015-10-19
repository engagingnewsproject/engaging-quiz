<?php
/*
Template Name: Quiz Answer
*/
?>

<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() . '/self-service-quiz/css/iframe.css'; ?>" type="text/css" media="screen" />
  <?php do_action('et_head_meta'); ?>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

  <?php
  $quiz = $wpdb->get_row(
    $wpdb->prepare(
      "SELECT * FROM enp_quiz
      WHERE guid = '%s'",
      $_GET["guid"]
    )
  );

  $parentQuiz = $wpdb->get_var("
    SELECT parent_guid FROM enp_quiz_next
    WHERE curr_quiz_id = '" . $quiz->ID . "' ");

  $quizQuestions = $wpdb->get_results( "
    SELECT curr_quiz_id FROM enp_quiz_next
    WHERE parent_guid = '" . $parentQuiz ."' ", OBJECT );

  $nextQuiz = $wpdb->get_var("
    SELECT next_quiz_id FROM enp_quiz_next
    WHERE curr_quiz_id = '" . $quiz->ID . "' ");

  $nextGuid = $wpdb->get_row("
    SELECT * FROM enp_quiz
    WHERE id = '" . $nextQuiz . "' ");

  if($parentQuiz) {

	  $parentID = $wpdb->get_var("
      SELECT id FROM enp_quiz
      WHERE guid = '" . $parentQuiz . "' ");
  }

  // set our style ID to get display options from
  if ($parentID > 0) {
    $quiz_style_ID = $parentID;
  } else {
    $quiz_style_ID = $quiz->ID;
  }
  ?>
<div class="quiz-iframe">
<div style="<? echo (!empty($quiz_style_ID) ? get_quiz_styles($quiz_style_ID) : ''); ?>" class="bootstrap quiz-answer">
    <?php

    $quiz_response = get_quiz_response( $_GET["response_id"] );

    $exact_value = false;

    //$display_answer = $quiz_response->correct_option_value;

    if ( $quiz->quiz_type == "multiple-choice" ) {
	    $wpdb->query('SET OPTION SQL_BIG_SELECTS = 1');
      $question_options = $wpdb->get_row("
        SELECT correct.value 'correct_answer_message', incorrect.value 'incorrect_answer_message'
        FROM enp_quiz_options po
        LEFT OUTER JOIN enp_quiz_options correct ON correct.field = 'correct_answer_message' AND po.quiz_id = correct.quiz_id
        LEFT OUTER JOIN enp_quiz_options incorrect ON incorrect.field = 'incorrect_answer_message' AND po.quiz_id = incorrect.quiz_id
        WHERE po.quiz_id = " . $quiz->ID . "
        GROUP BY po.quiz_id;");
    } else if ( $quiz->quiz_type == "slider" ) {

      $wpdb->query('SET OPTION SQL_BIG_SELECTS = 1');
      $question_options = $wpdb->get_row("
        SELECT po_high_answer.value 'slider_high_answer', po_low_answer.value 'slider_low_answer', po_correct_answer.value 'slider_correct_answer', po_correct_message.value 'correct_answer_message', po_incorrect_message.value 'incorrect_answer_message', po_label.value 'slider_label'
        FROM enp_quiz_options po
        LEFT OUTER JOIN enp_quiz_options po_high_answer ON po_high_answer.field = 'slider_high_answer' AND po.quiz_id = po_high_answer.quiz_id
        LEFT OUTER JOIN enp_quiz_options po_low_answer ON po_low_answer.field = 'slider_low_answer' AND po.quiz_id = po_low_answer.quiz_id
        LEFT OUTER JOIN enp_quiz_options po_correct_answer ON po_correct_answer.field = 'slider_correct_answer' AND po.quiz_id = po_correct_answer.quiz_id
        LEFT OUTER JOIN enp_quiz_options po_label ON po_label.field = 'slider_label' AND po.quiz_id = po_label.quiz_id
        LEFT OUTER JOIN enp_quiz_options po_correct_message ON po_correct_message.field = 'correct_answer_message' AND po.quiz_id = po_correct_message.quiz_id
        LEFT OUTER JOIN enp_quiz_options po_incorrect_message ON po_incorrect_message.field = 'incorrect_answer_message' AND po.quiz_id = po_incorrect_message.quiz_id
        WHERE po.quiz_id = " . $quiz->ID . "
        GROUP BY po.quiz_id;");

    } else {
      $exact_value = true;
    }
    ?>
    <div class="col-sm-12">
        <?php

        $question_text = $quiz->question;
        $answer_message = render_answer_response_message( $quiz->quiz_type, $quiz_response, $question_options );

        include(locate_template('self-service-quiz/quiz-answer.php'));

        $parentQuiz = ($parentQuiz) ? $parentQuiz : $_GET["guid"];
        $guidLink = ($nextGuid) ? $nextGuid->guid : $parentQuiz;
        ?>

	    <?php
	        if ($nextGuid) {
	    ?>
		        <p><a href="<?php echo get_site_url() . '/iframe-quiz/?guid=' . $nextGuid->guid; echo (isset($_GET["quiz_preview"]) && ('' != $_GET["quiz_preview"]))? '&quiz_preview=true' : '';?>" class="btn btn-sm btn-primary">Next Question</a></p>
        <?php } else { ?>
		        <p>Thanks for taking our quiz!<br><a href="<?php echo get_site_url() . '/iframe-quiz/?guid=' . $guidLink; echo (isset($_GET["quiz_preview"]) && ('' != $_GET["quiz_preview"]))? '&quiz_preview=true' : '';?>" class="btn btn-sm btn-primary">Return to the beginning</a> <a href="<?php echo get_site_url() . '/iframe-quiz/?summary=' . $guidLink; echo (isset($_GET["quiz_preview"]) && ('' != $_GET["quiz_preview"]))? '&quiz_preview=true' : '';?>" class="btn btn-sm btn-primary">View Summary</a></p>
		        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5420b26c5d05a323"></script>
		        <!-- Go to www.addthis.com/dashboard to customize your tools -->
		        <script>
			        document.write('<div class="addthis_sharing_toolbox" data-url="'+ localStorage.getItem('refer') +'" data-title="Try this quiz from Engaging News Project!" style="margin-top:5px;"></div>');
		        </script>

	        <?php } ?>
    </div>
      <div class="form-group iframe-credits">
        <div class="col-sm-12">
          <p>Built by the <a href="<?php echo get_site_url() ?>" target="_blank">Engaging News Project</a></p>
        </div>
      </div>
</div>
</div> <!-- end #main_content -->
		<?php wp_footer(); ?>
	</body>

</html>
