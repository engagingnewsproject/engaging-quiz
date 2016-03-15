<?php
/**
 * The template for a user to preview and test their quiz before
 * publishing it.
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>

<div class="enp-container enp-preview-page-container">
    <?php include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-breadcrumbs.php');?>
    <div class="enp-flex enp-preview-page-flex-container">
        <section class="enp-container enp-quiz-settings-container">
            <h1 class="enp-quiz-settings__title">Quiz Settings</h1>
            <form class="enp-form enp-quiz-settings__form" action="<?php echo htmlentities(site_url('enp-quiz/quiz-preview')); ?>">
                <fieldset class="enp-fieldset enp-title-display">
                    <legend class="enp-legend enp-title-display__legend">Title Display</legend>
                    <input id="enp-quiz-title-show" class="enp-radio enp-title-display__input enp-title-display__input--title-display" type="radio" name="title-display" value="title-show" checked="checked"/>
                    <label class="enp-label enp-title-display__label enp-title-display__label--title-display" for="enp-quiz-title-show">
                        Show Title
                    </label>
                    <input class="enp-radio enp-title-display__input enp-title-display__input--hide-title" id="enp-quiz-title-hide" type="radio" name="title-display" value="title-hide"/>
                    <label class="enp-label enp-title-display__label enp-title-display__label--hide-title" for="enp-quiz-title-hide">
                        Hide Title
                    </label>
                </fieldset>

                <fieldset class="enp-fieldset enp-quiz-bg-color">
                    <legend class="enp-legend enp-quiz-bg-color__legend">Background Color</legend>
                    <input id="white" class="enp-radio enp-quiz-bg-color__input enp-quiz-bg-color__input--white" type="radio" name="enp-quiz-bg-color" value="#ffffff" checked="checked"/>
                    <label class="enp-label enp-quiz-bg-color__label enp-quiz-bg-color__label--white" for="white">
                        White
                    </label>
                </fieldset>

                <fieldset class="enp-fieldset enp-quiz-text-color">
                    <legend class="enp-legend enp-quiz-text-color__legend">Text Color</legend>
                    <input id="black" class="enp-radio enp-quiz-text-color__input enp-quiz-text-color__input--black" type="radio" name="enp-quiz-text-color" value="title" checked="checked"/>
                    <label class="enp-label enp-quiz-text-color__label enp-quiz-text-color__label--black" for="black">
                        Black
                    </label>
                </fieldset>

                <button type="submit" class="enp-btn--submit enp-btn--next-step enp-preview-form__submit">Publish <svg class="enp-icon enp-icon--chevron-right enp-btn--next-step__icon enp-preview-form__submit__icon">
                  <use xlink:href="#icon-chevron-right" />
                </svg></button>
            </form>
        </section>

        <section class="enp-container enp-quiz-preview-container">
            <h2 class="enp-quiz-preview__title">Quiz Preview</h2>

            <?php include_once(ENP_QUIZ_TAKE_TEMPLATES_PATH.'/quiz.php');?>

        </section>
    </div>
</div>
