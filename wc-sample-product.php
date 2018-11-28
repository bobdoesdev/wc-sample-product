<?php 
/**
 * Plugin Name: WooCommerce Sample Product
 * Description: Allows for a sample product to be added to the from the single product page and adds a user role for Salesperson who will receive email to perform a follow up with customer.
 * Version: 1.0
 * Author: Bob, O'Brien, Digital Eel Inc.
 * Author URI:http://digitaleel.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins') ) ) ){

    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-sample-product.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-sample-product-salesperson.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-sample-product-metabox.php';

  add_action( 'plugins_loaded', 'wc_sp_plugin_startup_settings' );
  /**
   * Starts the plugin.
   *
   * @since 1.0.0
   */
  function wc_sp_plugin_startup_settings() {

    $sample_product = new WC_Sample_Product();
    $salesperson_user_role = new WC_Sample_Product_Salesperson();
    $sample_product_metabox = new WC_Sample_Product_Metabox();
      

  }

  function wc_sp_activate() {
    wp_insert_post(
      array(
        'post_type'     => 'product',
        'post_title' => 'Sample Product',
        'post_status' => 'publish'
        )
      );
  }
  register_activation_hook( __FILE__, 'wc_sp_activate' );

  function wc_sp_deactivate() {
    $sample_product = get_page_by_title('Sample Product', OBJECT, 'product');
    wp_delete_post( $sample_product->ID, true );
  }
  register_deactivation_hook( __FILE__, 'wc_sp_deactivate' );

}




