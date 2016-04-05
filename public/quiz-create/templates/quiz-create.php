<?php
/**
 * The template for a user to create their quiz
 * and add questions to the quiz.
 *
 * @since             0.0.1
 * @package           Enp_quiz
 *
 * Data available to this view:
 * $quiz = quiz object (if exits), false if new quiz
 *  // example user actions
 *  $user_action = array(
 *                      'action' =>'add',
 *                      'element' => 'mc_option',
 *                      'details' => array(
 *                        'question' => '1',
 *                      ),
 *                  );
 *
 */
// var_dump($quiz);
// var_dump($user_action);
//

 if(is_numeric($quiz->get_quiz_id()) || is_int($quiz->get_quiz_id())) {
     $quiz_action_url = site_url('enp-quiz/quiz-create/').$quiz->get_quiz_id().'/';
 } else {
     $quiz_action_url = site_url('enp-quiz/quiz-create/new/');
 }
?>

<section class="enp-container enp-quiz-form-container">
    <?php

    include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-breadcrumbs.php');
    ?>

    <?php do_action('enp_quiz_display_messages'); ?>

    <form class="enp-form enp-quiz-form" enctype="multipart/form-data" method="post" action="<?php echo htmlentities($quiz_action_url); ?>">
        <?php $enp_quiz_nonce->outputKey();?>
        <input type="hidden" name="enp_quiz[quiz_id]" value="<? echo $quiz->get_quiz_id(); ?>" />

        <fieldset class="enp-fieldset enp-quiz-title">
            <label class="enp-label enp-quiz-title__label" for="quiz-title">
                Quiz Title
            </label>
            <textarea class="enp-textarea enp-quiz-title__textarea" type="text" name="enp_quiz[quiz_title]" maxlength="255" placeholder="My Engaging Quiz Title"/><? echo $quiz->get_value('quiz_title') ?></textarea>
        </fieldset>

        <?php
            $question_i = 0;
            // count the number of questions
            $question_array = $quiz->get_questions();
            $question_count = count($question_array);
            // even if it's zero, a do loop will do the loop once before checking for condition
             while($question_i < $question_count) {
                include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-question.php');
                $question_i++;
            }
        ?>

        <button type="submit" class="enp-btn--add enp-quiz-submit enp-quiz-form__add-question" name="enp-quiz-submit" value="add-question"><svg class="enp-icon enp-icon--add enp-add-question__icon">
          <use xlink:href="#icon-add" />
        </svg> Add Question</button>

        <button type="submit" class="enp-btn--submit enp-quiz-submit enp-btn--next-step enp-quiz-form__submit" name="enp-quiz-submit" value="quiz-preview">Preview <svg class="enp-icon enp-icon--chevron-right enp-btn--next-step__icon enp-quiz-form__submit__icon">
          <use xlink:href="#icon-chevron-right" />
        </svg></button>


    </form>
</section>
