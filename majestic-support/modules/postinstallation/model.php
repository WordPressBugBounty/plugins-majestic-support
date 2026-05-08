<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_PostinstallationModel {

    function updateInstallationStatusConfiguration(){
            $MJTC_flag = get_option('majesticsupport_post_installation');
            if($MJTC_flag == false){
                add_option( 'majesticsupport_post_installation', '1', '', 'yes' );
            }else{
                update_option( 'majesticsupport_post_installation', '1');
            }
    }

    function storeConfigurations($MJTC_data){
        if (empty($MJTC_data))
            return false;

        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        $MJTC_error = false;
        unset($MJTC_data['action']);
        unset($MJTC_data['form_request']);

        // Sanitize all input data
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions

        // Additional security for specific parameters
        if (isset($MJTC_data['support_custom_img'])) {
            $MJTC_data['support_custom_img'] = sanitize_file_name($MJTC_data['support_custom_img']); // Prevent directory traversal
        }

        foreach ($MJTC_data as $MJTC_key => $MJTC_value) {
            $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_config` SET `configvalue` = '" . esc_sql($MJTC_value) . "' WHERE `configname`= '" . esc_sql($MJTC_key) . "'";
            majesticsupport::$_db->query($MJTC_query);

            // Track status for error handling
            if (majesticsupport::$_db->last_error == null) {
                $MJTC_status = 0;
            } else {
                $MJTC_status = 1;
            }
        }

        if ($MJTC_status == 0) {
            MJTC_message::MJTC_setMessage(esc_html(__('Configuration', 'majestic-support')) . ' ' . esc_html(__('has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Configuration', 'majestic-support')) . ' ' . esc_html(__('has not been changed', 'majestic-support')), 'error');
        }

        return;
    }

    function getConfigurationValues() {
        $this->updateInstallationStatusConfiguration();
        $MJTC_query = "SELECT configname,configvalue
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_config` ";//WHERE configfor != 'ticketviaemail'";
        $MJTC_data = majesticsupport::$_db->get_results($MJTC_query);
        
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        foreach ($MJTC_data AS $MJTC_config) {
            majesticsupport::$_data[0][$MJTC_config->configname] = $MJTC_config->configvalue;
        }
        return;
    }


    function getPageList() {
        $MJTC_query = "SELECT ID AS id, post_title AS text FROM `" . majesticsupport::$_db->prefix . "posts` WHERE post_type = 'page' AND post_status = 'publish' ";
        $MJTC_pages = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_pages;
    }

}?>
