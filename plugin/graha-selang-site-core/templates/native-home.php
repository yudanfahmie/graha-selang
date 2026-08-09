<?php defined( 'ABSPATH' ) || exit; ?>
<div class="graha-ui graha-native-home graha-stack--large">
	<?php if ( '' !== trim( wp_strip_all_tags( (string) $content ) ) ) : ?><div class="graha-native-home__source graha-stack"><?php echo wp_kses_post( $content ); ?></div><?php endif; ?>

	<section class="graha-page-section graha-page-section--hero">
		<?php
		graha_render_section_heading(
			__( 'Cara menjelajahi Graha Selang', 'graha-selang' ),
			__( 'Jelajahi berdasarkan kebutuhan', 'graha-selang' ),
			__( 'Telusuri katalog produk atau ajukan konsultasi teknis langsung ke tim kami.', 'graha-selang' )
		);
		graha_render_discovery_grid(
			array(
				array(
					'icon'  => 'box',
					'title' => __( 'Produk', 'graha-selang' ),
					'copy'  => __( 'Jelajahi katalog selang industri dan hidrolik.', 'graha-selang' ),
					'url'   => $products_url,
					'cta'   => __( 'Lihat katalog', 'graha-selang' ),
				),
				array(
					'icon'  => 'chat',
					'title' => __( 'Konsultasi Teknis', 'graha-selang' ),
					'copy'  => __( 'Diskusikan kebutuhan spesifikasi produk melalui Request Quote.', 'graha-selang' ),
					'url'   => $rfq_url,
					'cta'   => __( 'Request Quote', 'graha-selang' ),
				),
			)
		);
		?>
	</section>

	<section class="graha-page-section graha-page-section--products">
		<?php
		graha_render_section_heading(
			__( 'Katalog', 'graha-selang' ),
			__( 'Produk & solusi', 'graha-selang' ),
			__( 'Enam kelompok produk selang dan fitting untuk kebutuhan industri dan hidrolik Anda.', 'graha-selang' )
		);
		?>
		<div class="graha-priority-grid">
			<?php foreach ( self::HOME_GROUPS as $key => $definition ) : $products = isset( $groups[ $key ]['products'] ) ? $groups[ $key ]['products'] : array(); ?>
				<section class="graha-card graha-card--<?php echo esc_attr( $definition['priority'] ); ?>">
					<span class="graha-discovery-card__tag"><?php
						echo esc_html(
							'anchor' === $definition['priority'] ? __( 'Kategori Andalan', 'graha-selang' ) : ( 'specialist' === $definition['priority'] ? __( 'Kategori Spesialis', 'graha-selang' ) : __( 'Kategori Pendukung', 'graha-selang' ) )
						);
					?></span>
					<h3><?php echo esc_html( $definition['label'] ); ?></h3>
					<?php if ( $products ) : ?>
						<ul class="graha-link-list"><?php foreach ( array_slice( $products, 0, 6 ) as $product ) : ?><li><a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['name'] ); ?></a></li><?php endforeach; ?></ul>
					<?php else : ?>
						<p><?php echo esc_html__( 'Gunakan katalog atau konsultasi teknis untuk menelusuri kebutuhan pada kelompok ini.', 'graha-selang' ); ?></p>
					<?php endif; ?>
				</section>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="graha-page-section graha-page-section--proof">
		<?php
		graha_render_section_heading(
			__( 'Perusahaan', 'graha-selang' ),
			__( 'Layanan & informasi perusahaan', 'graha-selang' ),
			__( 'Lihat informasi layanan dan profil perusahaan yang dipublikasikan Graha Selang untuk memahami jalur dukungan yang tersedia.', 'graha-selang' )
		);
		graha_render_trust_strip(
			array(
				array(
					'label'  => __( 'Layanan Teknis', 'graha-selang' ),
					'detail' => __( 'Crimping/assembly, custom fitting, dan konsultasi pemilihan produk', 'graha-selang' ),
					'url'    => $services_url,
				),
				array(
					'label'  => __( 'Profil Perusahaan', 'graha-selang' ),
					'detail' => __( 'Kapabilitas dan informasi Graha Selang', 'graha-selang' ),
					'url'    => $about_url,
				),
			)
		);
		?>
	</section>

	<section class="graha-page-section graha-page-section--consultation">
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
	</section>
</div>
