<?php
if (!defined('ABSPATH')) die('Restricted Access');

class MJTC_zywrapController {

    // --- ADD THESE TWO FUNCTIONS FOR THE MENU ROUTING ---
    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'zywrap');
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        
        if (self::canaddfile($MJTC_layout)) {
            switch ($MJTC_layout) {
                case 'admin_zywrap':
                    include_once MJTC_PLUGIN_PATH . 'includes/updates/updates.php';
                    MJTC_includer::MJTC_getModel('zywrap')->getDashboardStats();
                    break;

                case 'admin_zywrap_settings':
                    // Settings usually just load the view, model logic handled via AJAX save
                    break;

                case 'admin_zywrap_playground':
                    // No model function needed, the playground queries directly via AJAX
                    $MJTC_layout = 'admin_zywrap_playground'; 
                    break;

                case 'admin_zywrap_logs':
                    MJTC_includer::MJTC_getModel('zywrap')->getLogs();
                    $MJTC_layout = 'admin_zywrap_logs'; // Force load the admin template
                    break;
                    
                case 'admin_zywrap_errors':
                    MJTC_includer::MJTC_getModel('zywrap')->getErrors();
                    $MJTC_layout = 'admin_zywrap_errors'; 
                    break;
                    
                default:
                    exit;
            }
            $MJTC_module = (is_admin()) ? 'page' : 'mjsmod';
            $MJTC_module = MJTC_request::MJTC_getVar($MJTC_module, null, 'zywrap');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_module);

            if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_layout, 'admin_')){
                if (!current_user_can('manage_options')) {
                    return false;
                }
            }
            MJTC_includer::MJTC_include_file($MJTC_layout, $MJTC_module);
        }
    }

    function canaddfile($MJTC_layout) {
        $MJTC_nonce_value = MJTC_request::MJTC_getVar('MJTC_nonce');
        if ( wp_verify_nonce( $MJTC_nonce_value, 'MJTC_nonce') ) {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport') {
                return false;
            } elseif (isset($_GET['action']) && $_GET['action'] == 'mstask') {
                return false;
            } else {
                if(!is_admin() && majesticsupportphplib::MJTC_strpos($MJTC_layout, 'admin_') === 0){
                    return false;
                }
                return true;
            }
        }
        return false;
    }
    
    static function delete_log() {
        // SECURITY: ONLY ADMINS CAN DELETE LOGS
        if (!current_user_can('manage_options')) {
            wp_die('Security Error: Administrators only.');
        }
        
        $MJTC_id = MJTC_request::MJTC_getVar('id');
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        
        if (!wp_verify_nonce($MJTC_nonce, 'delete_log_'.$MJTC_id)) {
            die('Security check Failed');
        }
        
        MJTC_includer::MJTC_getModel('zywrap')->deleteLog($MJTC_id);
        
        // Redirect back to the errors page
        $MJTC_url = admin_url("admin.php?page=majesticsupport_zywrap&mjslay=zywrap_errors");
        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum');
        if ($MJTC_pagenum) {
            $MJTC_url .= '&pagenum=' . $MJTC_pagenum;
        }

        wp_safe_redirect($MJTC_url);
        exit;
    }

}

$MJTC_zywrapController = new MJTC_zywrapController();
