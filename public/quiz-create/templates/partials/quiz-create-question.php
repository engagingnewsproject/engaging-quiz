<?
    if(!empty($question_array[$i])) {
        $question_number = $question_array[$i];
    } else {
        $question_number = 0;
    }
    $question = new Enp_quiz_Question($question_number);
?>

<section class="enp-question-content">
    <input type="hidden" name="enp_question[<?echo $i;?>]['question_id']" value="<? echo $question->get_question_id();?>" />

    <div class="enp-question-inner enp-question">
        <label class="enp-label enp-question-title__label" for="question-title">
            Question
        </label>
        <textarea class="enp-textarea enp-question-title__textarea" name="enp_question[<?echo $i;?>]['question_title']" placeholder="Why can't we tickle ourselves?"/><? echo $question->get_question_title();?></textarea>

        <label for="enp-image-upload" class="enp-btn--add enp-image-upload"><svg class="enp-icon enp-icon--photo enp-image-upload__icon--photo">
            <use xlink:href="#icon-photo" />
        </svg>
        <svg class="enp-icon enp-icon--add enp-image-upload__icon--add">
            <use xlink:href="#icon-add" />
        </svg> Add Image</label>
        <input type="file" class="enp-image-upload__input" name="enp_question[<?echo $i;?>]['question_image']">


        <h4 class="enp-legend enp-question-type__legend">Question Type</h4>

        <input type="radio" id="enp-question-type__mc" class="enp-radio enp-question-type__input enp-question-type__input--mc" name="enp_question[<?echo $i;?>]['question_type']" value="mc" checked="checked">
        <label class="enp-label enp-question-type__label enp-question-type__label--mc" for="enp-question-type__mc">Multiple Choice</label>

        <input type="radio" id="enp-question-type__slider" class="enp-radio enp-question-type__input enp-question-type__input--slider" name="enp_question[<?echo $i;?>]['question_type']" value="slider">
        <label class="enp-label enp-question-type__label enp-question-type__label--slider" for="enp-question-type__slider">Slider</label>


        <?php
        $mc_option_i = 0;
        include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc.php');

        include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-slider.php');
        ?>
    </div>

    <div class="enp-question-inner enp-answer-explanation">
        <fieldset class="enp-fieldset enp-answer-explanation__fieldset">
            <label class="enp-label enp-answer-explanation__label">Answer Explanation</label>
            <textarea class="enp-textarea enp-answer-explanation__textarea" name="enp_question[<?echo $i;?>]['question_explanation']" placeholder="Your cerebellum can predict your own actions, so you're unable to 'surprise' yourself with a tickle."><? echo $question->get_question_explanation();?></textarea>
        </fieldset>
    </div>
</section>
