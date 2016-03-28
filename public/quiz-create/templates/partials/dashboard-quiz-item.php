<li class="enp-dash-item">
    <h3 class="enp-dash-item__title"><a href="<?php echo ENP_QUIZ_CREATE_URL;?>/<? echo $quiz->get_quiz_id();?>"><span class="enp-screen-reader-text">Edit </span><? echo $quiz->get_quiz_title();?> <svg class="enp-icon enp-dash-item__title__icon">
      <use xlink:href="#icon-edit" />
    </svg></a></h3>
    <div class="enp-dash-item__controls">
        <div class="enp-dash-item__status"><? echo $quiz->get_quiz_status();?></div>
        <ul class="enp-dash-item__nav">
            <li class="enp-dash-item__nav__item"><a href="<?php echo ENP_QUIZ_RESULTS_URL;?>">Results</a></li>
            <li class="enp-dash-item__nav__item"><a href="<?php echo ENP_QUIZ_PUBLISH_URL;?>">Embed</a></li>
        </ul>
    </div>
</li>
