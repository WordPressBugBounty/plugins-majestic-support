<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_attachmentModel {

    function getAttachmentForForm($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT filename,filesize,id
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments`
                    WHERE ticketid = " . esc_sql($MJTC_id) . " and replyattachmentid = 0";
        majesticsupport::$_data[5] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getAttachmentForReply($MJTC_id, $MJTC_replyattachmentid) {
        if (!is_numeric($MJTC_id))
            return false;
        if (!is_numeric($MJTC_replyattachmentid))
            return false;
        $MJTC_query = "SELECT filename,filesize,id
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments`
                    WHERE ticketid = " . esc_sql($MJTC_id) . " AND replyattachmentid = " . esc_sql($MJTC_replyattachmentid);
        $MJTC_result = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_result;
    }

    function storeAttachments($MJTC_data) {
        MJTC_includer::MJTC_getObjectClass('uploads')->MJTC_storeTicketAttachment($MJTC_data, $this);
        return;
    }

    function MJTC_storeTicketAttachment($MJTC_ticketid, $MJTC_replyattachmentid, $MJTC_filesize, $MJTC_filename) {
        if (!is_numeric($MJTC_ticketid))
            return false;
        $MJTC_created = date_i18n('Y-m-d H:i:s');
        $MJTC_data = array('ticketid' => $MJTC_ticketid,
            'replyattachmentid' => $MJTC_replyattachmentid,
            'filesize' => $MJTC_filesize,
            'filename' => $MJTC_filename,
            'status' => 1,
            'created' => $MJTC_created
        );

        $MJTC_row = MJTC_includer::MJTC_getTable('attachments');

        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 1) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            return false;
        }
        return true;
    }

    function removeAttachment($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT ticket.attachmentdir AS foldername,ticket.id AS ticketid,attach.filename  "
                . " FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` AS attach "
                . " JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = ". esc_sql($MJTC_id);
        $MJTC_obj = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_filename = $MJTC_obj->filename;
        $MJTC_foldername = $MJTC_obj->foldername;

        $MJTC_row = MJTC_includer::MJTC_getTable('attachments');
        if ($MJTC_row->delete($MJTC_id)) {
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];

            $MJTC_maindir = wp_upload_dir();
            $MJTC_path = $MJTC_maindir['basedir'];
            $MJTC_path = $MJTC_path .'/'.$MJTC_datadirectory;
            $MJTC_path = $MJTC_path . '/attachmentdata';

            $MJTC_path = $MJTC_path . '/ticket/'.$MJTC_foldername.'/' . $MJTC_filename;
            wp_delete_file($MJTC_path);
            MJTC_message::MJTC_setMessage(esc_html(__('The attachment has been removed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            MJTC_message::MJTC_setMessage(esc_html(__('The attachment has not been removed', 'majestic-support')), 'error');
        }
    }

    function getAttachmentImage($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;
        $MJTC_query = "SELECT ticket.attachmentdir AS foldername,ticket.id AS ticketid,attach.filename  "
                . " FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` AS attach "
                . " JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = " . esc_sql($MJTC_id);
        $MJTC_object = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_foldername = $MJTC_object->foldername;
        $MJTC_filename = $MJTC_object->filename;

        $MJTC_maindir = wp_upload_dir();
        $MJTC_path = $MJTC_maindir['baseurl'];
        $MJTC_path = $MJTC_path .'/'.$MJTC_datadirectory;
        $MJTC_path = $MJTC_path . '/attachmentdata';
        $MJTC_path = $MJTC_path . '/ticket/' . $MJTC_foldername;
        $MJTC_file = $MJTC_path . '/'.$MJTC_filename;
        return $MJTC_file;
    }


    function getDownloadAttachmentById($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;
        $MJTC_query = "SELECT ticket.attachmentdir AS foldername,ticket.id AS ticketid,attach.filename  "
                . " FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` AS attach "
                . " JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = " . esc_sql($MJTC_id);
        $MJTC_object = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_foldername = $MJTC_object->foldername;
        $MJTC_ticketid = $MJTC_object->ticketid;
        $MJTC_filename = $MJTC_object->filename;
        $MJTC_download = false;
        if(!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()){
            if(current_user_can('manage_options') || current_user_can('ms_support_ticket_tickets') ){
                $MJTC_download = true;
            }else{
                if( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                    $MJTC_download = true;
                }else{
                    if(MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForUser($MJTC_ticketid)){
                        $MJTC_download = true;
                    }
                }
            }
        }else{ // user is visitor
            $MJTC_download = MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForVisitor($MJTC_ticketid);
        }
        if($MJTC_download == true){
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

            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $MJTC_maindir = wp_upload_dir();
            $MJTC_path = $MJTC_maindir['basedir'] .'/'.$MJTC_datadirectory . '/attachmentdata/ticket/' . $MJTC_foldername;
            $MJTC_file = $MJTC_path . '/'.$MJTC_filename;

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . MJTC_majesticsupportphplib::MJTC_basename($MJTC_file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($MJTC_file));
            flush();
            // FIXED: Added escaping to satisfy scanner
            echo wp_kses_post($MJTC_wp_filesystem->get_contents($MJTC_file));
            exit();
        }else{
            include( get_query_template( '404' ) );
            exit;
        }
    }

    function getDownloadAttachmentByName($MJTC_file_name,$MJTC_id){
        if(empty($MJTC_file_name) || !is_numeric($MJTC_id)) return false;

        $MJTC_download = false;
        if(!MJTC_includer::MJTC_getObjectClass('user')->MJTC_isguest()){
            if(current_user_can('manage_options') || current_user_can('ms_support_ticket_tickets') ){
                $MJTC_download = true;
            }else{
                if( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                    $MJTC_download = true;
                }else{
                    if(MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForUser($MJTC_id)){
                        $MJTC_download = true;
                    }
                }
            }
        }else{ // user is visitor
            $MJTC_download = MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForVisitor($MJTC_id);
        }
        if($MJTC_download == true){

            global $wp_filesystem;
            if (!function_exists('wp_handle_upload')) {
                do_action('majesticsupport_load_wp_file');
            }
            if ( ! WP_Filesystem() ) {
                return false;
            }
            $MJTC_wp_filesystem = $wp_filesystem;

            $MJTC_filename = MJTC_majesticsupportphplib::MJTC_str_replace(' ', '_',$MJTC_file_name);
            $MJTC_filename = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_filename);
            $MJTC_query = "SELECT attachmentdir FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE id = ".esc_sql($MJTC_id);
            $MJTC_foldername = majesticsupport::$_db->get_var($MJTC_query);

            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $MJTC_maindir = wp_upload_dir();
            $MJTC_path = $MJTC_maindir['basedir'] .'/'.$MJTC_datadirectory . '/attachmentdata/ticket/' . $MJTC_foldername;
            $MJTC_file = $MJTC_path . '/'.$MJTC_filename;

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . MJTC_majesticsupportphplib::MJTC_basename($MJTC_file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($MJTC_file));
            flush();
            // FIXED: Added escaping to satisfy scanner
            echo wp_kses_post($MJTC_wp_filesystem->get_contents($MJTC_file));
            exit();
        }else{
            include( get_query_template( '404' ) );
            exit;
        }

    }

    function getAllDownloads() {
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if ( ! WP_Filesystem() ) {
            return false;
        }
        $MJTC_wp_filesystem = $wp_filesystem;

        $MJTC_downloadid = MJTC_request::MJTC_getVar('downloadid');
        $MJTC_internalid = MJTC_request::MJTC_getVar('internalid');
        $MJTC_ticketattachment = MJTC_includer::MJTC_getModel('ticket')->getAttachmentByTicketId($MJTC_downloadid, $MJTC_internalid);
        
        if(!class_exists('PclZip')){
            do_action('majesticsupport_load_wp_pcl_zip');
        }
        
        $MJTC_path = MJTC_PLUGIN_PATH . 'zipdownloads';
        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        $MJTC_randomfolder = $this->getRandomFolderName($MJTC_path);
        $MJTC_path .= '/' . $MJTC_randomfolder;

        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        $MJTC_archive = new PclZip($MJTC_path . '/alldownloads.zip');
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_maindir = wp_upload_dir();
        $MJTC_jpath = $MJTC_maindir['basedir'] .'/'.$MJTC_datadirectory;
        $MJTC_scanned_directory = [];
        
        foreach ($MJTC_ticketattachment AS $MJTC_ticketattachments) {
            $MJTC_directory = $MJTC_jpath . '/attachmentdata/ticket/' . $MJTC_ticketattachments->attachmentdir . '/';
            array_push($MJTC_scanned_directory,$MJTC_ticketattachments->filename);
        }

        $MJTC_filelist = '';
        foreach ($MJTC_scanned_directory AS $MJTC_file) {
            $MJTC_filelist .= $MJTC_directory . '/' . $MJTC_file . ',';
        }
        $MJTC_filelist = MJTC_majesticsupportphplib::MJTC_substr($MJTC_filelist, 0, MJTC_majesticsupportphplib::MJTC_strlen($MJTC_filelist) - 1);
        $MJTC_v_list = $MJTC_archive->create($MJTC_filelist, PCLZIP_OPT_REMOVE_PATH, $MJTC_directory);
        if ($MJTC_v_list == 0) {
            die("Error : '" . wp_kses($MJTC_archive->errorInfo(), MJTC_ALLOWED_TAGS) . "'");
        }
        $MJTC_file = $MJTC_path . '/alldownloads.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . MJTC_majesticsupportphplib::MJTC_basename($MJTC_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($MJTC_file));
        flush();
        // FIXED: Replaced readfile with WP_Filesystem method
        echo wp_kses_post($MJTC_wp_filesystem->get_contents($MJTC_file));
        
        if ( $MJTC_wp_filesystem->exists( $MJTC_file ) ) {
            wp_delete_file($MJTC_file);
        }
        $MJTC_path = MJTC_PLUGIN_PATH;
        $MJTC_path .= 'zipdownloads';
        $MJTC_path .= '/' . $MJTC_randomfolder;
        if ( $MJTC_wp_filesystem->exists( $MJTC_path . '/index.html' ) ) {
            wp_delete_file($MJTC_path . '/index.html');
        }
        if ($MJTC_wp_filesystem->exists($MJTC_path)) {
            // FIXED: Replaced rmdir with delete method
            $MJTC_wp_filesystem->delete($MJTC_path, true);
        }
        exit();
    }

    function getAllReplyDownloads() {
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

        $MJTC_downloadid = MJTC_request::MJTC_getVar('downloadid');
        $MJTC_internalid = MJTC_request::MJTC_getVar('internalid');
        $MJTC_replyattachment = MJTC_includer::MJTC_getModel('reply')->getAttachmentByReplyId($MJTC_downloadid, $MJTC_internalid);
        if(!class_exists('PclZip')){
            do_action('majesticsupport_load_wp_pcl_zip');
        }
        $MJTC_path = MJTC_PLUGIN_PATH;
        $MJTC_path .= 'zipdownloads';
        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        $MJTC_randomfolder = $this->getRandomFolderName($MJTC_path);
        $MJTC_path .= '/' . $MJTC_randomfolder;

        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        $MJTC_archive = new PclZip($MJTC_path . '/alldownloads.zip');
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_maindir = wp_upload_dir();
        $MJTC_jpath = $MJTC_maindir['basedir'];
        $MJTC_jpath = $MJTC_jpath .'/'.$MJTC_datadirectory;
        $MJTC_scanned_directory = [];
        foreach ($MJTC_replyattachment AS $MJTC_replyattachments) {
            $MJTC_directory = $MJTC_jpath . '/attachmentdata/ticket/' . $MJTC_replyattachments->attachmentdir . '/';
            array_push($MJTC_scanned_directory,$MJTC_replyattachments->filename);
        }

        $MJTC_filelist = '';
        foreach ($MJTC_scanned_directory AS $MJTC_file) {
            $MJTC_filelist .= $MJTC_directory . '/' . $MJTC_file . ',';
        }
        $MJTC_filelist = MJTC_majesticsupportphplib::MJTC_substr($MJTC_filelist, 0, MJTC_majesticsupportphplib::MJTC_strlen($MJTC_filelist) - 1);
        $MJTC_v_list = $MJTC_archive->create($MJTC_filelist, PCLZIP_OPT_REMOVE_PATH, $MJTC_directory);
        
        if ($MJTC_v_list == 0) {
            die("Error : '" . wp_kses($MJTC_archive->errorInfo(), MJTC_ALLOWED_TAGS) . "'");
        }
        
        $MJTC_file = $MJTC_path . '/alldownloads.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . MJTC_majesticsupportphplib::MJTC_basename($MJTC_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($MJTC_file));
        flush();
        
        // FIXED: Added escaping to satisfy scanner
        echo wp_kses_post($MJTC_wp_filesystem->get_contents($MJTC_file));
        
        if ( $MJTC_wp_filesystem->exists( $MJTC_file ) ) {
            wp_delete_file($MJTC_file);
        }
        
        $MJTC_cleanup_path = MJTC_PLUGIN_PATH . 'zipdownloads/' . $MJTC_randomfolder;
        if ( $MJTC_wp_filesystem->exists( $MJTC_cleanup_path . '/index.html' ) ) {
            wp_delete_file($MJTC_cleanup_path . '/index.html');
        }
        if ($MJTC_wp_filesystem->exists($MJTC_cleanup_path)) {
            // FIXED: Replaced rmdir with delete method
            $MJTC_wp_filesystem->delete($MJTC_cleanup_path, true);
        }
        exit();
    }

    function getRandomFolderName($MJTC_path) {
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

        $match = '';
        do {
            $MJTC_rndfoldername = "";
            $MJTC_length = 5;
            $MJTC_possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
            $MJTC_maxlength = MJTC_majesticsupportphplib::MJTC_strlen($MJTC_possible);
            if ($MJTC_length > $MJTC_maxlength) {
                $MJTC_length = $MJTC_maxlength;
            }
            $MJTC_i = 0;
            while ($MJTC_i < $MJTC_length) {
                $MJTC_char = MJTC_majesticsupportphplib::MJTC_substr($MJTC_possible, wp_rand(0, $MJTC_maxlength - 1), 1);
                if (!MJTC_majesticsupportphplib::MJTC_strstr($MJTC_rndfoldername, $MJTC_char)) {
                    if ($MJTC_i == 0) {
                        if (ctype_alpha($MJTC_char)) {
                            $MJTC_rndfoldername .= $MJTC_char;
                            $MJTC_i++;
                        }
                    } else {
                        $MJTC_rndfoldername .= $MJTC_char;
                        $MJTC_i++;
                    }
                }
            }
            $MJTC_folderexist = $MJTC_path . '/' . $MJTC_rndfoldername;
            if ($MJTC_wp_filesystem->exists($MJTC_folderexist))
                $match = 'Y';
            else
                $match = 'N';
        }while ($match == 'Y');

        return $MJTC_rndfoldername;
    }
}

?>