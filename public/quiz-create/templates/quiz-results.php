<?php
/**
 * The template for viewing the results of a quiz
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>
<a class="enp-breadcrumb-link" href="<?php echo ENP_QUIZ_DASHBOARD_URL;?>/user">Dashboard</a>
<h1 class="enp-page-title enp-results-title"><?php echo $quiz->get_quiz_title();?></h1>
<section class="enp-container enp-results__container">
    <section class="enp-container enp-results-flow__container">
        <?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/quiz-results-flow.php');?>
    </section>
    <!--<section class="enp-container enp-quiz-scores__container">
        <div class="enp-quiz-scores">
            <canvas id="enp-quiz-scores__canvas" height="300" width="480">
                <h1>Quiz Scores - Put Screen Reader Line Chart Data here</h1>
            </canvas>
        </div>
    </section>-->
</section>

<?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/question-results-section.php');?>

<?php get_footer(); ?>
