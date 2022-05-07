<!-- <question> creation -->
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

<section id="enp-question--<?php echo $question_id; ?>" class="enp-question-content">

	<!-- Question order hidden inputs -->
	<input class="enp-question-id hide" name="enp_question[<?php echo $question_i;?>][question_id]" value="<?php echo $question_id;?>" />
	<input class="enp-question-order hide" name="enp_question[<?php echo $question_i;?>][question_order]" value="<?php echo $question_i;?>" />

	<!-- Question delete button -->
	<?php echo $Quiz_create->get_question_delete_button($question_id); ?>

	<!-- Question move buttons -->
	<?php if( isset( $question_ids )) : ?>
		<div class="enp-question__move">
			<?php 
			echo $Quiz_create->get_question_move_button($question_id, $question_i, 'up', $question_ids);
			echo $Quiz_create->get_question_move_button($question_id, $question_i, 'down', $question_ids);
			?>
		</div>
	<?php endif; ?>

	<!-- Question settings -->
	<div class="enp-question-inner enp-question">
		<label class="enp-label enp-question-title__label" for="question-title-<?php echo $question_id;?>">Question</label>
		
		<!-- Question title -->
		<textarea id="question-title-<?php echo $question_id;?>" class="enp-textarea enp-question-title__textarea" name="enp_question[<?php echo $question_i;?>][question_title]" placeholder="Why can't we tickle ourselves?"/><?php echo $question->get_question_title();?></textarea>
		
		<!-- <input> question image -->
		<input id="enp-question-image-<?echo $question_id;?>" class="enp-question-image__input hide" name="enp_question[<?echo $question_i;?>][question_image]" value="<?php echo $question_image;?>" />
		
		<!-- # GETS: <img> js template
			via:		$question_image
			$question: 	Enp_quiz_Question
			$this: 		Enp_quiz_Quiz_create 
		-->
		<?php
			echo $Quiz_create->get_question_image_template($question, $question_id, $question_i, $question_image);
		// var_dump( $Quiz_create );
		// die();
		?>

		<!-- Question type selector -->
		<h4 class="enp-legend enp-question-type__legend">Question Type</h4>
		<?php 
		echo $Quiz_create->get_question_type_input($question, $question_id, $question_i);
		include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc.php');
		$slider_id = $question->get_slider();
		$slider = new Enp_quiz_Slider($slider_id);
		// don't add slider in for our js question_template
		if($slider_id !== '') {
			include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-slider.php');
		} 
		$editor_default_content = "Your cerebellum can predict your own actions, so you're unable to 'surprise' yourself with a tickle.";
		?>
	</div>

	<!-- Question answer explanation -->
	<div class="enp-question-inner enp-answer-explanation">
		<fieldset class="enp-fieldset enp-answer-explanation__fieldset">
			<label class="enp-label enp-answer-explanation__label" for="enp-question-explanation__<?php echo $question_id;?>">Answer Explanation</label>
			<textarea id="enp-question-explanation__<?php echo $question_id; ?>" class="enp-textarea enp-answer-explanation__textarea" name="enp_question[<?php echo $question_i;?>][question_explanation]" maxlength="6120" rows="5" placeholder="Your cerebellum can predict your own actions, so you're unable to 'surprise' yourself with a tickle."><?php echo $question->get_question_explanation();?></textarea>
		</fieldset>
	</div>
</section>
<!-- Question exlanation script -->
<script>
	// // // // // // 
	// https://www.davidangulo.xyz/how-to-insert-data-into-wordpress-database/
	// // // // // // SAVE TO THE DATABASE!!
	tinymce.init({
		selector: '#enp-question-explanation__<?php echo $question_id;?>',  // change this value according to your HTML
		plugins: 'quickbars link' ,
		toolbar: 'bold italic link blockquote',
		quickbars_selection_toolbar: 'bold italic link blockquote',
		quickbars_insert_toolbar: false,
		quickbars_image_toolbar: false,
		menubar: false,
		statusbar: false,
		body_id: 'enp-question-explanation__<?php echo $question_id;?>',
		body_class: 'enp-textarea enp-answer-explanation__textarea',
		auto_focus : '#enp-question-explanation__<?php echo $question_id;?>',
		strict_loading_mode : true
	});
</script>