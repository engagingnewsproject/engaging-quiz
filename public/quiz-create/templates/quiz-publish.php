<?php
/**
 * The template for the "publish" page when a user
 * finishes creating a quiz. This also has the embed code
 * on it.
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */

get_header(); ?>

<div class="enp-container enp-publish-page-container">
    <?php include('template/breadcrumbs.php');?>

    <div class="enp-flex enp-publish-page-flex-container">
        <section class="enp-container enp-publish-container">
            <h1 class="enp-page-title enp-publish-page__title">Published!</h1>
            <p>Copy and paste the embed code onto your website where you'd like it to appear.</p>
            <textarea class="enp-embed-code enp-publish-page__embed-code" rows="3"><iframe frameBorder="0" height="500px" width="100%" src="http://engagingnewsproject.org/enp_prod/iframe-quiz/?guid=definitely_not_a_real_link"></iframe></textarea>
        </section>

        <section class="enp-container enp-aside-container enp-publish-page__aside-container">
            <aside class="enp-aside enp-share-quiz__container">
                <h3 class="enp-aside-title enp-share-quiz__title">Share Your Quiz</h3>
                <a class="enp-share-quiz__url" href="#">http://engagingnewsproject.org/quiz_id_url/not-real-yet</a></p>
                <ul class="enp-share-quiz">
                    <li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--facebook" href="#facebook">
                        <svg class="enp-icon enp-icon--facebook enp-share-quiz__item__icon enp-share-quiz__item__icon--facebook">
                          <use xlink:href="#icon-facebook" />
                        </svg>
                    </a></li>
                    <li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--twitter" href="#twitter">
                        <svg class="enp-icon enp-icon--twitter enp-share-quiz__item__icon enp-share-quiz__item__icon--twitter">
                          <use xlink:href="#icon-twitter" />
                        </svg>
                    </a></li>
                    <li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--email" href="#email">
                        <svg class="enp-icon enp-icon--mail enp-share-quiz__item__icon enp-share-quiz__item__icon--email">
                          <use xlink:href="#icon-mail" />
                        </svg>
                    </a></li>
                </ul>
            </aside>

            <aside class="enp-aside enp-ab-ad__container">
                <h3 class="enp-aside-title enp-ab-ad__title">A/B Test</h3>
                <p class="enp-ab-ad__description">Some description on what an A/B Test is.</p>
                <a class="enp-btn enp-ab-ad__link" href="a-b-test.php">New A/B Test</a>
            </aside>
        </section>
    </div>
</div>




<?php get_footer(); ?>