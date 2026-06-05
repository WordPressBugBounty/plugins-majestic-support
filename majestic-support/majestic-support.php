<?php

/*
  Plugin Name: Majestic Support - The Leading-Edge Help Desk & Customer Support Plugin
  Plugin URI: https://www.majesticsupport.com
  Description: Majestic Support is a trusted open source ticket system. Majestic Support is a simple, easy to use, web-based customer support system. User can create ticket from front-end. Majestic Support comes packed with lot features than most of the expensive(and complex) support ticket system on market. Majestic Support provide you best industry Majestic Support system.
  Author: Majestic Support
  Version: 1.1.8
  License: GPLv3
  Text Domain: majestic-support
  Domain Path: /languages
  
 */

if (!defined('ABSPATH'))
    die('Restricted Access');

class majesticsupport {

    public static $_path;
    public static $_pluginpath;
    public static $_data; /* data[0] for list , data[1] for total paginition ,data[2] userfieldsforview , data[3] userfield for form , data[4] for reply , data[5] for ticket history  , data[6] for internal notes  , data[7] for ban email  , data['ticket_attachment'] for attachment */
    public static $_pageid;
    public static $_db;
    public static $_config;
    public static $_sorton;
    public static $_sortorder;
    public static $_ordering;
    public static $_sortlinks;
    public static $_msg;
    public static $_wpprefixforuser;
    public static $MJTC_colors;
    public static $_active_addons;
    public static $_addon_query;
    public static $_currentversion;
    public static $_search;
    public static $_captcha;
    public static $_mjtcsession;


