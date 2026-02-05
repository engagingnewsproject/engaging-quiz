<?php
/**
 * Partial: question image upload (label + file input + submit button).
 *
 * Renders the hidden file input and native submit button for uploading a question image.
 * The visible "ADD IMAGE" control is the styled button injected elsewhere; this partial
 * is included inside the question image area and is kept for form semantics and a11y.
 *
 * Required: $question_id (int) — current question ID for input names/ids.
 */
?>
<label for="enp-question-image-upload-<?php echo $question_id;?>" class="enp-label enp-image-upload__label enp-screen-reader-text">Add Image</label>
<input id="enp-question-image-upload-<?php echo $question_id;?>" type="file" accept="image/*" class="enp-question-image-upload__input" name="question_image_upload_<?php echo $question_id;?>" aria-label="<?php esc_attr_e( 'Add image', 'enp-quiz' ); ?>">
<button class="enp-button enp-quiz-submit enp-button__question-image-upload" name="enp-quiz-submit" value="question-image--upload-<?php echo $question_id;?>">Upload Image</button>
