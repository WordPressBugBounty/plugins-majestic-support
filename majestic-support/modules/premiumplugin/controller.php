<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class MJTC_premiumpluginController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_layout = MJTC_request::MJTC_getLayout('mjslay', null, 'step1');
        $MJTC_module = "premiumplugin";
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if ($this->canAddLayout($MJTC_layout)) {
            MJTC_includer::MJTC_getModel('majesticsupport')->mjtc_check_license_status();
            switch ($MJTC_layout) {
                case 'admin_step1':
                    majesticsupport::$_data['versioncode'] = MJTC_includer::MJTC_getModel('configuration')->getConfigurationByConfigName('versioncode');
                    majesticsupport::$_data['productcode'] = MJTC_includer::MJTC_getModel('configuration')->getConfigurationByConfigName('productcode');
                    majesticsupport::$_data['producttype'] = MJTC_includer::MJTC_getModel('configuration')->getConfigurationByConfigName('producttype');
                break;
                case 'admin_step2':
                break;
                case 'admin_step3':
                break;
                case 'admin_addonfeatures':
                break;
                case 'admin_addonstatus':
                MJTC_includer::MJTC_getModel('majesticsupport')->mjtc_check_license_status();
                break;
                case 'admin_updatekey':
                    majesticsupport::$_data['token'] = MJTC_request::MJTC_getVar('token');
                    majesticsupport::$_data['extra_addons'] = MJTC_request::MJTC_getVar('extraaddons');
                    majesticsupport::$_data['allowed_addons'] = MJTC_request::MJTC_getVar('allowedaddons');
                    break;
                case 'admin_missingaddon':
                    break;
                case 'missingaddon':
                break;
                default:
                    exit;
            }
            $MJTC_module =  'premiumplugin';
            MJTC_includer::MJTC_include_file($MJTC_layout, $MJTC_module);
        }
    }

    function canAddLayout($MJTC_layout) {
        $MJTC_nonce_value = MJTC_request::MJTC_getVar('MJTC_nonce');
        if ( wp_verify_nonce( $MJTC_nonce_value, 'MJTC_nonce') ) {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport') {
                return false;
            } elseif (isset($_GET['action']) && $_GET['action'] == 'mstask') {
                return false;
            } else {
                if(!is_admin() && MJTC_majesticsupportphplib::MJTC_strpos($MJTC_layout, 'admin_') === 0){
                    return false;
                }
                return true;
            }
        }
    }

    function verifytransactionkey(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'verify-transaction-key') ) {
            die( 'Security check Failed' );
        }
        $MJTC_post_data['transactionkey'] = MJTC_request::MJTC_getVar('transactionkey','','');
        if($MJTC_post_data['transactionkey'] != ''){


            $MJTC_post_data['domain'] = site_url();
            $MJTC_post_data['step'] = 'one';
            $MJTC_post_data['myown'] = 1;

            $MJTC_url = 'https://majesticsupport.com/setup/index.php';

            $MJTC_response = wp_remote_post( $MJTC_url, array('body' => $MJTC_post_data,'timeout'=>7,'sslverify'=>false));
            if( !is_wp_error($MJTC_response) && $MJTC_response['response']['code'] == 200 && isset($MJTC_response['body']) ){
                $MJTC_result = $MJTC_response['body'];
                $MJTC_result = json_decode($MJTC_result,true);
            }else{
                $MJTC_result = false;
                if(!is_wp_error($MJTC_response)){
                   $MJTC_error = $MJTC_response['response']['message'];
               }else{
                    $MJTC_error = $MJTC_response->get_error_message();
               }
            }
            if(is_array($MJTC_result) && isset($MJTC_result['status']) && $MJTC_result['status'] == 1 ){ // means everthing ok
                $MJTC_resultaddon = wp_json_encode($MJTC_result);
                $MJTC_resultaddon = MJTC_majesticsupportphplib::MJTC_safe_encoding( $MJTC_resultaddon );
                $MJTC_result['actual_transaction_key'] = $MJTC_post_data['transactionkey'];
                // in case of session not working
                add_option('ms_addon_install_data',wp_json_encode($MJTC_result));
                $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step2");
                wp_safe_redirect($MJTC_url);
                return;
            }else{
                if(isset($MJTC_result[0]) && $MJTC_result[0] == 0){
                    $MJTC_error = $MJTC_result[1];
                }elseif(isset($MJTC_result['error']) && $MJTC_result['error'] != ''){
                    $MJTC_error = $MJTC_result['error'];
                }
            }
        }else{
            $MJTC_error = esc_html(__('Please insert activation key to proceed','majestic-support')).'!';
        }
        $MJTC_array['data'] = array();
        $MJTC_array['status'] = 0;
        $MJTC_array['message'] = $MJTC_error;
        $MJTC_array['transactionkey'] = $MJTC_post_data['transactionkey'];
        $MJTC_array = wp_json_encode( $MJTC_array );
        $MJTC_array = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_array);
        MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, COOKIEPATH);
        if ( SITECOOKIEPATH != COOKIEPATH ){
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, SITECOOKIEPATH);
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
        wp_safe_redirect($MJTC_url);
        return;
    }

    function updatetransactionkey(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'update-transaction-key') ) {
            die( 'Security check Failed' );
        }

        $MJTC_post_data = MJTC_request::get('post');
        $MJTC_addons_array = $MJTC_post_data;
        if(isset($MJTC_addons_array['transactionkey'])){
            unset($MJTC_addons_array['transactionkey']);
        }
        $MJTC_addon_json_array = array();
        $MJTC_addon_name = '';
        foreach ($MJTC_addons_array as $MJTC_key => $MJTC_value) {
            $MJTC_addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_key);
            $MJTC_addon_name = $MJTC_key;
        }

        if (empty($MJTC_addon_json_array)) {
            MJTC_message::MJTC_setMessage(esc_html(__("Please select at least one addon!", "majestic-support")),'error');
            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey");
            wp_safe_redirect($MJTC_url);
            return;
        }

        $MJTC_token = $MJTC_post_data['transactionkey'];
        if($MJTC_token != ''){
            $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
            $MJTC_post_data['transactionkey'] = $MJTC_token;
            $MJTC_post_data['domain'] = $MJTC_site_url;
            $MJTC_post_data['step'] = 'one';
            $MJTC_post_data['myown'] = 1;

            $MJTC_url = 'https://majesticsupport.com/setup/index.php';

            $MJTC_response = wp_remote_post( $MJTC_url, array('body' => $MJTC_post_data,'timeout'=>7,'sslverify'=>false));
            if( !is_wp_error($MJTC_response) && $MJTC_response['response']['code'] == 200 && isset($MJTC_response['body']) ){
                $MJTC_result = $MJTC_response['body'];
                $MJTC_result = json_decode($MJTC_result,true);
            } else {
                $MJTC_result = false;
                if(!is_wp_error($MJTC_response)){
                   $MJTC_error = $MJTC_response['response']['message'];
                }else{
                    $MJTC_error = $MJTC_response->get_error_message();
                }
            }
            if(is_array($MJTC_result) && isset($MJTC_result['status']) && $MJTC_result['status'] == 1 ){ // means everthing ok
                $MJTC_extra_addons = [];
                $MJTC_allowed_addons = [];
                foreach ($MJTC_addons_array as $MJTC_key => $MJTC_value) {
                    if (!array_key_exists($MJTC_key, $MJTC_result['data'])) {
                        $MJTC_extra_addons[] = $MJTC_key;
                    } else {
                        $MJTC_allowed_addons[] = $MJTC_key;
                    }
                }
                if (!empty($MJTC_extra_addons)) {
                    $MJTC_extraaddons = wp_json_encode($MJTC_extra_addons);
                    $MJTC_allowedaddons = wp_json_encode($MJTC_allowed_addons);
                    $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey&token=".$MJTC_token."&extraaddons=".$MJTC_extraaddons."&allowedaddons=".$MJTC_allowedaddons);
                    wp_safe_redirect($MJTC_url);
                    return;
                }

                require_once MJTC_PLUGIN_PATH.'includes/addon-updater/msupdater.php';
                $MJTC_SUPPORTTICKETUpdater  = new MJTC_SUPPORTTICKETUpdater();
                $MJTC_token_key = $MJTC_SUPPORTTICKETUpdater->MJTC_getTokenFromTransactionKey( $MJTC_token,$MJTC_addon_name);

                $MJTC_url = 'https://majesticsupport.com/setup/index.php?token='.esc_attr($MJTC_token_key).'&productcode='. wp_json_encode($MJTC_addon_json_array).'&domain='. $MJTC_site_url;
                $MJTC_verifytransactionkey = MJTC_includer::MJTC_getModel('majesticsupport')->verifytransactionkey($MJTC_token_key, $MJTC_url);
                if($MJTC_verifytransactionkey['status'] == 0){
                    MJTC_message::MJTC_setMessage(esc_html($MJTC_verifytransactionkey['message']),'error');
                    $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey");
                    wp_safe_redirect($MJTC_url);
                    return;
                }
                $MJTC_install_count = 0;

                $MJTC_installed = $this->install_plugin($MJTC_url);
                if ( !is_wp_error( $MJTC_installed ) && $MJTC_installed ) {
                    // had to run two seprate loops to save token for all the addons even if some error is triggered by activation.
                    foreach ($MJTC_post_data as $MJTC_key => $MJTC_value) {
                        if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                            update_option('transaction_key_for_'.$MJTC_key,$MJTC_token_key);
                        }
                    }

                    foreach ($MJTC_post_data as $MJTC_key => $MJTC_value) {
                        if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                            $MJTC_activate = activate_plugin( $MJTC_key.'/'.$MJTC_key.'.php' );
                            $MJTC_install_count++;
                        }
                    }
                    MJTC_message::MJTC_setMessage(esc_html(__('Addon(s) Installed successfully!', 'majestic-support')),'updated');
                }else{
                    MJTC_message::MJTC_setMessage(esc_html(__('Addon(s) Installation Failed', 'majestic-support')),'error');
                }
            }else{
                if(isset($MJTC_result[0]) && $MJTC_result[0] == 0){
                    $MJTC_error = $MJTC_result[1];
                }elseif(isset($MJTC_result['error']) && $MJTC_result['error'] != ''){
                    $MJTC_error = $MJTC_result['error'];
                }
                MJTC_message::MJTC_setMessage(esc_html($MJTC_error),'error');
            }
        }else{
            MJTC_message::MJTC_setMessage(esc_html(__('Please insert activation key to proceed', 'majestic-support')),'error');
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey");
        wp_safe_redirect($MJTC_url);
        return;
    }

    function downloadandinstalladdons(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'download-and-install-addons') ) {
            die( 'Security check Failed' );
        }
        $MJTC_post_data = MJTC_request::get('post');

        $MJTC_addons_array = $MJTC_post_data;
        if(isset($MJTC_addons_array['token'])){
            unset($MJTC_addons_array['token']);
        }
        $MJTC_addon_json_array = array();

        foreach ($MJTC_addons_array as $MJTC_key => $MJTC_value) {
            if($MJTC_key != ''){
                $MJTC_addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_key);
            }
        }
        $MJTC_token = $MJTC_post_data['token'];
        if($MJTC_token == ''){
            $MJTC_array['data'] = array();
            $MJTC_array['status'] = 0;
            $MJTC_array['message'] = esc_html(__('Addon Installation Failed','majestic-support')).'!';
            $MJTC_array['transactionkey'] = $MJTC_post_data['transactionkey'];
            $MJTC_array = wp_json_encode( $MJTC_array );
            $MJTC_array = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_array);
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, SITECOOKIEPATH);
            }
            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
            wp_safe_redirect($MJTC_url);
            exit;
        }
        $MJTC_site_url = site_url();
        if($MJTC_site_url != ''){
		    $MJTC_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("https://","",$MJTC_site_url);
            $MJTC_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("http://","",$MJTC_site_url);
        }
        $MJTC_url = 'https://majesticsupport.com/setup/index.php?token='.esc_attr($MJTC_token).'&productcode='. wp_json_encode($MJTC_addon_json_array).'&domain='. esc_attr($MJTC_site_url);

        $MJTC_install_count = 0;

        $MJTC_installed = $this->install_plugin($MJTC_url);
        if ( !is_wp_error( $MJTC_installed ) && $MJTC_installed ) {
            // had to run two seprate loops to save token for all the addons even if some error is triggered by activation.
            foreach ($MJTC_post_data as $MJTC_key => $MJTC_value) {
                if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                    update_option('transaction_key_for_'.$MJTC_key,$MJTC_token);
                }
            }

            foreach ($MJTC_post_data as $MJTC_key => $MJTC_value) {
                if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                    $MJTC_activate = activate_plugin( $MJTC_key.'/'.$MJTC_key.'.php' );
                    $MJTC_install_count++;
                }
            }

        }else{
            $MJTC_array['data'] = array();
            $MJTC_array['status'] = 0;
            $MJTC_array['message'] = esc_html(__('Addon Installation Failed','majestic-support')).'!';
            $MJTC_array['transactionkey'] = $MJTC_post_data['transactionkey'];
            $MJTC_array = wp_json_encode( $MJTC_array );
            $MJTC_array = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_array);
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, SITECOOKIEPATH);
            }

            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
            wp_safe_redirect($MJTC_url);
            exit;
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step3");
        wp_safe_redirect($MJTC_url);
    }

    function install_plugin( $MJTC_plugin_zip ) {

        do_action('majesticsupport_load_wp_admin_file');
        WP_Filesystem();
        $tmpfile = download_url( $MJTC_plugin_zip);

        if ( !is_wp_error( $tmpfile ) && $tmpfile ) {
            $MJTC_plugin_path = WP_CONTENT_DIR;
            $MJTC_plugin_path = $MJTC_plugin_path.'/plugins/';
            $MJTC_path = MJTC_PLUGIN_PATH.'addon.zip';
            copy( $tmpfile, $MJTC_path );
            $MJTC_unzipfile = unzip_file( $MJTC_path, $MJTC_plugin_path);

            if ( file_exists( $MJTC_path ) ) {
                wp_delete_file( $MJTC_path ); // must unlink afterwards
            }
            if ( file_exists( $tmpfile ) ) {
                wp_delete_file( $tmpfile ); // must unlink afterwards
            }

            if ( is_wp_error( $MJTC_unzipfile ) ) {
                $MJTC_array['data'] = array();
                $MJTC_array['status'] = 0;
                $MJTC_array['message'] = esc_html(__('Addon installation failed','majestic-support')).'.';
                $MJTC_array['message'] .= " ".wp_kses(majesticsupport::MJTC_getVarValue($MJTC_unzipfile->get_error_message(), MJTC_ALLOWED_TAGS));
                $MJTC_array['transactionkey'] = $MJTC_post_data['transactionkey'];
                $MJTC_array = wp_json_encode( $MJTC_array );
                $MJTC_array = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_array);
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, COOKIEPATH);
                if ( SITECOOKIEPATH != COOKIEPATH ){
                    MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, SITECOOKIEPATH);
                }

                $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
                wp_safe_redirect($MJTC_url);
                exit;
            } else {
                return true;
            }
        }else{
            $MJTC_array['data'] = array();
            $MJTC_array['status'] = 0;
            $MJTC_error_string = $tmpfile->get_error_message();
            $MJTC_array['message'] = esc_html(__('Addon Installation Failed, File download error','majestic-support')).'! '.$MJTC_error_string;
            $MJTC_array['transactionkey'] = $MJTC_post_data['transactionkey'];
            $MJTC_array = wp_json_encode( $MJTC_array );
            $MJTC_array = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_array);
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $MJTC_array , 0, SITECOOKIEPATH);
            }
            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
            wp_safe_redirect($MJTC_url);
            exit;
        }
    }
}
$MJTC_premiumpluginController = new MJTC_premiumpluginController();
?>
