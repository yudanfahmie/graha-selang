<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AssetService {
	const TOKENS_STYLE         = 'graha-selang-tokens';
	const FOUNDATION_STYLE     = 'graha-selang-foundation';
	const NAVIGATION_STYLE     = 'graha-selang-navigation';
	const SHELL_STYLE          = 'graha-selang-shell';
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
	 * Register the public design/presentation assets. Enqueue remains opt-in.
	 *
	 * @return void
	 */
	public function register_public_assets() {
		wp_register_style(
			self::TOKENS_STYLE,
			$this->base_url . 'assets/css/tokens.css',
			array(),
			$this->version
		);

		wp_register_style(
			self::FOUNDATION_STYLE,
			$this->base_url . 'assets/css/foundation.css',
			array( self::TOKENS_STYLE ),
			$this->version
		);

		wp_register_style(
			self::NAVIGATION_STYLE,
			$this->base_url . 'assets/css/navigation.css',
			array( self::FOUNDATION_STYLE ),
			$this->version
		);

		wp_register_style(
			self::SHELL_STYLE,
			$this->base_url . 'assets/css/shell.css',
			array( self::FOUNDATION_STYLE, self::NAVIGATION_STYLE ),
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
	 * Enqueue only generic Graha UI primitives requested by a live component.
	 *
	 * @return void
	 */
	public function enqueue_foundation() {
		$this->register_public_assets();
		wp_enqueue_style( self::TOKENS_STYLE );
		wp_enqueue_style( self::FOUNDATION_STYLE );
	}

	/**
	 * Enqueue navigation assets when presentation actually renders the nav.
	 *
	 * @return void
	 */
	public function enqueue_navigation() {
		$this->register_public_assets();
		wp_enqueue_style( self::TOKENS_STYLE );
		wp_enqueue_style( self::FOUNDATION_STYLE );
		wp_enqueue_style( self::NAVIGATION_STYLE );
		wp_enqueue_script( self::NAVIGATION_SCRIPT );
	}

	/**
	 * Enqueue the complete semantic shell bundle on explicit presentation use.
	 *
	 * @return void
	 */
	public function enqueue_shell() {
		$this->register_public_assets();
		wp_enqueue_style( self::TOKENS_STYLE );
		wp_enqueue_style( self::FOUNDATION_STYLE );
		wp_enqueue_style( self::NAVIGATION_STYLE );
		wp_enqueue_style( self::SHELL_STYLE );
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
