<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_updates {

    static function MJTC_checkUpdates($MJTC_cversion=null) {
        if (is_null($MJTC_cversion)) {
            $MJTC_cversion = majesticsupport::$_currentversion;
        }
        $MJTC_installedversion = MJTC_updates::MJTC_getInstalledVersion();
        if ($MJTC_installedversion != $MJTC_cversion) {
			//UPDATE the last_version of the plugin
			$MJTC_query = "REPLACE INTO `".majesticsupport::$_db->prefix."mjtc_support_config` (`configname`, `configvalue`, `configfor`) VALUES ('last_version','','default');";
			majesticsupport::$_db->query($MJTC_query); //old actual
			$MJTC_query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname='versioncode'";
			$MJTC_versioncode = majesticsupport::$_db->get_var($MJTC_query);
            if($MJTC_versioncode != ''){
			    $MJTC_versioncode = MJTC_majesticsupportphplib::MJTC_str_replace('.','',$MJTC_versioncode);
            }
			$MJTC_query = "UPDATE `".majesticsupport::$_db->prefix."mjtc_support_config` SET configvalue = '".esc_sql($MJTC_versioncode)."' WHERE configname = 'last_version';";
			majesticsupport::$_db->query($MJTC_query);
            $MJTC_from = $MJTC_installedversion + 1;
            $to = $MJTC_cversion;
            // --- START FILESYSTEM FIX ---
            global $wp_filesystem;
            if (!function_exists('wp_handle_upload')) {
                do_action('majesticsupport_load_wp_file');
            }
            if ( ! WP_Filesystem() ) {
                return false;
            }
            $MJTC_wp_filesystem = $wp_filesystem;

            for ($MJTC_i = $MJTC_from; $MJTC_i <= $to; $MJTC_i++) {
                $MJTC_installfile = MJTC_PLUGIN_PATH . 'includes/updates/sql/' . $MJTC_i . '.sql';
                
                // Use $MJTC_wp_filesystem->exists instead of file_exists
                if ($MJTC_wp_filesystem->exists($MJTC_installfile)) {
                    $MJTC_delimiter = ';';
                    
                    // Use get_contents to read the file instead of fopen
                    $MJTC_file_content = $MJTC_wp_filesystem->get_contents($MJTC_installfile);
                    
                    if ($MJTC_file_content !== false) {
                        // Split content into lines to simulate fgets() behavior
                        $MJTC_file_lines = explode("\n", $MJTC_file_content);
                        $MJTC_query = array();

                        foreach ($MJTC_file_lines as $MJTC_line) {
                            $MJTC_query[] = $MJTC_line;
                            if (MJTC_majesticsupportphplib::MJTC_preg_match('~' . preg_quote($MJTC_delimiter, '~') . '\s*$~iS', end($MJTC_query)) === 1) {
                                $MJTC_query = MJTC_majesticsupportphplib::MJTC_trim(implode('', $MJTC_query));
                                if($MJTC_query != ''){
                                    $MJTC_query = MJTC_majesticsupportphplib::MJTC_str_replace("#__", majesticsupport::$_db->prefix, $MJTC_query);
                                }
                                if (!empty($MJTC_query)) {
                                    majesticsupport::$_db->query($MJTC_query);
                                }
                            }
                            if (is_string($MJTC_query) === true) {
                                $MJTC_query = array();
                            }
                        }
                    }
                }
            }
        }
    }

    static function MJTC_getInstalledVersion() {
        $MJTC_query = "SELECT configvalue FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configname = 'versioncode'";
        $MJTC_version = majesticsupport::$_db->get_var($MJTC_query);
        if (!$MJTC_version){
            $MJTC_version = '102';
        }
        else{
            if($MJTC_version != ''){
                $MJTC_version = MJTC_majesticsupportphplib::MJTC_str_replace('.', '', $MJTC_version);
            }
        }
        return $MJTC_version;
    }

}

?>