    function __construct() {
        // php 8.1 issues
        require_once 'includes/majesticsupportphplib.php';
        // to check what addons are active and create an array.
        $MJTC_plugin_array = get_option('active_plugins');
        $MJTC_addon_array = array();
        foreach ($MJTC_plugin_array as $MJTC_key => $MJTC_value) {
            $MJTC_plugin_name = pathinfo($MJTC_value, PATHINFO_FILENAME);
            if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_plugin_name, 'majestic-support-')){
                if($MJTC_plugin_name != ''){
                    $MJTC_addon_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_plugin_name);
                }
            }
        }
        self::$_active_addons = $MJTC_addon_array;
        // above code is its right place
        self::includes();
        self::mjtcLoadWpCoreFiles();
        self::registeractions();
        self::$_path = plugin_dir_path(__FILE__);
        self::$_pluginpath = plugins_url('/', __FILE__);
        self::$_data = array();
        self::$_search = array();
        self::$_captcha = array();
        self::$_currentversion = '118';
        self::$_addon_query = array('select'=>'','join'=>'','where'=>'');
        self::$_mjtcsession = MJTC_includer::MJTC_getObjectClass('wphdsession');
        global $wpdb;
        self::$_db = $wpdb;
        if(is_multisite()) {
            self::$_wpprefixforuser = $wpdb->base_prefix;
        }else{
            self::$_wpprefixforuser = self::$_db->prefix;
        }
        add_filter('cron_schedules',array($this,'majesticsupport_customschedules'));
        add_filter('the_content', array($this, 'checkRequest'));
        MJTC_includer::MJTC_getModel('configuration')->getConfiguration();
        register_activation_hook(__FILE__, array($this, 'MJTC_activate'));
        register_deactivation_hook(__FILE__, array($this, 'MJTC_deactivate'));
        if(version_compare(get_bloginfo('version'),'5.1', '>=')){ //for wp version >= 5.1
            add_action('wp_insert_site', array($this, 'majesticsupport_new_site')); //when new site is added in multisite
        }else{ //for wp version < 5.1
            add_action('wpmu_new_blog', array($this, 'majesticsupport_new_blog'), 10, 6);
        }
        add_filter('wpmu_drop_tables', array($this, 'majesticsupport_delete_site')); //when site is deleted in multisite

        // add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        add_action('majesticsupport_updateticketstatus', array($this,'updateticketstatus'));
        add_action('majesticsupport_checkforaddonsupdate', array($this,'checkforaddonsupdate'));
        if(in_array('actions',majesticsupport::$_active_addons)){
            add_action('template_redirect', array($this, 'printTicket'), 5); // Only for the print ticket in wordpress
        }
        add_action('admin_init', array($this, 'majesticsupport_activation_redirect'));
        add_action( 'wp_footer', array($this,'checkScreenTag') );
        add_action( 'MJTC_resetnotificationvalues', array($this, 'resetNotificationValues'));
        //for style sheets
        add_action('wp_head', array($this,'ms_register_plugin_styles'));
        add_action('admin_enqueue_scripts', array($this,'ms_admin_register_plugin_styles') );
        add_action('MJTC_reset_addon_query', array($this,'MJTC_reset_addon_query') );
        add_action('majesticsupport_ticketviaemail', array($this,'ticketviaemail'));// this also handles ticket over due and ticket feedback
        add_action('init', array($this,'ms_handle_public_cronjob'));
        add_action('admin_init', array($this,'ms_handle_search_form_data'));
        add_action('admin_init', array($this,'ms_handle_delete_cookies'));
        add_action('init', array($this,'ms_handle_search_form_data'));
        add_action( 'ms_delete_expire_session_data', array($this , 'mjtc_delete_expire_session_data') );
        add_filter('safe_style_css', array($this,'mjtc_safe_style_css'));
        if( !wp_next_scheduled( 'ms_delete_expire_session_data' ) ) {
            // Schedule the event
            wp_schedule_event( time(), 'daily', 'ms_delete_expire_session_data' );
        }
        add_action( 'mjtc_process_transation_key_status', array($this , 'ms_process_transation_key_status') );
        if( !wp_next_scheduled( 'mjtc_process_transation_key_status' ) ) {
            // Schedule the event
            wp_schedule_event( time(), 'daily', 'mjtc_process_transation_key_status' );
        }
        add_action( 'mjtc_auto_update_addons', array($this , 'ms_auto_update_addons') );
        if( !wp_next_scheduled( 'mjtc_auto_update_addons' ) ) {
            // Schedule the event
            wp_schedule_event( time(), 'daily', 'mjtc_auto_update_addons' );
        }
        //add_action( 'upgrader_process_complete', array($this , 'majesticsupport_upgrade_completed'), 10, 2 );
        // If seo plugin is activated
        if (is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ) ){
            add_filter( 'aioseo_disable_shortcode_parsing', '__return_true' );
        }
        add_action('admin_notices', array($this , 'mjtc_show_expiry_error_notice') );

        add_action( 'majesticsupport_daily_attachment_cleanup', array($this , 'ms_auto_delete_old_attachments_cron' ) );
        if ( ! wp_next_scheduled( 'majesticsupport_daily_attachment_cleanup' ) ) {
            // Schedule the event to run daily, starting right now
            wp_schedule_event( time(), 'daily', 'majesticsupport_daily_attachment_cleanup' );
        }
    }

    function majesticsupport_customschedules($MJTC_schedules){
        $MJTC_schedules['halfhour'] = array(
           'interval' => 1800,
           'display'=> 'Half hour'
        );
       return $MJTC_schedules;
    }

    function MJTC_activate($MJTC_network_wide = false) {
        include_once 'includes/activation.php';
        if(function_exists('is_multisite') && is_multisite() && $MJTC_network_wide){
            global $wpdb;
            $MJTC_blogs = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs", array() ) );
            foreach($MJTC_blogs as $MJTC_blog_id){
                switch_to_blog( $MJTC_blog_id );
                MJTC_activation::MJTC_activate();
                restore_current_blog();
            }
        }else{
            MJTC_activation::MJTC_activate();
        }
        wp_schedule_event(time(), 'daily', 'majesticsupport_updateticketstatus');
        add_option('majesticsupport_do_activation_redirect', true);
        wp_schedule_event(time(), 'halfhour', 'majesticsupport_ticketviaemail');// this also handles ticket overdue (bcz of hors configuration)
        wp_schedule_event(time(), 'daily', 'majesticsupport_checkforaddonsupdate');

    }

    function majesticsupport_new_site($MJTC_new_site){
        $MJTC_pluginname = plugin_basename(__FILE__);
        if(is_plugin_active_for_network($MJTC_pluginname)){
            include_once 'includes/activation.php';
            switch_to_blog($MJTC_new_site->blog_id);
            MJTC_activation::MJTC_activate();
            restore_current_blog();
        }
    }

    function majesticsupport_new_blog($MJTC_blog_id, $MJTC_user_id, $MJTC_domain, $MJTC_path, $MJTC_site_id, $meta){
        $MJTC_pluginname = plugin_basename(__FILE__);
        if(is_plugin_active_for_network($MJTC_pluginname)){
            include_once 'includes/activation.php';
            switch_to_blog($MJTC_blog_id);
            MJTC_activation::MJTC_activate();
            restore_current_blog();
        }
    }

    function majesticsupport_delete_site($tables){
        include_once 'includes/deactivation.php';
        $MJTC_tablestodrop = MJTC_deactivation::MJTC_tables_to_drop();
        foreach($MJTC_tablestodrop as $MJTC_tablename){
            $tables[] = $MJTC_tablename;
        }
        return $tables;
    }

    function majesticsupport_activation_redirect(){
        if (get_option('majesticsupport_do_activation_redirect')) {
            delete_option('majesticsupport_do_activation_redirect');
            exit(esc_url(wp_safe_redirect(admin_url('admin.php?page=majesticsupport_postinstallation&mjslay=welcome'))));
        }
    }

    function ms_handle_public_cronjob(){
        $MJTC_action = MJTC_request::MJTC_getVar('mscron','get',null);
        if ($MJTC_action) {
            switch ($MJTC_action) {
                case 'ticketviaemail':
                    do_action('majesticsupport_ticketviaemail');
                    break;
                case 'updateticketstatus':
                    do_action('majesticsupport_updateticketstatus');
                    break;
                case 'checkforaddonsupdate':
                    do_action('majesticsupport_checkforaddonsupdate');
                    break;
            }
            exit();
        }
    }

    function mjtc_safe_style_css(){
        $MJTC_styles[] = 'display';
        $MJTC_styles[] = 'color';
        $MJTC_styles[] = 'width';
        $MJTC_styles[] = 'max-width';
        $MJTC_styles[] = 'min-width';
        $MJTC_styles[] = 'height';
        $MJTC_styles[] = 'min-height';
        $MJTC_styles[] = 'max-height';
        $MJTC_styles[] = 'background-color';
        $MJTC_styles[] = 'border';
        $MJTC_styles[] = 'border-bottom';
        $MJTC_styles[] = 'border-top';
        $MJTC_styles[] = 'border-left';
        $MJTC_styles[] = 'border-right';
        $MJTC_styles[] = 'border-color';
        $MJTC_styles[] = 'padding';
        $MJTC_styles[] = 'padding-top';
        $MJTC_styles[] = 'padding-bottom';
        $MJTC_styles[] = 'padding-left';
        $MJTC_styles[] = 'padding-right';
        $MJTC_styles[] = 'margin';
        $MJTC_styles[] = 'margin-top';
        $MJTC_styles[] = 'margin-bottom';
        $MJTC_styles[] = 'margin-left';
        $MJTC_styles[] = 'margin-right';
        $MJTC_styles[] = 'background';
        $MJTC_styles[] = 'font-weight';
        $MJTC_styles[] = 'font-size';
        $MJTC_styles[] = 'text-align';
        $MJTC_styles[] = 'text-decoration';
        $MJTC_styles[] = 'text-transform';
        $MJTC_styles[] = 'line-height';
        $MJTC_styles[] = 'visibility';
        $MJTC_styles[] = 'cellspacing';
        $MJTC_styles[] = 'data-id';
        $MJTC_styles[] = 'cursor';
        $MJTC_styles[] = 'vertical-align';
        $MJTC_styles[] = 'float';
        $MJTC_styles[] = 'position';
        $MJTC_styles[] = 'left';
        $MJTC_styles[] = 'right';
        $MJTC_styles[] = 'bottom';
        $MJTC_styles[] = 'top';
        $MJTC_styles[] = 'z-index';
        $MJTC_styles[] = 'overflow';
        return $MJTC_styles;
    }

    function ms_handle_search_form_data(){

        $MJTC_isadmin = is_admin();
        $mjslay = '';
        if(isset($_REQUEST['mjslay'])){
            $mjslay = majesticsupport::MJTC_sanitizeData($_REQUEST['mjslay']); // MJTC_sanitizeData() function uses wordpress santize functions
        }elseif(isset($_REQUEST['page'])){
            $mjslay = majesticsupport::MJTC_sanitizeData($_REQUEST['page']); // MJTC_sanitizeData() function uses wordpress santize functions
        }elseif(isset($_REQUEST['mjtcslay'])){
            $mjslay = majesticsupport::MJTC_sanitizeData($_REQUEST['mjtcslay']); // MJTC_sanitizeData() function uses wordpress santize functions
        }
        $MJTC_layoutname = MJTC_majesticsupportphplib::MJTC_explode("majesticsupport_", $mjslay);// admin page has wpjobportal_ prefix
        if(isset($MJTC_layoutname[1])){
            $mjslay = $MJTC_layoutname[1];
        }        
        $MJTC_callfrom = 3;
        if(isset($_REQUEST['MS_form_search']) && $_REQUEST['MS_form_search'] == 'MS_SEARCH'){
            $MJTC_callfrom = 1;
        }elseif(MJTC_request::MJTC_getVar('pagenum', 'get', null) != null){
            $MJTC_callfrom = 2;
        }

        $MJTC_setcookies = false;
        $MJTC_ticket_search_cookie_data = '';
        $ms_search_array = array();
        switch($mjslay){
            case 'tickets':
            case 'myticket':
            case 'ticket':
            case 'staffmyticket':
                if( in_array('agent',majesticsupport::$_active_addons) ){
                    $MJTC_agent = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
                }else{
                    $MJTC_agent = false;
                }
                if(is_admin() || $MJTC_agent){
                    $MJTC_search_userfields = MJTC_includer::MJTC_getObjectClass('customfields')->adminFieldsForSearch(1);
                } else {
                    $MJTC_search_userfields = MJTC_includer::MJTC_getObjectClass('customfields')->userFieldsForSearch(1);
                }
                if($MJTC_callfrom == 1){
                    if(is_admin()){
                        $ms_search_array = MJTC_includer::MJTC_getModel('ticket')->getAdminTicketSearchFormData($MJTC_search_userfields);
                    }else{
                        $ms_search_array = MJTC_includer::MJTC_getModel('ticket')->getFrontSideTicketSearchFormData($MJTC_search_userfields);
                    }
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    $ms_search_array = MJTC_includer::MJTC_getModel('ticket')->getCookiesSavedSearchDataTicket($MJTC_search_userfields);
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                MJTC_includer::MJTC_getModel('ticket')->setSearchVariableForTicket($ms_search_array,$MJTC_search_userfields);
            break;
            case 'departments':
            case 'department':
                $MJTC_deptname = (is_admin()) ? 'departmentname' : 'ms-dept';
                if($MJTC_callfrom == 1){
                    $ms_search_array = MJTC_includer::MJTC_getModel('department')->getAdminDepartmentSearchFormData();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_department'])){
                        $ms_search_array['departmentname'] = $MJTC_ticket_search_cookie_data['departmentname'];
                        $ms_search_array['pagesize'] = $MJTC_ticket_search_cookie_data['pagesize'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // Departments
                majesticsupport::$_search['department']['departmentname'] = isset($ms_search_array['departmentname']) ? $ms_search_array['departmentname'] : null;
                majesticsupport::$_search['department']['pagesize'] = isset($ms_search_array['pagesize']) ? $ms_search_array['pagesize'] : null;
            break;
            case 'erasedatarequests':
                if($MJTC_callfrom == 1 && is_admin()){
                    $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
                    if (! wp_verify_nonce( $MJTC_nonce, 'erase-data-requests') ) {
                        die( 'Security check Failed' );
                    }
                    $ms_search_array = MJTC_includer::MJTC_getModel('gdpr')->getAdminSearchFormDataGDPR();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_gdpr'])){
                        $ms_search_array['email'] = $MJTC_ticket_search_cookie_data['email'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // gdpr
                majesticsupport::$_search['gdpr']['email'] = isset($ms_search_array['email']) ? $ms_search_array['email'] : null;
            break;
            case 'priorities':
            case 'priority':
                if($MJTC_callfrom == 1 && is_admin()){
                    $ms_search_array = MJTC_includer::MJTC_getModel('priority')->getAdminSearchFormDataPriority();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_priority'])){
                        $ms_search_array['title'] = $MJTC_ticket_search_cookie_data['title'];
                        $ms_search_array['pagesize'] = $MJTC_ticket_search_cookie_data['pagesize'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // priority
                majesticsupport::$_search['priority']['title'] = isset($ms_search_array['title']) ? $ms_search_array['title'] : null;
                majesticsupport::$_search['priority']['pagesize'] = isset($ms_search_array['pagesize']) ? $ms_search_array['pagesize'] : null;
            break;
            case 'statuses':
            case 'status':
                if($MJTC_callfrom == 1 && is_admin()){
                    $ms_search_array = MJTC_includer::MJTC_getModel('status')->getAdminSearchFormDataStatus();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_status'])){
                        $ms_search_array['title'] = $MJTC_ticket_search_cookie_data['title'];
                        $ms_search_array['pagesize'] = $MJTC_ticket_search_cookie_data['pagesize'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // status
                majesticsupport::$_search['status']['status'] = isset($ms_search_array['title']) ? $ms_search_array['title'] : null;
                majesticsupport::$_search['status']['pagesize'] = isset($ms_search_array['pagesize']) ? $ms_search_array['pagesize'] : null;
            break;
            case 'products':
            case 'product':
                if($MJTC_callfrom == 1 && is_admin()){
                    $ms_search_array = MJTC_includer::MJTC_getModel('product')->getAdminSearchFormDataProduct();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_product'])){
                        $ms_search_array['title'] = $MJTC_ticket_search_cookie_data['title'];
                        $ms_search_array['pagesize'] = $MJTC_ticket_search_cookie_data['pagesize'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // product
                majesticsupport::$_search['product']['product'] = isset($ms_search_array['title']) ? $ms_search_array['title'] : null;
                majesticsupport::$_search['product']['pagesize'] = isset($ms_search_array['pagesize']) ? $ms_search_array['pagesize'] : null;
            break;
            case 'smartreplies':
            case 'smartreply':
                $title = (is_admin()) ? 'title' : 'ms-title';
                if($MJTC_callfrom == 1){
                    $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
                    if (! wp_verify_nonce( $MJTC_nonce, 'smart-replies') ) {
                        die( 'Security check Failed' );
                    }
                    if (MJTC_request::MJTC_getVar($title) != '') {
                        $ms_search_array[$title] = MJTC_majesticsupportphplib::MJTC_addslashes(MJTC_majesticsupportphplib::MJTC_trim(MJTC_request::MJTC_getVar($title)));
                    } else {
                        $ms_search_array[$title] = '';
                    }
                    $ms_search_array['search_from_smartreply'] = 1;
                    $ms_search_array = MJTC_includer::MJTC_getModel('smartreply')->getAdminSearchFormDataSmartReply();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']);// MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_smartreply'])){
                        $ms_search_array[$title] = $MJTC_ticket_search_cookie_data[$title];
                        $ms_search_array['pagesize'] = $MJTC_ticket_search_cookie_data['pagesize'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // smartreply
                majesticsupport::$_search['smartreply'][$title] = isset($ms_search_array[$title]) ? $ms_search_array[$title] : null;
                majesticsupport::$_search['smartreply']['pagesize'] = isset($ms_search_array['pagesize']) ? $ms_search_array['pagesize'] : null;
            break;
            case 'slug':
                if($MJTC_callfrom == 1 && is_admin()){
                    $ms_search_array = MJTC_includer::MJTC_getModel('slug')->getAdminSearchFormDataSlug();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_slug'])){
                        $ms_search_array['slug'] = $MJTC_ticket_search_cookie_data['slug'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // system emails
                majesticsupport::$_search['slug']['slug'] = isset($ms_search_array['slug']) ? $ms_search_array['slug'] : null;
            break;
            case 'emails':
            case 'email':
                if($MJTC_callfrom == 1 && is_admin()){
                    $ms_search_array = MJTC_includer::MJTC_getModel('email')->getAdminSearchFormDataEmails();
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if($MJTC_ticket_search_cookie_data != '' && isset($MJTC_ticket_search_cookie_data['search_from_email'])){
                        $ms_search_array['email'] = $MJTC_ticket_search_cookie_data['email'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                // system emails
                majesticsupport::$_search['email']['email'] = isset($ms_search_array['email']) ? $ms_search_array['email'] : null;
            break;
            case 'departmentreport':
            case 'userreport':
            case 'staffreport':
            case 'departmentdetailreport':
            case 'userdetailreport':
            case 'stafftimereport':
                if($MJTC_callfrom == 1 && is_admin()){
                    $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
                    if (! wp_verify_nonce( $MJTC_nonce, 'reports') ) {
                        die( 'Security check Failed' );
                    }
                    $ms_search_array['date_start'] = MJTC_request::MJTC_getVar('date_start');
                    $ms_search_array['date_end'] = MJTC_request::MJTC_getVar('date_end');
                    $ms_search_array['uid'] = MJTC_request::MJTC_getVar('uid');
                    $ms_search_array['search_from_reports'] = 1;
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2 && is_admin()){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if(!empty($MJTC_ticket_search_cookie_data) && isset($MJTC_ticket_search_cookie_data['search_from_reports'])){
                        $ms_search_array['date_start'] = $MJTC_ticket_search_cookie_data['date_start'];
                        $ms_search_array['date_end'] = $MJTC_ticket_search_cookie_data['date_end'];
                        $ms_search_array['uid'] = $MJTC_ticket_search_cookie_data['uid'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                majesticsupport::$_search['report']['date_start'] = isset($ms_search_array['date_start']) ? $ms_search_array['date_start'] : null;
                majesticsupport::$_search['report']['date_end'] = isset($ms_search_array['date_end']) ? $ms_search_array['date_end'] : null;
                majesticsupport::$_search['report']['uid'] = isset($ms_search_array['uid']) ? $ms_search_array['uid'] : null;
            break;
            case 'staffreports':
                if($MJTC_callfrom == 1){
                    $ms_search_array['ms-date-start'] = MJTC_request::MJTC_getVar('ms-date-start');
                    $ms_search_array['ms-date-end'] = MJTC_request::MJTC_getVar('ms-date-end');
                    $ms_search_array['search_from_reports_staff'] = 1;
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if(!empty($MJTC_ticket_search_cookie_data) && isset($MJTC_ticket_search_cookie_data['search_from_reports_staff'])){
                        $ms_search_array['ms-date-start'] = $MJTC_ticket_search_cookie_data['ms-date-start'];
                        $ms_search_array['ms-date-end'] = $MJTC_ticket_search_cookie_data['ms-date-end'];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                majesticsupport::$_search['report']['ms-date-start'] = isset($ms_search_array['ms-date-start']) ? $ms_search_array['ms-date-start'] : null;
                majesticsupport::$_search['report']['ms-date-end'] = isset($ms_search_array['ms-date-end']) ? $ms_search_array['ms-date-end'] : null;
            break;
            case 'admin_staffdetailreport':
            case 'staffdetailreport':
                $MJTC_start_date = is_admin() ? 'date_start' : 'ms-date-start';
                $MJTC_end_date = is_admin() ? 'date_end' : 'ms-date-end';
                if($MJTC_callfrom == 1){
                    $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
                    if (! wp_verify_nonce( $MJTC_nonce, 'staff-detail-report') ) {
                        die( 'Security check Failed' );
                    }
                    $ms_search_array[$MJTC_start_date] = MJTC_request::MJTC_getVar($MJTC_start_date);
                    $ms_search_array[$MJTC_end_date] = MJTC_request::MJTC_getVar($MJTC_end_date);
                    $ms_search_array['search_from_reports_detail'] = 1;
                    $MJTC_setcookies = true;
                }elseif($MJTC_callfrom == 2){
                    if(isset($_COOKIE['ms_ticket_search_data'])){
                        $MJTC_ticket_search_cookie_data = majesticsupport::MJTC_sanitizeData($_COOKIE['ms_ticket_search_data']); // MJTC_sanitizeData() function uses wordpress santize functions
                        $MJTC_ticket_search_cookie_data = json_decode( MJTC_majesticsupportphplib::MJTC_safe_decoding($MJTC_ticket_search_cookie_data) , true );
                    }
                    if(!empty($MJTC_ticket_search_cookie_data) && isset($MJTC_ticket_search_cookie_data['search_from_reports_detail'])){
                        $ms_search_array[$MJTC_start_date] = $MJTC_ticket_search_cookie_data[$MJTC_start_date];
                        $ms_search_array[$MJTC_end_date] = $MJTC_ticket_search_cookie_data[$MJTC_end_date];
                    }
                }else{
                    majesticsupport::removeusersearchcookies();
                }
                majesticsupport::$_search['report'][$MJTC_start_date] = isset($ms_search_array[$MJTC_start_date]) ? $ms_search_array[$MJTC_start_date] : null;
                majesticsupport::$_search['report'][$MJTC_end_date] = isset($ms_search_array[$MJTC_end_date]) ? $ms_search_array[$MJTC_end_date] : null;
            break;
            case 'ticketdetail':
                $MJTC_ticketid = MJTC_request::MJTC_getVar('majesticsupportid');
                if (in_array('agent', majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) { //staff
                    if(current_user_can('ms_support_ticket')){
                        $MJTC_timecookies['ticket_time_start'][$MJTC_ticketid] = gmdate("Y-m-d h:i:s");
                    }else{
                        majesticsupport::$_data['permission_granted'] = MJTC_includer::MJTC_getModel('ticket')->validateTicketDetailForStaff($MJTC_ticketid);
                        if (majesticsupport::$_data['permission_granted']) { // validation passed
                            if(in_array('timetracking', majesticsupport::$_active_addons)){
                                $MJTC_timecookies['ticket_time_start'][$MJTC_ticketid] = gmdate("Y-m-d h:i:s");
                            }
                        }
                    }
                } else { // user
                    if(current_user_can('ms_support_ticket') || current_user_can('ms_support_ticket_tickets')){
                        if(in_array('timetracking', majesticsupport::$_active_addons)){
                            $MJTC_timecookies['ticket_time_start'][$MJTC_ticketid] = gmdate("Y-m-d h:i:s");
                        }
                    }
                }
                if(isset($MJTC_timecookies['ticket_time_start'][$MJTC_ticketid])){
                    $MJTC_val = 'ticket_time_start_'.esc_attr($MJTC_ticketid);
                    MJTC_majesticsupportphplib::MJTC_setcookie($MJTC_val , $MJTC_timecookies['ticket_time_start'][$MJTC_ticketid] , 0, COOKIEPATH);
                    if ( SITECOOKIEPATH != COOKIEPATH ){
                        MJTC_majesticsupportphplib::MJTC_setcookie('majesticsupport-timetack' , $MJTC_timecookies , 0, SITECOOKIEPATH);
                    }
                }
            break;
        }

        if($MJTC_setcookies){
            majesticsupport::setusersearchcookies($MJTC_setcookies,$ms_search_array);
        }
    }

    function mjtc_show_expiry_error_notice() {
        // Check if the option is set and equals '1'
        if (get_option('mjtc_show_key_expiry_msg') == '1') {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html(__('Your Majestic Support license key has expired or is invalid. Please update it to continue receiving support and updates.', 'majestic-support')); ?></p>
            </div>
            <?php
        }
    }

    function ms_handle_delete_cookies(){

        if(isset($_COOKIE['ms_addon_return_data'])){
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , '' , time() - 3600, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , '' , time() - 3600, SITECOOKIEPATH);
            }
        }

        if(isset($_COOKIE['ms_addon_install_data'])){
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_install_data' , '' , time() - 3600);
        }
    }

    public static function removeusersearchcookies(){
        if(isset($_COOKIE['ms_ticket_search_data'])){
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_ticket_search_data' , '' , time() - 3600 , COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_ticket_search_data' , '' , time() - 3600 , SITECOOKIEPATH);
            }
        }
    }

    public static function setusersearchcookies($MJTC_cookiesval, $ms_search_array){
        if(!$MJTC_cookiesval)
            return false;
        $MJTC_data = wp_json_encode( $ms_search_array );
        $MJTC_data = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_data);
        MJTC_majesticsupportphplib::MJTC_setcookie('ms_ticket_search_data' , $MJTC_data , 0 , COOKIEPATH);
        if ( SITECOOKIEPATH != COOKIEPATH ){
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_ticket_search_data' , $MJTC_data , 0 , SITECOOKIEPATH);
        }
    }

    function mjtc_delete_expire_session_data(){
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}mjtc_support_mjtcsessiondata WHERE sessionexpire < %d", time()));
    }

    function ms_process_transation_key_status(){
        MJTC_includer::MJTC_getModel('majesticsupport')->mjtc_check_license_status();
    }

    function ms_auto_update_addons() {
        MJTC_includer::MJTC_getModel('majesticsupport')->mjtc_check_license_status();
        MJTC_includer::MJTC_getModel('premiumplugin')->MSAddonsAutoUpdate();
    }

    function ms_auto_delete_old_attachments_cron(){
        MJTC_includer::MJTC_getModel('ticket')->autoDeleteOldAttachmentsCron();
    }

    /*
     * Update Ticket status every day schedule in the cron job
     */

    function updateticketstatus() {
        MJTC_includer::MJTC_getModel('ticket')->updateTicketStatusCron();
        if(in_array('overdue', majesticsupport::$_active_addons)){ // markticket overdue if duedate is passed.
            MJTC_includer::MJTC_getModel('overdue')->markTicketOverdueCron();
        }
    }

    function checkforaddonsupdate() {
        $MJTC_addone_count = MJTC_includer::MJTC_getModel('majesticsupport')->showUpdateAvaliableAlert();
        if ($MJTC_addone_count != 0) { 
            $MJTC_url = admin_url("?page=majesticsupport_premiumplugin&mjslay=addonstatus");?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <?php esc_attr(__('Hey there! We have recently launched a fresh update for the add-ons. Dont forget to update the add-ons to enjoy the greatest features!', 'majestic-support' )); ?>
                    <a href="<?php echo esc_url($MJTC_url) ?>">
                        <?php echo esc_attr(__("View","majestic-support")); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }

    /*
     * Email Piping every hourly schedule in the cron job
     */

     function printTicket() {
        $MJTC_layout = MJTC_request::MJTC_getVar('mjslay');
        if ($MJTC_layout == 'printticket') {
            $MJTC_ticketid = MJTC_request::MJTC_getVar('majesticsupportid');
            if(in_array('agent', majesticsupport::$_active_addons)){
                majesticsupport::$_data['user_staff'] = MJTC_includer::MJTC_getModel('agent')->isUserStaff();
            }else{
                majesticsupport::$_data['user_staff'] = false;
            }

            MJTC_includer::MJTC_getModel('ticket')->getTicketForDetail($MJTC_ticketid);
            majesticsupport::addStyleSheets();
            majesticsupport::ms_register_plugin_styles();
            majesticsupport::$_data['print'] = 1; //print flag to handle appearnce
            MJTC_includer::MJTC_include_file('ticketdetail', 'ticket');
            exit();
        }
    }

    function MJTC_deactivate($MJTC_network_wide = false) {
        include_once 'includes/deactivation.php';
        if(function_exists('is_multisite') && is_multisite() && $MJTC_network_wide){
            global $wpdb;
            $MJTC_blogs = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs", array() ) );
            foreach($MJTC_blogs as $MJTC_blog_id){
                switch_to_blog( $MJTC_blog_id );
                MJTC_deactivation::MJTC_deactivate();
                restore_current_blog();
            }
        }else{
            MJTC_deactivation::MJTC_deactivate();
        }
    }

    function ms_login_redirect( $MJTC_redirect_to, $MJTC_request, $MJTC_user ) {
        //is there a user to check?
        global $user;
        if ( isset( $user->roles ) && is_array( $user->roles ) ) {
            //check for admins
            if ( in_array( 'administrator', $user->roles ) ) {
                // redirect them to the default place
                return $MJTC_redirect_to;
            } else {
                $MJTC_redirecturl = MJTC_request::MJTC_getVar('redirect_to');
                if(majesticsupport::$_config['login_redirect'] == 1 && $MJTC_redirecturl == null){
                    $MJTC_pageid = majesticsupport::getPageid();
                    $MJTC_link = "index.php?page_id=".$MJTC_pageid;
                    return $MJTC_link;
                }elseif($MJTC_redirecturl != null){
                    return $MJTC_redirecturl;
                }else{
                    return home_url();
                }
            }
        } else {
            return $MJTC_redirect_to;
        }
    }

    function resetNotificationValues(){ // config and key values empty
        
    }

    function registeractions() {
        //Ticket Action Hooks
        add_action('MJTC_ticketcreate', array($this, 'ticketcreate'), 10, 1);
        add_action('MJTC_ticketreply', array($this, 'ticketreply'), 10, 1);
        add_action('MJTC_ticketclose', array($this, 'ticketclose'), 10, 1);
        add_action('MJTC_ticketdelete', array($this, 'ticketdelete'), 10, 1);
        add_action('MJTC_ticketbeforelisting', array($this, 'ticketbeforelisting'), 10, 1);
        add_action('MJTC_ticketbeforeview', array($this, 'ticketbeforeview'), 10, 1);
        //Email Hooks
        add_action('MJTC_beforeemailticketcreate', array($this, 'beforeemailticketcreate'), 10, 4);
        add_action('MJTC_beforeemailticketreply', array($this, 'beforeemailticketreply'), 10, 4);
        add_action('MJTC_beforeemailticketclose', array($this, 'beforeemailticketclose'), 10, 4);
        add_action('MJTC_beforeemailticketdelete', array($this, 'beforeemailticketdelete'), 10, 4);
    }

    //Funtions for Ticket Hooks
    function ticketcreate($MJTC_ticketobject) {
        return $MJTC_ticketobject;
    }

    function ticketreply($MJTC_ticketobject) {
        return $MJTC_ticketobject;
    }

    function ticketclose($MJTC_ticketobject) {
        return $MJTC_ticketobject;
    }

    function ticketdelete($MJTC_ticketobject) {
        return $MJTC_ticketobject;
    }

    function ticketbeforelisting($MJTC_ticketobject) {
        return $MJTC_ticketobject;
    }

    function ticketbeforeview($MJTC_ticketobject) {
        return $MJTC_ticketobject;
    }

    //Funtion for Email Hooks
    function beforeemailticketcreate($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail) {
        return;
    }

    function beforeemailticketdelete($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail) {
        return;
    }

    function beforeemailticketreply($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail) {
        return;
    }

    function beforeemailticketclose($MJTC_recevierEmail, $MJTC_subject, $MJTC_body, $MJTC_senderEmail) {
        return;
    }

    /*
     * Include the required files
     */

    function includes() {
        if (is_admin()) {
            include_once 'includes/majesticsupportadmin.php';
        }
        if(in_array('widgets', majesticsupport::$_active_addons)){
            include_once 'includes/pageswidget.php';
        }

        include_once 'includes/captcha.php';
        include_once 'includes/recaptchalib.php';
        include_once 'includes/layout.php';
        include_once 'includes/pagination.php';
        include_once 'includes/includer.php';
        include_once 'includes/formfield.php';
        include_once 'includes/request.php';
        include_once 'includes/breadcrumbs.php';
        include_once 'includes/formhandler.php';
        include_once 'includes/shortcodes.php';
        include_once 'includes/paramregister.php';

        include_once 'includes/message.php';
        include_once 'includes/ajax.php';
        include_once 'includes/ms-hooks.php';
        require_once 'includes/constants.php';
    }

    /*
     * Include the wp core files
     */

    function mjtcLoadWpCoreFiles() {
        add_action('majesticsupport_load_wp_plugin_file', array($this,'majesticsupport_load_wp_plugin_file') );
        add_action('majesticsupport_load_wp_admin_file', array($this,'majesticsupport_load_wp_admin_file') );
        add_action('majesticsupport_load_wp_file', array($this,'majesticsupport_load_wp_file') );
        add_action('majesticsupport_load_wp_pcl_zip', array($this,'majesticsupport_load_wp_pcl_zip') );
        add_action('majesticsupport_load_wp_upgrader', array($this,'majesticsupport_load_wp_upgrader') );
        add_action('majesticsupport_load_wp_ajax_upgrader_skin', array($this,'majesticsupport_load_wp_ajax_upgrader_skin') );
        add_action('majesticsupport_load_wp_plugin_upgrader', array($this,'majesticsupport_load_wp_plugin_upgrader') );
        add_action('majesticsupport_load_wp_translation_install', array($this,'majesticsupport_load_wp_translation_install') );
        add_action('majesticsupport_load_phpass', array($this,'majesticsupport_load_phpass') );
    }

    /*
     * Localization
     */

    // public function load_plugin_textdomain() {
        //if(!load_plugin_textdomain('majestic-support')){
            // load_plugin_textdomain('majestic-support', false, MJTC_majesticsupportphplib::MJTC_dirname(plugin_basename(__FILE__)) . '/languages/');
        /*}else{
            load_plugin_textdomain('majestic-support');
        }*/
    // }

    /*
     * Check the current request and handle according to it
     */

    function checkRequest($MJTC_content) {
        return $MJTC_content;
    }

    /*
     * function for the Style Sheets
     */

    static function addStyleSheets() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('majesticsupport-commonjs', MJTC_PLUGIN_URL . 'includes/js/common.js', array(), '1.0.0', true);
        wp_enqueue_script('majesticsupport-responsivetablejs', MJTC_PLUGIN_URL . 'includes/js/responsivetable.js', array(), '1.0.0', true);
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('majesticsupport-formvalidator',MJTC_PLUGIN_URL.'includes/js/jquery.form-validator.js', array(), '1.0.0', true);
        wp_enqueue_script( 'majestic-support-cmain-js', MJTC_PLUGIN_URL . 'includes/js/common_main.js', array( 'jquery' ),'1.0.0', true);
        if(in_array('notification', majesticsupport::$_active_addons)){
            wp_localize_script('commonjs', 'common', array('apiKey_firebase' => majesticsupport::$_config['apiKey_firebase'],'authDomain_firebase'=> majesticsupport::$_config['authDomain_firebase'],'databaseURL_firebase'=>majesticsupport::$_config['databaseURL_firebase'], 'projectId_firebase' => majesticsupport::$_config['projectId_firebase'], 'storageBucket_firebase' => majesticsupport::$_config['storageBucket_firebase'], 'messagingSenderId_firebase' => majesticsupport::$_config['messagingSenderId_firebase']));
        }
        //to localize validation error messages
        $MJTC_js = '
        jQuery.formUtils.LANG = {
            errorTitle: "'.esc_html(__("Form submission failed!",'majestic-support')).'",
            requiredFields: "'.esc_html(__("You have not answered all required fields",'majestic-support')).'",
            badTime: "'.esc_html(__("You have not given a correct time",'majestic-support')).'",
            badEmail: "'.esc_html(__("You have not given a correct e-mail address",'majestic-support')).'",
            badTelephone: "'.esc_html(__("You have not given a correct phone number",'majestic-support')).'",
            badSecurityAnswer: "'.esc_html(__("You have not given a correct answer to the security question",'majestic-support')).'",
            badDate: "'.esc_html(__("You have not given a correct date",'majestic-support')).'",
            lengthBadStart: "'.esc_html(__("The input value must be between ",'majestic-support')).'",
            lengthBadEnd: "'.esc_html(__(" characters",'majestic-support')).'",
            lengthTooLongStart: "'.esc_html(__("The input value is longer than ",'majestic-support')).'",
            lengthTooShortStart: "'.esc_html(__("The input value is shorter than ",'majestic-support')).'",
            notConfirmed: "'.esc_html(__("Input values could not be confirmed",'majestic-support')).'",
            badDomain: "'.esc_html(__("Incorrect domain value",'majestic-support')).'",
            badUrl: "'.esc_html(__("The input value is not a correct URL",'majestic-support')).'",
            badCustomVal: "'.esc_html(__("The input value is incorrect",'majestic-support')).'",
            badInt: "'.esc_html(__("The input value was not a correct number",'majestic-support')).'",
            badSecurityNumber: "'.esc_html(__("Your social security number was incorrect",'majestic-support')).'",
            badUKVatAnswer: "'.esc_html(__("Incorrect UK VAT Number",'majestic-support')).'",
            badStrength: "'.esc_html(__("The password isn't strong enough",'majestic-support')).'",
            badNumberOfSelectedOptionsStart: "'.esc_html(__("You have to choose at least ",'majestic-support')).'",
            badNumberOfSelectedOptionsEnd: "'.esc_html(__(" answers",'majestic-support')).'",
            badAlphaNumeric: "'.esc_html(__("The input value can only contain alphanumeric characters ",'majestic-support')).'",
            badAlphaNumericExtra: "'.esc_html(__(" and ",'majestic-support')).'",
            wrongFileSize: "'.esc_html(__("The file you are trying to upload is too large",'majestic-support')).'",
            wrongFileType: "'.esc_html(__("The file you are trying to upload is of the wrong type",'majestic-support')).'",
            groupCheckedRangeStart: "'.esc_html(__("Please choose between ",'majestic-support')).'",
            groupCheckedTooFewStart: "'.esc_html(__("Please choose at least ",'majestic-support')).'",
            groupCheckedTooManyStart: "'.esc_html(__("Please choose a maximum of ",'majestic-support')).'",
            groupCheckedEnd: "'.esc_html(__(" item(s)",'majestic-support')).'",
            badCreditCard: "'.esc_html(__("The credit card number is not correct",'majestic-support')).'",
            badCVV: "'.esc_html(__("The CVV number was not correct",'majestic-support')).'"
        };
        ';
        wp_add_inline_script('ms-formvalidator',$MJTC_js);
    }

    public static function ms_register_plugin_styles(){
        global $wp_styles;
            wp_enqueue_style('majesticsupport-icon-css', MJTC_PLUGIN_URL . 'includes/css/majestic_support.css', array(), '1.0.0');
        if (!isset($wp_styles->queue)) {
            // responsive style sheets
            wp_enqueue_style('majesticsupport-desktop-css', MJTC_PLUGIN_URL . 'includes/css/style_desktop.css',array(),'1.0.0','(min-width: 783px) and (max-width: 1280px)');
            wp_enqueue_style('majesticsupport-tablet-css', MJTC_PLUGIN_URL . 'includes/css/style_tablet.css',array(),'1.0.0','(min-width: 668px) and (max-width: 782px)');
            wp_enqueue_style('majesticsupport-mobile-css', MJTC_PLUGIN_URL . 'includes/css/style_mobile.css',array(),'1.0.0','(min-width: 481px) and (max-width: 667px)');
            wp_enqueue_style('majesticsupport-oldmobile-css', MJTC_PLUGIN_URL . 'includes/css/style_oldmobile.css',array(),'1.0.0','(max-width: 480px)');
            if(is_rtl()){
                wp_enqueue_style('majesticsupport-main-css-rtl', MJTC_PLUGIN_URL . 'includes/css/stylertl.css', array(), '1.0.0');
            }
        } else {    
            MJTC_includer::MJTC_getModel('majesticsupport')->checkIfMainCssFileIsEnqued();
        }
    }

    public static function ms_admin_register_plugin_styles() {
        wp_register_style('mjsupport-bootstrapcss', MJTC_PLUGIN_URL . 'includes/css/bootstrap.min.css', array(), '1.0.0');
        wp_register_style('mjsupport-admincss', MJTC_PLUGIN_URL . 'includes/css/admincss.css', array(), '1.0.0');
        wp_enqueue_style('mjsupport-admincss');
        if(is_rtl()){
            wp_register_style('mjsupport-admincss-rtl', MJTC_PLUGIN_URL . 'includes/css/admincssrtl.css', array(), '1.0.0');
            wp_enqueue_style('mjsupport-admincss-rtl');
        }
    }

    /*
     * function to get the pageid from the wpoptions
     */

    public static function getPageid() {
        if(majesticsupport::$_pageid != ''){
            return majesticsupport::$_pageid;
        }else{
            $MJTC_pageid = MJTC_request::MJTC_getVar('page_id','GET');
            if($MJTC_pageid){
                return $MJTC_pageid;
            }else{ // in case of categories popup
                $MJTC_query = "SELECT configvalue FROM `".majesticsupport::$_db->prefix."mjtc_support_config` WHERE configname = 'default_pageid'";
                $MJTC_pageid = majesticsupport::$_db->get_var($MJTC_query);
                return $MJTC_pageid;
            }
        }
    }

    public static function setPageID($MJTC_id) {
        majesticsupport::$_pageid = $MJTC_id;
        return;
    }

    static function MJTC_sanitizeData($MJTC_data){
        if($MJTC_data == null){
            return $MJTC_data;
        }
        if(is_array($MJTC_data)){
            return map_deep( $MJTC_data, 'sanitize_text_field' );
        }else{
            return sanitize_text_field( $MJTC_data );
        }
    }

    public static function MJTC_getVarValue($MJTC_text_string) {
        $translations = get_translations_for_domain('majestic-support');
        $translation  = $translations->translate( $MJTC_text_string );
        return esc_html($translation);
    }

    /*
     * function to parse the spaces in given string
     */

    public static function parseSpaces($MJTC_string) {
        // php 8 issue for str_replce
        if($MJTC_string == ''){
            return $MJTC_string;
        }
        return MJTC_majesticsupportphplib::MJTC_str_replace('%20',' ',$MJTC_string);
    }

    static function checkScreenTag(){
        if(!is_admin()){
            if (majesticsupport::$_config['support_screentag'] == 1) { // we need to show the support ticket tag
                if (majesticsupport::$_config['support_custom_img'] == '0') {
                    $MJTC_img_scr = MJTC_PLUGIN_URL.'includes/images/support.png';
                } else {
                    $MJTC_maindir = wp_upload_dir();
                    $MJTC_basedir = $MJTC_maindir['baseurl'];
                    $MJTC_datadirectory = majesticsupport::$_config['data_directory'];
                    $MJTC_img_scr = $MJTC_basedir . '/' . $MJTC_datadirectory.'/supportImg/'.esc_attr(majesticsupport::$_config['support_custom_img']);
                }
                if (isset(majesticsupport::$_config['support_custom_txt']) && majesticsupport::$_config['support_custom_txt'] != '') {
                    $MJTC_support_txt = majesticsupport::$_config['support_custom_txt'];
                } else {
                    $MJTC_support_txt = "Support";
                }
                $MJTC_location = 'left';
                $MJTC_borderradius = '0px 8px 8px 0px';
                $MJTC_padding = '5px 10px 5px 10px';
                switch (majesticsupport::$_config['screentag_position']) {
                    case 1: // Top left
                        $MJTC_top = "30px";
                        $MJTC_left = "0px";
                        $MJTC_right = "auto";
                        $MJTC_bottom = "auto";
                    break;
                    case 2: // Top right
                        $MJTC_top = "30px";
                        $MJTC_left = "auto";
                        $MJTC_right = "0px";
                        $MJTC_bottom = "auto";
                        $MJTC_location = 'right';
                        $MJTC_borderradius = '8px 0px 0px 8px';
                        $MJTC_padding = '5px 20px 5px 10px';
                    break;
                    case 3: // middle left
                        $MJTC_top = "48%";
                        $MJTC_left = "0px";
                        $MJTC_right = "auto";
                        $MJTC_bottom = "auto";
                    break;
                    case 4: // middle right
                        $MJTC_top = "48%";
                        $MJTC_left = "auto";
                        $MJTC_right = "0px";
                        $MJTC_bottom = "auto";
                        $MJTC_location = 'right';
                        $MJTC_borderradius = '8px 0px 0px 8px';
                        $MJTC_padding = '5px 20px 5px 10px';
                    break;
                    case 5: // bottom left
                        $MJTC_top = "auto";
                        $MJTC_left = "0px";
                        $MJTC_right = "auto";
                        $MJTC_bottom = "30px";
                    break;
                    case 6: // bottom right
                        $MJTC_top = "auto";
                        $MJTC_left = "auto";
                        $MJTC_right = "0px";
                        $MJTC_bottom = "30px";
                        $MJTC_location = 'right';
                        $MJTC_borderradius = '8px 0px 0px 8px';
                        $MJTC_padding = '5px 20px 5px 10px';
                    break;
                }

                $MJTC_html ='
                        <div id="mjtc-support_screentag" style = "top:'.$MJTC_top.';left:'.$MJTC_left.';right:'.$MJTC_right.';bottom:'.$MJTC_bottom.';padding:'.$MJTC_padding.';border-radius:'.$MJTC_borderradius.';">
                        <a class="mjtc-support_screentag_anchor" href="' . esc_url(site_url('?page_id=' . esc_attr(majesticsupport::$_config['default_pageid']))) . '">';
                if($MJTC_location == 'right'){
                    if (majesticsupport::$_config['support_custom_img'] == '0') {
                        $MJTC_html .= '<svg style="margin-'.$MJTC_location.':10px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"></path><path d="M21 16v2a4 4 0 0 1-4 4h-5"></path></svg>';
                    } else {
                        $MJTC_html .= '<img class="mjtc-support_screentag_image" style="margin-'.$MJTC_location.':10px;" alt="screen tag" src="'.esc_url($MJTC_img_scr).'" /><span class="text">'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_support_txt)).'</span>';
                    }
                }else{
                    if (majesticsupport::$_config['support_custom_img'] == '0') {
                        $MJTC_html .= '<span class="text">'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_support_txt)).'</span>
                        <svg style="margin-'.$MJTC_location.':10px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"></path><path d="M21 16v2a4 4 0 0 1-4 4h-5"></path></svg>';
                    } else {
                        $MJTC_html .= '<span class="text">'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_support_txt)).'</span><img class="mjtc-support_screentag_image" style="margin-'.$MJTC_location.':10px;" alt="screen tag" src="'.esc_url($MJTC_img_scr).'" />';
                    }
                }
                $MJTC_html .= '</a>
                        </div>';
                        $majesticsupport_js = '
                            jQuery(document).ready(function(){
                                jQuery("div#mjtc-support_screentag").css("'.esc_attr($MJTC_location).'","-"+(jQuery("div#mjtc-support_screentag span.text").width() + 25)+"px");
                                jQuery("div#mjtc-support_screentag").css("opacity",1);
                                jQuery("div#mjtc-support_screentag").hover(
                                    function(){
                                        jQuery(this).animate({'.esc_attr($MJTC_location).': "+="+(jQuery("div#mjtc-support_screentag span.text").width() + 25)}, 1000);
                                    },
                                    function(){
                                        jQuery(this).animate({'.esc_attr($MJTC_location).': "-="+(jQuery("div#mjtc-support_screentag span.text").width() + 25)}, 1000);
                                    }
                                );
                            });';
                        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
                echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
            }
        }
    }

    static function makeUrl($args = array()){
        global $wp_rewrite;

        $pageid = MJTC_request::MJTC_getVar('mspageid');
        if(is_numeric($pageid)){
            $permalink = get_the_permalink($pageid);
        }else{
            if(isset($args['mspageid']) && is_numeric($args['mspageid'])){
                $permalink = get_the_permalink($args['mspageid']);
            }else{
                $permalink = get_the_permalink();
            }
        }

        if (!$wp_rewrite->using_permalinks() || is_feed()){
            if(!MJTC_majesticsupportphplib::MJTC_strstr($permalink, 'page_id') && !MJTC_majesticsupportphplib::MJTC_strstr($permalink, '?p=')){
                $page['page_id'] = get_option('page_on_front');
                $args = $page + $args;
            }
            $MJTC_redirect_url = add_query_arg($args,$permalink);
            return $MJTC_redirect_url;
        }

        if(isset($args['mjsmod']) && isset($args['mjslay'])){
            // Get the original query parts
            $redirect = wp_parse_url($permalink);
            if (!isset($redirect['query']))
                $redirect['query'] = '';

            if(MJTC_majesticsupportphplib::MJTC_strstr($permalink, '?')){ // if variable exist
                $redirect_array = MJTC_majesticsupportphplib::MJTC_explode('?', $permalink);
                $_redirect = $redirect_array[0];
            }else{
                $_redirect = $permalink;
            }

            if($_redirect[MJTC_majesticsupportphplib::MJTC_strlen($_redirect) - 1] == '/'){
                $_redirect = MJTC_majesticsupportphplib::MJTC_substr($_redirect, 0, MJTC_majesticsupportphplib::MJTC_strlen($_redirect) - 1);
            }

            // If is layout
            $changename = false;
            if(file_exists(WP_PLUGIN_DIR.'/js-jobs/js-jobs.php')){
                $changename = true;
            }
            if(file_exists(WP_PLUGIN_DIR.'/js-vehicle-manager/mjtc-vehicle-manager.php')){
                $changename = true;
            }
            if (isset($args['mjslay'])) {
                $layout = '';
                $layout = MJTC_includer::MJTC_getModel('slug')->getSlugFromFileName($args['mjslay'],$args['mjsmod']);
                global $wp_rewrite;
                $slug_prefix = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('home_slug_prefix');
                if(is_home() || is_front_page()){
                    if($_redirect == site_url()){
                        $layout = $slug_prefix.$layout;
                    }
                }else{
                    if($_redirect == site_url()){
                        $layout = $slug_prefix.$layout;
                    }
                }
                $_redirect .= '/' . $layout;
            }
            // If is list
            if (isset($args['list'])) {
                $_redirect .= '/' . $args['list'];
            }
            // If is sortby
            if (isset($args['sortby'])) {
                $_redirect .= '/' . $args['sortby'];
            }
            // If is majesticsupport_ticketid
            if (isset($args['majesticsupportid'])) {
                $_redirect .= '/' . $args['majesticsupportid'];
                if($args['mjslay'] == 'addticket'){
                    $_redirect .= '_10';// 10 for ticket id
                }
            }

            if (isset($args['edd_order_id'])) {
                $_redirect .= '/' . $args['edd_order_id'].'_11';// 11 for easy digital downloads id
            }

            if (isset($args['uid'])) {
                $_redirect .= '/' . $args['uid'].'_12';// 12 for user id
            }

            if (isset($args['paidsupportid'])) {
                $_redirect .= '/' . $args['paidsupportid'].'_13';// 13 for paid support id
            }
            if (isset($args['formid'])){
                $_redirect .= '/' . $args['formid'].'_15';// 15 multi form id
            }


            if (isset($args['ms-id'])){
                $_redirect .= '/' . $args['ms-id'];
            }
            if (isset($args['ms-date-start'])){
                $_redirect .= '/date-start:' . $args['ms-date-start'];
            }
            if (isset($args['ms-date-end'])){
                $_redirect .= '/date-end:' . $args['ms-date-end'];
            }
            if (isset($args['mjtc_redirecturl'])){
                $_redirect .= '/?mjtc_redirecturl=' . $args['mjtc_redirecturl'];
            }
            if (isset($args['token'])){
                $_redirect .= '/?token=' . $args['token'];
            }
            if (isset($args['successflag'])){
                $_redirect .= '/?successflag=' . $args['successflag'];
            }
            return $_redirect;
        }else{ // incase of form
            $MJTC_redirect_url = add_query_arg($args,$permalink);
            return $MJTC_redirect_url;
        }
    }

    function MJTC_reset_addon_query(){
        majesticsupport::$_addon_query = array('select'=>'','join'=>'','where'=>'');
    }

    function majesticsupport_load_wp_plugin_file() {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    function majesticsupport_load_wp_admin_file() {
        require_once ABSPATH . 'wp-admin/includes/admin.php';
    }

    function majesticsupport_load_wp_file() {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    function majesticsupport_load_wp_pcl_zip() {
        require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
    }

    function majesticsupport_load_wp_ajax_upgrader_skin() {
        require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
    }

    function majesticsupport_load_wp_upgrader() {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    function majesticsupport_load_wp_plugin_upgrader() {
        require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
    }

    function majesticsupport_load_wp_translation_install() {
        require_once ABSPATH . 'wp-admin/includes/translation-install.php';
    }

    function majesticsupport_load_wp_plugin_install() {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    function majesticsupport_load_phpass() {
        require_once ABSPATH . 'wp-includes/class-phpass.php';
    }

    function ticketviaemail() {// this funtion also handles ticket overdue bcz of hours confiuration
        if(in_array('overdue', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('overdue')->updateTicketStatusToOverDueCron();// this funtions handles the overdue of tickets by cron
        }
        if(in_array('feedback', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getModel('ticket')->sendFeedbackMail();// this funtions handles the the feedback email
        }
        if(in_array('emailpiping', majesticsupport::$_active_addons)){
            MJTC_includer::MJTC_getController('emailpiping')->registerReadEmails();
            MJTC_includer::MJTC_getModel('emailpiping')->getAllEmailsForTickets();
        }
    }
}

add_action('init', 'mjtc_custom_init_session', 1);
function mjtc_custom_init_session() {
    wp_enqueue_script("jquery");
    majesticsupport::addStyleSheets();
}

// add the filter
$majesticsupport = new majesticsupport();

add_filter( 'login_form_middle', 'MJTC_AddLostPasswordLink' );
function MJTC_AddLostPasswordLink($MJTC_content) {
   return $MJTC_content.'
   <a href="'.site_url().'/wp-login.php?action=lostpassword">'. esc_html(__('Lost your password','majestic-support')) .'?</a>';
}

add_filter( 'login_form_middle', 'MJTC_AddRegisterLink' );
function MJTC_AddRegisterLink($MJTC_content) {
    if(get_option('users_can_register')){
        $MJTC_registerval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_register_link');
        $MJTC_registerlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('register_link');
        if($MJTC_registerval == 3){
            $MJTC_content .= ' <a href="'.esc_url(wp_registration_url()).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
        }else if($MJTC_registerval == 2 && $MJTC_registerlink != ""){
            $MJTC_content .= ' <a href="'.esc_url($MJTC_registerlink).'" title="' . esc_html(__('Register', 'majestic-support')) . '">' . esc_html(__('Register', 'majestic-support')) . '</a>';
        }else{
            $MJTC_content .= ' <a href="'.esc_url(majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport','mjslay'=>'userregister'))).'" title="' . esc_html(__('Register', 'majestic-support')) . '">'. esc_html(__('Register','majestic-support')) .'</a>';
        }
    }
    return $MJTC_content;
}

add_action( 'ms_addon_update_date_failed', 'MJTC_addonUpdateDateFailed' );
function MJTC_addonUpdateDateFailed(){
    die();
}

add_filter('style_loader_tag', 'MJTC_W3cValidation', 10, 2);
add_filter('script_loader_tag', 'MJTC_W3cValidation', 10, 2);
function MJTC_W3cValidation($tag, $MJTC_handle) {
    return MJTC_majesticsupportphplib::MJTC_preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
}

if(!empty(majesticsupport::$_active_addons)){
    require_once 'includes/addon-updater/msupdater.php';
    $MJTC_SUPPORTTICKETUpdater  = new MJTC_SUPPORTTICKETUpdater();
}

if(is_file('includes/updater/updater.php')){
    include_once 'includes/updater/updater.php';
}
// file for admin review
if(is_admin() && is_file('includes/classes/msadminreviewbox.php')){
    
}

function MJTC_get_avatar($MJTC_uid, $MJTC_class = '') {
    // Default avatar image URL
    $MJTC_defaultImage = MJTC_PLUGIN_URL . '/includes/images/user.png';

    // Ensure the UID is valid and numeric
    if (!is_numeric($MJTC_uid) || !$MJTC_uid) {
        return '<img alt="' . esc_html(__('Image', 'majestic-support')) . '" src="' . esc_url($MJTC_defaultImage) . '" class="' . esc_attr($MJTC_class) . '" />';
    }

    // in case if user is agent
    if ( in_array('agent',majesticsupport::$_active_addons)) {
        $MJTC_query = "
        SELECT id, photo FROM `" . majesticsupport::$_db->prefix . "mjtc_support_staff` AS staff WHERE staff.uid = ".intval($MJTC_uid);
        $MJTC_staff_data = majesticsupport::$_db->get_row($MJTC_query);
        if (!empty($MJTC_staff_data->photo)) {
            $MJTC_maindir = wp_upload_dir();
            $MJTC_path = $MJTC_maindir['baseurl'];

            $MJTC_imageurl = $MJTC_path."/".majesticsupport::$_config['data_directory']."/staffdata/staff_".$MJTC_staff_data->id."/".$MJTC_staff_data->photo;

            return '<img alt="' . esc_html(__('Image', 'majestic-support')) . '" src="' . esc_url($MJTC_imageurl) . '" class="' . esc_attr($MJTC_class) . '" />';
        }
    }
    $MJTC_uid = MJTC_includer::MJTC_getModel('majesticsupport')->getWPUidById($MJTC_uid);

    // Get the avatar URL
    if(majesticsupport::$_config['show_avatar'] == 1){
        $MJTC_avatar_url = get_avatar_url($MJTC_uid, array('size' => 96));
    } else {
        $MJTC_avatar_url = "";
    }

    // Check if the avatar URL is valid
    if (!empty($MJTC_avatar_url) && @getimagesize($MJTC_avatar_url)) {
        // Use WordPress's get_avatar function to generate the avatar HTML
        return get_avatar($MJTC_uid, 96, '', '', array('class' => $MJTC_class));
    } else {
        // Fallback to the default image if the avatar URL is invalid
        return '<img alt="' . esc_html(__('Image', 'majestic-support')) . '" src="' . esc_url($MJTC_defaultImage) . '" class="' . esc_attr($MJTC_class) . '" />';
    }
}

function mjtc_checkPluginInfo($MJTC_slug){
    if(file_exists(WP_PLUGIN_DIR . '/'.$MJTC_slug) && is_plugin_active($MJTC_slug)){
        $MJTC_text = esc_html(__("Activated",'majestic-support'));
        $MJTC_disabled = "disabled";
        $MJTC_class = "mjtc-btn-activated";
        $MJTC_availability = "-1";
    }else if(file_exists(WP_PLUGIN_DIR . '/'.$MJTC_slug) && !is_plugin_active($MJTC_slug)){
        $MJTC_text = esc_html(__("Active Now",'majestic-support'));
        $MJTC_disabled = "";
        $MJTC_class = "mjtc-btn-green mjtc-btn-active-now";
        $MJTC_availability = "1";
    }else if(!file_exists(WP_PLUGIN_DIR . '/'.$MJTC_slug)){
        $MJTC_text = esc_html(__("Install Now",'majestic-support'));
        $MJTC_disabled = "";
        $MJTC_class = "mjtc-btn-install-now";
        $MJTC_availability = "0";
    }
    return array("text" => $MJTC_text, "disabled" => $MJTC_disabled, "class" => $MJTC_class, "availability" => $MJTC_availability);
}

add_action( 'upgrader_process_complete', 'majesticsupport_upgrade_completed', 10, 2 ); // some time above hook does not workin, so add this hook.
function majesticsupport_upgrade_completed( $MJTC_upgrader_object, $MJTC_options ) {
    // The path to our plugin's main file
    $MJTC_our_plugin = plugin_basename( __FILE__ );
    // If an update has taken place and the updated type is plugins and the plugins element exists
    if( $MJTC_options['action'] == 'update' && $MJTC_options['type'] == 'plugin' && isset( $MJTC_options['plugins'] ) ) {
        // Iterate through the plugins being updated and check if ours is there
        foreach( $MJTC_options['plugins'] as $MJTC_plugin ) {
            if( $MJTC_plugin == $MJTC_our_plugin ) {
                update_option('ms_currentversion', majesticsupport::$_currentversion);
                include_once MJTC_PLUGIN_PATH . 'includes/updates/updates.php';
                MJTC_updates::MJTC_checkUpdates('118');
                MJTC_includer::MJTC_getModel('majesticsupport')->updateColorFile();
                MJTC_includer::MJTC_getModel('majesticsupport')->mjtc_check_license_status();
                MJTC_includer::MJTC_getModel('premiumplugin')->MSAddonsAutoUpdate();
                // MJTC_includer::MJTC_getModel('majesticsupport')->MJTCAddonsAutoUpdate();
            }
        }
    }
}


// =========================================================================
// LOAD PLUGIN TRANSLATIONS (AUTO-FALLBACK TO PLUGIN LANGUAGES FOLDER)
// =========================================================================
add_action('plugins_loaded', 'majesticsupport_load_plugin_textdomain', 1);

function majesticsupport_load_plugin_textdomain() {
    $domain = 'majestic-support';
    $locale = function_exists('determine_locale') ? determine_locale() : get_locale();
    $locale = apply_filters('plugin_locale', $locale, $domain);

    $candidates = array_unique(array_filter([
        $locale,
        strtolower((string) $locale),
        strpos((string) $locale, '_') !== false ? substr((string) $locale, 0, 2) : '',
    ]));

    foreach ($candidates as $candidate) {
        $global_mo = trailingslashit(WP_LANG_DIR) . 'plugins/' . $domain . '-' . $candidate . '.mo';
        if (file_exists($global_mo) && load_textdomain($domain, $global_mo)) {
            return;
        }
    }

    foreach ($candidates as $candidate) {
        $bundled_mo = trailingslashit(MJTC_PLUGIN_PATH) . 'languages/' . $domain . '-' . $candidate . '.mo';
        if (file_exists($bundled_mo) && load_textdomain($domain, $bundled_mo)) {
            return;
        }
    }

    // Keep the standard WordPress path registration as a final fallback.
    load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . '/languages');
}
?>
