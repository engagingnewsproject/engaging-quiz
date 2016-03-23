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

        <label for="enp-image-upload" class="enp-btn--add enp-image-upload"><svg class="enp-icon enp-icon--photo enp-image-upload__icon--photo">
            <use xlink:href="#icon-photo" />
        </svg>
        <svg class="enp-icon enp-icon--add enp-image-upload__icon--add">
            <use xlink:href="#icon-add" />
        </svg> Add Image</label>
        <input type="file" class="enp-image-upload__input" name="enp_question[<?echo $question_i;?>][question_image]">


        <h4 class="enp-legend enp-question-type__legend">Question Type</h4>

        <input type="radio" id="enp-question-type__mc" class="enp-radio enp-question-type__input enp-question-type__input--mc" name="enp_question[<?echo $question_i;?>][question_type]" value="mc" checked="checked">
        <label class="enp-label enp-question-type__label enp-question-type__label--mc" for="enp-question-type__mc">Multiple Choice</label>

        <input type="radio" id="enp-question-type__slider" class="enp-radio enp-question-type__input enp-question-type__input--slider" name="enp_question[<?echo $question_i;?>][question_type]" value="slider">
        <label class="enp-label enp-question-type__label enp-question-type__label--slider" for="enp-question-type__slider">Slider</label>


        <?php
        $mc_option_i = 0;
        // count the number of mc_options
        $mc_option_array = $question->get_mc_options();
        $mc_option_count = count($mc_option_array);
        if($user_action['action'] === 'add' && $user_action['element'] === 'mc_option') {
            // if we're adding a new mc_option, add one to the count so we have an extra (empty) mc_option to loop through
            $mc_option_count++;
        }
        // even if it's zero, a do loop will do the loop once before checking for condition
        do {
            include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc.php');            $mc_option_i++;
        } while($mc_option_i < $mc_option_count);


        include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-slider.php');
        ?>
    </div>

    <div class="enp-question-inner enp-answer-explanation">
        <fieldset class="enp-fieldset enp-answer-explanation__fieldset">
            <label class="enp-label enp-answer-explanation__label">Answer Explanation</label>
            <textarea class="enp-textarea enp-answer-explanation__textarea" name="enp_question[<?echo $question_i;?>][question_explanation]" maxlength="255" placeholder="Your cerebellum can predict your own actions, so you're unable to 'surprise' yourself with a tickle."><? echo $question->get_value('question_explanation', $question_i);?></textarea>
        </fieldset>
    </div>
</section>
