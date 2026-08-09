<?php
/**
 * Small reusable Graha Selang UI composition primitives.
 *
 * Plain render helpers shared by the homepage and page-family templates.
 * These intentionally hold no data-fetching/business logic; callers pass
 * already-approved/native content in.
 *
 * @package GrahaSelang
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'graha_ui_icon' ) ) {
	/**
	 * Minimal hand-authored line icon set (no external icon library).
	 *
	 * @param string $name One of: box, gear, tag, chat, check, arrow.
	 * @return string
	 */
	function graha_ui_icon( $name ) {
		$common = 'width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"';
		switch ( $name ) {
			case 'box':
				return '<svg ' . $common . '><path d="M4 8.2 12 4l8 4.2v7.6L12 20l-8-4.2V8.2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M4 8.2 12 12l8-3.8M12 12v8" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>';
			case 'gear':
				return '<svg ' . $common . '><path d="m12 3 2.1 1.2 2.3-.6 1.4 2 2.3.6.1 2.4 1.8 1.6-1.8 1.6-.1 2.4-2.3.6-1.4 2-2.3-.6L12 21l-2.1-1.2-2.3.6-1.4-2-2.3-.6-.1-2.4L2 12.6l1.8-1.6.1-2.4 2.3-.6 1.4-2 2.3.6L12 3Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><circle cx="12" cy="12" r="3.1" stroke="currentColor" stroke-width="1.3"/></svg>';
			case 'tag':
				return '<svg ' . $common . '><path d="M11.4 3.6h5a2 2 0 0 1 2 2v5a2 2 0 0 1-.6 1.4l-7.4 7.4a2 2 0 0 1-2.8 0l-4.6-4.6a2 2 0 0 1 0-2.8l7.4-7.4a2 2 0 0 1 1-.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="15.5" cy="8.5" r="1.4" stroke="currentColor" stroke-width="1.4"/></svg>';
			case 'chat':
				return '<svg ' . $common . '><path d="M4 5.5h16v10.2H9.8L5.6 19v-3.3H4V5.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M7.5 9h9M7.5 12.2h6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>';
			case 'check':
				return '<svg ' . $common . '><path d="m5 12.5 4.2 4.2L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			case 'arrow':
				return '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false"><path d="M6 3.5 11 8l-5 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			default:
				return '';
		}
	}
}

