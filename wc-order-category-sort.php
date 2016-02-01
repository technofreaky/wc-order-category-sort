<?php
/**
 * Plugin Name:       WC Order Category Sort
 * Plugin URI:        https://github.com/technofreaky/wc-order-category-sort
 * Description:       Sort Products Based On Category In Orders 
 * Version:           1.0
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       wc-order-category-sort
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * GitHub Plugin URI: https://github.com/technofreaky/wc-order-category-sort
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
require_once(plugin_dir_path(__FILE__).'bootstrap.php');
require_once(plugin_dir_path(__FILE__).'includes/class-dependencies.php');


if(WC_Order_Category_Sort_Dependencies()){
	if(!function_exists('WC_Order_Category_Sort')){
		function WC_Order_Category_Sort(){
			return WC_Order_Category_Sort::get_instance();
		}
	}
	WC_Order_Category_Sort();
}

?>