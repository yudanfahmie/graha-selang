<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__ ) . '/templates/parts/composition-helpers.php';

final class TemplateService {
	const PREPARE_HOOK = 'graha_selang_prepare_page';
	const RENDER_HOOK = 'graha_selang_render_page';
	const BREADCRUMB_HOOK = 'graha_selang_render_breadcrumbs';
	const FRONT_PAGE_HOOK = 'graha_selang_render_front_page';
	const STATIC_PAGE_HOOK = 'graha_selang_render_static_page';
	const SOURCE_META = '_graha_source_identity';
	const HOME_GROUP_META = '_graha_home_group';
	const PRODUCT_POST_TYPE = 'graha_product';
	const FAMILIES = array( 'home','product_archive','product_category','product_single','application','brand','about','service','contact','technical_rfq','article','legal','search','not_found' );
	const HOME_GROUPS = array(
		'hydraulic_anchor' => array( 'label' => 'Hydraulic Hose / MORGEN', 'priority' => 'anchor' ),
		'industrial_anchor' => array( 'label' => 'Industrial Hose & Assembly / HAMMER + SUNFLEX', 'priority' => 'anchor' ),
		'ducting_support' => array( 'label' => 'Ducting Hose', 'priority' => 'support' ),
		'pvc_support' => array( 'label' => 'PVC Spiral / Spring / Suction Hose', 'priority' => 'support' ),
		'fittings_support' => array( 'label' => 'Fittings / Couplings / Accessories', 'priority' => 'support' ),
		'cng_specialist' => array( 'label' => 'CNG / High-pressure Gas Hose', 'priority' => 'specialist' ),
	);
	/** Canonical structural Page slug -> presentation family. Unmapped Pages default to 'legal'. */
	const STATIC_PAGE_FAMILIES = array(
		'about-us' => 'about',
		'layanan-kami' => 'service',
		'contact-us' => 'contact',
		'request-quote' => 'technical_rfq',
	);

	private $assets;
	private $navigation;
	private $native_home_groups = null;

	public function __construct( AssetService $assets, NavigationService $navigation ) {
		$this->assets = $assets;
		$this->navigation = $navigation;
	}

