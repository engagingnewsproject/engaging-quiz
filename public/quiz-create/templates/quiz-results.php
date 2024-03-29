<?php

/**
 * The template for viewing the results of a quiz
 *
 * @since             0.0.1
 * @package           Enp_quiz
 */
?>
<aside class="enp-dash__section-aside">
	<?php echo $this->dashboard_breadcrumb_link(); ?>

	<section class="enp-container enp-aside__container">
		<?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH . 'partials/quiz-share.php'); ?>
		<div class="enp-embed__container">
			<h3 class="enp-aside__title enp-embed__title">Embed</h3>
			<?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH . 'partials/quiz-embed-code.php'); ?>
		</div>
	</section>
</aside>

<article class="enp-container enp-dash-container">
	<h1 class="enp-page-title enp-results-title"><?php echo $quiz->get_quiz_title(); ?></h1>
	<!-- <h1 class="enp-page-title enp-results-title"><?php // echo $quiz->get_quiz_title_test(); 
														?></h1> -->
	<section class="enp-container enp-results__container">
		<section class="enp-container enp-results-flow__container">
			<?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH . 'partials/quiz-results-flow.php'); ?>
		</section>

		<section class="enp-container enp-quiz-scores__container">
			<div class="enp-quiz-scores">
				<div class="enp-quiz-score__line-chart"></div>
			</div>

			<table class="enp-quiz-scores-table">
				<tr>
					<th class="enp-quiz-scores-table__label">Score</th>
					<th class="enp-quiz-scores-table__score"># of People</th>
				</tr>
				<?php foreach ($quiz->quiz_score_chart_data['quiz_scores'] as $key => $val) { ?>
					<tr>
						<td class="enp-quiz-scores-table__label"><?php echo $quiz->quiz_score_chart_data['quiz_scores_labels'][$key]; ?></td>
						<td class="enp-quiz-scores-table__score"><?php echo $val; ?></td>
					</tr>
				<?php } ?>

			</table>
		</section>
	</section>

	<?php include(ENP_QUIZ_CREATE_TEMPLATES_PATH . 'partials/question-results-section.php'); ?>

</article>