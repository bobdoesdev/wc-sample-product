<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( !class_exists( 'WC_Sample_Product' ) ) {

	class WC_Sample_Product{

		public function __construct(){
			// add_filter('woocommerce_add_to_cart_validation', array($this, 'do_not_add_to_cart'), 10, 2 );
			add_action( 'woocommerce_single_product_summary', array($this, 'add_sample_product_add_cart'), 5  );
			add_filter( 'woocommerce_add_cart_item_data', array($this, 'store_sample_product_id' ) , 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'get_cart_items_from_session') , 10, 2  );
			add_filter( 'woocommerce_cart_item_name', array($this, 'alter_cart_item_name') , 10, 3  );
			add_action('woocommerce_add_order_item_meta',array($this, 'save_posted_field_into_order') , 10, 2);
			add_action( 'woocommerce_add_to_cart_validation', array( $this, 'update_price' ), 10, 3 );


		}

		//*** Prevent mixture of sample and other prods in same cart ***//
		
		public function do_not_add_to_cart($validation, $product_id) {

		// Set flag false until we find a product in cat sample
	    $cart_has_sample = false;


		// Set $cat_check true if a cart item is in sample cat
	    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

	        $product = $cart_item['data'];

	        if ( $product->name === 'Sample Product') {
	            $cart_has_sample = true;
	            break;
	        }
	    }

	    $sample_product = get_page_by_title('Sample Product', OBJECT, 'product');
	    $product_is_sample = false;
	    if ( $product_id === $sample_product->ID ) {
	        $product_is_sample = true;
	    }

		// Return true if cart empty
	    // if (!WC()->cart->get_cart_contents_count() == 0) {
	    //     // If cart contains sample and product to be added is not sample, display error message and return false.
	    //     if ($cart_has_sample && $product_is_sample) {
	    //         $validation = true;
	    //     } elseif (!$cart_has_sample && !$product_is_sample) {
	    //        $validation = true;
	    //     } else{
	    //         wc_add_notice('Sorry, you can only purchase sample products on their own. To purchase this product, please checkout your current cart or empty your cart and try again<a href="/cart" class="btn btn-idra cart-btn">View Cart</a>', 'error');
	    //         $validation = false;
	    //     }
	    // }
	    return $validation;
		}
		
		public function update_price( $true,  $product_id,  $quantity ) {
		  // $cart_items = WC()->get_cart()->cart_contents;

		  $sample_product = get_page_by_title('Sample Product', OBJECT, 'product');
		  $product_is_sample = false;
		  if ( $product_id === $sample_product->ID ) {
		      $product_is_sample = true;
		      $cart_sample_product = new WC_Product($product_id);
		      $cart_sample_product->set_price('400');
		  }

		
		  // if ( $product_is_sample) {
		  //   $price = 100;
		  //   foreach ( $cart_items as $key => $value ) {
		  //     $value['data']->set_price( $price );
		  //   }
		  // }
		  return $true;
		}


		//add a required field to checkout for name of the sample product you are requesting
		//1. add the sample to the cart
		// on the condition that it is enabled in the product page

		//if sample product exists, include add to cart button
		public function add_sample_product_add_cart() {
		    if ( get_field( 'has_sample_product' ) ) {
		    	require_once plugin_dir_path(__DIR__) . 'public/checkout-form.php';
			}
		}
		 
		// 2. Add the custom field to $cart_item
		public function store_sample_product_id( $cart_item, $product_id ) {
		    if( isset( $_POST['sample_product'] ) ) {
		        $cart_item['sample_product'] = $_POST['sample_product'];
		    }
			return $cart_item; 
		}
		 
		// 3. Preserve the custom field in the session	 
		public function get_cart_items_from_session( $cart_item, $values ) {
		    if ( isset( $values['sample_product'] ) ){
		        $cart_item['sample_product'] = $values['sample_product'];
		    }
		return $cart_item;
		}
		 
		// 4. Concatenate "Free Sample" with product name (CART & CHECKOUT)
		public function alter_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
			if ( $product_name == "Sample Product" ) {
				$product = wc_get_product( $cart_item["sample_product"] );
				$product_name .=  " (" . $product->get_name() . ")";
			}
			return $product_name;
		}

		//5. Add "Free Sample" product name to order meta, which will show on thank you page, emails and orders 
		public function save_posted_field_into_order( $itemID, $values ) {
		    if ( !empty( $values['sample_product'] )) {
		        $product = wc_get_product( $values['sample_product'] );
		        $product_name .=  " (" . $product->get_name() . ")";
		        wc_add_order_item_meta( $itemID, 'Sample for', $product_name );
		    }
		}

	}

}



//if smaple is checked on product and value matches id of smaple product, add sample item to cart with price specified on each product page
//will i have to change the price of the sample product each time i try to add a sample to the cart?