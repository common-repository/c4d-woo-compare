<?php 
	global $c4d_plugin_manager;
	$removeIcon = isset($c4d_plugin_manager['c4d-woo-compare-remove-icon']) ? $c4d_plugin_manager['c4d-woo-compare-remove-icon'] : 'fa fa-trash-o';
?>
<div class="c4d-woo-compare-cart__list_header">
	<h3 class="title">
		<?php esc_html_e('Your Compare', 'c4d-woo-compare'); ?>
		<span class="count"> (<span class="number"><?php echo count(array_filter($current, 'strlen')); ?></span>) <?php esc_html_e('items', 'c4d-woo-compare'); ?></span>
	</h3>
</div>
<div class="c4d-woo-compare-cart__list_items">
<?php 
$args = array(
    'post_type' 		=> 'product',
    'orderby'   		=> 'date',
	'order'     		=> 'desc',
    'post_status'       => 'publish',
    'post__in' 			=> $current,
    'numberposts'		=> 20,
    'posts_per_page'	=> 20,
    'paged'				=> 0
);
$q = new WP_Query($args);
if ($q->have_posts()) {
	while($q->have_posts()){
		$q->the_post();
		global $product;
			echo '<div class="item">';

			echo '<a data-id="'.esc_attr($product->get_id()).'" class="c4d-woo-compare-remove-item" href="#"><i class="'.esc_attr($removeIcon).'"></i></a>';
			echo '<div class="item_row">';
			echo '<div class="image">';
			echo '<a href="'.esc_attr($product->get_permalink()).'">';
			woocommerce_template_loop_product_thumbnail();
			echo '</a>';
			echo '</div>';
			echo '</div>';

			echo '<div class="item_row">';
			echo '<a href="'.esc_attr($product->get_permalink()).'"><h3 class="title">'.$product->get_title().'</h3></a>';
			echo '</div>';

			echo '<div class="item_row">';
			echo '<div class="price">'. $product->get_price_html() . '</div>';
			echo '</div>';
			echo '<div class="item_row">';
			woocommerce_template_loop_add_to_cart();
			echo '</div>';

			echo '</div>';
	}
} else {
	echo '<div class="c4d-woo-compare-cart__no-items">'.esc_html__('No Items', 'c4d-woo-compare').'</div>';
}
woocommerce_reset_loop();
wp_reset_postdata();
?>
</div>