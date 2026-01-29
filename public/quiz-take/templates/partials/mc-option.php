<input id="enp-option__<?php echo $mc_option->get_mc_option_id();?>" class="enp-option__input enp-option__input--radio" type="radio" name="enp-question-response" value="<?php echo $mc_option->get_mc_option_id();?>"/>
<?php
$mc_option_image = $mc_option->get_mc_option_image();
// Use pre-built URL when set (JS template placeholder or server-provided); else build from question context
$src_from_placeholder = isset( $mc_option->mc_option_image_src ) && (string) $mc_option->mc_option_image_src !== '';
if ( $src_from_placeholder ) {
	$src = $mc_option->mc_option_image_src;
} elseif ( ! empty( $mc_option_image ) && isset( $qt_question->question ) ) {
	$quiz_id     = $qt_question->question->get_quiz_id();
	$question_id = $qt_question->question->get_question_id();
	$src         = ENP_QUIZ_IMAGE_URL . $quiz_id . '/' . $question_id . '/mc_option_' . $mc_option->get_mc_option_id() . '/' . $mc_option_image;
}
if ( ! empty( $mc_option_image ) || $src_from_placeholder ) {
	$alt         = $mc_option->get_mc_option_content();
	// Don't escape template placeholder so JS can interpolate; escape real URLs
	$safe_src = ( strpos( (string) $src, '{{' ) === 0 ) ? $src : ( function_exists( 'esc_attr' ) ? esc_attr( $src ) : htmlspecialchars( $src, ENT_QUOTES, 'UTF-8' ) );
	$safe_alt = function_exists( 'esc_attr' ) ? esc_attr( $alt ) : htmlspecialchars( $alt, ENT_QUOTES, 'UTF-8' );
	// Wrapper (position: relative) so expand button can overlay; button is outside label so click doesn't select the option
	$mc_option_content = $mc_option->get_mc_option_content();
	$has_text          = isset( $mc_option_content ) && trim( (string) $mc_option_content ) !== '';
	?>
	<div class="enp-option__image-wrap">
		<label for="enp-option__<?php echo $mc_option->get_mc_option_id();?>" class="enp-option__label">
			<?php if ( $has_text ) : ?>
				<span class="enp-option__text"><?php echo $safe_alt; ?></span>
			<?php endif; ?>
			<img class="enp-option__image" src="<?php echo $safe_src; ?>" alt="<?php echo $safe_alt; ?>" />
		</label>
		<button type="button" class="enp-option__image-expand" data-enp-expand-src="<?php echo $safe_src; ?>" aria-label="Expand image"><svg class="enp-icon enp-icon--expand" aria-hidden="true"><use xlink:href="#icon-expand"></use></svg></button>
	</div>
	<?php
} else {
	?>
	<label for="enp-option__<?php echo $mc_option->get_mc_option_id();?>" class="enp-option__label">
		<?php echo $mc_option->get_mc_option_content(); ?>
	</label>
	<?php
}
?>
