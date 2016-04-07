<?
    // set a counter
    $mc_option_i = 0;
    // count the number of mc_options
    $mc_option_ids = $question->get_mc_options();
    // a little hack-ey, but we're only using this as a JS template
    // so it will run the loop once (or again) so we have access to it
    if($question_id === 'questionTemplateID') {
        // if it's our template, add in the fake one
        $mc_option_ids[] = 'mcOptionTemplateID';
    }

?>
<fieldset class="enp-mc-options">
    <legend class="enp-legend enp-mc-options__legend">Multiple Choice Options</legend>
    <ul class="enp-mc-options__list">
        <?php
        // even if it's zero, a do loop will do the loop once before checking for condition
        foreach($mc_option_ids as $mc_option_id) {
            // will return the ID of the mc_option
            $mc_option = new Enp_quiz_MC_option($mc_option_id);
            include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc-option.php');
            $mc_option_i++;
        }
        ?>
        <li class="enp-mc-option enp-mc-option--add">
            <button class="enp-btn--add enp-quiz-submit enp-mc-option__add" name="enp-quiz-submit" value="add-mc-option__question-<?echo $question_id;?>"><svg class="enp-icon enp-icon--add enp-mc-option__add__icon"><use xlink:href="#icon-add" /></svg> Add Another Option</button>
        </li>
    </ul>
</fieldset>
