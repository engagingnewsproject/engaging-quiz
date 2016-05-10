<?php
/**
 * The template for viewing the results of an A/B Test
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>

<h1 class="enp-page-title enp-results-title enp-results-title--ab"><?php echo $ab_test->get_ab_test_title();?></h1>
<section class="enp-container enp-results__container enp-results__container--ab">
    <section class="enp-results enp-results--ab enp-results--a enp-results--loser">
        <h2 class="enp-results-title enp-results-title--ab enp-results-title--a">
            <?php echo $quiz_a->get_quiz_title();?>
        </h2>
        <section class="enp-results-flow__container">
            <?php
            $quiz = $quiz_a;
            include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/quiz-results-flow.php');?>
        </section>

    </section>
    <section class="enp-results enp-results--ab enp-results--b enp-results--winner">
        <h2 class="enp-results-title enp-results-title--ab enp-results-title--b">
            <?php echo $quiz_b->get_quiz_title();?>
        </h2>
        <section class="enp-results-flow__container">
            <?php
            $quiz = $quiz_b;
            include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/quiz-results-flow.php');?>
        </section>

    </section>
</section>
<section class="enp-container enp-results__container enp-results__container--ab">
    <section class="enp-results enp-results--ab enp-results--a enp-results--loser">
        <?php
        $quiz = $quiz_a; include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/question-results-section.php');?>
    </section>

    <section class="enp-results enp-results--ab enp-results--b enp-results--winner">
        <?php
        $quiz = $quiz_b; include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/question-results-section.php');?>
    </section>
</section>



<!-- SECTION FOR CHART AFTER WE HAVE PLOT OF SCORES
<section class="enp-container enp-ab-scores">
    <h2 class="enp-ab-scores__title">Quiz Scores</h2>
    <canvas id="enp-ab-scores__canvas" height="200" width="480"></canvas>
</section>-->
<section id="enp-ab-embed-code" class="enp-ab-embed-code__section">
    <div class="enp-container enp-ab-embed-code">
        <h3 class="enp-ab-embed-code__title">Embed Code</h3>
        <textarea class="enp-embed-code enp-embed-code__textarea" rows="7"><script type="text/javascript" src="<?php echo ENP_QUIZ_PLUGIN_URL;?>public/quiz-take/js/dist/iframe-parent.js"></script>
<iframe id="enp-quiz-iframe-<?php echo $ab_test->get_ab_test_id();?>" class="enp-quiz-iframe" src="<?php echo ENP_TAKE_AB_TEST_URL.$ab_test->get_ab_test_id();?>" style="width: <?php echo $quiz_a->get_quiz_width();?>; height: 500px;"></iframe></textarea>
        <div class="enp-embed-code__instructions">
            <p>Here's some instructions on how to use the embed code</p>
        </div>
    </div>
</section>
