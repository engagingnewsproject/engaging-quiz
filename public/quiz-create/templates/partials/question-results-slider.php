<?php
    $slider_range_low = $slider->get_slider_range_low();
    $slider_range_high = $slider->get_slider_range_high();
    $slider_correct_low = $slider->get_slider_correct_low();
    $slider_correct_high = $slider->get_slider_correct_high();
?>

<li class="enp-results-question__option enp-results-question__option--incorrect">
    <?php echo $this->option_correct_icon('0');?>
    <div class="enp-results-question__option__text">
        <span class="enp-results-question__option__text__helper">low</span> <?php echo $slider_range_low;?> to <?php echo ( $slider_correct_low - $slider->get_slider_increment() );?>
    </div>
    <div class="enp-results-question__option__number-selected">
        <?php echo $slider->get_slider_responses_low();?>&nbsp;/&nbsp;<span class="enp-results-question__option__percentage enp-results-question__option__percentage--incorrect"><?php echo $this->percentagize( $slider->get_slider_responses_low(), $slider->get_slider_responses_total(), 1);?>%</span>
    </div>
</li>
<li class="enp-results-question__option enp-results-question__option--correct">
    <?php echo $this->option_correct_icon('1');?>
    <div class="enp-results-question__option__text">
        <span class="enp-results-question__option__text__helper">correct</span> <?php echo $slider_correct_low . ($slider_correct_low === $slider_correct_high ? '' :' to '. $slider_correct_high);?>
    </div>
    <div class="enp-results-question__option__number-selected">
        <?php echo $slider->get_slider_responses_correct();?>&nbsp;/&nbsp;<span class="enp-results-question__option__percentage enp-results-question__option__percentage--correct"><?php echo $this->percentagize( $slider->get_slider_responses_correct(), $slider->get_slider_responses_total(), 1);?>%</span>
    </div>
</li>
<li class="enp-results-question__option enp-results-question__option--incorrect">
    <?php echo $this->option_correct_icon('0');?>
    <div class="enp-results-question__option__text">
        <span class="enp-results-question__option__text__helper">high</span> <?php echo ( $slider_correct_high + $slider->get_slider_increment() );?> to <?php echo $slider->get_slider_range_high();?>
    </div>
    <div class="enp-results-question__option__number-selected">
        <?php echo $slider->get_slider_responses_high();?>&nbsp;/&nbsp;<span class="enp-results-question__option__percentage enp-results-question__option__percentage--incorrect"><?php echo $this->percentagize( $slider->get_slider_responses_high(), $slider->get_slider_responses_total(), 1);?>%</span>
    </div>
</li>
