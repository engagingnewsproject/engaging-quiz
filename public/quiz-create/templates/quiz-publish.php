<?php

/**
 * The template for the "publish" page when a user
 * finishes creating a quiz. This also has the embed code
 * on it.
 *
 * @since             0.0.1
 * @package           Enp_quiz
 * Data available to this view:
 * $quiz = quiz object (if exits), error page if it doesn't (TODO)
 */

?>
<aside class="enp-dash__section-aside">
    <?php echo $this->dashboard_breadcrumb_link(); ?>

    <section class="enp-container enp-aside-container enp-publish-page__aside-container">
        <?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH . 'partials/quiz-share.php'); ?>
        <textarea id="enp-quiz-feedback" class="enp-textarea enp-quiz-feedback__textarea enp-textarea" maxlength="140" name="enp_quiz[quiz_feedback]" placeholder="We would love to hear from you!"></textarea>
        <button type="submit" class="enp-btn--submit enp-preview-form__submit" name="enp-quiz-submit" value="quiz-save">Save</button>
    </section>
</aside>

<article class="enp-container enp-dash-container">
    <div class="enp-container enp-publish-page-container">
        <?php include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH . '/partials/quiz-create-breadcrumbs.php'); ?>
        <?php do_action('enp_quiz_display_messages'); ?>
        <div class="enp-flex enp-publish-page-flex-container">
            <section class="enp-container enp-publish-container">
                <h1 class="enp-page-title enp-publish-page__title">Embed</h1>
                <?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH . 'partials/quiz-embed-code.php'); ?>
                <aside class="enp-aside enp-ab-ad__container">
                    <h3 class="enp-aside__title enp-ab-ad__title">A/B Test</h3>
                    <p class="enp-ab-ad__description">Test two quizzes against each other to see which one is more engaging.</p>
                    <a class="enp-btn enp-ab-ad__link" href="<? echo ENP_AB_TEST_URL; ?>new">New A/B Test</a>
                </aside>
            </section>
        </div>
    </div>
</article>