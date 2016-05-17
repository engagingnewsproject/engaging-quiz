<label for="enp-slider__<?php echo $slider->get_slider_id();?>" class="enp-slider__label">
    Enter a number between <?php echo $slider->get_slider_range_low();?> and <?php echo $slider->get_slider_range_high();?>
</label>
<div class="enp-slider-input__container">
    <span class="enp-slider-input__prefix"><?php echo $slider->get_slider_prefix();?></span>
    <input id="enp-slider-input__<?php echo $slider->get_slider_id();?>" class="enp-slider-input__input" type="number" name="enp-question-response" min="<?php echo $slider->get_slider_range_low();?>" max="<?php echo $slider->get_slider_range_high();?>" step="<?php echo $slider->get_slider_increment();?>" size="<?php echo $slider->get_slider_input_size();?>" value="<?php echo $slider->get_slider_start();?>"/>
    <span class="enp-slider-input__suffix"><?php echo $slider->get_slider_suffix();?></span>
    <div class="enp-slider_input__range-helper enp-slider_input__range-helper--min"><span class="enp-slider-input__range-helper__number enp-slider_input__range-helper__number--min" role="presentation" aria-hidden="true"><?php echo $slider->get_slider_range_low();?></span></div>
    <div class="enp-slider_input__range-helper enp-slider_input__range-helper--max"><span class="enp-slider-input__range-helper__number enp-slider_input__range-helper__number--max"  role="presentation" aria-hidden="true"><?php echo $slider->get_slider_range_high();?></span></div>
</div>
