<li class="enp-results-question">
    <div class="enp-results-question__header">
        <h3 class="enp-results-question__question"><?php echo $question->get_question_title();?></h3>
        <div class="enp-results-question__stats">
            <span class="enp-results-question__views"><?php echo $question->get_question_views();?></span>&nbsp;/&nbsp;<span class="enp-results-question__completion-rate"><?php echo $this->percentagize($question->get_question_responses(), $question->get_question_views(), 0);?>%</span>
        </div>
    </div>
    <div class="enp-results-question__content">
        <ul class="enp-results-question__deep-stats">
            <li class="enp-results-question__deep-stat enp-results-question__deep-stat--correct">
                <span class="enp-results-question__deep-stat__number enp-results-question__deep-stat__number--correct"><?php echo  $question->get_question_responses_correct_percentage();?></span>
                <div class="enp-results-question__deep-stat__label">Correct</div>
            </li>
            <li class="enp-results-question__deep-stat enp-results-question__deep-stat--incorrect">
                <span class="enp-results-question__deep-stat__number enp-results-question__deep-stat__number--incorrect"><?php echo  $question->get_question_responses_incorrect_percentage();?></span>
                <div class="enp-results-question__deep-stat__label">Incorrect</div>
            </li>
            <li class="enp-results-question__deep-stat enp-results-question__deep-stat--views">
                <span class="enp-results-question__deep-stat__number enp-results-question__deep-stat__number--views"><?php echo $question->get_question_views();?></span>
                <div class="enp-results-question__deep-stat__label">Views</div>
            </li>
            <li class="enp-results-question__deep-stat enp-results-question__deep-stat--responses">
                <span class="enp-results-question__deep-stat__number enp-results-question__deep-stat__number--responses"><?php echo $question->get_question_responses();?></span>
                <div class="enp-results-question__deep-stat__label">Responses</div>
            </li>
        </ul>
        <ul class="enp-results-question__options">
            <?php
                $question_type = $question->get_question_type();
                if($question_type === 'mc') {
                    $mc_option_ids = $question->get_mc_options();
                    foreach($mc_option_ids as $mc_option_id) {
                        // IF AB Test, we need to return AB Test MC Option data instead
                        if(isset($ab_test) && !empty($ab_test->ab_test_id)) {
                            $mc_option = new Enp_quiz_MC_option_AB_test_result($mc_option_id, $ab_test->get_ab_test_id());
                        } else {
                            $mc_option = new Enp_quiz_MC_option($mc_option_id);
                        }
                        include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/question-results-mc-option.php');
                    }
                }

            ?>

        </ul>
    </div>
</li>
