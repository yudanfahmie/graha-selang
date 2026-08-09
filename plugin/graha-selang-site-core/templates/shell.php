<?php defined( 'ABSPATH' ) || exit; ?>
<div class="graha-site-shell graha-ui" data-graha-family="<?php echo esc_attr( $family ); ?>">
	<a class="graha-skip-link" href="#graha-main"><?php echo esc_html__( 'Lewati ke konten utama', 'graha-selang' ); ?></a>
	<?php include __DIR__ . '/parts/header.php'; ?>
	<?php echo $main; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<footer class="graha-site-footer">
		<?php include __DIR__ . '/parts/footer.php'; ?>
		<?php if ( isset( $footer ) && '' !== $footer ) : ?><div class="graha-container graha-container--wide"><?php echo $footer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
	</footer>
</div>
