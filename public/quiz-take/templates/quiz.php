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
        // require the necessary files
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-quiz.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-question.php';
        require ENP_QUIZ_PLUGIN_DIR . 'includes/class-enp_quiz-mc_option.php';
        // Database
        require ENP_QUIZ_PLUGIN_DIR . 'database/class-enp_quiz_db.php';


        // set-up our quiz
        $quiz = new Enp_quiz_Quiz($quiz_id);
        $quiz = $quiz->get_quiz_json();
        $quiz = json_decode($quiz);

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
        }
    } else {
        echo 'No Quiz Requested';
        exit;
    }?>
    <link rel="stylesheet" type="text/css" href="<? echo ENP_QUIZ_PLUGIN_URL;?>public/quiz-take/css/enp_quiz-take.min.css" media="all" />
</head>
<body id="enp-quiz">
<?php //add in our SVG
    $svg = file_get_contents(ENP_QUIZ_PLUGIN_URL.'/public/quiz-take/svg/symbol-defs.svg');
    echo $svg;
?>
<section class="enp-quiz__container">
    <style tyle="text/css">
        #enp-quiz .enp-quiz__container {
            background-color: <?php echo $quiz_bg_color;?>;
            color: <?php echo $quiz_text_color;?>;
        }
        #enp-quiz .enp-quiz__title,
        #enp-quiz .enp-question__question,
        #enp-quiz .enp-option__label,
        #enp-quiz .enp-question__helper {
            color: <?php echo $quiz_text_color;?>;
        }
    </style>
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
            <?php
            foreach($question as $question) {
                // set-up vars like $question_title, etc
                foreach($question as $key=>$value ) {
                    // $$key will be what the key of the array is
                    // if $key = 'quiz_title', then $$key will be available as $quiz_title
                    $$key = $value;
                }
                include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question.php');
            // just for now while we're getting set up
            // remove this to go through more questions
            break;
            }?>
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
?>

<script src="<? echo ENP_QUIZ_PLUGIN_URL;?>public/quiz-take/js/enp_quiz-take.js"></script>
</body>
</html>
