<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class Kernel {
	/** @var string */
	private $plugin_file;

	/** @var string */
	private $version;

	/** @var array<int, object> */
	private $services = array();

	/**
	 * @param string $plugin_file Main plugin file.
	 * @param string $version Plugin version.
	 */
	public function __construct( $plugin_file, $version ) {
		$this->plugin_file = $plugin_file;
		$this->version     = $version;
	}

	/**
	 * Compose and register first-party owners once.
	 *
	 * @return void
	 */
	public function boot() {
		if ( ! empty( $this->services ) ) {
			return;
		}

		$assets     = new AssetService( $this->plugin_file, $this->version );
		$navigation = new NavigationService();

		$this->services = array(
			new ProductContentService(),
			$assets,
			$navigation,
			new TemplateService( $assets, $navigation ),
			new AdminService( $assets ),
		);

		foreach ( $this->services as $service ) {
			$service->register();
		}
	}
}
