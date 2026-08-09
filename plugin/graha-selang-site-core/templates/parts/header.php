<?php
/**
 * Global Graha Selang header: identity, primary navigation, RFQ CTA.
 *
 * Included from templates/shell.php inside TemplateService's render scope,
 * which already defines $site_name, $logo, $nav and $rfq_url.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$site_name = isset( $site_name ) ? (string) $site_name : '';
$logo      = isset( $logo ) ? (string) $logo : '';
$nav       = isset( $nav ) ? (string) $nav : '';
$rfq_url   = isset( $rfq_url ) ? (string) $rfq_url : '';
?>
<header class="graha-site-header"><div class="graha-site-header__inner graha-container graha-container--wide">
	<?php if ( '' !== $logo || '' !== $site_name ) : ?>
	<div class="graha-site-identity">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( '' !== $logo ) : ?>
				<?php echo wp_kses_post( $logo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<span class="graha-site-identity__mark" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $site_name, 0, 1 ) ) ); ?></span>
				<span><?php echo esc_html( $site_name ); ?></span>
			<?php endif; ?>
		</a>
	</div>
	<?php endif; ?>
	<div class="graha-site-header__nav-cta">
		<?php echo $nav; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( '' !== $rfq_url ) : ?>
		<div class="graha-site-header__cta">
			<a class="graha-button graha-button--primary" href="<?php echo esc_url( $rfq_url ); ?>"><?php echo esc_html__( 'Request Quote', 'graha-selang' ); ?></a>
		</div>
		<?php endif; ?>
	</div>
</div></header>
