<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_configurationModel {

    function getConfigurations() {
        $query = "SELECT configname,configvalue,addon
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` ";//WHERE configfor != 'ticketviaemail'";
        $MJTC_data = majesticsupport::$_db->get_results($query);

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        foreach ($MJTC_data AS $config) {
            if($config->addon == '' ||  in_array($config->addon, majesticsupport::$_active_addons)){
                majesticsupport::$_data[0][$config->configname] = $config->configvalue;
            }
        }

        majesticsupport::$_data[1] = MJTC_includer::MJTC_getModel('email')->getAllEmailsForCombobox();
        if(in_array('banemail', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('banemaillog')->checkbandata();
        }
        return;
    }

    function getConfigurationByFor($for) {
		if($for == 'ticketviaemail'){
			$query = "SELECT COUNT(configname) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($for)."'";
			$MJTC_count = majesticsupport::$_db->get_var($query);
			if($MJTC_count < 5){
				$query = "SELECT configname,configvalue
							FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` ";
				$MJTC_data = majesticsupport::$_db->get_results($query);
				if (majesticsupport::$_db->last_error != null) {
					MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
				}
				foreach ($MJTC_data AS $config) {
					majesticsupport::$_data[0][$config->configname] = $config->configvalue;
				}
				if(in_array('banemail', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('banemaillog')->checkbandata();
                }
                return;
			}
		}
        $query = "SELECT configname,configvalue
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($for)."'";
        $MJTC_data = majesticsupport::$_db->get_results($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        foreach ($MJTC_data AS $config) {
            majesticsupport::$_data[0][$config->configname] = $config->configvalue;
        }
        if(in_array('banemail', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('banemaillog')->checkbandata();
        }
        return;
    }
    function getCountByConfigFor($for) {
        if (( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())) {
            $query = "SELECT COUNT(configvalue)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($for). "' AND configname LIKE '%staff' AND configvalue = 1 " ;
        }else{
            $query = "SELECT COUNT(configvalue)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($for) . "' AND configname LIKE '%user' AND configvalue = 1 " ;
        }
        $MJTC_data = majesticsupport::$_db->get_var($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_data;
    }

    function storeDesktopNotificationLogo($filename) {
        majesticsupport::$_db->query("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_config` SET configvalue = '" . esc_sql($filename) . "' WHERE configname = 'logo_for_desktop_notfication_url' ");
    }

    function deleteDesktopNotificationsLogo() {
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];

        $maindir = wp_upload_dir();
        $path = $maindir['basedir'];
        $path = $path .'/'.$MJTC_datadirectory;

        $file_name = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('logo_for_desktop_notfication_url');

        $path = $path . '/attachmentdata/';
        $dsk_logo_file =  $path.$file_name;
        if($file_name != ''){
            if ( file_exists( $dsk_logo_file ) ) {
                wp_delete_file($dsk_logo_file);
            }
        }
    }


    function storeConfiguration($MJTC_data) {
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'save-configuration') ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $notsave = false;
        $updateColors = false;
        foreach ($MJTC_data AS $MJTC_key => $MJTC_value) {
            $query = true;

            if ($MJTC_key == 'offline_message') {
                $offline_message = $MJTC_value;
                if(!empty($offline_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['offline_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }

            if ($MJTC_key == 'visitor_message') {
                $visitor_message = $MJTC_value;
                if(!empty($visitor_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['visitor_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }

            if ($MJTC_key == 'new_ticket_message') {
                $new_ticket_message = $MJTC_value;
                if(!empty($new_ticket_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['new_ticket_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }

            if ($MJTC_key == 'feedback_thanks_message') {
                $feedback_thanks_message = $MJTC_value;
                if(!empty($feedback_thanks_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['feedback_thanks_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }
            if ($MJTC_key == 'screentag_position') {
                if ($MJTC_value != majesticsupport::$_config['screentag_position']) {
                    $updateColors = true;
                }
            }

            if ($MJTC_key == 'pagination_default_page_size') {
                if ($MJTC_value < 3) {
                    MJTC_message::MJTC_setMessage(esc_html(__('Pagination default page size not saved', 'majestic-support')), 'error');
                    continue;
                }
            }

            if($MJTC_key == 'del_logo_for_desktop_notfication' && $MJTC_value == 1){
                $this->deleteDesktopNotificationsLogo();
                $MJTC_key = 'logo_for_desktop_notfication_url';
                $MJTC_value = '';
            }


            if ($MJTC_key == 'data_directory') {
                $MJTC_data_directory = $MJTC_value;
                if(empty($MJTC_data_directory)){
                    MJTC_message::MJTC_setMessage(esc_html(__('Data directory cannot empty.', 'majestic-support')), 'error');
                    continue;
                }
                if(MJTC_majesticsupportphplib::MJTC_strpos($MJTC_data_directory, '/') !== false){
                    MJTC_message::MJTC_setMessage(esc_html(__('Data directory is not proper.', 'majestic-support')), 'error');
                    continue;
                }
                $path = MJTC_PLUGIN_PATH.'/'.$MJTC_data_directory;
                if ( ! file_exists($path)) {
                   mkdir($path, 0755);
                }
                if( ! is_writeable($path)){
                    MJTC_message::MJTC_setMessage(esc_html(__('Data directory is not writable.', 'majestic-support')), 'error');
                    continue;
                }
            }
            if ($MJTC_key == 'system_slug') {
                if(empty($MJTC_value)){
                    MJTC_message::MJTC_setMessage(esc_html(__('System slug not be empty.', 'majestic-support')), 'error');
                    continue;
                }
                if($MJTC_value != ''){
                    $MJTC_value = MJTC_majesticsupportphplib::MJTC_str_replace(' ', '-', $MJTC_value);
                }
                $query = 'SELECT COUNT(ID) FROM `'.majesticsupport::$_db->prefix.'posts` WHERE post_name = "'.esc_sql($MJTC_value).'"';
                $MJTC_countslug = majesticsupport::$_db->get_var($query);
                if($MJTC_countslug >= 1){
                    MJTC_message::MJTC_setMessage(esc_html(__('System slug is conflicted with post or page slug.', 'majestic-support')), 'error');
                    continue;
                }
            }
            majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => $MJTC_value), array('configname' => $MJTC_key));
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                $notsave = true;
            }
        }
        if ($notsave == false) {
            MJTC_message::MJTC_setMessage(esc_html(__('The setting has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('The setting not has been stored', 'majestic-support')), 'error');
        }
        if ($updateColors == true) {
            MJTC_includer::MJTC_getModel('majesticsupport')->updateColorFile();
        }
        update_option('rewrite_rules', '');

        if (isset($_FILES['logo_for_desktop_notfication'])) { // upload image for desktop notifications
            MJTC_includer::MJTC_getObjectClass('uploads')->MJTC_uploadDesktopNotificationLogo();
        }
        if (isset($_FILES['support_custom_img'])) { // upload image for custom image
            $this->storeSupportCustomImage();
        }
        return;
    }

    function storeSupportCustomImage() {
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $maindir = wp_upload_dir();
        $basedir = $maindir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        
        $path = $basedir . '/' . $MJTC_datadirectory;
        if (!file_exists($path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($path);
        }
        $isupload = false;
        $path = $path . '/supportImg';
        if (!file_exists($path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($path);
        }
        
        if ($_FILES['support_custom_img']['size'] > 0) {
            $file_name = MJTC_majesticsupportphplib::MJTC_str_replace(' ', '_', sanitize_file_name($_FILES['support_custom_img']['name']));
            $file_tmp = majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['tmp_name']); // actual location // MJTC_sanitizeData() function uses wordpress santize functions

            $userpath = $path;
            $isupload = true;
        }
        if ($isupload) {
            $this->uploadfor = 'supportcustomlogo';
            // Register our path override.
            add_filter( 'upload_dir', array($this,'majesticsupport_upload_custom_logo'));
            // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
            $result = array();
            $file = array(
                'name' => sanitize_file_name($_FILES['support_custom_img']['name']),
                'type' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['type']),
                'tmp_name' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['tmp_name']),
                'error' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['error']),
                'size' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['size']),
            ); // MJTC_sanitizeData() function uses wordpress santize functions
            $result = wp_handle_upload($file, array('test_form' => false));
            if ( $result && ! isset( $result['error'] ) ) {
                $this->setSupportCustomImage($file_name, $userpath);
            }
            // Set everything back to normal.
            remove_filter( 'upload_dir', array($this,'majesticsupport_upload_custom_logo'));
        }
    }

    function majesticsupport_upload_custom_logo( $dir ) {
        if($this->uploadfor == 'supportcustomlogo'){
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $path = $MJTC_datadirectory . '/supportImg';
            $array = array(
                'path'   => $dir['basedir'] . '/' . $path,
                'url'    => $dir['baseurl'] . '/' . $path,
                'subdir' => '/'. $path,
            ) + $dir;
            return $array;
        }else{
            return $dir;
        }
    }

    function setSupportCustomImage($filename, $userpath){
        $query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'support_custom_img'";
        $MJTC_key = majesticsupport::$_db->get_var($query);
        if ($MJTC_key) {
            $unlinkPath = $userpath.'/'.$MJTC_key;
            if (is_file($unlinkPath)) {
                wp_delete_file($unlinkPath);
            }
        }
        majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => $filename), array('configname' => 'support_custom_img'));
    }

    function deleteSupportCustomImage() {

        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($nonce, 'delete-support-customimage')) {
            die('Security check Failed');
        }

        $maindir = wp_upload_dir();
        $basedir = trailingslashit($maindir['basedir']);
        $MJTC_datadirectory = isset(majesticsupport::$_config['data_directory']) ? sanitize_text_field(majesticsupport::$_config['data_directory']) : '';
        $path = $basedir . trailingslashit($MJTC_datadirectory) . 'supportImg/';

        $query = "SELECT configvalue FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configname = 'support_custom_img'";
        $MJTC_key = majesticsupport::$_db->get_var($query);

        if ($MJTC_key) {
            $MJTC_key = sanitize_file_name($MJTC_key); // Sanitize filename
            $unlinkPath = realpath($path . $MJTC_key); // Get absolute path

            // Ensure the file is within the allowed directory
            if ($unlinkPath && MJTC_majesticsupportphplib::MJTC_strpos($unlinkPath, realpath($path)) === 0 && is_file($unlinkPath)) {
                wp_delete_file($unlinkPath);
            }
        }

        // Update database to remove reference
        majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => 0), array('configname' => 'support_custom_img'));

        return 'success';
    }

    function getEmailReadTime() {
        $time = null;
        $query = "SELECT config.configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` AS config WHERE config.configname = 'lastEmailReadingTime'";
        $time = majesticsupport::$_db->get_var($query);
        return $time;
    }

    function setEmailReadTime($time) {
        majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => $time), array('configname' => 'lastEmailReadingTime'));
    }

    function getConfiguration() {
        do_action('majesticsupport_load_wp_plugin_file');
        // check for plugin using plugin name
        if (is_plugin_active('majestic-support/majestic-support.php')) {
            //plugin is activated
            $query = "SELECT config.* FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` AS config WHERE config.configfor != 'ticketviaemail'";
            $config = majesticsupport::$_db->get_results($query);
            foreach ($config as $conf) {
                majesticsupport::$_config[$conf->configname] = $conf->configvalue;
            }
            majesticsupport::$_config['config_count'] = COUNT($config);
        }
    }

    function getCheckCronKey() {
        $query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'ck'";
        $MJTC_key = majesticsupport::$_db->get_var($query);
        if ($MJTC_key && $MJTC_key != '')
            return true;
        else
            return false;
    }

    function genearateCronKey() {
        $MJTC_key = MJTC_majesticsupportphplib::MJTC_md5(gmdate('Y-m-d'));
        $query = "UPDATE `".majesticsupport::$_db->prefix."mjtc_support_config` SET configvalue = '".esc_sql($MJTC_key)."' WHERE configname = 'ck'" ;
        majesticsupport::$_db->query($query);
        return true;
    }

    function getCronKey($passkey) {
        if ($passkey == MJTC_majesticsupportphplib::MJTC_md5(gmdate('Y-m-d'))) {
            $query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'ck'";
            $MJTC_key = majesticsupport::$_db->get_var($query);
            return $MJTC_key;
        }
        else
            return false;
    }

    function getConfigValue($configname){
        $query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = '".esc_sql($configname)."'";
        $configvalue = majesticsupport::$_db->get_var($query);
        return $configvalue;
    }

    function getPageList() {
        $query = "SELECT ID AS id, post_title AS text FROM `" . majesticsupport::$_db->prefix . "posts` WHERE post_type = 'page' AND post_status = 'publish' ";
        $emails = majesticsupport::$_db->get_results($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $emails;
    }

    function getWooCommerceCategoryList() {
        $orderby = 'term_id';
        $order = 'desc';
        $hide_empty = false ;
        $cat_args = array(
            'orderby'    => $orderby,
            'order'      => $order,
            'hide_empty' => $hide_empty,
        );
        $product_categories = get_terms( 'product_cat', $cat_args );
        $catList = array();
        foreach ($product_categories as $category) {
            $catList[] = (object) array('id' => $category->term_id, 'text' => $category->name);
        }
        return $catList;
    }

    function getConfigurationByConfigName($configname) {
        $query = "SELECT configvalue
                  FROM  `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname ='" . esc_sql($configname) . "'";
        $result = majesticsupport::$_db->get_var($query);
        return $result;
    }

    function getCountConfig() {
        $query = "SELECT COUNT(*)
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_config`";
        $result = majesticsupport::$_db->get_var($query);
        return $result;
    }

    function storeAutoUpdateConfig() {

        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $configvalue = MJTC_request::MJTC_getVar('mjtc_addons_auto_update','','');

        if (!is_numeric($configvalue)) { //can only have numric value
            return false;
        }

        $error = false;
        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_config` SET `configvalue` = ".esc_sql($configvalue)." WHERE `configname`= 'mjtc_addons_auto_update'";
        if (false === majesticsupport::$_db->query($query)) {
            $error = true;
        }

        if ($error) {
            MJTC_message::MJTC_setMessage(esc_html(__('Something went wrong!', 'majestic-support')), 'error');
            return WPJOBPORTAL_SAVE_ERROR;
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('The setting has been stored.', 'majestic-support')), 'updated');
        }
        return;
    }
}

?>
