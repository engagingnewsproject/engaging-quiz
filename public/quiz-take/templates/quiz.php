
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
                <div class="enp-quiz__progress__bar__question-count">1/<?php echo count($quiz->get_questions());?><span class="enp-"</div>
            </div>
        </div>
    </header>

    <section class="enp-question__container enp-question__container--unanswered">
        <form class="enp-question__form" method="post" action="<?php echo htmlentities(site_url('enp-quiz/quiz-take/').$quiz->get_quiz_id().'/'); ?>"">
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

<script src="js/quiz.js"></script>
