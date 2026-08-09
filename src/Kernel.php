<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class Kernel {
	/**
	 * @var array<int, object>
	 */
	private $services = array();

	/**
	 * Compose and register first-party owners once.
	 *
	 * @return void
	 */
	public function boot() {
		if ( ! empty( $this->services ) ) {
			return;
		}

		$this->services = array(
			new AdminService(),
		);

		foreach ( $this->services as $service ) {
			$service->register();
		}
	}
}
