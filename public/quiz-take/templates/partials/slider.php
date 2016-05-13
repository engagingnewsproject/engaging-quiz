<label for="enp-slider__<?php echo $slider->get_slider_id();?>" class="enp-slider__label">
    Enter a number
</label>
<div class="enp-slider">
    <span class="enp-slider__prefix"><?php echo $slider->get_slider_prefix();?></span>
    <input id="enp-slider__<?php echo $slider->get_slider_id();?>" class="enp-slider__input" type="number" name="enp-question-response" min="<?php echo $slider->get_slider_range_low();?>" max="<?php echo $slider->get_slider_range_high();?>" step="<?php echo $slider->get_slider_increment();?>" size="<?php echo $slider->get_slider_input_size();?>" value="<?php echo $slider->get_slider_start();?>"/>
    <span class="enp-slider__suffix"><?php echo $slider->get_slider_suffix();?></span>
</div>
<div class="jquery-slider"></div>
