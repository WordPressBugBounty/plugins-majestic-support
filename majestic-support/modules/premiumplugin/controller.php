<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class MJTC_premiumpluginController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = MJTC_request::MJTC_getLayout('mjslay', null, 'step1');
        $module = "premiumplugin";
        majesticsupport::$_data['sanitized_args']['MJTC_nonce'] = esc_html(wp_create_nonce('MJTC_nonce'));
        if ($this->canAddLayout($layout)) {
            MJTC_includer::MJTC_getModel('majesticsupport')->mjtc_check_license_status();
            switch ($layout) {
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
            $module =  'premiumplugin';
            MJTC_includer::MJTC_include_file($layout, $module);
        }
    }

    function canAddLayout($layout) {
        $nonce_value = MJTC_request::MJTC_getVar('MJTC_nonce');
        if ( wp_verify_nonce( $nonce_value, 'MJTC_nonce') ) {
            if (isset($_POST['form_request']) && $_POST['form_request'] == 'majesticsupport') {
                return false;
            } elseif (isset($_GET['action']) && $_GET['action'] == 'mstask') {
                return false;
            } else {
                if(!is_admin() && MJTC_majesticsupportphplib::MJTC_strpos($layout, 'admin_') === 0){
                    return false;
                }
                return true;
            }
        }
    }

    function verifytransactionkey(){
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'verify-transaction-key') ) {
            die( 'Security check Failed' );
        }
        $post_data['transactionkey'] = MJTC_request::MJTC_getVar('transactionkey','','');
        if($post_data['transactionkey'] != ''){


            $post_data['domain'] = site_url();
            $post_data['step'] = 'one';
            $post_data['myown'] = 1;

            $MJTC_url = 'https://majesticsupport.com/setup/index.php';

            $response = wp_remote_post( $MJTC_url, array('body' => $post_data,'timeout'=>7,'sslverify'=>false));
            if( !is_wp_error($response) && $response['response']['code'] == 200 && isset($response['body']) ){
                $result = $response['body'];
                $result = json_decode($result,true);
            }else{
                $result = false;
                if(!is_wp_error($response)){
                   $error = $response['response']['message'];
               }else{
                    $error = $response->get_error_message();
               }
            }
            if(is_array($result) && isset($result['status']) && $result['status'] == 1 ){ // means everthing ok
                $resultaddon = wp_json_encode($result);
                $resultaddon = MJTC_majesticsupportphplib::MJTC_safe_encoding( $resultaddon );
                $result['actual_transaction_key'] = $post_data['transactionkey'];
                // in case of session not working
                add_option('ms_addon_install_data',wp_json_encode($result));
                $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step2");
                wp_safe_redirect($MJTC_url);
                return;
            }else{
                if(isset($result[0]) && $result[0] == 0){
                    $error = $result[1];
                }elseif(isset($result['error']) && $result['error'] != ''){
                    $error = $result['error'];
                }
            }
        }else{
            $error = esc_html(__('Please insert activation key to proceed','majestic-support')).'!';
        }
        $array['data'] = array();
        $array['status'] = 0;
        $array['message'] = $error;
        $array['transactionkey'] = $post_data['transactionkey'];
        $array = wp_json_encode( $array );
        $array = MJTC_majesticsupportphplib::MJTC_safe_encoding($array);
        MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, COOKIEPATH);
        if ( SITECOOKIEPATH != COOKIEPATH ){
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, SITECOOKIEPATH);
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
        wp_safe_redirect($MJTC_url);
        return;
    }

    function updatetransactionkey(){
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'update-transaction-key') ) {
            die( 'Security check Failed' );
        }

        $post_data = MJTC_request::get('post');
        $addons_array = $post_data;
        if(isset($addons_array['transactionkey'])){
            unset($addons_array['transactionkey']);
        }
        $addon_json_array = array();
        $addon_name = '';
        foreach ($addons_array as $MJTC_key => $MJTC_value) {
            $addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_key);
            $addon_name = $MJTC_key;
        }

        if (empty($addon_json_array)) {
            MJTC_message::MJTC_setMessage(esc_html(__("Please select at least one addon!", "majestic-support")),'error');
            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey");
            wp_safe_redirect($MJTC_url);
            return;
        }

        $token = $post_data['transactionkey'];
        if($token != ''){
            $site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
            $post_data['transactionkey'] = $token;
            $post_data['domain'] = $site_url;
            $post_data['step'] = 'one';
            $post_data['myown'] = 1;

            $MJTC_url = 'https://majesticsupport.com/setup/index.php';

            $response = wp_remote_post( $MJTC_url, array('body' => $post_data,'timeout'=>7,'sslverify'=>false));
            if( !is_wp_error($response) && $response['response']['code'] == 200 && isset($response['body']) ){
                $result = $response['body'];
                $result = json_decode($result,true);
            } else {
                $result = false;
                if(!is_wp_error($response)){
                   $error = $response['response']['message'];
                }else{
                    $error = $response->get_error_message();
                }
            }
            if(is_array($result) && isset($result['status']) && $result['status'] == 1 ){ // means everthing ok
                $extra_addons = [];
                $allowed_addons = [];
                foreach ($addons_array as $MJTC_key => $MJTC_value) {
                    if (!array_key_exists($MJTC_key, $result['data'])) {
                        $extra_addons[] = $MJTC_key;
                    } else {
                        $allowed_addons[] = $MJTC_key;
                    }
                }
                if (!empty($extra_addons)) {
                    $extraaddons = wp_json_encode($extra_addons);
                    $allowedaddons = wp_json_encode($allowed_addons);
                    $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey&token=".$token."&extraaddons=".$extraaddons."&allowedaddons=".$allowedaddons);
                    wp_safe_redirect($MJTC_url);
                    return;
                }

                require_once MJTC_PLUGIN_PATH.'includes/addon-updater/msupdater.php';
                $MJTC_SUPPORTTICKETUpdater  = new MJTC_SUPPORTTICKETUpdater();
                $token_key = $MJTC_SUPPORTTICKETUpdater->MJTC_getTokenFromTransactionKey( $token,$addon_name);

                $MJTC_url = 'https://majesticsupport.com/setup/index.php?token='.esc_attr($token_key).'&productcode='. wp_json_encode($addon_json_array).'&domain='. $site_url;
                $verifytransactionkey = MJTC_includer::MJTC_getModel('majesticsupport')->verifytransactionkey($token_key, $MJTC_url);
                if($verifytransactionkey['status'] == 0){
                    MJTC_message::MJTC_setMessage(esc_html($verifytransactionkey['message']),'error');
                    $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey");
                    wp_safe_redirect($MJTC_url);
                    return;
                }
                $install_count = 0;

                $installed = $this->install_plugin($MJTC_url);
                if ( !is_wp_error( $installed ) && $installed ) {
                    // had to run two seprate loops to save token for all the addons even if some error is triggered by activation.
                    foreach ($post_data as $MJTC_key => $MJTC_value) {
                        if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                            update_option('transaction_key_for_'.$MJTC_key,$token_key);
                        }
                    }

                    foreach ($post_data as $MJTC_key => $MJTC_value) {
                        if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                            $activate = activate_plugin( $MJTC_key.'/'.$MJTC_key.'.php' );
                            $install_count++;
                        }
                    }
                    MJTC_message::MJTC_setMessage(esc_html(__('Addon(s) Installed successfully!', 'majestic-support')),'updated');
                }else{
                    MJTC_message::MJTC_setMessage(esc_html(__('Addon(s) Installation Failed', 'majestic-support')),'error');
                }
            }else{
                if(isset($result[0]) && $result[0] == 0){
                    $error = $result[1];
                }elseif(isset($result['error']) && $result['error'] != ''){
                    $error = $result['error'];
                }
                MJTC_message::MJTC_setMessage(esc_html($error),'error');
            }
        }else{
            MJTC_message::MJTC_setMessage(esc_html(__('Please insert activation key to proceed', 'majestic-support')),'error');
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=updatekey");
        wp_safe_redirect($MJTC_url);
        return;
    }

    function downloadandinstalladdons(){
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'download-and-install-addons') ) {
            die( 'Security check Failed' );
        }
        $post_data = MJTC_request::get('post');

        $addons_array = $post_data;
        if(isset($addons_array['token'])){
            unset($addons_array['token']);
        }
        $addon_json_array = array();

        foreach ($addons_array as $MJTC_key => $MJTC_value) {
            if($MJTC_key != ''){
                $addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_key);
            }
        }
        $token = $post_data['token'];
        if($token == ''){
            $array['data'] = array();
            $array['status'] = 0;
            $array['message'] = esc_html(__('Addon Installation Failed','majestic-support')).'!';
            $array['transactionkey'] = $post_data['transactionkey'];
            $array = wp_json_encode( $array );
            $array = MJTC_majesticsupportphplib::MJTC_safe_encoding($array);
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, SITECOOKIEPATH);
            }
            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
            wp_safe_redirect($MJTC_url);
            exit;
        }
        $site_url = site_url();
        if($site_url != ''){
		    $site_url = MJTC_majesticsupportphplib::MJTC_str_replace("https://","",$site_url);
            $site_url = MJTC_majesticsupportphplib::MJTC_str_replace("http://","",$site_url);
        }
        $MJTC_url = 'https://majesticsupport.com/setup/index.php?token='.esc_attr($token).'&productcode='. wp_json_encode($addon_json_array).'&domain='. esc_attr($site_url);

        $install_count = 0;

        $installed = $this->install_plugin($MJTC_url);
        if ( !is_wp_error( $installed ) && $installed ) {
            // had to run two seprate loops to save token for all the addons even if some error is triggered by activation.
            foreach ($post_data as $MJTC_key => $MJTC_value) {
                if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                    update_option('transaction_key_for_'.$MJTC_key,$token);
                }
            }

            foreach ($post_data as $MJTC_key => $MJTC_value) {
                if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                    $activate = activate_plugin( $MJTC_key.'/'.$MJTC_key.'.php' );
                    $install_count++;
                }
            }

        }else{
            $array['data'] = array();
            $array['status'] = 0;
            $array['message'] = esc_html(__('Addon Installation Failed','majestic-support')).'!';
            $array['transactionkey'] = $post_data['transactionkey'];
            $array = wp_json_encode( $array );
            $array = MJTC_majesticsupportphplib::MJTC_safe_encoding($array);
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, SITECOOKIEPATH);
            }

            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
            wp_safe_redirect($MJTC_url);
            exit;
        }
        $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step3");
        wp_safe_redirect($MJTC_url);
    }

    function install_plugin( $plugin_zip ) {

        do_action('majesticsupport_load_wp_admin_file');
        WP_Filesystem();
        $tmpfile = download_url( $plugin_zip);

        if ( !is_wp_error( $tmpfile ) && $tmpfile ) {
            $plugin_path = WP_CONTENT_DIR;
            $plugin_path = $plugin_path.'/plugins/';
            $path = MJTC_PLUGIN_PATH.'addon.zip';
            copy( $tmpfile, $path );
            $unzipfile = unzip_file( $path, $plugin_path);

            if ( file_exists( $path ) ) {
                wp_delete_file( $path ); // must unlink afterwards
            }
            if ( file_exists( $tmpfile ) ) {
                wp_delete_file( $tmpfile ); // must unlink afterwards
            }

            if ( is_wp_error( $unzipfile ) ) {
                $array['data'] = array();
                $array['status'] = 0;
                $array['message'] = esc_html(__('Addon installation failed','majestic-support')).'.';
                $array['message'] .= " ".wp_kses(majesticsupport::MJTC_getVarValue($unzipfile->get_error_message(), MJTC_ALLOWED_TAGS));
                $array['transactionkey'] = $post_data['transactionkey'];
                $array = wp_json_encode( $array );
                $array = MJTC_majesticsupportphplib::MJTC_safe_encoding($array);
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, COOKIEPATH);
                if ( SITECOOKIEPATH != COOKIEPATH ){
                    MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, SITECOOKIEPATH);
                }

                $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
                wp_safe_redirect($MJTC_url);
                exit;
            } else {
                return true;
            }
        }else{
            $array['data'] = array();
            $array['status'] = 0;
            $error_string = $tmpfile->get_error_message();
            $array['message'] = esc_html(__('Addon Installation Failed, File download error','majestic-support')).'! '.$error_string;
            $array['transactionkey'] = $post_data['transactionkey'];
            $array = wp_json_encode( $array );
            $array = MJTC_majesticsupportphplib::MJTC_safe_encoding($array);
            MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, COOKIEPATH);
            if ( SITECOOKIEPATH != COOKIEPATH ){
                MJTC_majesticsupportphplib::MJTC_setcookie('ms_addon_return_data' , $array , 0, SITECOOKIEPATH);
            }
            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
            wp_safe_redirect($MJTC_url);
            exit;
        }
    }
}
$MJTC_premiumpluginController = new MJTC_premiumpluginController();
?>
