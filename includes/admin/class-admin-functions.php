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
			$cats = wp_get_post_terms( $product, 'product_cat');
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