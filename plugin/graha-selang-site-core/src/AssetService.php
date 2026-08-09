<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class AssetService {
	const TOKENS_STYLE          = 'graha-selang-tokens';
	const FOUNDATION_STYLE      = 'graha-selang-foundation';
	const NAVIGATION_STYLE      = 'graha-selang-navigation';
	const SHELL_STYLE           = 'graha-selang-shell';
	const NAVIGATION_SCRIPT     = 'graha-selang-navigation';
	const ADMIN_OVERVIEW_STYLE  = 'graha-selang-admin-overview';
	const ADMIN_MIGRATION_STYLE = 'graha-selang-admin-migration';
	const ADMIN_MIGRATION_SCRIPT = 'graha-selang-admin-migration';

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

	/** @return void */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_assets' ), 5 );
	}

	/** @return void */
	public function register_public_assets() {
		wp_register_style( self::TOKENS_STYLE, $this->base_url . 'assets/css/tokens.css', array(), $this->version );
		wp_register_style( self::FOUNDATION_STYLE, $this->base_url . 'assets/css/foundation.css', array( self::TOKENS_STYLE ), $this->version );
		wp_register_style( self::NAVIGATION_STYLE, $this->base_url . 'assets/css/navigation.css', array( self::FOUNDATION_STYLE ), $this->version );
		wp_register_style( self::SHELL_STYLE, $this->base_url . 'assets/css/shell.css', array( self::FOUNDATION_STYLE, self::NAVIGATION_STYLE ), $this->version );
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
