<?php
/** Phase D1 Home product-storytelling regression contract. */

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['products'] = array();
$GLOBALS['pages'] = array(
	'contact-us'    => (object) array( 'ID' => 201, 'post_name' => 'contact-us', 'post_status' => 'publish' ),
	'layanan-kami'  => (object) array( 'ID' => 202, 'post_name' => 'layanan-kami', 'post_status' => 'publish' ),
	'about-us'      => (object) array( 'ID' => 203, 'post_name' => 'about-us', 'post_status' => 'publish' ),
	'request-quote' => (object) array( 'ID' => 204, 'post_name' => 'request-quote', 'post_status' => 'publish' ),
);

function add_action() {}
function add_filter() {}
function remove_filter() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function is_admin() { return false; }
function is_front_page() { return true; }
function is_singular() { return false; }
function in_the_loop() { return true; }
function is_main_query() { return true; }
function post_type_exists( $type ) { return 'graha_product' === $type; }
function get_posts() { return array_keys( $GLOBALS['products'] ); }
function get_post_meta( $id, $key, $single = true ) { return isset( $GLOBALS['products'][ $id ][ $key ] ) ? $GLOBALS['products'][ $id ][ $key ] : ''; }
function get_the_ID() { return 0; }
function get_the_title( $id = 0 ) { return isset( $GLOBALS['products'][ $id ] ) ? $GLOBALS['products'][ $id ]['name'] : ''; }
function get_permalink( $target ) { return is_object( $target ) ? 'https://example.test/' . $target->post_name . '/' : 'https://example.test/product/' . (int) $target . '/'; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return isset( $GLOBALS['pages'][ $slug ] ) ? $GLOBALS['pages'][ $slug ] : null; }
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function wp_kses_post( $value ) { return (string) $value; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ); }
function __( $value ) { return $value; }
function esc_html__( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function get_nav_menu_locations() { return array(); }
function absint( $value ) { return abs( (int) $value ); }
function wp_get_nav_menu_items() { return array(); }
function register_nav_menus() {}
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_bloginfo() { return 'Graha Selang'; }
function get_custom_logo() { return ''; }
function get_queried_object_id() { return 0; }
function get_post_field() { return ''; }
function apply_filters( $hook, $value ) { return $value; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function ok( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}

$group_keys = array(
	'hydraulic_anchor',
	'industrial_anchor',
	'ducting_support',
	'pvc_support',
	'fittings_support',
	'cng_specialist',
);
$id = 1;
foreach ( $group_keys as $group ) {
	for ( $i = 1; $i <= 5; $i++ ) {
		$GLOBALS['products'][ $id ] = array(
			'name'              => $group . ' product ' . $i,
			'_graha_home_group' => $group,
		);
		$id++;
	}
}

$plugin = dirname( __DIR__ ) . '/plugin/graha-selang-site-core';
$assets = new \GrahaSelang\AssetService( $plugin . '/graha-selang.php', '0.7.6' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$html = $templates->enhance_native_content( '' );

ok( false !== strpos( $html, 'graha-home-orientation' ), 'buyer orientation section renders immediately below Hero composition' );
ok( false !== strpos( $html, 'graha-home-anchors' ) && false !== strpos( $html, 'graha-section--brand-soft' ), 'anchor families own a full-width brand-soft zone' );
ok( false !== strpos( $html, 'graha-home-support' ), 'support families own a separate full-width zone' );
ok( false !== strpos( $html, 'graha-home-specialist' ) && false !== strpos( $html, 'graha-section--contrast' ), 'CNG owns a distinct specialist contrast zone' );
ok( 6 <= substr_count( $html, 'class="graha-section ' ), 'post-Hero journey uses repeated full-width section bands instead of one page section stack' );
ok( substr_count( $html, 'graha-home-family-card--anchor' ) === 2, 'exactly two anchor family cards render when native data exists' );
ok( substr_count( $html, 'graha-home-family-card--support' ) === 3, 'exactly three support family cards render when native data exists' );

foreach ( array( 'hydraulic-hose.svg', 'industrial-hose.svg', 'ducting-hose.svg', 'pvc-hose.svg', 'fittings-couplings.svg', 'cng-hose.svg' ) as $svg ) {
	ok( false !== strpos( $html, $svg ), "category illustration is wired through the Home presentation: {$svg}" );
}
ok( 6 === substr_count( $html, 'loading="lazy"' ), 'all six below-fold category illustrations are lazy' );
ok( 6 === substr_count( $html, 'width="640"' ) && 6 === substr_count( $html, 'height="480"' ), 'all six category illustrations preserve 640x480 intrinsic dimensions' );
ok( 18 === substr_count( $html, 'href="https://example.test/product/' ), 'Home limits every six family groups to three representative native products' );
ok( false === strpos( $html, '<h1' ), 'post-Hero D1 composition introduces no H1' );

$hero = (string) file_get_contents( $plugin . '/templates/parts/home-hero.php' );
$content_template = (string) file_get_contents( $plugin . '/templates/home/content.php' );
$template_service = (string) file_get_contents( $plugin . '/src/TemplateService.php' );
$home_css = (string) file_get_contents( $plugin . '/assets/css/home.css' );
ok( 1 === substr_count( $hero, '<h1' ) && 0 === substr_count( $content_template, '<h1' ), 'Hero remains the only intended H1 owner' );
ok( false !== strpos( $hero, 'loading="eager"' ) && false !== strpos( $hero, 'fetchpriority="high"' ), 'Hero remains the only eager/high-priority illustration path' );
ok( false === strpos( $template_service, '<div class="graha-container graha-container--wide graha-stack--large"><article class="graha-page graha-page--home">' ), 'front-page shell no longer wraps the whole post-Hero journey in one shared wide container' );
foreach ( array( 'wp_insert_post', 'wp_update_post', 'wp_set_object_terms', 'update_post_meta', 'add_post_meta', 'delete_post_meta' ) as $mutation ) {
	ok( false === strpos( $template_service . $content_template, $mutation ), "D1 presentation performs no product mutation: {$mutation}" );
}
ok( false === preg_match( '/https?:\/\//', $home_css ), 'D1 Home CSS introduces no external dependency' );

$cng_ids = array();
foreach ( $GLOBALS['products'] as $product_id => $product ) {
	if ( 'cng_specialist' === $product['_graha_home_group'] ) $cng_ids[] = $product_id;
}
foreach ( $cng_ids as $product_id ) unset( $GLOBALS['products'][ $product_id ] );
$templates_without_cng = new \GrahaSelang\TemplateService( $assets, $nav );
$html_without_cng = $templates_without_cng->enhance_native_content( '' );
ok( false === strpos( $html_without_cng, 'graha-home-specialist' ), 'specialist zone is omitted when no real native CNG products exist' );
ok( false === strpos( $html_without_cng, 'cng-hose.svg' ), 'CNG illustration is omitted with its unavailable native group' );

echo "Home product storytelling checks passed.\n";
