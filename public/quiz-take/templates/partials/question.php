
<input type="hidden" name="enp-question-id" value="<? echo $question_id;?>"/>
<input type="hidden" name="enp-question-type" value="<? echo $question_type;?>"/>
<fieldset class="enp-question__fieldset">
    <legend class="enp-question__legend enp-question__question"><? echo $question_title;?></legend>

    <?php
    if(!empty($question_image)) {
        echo '<img
            class="enp-question-image enp-question-image"
            src="'.$question_image_src.'"
            srcset="'.$question_image_srcset.'"
            alt="'.$question_image_alt.'"
        />';
    }

    if($question_type === 'mc') {?>
        <p class="enp-question__helper">Select one option.</p>
        <?foreach($mc_option as $mc_option) {
            foreach($mc_option as $key=>$value ) {
                // $$key will be what the key of the array is
                // if $key = 'quiz_title', then $$key will be available as $quiz_title
                $$key = $value;
            }
            if($mc_option_correct === '1') {
                $mc_option_correct = 'correct';
            } else {
                $mc_option_correct = 'incorrect';
            }
            include(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/partials/mc_option.php');
        }
    }?>

    <button type="submit" class="enp-btn enp-options__submit enp-question__submit" name="enp-question-submit" value="enp-question-submit">Submit Answer <svg class="enp-icon enp-icon--chevron-right enp-options__submit__icon enp-question__submit__icon">
      <use xlink:href="#icon-chevron-right" />
    </svg></button>
</fieldset>
