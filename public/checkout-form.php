<?php 
	$sample_product = get_page_by_title('Sample Product', OBJECT, 'product');
?>

<p class="price">
	<span class="woocommercePrice-amount amount">
		<span class="woocommerce-Price-currencySymbol">$</span>
			<?php 
				global $product;
				echo get_post_meta($product->get_id(), 'sample_product_price', true); 
			?>
	</span>	
</p>

<form class="cart" method="post" enctype='multipart/form-data'>
	<button type="submit" name="add-to-cart" value="<?php echo $sample_product->ID; ?>" class="single_add_to_cart_button button alt">Order a Sample</button>
	<input type="hidden" name="sample_product" value="<?php the_ID(); ?>">
</form>