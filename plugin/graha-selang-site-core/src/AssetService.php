<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AssetService {
	const TOKENS_STYLE            = 'graha-selang-tokens';
	const FONTS_STYLE             = 'graha-selang-fonts';
	const FOUNDATION_STYLE        = 'graha-selang-foundation';
	const NAVIGATION_STYLE        = 'graha-selang-navigation';
	const SHELL_STYLE             = 'graha-selang-shell';
	const HOME_STYLE              = 'graha-selang-home';
	const NAVIGATION_SCRIPT       = 'graha-selang-navigation';
	const ADMIN_OVERVIEW_STYLE    = 'graha-selang-admin-overview';
	const ADMIN_MIGRATION_STYLE   = 'graha-selang-admin-migration';
	const ADMIN_MIGRATION_SCRIPT  = 'graha-selang-admin-migration';
	const WORDMARK_RELATIVE_PATH  = 'assets/images/graha-selang-logo-text.svg';
	const MARK_RELATIVE_PATH      = 'assets/images/graha-selang-logo.svg';
	const ILLUSTRATION_RELATIVE_PATH = 'assets/images/illustrations/';
	/** Primary self-hosted font subset: preloaded so the swap window stays short. */
	const FONT_PRELOAD_RELATIVE_PATH = 'assets/fonts/instrument-sans-latin.woff2';

	/** @var string */
	private $base_url;

	/** @var string */
	private $base_path;

	/** @var string */
	private $version;

	/**
	 * @param string $plugin_file Main plugin file.
	 * @param string $version Plugin version.
	 */
	public function __construct( $plugin_file, $version ) {
		$this->base_url  = plugin_dir_url( $plugin_file );
		$this->base_path = function_exists( 'plugin_dir_path' ) ? plugin_dir_path( $plugin_file ) : rtrim( dirname( $plugin_file ), '/\\' ) . '/';
		$this->version   = $version;
	}

	/** @return void */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_assets' ), 5 );
		add_action( 'wp_head', array( $this, 'render_canonical_favicons' ), 10 );
	}

	/** @return void */
	public function register_public_assets() {
		wp_register_style( self::FONTS_STYLE, $this->base_url . 'assets/css/fonts.css', array(), $this->version );
		wp_register_style( self::TOKENS_STYLE, $this->base_url . 'assets/css/tokens.css', array( self::FONTS_STYLE ), $this->version );
		wp_register_style( self::FOUNDATION_STYLE, $this->base_url . 'assets/css/foundation.css', array( self::TOKENS_STYLE ), $this->version );
		wp_register_style( self::NAVIGATION_STYLE, $this->base_url . 'assets/css/navigation.css', array( self::FOUNDATION_STYLE ), $this->version );
		wp_register_style( self::SHELL_STYLE, $this->base_url . 'assets/css/shell.css', array( self::FOUNDATION_STYLE, self::NAVIGATION_STYLE ), $this->version );
		wp_register_style( self::HOME_STYLE, $this->base_url . 'assets/css/home.css', array( self::SHELL_STYLE ), $this->version );
		wp_register_script( self::NAVIGATION_SCRIPT, $this->base_url . 'assets/js/navigation.js', array(), $this->version, true );
	}

	/** @return void */
	public function enqueue_foundation() {
		$this->register_public_assets();
		wp_enqueue_style( self::TOKENS_STYLE );
		wp_enqueue_style( self::FOUNDATION_STYLE );
	}

	/** @return void */
	public function enqueue_navigation() {
		$this->register_public_assets();
		wp_enqueue_style( self::TOKENS_STYLE );
		wp_enqueue_style( self::FOUNDATION_STYLE );
		wp_enqueue_style( self::NAVIGATION_STYLE );
		wp_enqueue_script( self::NAVIGATION_SCRIPT );
	}

	/** @return void */
	public function enqueue_shell() {
		$this->register_public_assets();
		wp_enqueue_style( self::TOKENS_STYLE );
		wp_enqueue_style( self::FOUNDATION_STYLE );
		wp_enqueue_style( self::NAVIGATION_STYLE );
		wp_enqueue_style( self::SHELL_STYLE );
		wp_enqueue_script( self::NAVIGATION_SCRIPT );
	}

	/** Front-page-only composition layered on the established shell chain. */
	public function enqueue_home() {
		$this->enqueue_shell();
		wp_enqueue_style( self::HOME_STYLE );
	}

	/** Canonical plugin-owned Graha wordmark URL. */
	public function canonical_wordmark_url() {
		return $this->base_url . self::WORDMARK_RELATIVE_PATH;
	}

	/** Canonical plugin-owned Graha mark URL. */
	public function canonical_mark_url() {
		return $this->base_url . self::MARK_RELATIVE_PATH;
	}

	/** Backward-compatible helper name; canonical ownership is always the wordmark. */
	public function bundled_logo_url() {
		return $this->canonical_wordmark_url();
	}

	/** URL for a committed canonical image derivative. */
	public function image_url( $filename ) {
		return $this->base_url . 'assets/images/' . ltrim( (string) $filename, '/' );
	}

	/** Filesystem path for a committed canonical image derivative. */
	public function image_path( $filename ) {
		return $this->base_path . 'assets/images/' . ltrim( (string) $filename, '/' );
	}

	/** Canonical URL for one source-controlled illustration basename. */
	public function illustration_url( $filename ) {
		$filename = basename( trim( (string) $filename ) );
		return '' === $filename ? '' : $this->base_url . self::ILLUSTRATION_RELATIVE_PATH . rawurlencode( $filename );
	}

	/** Canonical filesystem path for one source-controlled illustration basename. */
	public function illustration_path( $filename ) {
		$filename = basename( trim( (string) $filename ) );
		return '' === $filename ? '' : $this->base_path . self::ILLUSTRATION_RELATIVE_PATH . $filename;
	}

	/** URL for the preloaded self-hosted Instrument Sans subset. */
	public function font_preload_url() {
		return $this->base_url . self::FONT_PRELOAD_RELATIVE_PATH;
	}

	/**
	 * Preload the primary (latin) Instrument Sans subset so the browser starts
	 * fetching it in parallel with CSS parsing instead of discovering it only
	 * after fonts.css loads -- this is what keeps the font-display:swap window
	 * short and avoids a visible reflow/flash once the webfont is ready. Kept
	 * as one wp_head-hooked emission alongside favicons rather than a second
	 * hook registration.
	 */
	private function render_font_preload() {
		if ( ! file_exists( $this->base_path . self::FONT_PRELOAD_RELATIVE_PATH ) ) return;
		echo '<link rel="preload" href="' . esc_url( $this->font_preload_url() ) . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
	}

	/**
	 * Graha owns favicon presentation while its branded presentation layer is
	 * active. Remove WordPress' Site Icon output before its priority-99 hook,
	 * then emit one canonical set derived from the approved Graha mark.
	 */
	public function render_canonical_favicons() {
		$this->render_font_preload();

		remove_action( 'wp_head', 'wp_site_icon', 99 );

		echo '<link rel="icon" type="image/svg+xml" sizes="any" href="' . esc_url( $this->canonical_mark_url() ) . '">' . "\n";

		$icons = array(
			array( 'favicon.ico', 'icon', '', 'any' ),
			array( 'favicon-16x16.png', 'icon', 'image/png', '16x16' ),
			array( 'favicon-32x32.png', 'icon', 'image/png', '32x32' ),
			array( 'icon-192.png', 'icon', 'image/png', '192x192' ),
			array( 'icon-512.png', 'icon', 'image/png', '512x512' ),
			array( 'apple-touch-icon.png', 'apple-touch-icon', 'image/png', '180x180' ),
		);
		foreach ( $icons as $icon ) {
			if ( ! file_exists( $this->image_path( $icon[0] ) ) ) continue;
			echo '<link rel="' . esc_attr( $icon[1] ) . '"'
				. ( '' !== $icon[2] ? ' type="' . esc_attr( $icon[2] ) . '"' : '' )
				. ' sizes="' . esc_attr( $icon[3] ) . '" href="' . esc_url( $this->image_url( $icon[0] ) ) . '">' . "\n";
		}
	}

	/** Small factual deployment-readiness signal for canonical brand assets. */
	public function canonical_asset_status() {
		$favicon_files = array( 'graha-selang-logo.svg', 'favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png', 'icon-192.png', 'icon-512.png' );
		$favicons_ok = true;
		foreach ( $favicon_files as $file ) {
			if ( ! file_exists( $this->image_path( $file ) ) ) {
				$favicons_ok = false;
				break;
			}
		}
		return array(
			'wordmark' => file_exists( $this->base_path . self::WORDMARK_RELATIVE_PATH ),
			'favicons' => $favicons_ok,
		);
	}

	/** @return void */
	public function enqueue_admin_overview() {
		wp_register_style( self::ADMIN_OVERVIEW_STYLE, $this->base_url . 'assets/css/admin-overview.css', array(), $this->version );
		wp_enqueue_style( self::ADMIN_OVERVIEW_STYLE );
	}

	/**
	 * Load migration assets only on the temporary migration child screen.
	 *
	 * @param string $action Authenticated admin-AJAX action.
	 * @param string $nonce Nonce for the current authorized user.
	 * @return void
	 */
	public function enqueue_admin_migration( $action, $nonce ) {
		wp_register_style( self::ADMIN_MIGRATION_STYLE, $this->base_url . 'assets/css/admin-migration.css', array(), $this->version );
		wp_register_script( self::ADMIN_MIGRATION_SCRIPT, $this->base_url . 'assets/js/admin-migration.js', array(), $this->version, true );
		wp_localize_script(
			self::ADMIN_MIGRATION_SCRIPT,
			'GrahaProductMigration',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => (string) $action,
				'nonce'   => (string) $nonce,
			)
		);
		wp_enqueue_style( self::ADMIN_MIGRATION_STYLE );
		wp_enqueue_script( self::ADMIN_MIGRATION_SCRIPT );
	}
}
