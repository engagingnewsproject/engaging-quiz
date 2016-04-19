
<input type="hidden" name="enp-question-id" value="<? echo $qt->question->get_question_id();?>"/>
<input type="hidden" name="enp-question-type" value="<? echo $qt->question->get_question_type();?>"/>
<fieldset class="enp-question__fieldset">
    <legend class="enp-question__legend enp-question__question"><? echo $qt->question->get_question_title();?></legend>

    <?php
    if(!empty($qt->question->get_question_image())) {
        include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-image.php');
    }

    if($qt->question->get_question_type() === 'mc') {?>
        <p class="enp-question__helper">Select one option.</p>
        <?foreach($qt->question->get_mc_options() as $mc_option_id) {
            $mc_option = new Enp_quiz_MC_option($mc_option_id);
            include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/mc-option.php');
        }
    }?>

    <button type="submit" class="enp-btn enp-options__submit enp-question__submit" name="enp-question-submit" value="enp-question-submit">Submit Answer <svg class="enp-icon enp-icon--chevron-right enp-options__submit__icon enp-question__submit__icon">
      <use xlink:href="#icon-chevron-right" />
    </svg></button>
</fieldset>
