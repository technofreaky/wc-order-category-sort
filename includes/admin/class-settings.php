<?php
/**
 * WooCommerce General Settings
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Order_Category_Sort_Settings' ) ) :

/**
 * WC_Admin_Settings_General
 */
class WC_Order_Category_Sort_Settings  {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_products', array($this,'get_sections'));
		add_filter( 'woocommerce_get_settings_products', array($this,'get_settings'), 10, 2 );
		add_action( 'woocommerce_admin_field_wc_custom_category_sort', array( $this, 'wc_custom_category_sort_settings' ) );
		add_action( 'woocommerce_settings_save_products', array( $this, 'save' ) );
	}	

    /**
     * Get sections
     *
     * @return array
     */
    public function get_sections($sections) {
		$sections['wcocs'] = __( 'WC Order Category Sort', WCOCS_TXT);
		return $sections;
    }
    
    
    
    
    
    
    //public function output_settings(){
    //    global $current_section;
    //    $settings = $this->get_settings( $current_section ); 
    //    WC_Admin_Settings::output_fields( $settings );
    //}    
    
    
	/**
	 * Get settings array
	 *
	 * @return array
	 */
	function get_settings( $settings, $current_section) {
		if ( $current_section == 'wcocs' ) {
			$args = array( 'hide_empty' => false,'fields' => 'id=>name'); 
			$terms = get_terms('product_shelf', $args);
			
			$settings_slider = array();
			$settings_slider[] = array( 
				'name' => __( 'WC Order Category Sort', WCOCS_TXT ), 
				'type' => 'title', 
				'desc' => __( 'The following options are used to configure WC Order Category Sort', WCOCS_TXT), 
				'id' => 'wcocs' );

			$settings_slider[] = array(
				'name'     => __( 'Category List', WCOCS_TXT ),
				'desc_tip' => __( 'This will automatically insert your slider into the single product page', WCOCS_TXT ),
				'id'       => WCOCS_DB.'selected_category',
				'type'     => 'multiselect',
				'css'      => 'min-width:300px;',
				'options' => $terms,
				'class' => 'wc-enhanced-select',
				'desc'     => __( 'Select Categories To Order It', WCOCS_TXT ),
			);

			$settings_slider[] = array( 'type' => 'wc_custom_category_sort', );
			$settings_slider[] = array( 'type' => 'sectionend', 'id' => 'wcocs' );		
			return $settings_slider;
		} 
		else { return $settings; }
	}
 
	
	public function wc_custom_category_sort_settings(){
	?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Category Sort', WCOCS_TXT ) ?></th>
			<td class="forminp">
				<table class="wc_gateways wc_order_category_sort widefat" cellspacing="0" style="width:50%;">
					<thead>
						<tr>
							<?php
								$columns = array(
									'sort'     => '',
									'term_id'  => __("Term ID",WCOCS_TXT),
									'name'     => __( 'Category Name', WCOCS_TXT ),
									'slug'     => __( 'Category Slug', WCOCS_TXT ),
									//'order'   => __( 'Order', WCOCS_TXT )
								);

								foreach ( $columns as $key => $column ) {
									echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						$selected_categories = get_option(WCOCS_DB.'selected_category');
						if(!empty($selected_categories)){
							foreach ($selected_categories as $category ) {
								echo '<tr>';
								$current_term = get_term($category,'product_shelf');
								if($current_term == null){continue;}
								foreach ( $columns as $key => $column ) {

									switch ( $key ) {

										case 'sort' :
											echo '<td width="1%" class="sort">
												<input type="hidden" name="'.WCOCS_DB.'order_category[]" value="'.esc_attr($category).'" />
											</td>';
										break;

										case 'term_id' :
											echo '<td class="name">
												<a href="#">' . esc_html( $current_term->term_id ) . '</a>
											</td>';
										break;

										case 'name' :
											echo '<td class="name">' . esc_html( $current_term->name ) . '</td>';
										break;
										case 'slug' :
											echo '<td class="name">' . esc_html( $current_term->slug ) . '</td>';
										break;


										default :

										break;
									}
								}

								echo '</tr>';
							}
						} else {
							echo '<tr>';
								echo '<td colspan="4" >';
									echo '<p>'.__("No Shelf Selected",WCOCS_TXT).'</p>';
								echo '</td>';
							echo '</tr>';
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;
		
		$ordered = isset($_POST[WCOCS_DB.'order_category']) ? $_POST[WCOCS_DB.'order_category'] : array();
		$source = isset($_POST[ WCOCS_DB.'selected_category' ]) ? $_POST[ WCOCS_DB.'selected_category' ] : array();

		if(!empty($source)){
			foreach($source as $cats){ if(!in_array($cats,$ordered)){$ordered[] = $cats;} }
			foreach($ordered as $id=>$cats){ if(!in_array($cats,$source)){unset($ordered[$id]);} }
			$_POST[ WCOCS_DB.'selected_category'] = $ordered;
		}

		$settings = $this->get_settings(array(),'wcocs');
		WC_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new WC_Order_Category_Sort_Settings();
