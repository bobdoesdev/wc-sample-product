<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( !class_exists( 'WC_Sample_Product_Salesperson' ) ) {
	class WC_Sample_Product_Salesperson{

		public function __construct(){
			add_action('init', array($this, 'add_salesperson_role') );
		}

		//add saleserson role
		public function add_salesperson_role(){    
		    global $wp_roles;
		    if (!isset($wp_roles))
		        $wp_roles = new WP_Roles();
		    $auth = $wp_roles->get_role('author');
		    $wp_roles->add_role('salesperson', 'Salesperson', $auth->capabilities);
		}

	}
}