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
<?php echo $this->dashboard_breadcrumb_link();?>
<div class="enp-container enp-publish-page-container">
    <?php include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-breadcrumbs.php');?>
    <?php do_action('enp_quiz_display_messages'); ?>
    <div class="enp-flex enp-publish-page-flex-container">
        <section class="enp-container enp-publish-container">
            <h1 class="enp-page-title enp-publish-page__title">Embed</h1>
            <p>Copy and paste the embed code onto your website where you'd like it to appear.</p>
            <textarea class="enp-embed-code enp-publish-page__embed-code" rows="7"><script type="text/javascript" src="<?php echo ENP_QUIZ_PLUGIN_URL;?>public/quiz-take/js/dist/iframe-parent.js"></script>
<iframe id="enp-quiz-iframe-<?php echo $quiz->get_quiz_id();?>" class="enp-quiz-iframe" src="<?php echo ENP_QUIZ_URL.$quiz->get_quiz_id();?>" style="width: <? echo $quiz->get_quiz_width();?>; height: 500px;"></iframe>
            </textarea>

        </section>

        <section class="enp-container enp-aside-container enp-publish-page__aside-container">

            <?php include (ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/quiz-share.php');?>

            <aside class="enp-aside enp-ab-ad__container">
                <h3 class="enp-aside-title enp-ab-ad__title">A/B Test</h3>
                <p class="enp-ab-ad__description">Test two quizzes against each other to see which one is more engaging.</p>
                <a class="enp-btn enp-ab-ad__link" href="<?echo ENP_AB_TEST_URL;?>">New A/B Test</a>
            </aside>
        </section>
    </div>
</div>
