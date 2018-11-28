/add saleserson role
add_action('init', 'salesperson_user_role');
function salesperson_user_role(){    
    global $wp_roles;
    if (!isset($wp_roles))
        $wp_roles = new WP_Roles();
    $auth = $wp_roles->get_role('author');
    $wp_roles->add_role('salesperson', 'Salesperson', $auth->capabilities);
}
      

//*** Prevent mixture of sample and other prods in same cart ***//
add_filter('woocommerce_add_to_cart_validation', 'do_not_add_to_cart_containing_other', 10, 2);
function do_not_add_to_cart_containing_other($validation, $product_id) {

// Set flag false until we find a product in cat sample
    $cart_has_sample = false;

// Set $cat_check true if a cart item is in sample cat
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

        $product = $cart_item['data'];

        if ( $product->name === 'Sample Product') {
            $cart_has_sample = true;
            // break because we only need one "true" to matter here
            break;
        }
    }

    $product_is_sample = false;
    if ( $product_id === 46854 ) {
        $product_is_sample = true;
    }

// Return true if cart empty
    if (!WC()->cart->get_cart_contents_count() == 0) {
        // If cart contains sample and product to be added is not sample, display error message and return false.
        if ($cart_has_sample && $product_is_sample) {
            $validation = true;
        } elseif (!$cart_has_sample && !$product_is_sample) {
           $validation = true;
        } else{
            wc_add_notice('Sorry, you can only purchase sample products on their own. To purchase this product, please checkout your current cart or empty your cart and try again<a href="/cart" class="btn btn-idra cart-btn">View Cart</a>', 'error');
            $validation = false;
        }
    }
    // Otherwise, return true.
    return $validation;
}


//add a required field to checkout for name of the sample product you are requesting
//1. add the sample to the cart
// on the condition that it is enabled in the product page
add_action( 'woocommerce_single_product_summary', 'dei_add_sample_product_add_cart', 5 );
function dei_add_sample_product_add_cart() {
    if ( get_field( 'add_a_related_product_sample' ) ) {
?>
<form class="cart" method="post" enctype='multipart/form-data'>
<button type="submit" name="add-to-cart" value="46854" class="single_add_to_cart_button button alt">Order a Sample</button>
<input type="hidden" name="sample_product" value="<?php the_ID(); ?>">
</form>
 
<?php
}
}
 
// 2. Add the custom field to $cart_item
add_filter( 'woocommerce_add_cart_item_data', 'dei_store_sample_product_id', 10, 2 );
function dei_store_sample_product_id( $cart_item, $product_id ) {
    if( isset( $_POST['sample_product'] ) ) {
        $cart_item['sample_product'] = $_POST['sample_product'];
    }
return $cart_item; 
}
 
// 3. Preserve the custom field in the session
add_filter( 'woocommerce_get_cart_item_from_session', 'dei_get_cart_items_from_session', 10, 2 );
 
function dei_get_cart_items_from_session( $cart_item, $values ) {
    if ( isset( $values['sample_product'] ) ){
        $cart_item['sample_product'] = $values['sample_product'];
    }
return $cart_item;
}
 
// 4. Concatenate "Free Sample" with product name (CART & CHECKOUT)
add_filter( 'woocommerce_cart_item_name', 'dei_alter_cart_item_name', 10, 3 );
function dei_alter_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
if ( $product_name == "Sample Product" ) {
$product = wc_get_product( $cart_item["sample_product"] );
$product_name .=  " (" . $product->get_name() . ")";
}
return $product_name;
}




//5. Add "Free Sample" product name to order meta, which will show on thank you page, emails and orders 
add_action('woocommerce_add_order_item_meta','dei_save_posted_field_into_order', 10, 2);
function dei_save_posted_field_into_order( $itemID, $values ) {
    if ( !empty( $values['sample_product'] )) {
        $product = wc_get_product( $values['sample_product'] );
        $product_name .=  " (" . $product->get_name() . ")";
        wc_add_order_item_meta( $itemID, 'Sample for', $product_name );
    }
}