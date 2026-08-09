<?php defined( 'ABSPATH' ) || exit; ?>
<div class="graha-site-shell graha-ui" data-graha-family="<?php echo esc_attr( $family ); ?>">
	<a class="graha-skip-link" href="#graha-main"><?php echo esc_html__( 'Lewati ke konten utama', 'graha-selang' ); ?></a>
	<header class="graha-site-header"><div class="graha-site-header__inner graha-container graha-container--wide">
		<?php if ( '' !== $logo || '' !== $site_name ) : ?><div class="graha-site-identity"><?php if ( '' !== $logo ) : echo wp_kses_post( $logo ); elseif ( '' !== $site_name ) : ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $site_name ); ?></a><?php endif; ?></div><?php endif; ?>
		<?php echo $nav; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div></header>
	<?php echo $main; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<footer class="graha-site-footer"><div class="graha-container graha-container--wide graha-stack"><?php if ( '' !== $footer ) : echo $footer; elseif ( '' !== $site_name ) : ?><p class="graha-site-footer__identity"><?php echo esc_html( $site_name ); ?></p><?php endif; ?></div></footer>
</div>
