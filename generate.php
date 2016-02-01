<?php
if(isset($_REQUEST['makePOT'])){
	$current_dir = __DIR__;
	$file_name = basename($current_dir);
	$lang_dir = $current_dir."/language/$file_name.pot";
	$php_path = 'C:\xampp\php\php.exe';
	$makePotFile = 'C:\xampp\htdocs\wptools\makepot.php';
	$project = 'wp-plugin';
	exec($php_path. ' '.$makePotFile.' '.$project.' '.$current_dir.' '.$lang_dir);
}


if(isset($_REQUEST['change'])){
	$files_check = array();
	get_php_files(__DIR__);
	foreach ($files_check as $f){
		$file = file_get_contents($f);
		
		$file = str_replace('WooCommerce_Plugin_Boiler_Plate', 'WC_Order_Category_Sort', $file);
		$file = str_replace('WooCommerce Plugin Boiler Plate', 'WC Order Category Sort', $file);
		$file = str_replace('woocommerce-plugin-boiler-plate', 'wc-order-category-sort', $file);
		$file = str_replace('PLUGIN_NAME', 'WCOCS_NAME', $file);
		$file = str_replace('PLUGIN_SLUG', 'WCOCS_SLUG', $file);
		$file = str_replace('PLUGIN_TXT', 'WCOCS_TXT', $file);
		$file = str_replace('PLUGIN_DB', 'WCOCS_DB', $file);
		$file = str_replace('PLUGIN_V', 'WCOCS_V', $file);
		$file = str_replace('PLUGIN_PATH', 'WCOCS_PATH', $file);
		$file = str_replace('PLUGIN_LANGUAGE_PATH', 'WCOCS_LANGUAGE_PATH', $file);
		$file = str_replace('PLUGIN_INC', 'WCOCS_INC', $file);
		$file = str_replace('PLUGIN_ADMIN', 'WCOCS_ADMIN', $file);
		$file = str_replace('PLUGIN_SETTINGS', 'WCOCS_SETTINGS', $file);
		$file = str_replace('PLUGIN_URL', 'WCOCS_URL', $file);
		$file = str_replace('PLUGIN_CSS', 'WCOCS_CSS', $file);
		$file = str_replace('PLUGIN_IMG', 'WCOCS_IMG', $file);
		$file = str_replace('PLUGIN_JS', 'WCOCS_JS', $file);
		$file = str_replace('PLUGIN_FILE', 'WCOCS_FILE', $file);
		$file = str_replace('wc_pbp', 'wc_ocs', $file);		
		
		file_put_contents($f,$file); 
	}


}

function get_php_files($dir = __DIR__){
	global $files_check;
	$files = scandir($dir); 
	foreach($files as $file) {
		if($file == '' || $file == '.' || $file == '..' ){continue;}
		if(is_dir($dir.'/'.$file)){
			get_php_files($dir.'/'.$file);
		} else {
			if(pathinfo($dir.'/'.$file, PATHINFO_EXTENSION) == 'php' || pathinfo($dir.'/'.$file, PATHINFO_EXTENSION) == 'txt'){
				if($file == 'generate.php'){continue;}
				$files_check[$file] = $dir.'/'.$file;
			}
		}
	}
}
?>


