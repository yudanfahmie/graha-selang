<?php
/**
 * Global Graha Selang header: identity, primary navigation, RFQ CTA.
 *
 * Included from templates/shell.php inside TemplateService's render scope,
 * which already defines $site_name, $logo, $brand_logo_url, $nav and $rfq_url.
 *
 * Brand-mark hierarchy (never the bare letter mark in normal production use):
 *   1. a real WordPress Custom Logo, output exactly as WordPress builds it
 *      (it already includes its own accessible link+image, so it is never
 *      re-wrapped in a second anchor here -- that would be invalid nested
 *      links and a duplicate accessible name);
 *   2. otherwise the bundled Graha Selang SVG brand mark, decorative
 *      (empty alt) inside our own link whose accessible name comes from the
 *      one visible site-name text node -- one link, one accessible name;
 *   3. a plain letter mark only if even the bundled asset URL is somehow
 *      unavailable.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$site_name      = isset( $site_name ) ? (string) $site_name : '';
$logo           = isset( $logo ) ? (string) $logo : '';
$brand_logo_url = isset( $brand_logo_url ) ? (string) $brand_logo_url : '';
$nav            = isset( $nav ) ? (string) $nav : '';
$rfq_url        = isset( $rfq_url ) ? (string) $rfq_url : '';
?>
<header class="graha-site-header"><div class="graha-site-header__inner graha-container graha-container--wide">
	<?php if ( '' !== $logo || '' !== $site_name ) : ?>
	<div class="graha-site-identity">
		<?php if ( '' !== $logo ) : ?>
			<?php echo wp_kses_post( $logo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress' own get_custom_logo() already returns a complete accessible <a><img></a>. ?>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( '' !== $brand_logo_url ) : ?>
					<img class="graha-site-identity__logo" src="<?php echo esc_url( $brand_logo_url ); ?>" width="47" height="43" alt="" aria-hidden="true" decoding="async">
				<?php else : ?>
					<span class="graha-site-identity__mark" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $site_name, 0, 1 ) ) ); ?></span>
				<?php endif; ?>
				<span class="graha-site-identity__name"><?php echo esc_html( $site_name ); ?></span>
			</a>
		<?php endif; ?>
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
