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
$quiz_status = $quiz->get_quiz_status();
?>
<?php echo $Quiz_create->dashboard_breadcrumb_link();?>
<section class="enp-container enp-quiz-form-container js-enp-quiz-create-form-container">
    <?php include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-breadcrumbs.php');?>

    <?php do_action('enp_quiz_display_messages'); ?>

    <form id="enp-quiz-create-form" class="enp-form enp-quiz-form" enctype="multipart/form-data" method="post" action="<?php echo $Quiz_create->get_quiz_action_url(); ?>" novalidate>
        <?php
        $enp_quiz_nonce->outputKey();
        echo $Quiz_create->hidden_fields();?>

        <fieldset class="enp-fieldset enp-quiz-title">
            <label class="enp-label enp-quiz-title__label" for="quiz-title">
                Quiz Title
            </label>
            <textarea id="quiz-title" class="enp-textarea enp-quiz-title__textarea" type="text" name="enp_quiz[quiz_title]" maxlength="255" placeholder="My Engaging Quiz Title"/><? echo $quiz->get_value('quiz_title') ?></textarea>
        </fieldset>

        <?php
            $question_i = 0;
            // count the number of questions
            $question_ids = $quiz->get_questions();
            if(!empty($question_ids)){
                foreach($question_ids as $question_id) {
                    include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-question.php');
                    $question_i++;
                }
            }
        ?>

        <?php if($quiz_status !== 'published') {?>
            <button type="submit" class="enp-btn--add enp-quiz-submit enp-quiz-form__add-question" name="enp-quiz-submit" value="add-question"><svg class="enp-icon enp-icon--add enp-add-question__icon" role="presentation" aria-hidden="true">
              <use xlink:href="#icon-add" />
            </svg> Add Question</button>
        <?php } ?>


        <button type="submit" class="enp-btn--save enp-quiz-submit enp-quiz-form__save" name="enp-quiz-submit" value="save">Save</button>

        <button type="submit" id="enp-btn--next-step" class="enp-btn--submit enp-quiz-submit enp-btn--next-step enp-quiz-form__submit" name="enp-quiz-submit" value="quiz-preview"><?php echo ($quiz_status !== 'published' ? 'Preview' : 'Settings');?> <svg class="enp-icon enp-icon--chevron-right enp-btn--next-step__icon enp-quiz-form__submit__icon">
          <use xlink:href="#icon-chevron-right" />
        </svg></button>

    </form>
</section>
