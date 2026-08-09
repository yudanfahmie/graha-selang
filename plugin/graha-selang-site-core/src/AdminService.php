<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AdminService {
	const MENU_SLUG            = 'graha-selang-content';
	const OVERVIEW_CAPABILITY  = 'edit_pages';
	const MIGRATION_CAPABILITY = 'manage_woocommerce';
	const MIGRATION_SLUG       = 'graha-selang-product-migration';
	const MIGRATION_AJAX       = 'graha_selang_run_product_catalog_migration';
	const MIGRATION_NONCE      = 'graha_selang_product_catalog_migration';

	/** @var AssetService */
	private $assets;

	/** @var ProductCatalogMigration|null */
	private $migration;

	/** @var array<int, string> */
	private $overview_hooks = array();

	/** @var string */
	private $migration_hook = '';

	/**
	 * @param AssetService $assets Canonical asset owner.
	 */
	public function __construct( AssetService $assets ) {
		$this->assets    = $assets;
		$this->migration = null;
	}

	/** @return void */
	public function register() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		if ( function_exists( 'is_admin' ) && is_admin() ) {
			add_action( 'wp_ajax_' . self::MIGRATION_AJAX, array( $this, 'run_product_migration' ) );
		}
	}

	/** @return void */
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

		$migration = current_user_can( self::MIGRATION_CAPABILITY ) ? $this->get_migration() : null;
		if ( $migration && $migration->should_show_menu() ) {
			$hook = add_submenu_page(
				self::MENU_SLUG,
				'Migrasi Katalog Produk',
				'Migrasi Produk',
				self::MIGRATION_CAPABILITY,
				self::MIGRATION_SLUG,
				array( $this, 'render_product_migration' )
			);
			if ( is_string( $hook ) ) {
				$this->migration_hook = $hook;
			}
		}
	}

	/**
	 * @param string $hook_suffix Current admin hook suffix.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( in_array( $hook_suffix, $this->overview_hooks, true ) ) {
			$this->assets->enqueue_admin_overview();
			return;
		}

		if ( '' !== $this->migration_hook && $hook_suffix === $this->migration_hook && current_user_can( self::MIGRATION_CAPABILITY ) ) {
			$this->assets->enqueue_admin_migration(
				self::MIGRATION_AJAX,
				wp_create_nonce( self::MIGRATION_NONCE )
			);
		}
	}

	/** @return void */
	public function render_overview() {
		if ( ! current_user_can( self::OVERVIEW_CAPABILITY ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk membuka halaman ini.', 'graha-selang' ) );
		}

		$migration = current_user_can( self::MIGRATION_CAPABILITY ) ? $this->get_migration() : null;
		$summary   = $migration ? $migration->get_summary() : array();
		?>
		<div class="wrap graha-admin-overview">
			<h1><?php echo esc_html( 'Graha Selang Content' ); ?></h1>
			<p class="graha-admin-overview__intro"><?php echo esc_html__( 'Kelola konten melalui layar WordPress dan WooCommerce yang tetap menjadi pemilik datanya.', 'graha-selang' ); ?></p>

			<?php if ( ! empty( $summary ) && 'invalid' === $summary['detection'] ) : ?>
				<div class="notice notice-error inline"><p><?php echo esc_html( $summary['message'] ); ?></p></div>
			<?php elseif ( ! empty( $summary ) && 'consumed' === $summary['detection'] && isset( $summary['cleanup'] ) && 'failed' === $summary['cleanup'] ) : ?>
				<div class="notice notice-warning inline"><p><?php echo esc_html__( 'Migrasi telah selesai dan tidak dapat dijalankan ulang, tetapi cleanup runtime bundle gagal: ', 'graha-selang' ); ?><?php echo esc_html( isset( $summary['cleanup_message'] ) ? $summary['cleanup_message'] : '' ); ?></p></div>
			<?php endif; ?>

			<div class="graha-admin-overview__links" aria-label="<?php echo esc_attr__( 'Akses pengelolaan konten', 'graha-selang' ); ?>">
				<?php if ( current_user_can( 'edit_pages' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"><strong><?php echo esc_html__( 'Halaman', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Gunakan editor WordPress untuk halaman tetap.', 'graha-selang' ); ?></span></a>
				<?php endif; ?>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>"><strong><?php echo esc_html__( 'Artikel', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Gunakan Posts untuk artikel dan panduan.', 'graha-selang' ); ?></span></a>
				<?php endif; ?>
				<?php if ( current_user_can( 'upload_files' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>"><strong><?php echo esc_html__( 'Media', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Gunakan Media Library untuk aset yang disetujui.', 'graha-selang' ); ?></span></a>
				<?php endif; ?>
				<?php if ( post_type_exists( 'product' ) && current_user_can( 'edit_products' ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>"><strong><?php echo esc_html__( 'Produk WooCommerce', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Produk tetap dikelola melalui WooCommerce.', 'graha-selang' ); ?></span></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/** @return void */
	public function render_product_migration() {
		if ( ! current_user_can( self::MIGRATION_CAPABILITY ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk menjalankan migrasi produk.', 'graha-selang' ) );
		}

		$migration = $this->get_migration();
		$summary   = $migration->get_summary();
		if ( ! in_array( $summary['detection'], array( 'pending', 'failed', 'running' ), true ) ) {
			wp_die( esc_html__( 'Bundle migrasi tidak tersedia atau sudah dikonsumsi.', 'graha-selang' ) );
		}
		?>
		<div class="wrap graha-admin-migration" data-graha-product-migration>
			<h1><?php echo esc_html__( 'Migrasi Katalog Produk', 'graha-selang' ); ?></h1>
			<p><?php echo esc_html__( 'Halaman ini hanya menampilkan status ringan. Validasi checksum dan import dijalankan setelah tombol eksekusi ditekan.', 'graha-selang' ); ?></p>
			<dl class="graha-admin-migration__summary">
				<dt><?php echo esc_html__( 'Bundle', 'graha-selang' ); ?></dt><dd><?php echo esc_html( $summary['bundle_id'] ); ?></dd>
				<dt><?php echo esc_html__( 'Versi sumber', 'graha-selang' ); ?></dt><dd><?php echo esc_html( $summary['source_version'] ); ?></dd>
				<dt><?php echo esc_html__( 'Jumlah record', 'graha-selang' ); ?></dt><dd><?php echo esc_html( (string) $summary['expected_records'] ); ?></dd>
				<dt><?php echo esc_html__( 'Status', 'graha-selang' ); ?></dt><dd data-graha-migration-state><?php echo esc_html( $summary['detection'] ); ?></dd>
			</dl>
			<?php if ( ! empty( $summary['message'] ) ) : ?><div class="notice notice-warning inline"><p><?php echo esc_html( $summary['message'] ); ?></p></div><?php endif; ?>
			<p><button type="button" class="button button-primary" data-graha-migration-run <?php disabled( 'running' === $summary['detection'] ); ?>><?php echo esc_html__( 'Validasi & impor produk', 'graha-selang' ); ?></button></p>
			<div class="graha-admin-migration__result" data-graha-migration-result aria-live="polite"></div>
		</div>
		<?php
	}

	/** @return void */
	public function run_product_migration() {
		if ( ! current_user_can( self::MIGRATION_CAPABILITY ) ) {
			wp_send_json_error( array( 'message' => __( 'Izin migrasi tidak mencukupi.', 'graha-selang' ) ), 403 );
		}
		if ( false === check_ajax_referer( self::MIGRATION_NONCE, 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce migrasi tidak valid.', 'graha-selang' ) ), 403 );
		}
		$migration = $this->get_migration();

		try {
			$result = $migration->execute();
			$cleanup_complete = isset( $result['cleanup'] ) && 'complete' === $result['cleanup'];
			$message = $cleanup_complete
				? __( 'Impor produk berhasil dan file runtime migrasi telah dibersihkan.', 'graha-selang' )
				: __( 'Impor produk berhasil, tetapi pembersihan file runtime gagal. Impor tidak akan dijalankan ulang.', 'graha-selang' );
			wp_send_json_success(
				array(
					'message' => $message,
					'state'   => $result,
				)
			);
		} catch ( \Throwable $error ) {
			wp_send_json_error( array( 'message' => $error->getMessage() ), 409 );
		}
	}

	/**
	 * Lazily load the narrow migration coordinator only for authorized admin
	 * menu/render/AJAX paths. Frontend requests never require this file.
	 *
	 * @return ProductCatalogMigration
	 */
	private function get_migration() {
		if ( ! $this->migration ) {
			require_once __DIR__ . '/ProductCatalogMigration.php';
			$this->migration = new ProductCatalogMigration( dirname( __DIR__ ) . '/graha-selang.php' );
		}
		return $this->migration;
	}
}
