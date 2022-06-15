<?php $mc_option = new Enp_quiz_MC_option($mc_option_id); ?>
<label for="enp-mc-option-image-upload-<?php echo $mc_option_id;?>" class="enp-label enp-image-upload__label enp-screen-reader-text">Add Image</label>


<!-- mc_option_image input -->
<input name="mc_option_image_upload_<?php echo $mc_option_id;?>" id="enp-mc-option-image-upload-<?php echo $mc_option_id;?>" class="enp-mc-option-image-upload__input" type="file" accept="image/*" >
<!-- mc_option_image input icon -->
<span class="enp-mc-option-image-upload__icon">
	<svg class="enp-icon enp-icon--photo enp-mc-option-image-upload__icon--photo" role="presentation" aria-hidden="true">
		<use xlink:href="#icon-photo" />
	</svg>
</span>
<!-- mc_option_image hidden upload button -->
<button class="enp-button enp-quiz-submit enp-button__mc-option-image-upload" name="enp-quiz-submit" value="mc-option-image--upload-<?php echo $mc_option_id;?>">Upload Image</button>