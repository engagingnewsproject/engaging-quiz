<?php

/**
 * The template for a user to create their quiz
 * and add questions to the quiz.
 *
 * @since   0.0.1
 * @package Enp_quiz
 *
 * Data available to this view:
 * $quiz = quiz object (if exits), false if new quiz
 *  // example user actions
 *  $user_action = array(
 *                      'action' =>'add',
 *                      'element' => 'mc_option',
 *                      'details' => array(
 *                        'question' => '1',
 *                      ),
 *                  );
 * for reference,
 * $this = $Quiz_create = Enp_quiz_Quiz_create class
 */
?>
<aside class="enp-dash__section-aside">
	<?php echo $Quiz_create->dashboard_breadcrumb_link(); ?>
</aside>
<article class="enp-container enp-dash-container">
	<section class="enp-container enp-quiz-form-container js-enp-quiz-create-form-container">
		<?php require_once ENP_QUIZ_CREATE_TEMPLATES_PATH . '/partials/quiz-create-breadcrumbs.php'; ?>

		<?php do_action('enp_quiz_display_messages'); ?>

		<form id="enp-quiz-create-form" class="enp-form enp-quiz-form" enctype="multipart/form-data" method="post" action="<?php echo $Quiz_create->get_quiz_action_url(); ?>" novalidate>
			<?php
			$enp_quiz_nonce->outputKey();
			echo $Quiz_create->hidden_fields(); ?>

			<fieldset class="enp-fieldset enp-quiz-title">
				<label class="enp-label enp-quiz-title__label enp-slider-correct-high__input-container--hidden" for="quiz-title">
					Quiz Title
				</label>
				<textarea id="quiz-title" class="enp-textarea enp-quiz-title__textarea" type="text" name="enp_quiz[quiz_title]" maxlength="255" placeholder="An engaging quiz title. . ." /><?php echo $quiz->get_value('quiz_title') ?></textarea>
				<!-- Quiz title test: /quiz-create/ -->
				<?php
				/**
				 * 
				 * // // // AN DUPLICATE (commented out) INPUT ELEMENT TO SAVE US ALL! // // //
				 * All of these files are part of the "quiz_title_test" example of how to replicate 
				 * an input element that will save to the database, and display across the quiz:
				 * 
				 * 1) quiz-create.php, 
				 * 2) quiz-preview.php, 
				 * 3) quiz-results.php, 
				 * 4) ab-test-fieldset.php
				 * 5) dashboard-ab-item.php, 
				 * 6) dashboard-quiz-item.php,  <-- Includes function: GET_QUIZ_DASHBOARD_ITEM_TITLE_TEST
				 * 7) question-results-section.php,
				 * 8) quiz.php, 
				 * 9) class-enp_quiz_save_quiz_response.php, 
				 * 10) class-enp_quiz_save_quiz.php,
				 * 11) class-enp_quiz-quiz.php, 
				 * 12) class-enp_quiz-dashboard.php, 
				 * 13) ab-results.php
				 * 14) class-enp_quiz-activator.php
				 * 
				 * 
				 *
				 * Not really in any particular order, it just looks nicer that way. 
				 *
				 */
				?>
				<!-- <textarea id="quiz-title_test" class="enp-textarea enp-quiz-title__textarea" type="text" name="enp_quiz[quiz_title_test]" maxlength="255" placeholder="An engaging quiz title. . ." /><?php // echo $quiz->get_value('quiz_title_test') ?></textarea> -->

			</fieldset>

			<section class="enp-quiz-create__questions">
				<?php
				$question_i = 0;
				// count the number of questions
				$question_ids = $quiz->get_questions();
				if (!empty($question_ids)) {
					foreach ($question_ids as $question_id) {
						include ENP_QUIZ_CREATE_TEMPLATES_PATH . '/partials/quiz-create-question.php';
						$question_i++;
					}
				}
				?>
			</section>

			<?php echo $Quiz_create->get_add_question_button(); ?>

			<div class="enp-btn--save__btns">

				<button type="submit" class="enp-btn--save enp-quiz-submit enp-quiz-form__save" name="enp-quiz-submit" value="save">Save</button>

				<?php echo $Quiz_create->get_next_step_button(); ?>

			</div>

		</form>
	</section>
</article>
<!-- <footer> -->
			<!-- <form id="enp-quiz-create-form" class="enp-form enp-quiz-form" enctype="multipart/form-data" method="post" action="<?php // echo $Quiz_create->get_quiz_action_url(); ?>" novalidate> -->
			<?php
			// $enp_quiz_nonce->outputKey();
			// echo $Quiz_create->hidden_fields(); ?>
				<!-- <textarea id="quiz-title_test" class="enp-textarea enp-quiz-title__textarea" type="text" name="enp_quiz[quiz_title_test]" maxlength="255" placeholder="An engaging quiz title. . ." /><?php // echo $quiz->get_value('quiz_title_test') ?></textarea> -->

			<!-- </form> -->
<!-- </footer> -->
<?php
