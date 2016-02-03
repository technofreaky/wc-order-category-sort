<?php
/**
 * The admin-specific functionality of the plugin.
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WC_Order_Category_Sort_Admin extends WC_Order_Category_Sort {

    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ),99);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ));
		add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
	}

    /**
     * Inits Admin Sttings
     */
    public function admin_init(){
		new WC_Order_Category_Sort_Admin_Fucntions;
    }
 
    
	/**
	 * Add a new integration to WooCommerce.
	 */
	public function settings_page( $integrations ) {
        foreach(glob(WCOCS_ADMIN.'woocommerce-settings*.php' ) as $file){
            $integrations[] = require_once($file);
        }
		return $integrations;
	}
    
    /**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() { 
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_style(WCOCS_SLUG.'_core_style',WCOCS_CSS.'style.css' , array(), WCOCS_V, 'all' );  
        }
	}
	
    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
        //if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_script(WCOCS_SLUG.'_core_script', WCOCS_JS.'script.js', array('jquery'), WCOCS_V, false ); 
       // }
 
	}
    
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    public function current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
    
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    public function get_screen_ids(){
        $screen_ids = array();
        return $screen_ids;
    }
    
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( WCOCS_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Report Issue',WCOCS_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author',WCOCS_TXT) );
		}
		return $plugin_meta;
	}	    
}

?>