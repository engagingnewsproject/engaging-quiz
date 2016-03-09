<?php
/**
 * The template for users to view all of the quizzes and
 * A/B Tests they have created, and begin actions on their
 * account (create new A/B Test, create new quiz, user alerts
 * etc).
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>

<section class="enp-container enp-dash-container">
    <header class="enp-dash__section-header">
        <h2 class="enp-dash__section-title">Quizzes</h2>
        <div class="enp-quiz-list__view">
            <svg class="enp-view-toggle enp-view-toggle__grid enp-icon">
              <use xlink:href="#icon-grid" />
            </svg>
            <svg class="enp-view-toggle enp-view-toggle__list enp-icon">
              <use xlink:href="#icon-list" />
            </svg>

            <select class="enp-sort-by">
                <option>Date Created</option>
                <option>Most Results</option>
            </select>
        </div>
    </header>
    <ul class="enp-quiz-list">
        <li class="enp-dash-item enp-dash-item--add-new">
            <a class="enp-dash-link--add-new enp-dash-link--add-new-quiz" href="quiz-add-question.php">
                <svg class="enp-dash-link__icon enp-icon">
                  <use xlink:href="#icon-add" />
                </svg>
                New Quiz
            </a>
        </li>
        <li class="enp-dash-item">
            <h3 class="enp-dash-item__title"><a href="quiz-add-question.php?quiz-title=How+Much+Do+You+Know+About+Syria?"><span class="enp-screen-reader-text">Edit </span>How Much Do You Know About Syria? <svg class="enp-icon enp-dash-item__title__icon">
              <use xlink:href="#icon-edit" />
            </svg></a></h3>
            <div class="enp-dash-item__controls">
                <div class="enp-dash-item__status">Published</div>
                <ul class="enp-dash-item__nav">
                    <li class="enp-dash-item__nav__item"><a href="results.php">Results</a></li>
                    <li class="enp-dash-item__nav__item"><a href="quiz-publish.php">Embed</a></li>
                </ul>
            </div>
        </li>
        <li class="enp-dash-item">
            <h3 class="enp-dash-item__title"><a href="quiz-add-question.php?quiz-title=Do+You+Know+The+Facts+About+The+2016+Election?"><span class="enp-screen-reader-text">Edit </span>Do You Know The Facts? <svg class="enp-icon enp-dash-item__title__icon">
              <use xlink:href="#icon-edit" />
            </svg></a></h3>
            <div class="enp-dash-item__controls">
                <div class="enp-dash-item__status">Published</div>
                <ul class="enp-dash-item__nav">
                    <li class="enp-dash-item__nav__item"><a href="quiz-add-question.php">Add Questions</a></li>
                    <li class="enp-dash-item__nav__item"><a href="quiz-preview.php">Preview</a></li>
                </ul>
            </div>
        </li>
        <li class="enp-dash-item">
            <h3 class="enp-dash-item__title"><a href="quiz-add-question.php?quiz-title=How+Much+Do+You+Know+About+Syria?"><span class="enp-screen-reader-text">Edit </span>How Much Do You Know About Syria? <svg class="enp-icon enp-dash-item__title__icon">
              <use xlink:href="#icon-edit" />
            </svg></a></h3>
            <div class="enp-dash-item__controls">
                <div class="enp-dash-item__status">Published</div>
                <ul class="enp-dash-item__nav">
                    <li class="enp-dash-item__nav__item"><a href="results.php">Results</a></li>
                    <li class="enp-dash-item__nav__item"><a href="quiz-publish.php">Embed</a></li>
                </ul>
            </div>
        </li>
        <li class="enp-dash-item">
            <h3 class="enp-dash-item__title"><a href="quiz-add-question.php?quiz-title=How+Much+Do+You+Know+About+Syria?"><span class="enp-screen-reader-text">Edit </span>How Much Do You Know About Syria? <svg class="enp-icon enp-dash-item__title__icon">
              <use xlink:href="#icon-edit" />
            </svg></a></h3>
            <div class="enp-dash-item__controls">
                <div class="enp-dash-item__status">Published</div>
                <ul class="enp-dash-item__nav">
                    <li class="enp-dash-item__nav__item"><a href="results.php">Results</a></li>
                    <li class="enp-dash-item__nav__item"><a href="quiz-publish.php">Embed</a></li>
                </ul>
            </div>
        </li>

    </ul>
</section>

<section class="enp-dash-container">
    <header class="enp-dash__section-header">
        <h2 class="enp-dash__section-title">A/B Test</h2>
        <div class="enp-quiz-list__view">
            <svg class="enp-view-toggle enp-view-toggle__grid enp-icon">
              <use xlink:href="#icon-grid" />
            </svg>
            <svg class="enp-view-toggle enp-view-toggle__list enp-icon">
              <use xlink:href="#icon-list" />
            </svg>

            <select class="enp-sort-by">
                <option>Date Created</option>
                <option>Most Results</option>
            </select>
        </div>
    </header>
    <ul class="enp-ab-list">
        <li class="enp-dash-item enp-dash-item--add-new">
            <a class="enp-dash-link--add-new enp-dash-link--add-new-ab-test" href="a-b-test.php"><svg class="enp-dash-link__icon enp-icon">
              <use xlink:href="#icon-add" />
            </svg>New A/B Test</a>
        </li>
        <li class="enp-dash-item">
            <h3 class="enp-dash-item__title"><a href="a-b-results.php?a-b-name=Syria+Quiz+Title+Test">Syria Quiz Title Test</a></h3>
            <div class="enp-dash-item__controls">
                <ul class="enp-dash-item__nav">
                    <li class="enp-dash-item__nav__item"><a href="a-b-results.php?a-b-name=Syria+Quiz+Title+Test">Results</a></li>
                    <li class="enp-dash-item__nav__item"><a  href="a-b-results.php?a-b-name=Syria+Quiz+Title+Test#embed-code">Embed</a></li>
                </ul>
            </div>
        </li>
    </ul>
</section>


<script src="js/dashboard.js"></script>
