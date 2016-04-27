<fieldset id="question_<?php echo $qt_question->question->get_question_id();?>" class="enp-question__fieldset <?php echo $qt_question->get_question_classes();?>">
    <input id="enp-question-id" type="hidden" name="enp-question-id" value="<? echo $qt_question->question->get_question_id();?>"/>
    <input id="enp-question-type" type="hidden" name="enp-question-type" value="<? echo $qt_question->question->get_question_type();?>"/>

    <legend class="enp-question__legend enp-question__question"><? echo $qt_question->question->get_question_title();?></legend>

    <?php
    if(!empty($qt_question->question->get_question_image())) {
        include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-image.php');
    }

    if($qt_question->qt->get_state() === 'question_explanation') {
        include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/question-explanation.php');
    }

    if($qt_question->question->get_question_type() === 'mc' && $qt_question->qt->get_state() === 'question') {?>
        <p class="enp-question__helper">Select one option.</p>
        <?php foreach($qt_question->question->get_mc_options() as $mc_option_id) {
            $mc_option = new Enp_quiz_MC_option($mc_option_id);
            include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/mc-option.php');
        }
    }?>

    <button type="submit" class="enp-btn enp-options__submit enp-question__submit" name="enp-question-submit" value="enp-question-submit">Submit Answer <svg class="enp-icon enp-icon--chevron-right enp-options__submit__icon enp-question__submit__icon">
      <use xlink:href="#icon-chevron-right" />
    </svg></button>


</fieldset>
