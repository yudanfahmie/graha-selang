<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

final class NavigationService {
	const PRIMARY_LOCATION = 'graha-primary';

	/** @var int */
	private static $render_count = 0;

	/**
	 * Register the single native menu location used as the primary nav source.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'after_setup_theme', array( $this, 'register_location' ) );
	}

	/**
	 * Register a native WordPress menu location without inventing menu items.
	 *
	 * @return void
	 */
	public function register_location() {
		register_nav_menus(
			array(
				self::PRIMARY_LOCATION => __( 'Navigasi Utama Graha Selang', 'graha-selang' ),
			)
		);
	}

	/**
	 * Resolve the assigned native menu into one normalized desktop/mobile tree.
	 * No assignment uses the deterministic Graha bootstrap fallback.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_primary_tree() {
		$locations = get_nav_menu_locations();
		$menu_id   = isset( $locations[ self::PRIMARY_LOCATION ] ) ? absint( $locations[ self::PRIMARY_LOCATION ] ) : 0;

		if ( ! $menu_id ) {
			return $this->fallback_tree();
		}

		$items = wp_get_nav_menu_items( $menu_id );
		if ( ! is_array( $items ) ) {
			return array();
		}

		return $this->normalize_items( $items );
	}

	/**
	 * Normalize WordPress menu-item objects into a stable nested tree.
	 * This method deliberately contains no persistence or route synthesis.
	 *
	 * @param array<int, object> $items WordPress-style menu items.
	 * @return array<int, array<string, mixed>>
	 */
	public function normalize_items( array $items ) {
		$nodes       = array();
		$child_index = array();
		$roots       = array();

		foreach ( $items as $item ) {
			if ( ! is_object( $item ) || empty( $item->ID ) ) {
				continue;
			}

			$id        = (int) $item->ID;
			$parent_id = isset( $item->menu_item_parent ) ? (int) $item->menu_item_parent : 0;
			$nodes[ $id ] = array(
				'id'       => $id,
				'parent'   => $parent_id,
				'title'    => isset( $item->title ) ? (string) $item->title : '',
				'url'      => isset( $item->url ) ? trim( (string) $item->url ) : '',
				'target'   => isset( $item->target ) && '_blank' === $item->target ? '_blank' : '',
				'rel'      => isset( $item->xfn ) ? trim( (string) $item->xfn ) : '',
				'current'  => ! empty( $item->current ),
				'ancestor' => ! empty( $item->current_item_ancestor ),
				'children' => array(),
			);

			if ( ! isset( $child_index[ $parent_id ] ) ) {
				$child_index[ $parent_id ] = array();
			}
			$child_index[ $parent_id ][] = $id;
		}

		$build = function ( $parent_id ) use ( &$build, &$nodes, &$child_index ) {
			$branch = array();
			foreach ( isset( $child_index[ $parent_id ] ) ? $child_index[ $parent_id ] : array() as $id ) {
				if ( ! isset( $nodes[ $id ] ) ) {
					continue;
				}
				$node             = $nodes[ $id ];
				$node['children'] = $build( $id );
				$branch[]         = $node;
			}
			return $branch;
		};

		$roots = $build( 0 );
		return $roots;
	}

