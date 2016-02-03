<?php
/*
Plugin Name: Custom List Table Example
Plugin URI: http://www.mattvanandel.com/
Description: A highly documented plugin that demonstrates how to create custom List Tables using official WordPress APIs.
Version: 1.4.1
Author: Matt van Andel
Author URI: http://www.mattvanandel.com
License: GPL2
*/
/*  Copyright 2015  Matthew Van Andel  (email : matt@mattvanandel.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/* == NOTICE ===================================================================
 * Please do not alter this file. Instead: make a copy of the entire plugin, 
 * rename it, and work inside the copy. If you modify this plugin directly and 
 * an update is released, your changes will be lost!
 * ========================================================================== */



/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class TT_Example_List_Table extends WP_List_Table {
 


	
    function __construct(){
        global $status, $page;
        parent::__construct( array(
            'singular'  => 'product_custom_listing',
            'plural'    => 'product_custom_listing',
            'ajax'      => false
        ) );
    }
	
	public function get_products (){
		$return_post = array();
		$args = array( 'hide_empty' => false,'fields' => 'id=>name'); 
		$shelfs = get_terms('product_shelf', $args);
		foreach($shelfs as $shelfI => $shelfN){
			$postIDS = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'product',  'tax_query' => array(array( 'taxonomy' => 'product_shelf', 'field' => 'term_id', 'terms' => $shelfI))));
			if(empty($postIDS)){continue;}
		    foreach($postIDS as $post){
				$subarray = array();
				$subarray['ID'] = $post->ID;
				$subarray['title'] = $post->post_title;
				$subarray['view_link'] = $post->guid;
				$subarray['edit_link'] = get_edit_post_link( $post->ID );
				$subarray['sku'] = get_post_meta($post->ID,'_sku',true);
				$productShelfs = wp_get_post_terms($post->ID, 'product_shelf');
				$categorys = wp_get_post_terms($post->ID, 'product_cat');

				foreach($categorys as $category){
					$subarray['productcat'][$category->term_id]['name'] = $category->name;
					$subarray['productcat'][$category->term_id]['count'] = $category->count;
					$subarray['productcat'][$category->term_id]['id'] = $category->term_id;
				}
				
				foreach($productShelfs as $productShelf){
					$subarray['shelf'][$productShelf->term_id]['name'] = $productShelf->name;
					$subarray['shelf'][$productShelf->term_id]['count'] = $productShelf->count;
					$subarray['shelf'][$productShelf->term_id]['id'] = $productShelf->term_id;
				}
				$return_post[] = $subarray;
				unset($subarray);
			}
		}
		return $return_post;
	}
	

	function get_columns(){
        $columns = array(
            'id'     => __('ID',WCOCS_TXT),
			'name'     => __('Product Name',WCOCS_TXT),
            'category'    => __('Category',WCOCS_TXT),
            'shelf'  => __('Shelf',WCOCS_TXT),
        );
        return $columns;
    }	

    function column_default($item, $column_name){ return print_r($item,true); }


    function column_name($item){
		$ajaxURL = admin_url('admin-ajax.php?action=wcordersortchangeshelf&productid='.$item['ID']).'&TB_iframe=true&width=600&height=250';
        $actions = array( 
			'view' => sprintf('<a href="%s">'.__('View').'</a>',$item['view_link']), 
			'edit' => sprintf('<a href="%s" style="color:red;">'.__('Edit').'</a>',$item['edit_link']), 
			'changeshelf' => sprintf('<a href="%s" class="thickbox">'.__('Change Shelf').'</a>',$ajaxURL),
		);       

		return sprintf('%1$s <span style="color:silver">(SKU:%2$s)</span>%3$s',
            /*$1%s*/ '<a href="'.$item['view_link'].'"> '.$item['title'].'</a>',
            /*$2%s*/ $item['sku'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    function column_id($item){return $item['ID']; }


	function column_category($item){
		$return = '';
		if(!empty($item['productcat'])){
			foreach($item['productcat'] as $category){
				$return .= '<span class="prod_cat_wcosort">'.$category['name'].'<span> ('.$category['count'].') </span> </span>';
			}
		}
		
		return $return;
	}
	
	function column_shelf($item){
		$return = '';
		if(!empty($item['shelf'])){
			foreach($item['shelf'] as $category){
				$return .= '<span class="prod_cat_wcosort">'.$category['name'].'<span> ('.$category['count'].') </span> </span>';
			}
		}
		
		return $return;
	}


    function get_sortable_columns() {
        $sortable_columns = array(
            'name'     => array('title',true),     //true means it's already sorted
            //'category'    => array('productcat',false),
            //'shelf'  => array('shelf',false)
        );
        return $sortable_columns;
    }


    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }


    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }
 
	
    function prepare_items() {
        global $wpdb;
		$per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $this->get_products(); 
		function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order==='asc') ? $result : -$result;
        }
        usort($data, 'usort_reorder');
		$current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }


}


