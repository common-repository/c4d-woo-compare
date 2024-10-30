<?php
/*
Plugin Name: C4D Woocommerce Compare
Plugin URI: http://coffee4dev.com/
Description: Add quickview button for product.
Author: Coffee4dev.com
Author URI: http://coffee4dev.com/
Text Domain: c4d-woo-compare
Version: 2.0.2
*/

define('C4DWCOMPARE_PLUGIN_URI', plugins_url('', __FILE__));

add_action( 'wp_enqueue_scripts', 'c4d_woo_compare_safely_add_stylesheet_to_frontsite');
add_action( 'wp_ajax_c4d_woo_compare_cart', 'c4d_woo_compare_cart');
add_action( 'wp_ajax_nopriv_c4d_woo_compare_cart', 'c4d_woo_compare_cart');
add_action( 'c4d-plugin-manager-section', 'c4d_woo_compare_section_options');
add_shortcode( 'c4d-woo-compare-cart', 'c4d_woo_compare_shortcode_cart');
add_shortcode( 'c4d-woo-compare-button', 'c4d_woo_compare_shortcode_button');
add_filter( 'plugin_row_meta', 'c4d_woo_compare_plugin_row_meta', 10, 2 );

function c4d_woo_compare_plugin_row_meta( $links, $file ) {
    if ( strpos( $file, basename(__FILE__) ) !== false ) {
        $new_links = array(
            'visit' => '<a href="http://coffee4dev.com">Visit Plugin Site</<a>',
            'premium' => '<a href="http://coffee4dev.com">Premium Support</<a>'
        );
        $links = array_merge( $links, $new_links );
    }
    return $links;
}

function c4d_woo_compare_safely_add_stylesheet_to_frontsite( $page ) {
	if(!defined('C4DPLUGINMANAGER_OFF_JS_CSS')) {
		wp_enqueue_style( 'c4d-woo-compare-frontsite-style', C4DWCOMPARE_PLUGIN_URI.'/assets/default.css' );
		wp_enqueue_script( 'c4d-woo-compare-frontsite-plugin-js', C4DWCOMPARE_PLUGIN_URI.'/assets/default.js', array( 'jquery' ), false, true ); 
	}
	wp_enqueue_style( 'fancybox', C4DWCOMPARE_PLUGIN_URI.'/libs/jquery.fancybox.min.css'); 
	wp_enqueue_script( 'fancybox', C4DWCOMPARE_PLUGIN_URI.'/libs/jquery.fancybox.min.js', array( 'jquery' ), false, true ); 
	wp_enqueue_style( 'owl-carousel', C4DWCOMPARE_PLUGIN_URI.'/libs/owl-carousel/owl.carousel.css' );
	wp_enqueue_style( 'owl-carousel-theme', C4DWCOMPARE_PLUGIN_URI.'/libs/owl-carousel/owl.theme.css' );
	wp_enqueue_script( 'owl-carousel', C4DWCOMPARE_PLUGIN_URI.'/libs/owl-carousel/owl.carousel.js', array( 'jquery' ), false, true ); 
	wp_enqueue_script( 'jquery-cookie', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js', array( 'jquery' ), false, true ); 
	wp_localize_script( 'jquery', 'c4d_woo_compare',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

function c4d_woo_compare_shortcode_cart($params, $content) {
	global $woocommerce, $c4d_plugin_manager;
	$icon = isset($c4d_plugin_manager['c4d-woo-compare-icon']) ? $c4d_plugin_manager['c4d-woo-compare-icon'] : 'fa fa-retweet';
	$params['icon'] = isset($params['icon']) ? $params['icon'] : $icon;
	$params['label'] = isset($params['label']) ? $params['label'] : '';
	
	$html = '<div class="c4d-woo-compare-cart">';
	$html .= '<div href="#c4d-woo-compare-cart__list" class="c4d-woo-compare-cart__icon"><i class="'.esc_attr($params['icon']).'"></i><span class="number"></span><span class="text">'.$params['label'].'</span></div>';
	$html .= '<div id="c4d-woo-compare-cart__list" class="c4d-woo-compare-cart__list">';
	$html .= '</div>';
	$html .= '</div>';
	return $html;
}
function c4d_woo_compare_shortcode_button($params, $content) {
	global $product, $c4d_plugin_manager;
	$current = array();
	if (isset($_COOKIE['c4d-woo-compare-cookie']) && $_COOKIE['c4d-woo-compare-cookie'] != '') {
		$current = $_COOKIE['c4d-woo-compare-cookie'];
		$current = explode(',', $current);
	}
	$pid = get_the_ID();
	$added = in_array($pid, $current) ? 'added' : '';
	$icon = isset($c4d_plugin_manager['c4d-woo-compare-icon']) ? $c4d_plugin_manager['c4d-woo-compare-icon'] : 'fa fa-retweet';
	$params['icon'] = isset($params['icon']) ? $params['icon'] : $icon;
	$params['label'] = isset($params['label']) ? '<span class="label">'.$params['label'].'</label>' : '';
	return '<a class="c4d-woo-compare-button '.($added).'" 
				data-id="'.esc_attr($pid).'"
				href="#"><span class="icon"><i class="'.esc_attr($params['icon']).'"></i></span>'.$params['label'].'</a>';
}

function c4d_woo_compare_cart(){
	if (isset($_COOKIE['c4d-woo-compare-cookie']) && $_COOKIE['c4d-woo-compare-cookie'] != '') {
		$current = $_COOKIE['c4d-woo-compare-cookie'];
		$current = explode(',', $current);
		if (is_array($current)) {
			require dirname(__FILE__). '/templates/default.php';
		}
	}
	wp_die();
}

function c4d_woo_compare_section_options(){
    $opt_name = 'c4d_plugin_manager';
    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Compare', 'c4d-woo-compare' ),
        'id'               => 'c4d-woo-compare',
        'desc'             => '',
        'customizer_width' => '400px',
        'icon'             => 'el el-home',
        'fields'           => array(
            array(
                'id'       => 'c4d-woo-compare-icon',
                'type'     => 'text',
                'title'    => esc_html__('Default Compare Icon', 'c4d-woo-compare'),
                'subtitle' => esc_html__('Set default icon. Support icon font only, insert the class of icon', 'c4d-woo-compare'),
                'default'  => 'fa fa-shopping-bag'
            ),
            array(
                'id'       => 'c4d-woo-compare-remove-icon',
                'type'     => 'text',
                'title'    => esc_html__('Remove Icon', 'c4d-woo-compare'),
                'default'  => 'fa fa-trash-o'
            )
        )
    ));
}