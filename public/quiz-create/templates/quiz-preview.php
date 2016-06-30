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
<?php echo $this->dashboard_breadcrumb_link();?>
<div class="enp-container enp-preview-page-container js-enp-quiz-create-form-container">
    <?php include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-breadcrumbs.php');?>
    <?php do_action('enp_quiz_display_messages'); ?>
    <div class="enp-flex enp-preview-page-flex-container">
        <section class="enp-container enp-quiz-settings-container">
            <h1 class="enp-quiz-settings__title">Quiz Settings</h1>
            <form class="enp-form enp-quiz-settings__form" method="post" action="<?php echo htmlentities(ENP_QUIZ_PREVIEW_URL.$quiz->get_quiz_id().'/'); ?>">
                <?php $enp_quiz_nonce->outputKey();?>
                <input type="hidden" name="enp_quiz[quiz_id]" value="<? echo $quiz->get_quiz_id();?>" />

                <fieldset id="enp-quiz-styles" class="enp-fieldset enp-fieldset--section enp-quiz-styles">
                    <legend class="enp-legend enp-quiz-styles__legend">Quiz Styles</legend>

                    <fieldset class="enp-fieldset enp-title-display">
                        <legend class="enp-legend enp-title-display__legend">Title Display</legend>
                        <? $quiz_title_display = $quiz->get_quiz_title_display();?>
                        <input id="enp-quiz-title-show" class="enp-radio enp-title-display__input enp-title-display__input--title-display" type="radio" name="enp_quiz[quiz_title_display]" value="show" <?php checked( $quiz_title_display, 'show' ); ?>/>
                        <label class="enp-label enp-title-display__label enp-title-display__label--title-display" for="enp-quiz-title-show">
                            Show Title
                        </label>
                        <input class="enp-radio enp-title-display__input enp-title-display__input--hide-title" id="enp-quiz-title-hide" type="radio" name="enp_quiz[quiz_title_display]" value="hide" <?php checked( $quiz_title_display, 'hide' ); ?>/>
                        <label class="enp-label enp-title-display__label enp-title-display__label--hide-title" for="enp-quiz-title-hide">
                            Hide Title
                        </label>
                    </fieldset>


                    <label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--width" for="enp-quiz-width">
                        Width
                    </label>
                    <input id="enp-quiz-width" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--width" type="text" maxlength="8" name="enp_quiz[quiz_width]" value="<? echo $quiz->get_quiz_width();?>"/>

                    <label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--bg-color" for="enp-quiz-bg-color">
                        Background Color
                    </label>
                    <input id="enp-quiz-bg-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--bg-color" type="text" name="enp_quiz[quiz_bg_color]" maxlength="7" value="<? echo $quiz->get_quiz_bg_color();?>" data-default="#ffffff"/>

                    <label class="enp-label enp-quiz-styles__label enp-quiz-styles__label--text-color" for="enp-quiz-text-color">
                        Text Color
                    </label>
                    <input id="enp-quiz-text-color" class="enp-input enp-quiz-styles__input enp-quiz-styles__input--color enp-quiz-styles__input--text-color" type="text" name="enp_quiz[quiz_text_color]" maxlength="7" value="<? echo $quiz->get_quiz_text_color();?>" data-default="#444444"/>

                </fieldset>

                <fieldset id="enp-quiz-share-text" class="enp-fieldset enp-fieldset--section">
                    <legend class="enp-legend enp-fieldset--section__title enp-quiz-share__legend">Quiz Share Text</legend>


                    <fieldset class="enp-fieldset enp-quiz-share enp-quiz-share--facebook">

                        <legend class="enp-legend enp-quiz-share__legend">Facebook Share</legend>
                        <p class="enp-input-description">When sharing your Quiz on Facebook, what should the title, description, and quote be?</p>

                        <label class="enp-label enp-quiz-share__label" for="enp-facebook-title">
                            Facebook Share Title
                        </label>
                        <textarea id="enp-facebook-title" class="enp-textarea enp-quiz-share__textarea enp-textarea" maxlength="140" name="enp_quiz[facebook_title]"><?php echo $quiz->get_facebook_title();?></textarea>

                        <label class="enp-label enp-quiz-share__label" for="enp-facebook-description">
                            Facebook Share Description
                        </label>
                        <textarea id="enp-facebook-description" class="enp-textarea enp-quiz-share__textarea enp-textarea" maxlength="140" name="enp_quiz[facebook_description]"><?php echo $quiz->get_facebook_description();?></textarea>

                        <label class="enp-label enp-quiz-share__label" for="enp-facebook-quote-end">
                            Facebook Share Quote
                        </label>
                        <p id="enp-share__facebook-quote-end-description" class="enp-textarea-description enp-textarea-description--before">Use {{score_percentage}} to show someone's score.</p>
                        <textarea id="enp-facebook-quote-end" class="enp-textarea enp-quiz-share__textarea enp-textarea enp-textarea--has-description--before" maxlength="255" name="enp_quiz[facebook_quote_end]" aria-describedby="enp-share__facebook-quote-end-description"><?php echo $quiz->get_facebook_quote_end();?></textarea>

                    </fieldset>

                    <fieldset class="enp-fieldset enp-quiz-share enp-quiz-share--twitter">
                        <legend class="enp-legend enp-quiz-share__legend">Twitter Share</legend>
                        <p  class="enp-input-description">After taking the quiz, what should someone's default tweet be?</p>

                        <label class="enp-label enp-quiz-share__label" for="enp-tweet-end">
                            Tweet
                        </label>
                        <p id="enp-share-twitter__description" class="enp-textarea-description enp-textarea-description--before">Use {{score_percentage}} to show someone's score.</p>
                        <textarea id="enp-tweet-end" class="enp-textarea enp-quiz-share__textarea enp-quiz-share__textarea--tweet enp-quiz-share__textarea--after" maxlength="255" name="enp_quiz[tweet_end]" aria-describedby="enp-share-twitter__description"><?php echo $quiz->get_tweet_end();?></textarea>


                    </fieldset>
                </fieldset>

                <button type="submit" class="enp-btn--submit enp-preview-form__submit" name="enp-quiz-submit" value="quiz-save">Save</button>

                <button type="submit" id="enp-btn--next-step" class="enp-btn--submit enp-btn--next-step enp-preview-form__submit--publish" name="enp-quiz-submit" value="quiz-publish"><?echo $enp_next_button_name;?> <svg class="enp-icon enp-icon--chevron-right enp-btn--next-step__icon enp-preview-form__submit__icon">
                  <use xlink:href="#icon-chevron-right" />
                </svg></button>
            </form>
        </section>

        <section class="enp-container enp-quiz-preview-container">
            <h2 class="enp-quiz-preview__title">Quiz Preview</h2>

            <script type="text/javascript" src="<?php echo ENP_QUIZ_PLUGIN_URL;?>public/quiz-take/js/dist/iframe-parent.js"></script>

            <iframe id="enp-quiz-iframe-<?php echo $quiz->get_quiz_id();?>" class="enp-quiz-iframe" src="<? echo ENP_QUIZ_URL.$quiz->get_quiz_id();?>" style="width: <? echo $quiz->get_quiz_width();?>; height: 500px;"></iframe>

        </section>
    </div>
</div>
