<?php
/**
 * Global Graha Selang footer: closing CTA band, navigation columns, bottom bar.
 *
 * Included from templates/shell.php inside TemplateService's render scope,
 * which already defines $site_name and $footer_links (real destinations only).
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$site_name    = isset( $site_name ) ? (string) $site_name : '';
$footer_links = isset( $footer_links ) && is_array( $footer_links ) ? $footer_links : array();

$explore = array_values( array_filter( array(
	! empty( $footer_links['products_url'] ) ? array( __( 'Produk', 'graha-selang' ), (string) $footer_links['products_url'] ) : null,
	! empty( $footer_links['services_url'] ) ? array( __( 'Layanan Kami', 'graha-selang' ), (string) $footer_links['services_url'] ) : null,
	! empty( $footer_links['about_url'] ) ? array( __( 'Tentang Kami', 'graha-selang' ), (string) $footer_links['about_url'] ) : null,
) ) );
$connect = array_values( array_filter( array(
	! empty( $footer_links['rfq_url'] ) ? array( __( 'Request Quote', 'graha-selang' ), (string) $footer_links['rfq_url'] ) : null,
	! empty( $footer_links['contact_url'] ) ? array( __( 'Hubungi Kami', 'graha-selang' ), (string) $footer_links['contact_url'] ) : null,
) ) );
?>
<?php if ( '' !== $site_name && ! empty( $footer_links['rfq_url'] ) ) : ?>
<div class="graha-footer__cta"><div class="graha-container graha-container--wide graha-footer__cta-inner">
	<div>
		<p class="graha-eyebrow graha-eyebrow--on-dark"><?php echo esc_html( $site_name ); ?></p>
		<h2><?php echo esc_html__( 'Konsultasikan kebutuhan produk atau aplikasi Anda', 'graha-selang' ); ?></h2>
	</div>
	<a class="graha-button graha-button--primary" href="<?php echo esc_url( (string) $footer_links['rfq_url'] ); ?>"><?php echo esc_html__( 'Request Quote', 'graha-selang' ); ?></a>
</div></div>
<?php endif; ?>
<div class="graha-container graha-container--wide graha-footer__grid">
	<div class="graha-footer__brand">
		<?php if ( '' !== $site_name ) : ?><p class="graha-footer__brand-name"><?php echo esc_html( $site_name ); ?></p><?php endif; ?>
		<p class="graha-footer__tagline"><?php echo esc_html__( 'Pemasok selang industri, hidrolik, dan fitting untuk kebutuhan teknis di lapangan.', 'graha-selang' ); ?></p>
	</div>
	<?php if ( $explore ) : ?>
	<div class="graha-footer__col">
		<h3><?php echo esc_html__( 'Jelajahi', 'graha-selang' ); ?></h3>
		<ul><?php foreach ( $explore as $link ) : ?><li><a href="<?php echo esc_url( $link[1] ); ?>"><?php echo esc_html( $link[0] ); ?></a></li><?php endforeach; ?></ul>
	</div>
	<?php endif; ?>
	<?php if ( $connect ) : ?>
	<div class="graha-footer__col">
		<h3><?php echo esc_html__( 'Konsultasi', 'graha-selang' ); ?></h3>
		<ul><?php foreach ( $connect as $link ) : ?><li><a href="<?php echo esc_url( $link[1] ); ?>"><?php echo esc_html( $link[0] ); ?></a></li><?php endforeach; ?></ul>
	</div>
	<?php endif; ?>
</div>
<div class="graha-container graha-container--wide graha-site-footer__bottom">
	<p>&copy; <?php echo esc_html( gmdate( 'Y' ) . ( '' !== $site_name ? ' ' . $site_name : '' ) ); ?></p>
</div>
