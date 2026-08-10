<?php
/**
 * Post-Hero Home composition.
 *
 * TemplateService prepares native product groups and real destinations. This
 * template owns Home section markup only and performs no persistence/mutation.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$groups                              = isset( $groups ) && is_array( $groups ) ? $groups : array();
$home_group_definitions              = isset( $home_group_definitions ) && is_array( $home_group_definitions ) ? $home_group_definitions : array();
$illustration_urls                   = isset( $illustration_urls ) && is_array( $illustration_urls ) ? $illustration_urls : array();
$technical_services_illustration_url = isset( $technical_services_illustration_url ) ? trim( (string) $technical_services_illustration_url ) : '';
$representative_limit                = isset( $representative_limit ) ? max( 1, min( 3, (int) $representative_limit ) ) : 3;
$products_url                        = isset( $products_url ) ? trim( (string) $products_url ) : '';
$services_url                        = isset( $services_url ) ? trim( (string) $services_url ) : '';
$contact_url                         = isset( $contact_url ) ? trim( (string) $contact_url ) : '';
$rfq_url                             = isset( $rfq_url ) ? trim( (string) $rfq_url ) : '';
$about_url                           = isset( $about_url ) ? trim( (string) $about_url ) : '';
$content                             = isset( $content ) ? (string) $content : '';

$anchor_keys = array_values(
	array_filter(
		array( 'hydraulic_anchor', 'industrial_anchor' ),
		static function ( $key ) use ( $groups ) {
			return ! empty( $groups[ $key ]['products'] );
		}
	)
);
$support_keys = array_values(
	array_filter(
		array( 'ducting_support', 'pvc_support', 'fittings_support' ),
		static function ( $key ) use ( $groups ) {
			return ! empty( $groups[ $key ]['products'] );
		}
	)
);
$specialist_key      = 'cng_specialist';
$specialist_products = ! empty( $groups[ $specialist_key ]['products'] ) ? $groups[ $specialist_key ]['products'] : array();

$trust_links = array();
if ( '' !== $about_url ) {
	$trust_links[] = array(
		'label' => __( 'Profil Perusahaan', 'graha-selang' ),
		'copy'  => __( 'Kenali fokus, konteks, dan informasi perusahaan Graha Selang.', 'graha-selang' ),
		'url'   => $about_url,
	);
}
if ( '' !== $services_url ) {
	$trust_links[] = array(
		'label' => __( 'Layanan Teknis', 'graha-selang' ),
		'copy'  => __( 'Pelajari jalur layanan yang mendukung pemilihan dan penyusunan kebutuhan selang.', 'graha-selang' ),
		'url'   => $services_url,
	);
}
if ( '' !== $products_url ) {
	$trust_links[] = array(
		'label' => __( 'Kedalaman Produk', 'graha-selang' ),
		'copy'  => __( 'Buka katalog untuk melihat pilihan produk di luar ringkasan Beranda.', 'graha-selang' ),
		'url'   => $products_url,
	);
}
if ( '' !== $rfq_url ) {
	$trust_links[] = array(
		'label' => __( 'Konsultasi Kebutuhan', 'graha-selang' ),
		'copy'  => __( 'Gunakan Request Quote saat kebutuhan memerlukan pembahasan yang lebih spesifik.', 'graha-selang' ),
		'url'   => $rfq_url,
	);
} elseif ( '' !== $contact_url ) {
	$trust_links[] = array(
		'label' => __( 'Hubungi Graha Selang', 'graha-selang' ),
		'copy'  => __( 'Lanjutkan percakapan melalui halaman kontak yang tersedia.', 'graha-selang' ),
		'url'   => $contact_url,
	);
}

$closing_primary_url   = '';
$closing_primary_label = '';
$closing_secondary_url = '';
$closing_secondary_label = '';
if ( '' !== $rfq_url ) {
	$closing_primary_url   = $rfq_url;
	$closing_primary_label = __( 'Buat Request Quote', 'graha-selang' );
	if ( '' !== $contact_url ) {
		$closing_secondary_url   = $contact_url;
		$closing_secondary_label = __( 'Hubungi Graha Selang', 'graha-selang' );
	} elseif ( '' !== $products_url ) {
		$closing_secondary_url   = $products_url;
		$closing_secondary_label = __( 'Kembali ke katalog', 'graha-selang' );
	}
} elseif ( '' !== $contact_url ) {
	$closing_primary_url   = $contact_url;
	$closing_primary_label = __( 'Hubungi Graha Selang', 'graha-selang' );
	if ( '' !== $products_url ) {
		$closing_secondary_url   = $products_url;
		$closing_secondary_label = __( 'Lihat katalog', 'graha-selang' );
	}
} elseif ( '' !== $products_url ) {
	$closing_primary_url   = $products_url;
	$closing_primary_label = __( 'Lihat katalog', 'graha-selang' );
}
?>
<div class="graha-native-home">
	<section class="graha-section graha-section--default graha-home-orientation">
		<div class="graha-section__inner graha-container graha-container--wide">
			<?php
			graha_render_section_heading(
				__( 'Mulai dari kebutuhan Anda', 'graha-selang' ),
				__( 'Pilih jalur pencarian yang paling relevan', 'graha-selang' ),
				__( 'Gunakan katalog atau konsultasi teknis sebagai titik awal, lalu lanjutkan ke keluarga produk yang paling relevan.', 'graha-selang' )
			);
			graha_render_discovery_grid(
				array(
					array(
						'icon'  => 'box',
						'title' => __( 'Produk', 'graha-selang' ),
						'copy'  => __( 'Telusuri katalog untuk membandingkan keluarga dan pilihan produk.', 'graha-selang' ),
						'url'   => $products_url,
						'cta'   => __( 'Lihat katalog', 'graha-selang' ),
					),
					array(
						'icon'  => 'chat',
						'title' => __( 'Konsultasi Teknis', 'graha-selang' ),
						'copy'  => __( 'Sampaikan konteks kebutuhan untuk dibahas melalui jalur Request Quote.', 'graha-selang' ),
						'url'   => $rfq_url,
						'cta'   => __( 'Request Quote', 'graha-selang' ),
					),
				)
			);
			?>
			<?php if ( '' !== trim( wp_strip_all_tags( $content ) ) ) : ?>
				<div class="graha-native-home__source graha-stack"><?php echo wp_kses_post( $content ); ?></div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $anchor_keys ) : ?>
		<section class="graha-section graha-section--brand-soft graha-section--major graha-section--brand-glow graha-home-anchors">
			<div class="graha-section__inner graha-container graha-container--wide">
				<?php
				graha_render_section_heading(
					__( 'Kategori Andalan', 'graha-selang' ),
					__( 'Dua keluarga utama untuk memulai pencarian', 'graha-selang' ),
					__( 'Mulai dari kelompok hidrolik atau industrial, lalu buka produk yang paling dekat dengan kebutuhan Anda.', 'graha-selang' )
				);
				?>
				<div class="graha-home-anchor-grid">
					<?php foreach ( $anchor_keys as $key ) :
						$definition       = isset( $home_group_definitions[ $key ] ) ? $home_group_definitions[ $key ] : array();
						$products         = $groups[ $key ]['products'];
						$illustration_url = isset( $illustration_urls[ $key ] ) ? $illustration_urls[ $key ] : '';
						$family_class     = str_replace( '_', '-', $key );
						?>
						<article class="graha-card graha-card--anchor graha-home-family-card graha-home-family-card--anchor graha-home-family-card--<?php echo esc_attr( $family_class ); ?>">
							<?php if ( '' !== $illustration_url ) : ?>
								<figure class="graha-card__visual graha-card__visual--illustration" aria-hidden="true">
									<img src="<?php echo esc_url( $illustration_url ); ?>" alt="" width="640" height="480" loading="lazy" decoding="async">
								</figure>
							<?php endif; ?>
							<span class="graha-discovery-card__tag"><?php echo esc_html__( 'Kategori Andalan', 'graha-selang' ); ?></span>
							<h3><?php echo esc_html( isset( $definition['label'] ) ? $definition['label'] : '' ); ?></h3>
							<p class="graha-home-family-card__copy"><?php echo esc_html__( 'Buka pilihan produk dari keluarga ini untuk penelusuran awal.', 'graha-selang' ); ?></p>
							<ul class="graha-link-list graha-home-family-card__links">
								<?php foreach ( array_slice( $products, 0, $representative_limit ) as $product ) : ?>
									<li><a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['name'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $support_keys ) : ?>
		<section class="graha-section graha-section--default graha-home-support">
			<div class="graha-section__inner graha-container graha-container--wide">
				<?php
				graha_render_section_heading(
					__( 'Kategori Pendukung', 'graha-selang' ),
					__( 'Perluas pencarian ke keluarga pendukung', 'graha-selang' ),
					__( 'Ducting, PVC, serta fitting dan coupling membantu mempersempit pencarian sebelum masuk ke katalog yang lebih lengkap.', 'graha-selang' )
				);
				?>
				<div class="graha-home-support-grid">
					<?php foreach ( $support_keys as $key ) :
						$definition       = isset( $home_group_definitions[ $key ] ) ? $home_group_definitions[ $key ] : array();
						$products         = $groups[ $key ]['products'];
						$illustration_url = isset( $illustration_urls[ $key ] ) ? $illustration_urls[ $key ] : '';
						$family_class     = str_replace( '_', '-', $key );
						?>
						<article class="graha-card graha-home-family-card graha-home-family-card--support graha-home-family-card--<?php echo esc_attr( $family_class ); ?>">
							<?php if ( '' !== $illustration_url ) : ?>
								<figure class="graha-card__visual graha-card__visual--illustration" aria-hidden="true">
									<img src="<?php echo esc_url( $illustration_url ); ?>" alt="" width="640" height="480" loading="lazy" decoding="async">
								</figure>
							<?php endif; ?>
							<span class="graha-discovery-card__tag"><?php echo esc_html__( 'Kategori Pendukung', 'graha-selang' ); ?></span>
							<h3><?php echo esc_html( isset( $definition['label'] ) ? $definition['label'] : '' ); ?></h3>
							<p class="graha-home-family-card__copy"><?php echo esc_html__( 'Lihat pilihan ringkas dari keluarga produk ini.', 'graha-selang' ); ?></p>
							<ul class="graha-link-list graha-home-family-card__links">
								<?php foreach ( array_slice( $products, 0, $representative_limit ) as $product ) : ?>
									<li><a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['name'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $specialist_products ) :
		$specialist_definition       = isset( $home_group_definitions[ $specialist_key ] ) ? $home_group_definitions[ $specialist_key ] : array();
		$specialist_illustration_url = isset( $illustration_urls[ $specialist_key ] ) ? $illustration_urls[ $specialist_key ] : '';
		?>
		<section class="graha-section graha-section--contrast graha-section--compact graha-home-specialist">
			<div class="graha-section__inner graha-container graha-container--wide">
				<?php
				graha_render_section_heading(
					__( 'Kategori Spesialis', 'graha-selang' ),
					__( 'Jalur khusus untuk kebutuhan CNG', 'graha-selang' ),
					__( 'Kebutuhan CNG ditempatkan pada jalur tersendiri agar mudah dikenali tanpa tercampur dengan kategori pendukung.', 'graha-selang' )
				);
				?>
				<div class="graha-home-specialist__layout">
					<?php if ( '' !== $specialist_illustration_url ) : ?>
						<figure class="graha-card__visual graha-card__visual--illustration graha-home-specialist__visual" aria-hidden="true">
							<img src="<?php echo esc_url( $specialist_illustration_url ); ?>" alt="" width="640" height="480" loading="lazy" decoding="async">
						</figure>
					<?php endif; ?>
					<div class="graha-home-specialist__body">
						<h3><?php echo esc_html( isset( $specialist_definition['label'] ) ? $specialist_definition['label'] : '' ); ?></h3>
						<p class="graha-section__copy"><?php echo esc_html__( 'Buka pilihan produk CNG untuk penelusuran awal.', 'graha-selang' ); ?></p>
						<ul class="graha-link-list graha-home-family-card__links">
							<?php foreach ( array_slice( $specialist_products, 0, $representative_limit ) as $product ) : ?>
								<li><a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['name'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( '' !== $services_url ) : ?>
		<section class="graha-section graha-section--soft graha-section--major graha-home-capability">
			<div class="graha-section__inner graha-container graha-container--wide">
				<div class="graha-home-capability__layout">
					<?php if ( '' !== $technical_services_illustration_url ) : ?>
						<figure class="graha-home-capability__visual" aria-hidden="true">
							<img src="<?php echo esc_url( $technical_services_illustration_url ); ?>" alt="" width="640" height="480" loading="lazy" decoding="async">
						</figure>
					<?php endif; ?>
					<div class="graha-home-capability__body">
						<?php
						graha_render_section_heading(
							__( 'Kapabilitas Teknis', 'graha-selang' ),
							__( 'Dukungan untuk menyusun kebutuhan selang dengan lebih terarah', 'graha-selang' ),
							__( 'Gunakan layanan teknis ketika pencarian membutuhkan pembahasan mengenai assembly, sambungan, atau pemilihan produk.', 'graha-selang' )
						);
						?>
						<ul class="graha-home-capability__items">
							<li><h3><?php echo esc_html__( 'Crimping & assembly', 'graha-selang' ); ?></h3><p><?php echo esc_html__( 'Jalur layanan untuk kebutuhan penyusunan hose assembly.', 'graha-selang' ); ?></p></li>
							<li><h3><?php echo esc_html__( 'Custom fitting, coupling & flange', 'graha-selang' ); ?></h3><p><?php echo esc_html__( 'Pembahasan sambungan dan komponen pendukung sesuai kebutuhan yang disampaikan.', 'graha-selang' ); ?></p></li>
							<li><h3><?php echo esc_html__( 'Konsultasi pemilihan produk', 'graha-selang' ); ?></h3><p><?php echo esc_html__( 'Pendampingan untuk mempersempit pilihan produk sebelum proses berikutnya.', 'graha-selang' ); ?></p></li>
						</ul>
						<div class="graha-home-capability__actions">
							<?php graha_render_button( __( 'Lihat layanan teknis', 'graha-selang' ), $services_url, 'outline' ); ?>
						</div>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( '' !== $products_url || '' !== $rfq_url ) : ?>
		<section class="graha-section graha-section--default graha-home-discovery">
			<div class="graha-section__inner graha-container graha-container--wide">
				<?php
				graha_render_section_heading(
					__( 'Jalur Penelusuran', 'graha-selang' ),
					__( 'Lanjutkan dari produk atau dari kebutuhan teknis', 'graha-selang' ),
					__( 'Pilih katalog ketika Anda ingin membandingkan produk, atau gunakan jalur spesifikasi ketika konteks kebutuhan perlu dibahas lebih dulu.', 'graha-selang' )
				);
				?>
				<div class="graha-home-pathways">
					<?php if ( '' !== $products_url ) : ?>
						<a class="graha-home-pathway" href="<?php echo esc_url( $products_url ); ?>">
							<span class="graha-home-pathway__icon"><?php echo graha_ui_icon( 'box' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="graha-home-pathway__body"><strong><?php echo esc_html__( 'Telusuri berdasarkan produk', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Buka katalog dan lanjutkan ke produk yang paling relevan.', 'graha-selang' ); ?></span></span>
							<span class="graha-home-pathway__arrow"><?php echo graha_ui_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</a>
					<?php endif; ?>
					<?php if ( '' !== $rfq_url ) : ?>
						<a class="graha-home-pathway" href="<?php echo esc_url( $rfq_url ); ?>">
							<span class="graha-home-pathway__icon"><?php echo graha_ui_icon( 'gear' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="graha-home-pathway__body"><strong><?php echo esc_html__( 'Mulai dari kebutuhan spesifikasi', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Sampaikan konteks penggunaan agar pembahasan dimulai dari kebutuhan Anda.', 'graha-selang' ); ?></span></span>
							<span class="graha-home-pathway__arrow"><?php echo graha_ui_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $trust_links ) : ?>
		<section class="graha-section graha-section--brand-soft graha-home-trust">
			<div class="graha-section__inner graha-container graha-container--wide">
				<div class="graha-home-trust__layout">
					<div class="graha-home-trust__intro">
						<?php
						graha_render_section_heading(
							__( 'Graha Selang', 'graha-selang' ),
							__( 'Produk, layanan, dan jalur konsultasi dalam satu alur', 'graha-selang' ),
							__( 'Gunakan informasi perusahaan dan layanan untuk memahami konteks Graha Selang sebelum melanjutkan ke produk atau konsultasi.', 'graha-selang' )
						);
						?>
					</div>
					<ul class="graha-home-trust__links">
						<?php foreach ( $trust_links as $trust_link ) : ?>
							<li><a href="<?php echo esc_url( $trust_link['url'] ); ?>"><strong><?php echo esc_html( $trust_link['label'] ); ?></strong><span><?php echo esc_html( $trust_link['copy'] ); ?></span><?php echo graha_ui_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( '' !== $closing_primary_url ) : ?>
		<section class="graha-section graha-section--contrast graha-section--major graha-home-closing">
			<div class="graha-section__inner graha-container graha-container--wide">
				<div class="graha-home-closing__layout">
					<div class="graha-home-closing__body">
						<?php graha_render_eyebrow( __( 'Langkah Berikutnya', 'graha-selang' ), true ); ?>
						<h2><?php echo esc_html__( 'Lanjutkan ke kebutuhan yang ingin Anda bahas', 'graha-selang' ); ?></h2>
						<p><?php echo esc_html__( 'Siapkan konteks penggunaan, produk yang dicari, atau kebutuhan sambungan agar percakapan berikutnya lebih terarah.', 'graha-selang' ); ?></p>
					</div>
					<div class="graha-home-closing__actions">
						<?php graha_render_button( $closing_primary_label, $closing_primary_url, 'primary' ); ?>
						<?php graha_render_button( $closing_secondary_label, $closing_secondary_url, 'outline', 'graha-button--on-dark' ); ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
</div>
