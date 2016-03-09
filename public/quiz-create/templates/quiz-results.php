<?php
/**
 * The template for viewing the results of a quiz
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>

<h1 class="enp-page-title enp-results-title">How Much Do You Know About Syria?</h1>
<section class="enp-container enp-results__container">

    <section class="enp-container enp-results-flow__container">
        <div class="enp-results-flow">
            <h2 class="enp-screen-reader-text">Quiz User Flow</h2>
            <div class="enp-results-flow__item enp-results-flow__item--total-views">
                <h3 class="enp-results-flow__title enp-results-flow__title--total-views">Total Views</h3>
                <div class="enp-results-flow__number enp-results-flow__number--total-views">1250</div>
            </div>
            <div class="enp-results-flow__item enp-results-flow__item--quiz-starts">
                <h3 class="enp-results-flow__title enp-results-flow__title--quiz-starts">Quiz Starts</h3>
                <div class="enp-results-flow__number enp-results-flow__number--quiz-starts">675</div>
                <div class="enp-results-flow__percentage enp-results-flow__percentage--quiz-starts">50</div>
            </div>
            <div class="enp-results-flow__item enp-results-flow__item--quiz-completions">
                <h3 class="enp-results-flow__title enp-results-flow__title--quiz-completions">Completions</h3>
                <div class="enp-results-flow__number enp-results-flow__number--quiz-completions">127</div>
                <div class="enp-results-flow__percentage enp-results-flow__percentage--quiz-completions">10.1</div>
            </div>
        </div>
    </section>
    <section class="enp-container enp-quiz-scores__container">
        <div class="enp-quiz-scores">
            <canvas id="enp-quiz-scores__canvas" height="300" width="480">
                <h1>Quiz Scores - Put Screen Reader Line Chart Data here</h1>
            </canvas>
        </div>
    </section>
</section>

<section class="enp-results-questions__section">
    <div class="enp-container enp-results-questions__container">
        <?php include 'template/question-results.php';?>
    </div>
</section>
<script src="js/Chart.min.js"></script>
<script src="js/quiz-results.js"></script>

<?php get_footer(); ?>
