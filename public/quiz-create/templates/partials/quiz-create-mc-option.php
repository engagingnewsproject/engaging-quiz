<li class="enp-mc-option">
    <button class="enp-mc-option__button enp-mc-option__button--correct"  name="enp-quiz-submit" value="mc-option--correct__question-<?echo $i;?>__mc-option-<?echo $mc_option_i;?>">
        <svg class="enp-icon enp-icon--check enp-mc-option__icon enp-mc-option__icon--correct"><use xlink:href="#icon-check" /></svg>
    </button>
    <input type="text" class="enp-input enp-mc-option__input" name="enp_question[<?echo $i;?>]['mc_options'][<?echo $mc_option_i;?>]" placeholder="It's one of the great mysteries of the universe."/>
    <button class="enp-mc-option__button enp-mc-option__button--delete" name="enp-quiz-submit" value="mc-option--delete">
        <svg class="enp-icon enp-icon--delete enp-mc-option__icon enp-mc-option__icon--delete__question-<?echo $i;?>__mc-option-<?echo $mc_option_i;?>"><use xlink:href="#icon-delete" /></svg>
    </button>
</li>
