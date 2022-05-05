<div class="enp-mc-option-image__container">
    <?php echo $this->get_quiz_create_mc_option_image( $mc_option, $mc_option_id );?>

    <button class="enp-button enp-quiz-submit enp-button__question-image-delete" name="enp-quiz-submit" value="enp-mc-option-image--delete-<?php echo $mc_option_id;?>"><svg class="enp-icon enp-icon--delete enp-mc-option__icon--question-image-delete">
        <use xlink:href="#icon-delete"><title>Delete Image</title></use></svg></button>
    <label class="enp-label enp-mc-option_label" for="enp-mc-option-image-alt--<?php echo $mc_option_id;?>">Image Description</label>
    <input id="enp-mc-option-image-alt--<?php echo $mc_option_id;?>" class="enp-input enp-input--has-description enp-question-image-alt__input" type="text" maxlength="3060"  name="enp-mc-option[<?php echo $mc_option_id;?>][mc_option_image_alt]" value="<?php echo $mc_option->get_mc_option_image_alt();?>" aria-describedby="enp-mc-option-image-alt-description--<?php echo $mc_option_id;?>">
    <p id="enp-mc-option-image-alt-description--<?php echo $mc_option_id;?>" class="enp-input-description enp-mc-option-image-alt__description">Used for Assistive Technology (i.e. screen readers) and SEO. Does not show on the question. </p>

</div>