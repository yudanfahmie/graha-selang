<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class Kernel {
	/**
	 * Single authoritative runtime version. The plugin header docblock in
	 * graha-selang.php carries its own literal only because WordPress
	 * requires that comment to be statically parseable; every other
	 * consumer (asset cache-busting, etc.) reads this constant so there is
	 * exactly one place code has to change per release. Keep both in sync;
	 * tests/version-consistency.php guards against them drifting apart.
	 */
	const VERSION = '0.7.3';

	/** @var string */
	private $plugin_file;

	/** @var array<int, object> */
	private $services = array();

	/** @param string $plugin_file Main plugin file. */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/** Compose and register first-party owners/collaborators once. */
	public function boot() {
		if ( ! empty( $this->services ) ) return;

		$assets     = new AssetService( $this->plugin_file, self::VERSION );
		$navigation = new NavigationService();
		$templates  = new TemplateService( $assets, $navigation );

		$this->services = array(
			new ProductContentService(),
			$assets,
			$navigation,
			$templates,
			new ProductPresentation( $templates, $assets ),
			new AdminService( $assets ),
			new SiteLifecycleService(),
		);

		foreach ( $this->services as $service ) $service->register();
	}

	/** Register rewrite owners before the lifecycle activation flush. */
	public static function activate() {
		$product_content = new ProductContentService();
		$product_content->register_content_model();

		$lifecycle = new SiteLifecycleService();
		$lifecycle->activate();
	}

	/** Deactivation leaves provisioned content and rewrites untouched. */
	public static function deactivate() {
		$lifecycle = new SiteLifecycleService();
		$lifecycle->deactivate();
	}
}
