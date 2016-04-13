<label for="enp-question-image-upload-<?echo $question_id;?>" class="enp-btn--add enp-question-image-upload"><svg class="enp-icon enp-icon--photo enp-question-image-upload__icon--photo">
    <use xlink:href="#icon-photo" />
</svg>
<svg class="enp-icon enp-icon--add enp-question-image-upload__icon--add">
    <use xlink:href="#icon-add" />
</svg> Add Image</label>
<input id="enp-question-image-upload-<?echo $question_id;?>" type="file" accept="image/*" class="enp-question-image-upload__input" name="question_image_upload_<?echo $question_id;?>">
<button class="enp-button enp-quiz-submit enp-button__question-image-upload" name="enp-quiz-submit" value="question-image--upload-<? echo $question_id;?>">Upload Image</button>
