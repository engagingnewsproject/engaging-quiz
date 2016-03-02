<?php
/**
 * The template for a user to create a new A/B Test
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */

get_header(); ?>

<div class="enp-container enp-ab-create__container">
    <h1 class="enp-screen-reader-text enp-page-title enp-ab-create__page-title">Create A/B Test</h1>
    <form class="enp-form enp-ab-create__form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
        <fieldset class="enp-fieldset enp-ab-create-title">
            <label class="enp-label enp-ab-create__label enp-ab-create-title__label" for="enp-ab-name">
                A/B Test Name
            </label>
            <textarea id="enp-ab-name" class="enp-textarea enp-ab-create-title__textarea" name="enp-ab-name" placeholder="Name your A/B Test"/></textarea>
        </fieldset>
        <fieldset class="enp-fieldset enp-ab-create-quiz-a">
            <label class="enp-label enp-ab-create__label enp-ab-create-quiz-a__label" for="quiz-a">Select Quiz A</label>
            <select class="enp-select enp-ab-create__select enp-ab-create-quiz-a__select" id="quiz-a">
                <option>How Much Do You Know About Syria?</option>
                <option>How Much Do You Know About the Conflict in Syria?</option>
            </select>
        </fieldset>
        <fieldset class="enp-fieldset enp-ab-create-quiz-b">
            <label class="enp-label enp-ab-create__label enp-ab-create-quiz-b__label" for="quiz-b">Select Quiz B</label>
            <select class="enp-select enp-ab-create__select enp-ab-create-quiz-b__select" id="quiz-b">
                <option>How Much Do You Know About Syria?</option>
                <option>How Much Do You Know About the Conflict in Syria?</option>
            </select>
        </fieldset>

        <button class="enp-btn enp-ab-create__submit" type="submit"/>Create A/B Test</button>

    </form>
</div>


<?php get_footer(); ?>
