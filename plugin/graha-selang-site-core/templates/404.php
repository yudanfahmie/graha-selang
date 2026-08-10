<?php
/**
 * Document shell for the native WordPress 404 condition. The real HTTP 404
 * status is preserved by TemplateService::output_not_found_page() (WordPress
 * itself already sends it via send_headers()); this file only owns the
 * branded HTML document wrapper, mirroring page.php.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'graha-site-body' ); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) wp_body_open(); ?>
<?php do_action( \GrahaSelang\TemplateService::NOT_FOUND_HOOK ); ?>
<?php wp_footer(); ?>
</body>
</html>
