<?php
/**
 * Plugin Name: Graha Selang Site Core
 * Description: WordPress-native presentation foundation for Graha Selang.
 * Version: 0.7.2
 * Text Domain: graha-selang
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/templates/parts/composition-helpers.php';
require_once __DIR__ . '/src/ProductContentService.php';
require_once __DIR__ . '/src/AssetService.php';
require_once __DIR__ . '/src/NavigationService.php';
require_once __DIR__ . '/src/TemplateService.php';
require_once __DIR__ . '/src/ProductPresentation.php';
require_once __DIR__ . '/src/AdminService.php';
require_once __DIR__ . '/src/SiteLifecycleService.php';
require_once __DIR__ . '/src/Kernel.php';

register_activation_hook( __FILE__, array( 'GrahaSelang\\Kernel', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GrahaSelang\\Kernel', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		$kernel = new \GrahaSelang\Kernel( __FILE__ );
		$kernel->boot();
	}
);