	public function register() {
		add_action( self::PREPARE_HOOK, array( $this, 'prepare_page' ) );
		add_action( self::RENDER_HOOK, array( $this, 'output_page' ), 10, 2 );
		add_action( self::BREADCRUMB_HOOK, array( $this, 'output_breadcrumbs' ), 10, 2 );
		add_action( self::FRONT_PAGE_HOOK, array( $this, 'output_front_page' ) );
		add_action( self::STATIC_PAGE_HOOK, array( $this, 'output_static_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'prepare_native_presentation' ), 20 );
		add_filter( 'template_include', array( $this, 'resolve_native_template' ), 99 );
		add_filter( 'the_content', array( $this, 'enhance_native_content' ), 30 );
	}

	/**
	 * Graha owns presentation for whatever WordPress itself resolves as the
	 * public front page -- the posts index, the canonical static Home Page,
	 * or any other Page an owner has configured as the site front. Reading
	 * Settings decide *what content* backs the front page; they never gate
	 * *whether* Graha's own shell renders it. See resolve_native_template().
	 */
	public function prepare_native_presentation() {
		if ( function_exists( 'is_admin' ) && is_admin() ) return;
		if ( is_front_page() ) { $this->assets->enqueue_home(); return; }
		if ( is_singular( 'page' ) ) { $this->assets->enqueue_shell(); return; }
		if ( is_singular( array( 'post', self::PRODUCT_POST_TYPE ) ) ) $this->assets->enqueue_foundation();
	}

	/**
	 * Resolve the real WordPress front page (regardless of latest-posts vs
	 * static-Page Reading Settings, and regardless of which Page is chosen)
	 * and any singular Page to their plugin-owned document shells.
	 * Posts/products keep the active theme's own template and only receive
	 * content-region enhancement.
	 */
	public function resolve_native_template( $template ) {
		if ( function_exists( 'is_admin' ) && is_admin() ) return $template;
		if ( is_front_page() ) {
			$front = dirname( __DIR__ ) . '/templates/front-page.php';
			return is_readable( $front ) ? $front : $template;
		}
		if ( is_singular( 'page' ) ) {
			$page_template = dirname( __DIR__ ) . '/templates/page.php';
			return is_readable( $page_template ) ? $page_template : $template;
		}
		return $template;
	}

	/** Output the Graha shell for the actual WordPress front page. */
	public function output_front_page() {
		echo $this->render_front_page_shell( $this->front_page_editor_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/** Output the Graha shell for any native singular Page (About/Services/Contact/RFQ/etc). */
	public function output_static_page() {
		echo $this->render_static_page(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function enhance_native_content( $content ) {
		if ( ( function_exists( 'is_admin' ) && is_admin() ) || ! in_the_loop() || ! is_main_query() ) return $content;
		if ( is_front_page() ) return $this->render_native_home_content( $content );
		if ( is_singular( 'page' ) ) $content = $this->bootstrap_page_fallback( $content );
		if ( is_singular( array( 'page', 'post', self::PRODUCT_POST_TYPE ) ) ) return '<div class="graha-ui graha-native-content graha-stack">' . $this->render_native_breadcrumbs() . wp_kses_post( $content ) . '</div>';
		return $content;
	}

	public function prepare_page( $family ) {
		if ( $this->is_supported_family( $family ) ) $this->assets->enqueue_shell();
	}

	public function output_page( $family, $context = array() ) {
		if ( ! is_array( $context ) ) $context = array();
		echo $this->render_page( $family, $context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function render_page( $family, array $context ) {
		$family = sanitize_key( (string) $family );
		$heading = isset( $context['heading'] ) ? trim( wp_strip_all_tags( (string) $context['heading'] ) ) : '';
		if ( ! $this->is_supported_family( $family ) || '' === $heading ) return '';
		$is_home = 'home' === $family;
		if ( $is_home && ! $this->home_context_is_ready( $context ) ) return '';
		$main = $this->render_main( $family, $context, $is_home );
		if ( '' === $main ) return '';
		$site_name = trim( (string) get_bloginfo( 'name' ) );
		$logo = function_exists( 'get_custom_logo' ) ? (string) get_custom_logo() : '';
		$brand_logo_url = $this->assets->bundled_logo_url();
		$nav = $this->navigation->render_primary();
		$footer = isset( $context['footer_html'] ) ? wp_kses_post( (string) $context['footer_html'] ) : '';
		$footer_links = $this->footer_link_context();
		$rfq_url = $footer_links['rfq_url'];
		ob_start();
		include dirname( __DIR__ ) . '/templates/shell.php';
		return (string) ob_get_clean();
	}

	public function output_breadcrumbs( $items = array(), $current_label = '' ) {
		if ( ! is_array( $items ) ) $items = array();
		echo $this->render_breadcrumbs( $items, $current_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function render_breadcrumbs( array $items, $current_label = '' ) {
		$items = $this->normalize_breadcrumbs( $items, $current_label );
		if ( count( $items ) < 2 ) return '';
		ob_start(); ?><nav class="graha-breadcrumbs" aria-label="<?php echo esc_attr__( 'Jejak navigasi', 'graha-selang' ); ?>"><ol><?php foreach ( $items as $i => $item ) : ?><li><?php if ( $i < count( $items ) - 1 && '' !== $item['url'] ) : ?><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a><?php else : ?><span<?php echo $i === count( $items ) - 1 ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></span><?php endif; ?></li><?php endforeach; ?></ol></nav><?php
		return (string) ob_get_clean();
	}

	private function render_native_breadcrumbs() {
		$post_id = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
		if ( ! $post_id ) return '';
		$current = trim( wp_strip_all_tags( (string) get_the_title( $post_id ) ) );
		if ( '' === $current ) return '';
		$items = array();
		if ( is_singular( 'page' ) ) {
			$items = $this->page_ancestor_breadcrumb_items( $post_id );
		} elseif ( is_singular( self::PRODUCT_POST_TYPE ) ) {
			$archive = get_post_type_archive_link( self::PRODUCT_POST_TYPE );
			if ( $archive ) $items[] = array( 'label' => __( 'Produk', 'graha-selang' ), 'url' => $archive );
		}
		return $this->render_breadcrumbs( $items, $current );
	}

	/**
	 * Native Page ancestor trail as breadcrumb items (excludes the current page).
	 *
	 * @param int $post_id Current Page ID.
	 * @return array<int,array<string,string>>
	 */
	private function page_ancestor_breadcrumb_items( $post_id ) {
		$items = array();
		if ( ! function_exists( 'get_post_ancestors' ) ) return $items;
		$ancestors = array_reverse( array_map( 'intval', (array) get_post_ancestors( $post_id ) ) );
		foreach ( $ancestors as $ancestor_id ) {
			if ( $ancestor_id < 1 || 'publish' !== get_post_status( $ancestor_id ) ) continue;
			$label = trim( wp_strip_all_tags( (string) get_the_title( $ancestor_id ) ) );
			$url = get_permalink( $ancestor_id );
			if ( '' !== $label && $url ) $items[] = array( 'label' => $label, 'url' => $url );
		}
		return $items;
	}

	private function render_main( $family, array $context, $is_home ) {
		$heading = trim( wp_strip_all_tags( (string) $context['heading'] ) );
		$eyebrow = isset( $context['eyebrow'] ) ? trim( wp_strip_all_tags( (string) $context['eyebrow'] ) ) : '';
		$lead = isset( $context['lead_html'] ) ? wp_kses_post( (string) $context['lead_html'] ) : '';
		$content = isset( $context['content_html'] ) ? wp_kses_post( (string) $context['content_html'] ) : '';
		$crumbs = $is_home ? '' : $this->render_breadcrumbs( isset( $context['breadcrumbs'] ) && is_array( $context['breadcrumbs'] ) ? $context['breadcrumbs'] : array(), $heading );
		ob_start(); ?><main id="graha-main" class="graha-site-main graha-site-main--<?php echo esc_attr( $family ); ?>"><div class="graha-container graha-container--wide graha-stack--large"><?php echo $crumbs; // phpcs:ignore ?><article class="graha-page graha-page--<?php echo esc_attr( $family ); ?>"><header class="graha-page-header"><?php if ( '' !== $eyebrow ) : ?><p class="graha-page-header__eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?><h1><?php echo esc_html( $heading ); ?></h1><?php if ( '' !== $lead ) : ?><div class="graha-page-header__lead"><?php echo $lead; // phpcs:ignore ?></div><?php endif; ?></header><?php if ( $is_home ) : echo $this->render_home_sections( $context['sections'] ); elseif ( '' !== $content ) : ?><div class="graha-page__content graha-stack"><?php echo $content; // phpcs:ignore ?></div><?php endif; ?></article></div></main><?php
		return (string) ob_get_clean();
	}

	private function render_home_sections( array $sections ) {
		ob_start(); foreach ( $sections as $section ) {
			if ( ! is_array( $section ) ) continue;
			$kind = isset( $section['kind'] ) ? sanitize_key( (string) $section['kind'] ) : '';
			$heading = isset( $section['heading'] ) ? trim( wp_strip_all_tags( (string) $section['heading'] ) ) : '';
			$content = isset( $section['content_html'] ) ? wp_kses_post( (string) $section['content_html'] ) : '';
			if ( '' === $heading || '' === trim( wp_strip_all_tags( $content ) ) ) continue;
			?><section class="graha-page-section graha-page-section--<?php echo esc_attr( $kind ); ?>"><h2 class="graha-page-section__heading"><?php echo esc_html( $heading ); ?></h2><div class="graha-page-section__content graha-stack"><?php echo $content; // phpcs:ignore ?></div></section><?php
		}
		return (string) ob_get_clean();
	}

	private function home_context_is_ready( array $context ) {
		if ( empty( $context['sections'] ) || ! is_array( $context['sections'] ) ) return false;
		$required = array( 'hero','products','proof','consultation' ); $ready = array();
		foreach ( $context['sections'] as $section ) {
			if ( ! is_array( $section ) ) continue;
			$kind = isset( $section['kind'] ) ? sanitize_key( (string) $section['kind'] ) : '';
			$heading = isset( $section['heading'] ) ? trim( wp_strip_all_tags( (string) $section['heading'] ) ) : '';
			$content = isset( $section['content_html'] ) ? trim( wp_strip_all_tags( (string) $section['content_html'] ) ) : '';
			$plain = strtolower( $heading . ' ' . $content );
			if ( in_array( $kind, $required, true ) && strlen( $heading ) >= 3 && strlen( $content ) >= 20 && ! preg_match( '/\b(?:lorem|ipsum|dummy|placeholder|sample|contoh\s+dummy|konten\s+uji)\b/i', $plain ) ) $ready[ $kind ] = true;
		}
		return count( $ready ) === count( $required );
	}

	private function normalize_breadcrumbs( array $items, $current_label ) {
		$out = array( array( 'label' => __( 'Beranda', 'graha-selang' ), 'url' => home_url( '/' ) ) );
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) continue;
			$label = isset( $item['label'] ) ? trim( wp_strip_all_tags( (string) $item['label'] ) ) : '';
			$url = isset( $item['url'] ) ? esc_url( (string) $item['url'] ) : '';
			if ( '' !== $label && __( 'Beranda', 'graha-selang' ) !== $label ) $out[] = array( 'label' => $label, 'url' => $url );
		}
		$current = trim( wp_strip_all_tags( (string) $current_label ) );
		$last = end( $out ); $last_label = is_array( $last ) ? $last['label'] : '';
		if ( '' !== $current && $current !== $last_label ) $out[] = array( 'label' => $current, 'url' => '' ); elseif ( count( $out ) > 1 ) $out[ count( $out ) - 1 ]['url'] = '';
		return $out;
	}

	private function is_supported_family( $family ) { return in_array( sanitize_key( (string) $family ), self::FAMILIES, true ); }

	/** Presentation family for a native Page slug; unmapped Pages get the generic 'legal' family. */
	private function family_for_page_slug( $slug ) {
		return isset( self::STATIC_PAGE_FAMILIES[ $slug ] ) ? self::STATIC_PAGE_FAMILIES[ $slug ] : 'legal';
	}

	/** Short, non-factual orientation kicker for a static Page family. */
	private function static_page_eyebrow( $family ) {
		$map = array(
			'about' => __( 'Tentang Kami', 'graha-selang' ),
			'service' => __( 'Layanan', 'graha-selang' ),
			'contact' => __( 'Kontak', 'graha-selang' ),
			'technical_rfq' => __( 'Konsultasi Teknis', 'graha-selang' ),
		);
		return isset( $map[ $family ] ) ? $map[ $family ] : '';
	}

	/** Short, non-factual orientation lead for a static Page family. */
	private function static_page_lead( $family ) {
		$map = array(
			'about' => __( 'Profil dan kapabilitas Graha Selang.', 'graha-selang' ),
			'service' => __( 'Layanan teknis Graha Selang untuk kebutuhan hidrolik dan industri Anda.', 'graha-selang' ),
			'contact' => __( 'Hubungi tim Graha Selang untuk kebutuhan produk atau konsultasi teknis.', 'graha-selang' ),
			'technical_rfq' => __( 'Ajukan permintaan penawaran atau konsultasi kebutuhan teknis Anda.', 'graha-selang' ),
		);
		return isset( $map[ $family ] ) ? '<p>' . esc_html( $map[ $family ] ) . '</p>' : '';
	}

	/** Compose the branded document shell for any native singular Page. */
	private function render_static_page() {
		$post_id = function_exists( 'get_queried_object_id' ) ? (int) get_queried_object_id() : 0;
		if ( ! $post_id || 'publish' !== get_post_status( $post_id ) ) return '';
		$heading = trim( wp_strip_all_tags( (string) get_the_title( $post_id ) ) );
		if ( '' === $heading ) return '';
		$slug = (string) get_post_field( 'post_name', $post_id );
		$family = $this->family_for_page_slug( $slug );
		$editor_html = $this->bootstrap_page_fallback( $this->rendered_editor_content( $post_id ) );
		$context = array(
			'heading' => $heading,
			'eyebrow' => $this->static_page_eyebrow( $family ),
			'lead_html' => $this->static_page_lead( $family ),
			'content_html' => $this->compose_static_page_content( $family, $editor_html ),
			'breadcrumbs' => $this->page_ancestor_breadcrumb_items( $post_id ),
		);
		return $this->render_page( $family, $context );
	}

	/**
	 * Finish each of the four core page families with its own composition
	 * instead of a single generic hero-then-content shape. The editor's own
	 * content (or the safe bootstrap fallback when it is empty) remains the
	 * authoritative body in every case; only the honest, real-destination
	 * supporting area around it differs per family.
	 *
	 * @param string $family Presentation family.
	 * @param string $editor_html Authoritative editor content (already sanitized).
	 * @return string
	 */
	private function compose_static_page_content( $family, $editor_html ) {
		$urls = $this->footer_link_context();
		ob_start();
		switch ( $family ) {
			case 'about':
				echo '<div class="graha-about-editorial graha-native-content graha-stack">' . $editor_html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->render_family_nextsteps( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'graha-about-nextsteps',
					__( 'Langkah berikutnya', 'graha-selang' ),
					array(
						array( 'icon' => 'gear', 'title' => __( 'Layanan Kami', 'graha-selang' ), 'copy' => __( 'Lihat cakupan layanan teknis Graha Selang.', 'graha-selang' ), 'url' => $urls['services_url'], 'cta' => __( 'Lihat layanan', 'graha-selang' ) ),
						array( 'icon' => 'chat', 'title' => __( 'Konsultasi Teknis', 'graha-selang' ), 'copy' => __( 'Diskusikan kebutuhan Anda dengan tim kami.', 'graha-selang' ), 'url' => $urls['rfq_url'], 'cta' => __( 'Request Quote', 'graha-selang' ) ),
					)
				);
				break;
			case 'service':
				echo '<div class="graha-service-capability graha-native-content graha-stack">' . $editor_html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->render_family_nextsteps( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'graha-service-nextsteps',
					__( 'Lanjutkan', 'graha-selang' ),
					array(
						array( 'icon' => 'box', 'title' => __( 'Produk', 'graha-selang' ), 'copy' => __( 'Telusuri katalog produk yang mendukung layanan kami.', 'graha-selang' ), 'url' => $urls['products_url'], 'cta' => __( 'Lihat produk', 'graha-selang' ) ),
						array( 'icon' => 'chat', 'title' => __( 'Request Quote', 'graha-selang' ), 'copy' => __( 'Ajukan kebutuhan layanan atau produk Anda.', 'graha-selang' ), 'url' => $urls['rfq_url'], 'cta' => __( 'Buat permintaan', 'graha-selang' ) ),
					)
				);
				break;
			case 'contact':
				echo '<div class="graha-contact-card graha-native-content graha-stack">' . $editor_html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( '' !== $urls['rfq_url'] ) {
					echo '<div class="graha-contact-alternative">';
					graha_render_section_heading( '', __( 'Lebih suka konsultasi terarah?', 'graha-selang' ), __( 'Sampaikan kebutuhan teknis Anda melalui Request Quote.', 'graha-selang' ) );
					graha_render_button( __( 'Ajukan Request Quote', 'graha-selang' ), $urls['rfq_url'], 'outline' );
					echo '</div>';
				}
				break;
			case 'technical_rfq':
				echo '<div class="graha-rfq-form-region graha-native-content graha-stack">' . $editor_html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( '' !== $urls['contact_url'] ) {
					echo '<p class="graha-rfq-alternative">' . esc_html__( 'Butuh jalur lain?', 'graha-selang' ) . ' <a href="' . esc_url( $urls['contact_url'] ) . '">' . esc_html__( 'Hubungi Graha Selang', 'graha-selang' ) . '</a></p>';
				}
				break;
			default:
				echo $editor_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		return (string) ob_get_clean();
	}

	/**
	 * Honest "what next" cards for a page family: only doors that already
	 * have a real destination render, and the whole block disappears when
	 * none do -- never a placeholder heading over an empty grid.
	 *
	 * @param string $wrapper_class Distinct per-family wrapper class.
	 * @param string $heading Section heading.
	 * @param array<int,array<string,string>> $doors Candidate doors.
	 * @return string
	 */
	private function render_family_nextsteps( $wrapper_class, $heading, array $doors ) {
		$doors = array_values( array_filter( $doors, static function ( $door ) {
			return is_array( $door ) && ! empty( $door['url'] );
		} ) );
		if ( ! $doors ) return '';
		ob_start();
		echo '<div class="' . esc_attr( $wrapper_class ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		graha_render_section_heading( '', $heading );
		graha_render_discovery_grid( $doors );
		echo '</div>';
		return (string) ob_get_clean();
	}

	private function bootstrap_page_fallback( $content ) {
		if ( $this->has_meaningful_content( $content ) ) return $content;
		$post_id = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
		$slug = $post_id ? (string) get_post_field( 'post_name', $post_id ) : '';
		if ( ! in_array( $slug, array( 'about-us', 'layanan-kami', 'contact-us', 'request-quote' ), true ) ) return $content;

		$products_url = get_post_type_archive_link( self::PRODUCT_POST_TYPE );
		$services_url = $this->published_page_url( 'layanan-kami' );
		$contact_url = $this->published_page_url( 'contact-us' );
		$rfq_url = $this->published_page_url( 'request-quote' );
		ob_start();
		?><div class="graha-bootstrap-page graha-sparse-state graha-stack"><?php
		if ( 'about-us' === $slug ) : ?>
			<section><h2><?php echo esc_html__( 'Tentang Graha Selang', 'graha-selang' ); ?></h2><p><?php echo esc_html__( 'Graha Selang menyajikan jalur informasi untuk produk selang industri dan hidrolik, layanan, serta konsultasi kebutuhan teknis.', 'graha-selang' ); ?></p><?php echo $this->render_public_links( array( array( 'Produk', $products_url ), array( 'Layanan', $services_url ), array( 'Hubungi Kami', $contact_url ) ) ); // phpcs:ignore ?></section>
		<?php elseif ( 'layanan-kami' === $slug ) : ?>
			<section><h2><?php echo esc_html__( 'Layanan dan kapabilitas', 'graha-selang' ); ?></h2><p><?php echo esc_html__( 'Jelajahi informasi crimping dan assembly, custom fitting/coupling/flange, konsultasi pemilihan produk, serta penilaian repair/replacement yang dipublikasikan Graha Selang.', 'graha-selang' ); ?></p><?php echo $this->render_public_links( array( array( 'Request Quote', $rfq_url ), array( 'Hubungi Kami', $contact_url ) ) ); // phpcs:ignore ?></section>
		<?php elseif ( 'contact-us' === $slug ) : ?>
			<section><h2><?php echo esc_html__( 'Hubungi Graha Selang', 'graha-selang' ); ?></h2><p><?php echo esc_html__( 'Untuk kebutuhan produk atau aplikasi, gunakan jalur konsultasi teknis yang tersedia. Kanal kontak langsung mengikuti informasi yang dipublikasikan Graha Selang.', 'graha-selang' ); ?></p><?php echo $this->render_public_links( array( array( 'Request Quote', $rfq_url ) ) ); // phpcs:ignore ?></section>
		<?php else : ?>
			<section><h2><?php echo esc_html__( 'Konsultasi kebutuhan teknis', 'graha-selang' ); ?></h2><p><?php echo esc_html__( 'Gunakan halaman ini untuk memulai pembahasan kebutuhan produk atau aplikasi. Jika jalur permintaan teknis belum tersedia, gunakan halaman Hubungi Kami.', 'graha-selang' ); ?></p><?php echo $this->render_public_links( array( array( 'Hubungi Kami', $contact_url ), array( 'Produk', $products_url ) ) ); // phpcs:ignore ?></section>
		<?php endif; ?></div><?php
		return (string) ob_get_clean();
	}

	private function has_meaningful_content( $content ) {
		if ( '' !== trim( html_entity_decode( wp_strip_all_tags( (string) $content ), ENT_QUOTES, 'UTF-8' ) ) ) return true;
		return 1 === preg_match( '/<(?:img|picture|video|audio|iframe|form|figure|table|ul|ol)\b|\[[a-z][^\]]*\]/i', (string) $content );
	}

	private function render_public_links( array $links ) {
		$items = array();
		foreach ( $links as $link ) {
			if ( ! is_array( $link ) || empty( $link[0] ) || empty( $link[1] ) ) continue;
			$items[] = '<a href="' . esc_url( (string) $link[1] ) . '">' . esc_html( (string) $link[0] ) . '</a>';
		}
		return empty( $items ) ? '' : '<p class="graha-bootstrap-page__links">' . implode( ' · ', $items ) . '</p>';
	}

	private function get_native_home_groups() {
		if ( null !== $this->native_home_groups ) return $this->native_home_groups;
		$this->native_home_groups = array();
		if ( ! post_type_exists( self::PRODUCT_POST_TYPE ) ) return $this->native_home_groups;
		$ids = get_posts( array( 'post_type'=>self::PRODUCT_POST_TYPE,'post_status'=>'publish','fields'=>'ids','numberposts'=>80,'suppress_filters'=>true ) );
		foreach ( is_array( $ids ) ? $ids : array() as $id ) {
			$group = (string) get_post_meta( $id, self::HOME_GROUP_META, true );
			if ( ! isset( self::HOME_GROUPS[ $group ] ) ) continue;
			if ( ! isset( $this->native_home_groups[ $group ] ) ) $this->native_home_groups[ $group ] = array_merge( self::HOME_GROUPS[ $group ], array( 'products'=>array() ) );
			$this->native_home_groups[ $group ]['products'][] = array( 'id'=>(int)$id, 'name'=>get_the_title( $id ), 'url'=>get_permalink( $id ) );
		}
		foreach ( self::HOME_GROUPS as $key => $unused ) if ( empty( $this->native_home_groups[ $key ]['products'] ) ) unset( $this->native_home_groups[ $key ] );
		return $this->native_home_groups;
	}

	private function render_native_home_content( $content ) {
		$groups = $this->get_native_home_groups();
		$products_url = get_post_type_archive_link( self::PRODUCT_POST_TYPE );
		if ( ! $products_url ) $products_url = home_url( '/products/' );
		$services_url = $this->published_page_url( 'layanan-kami' );
		$contact_url = $this->published_page_url( 'contact-us' );
		$rfq_url = $this->published_page_url( 'request-quote' );
		$about_url = $this->published_page_url( 'about-us' );
		ob_start();
		include dirname( __DIR__ ) . '/templates/native-home.php';
		return (string) ob_get_clean();
	}

	private function published_page_url( $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page || 'publish' !== $page->post_status ) return '';
		return (string) get_permalink( $page );
	}

	/** Real destinations only, used by the branded global footer. Empty entries are omitted, never fabricated. */
	private function footer_link_context() {
		return array(
			'products_url' => (string) get_post_type_archive_link( self::PRODUCT_POST_TYPE ),
			'services_url' => $this->published_page_url( 'layanan-kami' ),
			'about_url'    => $this->published_page_url( 'about-us' ),
			'contact_url'  => $this->published_page_url( 'contact-us' ),
			'rfq_url'      => $this->published_page_url( 'request-quote' ),
		);
	}

	/** Editor content for any native post, rendered through normal content filters, minus our own recursive enhancement. */
	private function rendered_editor_content( $post_id ) {
		$post_id = (int) $post_id;
		if ( ! $post_id ) return '';
		$raw = (string) get_post_field( 'post_content', $post_id );
		if ( '' === trim( wp_strip_all_tags( $raw ) ) ) return '';
		remove_filter( 'the_content', array( $this, 'enhance_native_content' ), 30 );
		$rendered = apply_filters( 'the_content', $raw );
		add_filter( 'the_content', array( $this, 'enhance_native_content' ), 30 );
		return wp_kses_post( (string) $rendered );
	}

	private function front_page_editor_content() {
		$post_id = function_exists( 'get_queried_object_id' ) ? (int) get_queried_object_id() : 0;
		return $this->rendered_editor_content( $post_id );
	}

	private function render_front_page_shell( $content ) {
		$family = 'home';
		$home_content = $this->render_native_home_content( $content );
		$products_url = get_post_type_archive_link( self::PRODUCT_POST_TYPE );
		if ( ! $products_url ) $products_url = home_url( '/products/' );
		$rfq_url = $this->published_page_url( 'request-quote' );
		$hero_illustration_url = $this->assets->illustration_url( 'hero-industrial-system.svg' );
		ob_start();
		?><main id="graha-main" class="graha-site-main graha-site-main--home">
			<?php include dirname( __DIR__ ) . '/templates/parts/home-hero.php'; ?>
			<div class="graha-container graha-container--wide graha-stack--large"><article class="graha-page graha-page--home"><?php echo $home_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></article></div>
		</main><?php
		$main = (string) ob_get_clean();
		$site_name = trim( (string) get_bloginfo( 'name' ) );
		$logo = function_exists( 'get_custom_logo' ) ? (string) get_custom_logo() : '';
		$brand_logo_url = $this->assets->bundled_logo_url();
		$nav = $this->navigation->render_primary();
		$footer = '';
		$footer_links = $this->footer_link_context();
		$rfq_url = $footer_links['rfq_url'];
		ob_start();
		include dirname( __DIR__ ) . '/templates/shell.php';
		return (string) ob_get_clean();
	}
}
