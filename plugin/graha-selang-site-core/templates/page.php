<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'graha-site-body' ); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) wp_body_open(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
	do_action( \GrahaSelang\TemplateService::STATIC_PAGE_HOOK );
endwhile; endif; ?>
<?php wp_footer(); ?>
</body>
</html>
