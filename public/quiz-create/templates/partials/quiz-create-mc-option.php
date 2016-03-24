<li class="enp-mc-option">
    <button class="enp-mc-option__button enp-mc-option__button--correct"  name="enp-quiz-submit" value="mc-option--correct__question-<?echo $question_i;?>__mc-option-<?echo $mc_option->get_mc_option_id();?>">
        <svg class="enp-icon enp-icon--check enp-mc-option__icon enp-mc-option__icon--correct"><use xlink:href="#icon-check" /></svg>
    </button>
    <input type="hidden" name="enp_question[<?echo $question_i;?>][mc_option][<?echo $mc_option_i;?>][mc_option_id]" value="<? echo $mc_option->get_mc_option_id();?>" />
    <input type="text" class="enp-input enp-mc-option__input" name="enp_question[<?echo $question_i;?>][mc_option][<?echo $mc_option_i;?>][mc_option_content]" maxlength="255" placeholder="It's one of the great mysteries of the universe." value="<?echo $mc_option->get_mc_option_content();?>"/>
    <button class="enp-mc-option__button enp-mc-option__button--delete" name="enp-quiz-submit" value="mc-option--delete">
        <svg class="enp-icon enp-icon--delete enp-mc-option__icon enp-mc-option__icon--delete__question-<?echo $question_i;?>__mc-option-<?echo  $mc_option->get_mc_option_id();?>"><use xlink:href="#icon-delete" /></svg>
    </button>
</li>
