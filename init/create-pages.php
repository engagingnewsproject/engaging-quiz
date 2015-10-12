<?
/*
*   Create all necessary pages and page templates
*
*
*/
$configure_quiz = array(
  // 'page_template' => 'self-service-quiz/page-configure-quiz.php',
  // 'page_template' => 'template-full-width.php',
  'post_type'     => 'page',
  'post_title'    => 'Configure Quiz',
  'post_content'  => '[configure_quiz]',
  'post_status'   => 'publish',
  'post_author'   => 4
);

$quiz_report = array(
  // 'page_template' => 'self-service-quiz/page-quiz-view.php',
  // 'page_template' => 'template-full-width.php',
  'post_type'     => 'page',
  'post_title'    => 'Quiz Report',
  'post_content'  => '[quiz_report]',
  'post_status'   => 'publish',
  'post_author'   => 4
);

$quiz_answer = array(
  'page_template' => 'self-service-quiz/page-quiz-answer.php',
  // 'page_template' => 'template-full-width.php',
  'post_type'     => 'page',
  'post_title'    => 'Quiz Answer',
  'post_content'  => '',
  'post_status'   => 'publish',
  'post_author'   => 4
);

$iframe_quiz = array(
  'page_template' => 'self-service-quiz/page-iframe-quiz.php',
  // 'page_template' => 'template-full-width.php',
  'post_type'     => 'page',
  'post_title'    => 'iframe quiz',
  'post_content'  => '',
  'post_status'   => 'publish',
  'post_author'   => 4
);

$create_a_quiz = array(
  // 'page_template' => 'self-service-quiz/page-create-a-quiz.php',
  // 'page_template' => 'template-full-width.php',
  'post_type'     => 'page',
  'post_title'    => 'Create a Quizz',
  'post_content'  => '[create-a-quiz]',
  'post_status'   => 'publish',
  'post_author'   => 4
);

$view_quiz = array(
  // 'page_template' => 'self-service-quiz/page-quiz-view.php',
  // 'page_template' => 'template-full-width.php',
  'post_type'     => 'page',
  'post_title'    => 'View Quiz',
  'post_content'  => '[view_quiz]',
  'post_status'   => 'publish',
  'post_author'   => 4
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
