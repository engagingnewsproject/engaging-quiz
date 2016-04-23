<?php
// STARTUP
// display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if(isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    // set enp-quiz-config file path (eehhhh... could be better to not use relative path stuff)
    require '../../../../../enp-quiz-config.php';
    require ENP_QUIZ_PLUGIN_DIR . 'public/quiz-take/class-enp_quiz-take.php';
    // load up quiz_take class
    $qt = new Enp_quiz_Take($quiz_id);
    if($qt->state !== 'quiz_end') {
        $qt_question = new Enp_quiz_Take_Question($qt);
    }
    $qt_end = new Enp_quiz_Take_Quiz_end($qt->quiz);
    $state = $qt->get_state();
} else {
    echo 'No quiz requested';
    exit;
}

?>
<html lang="en-US">
<head>
    <title><?php echo $qt->quiz->get_quiz_title();?></title>
    <?php // load styles
    $qt->styles();?>
</head>
<body id="enp-quiz">
<?php //add in our SVG
    echo $qt->load_svg();

?>
<section class="enp-quiz__container">
    <?php
    // echo styles
    echo $qt->load_quiz_styles();?>
    <header class="enp-quiz__header">
        <h3 class="enp-quiz__title"><?php echo $qt->quiz->get_quiz_title();?></h3>
        <div class="enp-quiz__progress">
            <div class="enp-quiz__progress__bar">
                <div class="enp-quiz__progress__bar__question-count"><?php echo  $qt->get_current_question_number();?>/<?php echo $qt->get_total_questions();?></div>
            </div>
        </div>
    </header>

    <section class="enp-question__container enp-question__container--unanswered">
        <form id="quiz" class="enp-question__form" method="post" action="<?php echo htmlentities(ENP_QUIZ_URL).$qt->quiz->quiz_id; ?>">
            <?php $qt->nonce->outputKey();?>
            <input type="hidden" name="enp-quiz-id" value="<? echo $qt->quiz->get_quiz_id();?>"/>
            <?php
            if(!empty($state) && $state === 'question') {
                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question.php');
            } elseif($state === 'question_explanation') {
                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-explanation.php');
            } elseif($state === 'quiz_end') {
                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/quiz-end.php');
            }?>
        </form>



    </section>

</section>



<?php
echo $qt->get_init_json();
echo $qt_end->get_init_json();
if(isset($qt_question) && is_object($qt_question)) {
    echo $qt_question->get_init_json();
    echo $qt_question->question_js_templates();
    echo $qt_question->question_explanation_js_template();
    echo $qt_question->mc_option_js_template();
}

// load scripts
$qt->scripts();
?>


</body>
</html>
