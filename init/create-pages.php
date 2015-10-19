<?
/*
*   Create all necessary pages and page templates
*
*
*/
$configure_quiz = array(
  'post_type'     => 'page',
  'post_title'    => 'Configure Quiz',
  'post_content'  => '[configure_quiz]',
  'post_status'   => 'publish'
);

$quiz_report = array(
  'post_type'     => 'page',
  'post_title'    => 'Quiz Report',
  'post_content'  => '[quiz_report]',
  'post_status'   => 'publish'
);

$quiz_answer = array(
  'page_template' => 'self-service-quiz/page-quiz-answer.php',
  'post_type'     => 'page',
  'post_title'    => 'Quiz Answer',
  'post_content'  => '',
  'post_status'   => 'publish'
);

$iframe_quiz = array(
  'page_template' => 'self-service-quiz/page-iframe-quiz.php',
  'post_type'     => 'page',
  'post_title'    => 'iframe quiz',
  'post_content'  => '',
  'post_status'   => 'publish'
);

$create_a_quiz = array(
  'post_type'     => 'page',
  'post_title'    => 'Create a Quizz',
  'post_content'  => '[create-a-quiz]',
  'post_status'   => 'publish'
);

$view_quiz = array(
  'post_type'     => 'page',
  'post_title'    => 'View Quiz',
  'post_content'  => '[view_quiz]',
  'post_status'   => 'publish'
);

if( !get_page_by_title('Configure Quiz') ) {
  wp_insert_post( $configure_quiz );
  wp_insert_post( $quiz_report );
  wp_insert_post( $quiz_answer );
  wp_insert_post( $iframe_quiz );
  wp_insert_post( $create_a_quiz );
  wp_insert_post( $view_quiz );
}

?>
