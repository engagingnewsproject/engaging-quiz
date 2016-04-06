<section class="enp-results">
    <div class="enp-results__score">
        <?php
            // calculate the score
            $score = 50; // or something...
            $r = 90;
            $c = M_PI*($r*2);

            $pct = ((100-$score)/100)*$c;
        ?>
        <h2 class="enp-results__score__title center">50<span class="enp-results__score__title__percentage">%</span> <span class="enp-screen-reader-text">Correct</span></h2>
        <svg class="enp-results__score__circle" width="200" height="200" viewPort="0 0 100 100" version="1.1" xmlns="http://www.w3.org/2000/svg">
          <circle class="enp-results__score__circle__bg" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
          <circle id="enp-results__score__circle__path" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="<?php echo $pct;?>"></circle>
        </svg>
    </div>
    <p class="enp-results__encouragement">Not bad!</p>
    <p class="enp-results__description">You did better than <strong>90%</strong> of people. That quiz was kind of a hard, eh?</p>
    <h3 class="enp-results__share-title">Share Your Results</h3>
    <ul class="enp-results__share">
        <li class="enp-results__share__item"><a class="enp-results__share__link enp-results__share__item--facebook" href="#facebook">
            <svg class="enp-icon enp-icon--facebook enp-results__share__item__icon enp-results__share__item__icon--facebook">
              <use xlink:href="#icon-facebook" />
            </svg>
        </a></li>
        <li class="enp-results__share__item"><a class="enp-results__share__link enp-results__share__item--twitter" href="#twitter">
            <svg class="enp-icon enp-icon--twitter enp-results__share__item__icon enp-results__share__item__icon--twitter">
              <use xlink:href="#icon-twitter" />
            </svg>
        </a></li>
        <li class="enp-results__share__item"><a class="enp-results__share__link enp-results__share__item--email" href="#email">
            <svg class="enp-icon enp-icon--mail enp-results__share__item__icon enp-results__share__item__icon--email">
              <use xlink:href="#icon-mail" />
            </svg>
        </a></li>
    </ul>
</section>