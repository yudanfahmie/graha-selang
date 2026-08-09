<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AssetService {
	const FOUNDATION_STYLE     = 'graha-selang-foundation';
	const NAVIGATION_STYLE     = 'graha-selang-navigation';
	const NAVIGATION_SCRIPT    = 'graha-selang-navigation';
	const ADMIN_OVERVIEW_STYLE = 'graha-selang-admin-overview';

	/** @var string */
	private $base_url;

	/** @var string */
	private $version;

	/**
	 * @param string $plugin_file Main plugin file.
	 * @param string $version Plugin version.
	 */
	public function __construct( $plugin_file, $version ) {
		$this->base_url = plugin_dir_url( $plugin_file );
		$this->version  = $version;
	}

	/**
	 * Register public assets without loading them globally.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_assets' ), 5 );
	}

	/**
	 * Register the small public foundation. Enqueue remains component-owned.
	 *
	 * @return void
	 */
	public function register_public_assets() {
		wp_register_style(
			self::FOUNDATION_STYLE,
			$this->base_url . 'assets/css/foundation.css',
			array(),
			$this->version
		);

		wp_register_style(
			self::NAVIGATION_STYLE,
			$this->base_url . 'assets/css/navigation.css',
			array( self::FOUNDATION_STYLE ),
			$this->version
		);

		wp_register_script(
			self::NAVIGATION_SCRIPT,
			$this->base_url . 'assets/js/navigation.js',
			array(),
			$this->version,
			true
		);
	}

	/**
	 * Enqueue only the generic Graha UI primitives requested by a live component.
	 *
	 * @return void
	 */
	public function enqueue_foundation() {
		$this->register_public_assets();
		wp_enqueue_style( self::FOUNDATION_STYLE );
	}

	/**
	 * Enqueue navigation assets when a future presentation owner renders the nav.
	 *
	 * @return void
	 */
	public function enqueue_navigation() {
		$this->register_public_assets();
		wp_enqueue_style( self::FOUNDATION_STYLE );
		wp_enqueue_style( self::NAVIGATION_STYLE );
		wp_enqueue_script( self::NAVIGATION_SCRIPT );
	}

	/**
	 * Enqueue the Ringkasan-only admin stylesheet on demand.
	 *
	 * @return void
	 */
	public function enqueue_admin_overview() {
		wp_register_style(
			self::ADMIN_OVERVIEW_STYLE,
			$this->base_url . 'assets/css/admin-overview.css',
			array(),
			$this->version
		);

		wp_enqueue_style( self::ADMIN_OVERVIEW_STYLE );
	}
}
