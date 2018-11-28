<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( !class_exists( 'WC_Sample_Product_Metabox' ) ) {

	class WC_Sample_Product_Metabox{
		 
        public function __construct() {
            add_action( 'woocommerce_product_options_general_product_data', array($this, 'add_field'));
            add_action( 'woocommerce_process_product_meta', array($this, 'save_fields') );
        }
 
        public function add_field( $array ){
            echo '<div class="options_group">';
                          
                woocommerce_wp_checkbox( array(
                    'id'      => 'has_sample_product',
                    'value'   => get_post_meta( get_the_ID(), 'has_sample_product', true ),
                    'label'   => 'Is a sample of this product available?',
                    'desc_tip' => true,
                    'description' => 'Is there a sample of this product available for purchase?',
                ) );

                woocommerce_wp_text_input(
                    array(
                        'id'        => 'sample_product_price',
                        'value'     => get_post_meta( get_the_ID(), 'sample_product_price', true ),
                        'label'     => 'Sample Product Price ($)',
                        'type'      => 'price',
                        'desc_tip' => true,
                        'description' => 'Price of sample product',
                    )
                );

            echo '</div>';
        }

        public function save_fields( $id ){
            if( !empty( $_POST['has_sample_product'] ) ) {
                update_post_meta( $id, 'has_sample_product', $_POST['has_sample_product'] );
            }

            if (!empty( $_POST['sample_product_price'])) {
                update_post_meta( $id, 'sample_product_price', $_POST['sample_product_price'] );
            } 

        }

	}

}