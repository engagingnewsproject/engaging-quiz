<html lang="en-US">
<head>
    <style>
    html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
    border: 0;
    font-family: inherit;
    font-size: 100%;
    font-style: inherit;
    font-weight: inherit;
    margin: 0;
    outline: 0;
    padding: 0;
    vertical-align: baseline;
}
html {
    font-size: 100%; /* Corrects text resizing oddly in IE6/7 when body font-size is set using em units http://clagnut.com/blog/348/#c790 */
    overflow-y: scroll; /* Keeps page centred in all browsers regardless of content height */
    -webkit-text-size-adjust: 100%; /* Prevents iOS text size adjust after orientation change, without disabling user zoom */
    -ms-text-size-adjust: 100%; /* www.456bereastreet.com/archive/201012/controlling_text_size_in_safari_for_ios_without_disabling_user_zoom/ */
}
body {
    overflow: hidden;
}
article,
aside,
details,
figcaption,
figure,
footer,
header,
nav,
section {
    display: block;
}
ol, ul {
    list-style: none;
}
table { /* tables still need 'cellspacing="0"' in the markup */
    border-collapse: separate;
    border-spacing: 0;
}
caption, th, td {
    font-weight: normal;
    text-align: left;
}
blockquote:before, blockquote:after,
q:before, q:after {
    content: "";
}
blockquote, q {
    quotes: "" "";
}
a:focus {
    outline: thin dotted;
}
a:hover,
a:active { /* Improves readability when focused and also mouse hovered in all browsers people.opera.com/patrickl/experiments/keyboard/test */
    outline: 0;
}
a img {
    border: 0;
}

/* Reseting to border-box so we can do 100% width with 20px and not mess stuff up. */
html {
  box-sizing: border-box;
}

*, *:before, *:after {
  box-sizing: inherit;
}</style>
    <? if(isset($_GET['quiz_id'])) {
        $quiz_id = $_GET['quiz_id'];
        // set enp-quiz-config file path
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
        if($quiz->get_quiz_id() === null) {
            echo 'Quiz ID '.$quiz_id.' not found';
            exit;
        }
    } else {
        echo 'No Quiz Requested';
        exit;
    }?>
    <link rel="stylesheet" type="text/css" href="<? echo ENP_QUIZ_PLUGIN_URL;?>public/quiz-create/css/enp_quiz-create.min.css" media="all" />
</head>
<body id="enp-quiz">
<section class="enp-quiz__container">
    <style tyle="text/css">
        #enp-quiz .enp-quiz__container {
            background-color: <? echo $quiz->get_quiz_bg_color();?>;
            color: <? echo $quiz->get_quiz_bg_color();?>;
        }
        #enp-quiz .enp-quiz__title,
        #enp-quiz .enp-question__question,
        #enp-quiz .enp-option__label,
        #enp-quiz .enp-question__helper {
            color: <? echo $quiz->get_quiz_text_color();?>;
        }
    </style>
    <header class="enp-quiz__header">
        <h3 class="enp-quiz__title"><? echo $quiz->get_quiz_title();?></h3>
        <div class="enp-quiz__progress">
            <div class="enp-quiz__progress__bar">
                <div class="enp-quiz__progress__bar__question-count">1/<?php echo count($quiz->get_questions());?></div>
            </div>
        </div>
    </header>

    <section class="enp-question__container enp-question__container--unanswered">
        <form class="enp-question__form" method="post" action="<?php echo htmlentities(ENP_QUIZ_URL).$quiz->get_quiz_id(); ?>">
            <? foreach($quiz->get_questions() as $question_id) {
                $question = new Enp_quiz_Question($question_id);?>
                <input type="hidden" name="enp-question-id" value="<? echo $question_id;?>"/>
                <fieldset class="enp-question__fieldset">
                    <legend class="enp-question__legend enp-question__question"><? echo $question->get_question_title();?></legend>
                    <img
                        class="enp-question-image enp-question-image"
                        src="<? echo $question->get_question_image_src();?>"
                        srcset="<? echo $question->get_question_image_srcset();?>"
                        alt="<? echo $question->get_question_image_alt();?>"
                    />
                    <? if($question->get_question_type() === 'mc') {?>
                        <p class="enp-question__helper">Select one option.</p>
                        <?foreach($question->get_mc_options() as $mc_option_id) {
                            $mc_option = new Enp_quiz_MC_option($mc_option_id);?>
                            <input id="enp-option__<?echo $mc_option_id;?>" class="enp-option__input enp-option__input--radio enp-option__input--<? echo ($mc_option->get_mc_option_correct() == 1 ? 'correct' : 'incorrect');?>" type="radio" name="enp-options" />
                            <label for="enp-option__<?echo $mc_option_id;?>" class="enp-option__label">
                                <? echo $mc_option->get_mc_option_content();?>
                            </label>
                        <?
                        }
                    }?>

                    <button class="enp-btn enp-options__submit enp-question__submit">Submit Answer <svg class="enp-icon enp-icon--chevron-right enp-options__submit__icon enp-question__submit__icon">
                      <use xlink:href="#icon-chevron-right" />
                    </svg></button>
                </fieldset>

            <?
            // just for now while we're getting set up
            // remove this to go through more questions
            break;
            }?>
        </form>

        <? include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-explanation.php');?>

    </section>

    <? // include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/quiz-results.php');?>
</section>


<script src="<? echo ENP_QUIZ_PLUGIN_URL;?>public/quiz-take/js/enp_quiz-take.js"></script>
</body>
</html>
