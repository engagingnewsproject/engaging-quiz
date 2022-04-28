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
<?php do_action('enp_quiz_display_messages');
?>
<aside class="enp-dash__section-aside">
	<h2 class="enp-dash__section-title">Search Quizzes</h2>
	<div class="enp-quiz-list__view">
		<?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/dashboard-quiz-sort.php');?>
	</div>
</aside>
<article class="enp-container enp-dash-container">
	<section class="enp-dash-wrap">
		<?php
		if( $search !== '' ) :
			echo '<p class="enp-search-results-description"> ' . $paginate->total . ' results found for "<strong>'.$_GET['search'].'</strong>". <a class="enp-search-results-description__link" href="'.$this->get_clear_search_url().'"><svg role="presentation" aria-hidden="true" class="enp-icon enp-search-results-description__icon"><use xlink:href="#icon-close" /></svg> Clear Search</a></p>';
		endif;
		?>
		<!-- Add new quizzes -->
		<header class="enp-dash-item--add-new__wrap">
			<div class="enp-dash-item enp-dash-item--add-new">
				<a class="enp-dash-link--add-new enp-dash-link--add-new-quiz" href="<?php echo ENP_QUIZ_CREATE_URL;?>new/">
					<svg class="enp-dash-link__icon enp-icon">
					<use xlink:href="#icon-add"><title>Add</title></use>
					</svg>
					New Quiz
				</a>
			</div>
			<?php
			$published_quizzes = count($user->get_published_quizzes());
			if($published_quizzes < 2) : ?>
				<div class="enp-dash__ab-test-helper enp-dash__ab-test-helper--not-enough-quizzes">
					<p>To create an A/B Test, create at least two published quizzes.</p>
				</div>
			<?php else: ?>
				<div class="enp-dash-item enp-dash-item--add-new">
					<a class="enp-dash-link--add-new enp-dash-link--add-new-ab-test" href="<?php echo ENP_AB_TEST_URL;?>new/"><svg class="enp-dash-link__icon enp-icon">
					<use xlink:href="#icon-add" />
					</svg>New A/B Test</a>
				</div>
			<?php endif; ?>
		</header>
		<!-- List quizzes -->
		<section class="enp-dash-list--quiz__container">
			<div class="enp-dash-list--quiz__wrap">
				<header class="enp-dash__section-header">
					<h2 class="enp-dash__section-title">Quiz List</h2>
				</header>
				<ul class="enp-dash-list enp-dash-list--quiz">
					<?php
					// Reg quizzes.
					if(!empty($quizzes)) {
						foreach($quizzes as $quiz) {
							$quiz = new Enp_quiz_Quiz($quiz);
							include(ENP_QUIZ_CREATE_TEMPLATES_PATH.'/partials/dashboard-quiz-item.php');
						}
					}
					?>
				</ul>
			</div>
			<div class="enp-dash-list--quiz__wrap">
				<header class="enp-dash__section-header">
					<h2 class="enp-dash__section-title">A/B Tests</h2>
				</header>
				<?php
				$published_quizzes = count($user->get_published_quizzes());
				if($published_quizzes > 1) : ?>
					<ul class="enp-dash-list enp-dash-list--ab">
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
			</div>
		</section>
		<?php echo $paginate->get_pagination_links();?>
	</section>
</article>
