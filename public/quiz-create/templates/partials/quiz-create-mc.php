<?php
    // set a counter
    $mc_option_i = 0;
    // count the number of mc_options
    $mc_option_ids = $question->get_mc_options();
    $mc_option_image = $mc_option_ids->get_mc_option_image();
?>
<fieldset class="enp-mc-options">
    <legend class="enp-legend enp-mc-options__legend">Multiple Choice Options</legend>
    <?php var_dump($mc_option_image_alt); die(); ?>
    <ul class="enp-mc-options__list">
        <?php
        if(!empty($mc_option_ids)) {
            foreach($mc_option_ids as $mc_option_id) {

                include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/quiz-create-mc-option.php');
                echo $Quiz_create->get_mc_option_image_template( $mc_option, $mc_option_id, $mc_option_i, $mc_option_image);
                $mc_option_i++;
            }
        }
        ?>
        <?php echo $Quiz_create->get_mc_option_add_button($question_id);?>
    </ul>
</fieldset>
<?php 
// var_dump( $mc_option_content);
// die();
?>