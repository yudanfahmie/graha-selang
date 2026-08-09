<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class TemplateService {
	const PREPARE_HOOK     = 'graha_selang_prepare_page';
	const RENDER_HOOK      = 'graha_selang_render_page';
	const BREADCRUMB_HOOK  = 'graha_selang_render_breadcrumbs';

	const FAMILIES = array(
		'home',
		'product_archive',
		'product_category',
		'product_single',
		'application',
		'brand',
		'about',
		'service',
		'technical_rfq',
		'article',
		'legal',
		'search',
		'not_found',
	);

	/** @var AssetService */
	private $assets;

	/** @var NavigationService */
	private $navigation;

	/**
	 * @param AssetService      $assets Canonical asset owner.
	 * @param NavigationService $navigation Canonical navigation owner.
	 */
	public function __construct( AssetService $assets, NavigationService $navigation ) {
		$this->assets     = $assets;
		$this->navigation = $navigation;
	}

	/**
	 * Register opt-in presentation hooks without claiming public routes.
	 *
	 * @return void
	 */
	public function register() {
		add_action( self::PREPARE_HOOK, array( $this, 'prepare_page' ) );
		add_action( self::RENDER_HOOK, array( $this, 'output_page' ), 10, 2 );
		add_action( self::BREADCRUMB_HOOK, array( $this, 'output_breadcrumbs' ), 10, 2 );
	}

	/**
	 * Opt-in asset preparation for a Graha-rendered family.
	 * Call before the document head is emitted.
	 *
	 * @param string $family Presentation family.
	 * @return void
	 */
	public function prepare_page( $family ) {
		if ( ! $this->is_supported_family( $family ) ) {
			return;
		}

		$this->assets->enqueue_shell();
	}

	/**
	 * Echo a complete semantic shell for an explicitly selected family/context.
	 *
	 * @param string $family Presentation family.
	 * @param mixed  $context Explicit presentation context.
	 * @return void
	 */
	public function output_page( $family, $context = array() ) {
		if ( ! is_array( $context ) ) {
			$context = array();
		}

		echo $this->render_page( $family, $context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render a complete shell without owning route selection.
	 *
	 * @param string               $family Presentation family.
	 * @param array<string, mixed> $context Presentation context.
	 * @return string
	 */
	public function render_page( $family, array $context ) {
		$family = sanitize_key( (string) $family );
		if ( ! $this->is_supported_family( $family ) ) {
			return '';
		}

		$heading = isset( $context['heading'] ) ? trim( wp_strip_all_tags( (string) $context['heading'] ) ) : '';
		if ( '' === $heading ) {
			return '';
		}

		$is_home = 'home' === $family;
		if ( $is_home && ! $this->home_context_is_ready( $context ) ) {
			return '';
		}

		$main = $this->render_main( $family, $context, $is_home );
		if ( '' === $main ) {
			return '';
		}

		$site_name = trim( (string) get_bloginfo( 'name' ) );
		$home_url  = home_url( '/' );
		$logo_html = function_exists( 'get_custom_logo' ) ? (string) get_custom_logo() : '';
		$nav_html  = $this->navigation->render_primary();
		$footer    = isset( $context['footer_html'] ) ? wp_kses_post( (string) $context['footer_html'] ) : '';

		ob_start();
		?>
		<div class="graha-site-shell graha-ui" data-graha-family="<?php echo esc_attr( $family ); ?>">
			<a class="graha-skip-link" href="#graha-main"><?php echo esc_html__( 'Lewati ke konten utama', 'graha-selang' ); ?></a>
			<header class="graha-site-header">
				<div class="graha-site-header__inner graha-container graha-container--wide">
					<?php if ( '' !== $logo_html || '' !== $site_name ) : ?>
						<div class="graha-site-identity">
							<?php if ( '' !== $logo_html ) : ?>
								<?php echo wp_kses_post( $logo_html ); ?>
							<?php elseif ( '' !== $site_name ) : ?>
								<a href="<?php echo esc_url( $home_url ); ?>"><?php echo esc_html( $site_name ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php echo $nav_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</header>
			<?php echo $main; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<footer class="graha-site-footer">
				<div class="graha-container graha-container--wide graha-stack">
					<?php if ( '' !== $footer ) : ?>
						<?php echo $footer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php elseif ( '' !== $site_name ) : ?>
						<p class="graha-site-footer__identity"><?php echo esc_html( $site_name ); ?></p>
					<?php endif; ?>
				</div>
			</footer>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Echo reusable visible breadcrumbs only; no schema/metadata is emitted.
	 *
	 * @param mixed  $items Breadcrumb items.
	 * @param string $current_label Current page label when caller omits final item.
	 * @return void
	 */
	public function output_breadcrumbs( $items = array(), $current_label = '' ) {
		if ( ! is_array( $items ) ) {
			$items = array();
		}

		echo $this->render_breadcrumbs( $items, $current_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render one provider-safe visible breadcrumb trail.
	 *
	 * @param array<int, array<string, mixed>> $items Breadcrumb ancestors/current item.
	 * @param string                            $current_label Current page label.
	 * @return string
	 */
	public function render_breadcrumbs( array $items, $current_label = '' ) {
		$normalized = $this->normalize_breadcrumbs( $items, $current_label );
		if ( count( $normalized ) < 2 ) {
			return '';
		}

		ob_start();
		?>
		<nav class="graha-breadcrumbs" aria-label="<?php echo esc_attr__( 'Jejak navigasi', 'graha-selang' ); ?>">
			<ol>
				<?php foreach ( $normalized as $index => $item ) : ?>
					<li>
						<?php if ( $index < count( $normalized ) - 1 && '' !== $item['url'] ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
						<?php else : ?>
							<span<?php echo $index === count( $normalized ) - 1 ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</nav>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param string $family Presentation family.
	 * @return bool
	 */
	private function is_supported_family( $family ) {
		return in_array( sanitize_key( (string) $family ), self::FAMILIES, true );
	}

	/**
	 * Homepage renderer refuses to produce a production-looking page with fewer
	 * than four explicit substantial sections.
	 *
	 * @param array<string, mixed> $context Home context.
	 * @return bool
	 */
	private function home_context_is_ready( array $context ) {
		if ( empty( $context['sections'] ) || ! is_array( $context['sections'] ) ) {
			return false;
		}

		$ready = 0;
		foreach ( $context['sections'] as $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}
			$heading = isset( $section['heading'] ) ? trim( wp_strip_all_tags( (string) $section['heading'] ) ) : '';
			$content = isset( $section['content_html'] ) ? trim( wp_strip_all_tags( (string) $section['content_html'] ) ) : '';
			if ( '' !== $heading && '' !== $content ) {
				$ready++;
			}
		}

		return $ready >= 4;
	}

	/**
	 * @param string               $family Presentation family.
	 * @param array<string, mixed> $context Presentation context.
	 * @param bool                 $is_home Whether this is Home.
	 * @return string
	 */
	private function render_main( $family, array $context, $is_home ) {
		$heading     = trim( wp_strip_all_tags( (string) $context['heading'] ) );
		$eyebrow     = isset( $context['eyebrow'] ) ? trim( wp_strip_all_tags( (string) $context['eyebrow'] ) ) : '';
		$lead        = isset( $context['lead_html'] ) ? wp_kses_post( (string) $context['lead_html'] ) : '';
		$content     = isset( $context['content_html'] ) ? wp_kses_post( (string) $context['content_html'] ) : '';
		$breadcrumbs = isset( $context['breadcrumbs'] ) && is_array( $context['breadcrumbs'] ) ? $context['breadcrumbs'] : array();
		$breadcrumb  = $is_home ? '' : $this->render_breadcrumbs( $breadcrumbs, $heading );

		ob_start();
		?>
		<main id="graha-main" class="graha-site-main graha-site-main--<?php echo esc_attr( $family ); ?>">
			<div class="graha-container graha-container--wide graha-stack--large">
				<?php echo $breadcrumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<article class="graha-page graha-page--<?php echo esc_attr( $family ); ?>">
					<header class="graha-page-header">
						<?php if ( '' !== $eyebrow ) : ?>
							<p class="graha-page-header__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<h1><?php echo esc_html( $heading ); ?></h1>
						<?php if ( '' !== $lead ) : ?>
							<div class="graha-page-header__lead"><?php echo $lead; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
					</header>

					<?php if ( $is_home ) : ?>
						<?php echo $this->render_home_sections( $context['sections'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php elseif ( '' !== $content ) : ?>
						<div class="graha-page__content graha-stack"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>
				</article>
			</div>
		</main>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<int, array<string, mixed>> $sections Explicit Home sections.
	 * @return string
	 */
	private function render_home_sections( array $sections ) {
		ob_start();
		foreach ( $sections as $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}
			$heading = isset( $section['heading'] ) ? trim( wp_strip_all_tags( (string) $section['heading'] ) ) : '';
			$content = isset( $section['content_html'] ) ? wp_kses_post( (string) $section['content_html'] ) : '';
			if ( '' === $heading || '' === trim( wp_strip_all_tags( $content ) ) ) {
				continue;
			}
			?>
			<section class="graha-page-section">
				<h2 class="graha-page-section__heading"><?php echo esc_html( $heading ); ?></h2>
				<div class="graha-page-section__content graha-stack"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</section>
			<?php
		}
		return (string) ob_get_clean();
	}

	/**
	 * @param array<int, array<string, mixed>> $items Breadcrumb items.
	 * @param string                            $current_label Current page label.
	 * @return array<int, array{label:string,url:string}>
	 */
	private function normalize_breadcrumbs( array $items, $current_label ) {
		$normalized = array(
			array(
				'label' => __( 'Beranda', 'graha-selang' ),
				'url'   => home_url( '/' ),
			),
		);

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$label = isset( $item['label'] ) ? trim( wp_strip_all_tags( (string) $item['label'] ) ) : '';
			$url   = isset( $item['url'] ) ? esc_url( (string) $item['url'] ) : '';
			if ( '' === $label || __( 'Beranda', 'graha-selang' ) === $label ) {
				continue;
			}
			$normalized[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}

		$current_label = trim( wp_strip_all_tags( (string) $current_label ) );
		$last_label    = end( $normalized );
		$last_label    = is_array( $last_label ) ? $last_label['label'] : '';
		if ( '' !== $current_label && $current_label !== $last_label ) {
			$normalized[] = array(
				'label' => $current_label,
				'url'   => '',
			);
		} elseif ( count( $normalized ) > 1 ) {
			$normalized[ count( $normalized ) - 1 ]['url'] = '';
		}

		return $normalized;
	}
}