	/**
	 * Render crawlable navigation markup from the normalized tree.
	 *
	 * @param array<string, mixed> $args Optional render arguments.
	 * @return string
	 */
	public function render_primary( array $args = array() ) {
		$tree = $this->get_primary_tree();
		if ( empty( $tree ) ) {
			return '';
		}

		self::$render_count++;
		$nav_id = 'graha-primary-navigation-' . self::$render_count;
		$label  = isset( $args['aria_label'] ) && '' !== trim( (string) $args['aria_label'] )
			? (string) $args['aria_label']
			: __( 'Navigasi utama', 'graha-selang' );

		ob_start();
		?>
		<nav class="graha-nav graha-ui" aria-label="<?php echo esc_attr( $label ); ?>" data-graha-nav>
			<button class="graha-nav__toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $nav_id ); ?>" data-graha-nav-toggle hidden>
				<?php echo esc_html__( 'Menu', 'graha-selang' ); ?>
			</button>
			<div class="graha-nav__panel" id="<?php echo esc_attr( $nav_id ); ?>" data-graha-nav-panel>
				<?php echo $this->render_branch( $tree, $nav_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</nav>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<int, array<string, mixed>> $branch Navigation branch.
	 * @param string $nav_id Navigation instance ID.
	 * @return string
	 */
	private function render_branch( array $branch, $nav_id ) {
		ob_start();
		?>
		<ul class="graha-nav__list">
			<?php foreach ( $branch as $node ) : ?>
				<?php
				$has_children = ! empty( $node['children'] );
				$submenu_id   = $nav_id . '-submenu-' . (int) $node['id'];
				$safe_url     = esc_url( $node['url'] );
				$is_link      = '' !== $safe_url && '#' !== $safe_url;
				$rel          = $node['rel'];
				if ( '_blank' === $node['target'] && false === strpos( ' ' . $rel . ' ', ' noopener ' ) ) {
					$rel = trim( $rel . ' noopener' );
				}
				?>
				<li class="graha-nav__item<?php echo $has_children ? ' graha-nav__item--has-children' : ''; ?><?php echo ! empty( $node['current'] ) ? ' is-current' : ''; ?><?php echo ! empty( $node['ancestor'] ) ? ' is-ancestor' : ''; ?>">
					<div class="graha-nav__row">
						<?php if ( $is_link ) : ?>
							<a class="graha-nav__link" href="<?php echo $safe_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"<?php echo ! empty( $node['current'] ) ? ' aria-current="page"' : ''; ?><?php echo $node['target'] ? ' target="' . esc_attr( $node['target'] ) . '"' : ''; ?><?php echo $rel ? ' rel="' . esc_attr( $rel ) . '"' : ''; ?>><?php echo esc_html( $node['title'] ); ?></a>
						<?php else : ?>
							<span class="graha-nav__label"><?php echo esc_html( $node['title'] ); ?></span>
						<?php endif; ?>

						<?php if ( $has_children ) : ?>
							<button class="graha-nav__submenu-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $submenu_id ); ?>" data-graha-disclosure-toggle hidden>
								<span aria-hidden="true">+</span>
								<span class="graha-sr-only"><?php echo esc_html( sprintf( __( 'Buka submenu %s', 'graha-selang' ), $node['title'] ) ); ?></span>
							</button>
						<?php endif; ?>
					</div>

					<?php if ( $has_children ) : ?>
						<div class="graha-nav__submenu" id="<?php echo esc_attr( $submenu_id ); ?>" data-graha-disclosure-panel>
							<?php echo $this->render_branch( $node['children'], $nav_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		return (string) ob_get_clean();
	}

	/** @return array<int, array<string, mixed>> */
	private function fallback_tree() {
		$tree = array(
			$this->fallback_item( 100001, 'Beranda', home_url( '/' ), '/' ),
		);

		$product_url = function_exists( 'get_post_type_archive_link' ) ? get_post_type_archive_link( 'graha_product' ) : '';
		if ( $product_url ) {
			$tree[] = $this->fallback_item( 100002, 'Produk', (string) $product_url, '/products/' );
		}

		$pages = array(
			array( 100003, 'Layanan', 'layanan-kami', '/layanan-kami/' ),
			array( 100004, 'Tentang Kami', 'about-us', '/about-us/' ),
			array( 100005, 'Request Quote', 'request-quote', '/request-quote/' ),
			array( 100006, 'Hubungi Kami', 'contact-us', '/contact-us/' ),
		);
		foreach ( $pages as $definition ) {
			$page = get_page_by_path( $definition[2], OBJECT, 'page' );
			if ( ! $page instanceof \WP_Post || 'publish' !== $page->post_status ) {
				continue;
			}
			$url = get_permalink( $page );
			if ( ! $url ) {
				continue;
			}
			$tree[] = $this->fallback_item( $definition[0], $definition[1], (string) $url, $definition[3] );
		}
		return $tree;
	}

	/** @return array<string,mixed> */
	private function fallback_item( $id, $title, $url, $path ) {
		return array(
			'id'       => (int) $id,
			'parent'   => 0,
			'title'    => (string) $title,
			'url'      => (string) $url,
			'target'   => '',
			'rel'      => '',
			'current'  => $this->path_is_active( $path ),
			'ancestor' => false,
			'children' => array(),
		);
	}

	/** @param string $path Canonical path. @return bool */
	private function path_is_active( $path ) {
		$current = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/';
		$current = (string) strtok( $current, '?' );
		$target  = parse_url( home_url( $path ), PHP_URL_PATH );
		$target  = is_string( $target ) ? $target : $path;
		$current = '/' . ltrim( $current, '/' );
		$target  = '/' . ltrim( $target, '/' );
		if ( '/' === $target ) {
			return '/' === rtrim( $current, '/' ) . '/' || '/' === $current;
		}
		return 0 === strpos( rtrim( $current, '/' ) . '/', rtrim( $target, '/' ) . '/' );
	}
}
