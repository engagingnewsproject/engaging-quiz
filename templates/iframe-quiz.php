<?php
/*
Template Name: iframe Quiz
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
  <title>ENP iframe Poll</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() . '/self-service-quiz/css/iframe.css'; ?>" type="text/css" media="screen" >

  <script>
  $(function(){
    $('#quiz-display-form').on('submit', function(e){
      $('.btn-primary').attr("disabled", "disabled");
    });

    $('.input-group').on('click', function(e){
      $('.btn-primary').removeAttr("disabled");
    });
  });
  </script>

  <?php wp_head(); ?>
</head>
<body style="overflow: auto;">
  <div class="quiz-iframe">
    <?php enp_quiz_get_template_part( 'display', 'quiz-display');?>
  </div> <!-- end #quiz-iframe -->

	<?php wp_footer(); ?>

	</body>
</html>
