<fieldset class="enp-slider-options">
    <legend class="enp-legend enp-slider__legend">Slider Options</legend>
    <input class="enp-slider-id" type="hidden" name="enp_question[<?echo $question_i;?>][slider][slider_id]" value="<? echo $slider_id;?>" />
    <label class="enp-label enp-slider-range-low__label" for="enp-slider-range-low">Slider Start</label>
    <input id="enp-slider-range-low" class="enp-input enp-slider-range-low__input" type="number" name="enp_question[<?echo $question_i;?>][slider][slider_range_low]" value="<?php echo $slider->get_slider_range_low();?>">

    <label class="enp-label enp-slider-range-high__label" for="enp-slider-range-high">Slider End</label>
    <input id="enp-slider-range-high" class="enp-input enp-slider-range-high__input" type="number" name="enp_question[<?echo $question_i;?>][slider][slider_range_high]" value="<?php echo $slider->get_slider_range_high();?>">

    <label class="enp-label enp-slider-correct-low__label" for="enp-slider-correct-low">Slider Correct Low</label>
    <input id="enp-slider-correct-low" class="enp-input enp-slider-correct-low__input" type="number" name="enp_question[<?echo $question_i;?>][slider][slider_correct_low]" value="<?php echo $slider->get_slider_correct_low();?>">

    <label class="enp-label enp-slider-correct-high__label" for="enp-slider-correct-high">Slider Correct High</label>
    <input id="enp-slider-correct-high" class="enp-input enp-slider-correct-high__input" type="number" name="enp_question[<?echo $question_i;?>][slider][slider_correct_high]" value="<?php echo $slider->get_slider_correct_high();?>">

    <label class="enp-label enp-slider-increment__label" for="enp-slider-increment">Slider Increment</label>
    <input id="enp-slider-increment" class="enp-input enp-slider-increment__input" type="number" name="enp_question[<?echo $question_i;?>][slider][slider_increment]" value="<?php echo $slider->get_slider_increment();?>">

    <label class="enp-label enp-slider-prefix__label" for="enp-slider-prefix">Slider Number Prefix</label>
    <input id="enp-slider-prefix" class="enp-input enp-slider-prefix__input" type="text" maxlength="100" name="enp_question[<?echo $question_i;?>][slider][slider_prefix]" value="<?php echo $slider->get_slider_prefix();?>">

    <label class="enp-label enp-slider-suffix__label" for="enp-slider-suffix">Slider Number Suffix</label>
    <input id="enp-slider-suffix" class="enp-input enp-slider-suffix__input" type="text" maxlength="100" name="enp_question[<?echo $question_i;?>][slider][slider_suffix]" value="<?php echo $slider->get_slider_suffix();?>">
</fieldset>
