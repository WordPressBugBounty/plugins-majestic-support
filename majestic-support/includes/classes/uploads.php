<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_uploads {

    private $MJTC_ticketid;
    private $MJTC_articleid;
    private $MJTC_downloadid;
    private $MJTC_categoryid;
    private $MJTC_staffid;
    private $MJTC_uploadfor;

    private function MJTC_is_extension_allowed($MJTC_configured_extensions, $MJTC_extension) {
        $MJTC_extension = strtolower(ltrim((string) $MJTC_extension, '.'));
        if ($MJTC_extension === '') {
            return false;
        }

        $MJTC_configured_extensions = strtolower((string) $MJTC_configured_extensions);
        $MJTC_allowed_extensions = preg_split('/[\s,|]+/', $MJTC_configured_extensions, -1, PREG_SPLIT_NO_EMPTY);
        $MJTC_allowed_extensions = array_map(function($MJTC_ext) {
            return ltrim(trim($MJTC_ext), '.');
        }, $MJTC_allowed_extensions);

        return in_array($MJTC_extension, $MJTC_allowed_extensions, true);
    }

    private function MJTC_safe_folder_name($MJTC_foldername) {
        return sanitize_file_name(wp_basename((string) $MJTC_foldername));
    }


    function MJTC_upload_dir( $MJTC_dir ) {
        $MJTC_form_request = MJTC_request::MJTC_getVar('form_request');
        if($MJTC_form_request == 'majesticsupport' OR $this->MJTC_uploadfor == 'agent'){
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $MJTC_path = $MJTC_datadirectory . '/attachmentdata';

            $MJTC_foldername = '';

            if($this->MJTC_uploadfor == 'ticket'){
                if(!is_numeric($this->MJTC_ticketid)) return false;
                $MJTC_path = $MJTC_path . '/ticket';
                $MJTC_query = "SELECT attachmentdir FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE id = ".esc_sql($this->MJTC_ticketid);
                $MJTC_foldername = $this->MJTC_safe_folder_name(majesticsupport::$_db->get_var($MJTC_query));
            }elseif($this->MJTC_uploadfor == 'article'){
                $MJTC_path = $MJTC_path . '/articles/article_'.absint($this->MJTC_articleid);
            }elseif($this->MJTC_uploadfor == 'download'){
                $MJTC_path = $MJTC_path . '/downloads/download_'.absint($this->MJTC_downloadid);
            }elseif($this->MJTC_uploadfor == 'category'){
                $MJTC_path = $MJTC_datadirectory . '/knowledgebasedata/categories/category_'.absint($this->MJTC_categoryid);
            }elseif($this->MJTC_uploadfor == 'agent'){
                $MJTC_path = $MJTC_datadirectory . '/staffdata/staff_'.absint($this->MJTC_staffid);
            }

            $MJTC_userpath = $MJTC_path . '/' . $MJTC_foldername;
            $MJTC_array = array(
                'path'   => $MJTC_dir['basedir'] . '/' . $MJTC_userpath,
                'url'    => $MJTC_dir['baseurl'] . '/' . $MJTC_userpath,
                'subdir' => '/'. $MJTC_userpath,
            ) + $MJTC_dir;
            return $MJTC_array;
        }elseif($this->MJTC_uploadfor == 'notificationlogo'){
            $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
            $MJTC_path = $MJTC_datadirectory;
            return $MJTC_path;

        }else{
            return $MJTC_dir;
        }
    }

    function MJTC_storeTicketAttachment($MJTC_data, $MJTC_caller){
        $MJTC_ticketid = $MJTC_data['ticketid'];
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $this->MJTC_ticketid = $MJTC_ticketid;
        $this->MJTC_uploadfor = 'ticket';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        if(!isset($_FILES['filename'])){
            return;
        }
        $MJTC_files = majesticsupport::MJTC_sanitizeData($_FILES['filename']);// MJTC_sanitizeData() function uses wordpress santize functions

        if(!is_array($MJTC_files['name'])){
            return;
        }

        foreach ($MJTC_files['name'] as $MJTC_key => $MJTC_value) {
            if ($MJTC_files['name'][$MJTC_key]) {
                $MJTC_file = array(
                        'name'     => $MJTC_files['name'][$MJTC_key],
                        'type'     => $MJTC_files['type'][$MJTC_key],
                        'tmp_name' => $MJTC_files['tmp_name'][$MJTC_key],
                        'error'    => $MJTC_files['error'][$MJTC_key],
                        'size'     => $MJTC_files['size'][$MJTC_key]
                        );
                $MJTC_uploadfilesize = $MJTC_file['size'] / 1024; //kb
                if($MJTC_uploadfilesize > $MJTC_filesize){
                    MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
                    return;
                }
                $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES['filename']['name'][$MJTC_key]));
                if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
                    $MJTC_document_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
                    if($this->MJTC_is_extension_allowed($MJTC_document_file_types, $MJTC_filetyperesult['ext'])){

                        $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                        if ( $MJTC_result && ! isset( $MJTC_result['error'] ) ) {
                            // Get the folder where the file was uploaded
                            $MJTC_file_directory = MJTC_majesticsupportphplib::MJTC_dirname($MJTC_result['file']);
                            $MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
                            $MJTC_replyattachmentid = isset($MJTC_data['replyattachmentid']) ? $MJTC_data['replyattachmentid'] : '';
                            $MJTC_result = $MJTC_caller->MJTC_storeTicketAttachment($MJTC_ticketid, $MJTC_replyattachmentid, $MJTC_uploadfilesize, $MJTC_filename);
                        } else {
                            /**
                             * Error generated by _wp_handle_upload()
                             * @see _wp_handle_upload() in wp-admin/includes/file.php
                             */
                            MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                        }
                    }
                }
            }
        }
        // generate index file
        if (!empty($MJTC_file_directory)) {
            MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        return;
    }

    function MJTC_storeTicketViaEmailAttachment($MJTC_idsarray,$MJTC_key,$MJTC_value){
        $MJTC_ticketid = $MJTC_idsarray[0];
        if(!is_numeric($MJTC_ticketid))
            return;
        $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
        $MJTC_maindir = wp_upload_dir();
        $MJTC_path = $MJTC_maindir['basedir'];
        $MJTC_path = $MJTC_path .'/'.$MJTC_datadirectory;
        if (!file_exists($MJTC_path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        }
        $MJTC_path = $MJTC_path . '/attachmentdata';
        if (!file_exists($MJTC_path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        }
        $MJTC_path = $MJTC_path . '/ticket';
        if (!file_exists($MJTC_path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        }
        $MJTC_query = "SELECT attachmentdir FROM `".majesticsupport::$_db->prefix."mjtc_support_tickets` WHERE id = ".esc_sql($MJTC_idsarray[0]);
        $MJTC_foldername = $this->MJTC_safe_folder_name(majesticsupport::$_db->get_var($MJTC_query));

        $MJTC_path = $MJTC_path . '/' . $MJTC_foldername;
        if (!file_exists($MJTC_path)) { // create user directory
            MJTC_includer::MJTC_getModel('majesticsupport')->makeDir($MJTC_path);
        }

        $MJTC_filename = sanitize_file_name(wp_basename((string) $MJTC_key));
        if ($MJTC_filename === '') {
            return false;
        }

        $MJTC_filetyperesult = wp_check_filetype($MJTC_filename);
        $MJTC_allowed_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
        if (empty($MJTC_filetyperesult['ext']) || empty($MJTC_filetyperesult['type']) || !$this->MJTC_is_extension_allowed($MJTC_allowed_file_types, $MJTC_filetyperesult['ext'])) {
            return false;
        }

        $MJTC_target = trailingslashit($MJTC_path) . $MJTC_filename;
        $MJTC_real_base = realpath($MJTC_path);
        $MJTC_real_target_dir = realpath(dirname($MJTC_target));
        if (!$MJTC_real_base || !$MJTC_real_target_dir || strpos($MJTC_real_target_dir, $MJTC_real_base) !== 0) {
            return false;
        }

        return false !== file_put_contents($MJTC_target, $MJTC_value); // save the file
    }

    function MJTC_storeArticleAttachment($MJTC_data, $MJTC_caller){
        $MJTC_id = $MJTC_data['id'];
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $this->MJTC_articleid = $MJTC_id;
        $this->MJTC_uploadfor = 'article';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        if(!isset($_FILES['filename'])){
            return;
        }
        $MJTC_files = majesticsupport::MJTC_sanitizeData($_FILES['filename']);// MJTC_sanitizeData() function uses wordpress santize functions
        if(!is_array($MJTC_files['name'])){
            return;
        }

        foreach ($MJTC_files['name'] as $MJTC_key => $MJTC_value) {
            if ($MJTC_files['name'][$MJTC_key]) {
                $MJTC_file = array(
                        'name'     => $MJTC_files['name'][$MJTC_key],
                        'type'     => $MJTC_files['type'][$MJTC_key],
                        'tmp_name' => $MJTC_files['tmp_name'][$MJTC_key],
                        'error'    => $MJTC_files['error'][$MJTC_key],
                        'size'     => $MJTC_files['size'][$MJTC_key]
                        );
                $MJTC_uploadfilesize = $MJTC_file['size'] / 1024; //kb
                if($MJTC_uploadfilesize > $MJTC_filesize){
                    MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
                    return;
                }

                $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES['filename']['name'][$MJTC_key]));
                if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
                    $MJTC_document_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
                    if($this->MJTC_is_extension_allowed($MJTC_document_file_types, $MJTC_filetyperesult['ext'])){

                        $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                        if ( $MJTC_result && ! isset( $MJTC_result['error'] ) ) {
                            // Get the folder where the file was uploaded
                            $MJTC_file_directory = MJTC_majesticsupportphplib::MJTC_dirname($MJTC_result['file']);
                            $MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
                            $MJTC_result = $MJTC_caller->storeArticleAttachmet($MJTC_id , $MJTC_uploadfilesize, $MJTC_filename);
                        } else {
                            /**
                             * Error generated by _wp_handle_upload()
                             * @see _wp_handle_upload() in wp-admin/includes/file.php
                             */
                            MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                        }
                    }
                }
            }
        }
        // generate index file
        if (!empty($MJTC_file_directory)) {
            MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        return;
    }

    function MJTC_storeDownloadAttachment($MJTC_data, $MJTC_caller){
        $MJTC_id = $MJTC_data['id'];
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $this->MJTC_downloadid = $MJTC_id;
        $this->MJTC_uploadfor = 'download';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        if(!isset($_FILES['filename'])){
            return;
        }
        $MJTC_files = majesticsupport::MJTC_sanitizeData($_FILES['filename']);// MJTC_sanitizeData() function uses wordpress santize functions
        if(!is_array($MJTC_files['name'])){
            return;
        }

        foreach ($MJTC_files['name'] as $MJTC_key => $MJTC_value) {
            if ($MJTC_files['name'][$MJTC_key]) {
                $MJTC_file = array(
                        'name'     => $MJTC_files['name'][$MJTC_key],
                        'type'     => $MJTC_files['type'][$MJTC_key],
                        'tmp_name' => $MJTC_files['tmp_name'][$MJTC_key],
                        'error'    => $MJTC_files['error'][$MJTC_key],
                        'size'     => $MJTC_files['size'][$MJTC_key]
                        );
                $MJTC_uploadfilesize = $MJTC_file['size'] / 1024; //kb
                if($MJTC_uploadfilesize > $MJTC_filesize){
                    MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
                    return;
                }
                $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES['filename']['name'][$MJTC_key]));
                if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
                    $MJTC_document_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
                    if($this->MJTC_is_extension_allowed($MJTC_document_file_types, $MJTC_filetyperesult['ext'])){
                        $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                        if ( $MJTC_result && ! isset( $MJTC_result['error'] ) ) {
                            // Get the folder where the file was uploaded
                            $MJTC_file_directory = MJTC_majesticsupportphplib::MJTC_dirname($MJTC_result['file']);
                            $MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
                            $MJTC_result = $MJTC_caller->MJTC_storeDownloadAttachment($MJTC_id , $MJTC_uploadfilesize, $MJTC_filename);
                        } else {
                            /**
                             * Error generated by _wp_handle_upload()
                             * @see _wp_handle_upload() in wp-admin/includes/file.php
                             */
                            MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                        }
                    }
                }
            }
        }
        // generate index file
        if (!empty($MJTC_file_directory)) {
            MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        return;
    }

    function MJTC_uploadCategoryLogo($MJTC_id , $MJTC_caller){

        if(!is_numeric($MJTC_id))
            return false;
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $this->MJTC_categoryid = $MJTC_id;
        $this->MJTC_uploadfor = 'category';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        $MJTC_file = array(
                'name'     => sanitize_file_name($_FILES['filename']['name']),
                'type'     => majesticsupport::MJTC_sanitizeData($_FILES['filename']['type']),
                'tmp_name' => majesticsupport::MJTC_sanitizeData($_FILES['filename']['tmp_name']),
                'error'    => majesticsupport::MJTC_sanitizeData($_FILES['filename']['error']),
                'size'     => majesticsupport::MJTC_sanitizeData($_FILES['filename']['size']),
                );// MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_uploadfilesize = $MJTC_file['size'] / 1024; //kb
        if($MJTC_uploadfilesize > $MJTC_filesize){
            MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
            return;
        }

        $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES['filename']['name']));
        if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
            $MJTC_image_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');

            if($this->MJTC_is_extension_allowed($MJTC_image_file_types, $MJTC_filetyperesult['ext'])){

                $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                if ( $MJTC_result && ! isset( $MJTC_result['error'] ) ) {
                    // Get the folder where the file was uploaded
                    $MJTC_file_directory = dirname($MJTC_result['file']);
                    $MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
                    $MJTC_result = $MJTC_caller->storeCategoryLogo($MJTC_id , $MJTC_filename);
                    // generate index file
                    MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
                } else {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                }
            }
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        return;
    }

    function MJTC_uploadStaffLogo($MJTC_id , $MJTC_caller){
        if(!is_numeric($MJTC_id))
            return false;
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $this->MJTC_staffid = $MJTC_id;
        $this->MJTC_uploadfor = 'agent';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        $MJTC_file = array(
                'name'     => sanitize_file_name($_FILES['filename']['name']),
                'type'     => majesticsupport::MJTC_sanitizeData($_FILES['filename']['type']),
                'tmp_name' => majesticsupport::MJTC_sanitizeData($_FILES['filename']['tmp_name']),
                'error'    => majesticsupport::MJTC_sanitizeData($_FILES['filename']['error']),
                'size'     => majesticsupport::MJTC_sanitizeData($_FILES['filename']['size']),
                );// MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_uploadfilesize = $MJTC_file['size'] / 1024; //kb
        if($MJTC_uploadfilesize > $MJTC_filesize){
            MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
            return;
        }
        $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES['filename']['name']));
        if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
            $MJTC_image_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
            if($this->MJTC_is_extension_allowed($MJTC_image_file_types, $MJTC_filetyperesult['ext'])){

                $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                if ( $MJTC_result && ! isset( $MJTC_result['error'] ) ) {
                    // Get the folder where the file was uploaded
                    $MJTC_file_directory = MJTC_majesticsupportphplib::MJTC_dirname($MJTC_result['file']);
                    $MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
                    $MJTC_result = $MJTC_caller->storeStaffLogo($MJTC_id , $MJTC_filename);
                    // generate index file
                    MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
                } else {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                }
            }
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        return;
    }

    function MJTC_storeTicketCustomUploadFile($MJTC_id, $MJTC_field){
        if(!isset($_FILES[$MJTC_field])){
            return;
        }
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        $this->MJTC_ticketid = $MJTC_id;
        $this->MJTC_uploadfor = 'ticket';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        $MJTC_file = array(
                'name'     => sanitize_file_name($_FILES[$MJTC_field]['name']),
                'type'     => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['type']),
                'tmp_name' => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['tmp_name']),
                'error'    => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['error']),
                'size'     => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['size'])
                );// MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_uploadfilesize = majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['size']) / 1024; //kb
        // MJTC_sanitizeData() function uses wordpress santize functions
        if($MJTC_uploadfilesize > $MJTC_filesize){
            MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
            return;
        }
        $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES[$MJTC_field]['name']));
        if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
            $MJTC_image_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
            if($this->MJTC_is_extension_allowed($MJTC_image_file_types, $MJTC_filetyperesult['ext'])){

                $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                if (isset( $MJTC_result['error'] ) ) {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                }else{
                    $MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
                    // Get the folder where the file was uploaded
                    $MJTC_file_directory = MJTC_majesticsupportphplib::MJTC_dirname($MJTC_result['file']);
                    // generate index file
                    MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
                }
            }
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        //to store name of custom file in params
        MJTC_includer::MJTC_getModel('ticket')->storeUploadFieldValueInParams($MJTC_id,$MJTC_filename,$MJTC_field);
        return;
    }

	function MJTC_uploadInternalNoteAttachment($MJTC_id,$MJTC_field){
        if(!isset($_FILES[$MJTC_field])){
            return;
        }
        $MJTC_filename = '';
        $MJTC_filesize = '';
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        $this->MJTC_ticketid = $MJTC_id;
        $this->MJTC_uploadfor = 'ticket';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        $MJTC_file = array(
                'name'     => sanitize_file_name($_FILES[$MJTC_field]['name']),
                'type'     => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['type']),
                'tmp_name' => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['tmp_name']),
                'error'    => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['error']),
                'size'     => majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['size'])
                );// MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_uploadfilesize = majesticsupport::MJTC_sanitizeData($_FILES[$MJTC_field]['size']) / 1024; //kb
        // MJTC_sanitizeData() function uses wordpress santize functions
        if($MJTC_uploadfilesize > $MJTC_filesize){
            MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
            return;
        }
        $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES[$MJTC_field]['name']));
        if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
            $MJTC_image_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
            if($this->MJTC_is_extension_allowed($MJTC_image_file_types, $MJTC_filetyperesult['ext'])){

                $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                if (isset( $MJTC_result['error'] ) ) {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                }else{
					$MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
					$MJTC_filesize = $MJTC_file['size'];
                    // Get the folder where the file was uploaded
                    $MJTC_file_directory = MJTC_majesticsupportphplib::MJTC_dirname($MJTC_result['file']);
                    // generate index file
                    MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
				}
            }
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
		if($MJTC_filename != '' && $MJTC_filesize != ''){
			$MJTC_array = array('filename' => $MJTC_filename, 'filesize' => $MJTC_filesize);
			return $MJTC_array;
		}else{
			return false;
		}
	}

    function MJTC_uploadDesktopNotificationLogo(){
        $MJTC_filesize = majesticsupport::$_config['file_maximum_size'];
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        $this->MJTC_uploadfor = 'notificationlogo';
        // Register our path override.
        add_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        // Do our thing. WordPress will move the file to 'uploads/mycustomdir'.
        $MJTC_result = array();
        $MJTC_file = array(
                'name'     => sanitize_file_name($_FILES['logo_for_desktop_notfication']['name']),
                'type'     => majesticsupport::MJTC_sanitizeData($_FILES['logo_for_desktop_notfication']['type']),
                'tmp_name' => majesticsupport::MJTC_sanitizeData($_FILES['logo_for_desktop_notfication']['tmp_name']),
                'error'    => majesticsupport::MJTC_sanitizeData($_FILES['logo_for_desktop_notfication']['error']),
                'size'     => majesticsupport::MJTC_sanitizeData($_FILES['logo_for_desktop_notfication']['size']),
                );// MJTC_sanitizeData() function uses wordpress santize functions
        $MJTC_uploadfilesize = $MJTC_file['size'] / 1024; //kb
        if($MJTC_uploadfilesize > $MJTC_filesize){
            MJTC_message::MJTC_setMessage(esc_html(__('Error file size too large', 'majestic-support')), 'error');
            return;
        }
        $MJTC_filetyperesult = wp_check_filetype(sanitize_file_name($_FILES['logo_for_desktop_notfication']['name']));
        if(!empty($MJTC_filetyperesult['ext']) && !empty($MJTC_filetyperesult['type'])){
            $MJTC_image_file_types = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('file_extension');
            if($this->MJTC_is_extension_allowed($MJTC_image_file_types, $MJTC_filetyperesult['ext'])){
                $MJTC_result = wp_handle_upload($MJTC_file, array('test_form' => false));
                if ( $MJTC_result && ! isset( $MJTC_result['error'] ) ) {
                    // Get the folder where the file was uploaded
                    $MJTC_file_directory = dirname($MJTC_result['file']);
                    $MJTC_filename = MJTC_majesticsupportphplib::MJTC_basename( $MJTC_result['file'] );
                    $MJTC_result = MJTC_includer::MJTC_getModel('configuration')->storeDesktopNotificationLogo($MJTC_filename);
                    // generate index file
                    MJTC_includer::MJTC_getModel('majesticsupport')->generateIndexFile($MJTC_file_directory);
                } else {
                    MJTC_message::MJTC_setMessage($MJTC_result['error'], 'error');
                }
            }
        }
        // Set everything back to normal.
        remove_filter( 'upload_dir', array($this,'MJTC_upload_dir'));
        return;
    }

}

?>
