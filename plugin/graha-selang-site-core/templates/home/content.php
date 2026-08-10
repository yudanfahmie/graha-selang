<?php
/**
 * Phase D1 post-Hero Home composition.
 *
 * TemplateService prepares native product groups and real destinations. This
 * template owns section markup only and performs no persistence/mutation.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

$groups                 = isset( $groups ) && is_array( $groups ) ? $groups : array();
$home_group_definitions = isset( $home_group_definitions ) && is_array( $home_group_definitions ) ? $home_group_definitions : array();
$illustration_urls      = isset( $illustration_urls ) && is_array( $illustration_urls ) ? $illustration_urls : array();
$representative_limit   = isset( $representative_limit ) ? max( 1, min( 3, (int) $representative_limit ) ) : 3;
$products_url           = isset( $products_url ) ? trim( (string) $products_url ) : '';
$services_url           = isset( $services_url ) ? trim( (string) $services_url ) : '';
$contact_url            = isset( $contact_url ) ? trim( (string) $contact_url ) : '';
$rfq_url                = isset( $rfq_url ) ? trim( (string) $rfq_url ) : '';
$about_url              = isset( $about_url ) ? trim( (string) $about_url ) : '';
$content                = isset( $content ) ? (string) $content : '';

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
?>
<div class="graha-native-home">
	<section class="graha-section graha-section--default graha-home-orientation">
		<div class="graha-section__inner graha-container graha-container--wide">
			<?php
			graha_render_section_heading(
				__( 'Mulai dari kebutuhan Anda', 'graha-selang' ),
				__( 'Jalur yang jelas untuk menemukan produk', 'graha-selang' ),
				__( 'Gunakan katalog atau konsultasi teknis untuk menelusuri kebutuhan, lalu pilih keluarga produk yang saat ini tersedia.', 'graha-selang' )
			);
			graha_render_discovery_grid(
				array(
					array(
						'icon'  => 'box',
						'title' => __( 'Produk', 'graha-selang' ),
						'copy'  => __( 'Jelajahi katalog produk yang saat ini dipublikasikan Graha Selang.', 'graha-selang' ),
						'url'   => $products_url,
						'cta'   => __( 'Lihat katalog', 'graha-selang' ),
					),
					array(
						'icon'  => 'chat',
						'title' => __( 'Konsultasi Teknis', 'graha-selang' ),
						'copy'  => __( 'Gunakan Request Quote untuk melanjutkan pembahasan kebutuhan teknis.', 'graha-selang' ),
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
					__( 'Lihat beberapa produk nyata yang saat ini tersedia dari kelompok hidrolik dan industrial. Home menampilkan pilihan ringkas; katalog menyimpan kedalaman browsing.', 'graha-selang' )
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
							<p class="graha-home-family-card__copy"><?php echo esc_html__( 'Telusuri produk yang saat ini tersedia dalam kelompok ini.', 'graha-selang' ); ?></p>
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
					__( 'Keluarga produk pendukung', 'graha-selang' ),
					__( 'Gunakan kelompok pendukung untuk memperluas penelusuran tanpa menjadikan Home sebagai katalog lengkap.', 'graha-selang' )
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
							<p class="graha-home-family-card__copy"><?php echo esc_html__( 'Lihat beberapa produk yang saat ini dipublikasikan dalam kelompok ini.', 'graha-selang' ); ?></p>
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
					__( 'Kelompok spesialis dipisahkan dari kategori pendukung agar kebutuhan CNG tetap mudah dikenali tanpa menambahkan klaim teknis yang tidak tersedia.', 'graha-selang' )
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
						<p class="graha-section__copy"><?php echo esc_html__( 'Telusuri beberapa produk native yang saat ini tersedia pada kelompok spesialis ini.', 'graha-selang' ); ?></p>
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

	<?php if ( '' !== $services_url || '' !== $about_url ) : ?>
		<section class="graha-section graha-section--soft graha-home-existing-proof">
			<div class="graha-section__inner graha-container graha-container--wide">
				<?php
				graha_render_section_heading(
					__( 'Perusahaan', 'graha-selang' ),
					__( 'Layanan & informasi perusahaan', 'graha-selang' ),
					__( 'Lihat informasi layanan dan profil perusahaan yang dipublikasikan Graha Selang untuk memahami jalur dukungan yang tersedia.', 'graha-selang' )
				);
				$proof_items = array();
				if ( '' !== $services_url ) {
					$proof_items[] = array(
						'label'  => __( 'Layanan Teknis', 'graha-selang' ),
						'detail' => __( 'Crimping/assembly, custom fitting, dan konsultasi pemilihan produk', 'graha-selang' ),
						'url'    => $services_url,
					);
				}
				if ( '' !== $about_url ) {
					$proof_items[] = array(
						'label'  => __( 'Profil Perusahaan', 'graha-selang' ),
						'detail' => __( 'Kapabilitas dan informasi Graha Selang', 'graha-selang' ),
						'url'    => $about_url,
					);
				}
				graha_render_trust_strip( $proof_items );
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( '' !== $rfq_url || '' !== $contact_url ) : ?>
		<section class="graha-section graha-section--default graha-home-closing">
			<div class="graha-section__inner graha-container graha-container--wide">
				<?php
				graha_render_cta_panel(
					__( 'Konsultasi Teknis', 'graha-selang' ),
					__( 'Konsultasi teknis & Request Quote', 'graha-selang' ),
					__( 'Sampaikan kebutuhan produk atau aplikasi melalui Request Quote atau kanal kontak Graha Selang.', 'graha-selang' ),
					__( 'Buat Request Quote', 'graha-selang' ),
					$rfq_url,
					__( 'Hubungi Graha Selang', 'graha-selang' ),
					$contact_url
				);
				?>
			</div>
		</section>
	<?php endif; ?>
</div>
