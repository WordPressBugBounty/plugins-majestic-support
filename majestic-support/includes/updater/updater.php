<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

do_action('majesticsupport_load_wp_plugin_file');
// check for plugin using plugin name
if (is_plugin_active('majestic-support/majestic-support.php')) {
	$MJTC_query = "SELECT * FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'versioncode' OR configname = 'last_version' OR configname = 'last_step_updater'";
	$MJTC_result = majesticsupport::$_db->get_results($MJTC_query);
	$MJTC_config = array();
	foreach($MJTC_result AS $MJTC_rs){
		$MJTC_config[$MJTC_rs->configname] = $MJTC_rs->configvalue;
	}
	if($MJTC_config['versioncode'] != ''){
		$MJTC_config['versioncode'] = MJTC_majesticsupportphplib::MJTC_str_replace('.', '', $MJTC_config['versioncode']);
	}
	if(!empty($MJTC_config['last_version']) && $MJTC_config['last_version'] != '' && $MJTC_config['last_version'] < $MJTC_config['versioncode']){
		$MJTC_last_version = $MJTC_config['last_version'] + 1; // files execute from the next version
		$MJTC_currentversion = $MJTC_config['versioncode'];
		for($MJTC_i = $MJTC_last_version; $MJTC_i <= $MJTC_currentversion; $MJTC_i++){
			$MJTC_path = MJTC_PLUGIN_PATH.'includes/updater/files/'.$MJTC_i.'.php';
			if(file_exists($MJTC_path)){
				include_once($MJTC_path);
			}
		}
	}
	$MJTC_mainfile = MJTC_PLUGIN_PATH.'majestic-support.php';
	$MJTC_contents = file_get_contents($MJTC_mainfile);
	if($MJTC_contents != ''){
		$MJTC_contents = MJTC_majesticsupportphplib::MJTC_str_replace("include_once 'includes/updater/updater.php';", '', $MJTC_contents);
	}
	file_put_contents($MJTC_mainfile, $MJTC_contents);

	function mjtc_recursiveremove($MJTC_dir) {
	    // --- START FILESYSTEM FIX ---
	    global $wp_filesystem;
	    if (!function_exists('wp_handle_upload')) {
	        do_action('majesticsupport_load_wp_file');
	    }
	    if ( ! WP_Filesystem() ) {
	        return false;
	    }
	    $MJTC_wp_filesystem = $wp_filesystem;
	    // --- END FILESYSTEM FIX ---

	    // Use WP_Filesystem delete method with recursive flag (true)
	    // This replaces glob(), is_dir(), is_file(), wp_delete_file(), and rmdir()
	    if ($MJTC_wp_filesystem->exists($MJTC_dir)) {
	        return $MJTC_wp_filesystem->delete($MJTC_dir, true);
	    }

	    return false;
	}           	
	$MJTC_dir = MJTC_PLUGIN_PATH.'includes/updater';
	mjtc_recursiveremove($MJTC_dir);

}



?>
