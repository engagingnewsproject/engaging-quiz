<div class="enp-mc-option-image__container">
    <?php echo $this->get_quiz_create_question_image($question, $question_id);?>

    <button class="enp-button enp-quiz-submit enp-button__question-image-delete" name="enp-quiz-submit" value="enp-mc-option-image--delete-<?php echo $question_id;?>"><svg class="enp-icon enp-icon--delete enp-mc-option__icon--question-image-delete">
        <use xlink:href="#icon-delete"><title>Delete Image</title></use></svg></button>

    <label class="enp-label enp-mc-option_label" for="enp-mc-option-image-alt--<?php echo $question_id;?>">Image Description</label>
    <input id="enp-mc-option-image-alt--<?php echo $question_id;?>" class="enp-input enp-input--has-description enp-question-image-alt__input" type="text" maxlength="3060"  name="enp-mc-option[<?php echo $question_i;?>][question_image_alt]" value="<?php echo $question->get_question_image_alt();?>" aria-describedby="enp-mc-option-image-alt-description--<?php echo $question_id;?>">
    <p id="enp-mc-option-image-alt-description--<?php echo $question_id;?>" class="enp-input-description enp-mc-option-image-alt__description">Used for Assistive Technology (i.e. screen readers) and SEO. Does not show on the question. </p>

</div>
