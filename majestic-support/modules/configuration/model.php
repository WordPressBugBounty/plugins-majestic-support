<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_configurationModel {

    function getConfigurations() {
        $MJTC_query = "SELECT configname,configvalue,addon
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` ";//WHERE configfor != 'ticketviaemail'";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        foreach ($MJTC_data AS $MJTC_config) {
            // if($MJTC_config->addon == '' ||  in_array($MJTC_config->addon, majesticsupport::$_active_addons)){
                majesticsupport::$_data[0][$MJTC_config->configname] = $MJTC_config->configvalue;
            // }
        }

        majesticsupport::$_data[1] = MJTC_includer::MJTC_getModel('email')->getAllEmailsForCombobox();
        if(in_array('banemail', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('banemaillog')->checkbandata();
        }
        return;
    }

    function getConfigurationByFor($MJTC_for) {
		if($MJTC_for == 'ticketviaemail'){
			$MJTC_query = "SELECT COUNT(configname) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($MJTC_for)."'";
			$MJTC_count = majesticsupport::$_db->get_var($MJTC_query);
			if($MJTC_count < 5){
				$MJTC_query = "SELECT configname,configvalue
							FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` ";
				$MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
				if (majesticsupport::$_db->last_error != null) {
					MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
				}
				foreach ($MJTC_data AS $MJTC_config) {
					majesticsupport::$_data[0][$MJTC_config->configname] = $MJTC_config->configvalue;
				}
				if(in_array('banemail', majesticsupport::$_active_addons)){
                    MJTC_includer::MJTC_getModel('banemaillog')->checkbandata();
                }
                return;
			}
		}
        $MJTC_query = "SELECT configname,configvalue
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($MJTC_for)."'";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        foreach ($MJTC_data AS $MJTC_config) {
            majesticsupport::$_data[0][$MJTC_config->configname] = $MJTC_config->configvalue;
        }
        if(in_array('banemail', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('banemaillog')->checkbandata();
        }
        return;
    }
    function getCountByConfigFor($MJTC_for) {
        if (( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff())) {
            $MJTC_query = "SELECT COUNT(configvalue)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($MJTC_for). "' AND configname LIKE '%staff' AND configvalue = 1 " ;
        }else{
            $MJTC_query = "SELECT COUNT(configvalue)
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configfor = '".esc_sql($MJTC_for) . "' AND configname LIKE '%user' AND configvalue = 1 " ;
        }
        $MJTC_data = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_data;
    }

    function storeDesktopNotificationLogo($MJTC_filename) {
        majesticsupport::$_db->query("UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_config` SET configvalue = '" . esc_sql($MJTC_filename) . "' WHERE configname = 'logo_for_desktop_notfication_url' ");
    }

    function deleteDesktopNotificationsLogo() {
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];

        $MJTC_maindir = wp_upload_dir();
        $MJTC_path = $MJTC_maindir['basedir'];
        $MJTC_path = $MJTC_path .'/'.$MJTC_datadirectory;

        $MJTC_file_name = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('logo_for_desktop_notfication_url');

        $MJTC_path = $MJTC_path . '/attachmentdata/';
        $MJTC_dsk_logo_file =  $MJTC_path.$MJTC_file_name;
        if($MJTC_file_name != ''){
            if ( file_exists( $MJTC_dsk_logo_file ) ) {
                wp_delete_file($MJTC_dsk_logo_file);
            }
        }
    }

    function storeConfiguration($MJTC_data) {
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'save-configuration') ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }

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

        $MJTC_notsave = false;
        $MJTC_updateColors = false;
        foreach ($MJTC_data AS $MJTC_key => $MJTC_value) {
            $MJTC_query = true;

            if ($MJTC_key == 'offline_message') {
                $MJTC_offline_message = $MJTC_value;
                if(!empty($MJTC_offline_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['offline_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }

            if ($MJTC_key == 'visitor_message') {
                $MJTC_visitor_message = $MJTC_value;
                if(!empty($MJTC_visitor_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['visitor_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }

            if ($MJTC_key == 'new_ticket_message') {
                $MJTC_new_ticket_message = $MJTC_value;
                if(!empty($MJTC_new_ticket_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['new_ticket_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }

            if ($MJTC_key == 'feedback_thanks_message') {
                $MJTC_feedback_thanks_message = $MJTC_value;
                if(!empty($MJTC_feedback_thanks_message)){
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->getSanitizedEditorData($_POST['feedback_thanks_message']);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->msremovetags($MJTC_value);
                    $MJTC_value = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_value);// remove slashes with quotes.
                }
            }
            if ($MJTC_key == 'screentag_position') {
                if ($MJTC_value != majesticsupport::$_config['screentag_position']) {
                    $MJTC_updateColors = true;
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
                $MJTC_path = MJTC_PLUGIN_PATH.'/'.$MJTC_data_directory;
                
                // Replaced file_exists with $MJTC_wp_filesystem->exists
                if ( ! $MJTC_wp_filesystem->exists($MJTC_path)) {
                   // Replaced mkdir with $MJTC_wp_filesystem->mkdir
                   $MJTC_wp_filesystem->mkdir($MJTC_path, 0755);
                }
                
                // Replaced is_writeable with $MJTC_wp_filesystem->is_writable
                if( ! $MJTC_wp_filesystem->is_writable($MJTC_path)){
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
                $MJTC_query = 'SELECT COUNT(ID) FROM `'.majesticsupport::$_db->prefix.'posts` WHERE post_name = "'.esc_sql($MJTC_value).'"';
                $MJTC_countslug = majesticsupport::$_db->get_var($MJTC_query);
                if($MJTC_countslug >= 1){
                    MJTC_message::MJTC_setMessage(esc_html(__('System slug is conflicted with post or page slug.', 'majestic-support')), 'error');
                    continue;
                }
            }
            majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => $MJTC_value), array('configname' => $MJTC_key));
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                $MJTC_notsave = true;
            }
        }
        if ($MJTC_notsave == false) {
            MJTC_message::MJTC_setMessage(esc_html(__('The setting has been stored.', 'majestic-support')), 'updated');
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('The setting has not been stored.', 'majestic-support')), 'error');
        }
        if ($MJTC_updateColors == true) {
            MJTC_includer::MJTC_getModel('majesticsupport')->updateColorFile();
        }
        update_option('rewrite_rules', '');

        if (isset($_FILES['logo_for_desktop_notfication'])) { // upload image for desktop notifications
            MJTC_includer::MJTC_getObjectClass('uploads')->MJTC_uploadDesktopNotificationLogo();
        }
        if (isset($_FILES['support_custom_img'])) { // upload image for custom image
            $this->storeSupportCustomImage($MJTC_nonce);
        }
        return;
    }

    function storeSupportCustomImage($MJTC_nonce) {
        if (! wp_verify_nonce( $MJTC_nonce, 'save-configuration') ) {
            die( 'Security check Failed' );
        }
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $MJTC_maindir = wp_upload_dir();
        $MJTC_basedir = $MJTC_maindir['basedir'];
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        
        $MJTC_path = $MJTC_basedir . '/' . $MJTC_datadirectory;
        if (!file_exists($MJTC_path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        }
        $MJTC_isupload = false;
        $MJTC_path = $MJTC_path . '/supportImg';
        if (!file_exists($MJTC_path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        }
        
        if ($_FILES['support_custom_img']['size'] > 0) {
            $MJTC_file_name = MJTC_majesticsupportphplib::MJTC_str_replace(' ', '_', sanitize_file_name($_FILES['support_custom_img']['name']));
            $MJTC_file_tmp = majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['tmp_name']); // actual location // MJTC_sanitizeData() function uses wordpress santize functions

            $MJTC_userpath = $MJTC_path;
            $MJTC_isupload = true;
        }
        if ($MJTC_isupload) {
            $this->uploadfor = 'supportcustomlogo';
            // Register our path override.
            add_filter( 'upload_dir', array($this,'majesticsupport_upload_custom_logo'));
            // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
            $MJTC_result = array();
            $MJTC_file = array(
                'name' => sanitize_file_name($_FILES['support_custom_img']['name']),
                'type' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['type']),
                'tmp_name' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['tmp_name']),
                'error' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['error']),
                'size' => majesticsupport::MJTC_sanitizeData($_FILES['support_custom_img']['size']),
            ); // MJTC_sanitizeData() function uses wordpress santize functions
            $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
            if ( $MJTC_result && ! isset( $MJTC_result['error'] ) ) {
                $this->setSupportCustomImage($MJTC_file_name, $MJTC_userpath);
            }
            // Set everything back to normal.
            remove_filter( 'upload_dir', array($this,'majesticsupport_upload_custom_logo'));
        }
    }

    function majesticsupport_upload_custom_logo( $MJTC_dir ) {
        if($this->uploadfor == 'supportcustomlogo'){
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $MJTC_path = $MJTC_datadirectory . '/supportImg';
            $MJTC_array = array(
                'path'   => $MJTC_dir['basedir'] . '/' . $MJTC_path,
                'url'    => $MJTC_dir['baseurl'] . '/' . $MJTC_path,
                'subdir' => '/'. $MJTC_path,
            ) + $MJTC_dir;
            return $MJTC_array;
        }else{
            return $MJTC_dir;
        }
    }

    function setSupportCustomImage($MJTC_filename, $MJTC_userpath){
        $MJTC_query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'support_custom_img'";
        $MJTC_key = majesticsupport::$_db->get_var($MJTC_query);
        if ($MJTC_key) {
            $MJTC_unlinkPath = $MJTC_userpath.'/'.$MJTC_key;
            if (is_file($MJTC_unlinkPath)) {
                wp_delete_file($MJTC_unlinkPath);
            }
        }
        majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => $MJTC_filename), array('configname' => 'support_custom_img'));
    }

    function deleteSupportCustomImage() {

        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (!wp_verify_nonce($MJTC_nonce, 'delete-support-customimage')) {
            die('Security check Failed');
        }

        $MJTC_maindir = wp_upload_dir();
        $MJTC_basedir = trailingslashit($MJTC_maindir['basedir']);
        $MJTC_datadirectory = isset(majesticsupport::$_config['data_directory']) ? sanitize_text_field(majesticsupport::$_config['data_directory']) : '';
        $MJTC_path = $MJTC_basedir . trailingslashit($MJTC_datadirectory) . 'supportImg/';

        $MJTC_query = "SELECT configvalue FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` WHERE configname = 'support_custom_img'";
        $MJTC_key = majesticsupport::$_db->get_var($MJTC_query);

        if ($MJTC_key) {
            $MJTC_key = sanitize_file_name($MJTC_key); // Sanitize filename
            $MJTC_unlinkPath = realpath($MJTC_path . $MJTC_key); // Get absolute path

            // Ensure the file is within the allowed directory
            if ($MJTC_unlinkPath && MJTC_majesticsupportphplib::MJTC_strpos($MJTC_unlinkPath, realpath($MJTC_path)) === 0 && is_file($MJTC_unlinkPath)) {
                wp_delete_file($MJTC_unlinkPath);
            }
        }

        // Update database to remove reference
        majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => 0), array('configname' => 'support_custom_img'));

        return 'success';
    }

    function getEmailReadTime() {
        $MJTC_time = null;
        $MJTC_query = "SELECT config.configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` AS config WHERE config.configname = 'lastEmailReadingTime'";
        $MJTC_time = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_time;
    }

    function setEmailReadTime($MJTC_time) {
        majesticsupport::$_db->update(majesticsupport::$_db->prefix . 'mjtc_support_config', array('configvalue' => $MJTC_time), array('configname' => 'lastEmailReadingTime'));
    }

    function getConfiguration() {
        do_action('majesticsupport_load_wp_plugin_file');
        // check for plugin using plugin name
        if (is_plugin_active('majestic-support/majestic-support.php')) {
            //plugin is activated
            $MJTC_query = "SELECT config.* FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` AS config WHERE config.configfor != 'ticketviaemail'";
            $MJTC_config = majesticsupport::$_db->get_results($MJTC_query);
            foreach ($MJTC_config as $MJTC_conf) {
                majesticsupport::$_config[$MJTC_conf->configname] = $MJTC_conf->configvalue;
            }
            majesticsupport::$_config['config_count'] = COUNT($MJTC_config);
        }
    }

    function getCheckCronKey() {
        $MJTC_query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'ck'";
        $MJTC_key = majesticsupport::$_db->get_var($MJTC_query);
        if ($MJTC_key && $MJTC_key != '')
            return true;
        else
            return false;
    }

    function genearateCronKey() {
        $MJTC_key = MJTC_majesticsupportphplib::MJTC_md5(gmdate('Y-m-d'));
        $MJTC_query = "UPDATE `".majesticsupport::$_db->prefix."mjtc_support_config` SET configvalue = '".esc_sql($MJTC_key)."' WHERE configname = 'ck'" ;
        majesticsupport::$_db->query($MJTC_query);
        return true;
    }

    function getCronKey($MJTC_passkey) {
        if ($MJTC_passkey == MJTC_majesticsupportphplib::MJTC_md5(gmdate('Y-m-d'))) {
            $MJTC_query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'ck'";
            $MJTC_key = majesticsupport::$_db->get_var($MJTC_query);
            return $MJTC_key;
        }
        else
            return false;
    }

    function getConfigValue($MJTC_configname){
        $MJTC_query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = '".esc_sql($MJTC_configname)."'";
        $MJTC_configvalue = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_configvalue;
    }

    function getPageList() {
        $MJTC_query = "SELECT ID AS id, post_title AS text FROM `" . majesticsupport::$_db->prefix . "posts` WHERE post_type = 'page' AND post_status = 'publish' ";
        $MJTC_emails = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_emails;
    }

    function getWooCommerceCategoryList() {
        $MJTC_orderby = 'term_id';
        $MJTC_order = 'desc';
        $MJTC_hide_empty = false;
        
        $MJTC_cat_args = array(
            'taxonomy'   => 'product_cat', // Move taxonomy name here
            'orderby'    => $MJTC_orderby,
            'order'      => $MJTC_order,
            'hide_empty' => $MJTC_hide_empty,
        );
        
        // Pass only the $MJTC_cat_args array
        $MJTC_product_categories = get_terms( $MJTC_cat_args );
        
        $MJTC_catList = array();
        // Check if it's an array and not a WP_Error before looping
        if ( ! is_wp_error( $MJTC_product_categories ) && ! empty( $MJTC_product_categories ) ) {
            foreach ($MJTC_product_categories as $MJTC_category) {
                $MJTC_catList[] = (object) array('id' => $MJTC_category->term_id, 'text' => $MJTC_category->name);
            }
        }
        
        return $MJTC_catList;
    }

    function getConfigurationByConfigName($MJTC_configname) {
        $MJTC_query = "SELECT configvalue
                  FROM  `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname ='" . esc_sql($MJTC_configname) . "'";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_result;
    }

    function getCountConfig() {
        $MJTC_query = "SELECT COUNT(*)
                  FROM `".majesticsupport::$_db->prefix."mjtc_support_config`";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_result;
    }

    function storeAutoUpdateConfig() {

        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_configvalue = MJTC_request::MJTC_getVar('mjtc_addons_auto_update','','');

        if (!is_numeric($MJTC_configvalue)) { //can only have numric value
            return false;
        }

        $MJTC_error = false;
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_config` SET `configvalue` = ".esc_sql($MJTC_configvalue)." WHERE `configname`= 'mjtc_addons_auto_update'";
        if (false === majesticsupport::$_db->query($MJTC_query)) {
            $MJTC_error = true;
        }

        if ($MJTC_error) {
            MJTC_message::MJTC_setMessage(esc_html(__('Something went wrong. Please try again later.', 'majestic-support')), 'error');
            return WPJOBPORTAL_SAVE_ERROR;
        } else {
            MJTC_message::MJTC_setMessage(esc_html(__('The setting has been stored.', 'majestic-support')), 'updated');
        }
        return;
    }
}

?>
