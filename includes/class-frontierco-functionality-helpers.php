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


	public static function get_product_tags(){

		$_TERMS = get_terms(
			array(
				'taxonomy' => 'product_tag',
    			'hide_empty' => true,
			)
		);

		return $_TERMS;

	}


	public static function get_cat_display_name($_TERM){

		$prod_cat_ancestors = get_ancestors($_TERM->term_id, 'product_cat');

		$tree = array();
	
		$prod_cat_ancestors = array_reverse($prod_cat_ancestors);										

		foreach($prod_cat_ancestors as $_PA):
			$parent_term = get_term($_PA, 'product_cat');
			$tree[] = $parent_term->name;
		endforeach;

		$tree[] = $_TERM->name;

		$_DISPLAY = implode(' > ', $tree);

		return $_DISPLAY;

	}



	public static function get_tag_display_name($_TERM){

		$prod_cat_ancestors = get_ancestors($_TERM->term_id, 'product_tag');

		$tree = array();
	
		$prod_cat_ancestors = array_reverse($prod_cat_ancestors);										

		foreach($prod_cat_ancestors as $_PA):
			$parent_term = get_term($_PA, 'product_tag');
			$tree[] = $parent_term->name;
		endforeach;

		$tree[] = $_TERM->name;

		$_DISPLAY = implode(' > ', $tree);

		return $_DISPLAY;

	}


	public static function get_products_from_cat($_CAT_SLUG){

		$_ARGS = array(
			'posts_per_page' 	=> '-1',
			'post_type' 		=> 'product',
			'post_status'    	=> array( 'publish'),
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
			'orderby' => array('meta_value_num' => 'ASC', 'menu_order' => 'ASC'),

		);

		$_PRODUCTS = get_posts(
			$_ARGS
		);

		return $_PRODUCTS;

	}



	public static function get_products_from_tag($_TAG_SLUG){

		$_ARGS = array(
			'posts_per_page' 	=> '-1',
			'post_type' 		=> 'product',
			'post_status'    	=> array( 'publish'),
			'product_tag'    	=> $_TAG_SLUG,
			'meta_query'		=> array(
				'cat_ordering'  => array(
					'relation' => 'OR',
					array(
						'key' => 'tag_ordering_'.$_TAG_SLUG,
						'compare' => 'EXISTS'
						),
					array(
						'key' => 'tag_ordering_'.$_TAG_SLUG,
						'compare' => 'NOT EXISTS'
					)
				)
			),
			'orderby' => array('meta_value_num' => 'ASC', 'menu_order' => 'ASC'),

		);

		$_PRODUCTS = get_posts(
			$_ARGS
		);

		return $_PRODUCTS;

	}



}


?>