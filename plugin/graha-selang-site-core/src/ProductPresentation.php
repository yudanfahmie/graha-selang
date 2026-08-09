<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

/** Native Graha product-route context adapter; TemplateService remains shell renderer. */
final class ProductPresentation {
	const RENDER_HOOK = 'graha_selang_render_product_view';

	/** @var TemplateService */
	private $templates;

	/** @var AssetService */
	private $assets;

	public function __construct( TemplateService $templates, AssetService $assets ) {
		$this->templates = $templates;
		$this->assets    = $assets;
	}

	/** Register only native WordPress presentation hooks. */
	public function register() {
		add_action( self::RENDER_HOOK, array( $this, 'output' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'prepare_assets' ), 21 );
		add_filter( 'archive_template', array( $this, 'resolve_archive_template' ), 99 );
		add_filter( 'single_template', array( $this, 'resolve_single_template' ), 99 );
		add_filter( 'taxonomy_template', array( $this, 'resolve_taxonomy_template' ), 99 );
	}

	/** Full Graha shell assets only for the four native product route families. */
	public function prepare_assets() {
		if ( function_exists( 'is_admin' ) && is_admin() ) return;
		if ( '' !== $this->identify_family() ) $this->assets->enqueue_shell();
	}

	/** @param string $template WordPress archive template candidate. @return string */
	public function resolve_archive_template( $template ) {
		return 'product_archive' === $this->identify_family() ? $this->product_template_or( $template ) : $template;
	}

	/** @param string $template WordPress single template candidate. @return string */
	public function resolve_single_template( $template ) {
		return 'product_single' === $this->identify_family() ? $this->product_template_or( $template ) : $template;
	}

	/** @param string $template WordPress taxonomy template candidate. @return string */
	public function resolve_taxonomy_template( $template ) {
		return in_array( $this->identify_family(), array( 'product_category', 'brand' ), true ) ? $this->product_template_or( $template ) : $template;
	}

