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
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" >
  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() . '/self-service-quiz/css/iframe.css'; ?>" type="text/css" media="screen" >
  <?php do_action('et_head_meta'); ?>
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
<body <?php body_class(); ?> style="overflow: auto;">
  <div class="quiz-iframe">
    <?php get_template_part('self-service-quiz/quiz-display', 'page'); ?>
  </div> <!-- end #quiz-iframe -->

  <?php //get_footer(); ?>
	<?php wp_footer(); ?>

	</body>
</html>
