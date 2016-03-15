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
            <textarea class="enp-textarea enp-quiz-title__textarea" type="text" name="enp-quiz-title" placeholder="My Engaging Quiz Title"/><? echo (isset($_GET['enp-quiz-title']) ? $_GET['enp-quiz-title'] : '');?></textarea>
        </fieldset>

        <section class="enp-question-content">
            <div class="enp-question-inner enp-question">
                <label class="enp-label enp-question-title__label" for="question-title">
                    Question
                </label>
                <textarea class="enp-textarea enp-question-title__textarea" name="enp-question-title" placeholder="Why can't we tickle ourselves?"/></textarea>

                <label for="enp-image-upload" class="enp-btn--add enp-image-upload"><svg class="enp-icon enp-icon--photo enp-image-upload__icon--photo">
                    <use xlink:href="#icon-photo" />
                </svg>
                <svg class="enp-icon enp-icon--add enp-image-upload__icon--add">
                    <use xlink:href="#icon-add" />
                </svg> Add Image</label>
                <input type="file" class="enp-image-upload__input" name="enp-image-upload">


                <h4 class="enp-legend enp-question-type__legend">Question Type</h4>

                <input type="radio" id="enp-question-type__mc" class="enp-radio enp-question-type__input enp-question-type__input--mc" name="enp-question-type" value="mc" checked="checked">
                <label class="enp-label enp-question-type__label enp-question-type__label--mc" for="enp-question-type__mc">Multiple Choice</label>

                <input type="radio" id="enp-question-type__slider" class="enp-radio enp-question-type__input enp-question-type__input--slider" name="enp-question-type" value="slider">
                <label class="enp-label enp-question-type__label enp-question-type__label--slider" for="enp-question-type__slider">Slider</label>


                <fieldset class="enp-mc-options">
                    <legend class="enp-legend enp-mc-options__legend">Multiple Choice Options</legend>
                    <ul class="enp-mc-options__list">
                        <li class="enp-mc-option">
                            <button class="enp-mc-option__button enp-mc-option__button--correct"  name="enp-quiz-submit" value="mc-option--correct">
                                <svg class="enp-icon enp-icon--check enp-mc-option__icon enp-mc-option__icon--correct"><use xlink:href="#icon-check" /></svg>
                            </button>
                            <input type="text" class="enp-input enp-mc-option__input" name="enp-mc-option__input" placeholder="It's one of the great mysteries of the universe."/>
                            <button class="enp-mc-option__button enp-mc-option__button--delete" name="enp-quiz-submit" value="mc-option--delete">
                                <svg class="enp-icon enp-icon--delete enp-mc-option__icon enp-mc-option__icon--delete"><use xlink:href="#icon-delete" /></svg>
                            </button>
                        </li>
                        <li class="enp-mc-option">
                            <button class="enp-mc-option__button enp-mc-option__button--correct" name="enp-quiz-submit" value="mc-option--correct">
                                <svg class="enp-icon enp-icon--check enp-mc-option__icon enp-mc-option__icon--correct"><use xlink:href="#icon-check" /></svg>
                            </button>
                            <input type="text" class="enp-input enp-mc-option__input" name="enp-mc-option__input" placeholder="Your cerebellum can predict your own movements."/>
                            <button class="enp-mc-option__button enp-mc-option__button--delete" name="enp-quiz-submit" value="mc-option--delete">
                                <svg class="enp-icon enp-icon--delete enp-mc-option__icon enp-mc-option__icon--delete"><use xlink:href="#icon-delete" /></svg>
                            </button>
                        </li>
                        <li class="enp-mc-option">
                            <button class="enp-mc-option__button enp-mc-option__button--correct" name="enp-quiz-submit" value="mc-option--correct">
                                <svg class="enp-icon enp-icon--check enp-mc-option__icon enp-mc-option__icon--correct"><use xlink:href="#icon-check" /></svg>
                            </button>
                            <input type="text" class="enp-input enp-mc-option__input" name="enp-mc-option__input" placeholder="I think I'm just bad at tickling."/>
                            <button class="enp-mc-option__button enp-mc-option__button--delete" name="enp-quiz-submit" value="mc-option--delete">
                                <svg class="enp-icon enp-icon--delete enp-mc-option__icon enp-mc-option__icon--delete"><use xlink:href="#icon-delete" /></svg>
                            </button>
                        </li>
                        <li class="enp-mc-option enp-mc-option--add">
                            <button class="enp-btn--add enp-mc-option__add" name="enp-quiz-submit" value="mc-option__add"><svg class="enp-icon enp-icon--add enp-mc-option__add__icon"><use xlink:href="#icon-add" /></svg> Add Another Option</button>
                        </li>
                    </ul>
                </fieldset>

                <fieldset class="enp-slider-options">
                    <legend class="enp-legend enp-mc-options__legend">Slider Options</legend>
                    <h1>Slider stuff!</h1>
                </fieldset>
            </div>

            <div class="enp-question-inner enp-answer-explanation">
                <fieldset class="enp-fieldset enp-answer-explanation__fieldset">
                    <label class="enp-label enp-answer-explanation__label">Answer Explanation</label>
                    <textarea class="enp-textarea enp-answer-explanation__textarea" placeholder="Your cerebellum can predict your own actions, so you're unable to 'surprise' yourself with a tickle."></textarea>
                </fieldset>
            </div>
        </section>

        <?php
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
