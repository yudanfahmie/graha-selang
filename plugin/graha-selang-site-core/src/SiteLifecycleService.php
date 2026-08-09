<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

/** Narrow owner for activation, structural page provisioning, and schema upgrades. */
final class SiteLifecycleService {
	const SCHEMA_VERSION = '2';
	const VERSION_OPTION = 'graha_selang_site_schema_version';

	/** @return void */
	public function register() {
		add_action( 'admin_init', array( $this, 'maybe_upgrade' ), 5 );
	}

	/** Upgrade/retry only for authorized admin requests when schema changes. */
	public function maybe_upgrade() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( self::SCHEMA_VERSION === (string) get_option( self::VERSION_OPTION, '' ) ) {
			return;
		}

		$assign_front = $this->should_assign_home_as_front_page();
		$page_ids     = $this->provision_structure();
		if ( ! $this->structure_is_complete( $page_ids ) ) {
			return;
		}
		if ( $assign_front && ! empty( $page_ids['home'] ) ) {
			$this->assign_front_page( (int) $page_ids['home'] );
		}
		update_option( self::VERSION_OPTION, self::SCHEMA_VERSION, false );
		flush_rewrite_rules( false );
	}

	/** Activation is idempotent and performs one structural rewrite flush. */
	public function activate() {
		$assign_front = $this->should_assign_home_as_front_page();
		$page_ids     = $this->provision_structure();
		if ( $this->structure_is_complete( $page_ids ) ) {
			if ( $assign_front && ! empty( $page_ids['home'] ) ) {
				$this->assign_front_page( (int) $page_ids['home'] );
			}
			update_option( self::VERSION_OPTION, self::SCHEMA_VERSION, false );
		}
		flush_rewrite_rules( false );
	}

	/** Deactivation intentionally leaves content/options/routes untouched. */
	public function deactivate() {}

	/**
	 * @return array<string,int>
	 */
	private function provision_structure() {
		$definitions = array(
			'home'          => array( 'slug' => 'home', 'title' => 'Home' ),
			'about'         => array( 'slug' => 'about-us', 'title' => 'About Us' ),
			'services'      => array( 'slug' => 'layanan-kami', 'title' => 'Layanan Kami' ),
			'contact'       => array( 'slug' => 'contact-us', 'title' => 'Contact Us' ),
			'request_quote' => array( 'slug' => 'request-quote', 'title' => 'Request Quote' ),
		);

		$page_ids = array();
		foreach ( $definitions as $key => $definition ) {
			$page_ids[ $key ] = $this->ensure_page( $definition['slug'], $definition['title'] );
		}
		return $page_ids;
	}


	/** @param array<string,int> $page_ids Provisioned structural Page IDs. @return bool */
	private function structure_is_complete( array $page_ids ) {
		if ( 5 !== count( $page_ids ) ) {
			return false;
		}
		foreach ( $page_ids as $page_id ) {
			$page = (int) $page_id > 0 ? get_post( (int) $page_id ) : null;
			if ( ! $page instanceof \WP_Post || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Reuse the canonical Page when present. Existing title/content/status are editor-owned.
	 *
	 * @param string $slug Canonical Page slug.
	 * @param string $title Initial title used only for a missing Page.
	 * @return int
	 */
	private function ensure_page( $slug, $title ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page instanceof \WP_Post ) {
			return (int) $page->ID;
		}

		$result = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => '',
			),
			true
		);
		return is_wp_error( $result ) ? 0 : absint( $result );
	}

	/**
	 * Assign Home only for a genuinely fresh/default front-page state or an invalid static-front setting.
	 * Existing valid static front Pages and established posts-front sites are preserved.
	 *
	 * @return bool
	 */
	private function should_assign_home_as_front_page() {
		$show_on_front = (string) get_option( 'show_on_front', 'posts' );
		$front_id      = absint( get_option( 'page_on_front', 0 ) );

		if ( 'page' === $show_on_front ) {
			if ( $front_id > 0 ) {
				$front = get_post( $front_id );
				if ( $front instanceof \WP_Post && 'page' === $front->post_type && 'trash' !== $front->post_status ) {
					return false;
				}
			}
			return true;
		}

		return 1 === (int) get_option( 'fresh_site', 0 );
	}

	/** @param int $home_id Provisioned Home Page ID. @return void */
	private function assign_front_page( $home_id ) {
		if ( $home_id < 1 || 'publish' !== get_post_status( $home_id ) ) {
			return;
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
}