	/** Identify only routes already registered by ProductContentService. */
	public function identify_family() {
		if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( ProductContentService::POST_TYPE ) ) return 'product_archive';
		if ( function_exists( 'is_singular' ) && is_singular( ProductContentService::POST_TYPE ) ) return 'product_single';
		if ( function_exists( 'is_tax' ) && is_tax( ProductContentService::CATEGORY_TAXONOMY ) ) return 'product_category';
		if ( function_exists( 'is_tax' ) && is_tax( ProductContentService::BRAND_TAXONOMY ) ) return 'brand';
		return '';
	}

	/** @param string $fallback Theme-resolved candidate. @return string */
	private function product_template_or( $fallback ) {
		$product = dirname( __DIR__ ) . '/templates/product.php';
		return is_readable( $product ) ? $product : $fallback;
	}

	/** Render the current native product query through TemplateService. */
	public function output() {
		echo $this->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/** @return string */
	public function render() {
		$family = $this->identify_family();
		if ( '' === $family ) return '';
		if ( 'product_single' === $family ) return $this->render_single();

		$heading = __( 'Produk', 'graha-selang' );
		$eyebrow = __( 'Katalog Produk', 'graha-selang' );
		$lead    = __( 'Telusuri produk Graha Selang yang telah tersedia pada katalog.', 'graha-selang' );
		$crumbs  = array();

		if ( in_array( $family, array( 'product_category', 'brand' ), true ) ) {
			$term = function_exists( 'get_queried_object' ) ? get_queried_object() : null;
			if ( is_object( $term ) && ! empty( $term->name ) ) $heading = trim( wp_strip_all_tags( (string) $term->name ) );
			$eyebrow = 'brand' === $family ? __( 'Merek', 'graha-selang' ) : __( 'Kategori Produk', 'graha-selang' );
			$lead = 'brand' === $family
				? __( 'Produk yang terhubung dengan merek ini pada katalog Graha Selang.', 'graha-selang' )
				: __( 'Produk yang terhubung dengan kategori ini pada katalog Graha Selang.', 'graha-selang' );
			$archive = get_post_type_archive_link( ProductContentService::POST_TYPE );
			if ( $archive ) $crumbs[] = array( 'label' => __( 'Produk', 'graha-selang' ), 'url' => $archive );
		}

		return $this->templates->render_page(
			$family,
			array(
				'heading'      => $heading,
				'eyebrow'      => $eyebrow,
				'lead_html'    => '<p>' . esc_html( $lead ) . '</p>',
				'content_html' => $this->render_collection(),
				'breadcrumbs'  => $crumbs,
			)
		);
	}

	/** Current main query only; no data mutation, enrichment, or inferred taxonomy. */
	private function render_collection() {
		$query = isset( $GLOBALS['wp_query'] ) && is_object( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
		$posts = $query && isset( $query->posts ) && is_array( $query->posts ) ? $query->posts : array();
		$rfq_url = $this->published_page_url( 'request-quote' );

		if ( empty( $posts ) ) {
			ob_start(); ?>
			<div class="graha-catalog-empty graha-sparse-state graha-stack">
				<h2><?php echo esc_html__( 'Katalog siap digunakan', 'graha-selang' ); ?></h2>
				<p><?php echo esc_html__( 'Belum ada produk yang tersedia untuk tampilan ini. Gunakan konsultasi teknis jika Anda memerlukan bantuan pencarian produk.', 'graha-selang' ); ?></p>
				<?php if ( $rfq_url ) : graha_render_button( __( 'Request Quote', 'graha-selang' ), $rfq_url, 'primary' ); endif; ?>
			</div><?php
			return (string) ob_get_clean();
		}

		ob_start(); ?>
		<div class="graha-product-grid"><?php
		foreach ( $posts as $post ) {
			$post_id = is_object( $post ) && isset( $post->ID ) ? (int) $post->ID : (int) $post;
			if ( $post_id < 1 ) continue;
			$title = trim( wp_strip_all_tags( (string) get_the_title( $post_id ) ) );
			$url   = get_permalink( $post_id );
			if ( '' === $title || ! $url ) continue;
			$image   = function_exists( 'get_the_post_thumbnail' ) ? (string) get_the_post_thumbnail( $post_id, 'medium', array( 'loading' => 'lazy', 'decoding' => 'async' ) ) : '';
			$excerpt = function_exists( 'get_the_excerpt' ) ? trim( wp_strip_all_tags( (string) get_the_excerpt( $post_id ) ) ) : '';
			$terms   = $this->term_labels( $post_id );
			?>
			<article class="graha-product-card">
				<?php if ( '' !== $image ) : ?><a class="graha-product-card__media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_kses_post( $image ); ?></a><?php endif; ?>
				<div class="graha-product-card__body graha-stack">
					<?php if ( $terms ) : ?><p class="graha-product-card__terms"><?php echo esc_html( implode( ' · ', $terms ) ); ?></p><?php endif; ?>
					<h2 class="graha-product-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h2>
					<?php if ( '' !== $excerpt ) : ?><p class="graha-product-card__excerpt"><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
					<a class="graha-product-card__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html__( 'Lihat produk', 'graha-selang' ); ?></a>
				</div>
			</article><?php
		}
		?></div><?php
		if ( function_exists( 'get_the_posts_pagination' ) ) {
			echo wp_kses_post( (string) get_the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => __( 'Sebelumnya', 'graha-selang' ), 'next_text' => __( 'Berikutnya', 'graha-selang' ) ) ) );
		}
		if ( $rfq_url ) {
			echo '<div class="graha-catalog-cta">';
			graha_render_section_heading( '', __( 'Butuh bantuan memilih produk?', 'graha-selang' ), __( 'Sampaikan kebutuhan teknis Anda untuk memulai konsultasi.', 'graha-selang' ) );
			graha_render_button( __( 'Request Quote', 'graha-selang' ), $rfq_url, 'primary' );
			echo '</div>';
		}
		return (string) ob_get_clean();
	}

	/** Existing post title/content/image/terms only; sparse state stays factual. */
	private function render_single() {
		$post_id = function_exists( 'get_queried_object_id' ) ? (int) get_queried_object_id() : 0;
		if ( $post_id < 1 || 'publish' !== get_post_status( $post_id ) ) return '';
		$heading = trim( wp_strip_all_tags( (string) get_the_title( $post_id ) ) );
		if ( '' === $heading ) return '';

		$image   = function_exists( 'get_the_post_thumbnail' ) ? (string) get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'eager', 'decoding' => 'async' ) ) : '';
		$terms   = $this->term_labels( $post_id );
		$content = $this->rendered_editor_content( $post_id );
		$rfq_url = $this->published_page_url( 'request-quote' );
		$archive = get_post_type_archive_link( ProductContentService::POST_TYPE );

		ob_start(); ?>
		<div class="graha-product-single">
			<?php if ( '' !== $image ) : ?><div class="graha-product-single__media"><?php echo wp_kses_post( $image ); ?></div><?php endif; ?>
			<?php if ( $terms ) : ?><p class="graha-product-single__terms"><?php echo esc_html( implode( ' · ', $terms ) ); ?></p><?php endif; ?>
			<?php if ( '' !== $content ) : ?><div class="graha-product-single__content graha-native-content graha-stack"><?php echo wp_kses_post( $content ); ?></div><?php else : ?><div class="graha-sparse-state"><p><?php echo esc_html__( 'Informasi produk yang tersedia saat ini ditampilkan di atas. Detail tambahan belum dipublikasikan.', 'graha-selang' ); ?></p></div><?php endif; ?>
			<?php if ( $rfq_url ) : ?><div class="graha-catalog-cta"><?php graha_render_section_heading( '', __( 'Konsultasikan kebutuhan produk ini', 'graha-selang' ), __( 'Ajukan kebutuhan Anda melalui Request Quote.', 'graha-selang' ) ); graha_render_button( __( 'Request Quote', 'graha-selang' ), $rfq_url, 'primary' ); ?></div><?php endif; ?>
		</div><?php
		$content_html = (string) ob_get_clean();

		$crumbs = array();
		if ( $archive ) $crumbs[] = array( 'label' => __( 'Produk', 'graha-selang' ), 'url' => $archive );
		return $this->templates->render_page(
			'product_single',
			array(
				'heading'      => $heading,
				'eyebrow'      => __( 'Produk', 'graha-selang' ),
				'content_html' => $content_html,
				'breadcrumbs'  => $crumbs,
			)
		);
	}

	/** Existing category/brand names only; no inferred assignments. */
	private function term_labels( $post_id ) {
		if ( ! function_exists( 'get_the_terms' ) ) return array();
		$labels = array();
		foreach ( array( ProductContentService::CATEGORY_TAXONOMY, ProductContentService::BRAND_TAXONOMY ) as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( ! is_array( $terms ) ) continue;
			foreach ( $terms as $term ) {
				if ( is_object( $term ) && ! empty( $term->name ) ) {
					$label = trim( wp_strip_all_tags( (string) $term->name ) );
					if ( '' !== $label && ! in_array( $label, $labels, true ) ) $labels[] = $label;
				}
		}
		return $labels;
	}

	/** Normal content filters, minus TemplateService's recursive native wrapper. */
	private function rendered_editor_content( $post_id ) {
		$raw = (string) get_post_field( 'post_content', (int) $post_id );
		if ( '' === trim( wp_strip_all_tags( $raw ) ) ) return '';
		remove_filter( 'the_content', array( $this->templates, 'enhance_native_content' ), 30 );
		$rendered = apply_filters( 'the_content', $raw );
		add_filter( 'the_content', array( $this->templates, 'enhance_native_content' ), 30 );
		return wp_kses_post( (string) $rendered );
	}

	private function published_page_url( $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page || 'publish' !== $page->post_status ) return '';
		return (string) get_permalink( $page );
	}
}
