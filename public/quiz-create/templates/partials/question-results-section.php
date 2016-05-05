<section class="enp-results-questions__section">
    <div class="enp-container enp-results-questions__container">
        <header class="enp-results-questions__header">
            <h2 class="enp-results-questions__title">Question Results</h2>
            <div class="enp-results-questions__key">
                <span class="enp-results-questions__views">Views</span>&nbsp;/&nbsp;<span class="enp-results-questions__completion">Completion&nbsp;%</span>
            </div>
        </header>
        <ul class="enp-results-questions">
            <?php
                $question_ids = $quiz->get_questions();
                foreach($question_ids as $question_id) {
                    $question = new Enp_quiz_Question($question_id);
                    include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/question-results.php');
                }


            ?>
        </ul>
    </div>
</section>
