<aside class="enp-aside enp-share-quiz__container">
	<h3 class="enp-aside__title enp-share-quiz__title">Share Your Quiz</h3>
	<p><a class="enp-share-quiz__url" href="<?php echo ENP_QUIZ_URL.$quiz->get_quiz_id();?>"><?php echo ENP_QUIZ_URL.$quiz->get_quiz_id();?></a></p>
	<ul class="enp-share-quiz">
		<li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(ENP_QUIZ_URL.$quiz->get_quiz_id());?>">
			<svg class="enp-icon enp-icon--facebook enp-share-quiz__item__icon enp-share-quiz__item__icon--facebook">
			  <use xlink:href="#icon-facebook" />
			</svg>
		</a></li>
		<li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--twitter" href="http://twitter.com/intent/tweet?text=<?php echo $quiz->get_encoded('facebook_title','url');?>&url=<?php echo $quiz->get_quiz_url();?>">
			<svg class="enp-icon enp-icon--twitter enp-share-quiz__item__icon enp-share-quiz__item__icon--twitter">
			  <use xlink:href="#icon-twitter" />
			</svg>
		</a></li>
		<li class="enp-share-quiz__item"><a class="enp-share-quiz__link enp-share-quiz__item--email" href="mailto:?subject=<?php echo $quiz->get_encoded('email_subject', 'rawurl');?>&body=<?php echo $quiz->get_encoded('email_body_start', 'rawurl').rawurlencode(' '.$quiz->get_quiz_url());?>">
			<svg class="enp-icon enp-icon--mail enp-share-quiz__item__icon enp-share-quiz__item__icon--email">
			  <use xlink:href="#icon-mail" />
			</svg>
		</a></li>
	</ul>
	<!-- Feeback form -->
	<!-- TODO: Submit feedback to database -->
	<p>Thanks for using our quiz tool!</p>
	<form action="" method="get" class="form-example">
		<?php
		// $enp_quiz_nonce->outputKey();
		// echo $Quiz_create->hidden_fields();
		?>
		<input id="enp-quiz-id" type="hidden" name="enp_quiz[quiz_id]" value="enp-quiz-feedback-<?php echo $quiz->get_quiz_id();?>" />
		<fieldset class="enp-fieldset enp-quiz-title">
			<label for="enp-quiz-feedback-<?php echo $quiz->get_quiz_id();?>" class="enp-label enp-quiz-title__label">How can we improve this product?</label>
			<textarea id="enp-quiz-feedback-<?php echo $quiz->get_quiz_id();?>" name="enp-quiz-feedback-<?php echo $quiz->get_quiz_id();?>" rows="5" cols="50" placeholder="Enter your feeback here..."></textarea>
		</fieldset>
		<button type="submit" class="enp-btn--save enp-quiz-submit enp-quiz-form__save" name="enp-quiz-submit" value="save">Submit</button>
	</form>

</aside>
