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

$quiz_id = $quiz->get_quiz_id();

 if(is_numeric($quiz_id) || is_int($quiz_id)) {
     $quiz_action_url = site_url('enp-quiz/quiz-create/').$quiz_id.'/';
 } else {
     $quiz_action_url = site_url('enp-quiz/quiz-create/new/');
 }
 if(empty($quiz_id))
 { $new_quiz_flag= '1'; } else { $new_quiz_flag= '0'; }
?>

<section class="enp-container enp-quiz-form-container">
    <?php

    include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-breadcrumbs.php');
    ?>

    <?php do_action('enp_quiz_display_messages'); ?>

    <form id="enp-quiz-create-form" class="enp-form enp-quiz-form" enctype="multipart/form-data" method="post" action="<?php echo htmlentities($quiz_action_url); ?>">
        <?php $enp_quiz_nonce->outputKey();?>
        <input id="enp-quiz-id" type="hidden" name="enp_quiz[quiz_id]" value="<?php echo $quiz_id; ?>" />

        <input id="enp-quiz-new" type="hidden" name="enp_quiz[new_quiz]" value="<?php echo $new_quiz_flag;?>" />

        <fieldset class="enp-fieldset enp-quiz-title">
            <label class="enp-label enp-quiz-title__label" for="quiz-title">
                Quiz Title
            </label>
            <textarea class="enp-textarea enp-quiz-title__textarea" type="text" name="enp_quiz[quiz_title]" maxlength="255" placeholder="My Engaging Quiz Title"/><? echo $quiz->get_value('quiz_title') ?></textarea>
        </fieldset>

        <?php
            $question_i = 0;
            // count the number of questions
            $question_ids = $quiz->get_questions();
            // a little hack-ey, but we're only using this as a JS template
            // so it will run the loop once (or again) so we have access to it
            $question_ids[] = 'questionTemplateID';
            foreach($question_ids as $question_id) {
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