if ( ! function_exists( 'graha_render_eyebrow' ) ) {
	/**
	 * @param string $text Label text.
	 * @param bool   $on_dark Render the on-dark contrast variant.
	 * @return void
	 */
	function graha_render_eyebrow( $text, $on_dark = false ) {
		$text = trim( wp_strip_all_tags( (string) $text ) );
		if ( '' === $text ) {
			return;
		}
		echo '<p class="graha-eyebrow' . ( $on_dark ? ' graha-eyebrow--on-dark' : '' ) . '">' . esc_html( $text ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'graha_render_section_heading' ) ) {
	/**
	 * @param string $eyebrow Small kicker label.
	 * @param string $heading Section heading text.
	 * @param string $lead Optional supporting copy.
	 * @param string $modifier Optional extra modifier: 'center' or 'row'.
	 * @return void
	 */
	function graha_render_section_heading( $eyebrow, $heading, $lead = '', $modifier = '' ) {
		$heading = trim( wp_strip_all_tags( (string) $heading ) );
		if ( '' === $heading ) {
			return;
		}
		$class = 'graha-section-heading' . ( '' !== $modifier ? ' graha-section-heading--' . sanitize_key( $modifier ) : '' );
		echo '<div class="' . esc_attr( $class ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		graha_render_eyebrow( $eyebrow );
		echo '<h2>' . esc_html( $heading ) . '</h2>';
		$lead = trim( wp_strip_all_tags( (string) $lead ) );
		if ( '' !== $lead ) {
			echo '<p>' . esc_html( $lead ) . '</p>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'graha_render_button' ) ) {
	/**
	 * @param string $label Visible label.
	 * @param string $url Destination URL.
	 * @param string $variant primary|outline|ghost.
	 * @param string $extra_class Additional space-separated classes.
	 * @return void
	 */
	function graha_render_button( $label, $url, $variant = 'primary', $extra_class = '' ) {
		$label = trim( wp_strip_all_tags( (string) $label ) );
		$url   = trim( (string) $url );
		if ( '' === $label || '' === $url ) {
			return;
		}
		$class = 'graha-button graha-button--' . sanitize_key( $variant );
		if ( '' !== $extra_class ) {
			$class .= ' ' . $extra_class;
		}
		echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'graha_render_discovery_grid' ) ) {
	/**
	 * Render the catalog entry-door grid. Doors without a live destination
	 * render as an honest inert/sparse card instead of a fabricated link.
	 *
	 * @param array<int,array<string,string>> $doors Each: icon,title,copy,url,cta,badge.
	 * @return void
	 */
	function graha_render_discovery_grid( array $doors ) {
		$doors = array_values(
			array_filter(
				$doors,
				static function ( $door ) {
					return is_array( $door ) && ! empty( $door['title'] ) && ! empty( $door['copy'] );
				}
			)
		);
		if ( ! $doors ) {
			return;
		}
		echo '<div class="graha-grid graha-grid--4">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $doors as $door ) {
			$url  = isset( $door['url'] ) ? trim( (string) $door['url'] ) : '';
			$icon = isset( $door['icon'] ) ? graha_ui_icon( (string) $door['icon'] ) : '';
			$tag  = isset( $door['badge'] ) ? trim( wp_strip_all_tags( (string) $door['badge'] ) ) : '';
			$is_link = '' !== $url;
			$tag_el  = $is_link ? 'a' : 'div';
			echo '<' . $tag_el . ' class="graha-discovery-card' . ( $is_link ? '' : ' graha-discovery-card--sparse' ) . '"' . ( $is_link ? ' href="' . esc_url( $url ) . '"' : '' ) . '>';
			if ( $icon ) {
				echo '<span class="graha-discovery-card__icon">' . $icon . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( '' !== $tag ) {
				echo '<span class="graha-discovery-card__tag">' . esc_html( $tag ) . '</span>';
			}
			echo '<strong>' . esc_html( (string) $door['title'] ) . '</strong>';
			echo '<p>' . esc_html( (string) $door['copy'] ) . '</p>';
			if ( $is_link && ! empty( $door['cta'] ) ) {
				echo '<span class="graha-discovery-card__action">' . esc_html( (string) $door['cta'] ) . ' ' . graha_ui_icon( 'arrow' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</' . $tag_el . '>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'graha_render_trust_strip' ) ) {
	/**
	 * @param array<int,array<string,string>> $items Each: label, detail, url (optional).
	 * @return void
	 */
	function graha_render_trust_strip( array $items ) {
		$items = array_values(
			array_filter(
				$items,
				static function ( $item ) {
					return is_array( $item ) && ! empty( $item['label'] );
				}
			)
		);
		if ( ! $items ) {
			return;
		}
		echo '<ul class="graha-trust-strip">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $items as $item ) {
			$label  = esc_html( (string) $item['label'] );
			$detail = ! empty( $item['detail'] ) ? esc_html( (string) $item['detail'] ) : '';
			$url    = ! empty( $item['url'] ) ? trim( (string) $item['url'] ) : '';
			echo '<li>';
			if ( $url ) {
				echo '<a href="' . esc_url( $url ) . '"><strong>' . $label . '</strong>' . ( $detail ? '<span>' . $detail . '</span>' : '' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<strong>' . $label . '</strong>' . ( $detail ? '<span>' . $detail . '</span>' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</li>';
		}
		echo '</ul>';
	}
}

if ( ! function_exists( 'graha_render_cta_panel' ) ) {
	/**
	 * @param string $eyebrow Kicker label.
	 * @param string $heading Panel heading.
	 * @param string $copy Supporting copy.
	 * @param string $primary_label Primary action label.
	 * @param string $primary_url Primary action URL.
	 * @param string $secondary_label Optional secondary label.
	 * @param string $secondary_url Optional secondary URL.
	 * @param string $modifier Optional modifier, e.g. 'tint'.
	 * @return void
	 */
	function graha_render_cta_panel( $eyebrow, $heading, $copy, $primary_label, $primary_url, $secondary_label = '', $secondary_url = '', $modifier = '' ) {
		$heading = trim( wp_strip_all_tags( (string) $heading ) );
		if ( '' === $heading ) {
			return;
		}
		$class = 'graha-cta-panel' . ( '' !== $modifier ? ' graha-cta-panel--' . sanitize_key( $modifier ) : '' );
		echo '<div class="' . esc_attr( $class ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="graha-cta-panel__body">';
		graha_render_eyebrow( $eyebrow, 'tint' !== $modifier );
		echo '<h2>' . esc_html( $heading ) . '</h2>';
		$copy = trim( wp_strip_all_tags( (string) $copy ) );
		if ( '' !== $copy ) {
			echo '<p>' . esc_html( $copy ) . '</p>';
		}
		echo '</div><div class="graha-cta-panel__actions">';
		graha_render_button( $primary_label, $primary_url, 'primary' );
		graha_render_button( $secondary_label, $secondary_url, 'outline', 'tint' !== $modifier ? 'graha-button--on-dark' : '' );
		echo '</div></div>';
	}
}

if ( ! function_exists( 'graha_render_feature_grid' ) ) {
	/**
	 * @param array<int,array<string,string>> $items Each: icon, title, copy.
	 * @param string $columns Grid modifier, e.g. '3'.
	 * @return void
	 */
	function graha_render_feature_grid( array $items, $columns = '3' ) {
		$items = array_values(
			array_filter(
				$items,
				static function ( $item ) {
					return is_array( $item ) && ! empty( $item['title'] ) && ! empty( $item['copy'] );
				}
			)
		);
		if ( ! $items ) {
			return;
		}
		echo '<div class="graha-grid graha-grid--' . esc_attr( $columns ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $items as $item ) {
			echo '<div class="graha-feature-card">';
			if ( ! empty( $item['icon'] ) ) {
				echo '<span class="graha-feature-card__icon">' . graha_ui_icon( (string) $item['icon'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '<h3>' . esc_html( (string) $item['title'] ) . '</h3>';
			echo '<p>' . esc_html( (string) $item['copy'] ) . '</p>';
			echo '</div>';
		}
		echo '</div>';
	}
}
