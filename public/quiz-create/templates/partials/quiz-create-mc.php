<?
    // set a counter
    $mc_option_i = 0;
    // count the number of mc_options
    $mc_option_array = $question->get_mc_options();
    // how many do we need to add?
    $mc_option_count = count($mc_option_array);
    // see if the user wanted to add a new one
    if($user_action['action'] === 'add' && $user_action['element'] === 'mc_option' && $user_action['details']['question'] === $question_i) {
        // if we're adding a new mc_option, add one to the count so we have an extra (empty) mc_option to loop through
        $mc_option_count++;
    }
?>
<fieldset class="enp-mc-options">
    <legend class="enp-legend enp-mc-options__legend">Multiple Choice Options</legend>
    <ul class="enp-mc-options__list">
        <?
        // even if it's zero, a do loop will do the loop once before checking for condition
        do {
            if(!empty($mc_option_array[$mc_option_i])) {
                // will return the ID of the mc_option
                $mc_option_number = $mc_option_array[$mc_option_i];
            } else {
                $mc_option_number = 0;
            }
            $mc_option = new Enp_quiz_MC_option($mc_option_number);
            include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc-option.php');
            $mc_option_i++;
        } while($mc_option_i < $mc_option_count);
        ?>
        <li class="enp-mc-option enp-mc-option--add">
            <button class="enp-btn--add enp-mc-option__add" name="enp-quiz-submit" value="add-mc-option__question-<?echo $question_i;?>"><svg class="enp-icon enp-icon--add enp-mc-option__add__icon"><use xlink:href="#icon-add" /></svg> Add Another Option</button>
        </li>
    </ul>
</fieldset>
