<aside class="enp-aside enp-share-quiz__container">
    <h3 class="enp-aside__title enp-share-quiz__title">Share Your Quiz</h3>
    <p><a class="enp-share-quiz__url" href="<? echo ENP_QUIZ_URL.$quiz->get_quiz_id();?>"><? echo ENP_QUIZ_URL.$quiz->get_quiz_id();?></a></p>
    <ul class="enp-share-quiz">
        <li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(ENP_QUIZ_URL.$quiz->get_quiz_id());?>">
            <svg class="enp-icon enp-icon--facebook enp-share-quiz__item__icon enp-share-quiz__item__icon--facebook">
              <use xlink:href="#icon-facebook" />
            </svg>
        </a></li>
        <li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--twitter" href="http://twitter.com/intent/tweet?text=<?php echo $quiz->get_tweet_start();?>&url=<?php echo ENP_QUIZ_URL.$quiz->get_quiz_id();?>">
            <svg class="enp-icon enp-icon--twitter enp-share-quiz__item__icon enp-share-quiz__item__icon--twitter">
              <use xlink:href="#icon-twitter" />
            </svg>
        </a></li>
        <li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--email" href="mailto:?subject=<?php echo $quiz->get_email_subject_start();?>&body=<?php echo $quiz->get_email_body_start();?>">
            <svg class="enp-icon enp-icon--mail enp-share-quiz__item__icon enp-share-quiz__item__icon--email">
              <use xlink:href="#icon-mail" />
            </svg>
        </a></li>
    </ul>
</aside>
