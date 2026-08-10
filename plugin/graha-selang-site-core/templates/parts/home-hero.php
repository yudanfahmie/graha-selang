<?php
/**
 * Home Hero presentation only. TemplateService prepares real destinations and
 * the canonical illustration URL; this partial owns the markup.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$products_url          = isset( $products_url ) ? trim( (string) $products_url ) : '';
$rfq_url               = isset( $rfq_url ) ? trim( (string) $rfq_url ) : '';
$hero_illustration_url = isset( $hero_illustration_url ) ? trim( (string) $hero_illustration_url ) : '';
?>
<section class="graha-hero graha-home-hero" aria-labelledby="graha-home-hero-title">
	<div class="graha-home-hero__grid graha-container graha-container--wide">
		<div class="graha-home-hero__body">
			<p class="graha-eyebrow graha-hero__eyebrow"><?php echo esc_html__( 'Graha Selang', 'graha-selang' ); ?></p>
			<h1 id="graha-home-hero-title"><?php echo esc_html__( 'Solusi selang industri dan hidrolik untuk kebutuhan lapangan Anda', 'graha-selang' ); ?></h1>
			<div class="graha-home-hero__lead">
				<p><?php echo esc_html__( 'Untuk kebutuhan industri dan hidrolik, telusuri katalog produk yang tersedia atau lanjutkan ke konsultasi teknis melalui jalur Graha Selang.', 'graha-selang' ); ?></p>
			</div>
			<div class="graha-home-hero__actions">
				<?php graha_render_button( __( 'Lihat Katalog Produk', 'graha-selang' ), $products_url, 'primary', 'graha-button--lg' ); ?>
				<?php if ( '' !== $rfq_url ) : graha_render_button( __( 'Request Quote', 'graha-selang' ), $rfq_url, 'outline', 'graha-button--on-dark graha-button--lg' ); endif; ?>
			</div>
		</div>
		<?php if ( '' !== $hero_illustration_url ) : ?>
			<figure class="graha-home-hero__visual" aria-hidden="true">
				<span class="graha-accent-rule"></span>
				<img
					class="graha-home-hero__illustration"
					src="<?php echo esc_url( $hero_illustration_url ); ?>"
					alt=""
					width="800"
					height="640"
					loading="eager"
					fetchpriority="high"
					decoding="async"
				>
			</figure>
		<?php endif; ?>
	</div>
</section>
