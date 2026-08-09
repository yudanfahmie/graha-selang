<?php
/**
 * Global Graha Selang header: canonical plugin-owned wordmark, navigation and RFQ CTA.
 *
 * The branded shell owns its identity. A stale WordPress Custom Logo must not
 * silently outrank the committed Graha wordmark.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$site_name      = isset( $site_name ) ? trim( (string) $site_name ) : '';
$brand_logo_url = isset( $brand_logo_url ) ? (string) $brand_logo_url : '';
$nav            = isset( $nav ) ? (string) $nav : '';
$rfq_url        = isset( $rfq_url ) ? (string) $rfq_url : '';
$accessible_brand = '' !== $site_name ? $site_name : 'Graha Selang';
?>
<header class="graha-site-header"><div class="graha-site-header__inner graha-container graha-container--wide">
	<div class="graha-site-identity">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( '' !== $brand_logo_url ) : ?>
				<img class="graha-site-identity__logo" src="<?php echo esc_url( $brand_logo_url ); ?>" alt="<?php echo esc_attr( $accessible_brand ); ?>" decoding="async">
			<?php else : ?>
				<span class="graha-site-identity__name"><?php echo esc_html( $accessible_brand ); ?></span>
			<?php endif; ?>
		</a>
	</div>
	<div class="graha-site-header__nav-cta">
		<?php echo $nav; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( '' !== $rfq_url ) : ?>
		<div class="graha-site-header__cta">
			<a class="graha-button graha-button--primary" href="<?php echo esc_url( $rfq_url ); ?>"><?php echo esc_html__( 'Request Quote', 'graha-selang' ); ?></a>
		</div>
		<?php endif; ?>
	</div>
</div></header>
