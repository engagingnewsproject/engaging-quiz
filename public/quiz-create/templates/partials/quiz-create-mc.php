<?
    if(!empty($mc_option_array[$mc_option_i])) {
        // will return the ID of the mc_option
        $mc_option_number = $mc_option_array[$mc_option_i];
    } else {
        $mc_option_number = 0;
    }
    $mc_option = new Enp_quiz_MC_option($mc_option_number);
?>
<fieldset class="enp-mc-options">
    <legend class="enp-legend enp-mc-options__legend">Multiple Choice Options</legend>
    <ul class="enp-mc-options__list">
        <? include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc-option.php');?>
        <li class="enp-mc-option enp-mc-option--add">
            <button class="enp-btn--add enp-mc-option__add" name="enp-quiz-submit" value="mc-option__add-option__question-<?echo $i;?>"><svg class="enp-icon enp-icon--add enp-mc-option__add__icon"><use xlink:href="#icon-add" /></svg> Add Another Option</button>
        </li>
    </ul>
</fieldset>
