<?
    if(!empty($question_array[$question_i])) {
        $question_number = $question_array[$question_i];
    } else {
        $question_number = 0;
    }
    $question = new Enp_quiz_Question($question_number);
?>

<section class="enp-question-content">
    <input type="hidden" name="enp_question[<?echo $question_i;?>][question_id]" value="<? echo $question->get_question_id();?>" />

    <div class="enp-question-inner enp-question">
        <label class="enp-label enp-question-title__label" for="question-title">
            Question
        </label>
        <textarea class="enp-textarea enp-question-title__textarea" name="enp_question[<?echo $question_i;?>][question_title]" maxlength="255" placeholder="Why can't we tickle ourselves?"/><? echo $question->get_value('question_title', $question_i);?></textarea>


        <input type="hidden" id="enp-image-<?echo $question_i;?>" class="enp-image__input" name="enp_question[<?echo $question_i;?>][question_image]" value="<? echo $question->get_question_image();?>">

        <?
            if(!empty($question->get_question_image())) {
                echo '<img src="'.$question->get_question_image().'" alt="'. $question->get_question_image_alt().'"/>';
                echo '<button class="enp-button" name="enp-quiz-submit" value="question-image--delete-'.$question->get_question_id().'"><svg class="enp-icon enp-icon--delete enp-question__icon enp-question__icon--delete"><use xlink:href="#icon-delete" /></svg></button>';
            } else {?>
                <label for="enp-image-upload-<?echo $question->get_question_id();?>" class="enp-btn--add enp-image-upload"><svg class="enp-icon enp-icon--photo enp-image-upload__icon--photo">
                    <use xlink:href="#icon-photo" />
                </svg>
                <svg class="enp-icon enp-icon--add enp-image-upload__icon--add">
                    <use xlink:href="#icon-add" />
                </svg> Add Image</label>
                <input id="enp-image-upload-<?echo $question->get_question_id();?>" type="file" accept="image/*" class="enp-image-upload__input" name="question_image_upload_<?echo $question->get_question_id();?>">
            <?
            }
        ?>

        <label class="enp-label" for="enp-image-alt-<?echo $question_i;?>">Image Description</label>
        <input type="text" id="enp-image-alt-<?echo $question_i;?>" maxlength="255" class="enp-image-alt__input" name="enp_question[<?echo $question_i;?>][question_image_alt]" value="<? echo $question->get_question_image_alt();?>">


        <h4 class="enp-legend enp-question-type__legend">Question Type</h4>

        <input type="radio" id="enp-question-type__mc" class="enp-radio enp-question-type__input enp-question-type__input--mc" name="enp_question[<?echo $question_i;?>][question_type]" value="mc" checked="checked">
        <label class="enp-label enp-question-type__label enp-question-type__label--mc" for="enp-question-type__mc">Multiple Choice</label>

        <input type="radio" id="enp-question-type__slider" class="enp-radio enp-question-type__input enp-question-type__input--slider" name="enp_question[<?echo $question_i;?>][question_type]" value="slider">
        <label class="enp-label enp-question-type__label enp-question-type__label--slider" for="enp-question-type__slider">Slider</label>


        <?php

        include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc.php');

        include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-slider.php');
        ?>
    </div>

    <div class="enp-question-inner enp-answer-explanation">
        <fieldset class="enp-fieldset enp-answer-explanation__fieldset">
            <label class="enp-label enp-answer-explanation__label">Answer Explanation</label>
            <textarea class="enp-textarea enp-answer-explanation__textarea" name="enp_question[<?echo $question_i;?>][question_explanation]" maxlength="255" placeholder="Your cerebellum can predict your own actions, so you're unable to 'surprise' yourself with a tickle."><? echo $question->get_value('question_explanation', $question_i);?></textarea>
        </fieldset>
    </div>
    <button class="enp-question__button enp-question__button--delete" name="enp-quiz-submit" value="question--delete-<?echo $question->get_question_id();?>">
        <svg class="enp-icon enp-icon--delete enp-question__icon enp-question__icon--delete"><use xlink:href="#icon-delete" /></svg>
    </button>
</section>
