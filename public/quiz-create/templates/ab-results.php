<?php
/**
 * The template for viewing the results of an A/B Test
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>

<h1 class="enp-page-title enp-results-title enp-results-title--ab">
    <? if(isset($_GET['a-b-name']) &&  !empty($_GET['a-b-name'])) {
         echo $_GET['a-b-name'];
     } else {
         echo 'A/B Test';
     }
     ?>
</h1>
<section class="enp-container enp-results__container enp-results__container--ab">
    <section class="enp-results enp-results--ab enp-results--a enp-results--loser">
        <h2 class="enp-results-title enp-results-title--ab enp-results-title--a">
            How Much Do You Know?
        </h2>
        <div class="enp-container enp-results-flow__container enp-results-flow__container--ab enp-results-flow__container--a">
            <div class="enp-results-flow enp-results-flow--ab  enp-results-flow--a">
                <h2 class="enp-results-flow__section-title">Quiz User Flow</h2>
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
        </div>
    </section>
    <section class="enp-results enp-results--ab enp-results--b enp-results--winner">
        <h2 class="enp-results-title enp-results-title--ab enp-results-title--b">
            How Much Do You Know About the Conflict in Syria?
        </h2>
        <div class="enp-container enp-results-flow__container enp-results-flow__container--ab enp-results-flow__container--b">
            <div class="enp-results-flow enp-results-flow--ab  enp-results-flow--b">
                <h2 class="enp-results-flow__section-title ">Quiz User Flow</h2>
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
        </div>
    </section>
</section>
<section class="enp-container enp-ab-scores">
    <h2 class="enp-ab-scores__title">Quiz Scores</h2>
    <canvas id="enp-ab-scores__canvas" height="200" width="480"></canvas>
</section>
<section class="enp-ab-embed-code__section">
    <div class="enp-container enp-ab-embed-code">
        <h3 class="enp-ab-embed-code__title">Embed Code</h3>
        <textarea class="enp-embed-code enp-embed-code__textarea">Embed Code will go here.</textarea>
        <div class="enp-embed-code__instructions">
            <p>Here's some instructions on how to use the embed code</p>
        </div>
    </div>
</section>

<script src="js/Chart.min.js"></script>
<script src="js/a-b-results.js"></script>
