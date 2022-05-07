<!-- <li mc option> : scaffolds a single mc option -->
<?php // set-up the mc option
$mc_option = new Enp_quiz_MC_option($mc_option_id);

$mc_option_image = $mc_option->get_mc_option_image();
?>
<li id="enp-mc-option--<?php echo $mc_option_id;?>" class="enp-mc-option enp-mc-option--inputs<?php echo ((int)$mc_option->get_mc_option_correct() === 1 ? ' enp-mc_option--correct' : '');?>">

	<!-- MC order hidden inputs -->
	<input class="enp-mc-option-id hide" name="enp_question[<?php echo $question_i;?>][mc_option][<?php echo $mc_option_i;?>][mc_option_id]" value="<?php echo $mc_option_id;?>" />
	<input class="enp-mc-option-order hide" name="enp_question[<?php echo $question_i;?>][mc_option][<?php echo $mc_option_i;?>][mc_option_order]" value="<?php echo $mc_option_i;?>" />

	<!-- MC text input -->
	<label for="enp-mc-option__content--<?php echo $mc_option_id;?>" class="enp-screen-reader-text">Multiple Choice Option</label>
	<input id="enp-mc-option__content--<?php echo $mc_option_id;?>" type="text" class="enp-input enp-mc-option__input" name="enp_question[<?php echo $question_i;?>][mc_option][<?php echo $mc_option_i;?>][mc_option_content]" maxlength="255" placeholder="It's one of the great mysteries of the universe." value="<?php echo  $mc_option->get_mc_option_content();?>"/>

	<!-- TEST MC image upload -->
	<label for="enp-mc-option-image-upload-<?php echo $mc_option_id;?>" class="enp-label enp-image-upload__label">Add Image</label>
	<input id="enp-mc-option-image-upload-<?php echo $mc_option_id;?>" type="file" accept="image/*" class=" enp-mc-option-image-upload__input" name="mc_option_image_upload_<?php echo $mc_option_id;?>">
	<button class="enp-button enp-quiz-submit enp-button__mc-option-image-upload" name="enp-quiz-submit" value="mc-option-image--upload-<?php echo $mc_option_id;?>">Upload Image</button>
	
	<!-- get_mc_option_image_template -->
	<?php
		// undefined method: ENP_QUIZ_MC_OPTION
		// var_dump( $mc_option->get_mc_option_image_template() );
		// die();

		// undefined UNDEFINED VARIABLE: MC_OPTION
		// var_dump( $MC_option->get_mc_option_image_template() );
		// die();

		// Call to undefined method Enp_quiz_Quiz_create::get_mc_option_image_template() 



		// get & display the mc option image.
		// echo $mc_option->get_mc_option_image_template($mc_option, $mc_option_id, $mc_option_i, $mc_option_image);
		// var_dump( $mc_option_image );
		// die();
	?>

	<?php
	echo $Quiz_create->get_mc_option_correct_button($question_id, $mc_option_id);
	echo $Quiz_create->get_mc_option_delete_button($mc_option_id);
	?>
</li>
