<?php
	// get our question object
	$question = new Enp_quiz_Question($question_id);
	if($question_id === '{{question_id}}') {
		$question_i = '{{question_position}}';
	} else {
		$question_i = $question_i;
	}

	$question_image = $question->get_question_image();
?>

<section id="enp-question--<?php echo $question_id;?>" class="enp-question-content">
	<input class="enp-question-id" type="hidden" name="enp_question[<?php echo $question_i;?>][question_id]" value="<?php echo $question_id;?>" />
	<input class="enp-question-order" type="hidden" name="enp_question[<?php echo $question_i;?>][question_order]" value="<?php echo $question_i;?>" />

	<?php echo $Quiz_create->get_question_delete_button($question_id);
	 
	if(isset($question_ids)) : ?>
		<div class="enp-question__move">
		<?php 
		echo $Quiz_create->get_question_move_button($question_id, $question_i, 'up', $question_ids);
		echo $Quiz_create->get_question_move_button($question_id, $question_i, 'down', $question_ids);
		?>
		</div>
	<?php endif;?>


	

	<div class="enp-question-inner enp-question">
		<label class="enp-label enp-question-title__label" for="question-title-<?php echo $question_id;?>">
			Question
		</label>
		<textarea id="question-title-<?php echo $question_id;?>" class="enp-textarea enp-question-title__textarea" name="enp_question[<?php echo $question_i;?>][question_title]" maxlength="6120" placeholder="Why can't we tickle ourselves?"/><?php echo $question->get_question_title();?></textarea>

		<input type="hidden" id="enp-question-image-<?echo $question_id;?>" class="enp-question-image__input" name="enp_question[<?echo $question_i;?>][question_image]" value="<?php echo $question_image;?>" />

		<?php
			echo $Quiz_create->get_question_image_template($question, $question_id, $question_i, $question_image);
		?>

		<h4 class="enp-legend enp-question-type__legend">Question Type</h4>

		<?php echo $Quiz_create->get_question_type_input($question, $question_id, $question_i);


		include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc.php'); ?>

	<div class="enp-question-inner enp-answer-explanation">
		<fieldset class="enp-fieldset enp-answer-explanation__fieldset">
			<label class="enp-label enp-answer-explanation__label" for="enp-question-explanation__<?php echo $question_id; ?>">Answer Explanation</label>
			<script>tinymce.init({ selector: '#' + editor_id});</script>
			  <script type="text/javascript">
				tinymce.init({
					selector: '.myeditablediv',
					inline: true,
					menubar: false
				});
			</script>
			<?php
			
			// ? TODO: Need to fix: >1 question added the toolbar wont show, user has to save & refresh or preview
			// if ($question_i < 1) { // ? prevent json error for $question_id with {{}}'s
			// 	$question_id = 0;
			// }
			// $input       = $question->get_question_explanation();
			// $editor_id   = "enp-question-explanation__$question_id";
			// $editor_name = 'enp_question[' . $question_i . '][question_explanation]';
			// $settings    = array(
			// 	'textarea_name' => $editor_name,
			// 	'media_buttons' => false, // ? no media upload option
			// 	'quicktags'     => true,
			// 	'teeny'         => false,
			// 	'tinymce'       => array(
			// 		'toolbar1' => 'link,unlink', // ? limit what shows on the toolbar
			// 		'toolbar2' => '',
			// 		'toolbar3' => '',
			// 	),
			// );
			// wp_editor($input, $editor_id, $settings);

			
			?>
		</fieldset>
	</div>
</section>
