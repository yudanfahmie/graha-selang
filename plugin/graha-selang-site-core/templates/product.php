<?php
/** Graha-owned document renderer for native product archive/single/taxonomy routes. */
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'graha-product-document' ); ?>>
<?php
wp_body_open();
do_action( 'graha_selang_render_product_view' );
wp_footer();
?>
</body>
</html>
