<?php

/**
 * The template for a user to preview and test their quiz before
 * publishing it.
 *
 * @since             0.0.1
 * @package           Enp_quiz
 *
 * Data available to this view:
 * $quiz = quiz object (if exits), error page if it doesn't (TODO)
 */
?>
<aside class="enp-dash__section-aside">
	<?php echo $this->dashboard_breadcrumb_link(); ?>
</aside>

<article class="enp-container enp-dash-container">
<?php 
// $quiz_thing = new Enp_quiz_Quiz($dbQuiz['quiz_id']);
// var_dump($quiz_thing);
?>
	<div class="enp-container enp-preview-page-container js-enp-quiz-create-form-container">
		<?php include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH . '/partials/quiz-create-breadcrumbs.php'); ?>
		<?php do_action('enp_quiz_display_messages'); ?>
		<div class="enp-flex enp-preview-page-flex-container">
			<section class="enp-container enp-quiz-settings-container">
				<h1 class="enp-quiz-settings__title">Quiz Settings</h1>
				<form class="enp-form enp-quiz-settings__form" method="post" action="<?php echo htmlentities(ENP_QUIZ_PREVIEW_URL . $quiz->get_quiz_id() . '/'); ?>">
					<?php $enp_quiz_nonce->outputKey(); ?>
					<input type="hidden" name="enp_quiz[quiz_id]" value="<?php echo $quiz->get_quiz_id(); ?>" />

					<fieldset id="enp-quiz-styles" class="enp-fieldset enp-fieldset--section enp-quiz-styles">
						<legend class="enp-legend enp-quiz-styles__legend">Quiz Styles</legend>

						<fieldset class="enp-fieldset enp-title-display">
							<legend class="enp-legend enp-title-display__legend">Title Display</legend>
							<?php $quiz_title_display = $quiz->get_quiz_title_display(); ?>
							<input id="enp-quiz-title-show" class="enp-radio enp-title-display__input enp-title-display__input--title-display" type="radio" name="enp_quiz[quiz_title_display]" value="show" <?php checked($quiz_title_display, 'show'); ?> />
							<label class="enp-label enp-title-display__label enp-title-display__label--title-display" for="enp-quiz-title-show">
								Show Title
							</label>
							<input class="enp-radio enp-title-display__input enp-title-display__input--hide-title" id="enp-quiz-title-hide" type="radio" name="enp_quiz[quiz_title_display]" value="hide" <?php checked($quiz_title_display, 'hide'); ?> />
							<label class="enp-label enp-title-display__label enp-title-display__label--hide-title" for="enp-quiz-title-hide">
								Hide Title
							</label>
						</fieldset>

						<!-- <fieldset class="enp-fieldset enp-title-display">
							<legend class="enp-legend enp-title-display__legend">Title Test Display</legend>
							<?php // $quiz_title_test_display = $quiz->get_quiz_title_test_display(); 
							?>
							<input id="enp-quiz-title-show" class="enp-radio enp-title-display__input enp-title-display__input--title-display" type="radio" name="enp_quiz[quiz_title_test_display]" value="show" <?php // checked($quiz_title_test_display, 'show'); ?> />
							<label class="enp-label enp-title-display__label enp-title-display__label--title-display" for="enp-quiz-title-show">
								Show Title
							</label>
							<input class="enp-radio enp-title-display__input enp-title-display__input--hide-title" id="enp-quiz-title-hide" type="radio" name="enp_quiz[quiz_title_test_display]" value="hide" <?php // checked($quiz_title_test_display, 'hide'); ?> />
							<label class="enp-label enp-title-display__label enp-title-display__label--hide-title" for="enp-quiz-title-hide">
								Hide Title
							</label>
						</fieldset> -->

						<fieldset class="enp-fieldset enp-mc-options-order">
							<legend class="enp-legend enp-mc-options-order__legend">Multiple Choice Options Order</legend>
							<?php $quiz_mc_options_order = $quiz->get_quiz_mc_options_order(); ?>
							<input id="enp-quiz-mc-options-order--random" class="enp-radio enp-mc-options-order__input enp-mc-options-order__input--mc-options-order" type="radio" name="enp_quiz[quiz_mc_options_order]" value="random" <?php checked($quiz_mc_options_order, 'random'); ?> />
							<label class="enp-label enp-mc-options-order__label" for="enp-quiz-mc-options-order--random">
								Random
							</label>
							<input class="enp-radio enp-mc-options-order__input enp-mc-options-order__input--hide-title" id="enp-quiz-mc-options-order--user-order" type="radio" name="enp_quiz[quiz_mc_options_order]" value="user_order" <?php checked($quiz_mc_options_order, 'user_order'); ?> />
							<label class="enp-label enp-mc-options-order__label" for="enp-quiz-mc-options-order--user-order">
								Set Order
							</label>
						</fieldset>

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--width" for="enp-quiz-width">
							Width
						</label>
						<input id="enp-quiz-width" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--width" type="text" maxlength="8" name="enp_quiz[quiz_width]" value="<?php echo $quiz->get_quiz_width(); ?>" />

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--bg-color" for="enp-quiz-bg-color">
							Background Color
						</label>
						<input id="enp-quiz-bg-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--bg-color" type="text" name="enp_quiz[quiz_bg_color]" maxlength="7" value="<?php echo $quiz->get_quiz_bg_color(); ?>" data-default="#ffffff" />

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--text-color" for="enp-quiz-text-color">
							Text Color
						</label>
						<input id="enp-quiz-text-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--text-color" type="text" name="enp_quiz[quiz_text_color]" maxlength="7" value="<?php echo $quiz->get_quiz_text_color(); ?>" data-default="#444444" />

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--border-color" for="enp-quiz-border-color">
							Border Color
						</label>
						<input id="enp-quiz-border-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--border-color" type="border" name="enp_quiz[quiz_border_color]" maxlength="7" value="<?php echo $quiz->get_quiz_border_color(); ?>" data-default="#dddddd" />

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--button-color" for="enp-quiz-button-color">
							Button Color
						</label>
						<input id="enp-quiz-button-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--button-color" type="text" name="enp_quiz[quiz_button_color]" maxlength="7" value="<?php echo $quiz->get_quiz_button_color(); ?>" data-default="#5887C0" />

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--correct-color" for="enp-quiz-correct-color">
							"Correct" Color
						</label>
						<input id="enp-quiz-correct-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--correct-color" type="text" name="enp_quiz[quiz_correct_color]" maxlength="7" value="<?php echo $quiz->get_quiz_correct_color(); ?>" data-default="#3bb275" />

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--incorrect-color" for="enp-quiz-incorrect-color">
							"Incorrect" Color
						</label>
						<input id="enp-quiz-incorrect-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--incorrect-color" type="text" name="enp_quiz[quiz_incorrect_color]" maxlength="7" value="<?php echo $quiz->get_quiz_incorrect_color(); ?>" data-default="#f14021" />

						<label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--custom-css" for="enp-quiz-custom-css">
							Custom CSS
						</label>
						<p id="enp-quiz-styles__custom-css" class="enp-textarea-description enp-textarea-description--before"><strong>Advanced.</strong> Entered CSS could break your quiz, and is sanitized on save.</p>
						<textarea id="enp-quiz-custom-css" class="enp-textarea enp-quiz-styles__textarea enp-quiz-styles__textarea--custom-css" type="text" name="enp_quiz[quiz_custom_css]" aria-describedby="enp-quiz-styles__custom-css"><?php echo $quiz->get_quiz_custom_css(); ?></textarea>

					</fieldset>

					<!-- SHARE TEXT dropdown -->
					<fieldset id="enp-quiz-share-text" class="enp-fieldset enp-fieldset--section">
						<legend class="enp-legend enp-fieldset--section__title enp-quiz-share__legend">Quiz Share Text</legend>


						<fieldset class="enp-fieldset enp-quiz-share enp-quiz-share--facebook">

							<legend class="enp-legend enp-quiz-share__legend">Facebook Share</legend>
							<p class="enp-input-description">When sharing your quiz on Facebook, what should the title, description and quote be?</p>

							<label class="enp-label enp-quiz-share__label" for="enp-facebook-title">
								Facebook Share Title
							</label>
							<textarea id="enp-facebook-title" class="enp-textarea enp-quiz-share__textarea enp-textarea" maxlength="140" name="enp_quiz[facebook_title]"><?php echo $quiz->get_facebook_title(); ?></textarea>

							<label class="enp-label enp-quiz-share__label" for="enp-facebook-description">
								Facebook Share Description
							</label>
							<textarea id="enp-facebook-description" class="enp-textarea enp-quiz-share__textarea enp-textarea" maxlength="140" name="enp_quiz[facebook_description]"><?php echo $quiz->get_facebook_description(); ?></textarea>
							<label class="enp-label enp-quiz-share__label" for="enp-facebook-quote-end">
								Facebook Share Quote
							</label>
							<p id="enp-share__facebook-quote-end-description" class="enp-textarea-description enp-textarea-description--before">Use {{score_percentage}} to show someone's score.</p>
							<textarea id="enp-facebook-quote-end" class="enp-textarea enp-quiz-share__textarea enp-textarea enp-textarea--has-description--before" maxlength="255" name="enp_quiz[facebook_quote_end]" aria-describedby="enp-share__facebook-quote-end-description"><?php echo $quiz->get_facebook_quote_end(); ?></textarea>

						</fieldset>

						<fieldset class="enp-fieldset enp-quiz-share enp-quiz-share--twitter">
							<legend class="enp-legend enp-quiz-share__legend">Twitter Share</legend>
							<p class="enp-input-description">After taking the quiz, what should someone's default tweet be?</p>

							<label class="enp-label enp-quiz-share__label" for="enp-tweet-end">
								Tweet
							</label>
							<p id="enp-share-twitter__description" class="enp-textarea-description enp-textarea-description--before">Use {{score_percentage}} to show someone's score.</p>
							<textarea id="enp-tweet-end" class="enp-textarea enp-quiz-share__textarea enp-quiz-share__textarea--tweet enp-quiz-share__textarea--after" maxlength="117" name="enp_quiz[tweet_end]" aria-describedby="enp-share-twitter__description"><?php echo $quiz->get_tweet_end(); ?></textarea>
						</fieldset>
					</fieldset>

					<!-- win/lose RESULTS dropdown -->
					<fieldset id="enp-quiz-winlose-text" class="enp-fieldset enp-fieldset--section">
						<legend class="enp-legend enp-fieldset--section__title enp-quiz-winlose__legend">Results Screen</legend>
						<fieldset class="enp-fieldset enp-quiz-winlose enp-quiz-winlose--win">
							<p class="enp-input-description">Text to display on quiz completion.</p>

							<label class="enp-label enp-quiz-winlose__label" for="enp-win-title">
								Perfect Score &#129321;
							</label>
							<label class="enp-label__sm enp-quiz-winlose__label" for="enp-perfect-description">Title</label>
							<input id="enp-perfect-title" class="enp-input enp-input__sm enp-quiz-winlose-perfect__input" maxlength="140" rows="1" name="enp_quiz[quiz_end_perfect_title]" type="text" value="<?php echo $quiz->get_quiz_end_perfect_title(); ?>">
							<label class="enp-label__sm enp-quiz-winlose-fail__label" for="enp-good-description">Description</label>
							<textarea id="enp-perfect-description" class="enp-textarea enp-quiz-winlose__textarea enp-textarea" maxlength="140" name="enp_quiz[quiz_end_perfect_description]"><?php echo $quiz->get_quiz_end_perfect_description(); ?></textarea>
							<hr>
							
							<label class="enp-label enp-quiz-winlose__label" for="enp-good-title">
								Good Score :)
							</label>
							<label class="enp-label__sm enp-quiz-winlose__label" for="enp-good-description">Title</label>
							<input id="enp-good-title" class="enp-input enp-input__sm enp-quiz-winlose-fail__input" maxlength="140" rows="1" name="enp_quiz[quiz_end_good_title]" type="text" value="<?php echo $quiz->get_quiz_end_good_title(); ?>">
							<label class="enp-label__sm enp-quiz-winlose-fail__label" for="enp-good-description">Description</label>
							<textarea id="enp-good-description" class="enp-textarea enp-quiz-winlose__textarea enp-textarea" maxlength="140" name="enp_quiz[quiz_end_good_description]"><?php echo $quiz->get_quiz_end_good_description(); ?></textarea>
							<hr>

							<label class="enp-label enp-quiz-winlose__label" for="enp-average-title">
								Average Score :|
							</label>
							<label class="enp-label__sm enp-quiz-winlose__label" for="enp-average-description">Title</label>
							<input id="enp-average-title" class="enp-input enp-input__sm enp-quiz-winlose-fail__input" maxlength="140" rows="1" name="enp_quiz[quiz_end_average_title]" type="text" value="<?php echo $quiz->get_quiz_end_average_title(); ?>">
							<label class="enp-label__sm enp-quiz-winlose-fail__label" for="enp-average-description">Description</label>
							<textarea id="enp-average-description" class="enp-textarea enp-quiz-winlose__textarea enp-textarea" maxlength="140" name="enp_quiz[quiz_end_average_description]"><?php echo $quiz->get_quiz_end_average_description(); ?></textarea>
							<hr>

							<label class="enp-label enp-quiz-winlose__label" for="enp-fail-title">
								Failing Score :(
							</label>
							<label class="enp-label__sm enp-quiz-winlose__label" for="enp-fail-description">Title</label>
							<input id="enp-fail-title" class="enp-input enp-input__sm enp-quiz-winlose-fail__input" maxlength="140" rows="1" name="enp_quiz[quiz_end_fail_title]" type="text" value="<?php echo $quiz->get_quiz_end_fail_title(); ?>">
							<label class="enp-label__sm enp-quiz-winlose-fail__label" for="enp-fail-description">Description</label>
							<textarea id="enp-fail-description" class="enp-textarea enp-quiz-winlose__textarea enp-textarea" maxlength="140" name="enp_quiz[quiz_end_fail_description]"><?php echo $quiz->get_quiz_end_fail_description(); ?></textarea>

						</fieldset>
					</fieldset>
					<!-- feedback submission -->
					<fieldset id="enp-quiz-feedback-text" class="enp-fieldset enp-fieldset--section">
						<legend class="enp-legend enp-fieldset--section__title enp-quiz-feedback__legend">Feedback?</legend>
						<fieldset class="enp-fieldset enp-quiz-feedback enp-quiz-feedback">
							<p class="enp-input-description">What did you think of our quiz tool?</p>
							<textarea id="enp-quiz-feedback" class="enp-textarea enp-quiz-feedback__textarea enp-textarea" maxlength="140" name="enp_quiz[quiz_feedback]" placeholder="We would love to hear from you!"></textarea>
						</fieldset>
					</fieldset>
					<button type="submit" class="enp-btn--submit enp-preview-form__submit" name="enp-quiz-submit" value="quiz-save">Save</button>
					<button type="submit" id="enp-btn--next-step" class="enp-btn--submit enp-btn--next-step enp-preview-form__submit--publish" name="enp-quiz-submit" value="quiz-publish"><? echo $enp_next_button_name; ?> <svg class="enp-icon enp-icon--chevron-right enp-btn--next-step__icon enp-preview-form__submit__icon">
							<use xlink:href="#icon-chevron-right" />
						</svg></button>
				</form>
			</section>

			<section class="enp-container enp-quiz-preview-container">
				<h2 class="enp-quiz-preview__title">Quiz Preview</h2>

				<script type="text/javascript" src="<?php echo ENP_QUIZ_PLUGIN_URL; ?>public/quiz-take/js/dist/iframe-parent.js"></script>

				<iframe id="enp-quiz-iframe-<?php echo $quiz->get_quiz_id(); ?>" class="enp-quiz-iframe" src="<?php echo ENP_QUIZ_URL . $quiz->get_quiz_id(); ?>" style="width: <?php echo $quiz->get_quiz_width(); ?>; height: 500px;"></iframe>

			</section>
		</div>
	</div>

</article>