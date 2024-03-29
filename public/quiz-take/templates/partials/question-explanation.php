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
    <div id="enp-explanation__explanation" class="enp-explanation__explanation"><?php echo $qt_question->question->get_question_explanation();?></div>

    <button class="enp-btn enp-next-step" name="enp-question-submit" value="enp-next-question"><span class="enp-next-step__text"><?php echo $qt_question->get_question_next_step_text();?></span> <svg class="enp-icon enp-icon--chevron-right enp-next-step__icon" aria-hidden="true" role="presentation" >
      <use xlink:href="#icon-chevron-right" />
    </svg></button>
</section>
