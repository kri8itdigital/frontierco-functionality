<?php


class FRONTIERCO{


	public static function is_woocommerce_active(){

		if ( class_exists( 'woocommerce' ) ):
			return true;
		endif;

		return false;

	}


	public static function is_elementor_pro_active(){

		if (!function_exists('is_plugin_active')):
		    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		endif;

		if (is_plugin_active( 'elementor-pro/elementor-pro.php' )):
			return true;
		endif;

		return false;
	}


	public static function get_product_cats(){

		$_TERMS = get_terms(
			array(
				'taxonomy' => 'product_cat',
    			'hide_empty' => true,
			)
		);

		return $_TERMS;

	}


	public static function get_products_from_cat($_CAT_SLUG){

		$_ARGS = array(
			'posts_per_page' 	=> '-1',
			'post_type' 		=> 'product',
			'product_cat'    	=> $_CAT_SLUG,
			'meta_query'		=> array(
				'cat_ordering'  => array(
					'relation' => 'OR',
					array(
						'key' => 'cat_ordering_'.$_CAT_SLUG,
						'compare' => 'EXISTS'
						),
					array(
						'key' => 'cat_ordering_'.$_CAT_SLUG,
						'compare' => 'NOT EXISTS'
					)
				)
			),
			'orderby' => 'meta_value menu_order',
			'order' => 'ASC'

		);

		$_PRODUCTS = get_posts(
			$_ARGS
		);

		return $_PRODUCTS;

	}



}


?>