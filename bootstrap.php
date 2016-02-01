<?php 

if ( ! defined( 'WPINC' ) ) { die; }
 
class WC_Order_Category_Sort {
	public $version = '0.1';
	public $plugin_vars = array();
	
	protected static $_instance = null; # Required Plugin Class Instance
    protected static $functions = null; # Required Plugin Class Instance
	protected static $admin = null;     # Required Plugin Class Instance
	protected static $settings = null;  # Required Plugin Class Instance

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->define_constant();
        $this->load_required_files();
        $this->init_class();
        add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile',  array( $this, 'load_plugin_mo_files' ), 10, 2);
    }
    
    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files(){
       if($this->is_request('admin')){
           $this->load_files(WCOCS_ADMIN.'class-*.php');
       } 

    }
    
    /**
     * Inits loaded Class
     */
    private function init_class(){
        if($this->is_request('admin')){
            self::$admin = new WC_Order_Category_Sort_Admin;
        }
    }
    
	# Returns Plugin's Functions Instance
	public function func(){
		return self::$functions;
	}
	
	# Returns Plugin's Settings Instance
	public function settings(){
		return self::$settings;
	}
	
	# Returns Plugin's Admin Instance
	public function admin(){
		return self::$admin;
	}
    
    /**
     * Loads Files Based On Give Path & regex
     */
    protected function load_files($path,$type = 'require'){
        foreach( glob( $path ) as $files ){
            if($type == 'require'){ require_once( $files ); } 
			else if($type == 'include'){ include_once( $files ); }
        } 
    }
    
    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded(){
        load_plugin_textdomain(WCOCS_TXT, false, WCOCS_LANGUAGE_PATH );
    }
    
    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if (WCOCS_TXT === $domain)
            return WCOCS_LANGUAGE_PATH.'/'.get_locale().'.mo';

        return $mofile;
    }
    
    /**
     * Define Required Constant
     */
    private function define_constant(){
        $this->define('WCOCS_NAME', 'WC Order Category Sort'); # Plugin Name
        $this->define('WCOCS_SLUG', 'wc-order-category-sort'); # Plugin Slug
        $this->define('WCOCS_TXT',  'wc-order-category-sort'); #plugin lang Domain
		$this->define('WCOCS_DB', 'wc_ocs');
		$this->define('WCOCS_V',$this->version); # Plugin Version
		$this->define('WCOCS_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
		$this->define('WCOCS_LANGUAGE_PATH',WCOCS_PATH.'languages'); # Plugin Language Folder
		$this->define('WCOCS_INC',WCOCS_PATH.'includes/'); # Plugin INC Folder
		$this->define('WCOCS_ADMIN',WCOCS_INC.'admin/'); # Plugin Admin Folder
		$this->define('WCOCS_SETTINGS',WCOCS_INC.'admin/settings/'); # Plugin Settings Folder
		$this->define('WCOCS_URL',plugins_url('', __FILE__ ).'/');  # Plugin URL
		$this->define('WCOCS_CSS',WCOCS_URL.'includes/css/'); # Plugin CSS URL
		$this->define('WCOCS_IMG',WCOCS_URL.'includes/img/'); # Plugin IMG URL
		$this->define('WCOCS_JS',WCOCS_URL.'includes/js/'); # Plugin JS URL
        $this->define('WCOCS_FILE',plugin_basename( __FILE__ )); # Current File
    }
	
    /**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
    protected function define($key,$value){
        if(!defined($key)){
            define($key,$value);
        }
    }
    
	 
									 
	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
}
?>