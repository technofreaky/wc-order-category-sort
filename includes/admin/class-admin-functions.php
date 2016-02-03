<?php
/**
 * The admin-specific functionality of the plugin.
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WC_Order_Category_Sort_Admin_Fucntions {
    
    public function __construct() {
        add_filter('woocommerce_order_get_items',array($this,'sort_order'),10,2);
		add_action( 'wp_ajax_wcordersortchangeshelf', array($this,'show_ajax_form') );
		add_action( 'wp_ajax_wcordersortforechangeshelf', array($this,'force_change') );
    }
	
	public function force_change(){
		$productShelfs = wp_get_post_terms($_REQUEST['productid'], 'product_shelf');
		if(!empty($productShelfs)){
			foreach($productShelfs as $productShelf){
				wp_remove_object_terms( $_REQUEST['productid'], $productShelf->term_id, 'product_shelf' );
				$subarray['shelf'][$productShelf->term_id]['name'] = $productShelf->name;
				$subarray['shelf'][$productShelf->term_id]['count'] = $productShelf->count;
				$subarray['shelf'][$productShelf->term_id]['id'] = $productShelf->term_id;
			}		
		}
		wp_set_post_terms( $_REQUEST['productid'], $_REQUEST['wcordersortupdateshelf'], 'product_shelf', false );
		echo '<h2>Shelf Updated. <small>Please Close This POPUP</small></h2>';
		wp_die();
	}
	
	public function show_ajax_form(){

		if(isset($_REQUEST['productid'])){
			
 			$dropdownargs = array( 'show_option_all' => false, 'show_option_none' => false, 'orderby' => 'id', 'order' => 'ASC', 'show_count' => 1, 'hide_empty' => 0, 'child_of' => 0, 'exclude' => '', 'echo' => 0, 'selected' => 0, 'hierarchical' => 1, 'name' => 'wcordersortupdateshelf', 'id' => '', 'class' => 'postform', 'depth' => 0, 'tab_index' => 0, 'taxonomy' => 'product_shelf', 'hide_if_empty' => false, 'option_none_value' => -1, 'value_field' => 'term_id', );

			$echo = '';
			
			$product_name = '<a href="'.get_post_permalink($_REQUEST['productid']).'" >'.get_the_title($_REQUEST['productid']).'</a>';
			
			$echo .= '<form method="post"> <table class="flat-table" cellspacing="3" >';
				$echo .= '<tr>';
					$echo .= '<th>Product Name : </th>';
					$echo .= '<td>'.$product_name.'</td>';
				$echo .= '</tr>';
			
				$echo .= '<tr>';
					$echo .= '<th>Shelf : </th>';
					$echo .= '<td>'.wp_dropdown_categories($dropdownargs).'</td>';
				$echo .= '</tr>';
			
				$echo .= '<tr>';
					$echo .= '<td><input type="hidden" name="action" value="wcordersortforechangeshelf" /> </td>';
					$echo .= '<td><input type="submit" class="myButton "value="Update Shelf"/> </td>';
				$echo .= '</tr>';
			$echo .= '</table> </form>';
			
			$echo .= '<style>';
			$echo .= 'table { width: 100%; } 
					  th { background: #eee; color: black; font-weight: bold; }
					  td, th { padding: 6px; border: 1px solid #ccc; text-align: left; }';
			$echo .= ".myButton {	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #77b55a), color-stop(1, #72b352)); background:-moz-linear-gradient(top, #77b55a 5%, #72b352 100%); 	background:-webkit-linear-gradient(top, #77b55a 5%, #72b352 100%); 	background:-o-linear-gradient(top, #77b55a 5%, #72b352 100%); background:-ms-linear-gradient(top, #77b55a 5%, #72b352 100%); background:linear-gradient(to bottom, #77b55a 5%, #72b352 100%); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#77b55a', endColorstr='#72b352',GradientType=0); background-color:#77b55a; -moz-border-radius:4px; 	-webkit-border-radius:4px; 	border-radius:4px; 	border:1px solid #4b8f29; display:inline-block; cursor:pointer; color:#ffffff;  font-family:Arial; font-size:15px; font-weight:bold;  padding:6px 12px;	text-decoration:none;	text-shadow:0px 1px 0px #5b8a3c;} .myButton:hover { background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #72b352), color-stop(1, #77b55a)); 	background:-moz-linear-gradient(top, #72b352 5%, #77b55a 100%); 	background:-webkit-linear-gradient(top, #72b352 5%, #77b55a 100%); 	background:-o-linear-gradient(top, #72b352 5%, #77b55a 100%); 	background:-ms-linear-gradient(top, #72b352 5%, #77b55a 100%); 	background:linear-gradient(to bottom, #72b352 5%, #77b55a 100%); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#72b352', endColorstr='#77b55a',GradientType=0); 	background-color:#72b352; } .myButton:active { 	position:relative; 	top:1px; }";
			$echo .= '</style>';
			
			echo $echo;
		
		}

		wp_die();
	}
	
	public function sort_order($items,$order){
		$product_ids = $this->extract_product_ids($items);
		if(empty($product_ids)){return $items;}
		$cat_order = get_option(WCOCS_DB.'selected_category');
		$sortedIDS = $this->get_ordered_products($cat_order,$product_ids);
		return $this->sort_items($items,$sortedIDS,$product_ids);
		return $items;
	}
	
	public function extract_product_ids($items){
		$item_ids = array();
		foreach($items as $item_ID => $item){
			if(isset($item['product_id'])){
				$item_ids[$item_ID] = $item['product_id'];
			}
		}
		return $item_ids;
	}
	
	public function get_ordered_products($cat_order,$product_ids){
		$returned_sort = array();
		foreach($product_ids as $productID => $product){
			$addedTOSORT = false;
			$cats = wp_get_post_terms( $product, 'product_shelf');
			foreach($cats as $cat){
				if(!$addedTOSORT){
					$sort = array_search($cat->term_id, $cat_order);
					$returned_sort[$sort][] = $productID;
					$addedTOSORT = true;
				}
				
				 
			}
		}
		ksort($returned_sort);
		return $returned_sort;
	}
	
	
	public function sort_items($items,$sortedIDS,$product_ids){
		$gitems = array();
		foreach($sortedIDS as $ids){
			if(is_array($ids)){
				foreach($ids as $id){
					$gitems[$id] = $items[$id];
				}
			}
		}
		return $gitems;
	}
	
}
	


?>