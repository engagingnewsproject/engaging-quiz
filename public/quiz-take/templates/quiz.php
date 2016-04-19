<html lang="en-US">
<head>
    <?php
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

        // check if we have a question
        if(!empty($qt->question->question_title)) {
            // set-up vars like $qt->question_title, etc
            foreach($qt->question as $key=>$value ) {
                // $$key will be what the key of the array is
                // if $key = 'quiz_title', then $$key will be available as $quiz_title
                $$key = $value;
            }
        }

    } else {
        echo 'No quiz requested';
        exit;
    }
    // load styles
    $qt->styles();
    ?>

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
        <h3 class="enp-quiz__title"><?php echo $qt->quiz->quiz_title;?></h3>
        <div class="enp-quiz__progress">
            <div class="enp-quiz__progress__bar">
                <div class="enp-quiz__progress__bar__question-count"><?php echo  $qt->current_question_number;?>/<?php echo $qt->total_questions;?></div>
            </div>
        </div>
    </header>

    <section class="enp-question__container enp-question__container--unanswered">
        <form class="enp-question__form" method="post" action="<?php echo htmlentities(ENP_QUIZ_URL).$qt->quiz->quiz_id; ?>">
            <input type="hidden" name="enp-quiz-id" value="<? echo $qt->quiz->quiz_id;?>"/>
            <?php
            if(!empty($qt->state) && $qt->state === 'question') {
                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question.php');
            } elseif($qt->state === 'question_explanation') {
                 include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-explanation.php');
            } elseif($qt->state === 'quiz_end') {
                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/quiz-results.php');
            } else {
                // shouldn't happen
                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question.php');
            }?>
        </form>



    </section>

</section>



<script type="text/template" id="question_template">
    <?php
    // set-up handlebar values to inject into template
    foreach($qt->question as $key=>$value ) {
        // $$key will be what the key of the array is
        // if $key = 'quiz_title', then $$key will be available as $quiz_title
        $$key = '{{'.$key.'}}';
    }
    include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question.php');?>
</script>





<?php
echo $qt->question_explanation_js_template();
echo $qt->mc_option_js_template();
// load scripts
$qt->scripts();
?>


</body>
</html>
