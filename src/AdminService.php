<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AdminService {
	const MENU_SLUG           = 'graha-selang-content';
	const OVERVIEW_CAPABILITY = 'edit_pages';

	/** @var AssetService */
	private $assets;

	/** @var array<int, string> */
	private $overview_hooks = array();

	/**
	 * @param AssetService $assets Canonical asset owner.
	 */
	public function __construct( AssetService $assets ) {
		$this->assets = $assets;
	}

	/**
	 * Register admin hooks owned by this service.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register the single Graha-owned top-level admin parent and real Overview child.
	 *
	 * Position 3 is the canonical default. Collision-specific ordering remains
	 * deferred until the target WordPress admin can be inspected.
	 *
	 * @return void
	 */
	public function register_menu() {
		$top_hook = add_menu_page(
			'Graha Selang Content',
			'Graha Selang Content',
			self::OVERVIEW_CAPABILITY,
			self::MENU_SLUG,
			array( $this, 'render_overview' ),
			'dashicons-admin-site-alt3',
			3
		);

		$child_hook = add_submenu_page(
			self::MENU_SLUG,
			'Ringkasan Graha Selang',
			'Ringkasan',
			self::OVERVIEW_CAPABILITY,
			self::MENU_SLUG,
			array( $this, 'render_overview' )
		);

		foreach ( array( $top_hook, $child_hook ) as $hook ) {
			if ( is_string( $hook ) && '' !== $hook ) {
				$this->overview_hooks[] = $hook;
			}
		}
	}

	/**
	 * Load Graha admin assets only on the Graha-owned overview screen.
	 *
	 * @param string $hook_suffix Current admin hook suffix.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, $this->overview_hooks, true ) ) {
			return;
		}

		$this->assets->enqueue_admin_overview();
	}

	/**
	 * Render a small handoff page without duplicating native WordPress/Woo CRUD.
	 *
	 * @return void
	 */
	public function render_overview() {
		if ( ! current_user_can( self::OVERVIEW_CAPABILITY ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk membuka halaman ini.', 'graha-selang' ) );
		}
		?>
		<div class="wrap graha-admin-overview">
			<h1><?php echo esc_html( 'Graha Selang Content' ); ?></h1>
			<p class="graha-admin-overview__intro"><?php echo esc_html__( 'Kelola konten melalui layar WordPress dan WooCommerce yang tetap menjadi pemilik datanya.', 'graha-selang' ); ?></p>

			<div class="graha-admin-overview__links" aria-label="<?php echo esc_attr__( 'Akses pengelolaan konten', 'graha-selang' ); ?>">
				<?php if ( current_user_can( 'edit_pages' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">
						<strong><?php echo esc_html__( 'Halaman', 'graha-selang' ); ?></strong>
						<span><?php echo esc_html__( 'Gunakan editor WordPress untuk halaman tetap.', 'graha-selang' ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">
						<strong><?php echo esc_html__( 'Artikel', 'graha-selang' ); ?></strong>
						<span><?php echo esc_html__( 'Gunakan Posts untuk artikel dan panduan.', 'graha-selang' ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( current_user_can( 'upload_files' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>">
						<strong><?php echo esc_html__( 'Media', 'graha-selang' ); ?></strong>
						<span><?php echo esc_html__( 'Gunakan Media Library untuk aset yang disetujui.', 'graha-selang' ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( post_type_exists( 'product' ) && current_user_can( 'edit_products' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">
						<strong><?php echo esc_html__( 'Produk WooCommerce', 'graha-selang' ); ?></strong>
						<span><?php echo esc_html__( 'Produk tetap dikelola melalui WooCommerce.', 'graha-selang' ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
