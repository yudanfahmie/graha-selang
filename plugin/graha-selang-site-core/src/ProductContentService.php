<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;

/** Native Graha product content owner. */
final class ProductContentService {
	const POST_TYPE         = 'graha_product';
	const CATEGORY_TAXONOMY = 'graha_product_category';
	const BRAND_TAXONOMY    = 'graha_product_brand';

	/** @return void */
	public function register() {
		add_action( 'init', array( $this, 'register_content_model' ), 5 );
	}

	/** @return void */
	public function register_content_model() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels' => array(
					'name'          => __( 'Produk Graha', 'graha-selang' ),
					'singular_name' => __( 'Produk', 'graha-selang' ),
					'add_new_item'  => __( 'Tambah Produk', 'graha-selang' ),
					'edit_item'     => __( 'Edit Produk', 'graha-selang' ),
					'view_item'     => __( 'Lihat Produk', 'graha-selang' ),
					'search_items'  => __( 'Cari Produk', 'graha-selang' ),
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'has_archive'        => 'products',
				'rewrite'            => array( 'slug' => 'product', 'with_front' => false ),
				'query_var'          => true,
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'delete_with_user'   => false,
			)
		);

		register_taxonomy(
			self::CATEGORY_TAXONOMY,
			array( self::POST_TYPE ),
			array(
				'labels' => array(
					'name'          => __( 'Kategori Produk', 'graha-selang' ),
					'singular_name' => __( 'Kategori Produk', 'graha-selang' ),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'product-category', 'with_front' => false, 'hierarchical' => true ),
			)
		);

		register_taxonomy(
			self::BRAND_TAXONOMY,
			array( self::POST_TYPE ),
			array(
				'labels' => array(
					'name'          => __( 'Merek', 'graha-selang' ),
					'singular_name' => __( 'Merek', 'graha-selang' ),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'brand', 'with_front' => false ),
			)
		);
	}
}
