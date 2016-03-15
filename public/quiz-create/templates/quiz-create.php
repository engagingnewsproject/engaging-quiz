<?php
/**
 * The template for a user to create their quiz
 * and add questions to the quiz.
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>

<section class="enp-container enp-quiz-form-container">
    <?php
    include_once(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-breadcrumbs.php');
    ?>


    <form class="enp-form enp-quiz-form" method="post" action="<?php echo htmlentities(site_url('enp-quiz/quiz-create')); ?>">

        <fieldset class="enp-fieldset enp-quiz-title">
            <label class="enp-label enp-quiz-title__label" for="quiz-title">
                Quiz Title
            </label>
            <textarea class="enp-textarea enp-quiz-title__textarea" type="text" name="enp-quiz-title" placeholder="My Engaging Quiz Title"/></textarea>
        </fieldset>

        <?php
            $i = 0;
            include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-question.php');
            //<input type="hidden" name="save_type" value="insert" />
        ?>

        <button type="submit" class="enp-btn--add enp-quiz-form__add-question" name="enp-quiz-submit" value="add-question"><svg class="enp-icon enp-icon--add enp-add-question__icon">
          <use xlink:href="#icon-add" />
        </svg> Add Question</button>

        <button type="submit" class="enp-btn--submit enp-btn--next-step enp-quiz-form__submit" name="enp-quiz-submit" value="quiz-preview">Preview <svg class="enp-icon enp-icon--chevron-right enp-btn--next-step__icon enp-quiz-form__submit__icon">
          <use xlink:href="#icon-chevron-right" />
        </svg></button>


    </form>
</section>
