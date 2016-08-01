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
 /*
 $user = new Enp_quiz_User();
 object containing user quizzes and ab_tests
 $quizzes = quizzes available to this view);
 */
?>
<?php do_action('enp_quiz_display_messages'); ?>
<?php

?>
<section class="enp-container enp-dash-container">
    <?php

    if(isset($_COOKIE['enp_quiz_creator_first_visit']) && $_COOKIE['enp_quiz_creator_first_visit'] === '1' ) {

        // show the message
        ?>
        <aside class="enp-quiz-message enp-quiz-message--success enp-quiz-message--welcome">
            <h2 class="enp-quiz-message__title enp-quiz-message__title--success">Welcome to the New Quiz Creator!</h2>
            <p>We’ve been working hard to bring you an updated, modern way to create and take quizzes. We’ve made a number of improvements, so give our new tool a try and <a href="/about-us/contact-us/">let us know what you think!</a></p>

            <p>If you’re not ready for the switch yet, you can click the “Go to Old Quiz Tool” link at the bottom of the page to keep using the old tool.</p>

            <p>Best,<br/>
            The Engaging News Project Team</p>
        </aside>
    <?php } ?>

    <header class="enp-dash__section-header">
        <h2 class="enp-dash__section-title">Quizzes</h2>
        <div class="enp-quiz-list__view">
            <form id="enp-search-quizzes" class="enp-search-quizzes" method="get" action="<?php echo htmlentities(ENP_QUIZ_DASHBOARD_URL.'user/'); ?>">
                <?php
                // set our variables for the search
                $order_by = (isset($_GET['order_by']) ? $_GET['order_by'] : '');
                $search = (isset($_GET['search']) ? $_GET['search'] : '');
                $include = (isset($_GET['include']) ? $_GET['include'] : '');
                ;?>
                <div class="enp-search-quizzes__form-item enp-quiz-search">
                    <label class="enp-label enp-search-quizzes__label" for="enp-quiz-search">Search Quizzes</label>
                    <input id="enp-quiz-search" class="enp-input enp-quiz-search__input" type="search" name="search" value="<?php echo $search;?>"/>
                    <svg class="enp-quiz-search__icon enp-icon">
                      <use xlink:href="#icon-search"><title>Search</title></use>
                    </svg>
                </div>
                <div class="enp-search-quizzes__form-item">
                    <label for="enp-quiz-order-by" class="enp-label enp-search-quizzes__label">Order<span class="enp-screen-reader-text"> Quizzes</span> By</label>
                    <select id="enp-quiz-order-by" name="order_by" class="enp-search-quizzes__select">
                        <option <?php selected( $order_by, "quiz_created_at" ); ?> value="quiz_created_at">Created at</option>
                        <option <?php selected( $order_by, "quiz_score_average" ); ?> value="quiz_score_average">Average Score</option>
                        <option <?php selected( $order_by, "quiz_views"); ?> value="quiz_views">Views</option>
                        <option <?php selected( $order_by, "quiz_completion_rate" ); ?> value="quiz_completion_rate">Completion Rate</option>
                    </select>
                </div>
                <?php
                if(current_user_can('manage_options')) {
                    echo '<div class="enp-search-quizzes__form-item"><label class="enp-label enp-search-quizzes__label" for="enp-quiz-include">Include</label>';
                    echo '<select id="enp-quiz_include" name="include" class="enp-search-quizzes__select">
                    <option '.selected( $include, "user", false ).' value="user">My Quizzes</option>
                    <option '.selected( $include, "all_users", false ).' value="all_users">All User\'s Quizzes</option>
                    </select></div>';
                }?>

                <button class="enp-btn enp-search-quizzes__button">Search</button>

            </form>
        </div>
    </header>
    <ul class="enp-dash-list enp-dash-list--quiz">
        <li class="enp-dash-item enp-dash-item--add-new">
            <a class="enp-dash-link--add-new enp-dash-link--add-new-quiz" href="<?php echo ENP_QUIZ_CREATE_URL;?>new/">
                <svg class="enp-dash-link__icon enp-icon">
                  <use xlink:href="#icon-add"><title>Add</title></use>
                </svg>
                New Quiz
            </a>
        </li>

        <?php
        if(!empty($quizzes)) {
            foreach($quizzes as $quiz) {
                $quiz = new Enp_quiz_Quiz($quiz);
                include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/dashboard-quiz-item.php');
            }
        }
        ?>
    </ul>
</section>

<section class="enp-dash-container">
    <header class="enp-dash__section-header">
        <h2 class="enp-dash__section-title">A/B Test</h2>
        <div class="enp-quiz-list__view">

            <!--<select class="enp-sort-by">
                <option>Date Created</option>
                <option>Most Results</option>
            </select>-->
        </div>
    </header>
    <?php
    if($user->get_published_quizzes() < 2) : ?>
        <div class="enp-dash__ab-test-helper enp-dash__ab-test-helper--not-enough-quizzes">
            <p>To create an A/B Test, create at least two quizzes.</p>
        </div>
    <?php else: ?>
        <ul class="enp-dash-list enp-dash-list--ab">
            <li class="enp-dash-item enp-dash-item--add-new">
                <a class="enp-dash-link--add-new enp-dash-link--add-new-ab-test" href="<?php echo ENP_AB_TEST_URL;?>new/"><svg class="enp-dash-link__icon enp-icon">
                  <use xlink:href="#icon-add" />
                </svg>New A/B Test</a>
            </li>
            <?php
            $ab_tests = $user->get_ab_tests();
            if(!empty($ab_tests)) {
                foreach($ab_tests as $ab_test) {
                    $ab_test = new Enp_quiz_AB_test($ab_test);
                    include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'partials/dashboard-ab-item.php');
                }
            } ?>
        </ul>
    <?php endif; ?>

    <p class="enp-old-quiz-tool"><a class="enp-old-quiz-tool__link" href="<?php echo site_url('create-a-quiz');?>">Go to Old Quiz Tool</a></p>
</section>
