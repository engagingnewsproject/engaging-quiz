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
        $quiz_take = new Enp_quiz_Take();

        if(isset($_POST['enp-question-submit'])) {
        	$quiz_take->save_response();
        }
        // load our quiz json
        $quiz = $quiz_take->load_quiz($quiz_id);

        // check if we have a quiz
        if(empty($quiz->quiz_id)) {
            echo 'Quiz ID '.$quiz_id.' not found';
            exit;
        } else {
            // set-up Vars
            foreach($quiz as $key=>$value ) {
                // $$key will be what the key of the array is
                // if $key = 'quiz_title', then $$key will be available as $quiz_title
                $$key = $value;
            }
            $question_count = count($question);
            // get current state
            // which question are we on?
            $question = $quiz->question[0];
            // set-up vars like $question_title, etc
            foreach($question as $key=>$value ) {
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
    $quiz_take->styles();
    ?>

</head>
<body id="enp-quiz">
<?php //add in our SVG
    echo $quiz_take->load_svg();
?>
<section class="enp-quiz__container">
    <?php // prepare styles
    $styles = array('quiz_bg_color'=>$quiz_bg_color, 'quiz_text_color'=>$quiz_text_color);
    // echo styles
    echo $quiz_take->load_quiz_styles($styles);?>
    <header class="enp-quiz__header">
        <h3 class="enp-quiz__title"><? echo $quiz_title;?></h3>
        <div class="enp-quiz__progress">
            <div class="enp-quiz__progress__bar">
                <div class="enp-quiz__progress__bar__question-count">1/<?php echo $question_count;?></div>
            </div>
        </div>
    </header>

    <section class="enp-question__container enp-question__container--unanswered">
        <form class="enp-question__form" method="post" action="<?php echo htmlentities(ENP_QUIZ_URL).$quiz_id; ?>">
            <?php                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question.php');?>
        </form>

        <? include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-explanation.php');?>

    </section>


    <? // include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/quiz-results.php');?>
</section>


<?php
// set-up templates
$has_mc = false;
$has_slider = false;
?>
<script type="text/template" id="question_template">
    <?php
    // set-up handlebar values to inject into template
    foreach($question as $key=>$value ) {
        // $$key will be what the key of the array is
        // if $key = 'quiz_title', then $$key will be available as $quiz_title
        $$key = '{{'.$key.'}}';
        if($key === 'question_type' && $value === 'mc') {
            $has_mc = true;
        }
        if($key === 'question_type' && $value === 'slider') {
            $has_slider = true;
        }

    }
    include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question.php');?>
</script>



<script type="text/template" id="question_explanation_template">
    <?php include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-explanation.php');?>
</script>

<?php
if($has_mc === true) {
    $mc_option_id = '{{mc_option_id}}';
    $mc_option_content = '{{mc_option_content}}';
    echo '<script type="text/template" id="mc_option_template">';
    include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/mc_option.php');
    echo '</script>';
}

// load scripts
$quiz_take->scripts();
?>


</body>
</html>
