<?php
/**
 * Partial: single multiple-choice option row for the quiz create form.
 *
 * Renders one <li> containing the option text input, optional image upload (when quiz is
 * editable), image preview with remove button if an image exists, and the correct-answer
 * toggle. Used inside the MC options list for each question.
 *
 * Required (from including scope):
 *   - $mc_option_id (int)  Option ID for names/ids and loading data.
 *   - $question_id (int)   Parent question ID (for image paths and correct button).
 *   - $question_i (int)    Question index in form array.
 *   - $mc_option_i (int)   Option index in form array.
 *   - $Quiz_create         Enp_quiz_Create instance (quiz state, delete/correct buttons).
 */
$mc_option = new Enp_quiz_MC_option($mc_option_id);
$mc_option_image = $mc_option->get_mc_option_image();
$quiz_id = isset($Quiz_create->quiz) ? $Quiz_create->quiz->get_quiz_id() : 0;
$mc_option_image_src = '';
if ( ! empty( $mc_option_image ) && $quiz_id > 0 ) {
	$mc_option_image_src = ENP_QUIZ_IMAGE_URL . $quiz_id . '/' . $question_id . '/mc_option_' . $mc_option_id . '/' . $mc_option_image;
}
?>
<li id="enp-mc-option--<?php echo $mc_option_id;?>" class="enp-mc-option enp-mc-option--inputs<?php echo ((int)$mc_option->get_mc_option_correct() === 1 ? ' enp-mc-option--correct' : '');?>">
	<input class="enp-mc-option-id" type="hidden" name="enp_question[<?php echo $question_i;?>][mc_option][<?php echo $mc_option_i;?>][mc_option_id]" value="<?php echo $mc_option_id;?>" />
	<input class="enp-mc-option-order" type="hidden" name="enp_question[<?php echo $question_i;?>][mc_option][<?php echo $mc_option_i;?>][mc_option_order]" value="<?php echo $mc_option_i;?>" />
	<input class="enp-mc-option-image__input" type="hidden" name="enp_question[<?php echo $question_i;?>][mc_option][<?php echo $mc_option_i;?>][mc_option_image]" value="<?php echo esc_attr( $mc_option_image ); ?>" />

	<label for="enp-mc-option__content--<?php echo $mc_option_id;?>" class="enp-screen-reader-text">Multiple Choice Option</label>
	<div class="enp-mc-option__input-row">
		<div class="enp-mc-option__input-row-content">
			<input id="enp-mc-option__content--<?php echo $mc_option_id;?>" type="text" class="enp-input enp-mc-option__input" name="enp_question[<?php echo $question_i;?>][mc_option][<?php echo $mc_option_i;?>][mc_option_content]" maxlength="255" placeholder="It's one of the great mysteries of the universe." value="<?php echo esc_attr( $mc_option->get_mc_option_content() ); ?>"/>
			<div class="enp-mc-option__right-actions">
				<?php if ( $Quiz_create->is_quiz_fully_editable() ) : ?>
					<button type="button" class="enp-mc-option-image-upload__trigger enp-mc-option__button enp-mc-option__button--add-image" aria-label="Add image"><svg class="enp-icon enp-icon--image enp-mc-option__icon enp-mc-option__icon--add-image"><use xlink:href="#icon-image"></use></svg></button>
				<?php endif; ?>
				<?php echo $Quiz_create->get_mc_option_delete_button($mc_option_id); ?>
			</div>
		</div>
		<?php if ( $Quiz_create->is_quiz_fully_editable() ) : ?>
			<div class="enp-mc-option-image-upload">
				<label for="enp-mc-option-image-upload--<?php echo $mc_option_id; ?>" class="enp-label enp-image-upload__label enp-screen-reader-text">Add Image</label>
				<input id="enp-mc-option-image-upload--<?php echo $mc_option_id; ?>" type="file" accept="image/*" class="enp-mc-option-image-upload__input" name="mc_option_image_upload_<?php echo $mc_option_id; ?>" aria-label="<?php esc_attr_e( 'Add image', 'enp-quiz' ); ?>">
				<div class="enp-mc-option-image-preview enp-mc-option-image-preview--hidden"></div>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $mc_option_image_src ) ) : ?>
		<div class="enp-mc-option-image__container">
			<img class="enp-mc-option-image" src="<?php echo esc_attr( $mc_option_image_src ); ?>" alt="" />
			<button type="submit" class="enp-button enp-quiz-submit enp-mc-option-image__remove" name="enp-quiz-submit" value="mc-option-image--delete-<?php echo $mc_option_id; ?>" aria-label="Remove image"><svg class="enp-icon enp-icon--delete"><use xlink:href="#icon-delete"><title>Remove image</title></use></svg></button>
		</div>
	<?php endif; ?>

	<?php echo $Quiz_create->get_mc_option_correct_button($question_id, $mc_option_id); ?>
</li>
