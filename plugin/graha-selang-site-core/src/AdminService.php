<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AdminService {
	const MENU_SLUG             = 'graha-selang-content';
	const OVERVIEW_CAPABILITY   = 'edit_pages';
	const PRODUCT_CAPABILITY    = 'edit_posts';
	const TAXONOMY_CAPABILITY   = 'manage_categories';
	const MIGRATION_CAPABILITY  = 'edit_posts';
	const MIGRATION_SLUG        = 'graha-selang-product-migration';
	const MIGRATION_AJAX        = 'graha_selang_run_product_catalog_migration';
	const MIGRATION_NONCE       = 'graha_selang_product_catalog_migration';

	/** @var AssetService */
	private $assets;

	/** @var ProductCatalogMigration|null */
	private $migration;

	/** @var array<int, string> */
	private $overview_hooks = array();

	/** @var string */
	private $migration_hook = '';

	/** @param AssetService $assets Canonical asset owner. */
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

		if ( current_user_can( self::PRODUCT_CAPABILITY ) ) {
			add_submenu_page( self::MENU_SLUG, 'Produk Graha', 'Produk', self::PRODUCT_CAPABILITY, 'edit.php?post_type=graha_product', '' );
		}
		if ( current_user_can( self::TAXONOMY_CAPABILITY ) ) {
			add_submenu_page( self::MENU_SLUG, 'Kategori Produk', 'Kategori Produk', self::TAXONOMY_CAPABILITY, 'edit-tags.php?taxonomy=graha_product_category&post_type=graha_product', '' );
			add_submenu_page( self::MENU_SLUG, 'Merek Produk', 'Merek', self::TAXONOMY_CAPABILITY, 'edit-tags.php?taxonomy=graha_product_brand&post_type=graha_product', '' );
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

	/** @param string $hook_suffix Current admin hook suffix. @return void */
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

		$migration    = current_user_can( self::MIGRATION_CAPABILITY ) ? $this->get_migration() : null;
		$summary      = $migration ? $migration->get_summary() : array();
		$presentation = $this->presentation_status();
		?>
		<div class="wrap graha-admin-overview">
			<h1><?php echo esc_html( 'Graha Selang Content' ); ?></h1>
			<p class="graha-admin-overview__intro"><?php echo esc_html__( 'Kelola konten Graha melalui layar WordPress native yang tetap menjadi pemilik datanya.', 'graha-selang' ); ?></p>

			<?php if ( ! $presentation['ownership_active'] ) : ?>
				<div class="notice notice-error inline"><p><?php echo esc_html__( 'Graha tidak dapat menampilkan Homepage: berkas presentasi front-page plugin tidak ditemukan atau tidak dapat dibaca. Periksa hasil deployment plugin.', 'graha-selang' ); ?></p></div>
			<?php elseif ( ! $presentation['logo_ready'] || ! $presentation['favicons_ready'] || ! $presentation['product_presentation_active'] ) : ?>
				<div class="notice notice-warning inline"><p><?php echo esc_html__( 'Sebagian aset atau pemilik presentasi Graha tidak lengkap pada deployment ini. Periksa status Presentasi di bawah sebelum membuka frontend untuk stakeholder.', 'graha-selang' ); ?></p></div>
			<?php endif; ?>

			<h2><?php echo esc_html__( 'Presentasi', 'graha-selang' ); ?></h2>
			<dl class="graha-admin-overview__status">
				<dt><?php echo esc_html__( 'Versi plugin', 'graha-selang' ); ?></dt>
				<dd><?php echo esc_html( $presentation['version'] ); ?></dd>
				<dt><?php echo esc_html__( 'Kepemilikan Homepage', 'graha-selang' ); ?></dt>
				<dd><?php $this->render_status_badge( $presentation['ownership_active'], __( 'Aktif', 'graha-selang' ), __( 'Perlu perhatian', 'graha-selang' ) ); ?></dd>
				<dt><?php echo esc_html__( 'Logo header kanonis', 'graha-selang' ); ?></dt>
				<dd><?php $this->render_status_badge( $presentation['logo_ready'], __( 'OK', 'graha-selang' ), __( 'MISSING', 'graha-selang' ) ); ?></dd>
				<dt><?php echo esc_html__( 'Favicon kanonis', 'graha-selang' ); ?></dt>
				<dd><?php $this->render_status_badge( $presentation['favicons_ready'], __( 'OK', 'graha-selang' ), __( 'MISSING', 'graha-selang' ) ); ?></dd>
				<dt><?php echo esc_html__( 'Presentasi katalog produk', 'graha-selang' ); ?></dt>
				<dd><?php $this->render_status_badge( $presentation['product_presentation_active'], __( 'Aktif', 'graha-selang' ), __( 'MISSING', 'graha-selang' ) ); ?></dd>
				<dt><?php echo esc_html__( 'Mode Homepage WordPress', 'graha-selang' ); ?></dt>
				<dd><?php echo esc_html( $presentation['mode_label'] ); ?></dd>
				<dt><?php echo esc_html__( 'Halaman struktural tersedia', 'graha-selang' ); ?></dt>
				<dd><?php echo esc_html( sprintf( '%d/%d', $presentation['pages_available'], $presentation['pages_total'] ) ); ?></dd>
			</dl>

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
				<?php if ( current_user_can( self::PRODUCT_CAPABILITY ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit.php?post_type=graha_product' ) ); ?>"><strong><?php echo esc_html__( 'Produk Graha', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Kelola produk melalui editor WordPress native.', 'graha-selang' ); ?></span></a>
				<?php endif; ?>
				<?php if ( current_user_can( self::TAXONOMY_CAPABILITY ) ) : ?>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=graha_product_category&post_type=graha_product' ) ); ?>"><strong><?php echo esc_html__( 'Kategori Produk', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Kelola kategori produk native.', 'graha-selang' ); ?></span></a>
					<a class="graha-admin-overview__card" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=graha_product_brand&post_type=graha_product' ) ); ?>"><strong><?php echo esc_html__( 'Merek', 'graha-selang' ); ?></strong><span><?php echo esc_html__( 'Kelola merek produk yang telah diverifikasi.', 'graha-selang' ); ?></span></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/** Render one compact factual readiness badge. */
	private function render_status_badge( $ready, $ok_label, $bad_label ) {
		?><span class="graha-status-badge <?php echo $ready ? 'graha-status-badge--ok' : 'graha-status-badge--warn'; ?>"><?php echo esc_html( $ready ? $ok_label : $bad_label ); ?></span><?php
	}

	/** @return array<string,mixed> */
	private function presentation_status() {
		$front_template   = dirname( __DIR__ ) . '/templates/front-page.php';
		$product_template = dirname( __DIR__ ) . '/templates/product.php';
		$show_on_front    = (string) get_option( 'show_on_front', 'posts' );
		$asset_status     = $this->assets->canonical_asset_status();

		$slugs = array( 'home', 'about-us', 'layanan-kami', 'contact-us', 'request-quote' );
		$available = 0;
		foreach ( $slugs as $slug ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( $page instanceof \WP_Post && 'publish' === $page->post_status ) {
				$available++;
			}
		}

		return array(
			'version'                     => Kernel::VERSION,
			'ownership_active'            => is_readable( $front_template ),
			'logo_ready'                  => ! empty( $asset_status['wordmark'] ),
			'favicons_ready'              => ! empty( $asset_status['favicons'] ),
			'product_presentation_active' => is_readable( $product_template ),
			'mode_label'                  => 'page' === $show_on_front ? __( 'Halaman statis', 'graha-selang' ) : __( 'Pos terbaru', 'graha-selang' ),
			'pages_available'             => $available,
			'pages_total'                 => count( $slugs ),
		);
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

	/** @return ProductCatalogMigration */
	private function get_migration() {
		if ( ! $this->migration ) {
			require_once __DIR__ . '/ProductCatalogMigration.php';
			$this->migration = new ProductCatalogMigration( dirname( __DIR__ ) . '/graha-selang.php' );
		}
		return $this->migration;
	}
}
