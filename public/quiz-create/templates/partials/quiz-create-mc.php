<?
    // set a counter
    $mc_option_i = 0;
    // count the number of mc_options
    $mc_option_array = $question->get_mc_options();
    // how many do we need to add?
    $mc_option_count = count($mc_option_array);
?>
<fieldset class="enp-mc-options">
    <legend class="enp-legend enp-mc-options__legend">Multiple Choice Options</legend>
    <ul class="enp-mc-options__list">
        <?
        // even if it's zero, a do loop will do the loop once before checking for condition
        while($mc_option_i < $mc_option_count) {
            // will return the ID of the mc_option
            $mc_option_id = $mc_option_array[$mc_option_i];
            $mc_option = new Enp_quiz_MC_option($mc_option_id);
            include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc-option.php');
            $mc_option_i++;
        }
        ?>
        <li class="enp-mc-option enp-mc-option--add">
            <button class="enp-btn--add enp-mc-option__add" name="enp-quiz-submit" value="add-mc-option__question-<?echo $question_i;?>"><svg class="enp-icon enp-icon--add enp-mc-option__add__icon"><use xlink:href="#icon-add" /></svg> Add Another Option</button>
        </li>
    </ul>
</fieldset>
