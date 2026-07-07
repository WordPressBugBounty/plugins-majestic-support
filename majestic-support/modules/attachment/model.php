<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_attachmentModel {

    private function MJTC_send_404() {
        status_header(404);
        include(get_query_template('404'));
        exit;
    }

    private function MJTC_get_safe_ticket_attachment_path($MJTC_foldername, $MJTC_filename) {
        $MJTC_foldername = sanitize_file_name(wp_basename((string) $MJTC_foldername));
        $MJTC_filename = sanitize_file_name(wp_basename((string) $MJTC_filename));

        if ($MJTC_foldername === '' || $MJTC_filename === '') {
            return false;
        }

        $MJTC_datadirectory = sanitize_file_name((string) majesticsupport::$_config['data_directory']);
        $MJTC_maindir = wp_upload_dir();
        $MJTC_base_path = trailingslashit($MJTC_maindir['basedir']) . $MJTC_datadirectory . '/attachmentdata/ticket/' . $MJTC_foldername;
        $MJTC_file = trailingslashit($MJTC_base_path) . $MJTC_filename;

        $MJTC_real_base = realpath($MJTC_base_path);
        $MJTC_real_file = realpath($MJTC_file);

        if (!$MJTC_real_base || !$MJTC_real_file || strpos($MJTC_real_file, trailingslashit($MJTC_real_base)) !== 0 || !is_file($MJTC_real_file)) {
            return false;
        }

        return $MJTC_real_file;
    }

    private function MJTC_stream_attachment_file($MJTC_file) {
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if (!WP_Filesystem()) {
            return false;
        }
        $MJTC_wp_filesystem = $wp_filesystem;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . MJTC_majesticsupportphplib::MJTC_basename($MJTC_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($MJTC_file));
        flush();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Binary file download output
        echo $MJTC_wp_filesystem->get_contents($MJTC_file);
        exit();
    }

    function getAttachmentForForm($MJTC_id) {
        $MJTC_id = absint($MJTC_id);
        if (!$MJTC_id)
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT filename,filesize,id
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments`
                    WHERE ticketid = %d and replyattachmentid = 0", $MJTC_id);
        majesticsupport::$_data[5] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getAttachmentForReply($MJTC_id, $MJTC_replyattachmentid) {
        $MJTC_id = absint($MJTC_id);
        $MJTC_replyattachmentid = absint($MJTC_replyattachmentid);
        // in some case we pass MJTC_replyattachmentid = 0
        if (!$MJTC_id)
            return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT filename,filesize,id,deleted
                    FROM `" . majesticsupport::$_db->prefix . "mjtc_support_attachments`
                    WHERE ticketid = %d AND replyattachmentid = %d", $MJTC_id, $MJTC_replyattachmentid);
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
        $MJTC_id = absint( $MJTC_id );

        if ( empty( $MJTC_id ) ) {
            return false;
        }
        $MJTC_query = majesticsupport::$_db->prepare("SELECT ticket.attachmentdir AS foldername,ticket.id AS ticketid,attach.filename  "
                . " FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` AS attach "
                . " JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = %d", $MJTC_id);
        $MJTC_obj = majesticsupport::$_db->get_row($MJTC_query);
        if (!$MJTC_obj) {
            return false;
        }
        $MJTC_filename = $MJTC_obj->filename;
        $MJTC_foldername = $MJTC_obj->foldername;

        $MJTC_row = MJTC_includer::MJTC_getTable('attachments');
        if ($MJTC_row->delete($MJTC_id)) {
            $MJTC_path = $this->MJTC_get_safe_ticket_attachment_path($MJTC_foldername, $MJTC_filename);
            if ($MJTC_path) {
                wp_delete_file($MJTC_path);
            }
            MJTC_message::MJTC_setMessage(esc_html(__('The attachment has been removed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            MJTC_message::MJTC_setMessage(esc_html(__('The attachment has not been removed', 'majestic-support')), 'error');
        }
    }

    function getAttachmentImage($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;
        $MJTC_id = absint($MJTC_id);
        if (!$MJTC_id) return false;
        $MJTC_query = majesticsupport::$_db->prepare("SELECT ticket.attachmentdir AS foldername,ticket.id AS ticketid,attach.filename  "
                . " FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` AS attach "
                . " JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = %d", $MJTC_id);
        $MJTC_object = majesticsupport::$_db->get_row($MJTC_query);
        if (!$MJTC_object) return false;
        $MJTC_datadirectory = sanitize_file_name((string) majesticsupport::$_config['data_directory']);
        $MJTC_foldername = sanitize_file_name(wp_basename($MJTC_object->foldername));
        $MJTC_filename = sanitize_file_name(wp_basename($MJTC_object->filename));

        $MJTC_maindir = wp_upload_dir();
        $MJTC_path = $MJTC_maindir['baseurl'];
        $MJTC_path = $MJTC_path .'/'.$MJTC_datadirectory;
        $MJTC_path = $MJTC_path . '/attachmentdata';
        $MJTC_path = $MJTC_path . '/ticket/' . $MJTC_foldername;
        $MJTC_file = $MJTC_path . '/'.$MJTC_filename;
        return $MJTC_file;
    }


    function getDownloadAttachmentById($MJTC_id){
        $MJTC_id = absint($MJTC_id);
        if(!$MJTC_id) return false;

        $MJTC_query = majesticsupport::$_db->prepare("SELECT ticket.attachmentdir AS foldername,ticket.id AS ticketid,attach.filename  "
                . " FROM `".majesticsupport::$_db->prefix."mjtc_support_attachments` AS attach "
                . " JOIN `".majesticsupport::$_db->prefix."mjtc_support_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = %d", $MJTC_id);
        $MJTC_object = majesticsupport::$_db->get_row($MJTC_query);
        if (!$MJTC_object) {
            $this->MJTC_send_404();
        }

        $MJTC_foldername = $MJTC_object->foldername;
        $MJTC_ticketid = absint($MJTC_object->ticketid);
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
            $MJTC_file = $this->MJTC_get_safe_ticket_attachment_path($MJTC_foldername, $MJTC_filename);
            if (!$MJTC_file) {
                $this->MJTC_send_404();
            }
            $this->MJTC_stream_attachment_file($MJTC_file);
        }else{
            $this->MJTC_send_404();
        }
    }

    function getDownloadAttachmentByName($MJTC_file_name,$MJTC_id){
        $MJTC_id = absint($MJTC_id);
        if(empty($MJTC_file_name) || !$MJTC_id) return false;

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
            $MJTC_filename = sanitize_file_name(wp_basename(MJTC_majesticsupportphplib::MJTC_str_replace(' ', '_', $MJTC_file_name)));
            $MJTC_query = majesticsupport::$_db->prepare("SELECT attachmentdir FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE id = %d", $MJTC_id);
            $MJTC_foldername = majesticsupport::$_db->get_var($MJTC_query);
            $MJTC_file = $this->MJTC_get_safe_ticket_attachment_path($MJTC_foldername, $MJTC_filename);
            if (!$MJTC_file) {
                $this->MJTC_send_404();
            }
            $this->MJTC_stream_attachment_file($MJTC_file);
        }else{
            $this->MJTC_send_404();
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

        $MJTC_downloadid = absint(MJTC_request::MJTC_getVar('downloadid'));
        $MJTC_internalid = sanitize_text_field(MJTC_request::MJTC_getVar('internalid'));
        $MJTC_ticketattachment = MJTC_includer::MJTC_getModel('ticket')->getAttachmentByTicketId($MJTC_downloadid, $MJTC_internalid);
        
        if(!class_exists('PclZip')){
            do_action('majesticsupport_load_wp_pcl_zip');
        }
        
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_path = trailingslashit($MJTC_upload_dir['basedir']) . 'majestic-support/zipdownloads';
        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        $MJTC_randomfolder = $this->getRandomFolderName($MJTC_path);
        $MJTC_path .= '/' . $MJTC_randomfolder;

        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        $MJTC_archive = new PclZip($MJTC_path . '/alldownloads.zip');
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_maindir = wp_upload_dir();
        $MJTC_filelist_array = array();
        
        foreach ($MJTC_ticketattachment AS $MJTC_ticketattachments) {
            $MJTC_safe_file = $this->MJTC_get_safe_ticket_attachment_path($MJTC_ticketattachments->attachmentdir, $MJTC_ticketattachments->filename);
            if ($MJTC_safe_file) {
                $MJTC_filelist_array[] = $MJTC_safe_file;
            }
        }

        if (empty($MJTC_filelist_array)) {
            $this->MJTC_send_404();
        }

        $MJTC_filelist = implode(',', $MJTC_filelist_array);
        $MJTC_v_list = $MJTC_archive->create($MJTC_filelist, PCLZIP_OPT_REMOVE_ALL_PATH);
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
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Binary file download output
        // FIXED: Replaced readfile with WP_Filesystem method
        echo $MJTC_wp_filesystem->get_contents($MJTC_file);
        
        if ( $MJTC_wp_filesystem->exists( $MJTC_file ) ) {
            wp_delete_file($MJTC_file);
        }
        $MJTC_upload_dir = wp_upload_dir();
        $MJTC_path = trailingslashit($MJTC_upload_dir['basedir']) . 'majestic-support/zipdownloads';
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

        $MJTC_downloadid = absint(MJTC_request::MJTC_getVar('downloadid'));
        // internalid is not numeric
        $MJTC_internalid = sanitize_text_field(MJTC_request::MJTC_getVar('internalid'));

        $MJTC_replyattachment = MJTC_includer::MJTC_getModel('reply')->getAttachmentByReplyId($MJTC_downloadid, $MJTC_internalid);
        if(!class_exists('PclZip')){
            do_action('majesticsupport_load_wp_pcl_zip');
        }

        $MJTC_upload_dir = wp_upload_dir();

        // Create the folders step-by-step using your working custom method
        $MJTC_base_support_dir = trailingslashit($MJTC_upload_dir['basedir']) . 'majestic-support';
        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_base_support_dir);

        $MJTC_path = $MJTC_base_support_dir . '/zipdownloads';
        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        
        $MJTC_randomfolder = $this->getRandomFolderName($MJTC_path);
        $MJTC_path .= '/' . $MJTC_randomfolder;

        // Create the final unique folder using your custom method
        MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        
        $MJTC_archive = new PclZip($MJTC_path . '/alldownloads.zip');
        
        $MJTC_filelist_array = array();
        foreach ($MJTC_replyattachment AS $MJTC_replyattachments) {
            $MJTC_safe_file = $this->MJTC_get_safe_ticket_attachment_path($MJTC_replyattachments->attachmentdir, $MJTC_replyattachments->filename);
            if ($MJTC_safe_file) {
                $MJTC_filelist_array[] = $MJTC_safe_file;
            }
        }
    
        if (empty($MJTC_filelist_array)) {
            $this->MJTC_send_404();
        }

        $MJTC_filelist = implode(',', $MJTC_filelist_array);
        $MJTC_v_list = $MJTC_archive->create($MJTC_filelist, PCLZIP_OPT_REMOVE_ALL_PATH);
        
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
        
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Binary file download output
        echo $MJTC_wp_filesystem->get_contents($MJTC_file);
        
        if ( $MJTC_wp_filesystem->exists( $MJTC_file ) ) {
            wp_delete_file($MJTC_file);
        }
        
        $MJTC_cleanup_path = $MJTC_path;
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
