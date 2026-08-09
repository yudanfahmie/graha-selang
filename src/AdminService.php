<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AdminService {
	const MENU_SLUG  = 'graha-selang-content';
	const CAPABILITY = 'edit_pages';

	/**
	 * Register admin hooks owned by this service.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	/**
	 * Register the single Graha-owned top-level admin parent.
	 *
	 * Position 3 is the canonical default. A collision-specific ordering
	 * correction is intentionally deferred until the target admin is known.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			'Graha Selang Content',
			'Graha Selang Content',
			self::CAPABILITY,
			self::MENU_SLUG,
			array( $this, 'render_overview' ),
			'dashicons-admin-site-alt3',
			3
		);

		add_submenu_page(
			self::MENU_SLUG,
			'Ringkasan Graha Selang',
			'Ringkasan',
			self::CAPABILITY,
			self::MENU_SLUG,
			array( $this, 'render_overview' )
		);
	}

	/**
	 * Render a small handoff page without duplicating native WordPress/Woo CRUD.
	 *
	 * @return void
	 */
	public function render_overview() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk membuka halaman ini.', 'graha-selang' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( 'Graha Selang Content' ); ?></h1>
			<p><?php echo esc_html__( 'Fondasi plugin aktif. Kelola konten melalui layar WordPress dan WooCommerce yang menjadi pemilik datanya.', 'graha-selang' ); ?></p>
			<ul>
				<?php if ( current_user_can( 'edit_pages' ) ) : ?>
					<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"><?php echo esc_html__( 'Halaman', 'graha-selang' ); ?></a></li>
				<?php endif; ?>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<li><a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>"><?php echo esc_html__( 'Artikel', 'graha-selang' ); ?></a></li>
				<?php endif; ?>
				<?php if ( current_user_can( 'upload_files' ) ) : ?>
					<li><a href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>"><?php echo esc_html__( 'Media', 'graha-selang' ); ?></a></li>
				<?php endif; ?>
				<?php if ( post_type_exists( 'product' ) && current_user_can( 'edit_products' ) ) : ?>
					<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>"><?php echo esc_html__( 'Produk WooCommerce', 'graha-selang' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
	}
}
