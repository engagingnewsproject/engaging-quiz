<?php // start our current URL class
$current_url = new Enp_quiz_Current_URL();?>
<section id="enp-explanation_<?php echo $qt_question->question->get_question_id();?>" class="enp-explanation enp-explanation--<?php echo $qt_question->get_question_explanation_title();?>"
    aria-labelledby="enp-explanation__title"
    aria-describedby="enp-explanation__explanation">

    <header class="enp-explanation__header">
        <h3 id="enp-explanation__title" class="enp-explanation__title">
            <span class="enp-explanation__title__text"><?php echo $qt_question->get_question_explanation_title();?></span>
            <span class="enp-explanation__percentage"><?php echo $qt_question->get_question_explanation_percentage();?></span>
         </h3>
    </header>
    <p id="enp-explanation__explanation" class="enp-explanation__explanation"><?php echo $qt_question->question->get_question_explanation();?></p>

    <button class="enp-btn enp-next-step" name="enp-question-submit" value="enp-next-question"><span class="enp-next-step__text"><?php echo $qt_question->get_question_next_step_text();?></span> <svg class="enp-icon enp-icon--chevron-right enp-next-step__icon" aria-hidden="true" role="presentation" >
      <use xlink:href="#icon-chevron-right" />
    </svg></button>
</section>
<footer class="enp-callout enp-callout--quiz-answer">Powered by the <a href="<?php echo $current_url->get_root();?>/quiz-creator/?iframe_referral=true&amp;quiz_state=question_explanation&amp;quiz_id=<?php echo $qt_question->qt->quiz->get_quiz_id();?>&amp;question_id=<?php echo $qt_question->question->get_question_id();?>" target="_blank">Engaging News Project<span class="enp-screen-reader-text"> Link opens in a new window</span></a></footer>
