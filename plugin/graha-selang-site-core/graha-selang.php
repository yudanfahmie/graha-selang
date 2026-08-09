<?php
/**
 * Plugin Name: Graha Selang Site Core
 * Description: WordPress-native presentation foundation for Graha Selang.
 * Version: 0.5.0
 * Text Domain: graha-selang
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/src/ProductContentService.php';
require_once __DIR__ . '/src/AssetService.php';
require_once __DIR__ . '/src/NavigationService.php';
require_once __DIR__ . '/src/TemplateService.php';
require_once __DIR__ . '/src/AdminService.php';
require_once __DIR__ . '/src/Kernel.php';

add_action(
	'plugins_loaded',
	static function () {
		$kernel = new \GrahaSelang\Kernel( __FILE__, '0.5.0' );
		$kernel->boot();
	}
);
