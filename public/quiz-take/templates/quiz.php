
<section class="enp-quiz__container">
    <header class="enp-quiz__header">
        <h3 class="enp-quiz__title"><?php echo (isset($_GET["quiz-title"]) ? $_GET['quiz-title'] : 'The Quiz Title You Entered');?></h3>
        <div class="enp-quiz__progress">
            <div class="enp-quiz__progress__bar">
                <div class="enp-quiz__progress__bar__question-count">1/2<span class="enp-"</div>
            </div>
        </div>
    </header>

    <section class="enp-question__container enp-question__container--unanswered">
        <form class="enp-question__form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
            <fieldset class="enp-question__fieldset">
                <legend class="enp-question__legend enp-question__question">Is this the first question of your quiz?</legend>
                <p class="enp-question__helper">Select one option.</p>

                <input id="enp-option__1" class="enp-option__input enp-option__input--radio enp-option__input--incorrect" type="radio" name="enp-options" />
                <label for="enp-option__1" class="enp-option__label">
                    Yes. That's it, for sure.
                </label>

                <input id="enp-option__2" class="enp-option__input enp-option__input--radio enp-option__input--correct" type="radio" name="enp-options" />
                <label for="enp-option__2" class="enp-option__label">
                    Definitely not. Someone should probably work on that.
                </label>
                <input id="enp-option__3" class="enp-option__input enp-option__input--radio enp-option__input--incorrect" type="radio" name="enp-options" id="enp-option__3"/>
                <label for="enp-option__3" class="enp-option__label">
                    Maybe? I can't remember.
                </label>

                <button class="enp-btn enp-options__submit enp-question__submit">Submit Answer <svg class="enp-icon enp-icon--chevron-right enp-options__submit__icon enp-question__submit__icon">
                  <use xlink:href="#icon-chevron-right" />
                </svg></button>
            </fieldset>


        </form>

        <section class="enp-explanation">
            <header class="enp-explanation__header">
                <h3 class="enp-explanation__title">
                    <span class="enp-explanation__title__text"></span>
                    <span class="enp-explanation__percentage"></span>
                 </h3>
            </header>
            <p class="enp-explanation__explanation">This is not what you entered. This is just a prototype to test the design. We'll make it actually work later.</p>

            <button class="enp-btn enp-next-step"><span class="enp-next-step__text">Next Question</span> <svg class="enp-icon enp-icon--chevron-right enp-next-step__icon">
              <use xlink:href="#icon-chevron-right" />
            </svg></button>
        </section>

    </section>





    <section class="enp-results">
        <div class="enp-results__score">
            <?php
                // calculate the score
                $score = 50; // or something...
                $r = 90;
                $c = M_PI*($r*2);

                $pct = ((100-$score)/100)*$c;
            ?>
            <h2 class="enp-results__score__title center">50<span class="enp-results__score__title__percentage">%</span> <span class="enp-screen-reader-text">Correct</span></h2>
            <svg class="enp-results__score__circle" width="200" height="200" viewPort="0 0 100 100" version="1.1" xmlns="http://www.w3.org/2000/svg">
              <circle class="enp-results__score__circle__bg" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
              <circle id="enp-results__score__circle__path" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="<?php echo $pct;?>"></circle>
            </svg>
        </div>
        <p class="enp-results__encouragement">Not bad!</p>
        <p class="enp-results__description">You did better than <strong>90%</strong> of people. That quiz was kind of a hard, eh?</p>
        <h3 class="enp-results__share-title">Share Your Results</h3>
        <ul class="enp-results__share">
            <li class="enp-results__share__item"><a class="enp-results__share__link enp-results__share__item--facebook" href="#facebook">
                <svg class="enp-icon enp-icon--facebook enp-results__share__item__icon enp-results__share__item__icon--facebook">
                  <use xlink:href="#icon-facebook" />
                </svg>
            </a></li>
            <li class="enp-results__share__item"><a class="enp-results__share__link enp-results__share__item--twitter" href="#twitter">
                <svg class="enp-icon enp-icon--twitter enp-results__share__item__icon enp-results__share__item__icon--twitter">
                  <use xlink:href="#icon-twitter" />
                </svg>
            </a></li>
            <li class="enp-results__share__item"><a class="enp-results__share__link enp-results__share__item--email" href="#email">
                <svg class="enp-icon enp-icon--mail enp-results__share__item__icon enp-results__share__item__icon--email">
                  <use xlink:href="#icon-mail" />
                </svg>
            </a></li>
        </ul>
    </section>
</section>

<script src="js/quiz.js"></script>