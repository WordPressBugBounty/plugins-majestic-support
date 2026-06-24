<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_premiumpluginModel {

    private static $MJTC_server_url = 'https://majesticsupport.com/setup/index.php';

    function verfifyAddonActivation($MJTC_addon_name){
        $MJTC_option_name = 'transaction_key_for_majestic-support-'.esc_attr($MJTC_addon_name);
        $transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);
        try {
            if (! $transaction_key ) {
                throw new Exception( 'License key not found' );
            }
            if ( empty( $transaction_key ) ) {
                throw new Exception( 'License key not found' );
            }
            $MJTC_activate_results = $this->activate( array(
                'token'    => $transaction_key,
                'plugin_slug'    => $MJTC_addon_name
            ) );
            if ( false === $MJTC_activate_results ) {
                throw new Exception( 'Connection failed to the server' );
            } elseif ( isset( $MJTC_activate_results['error_code'] ) ) {
                throw new Exception( $MJTC_activate_results['error'] );
            } elseif(isset($MJTC_activate_results['verfication_status']) && $MJTC_activate_results['verfication_status'] == 1 ){
                return true;
            }
            throw new Exception( 'License could not activate. Please contact support.' );
        } catch ( Exception $MJTC_e ) {
            $MJTC_data = '<div class="notice notice-error is-dismissible">
                    <p>'.wp_kses_post($MJTC_e->getMessage()).'.</p>
                </div>';
            echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
            return false;
        }
    }

    function logAddonDeactivation($MJTC_addon_name){
        $MJTC_option_name = 'transaction_key_for_majestic-support-'.esc_attr($MJTC_addon_name);
        $transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);

        $MJTC_activate_results = $this->deactivate( array(
            'token'    => $transaction_key,
            'plugin_slug'    => $MJTC_addon_name
        ) );
    }

    function logAddonDeletion($MJTC_addon_name){
        $MJTC_option_name = 'transaction_key_for_majestic-support-'.esc_attr($MJTC_addon_name);
        $transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);
        $MJTC_activate_results = $this->delete( array(
            'token'    => $transaction_key,
            'plugin_slug'    => $MJTC_addon_name
        ) );
    }

    public static function activate( $MJTC_args ) {
        $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
        $MJTC_defaults = array(
            'request'  => 'activate',
            'domain' => $MJTC_site_url,
            'activation_call' => 1
        );

        $MJTC_args    = wp_parse_args( $MJTC_defaults, $MJTC_args );
        $MJTC_request = wp_remote_get( self::$MJTC_server_url . '?' . http_build_query( $MJTC_args, '', '&' ) );
        if ( is_wp_error( $MJTC_request ) ) {
            return wp_json_encode( array( 'error_code' => $MJTC_request->get_error_code(), 'error' => $MJTC_request->get_error_message() ) );
        }

        if ( wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
            return wp_json_encode( array( 'error_code' => wp_remote_retrieve_response_code( $MJTC_request ), 'error' => 'Error code: ' . wp_remote_retrieve_response_code( $MJTC_request ) ) );
        }
        $MJTC_response =  wp_remote_retrieve_body( $MJTC_request );
        $MJTC_response = json_decode($MJTC_response,true);
        return $MJTC_response;
    }

    /**
     * Attempt t deactivate a license
     */
    public static function deactivate( $MJTC_dargs ) {
        $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
        $MJTC_defaults = array(
            'request'  => 'deactivate',
            'domain' => $MJTC_site_url
        );

        $MJTC_args    = wp_parse_args( $MJTC_defaults, $MJTC_dargs );
        $MJTC_request = wp_remote_get( self::$MJTC_server_url . '?' . http_build_query( $MJTC_args, '', '&' ) );
        if ( is_wp_error( $MJTC_request ) || wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
            return false;
        } else {
            return wp_remote_retrieve_body( $MJTC_request );
        }
    }
    /**
     * Attempt t deactivate a license
     */
    public static function delete( $MJTC_args ) {
        $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
        $MJTC_defaults = array(
            'request'  => 'delete',
            'domain' => $MJTC_site_url,
        );

        $MJTC_args    = wp_parse_args( $MJTC_defaults, $MJTC_args );
        $MJTC_request = wp_remote_get( self::$MJTC_server_url . '?' . http_build_query( $MJTC_args, '', '&' ) );
        if ( is_wp_error( $MJTC_request ) || wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
            return false;
        } else {
            return;
        }
    }

    function verifyAddonSqlFile($MJTC_addon_name,$MJTC_addon_version){
        $MJTC_option_name = 'transaction_key_for_majestic-support-'.esc_attr($MJTC_addon_name);
        $transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);
        $MJTC_network_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getNetworkSiteUrl();
        $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
        $MJTC_defaults = array(
            'request'  => 'getactivatesql',
            'domain' => $MJTC_network_site_url,
            'subsite' => $MJTC_site_url,
            'activation_call' => 1,
            'plugin_slug' => $MJTC_addon_name,
            'addonversion' => $MJTC_addon_version,
            'token' => $transaction_key
        );
        $MJTC_request = wp_remote_get( self::$MJTC_server_url . '?' . http_build_query( $MJTC_defaults, '', '&' ) );
        if ( is_wp_error( $MJTC_request ) ) {
            return wp_json_encode( array( 'error_code' => $MJTC_request->get_error_code(), 'error' => $MJTC_request->get_error_message() ) );
        }

        if ( wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
            return wp_json_encode( array( 'error_code' => wp_remote_retrieve_response_code( $MJTC_request ), 'error' => 'Error code: ' . wp_remote_retrieve_response_code( $MJTC_request ) ) );
        }

        $MJTC_response =  wp_remote_retrieve_body( $MJTC_request );
        return $MJTC_response;
    }

    function getAddonSqlForUpdation($MJTC_plugin_slug,$MJTC_installed_version,$MJTC_new_version){
        $MJTC_option_name = 'transaction_key_for_majestic-support-'.esc_attr($MJTC_plugin_slug);
        $transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);
        $MJTC_network_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getNetworkSiteUrl();
        $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
        $MJTC_defaults = array(
            'request'  => 'getupdatesql',
            'domain' => $MJTC_network_site_url,
            'subsite' => $MJTC_site_url,
            'activation_call' => 1,
            'plugin_slug' => $MJTC_plugin_slug,
            'installedversion' => $MJTC_installed_version,
            'newversion' => $MJTC_new_version,
            'token' => $transaction_key
        );

        $MJTC_request = wp_remote_get( self::$MJTC_server_url . '?' . http_build_query( $MJTC_defaults, '', '&' ) );
        if ( is_wp_error( $MJTC_request ) ) {
            return wp_json_encode( array( 'error_code' => $MJTC_request->get_error_code(), 'error' => $MJTC_request->get_error_message() ) );
        }

        if ( wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
            return wp_json_encode( array( 'error_code' => wp_remote_retrieve_response_code( $MJTC_request ), 'error' => 'Error code: ' . wp_remote_retrieve_response_code( $MJTC_request ) ) );
        }

        $MJTC_response =  wp_remote_retrieve_body( $MJTC_request );
        return $MJTC_response;
    }

    function getAddonUpdateSqlFromUpdateDir($MJTC_installedversion, $MJTC_newversion, $MJTC_directory) {
        // --- START FILESYSTEM FIX ---
        global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            do_action('majesticsupport_load_wp_file');
        }
        if (!WP_Filesystem()) {
            return false;
        }
        $MJTC_wp_filesystem = $wp_filesystem;
        // --- END FILESYSTEM FIX ---

        if ($MJTC_installedversion != "" && $MJTC_newversion != "") {
            for ($MJTC_i = ($MJTC_installedversion + 1); $MJTC_i <= $MJTC_newversion; $MJTC_i++) {
                $MJTC_installfile = $MJTC_directory . '/' . $MJTC_i . '.sql';

                // Use $MJTC_wp_filesystem->exists instead of file_exists
                if ($MJTC_wp_filesystem->exists($MJTC_installfile)) {
                    $MJTC_delimiter = ';';
                    
                    // Use get_contents instead of fopen
                    $MJTC_file_content = $MJTC_wp_filesystem->get_contents($MJTC_installfile);
                    
                    if ($MJTC_file_content !== false) {
                        // Split the content into lines to replicate fgets logic
                        $MJTC_file_lines = explode("\n", $MJTC_file_content);
                        $MJTC_query = array();

                        foreach ($MJTC_file_lines as $MJTC_line) {
                            $MJTC_query[] = $MJTC_line;

                            if (MJTC_majesticsupportphplib::MJTC_preg_match('~' . preg_quote($MJTC_delimiter, '~') . '\s*$~iS', end($MJTC_query)) === 1) {
                                $MJTC_query = MJTC_majesticsupportphplib::MJTC_trim(implode('', $MJTC_query));
                                if ($MJTC_query != '') {
                                    $MJTC_query = MJTC_majesticsupportphplib::MJTC_str_replace("#__", majesticsupport::$_db->prefix, $MJTC_query);
                                }
                                if (!empty($MJTC_query)) {
                                    majesticsupport::$_db->query($MJTC_query);
                                }
                            }
                            if (is_string($MJTC_query) === true) {
                                $MJTC_query = array();
                            }
                        }
                    }
                }
            }
        }
    }

    function getAddonUpdateSqlFromLive($MJTC_installedversion,$MJTC_newversion,$MJTC_plugin_slug){
        if($MJTC_installedversion != "" && $MJTC_newversion != "" && $MJTC_plugin_slug != ""){
            $MJTC_addonsql = $this->getAddonSqlForUpdation($MJTC_plugin_slug,$MJTC_installedversion,$MJTC_newversion);
            $MJTC_decodedata = json_decode($MJTC_addonsql,true);
            $MJTC_delimiter = ';';
            if(isset($MJTC_decodedata['verfication_status']) && $MJTC_decodedata['update_sql'] != ""){
                $MJTC_lines = MJTC_majesticsupportphplib::MJTC_explode(PHP_EOL, $MJTC_addonsql);
                if(!empty($MJTC_lines)){
                    foreach($MJTC_lines as $MJTC_line){
                        $MJTC_query[] = $MJTC_line;
                        if (MJTC_majesticsupportphplib::MJTC_preg_match('~' . preg_quote($MJTC_delimiter, '~') . '\s*$~iS', end($MJTC_query)) === 1) {
                            $MJTC_query = MJTC_majesticsupportphplib::MJTC_trim(implode('', $MJTC_query));
                            if($MJTC_query != ''){
                                $MJTC_query = MJTC_majesticsupportphplib::MJTC_str_replace("#__", majesticsupport::$_db->prefix, $MJTC_query);
                            }
                            if (!empty($MJTC_query)) {
                                majesticsupport::$_db->query($MJTC_query);
                            }
                        }
                        if (is_string($MJTC_query) === true) {
                            $MJTC_query = array();
                        }
                    }
                }
            }
        }
    }

    function MJTC_checkAddoneInfo($MJTC_name){
        // Load WordPress Plugin API
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        // Get all installed plugins
        $MJTC_all_plugins = get_plugins();
        $MJTC_slug = $MJTC_name.'/'.$MJTC_name.'.php';
        if (isset($MJTC_all_plugins[$MJTC_slug])) {
            if(is_plugin_active($MJTC_slug)){
                $MJTC_status = esc_html(__("Activated",'majestic-support'));
                $MJTC_action = esc_html(__("Deactivate",'majestic-support'));
                $MJTC_actionMainClass = 'ms-admin-addon-status-Deactive';
                $MJTC_actionClass = 'ms-admin-adons-status-Deactive';
                $MJTC_url = "plugins.php?s=".$MJTC_name."&plugin_status=active";
                $MJTC_disabled = "disabled";
                $MJTC_class = "mjtc-btn-activated";
                $MJTC_availability = "-1";
                $MJTC_version = "";
            } else {
                $MJTC_status = esc_html(__("Deactivated",'majestic-support'));
                $MJTC_action = esc_html(__("Activate",'majestic-support'));
                $MJTC_actionMainClass = 'ms-admin-addon-status-Active';
                $MJTC_actionClass = 'ms-admin-adons-status-Active';
                $MJTC_url = "plugins.php?s=".$MJTC_name."&plugin_status=inactive";
                $MJTC_disabled = "";
                $MJTC_class = "mjtc-btn-green mjtc-btn-active-now";
                $MJTC_availability = "1";
                $MJTC_version = "";
            }
        } else {
            $MJTC_status = esc_html(__("Not Installed",'majestic-support'));
            $MJTC_action = esc_html(__("Install Now",'majestic-support'));
            $MJTC_actionMainClass = 'ms-admin-addon-status-Install';
            $MJTC_actionClass = 'ms-admin-adons-status-Install';
            $MJTC_url = admin_url("admin.php?page=majesticsupport_premiumplugin&mjslay=step1");
            $MJTC_disabled = "";
            $MJTC_class = "mjtc-btn-install-now";
            $MJTC_availability = "0";
            $MJTC_version = "---";
        }
        return array("status" => $MJTC_status, "action" => $MJTC_action, "url" => $MJTC_url, "disabled" => $MJTC_disabled, "class" => $MJTC_class, "availability" => $MJTC_availability, "actionMainClass" => $MJTC_actionMainClass, "actionClass" => $MJTC_actionClass, "version" => $MJTC_version);
    }

    function downloadandinstalladdonfromAjax(){
        if(!current_user_can('install_plugins')){
            return false;
        }
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'download-and-install-addon') ) {
            die( 'Security check Failed' );
        }

        $MJTC_key = MJTC_request::MJTC_getVar('dataFor');
        $MJTC_installedversion = MJTC_request::MJTC_getVar('currentVersion');
        $MJTC_newversion = MJTC_request::MJTC_getVar('cdnVersion');
        $MJTC_addon_json_array = array();

        if($MJTC_key != ''){
            $MJTC_addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_key);
            $MJTC_plugin_slug = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_key);
        }
        $MJTC_token = get_option('transaction_key_for_'.esc_attr($MJTC_key));
        $MJTC_result = array();
        $MJTC_result['error'] = false;
        if($MJTC_token == ''){
            $MJTC_result['error'] = esc_html(__('Addon Installation Failed','majestic-support'));
            $MJTC_result = wp_json_encode($MJTC_result);
            return $MJTC_result;
        }
        $MJTC_site_url = site_url();
        if($MJTC_site_url != ''){
            $MJTC_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("https://","",$MJTC_site_url);
            $MJTC_site_url = MJTC_majesticsupportphplib::MJTC_str_replace("http://","",$MJTC_site_url);
        }
        $MJTC_url = 'https://majesticsupport.com/setup/index.php?token='.esc_attr($MJTC_token).'&productcode='. wp_json_encode($MJTC_addon_json_array).'&domain='. $MJTC_site_url;
        // verify token
        $MJTC_verifytransactionkey = $this->verifytransactionkey($MJTC_token, $MJTC_url);
        if($MJTC_verifytransactionkey['status'] == 0){
            $MJTC_result['error'] = $MJTC_verifytransactionkey['message'];
            $MJTC_result = wp_json_encode($MJTC_result);
            return $MJTC_result;
        }
        $MJTC_install_count = 0;

        $MJTC_installed = $this->install_plugin($MJTC_url);
        if ( !is_wp_error( $MJTC_installed ) && $MJTC_installed ) {
            // had to run two seprate loops to save token for all the addons even if some error is triggered by activation.
            if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                update_option('transaction_key_for_'.$MJTC_key,$MJTC_token);
            }

            if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_key, 'majestic-support-')){
                $MJTC_activate = activate_plugin( $MJTC_key.'/'.$MJTC_key.'.php' );
                $MJTC_install_count++;
            }

            // run update sql
            if ($MJTC_installedversion != $MJTC_newversion) {
                $MJTC_optionname = 'ms-addon-'. $MJTC_plugin_slug .'s-version';
                update_option($MJTC_optionname, $MJTC_newversion);
                $MJTC_plugin_path = WP_CONTENT_DIR;
                $MJTC_plugin_path = $MJTC_plugin_path.'/plugins/'.$MJTC_key.'/includes';
                if(is_dir($MJTC_plugin_path . '/sql/') && is_readable($MJTC_plugin_path . '/sql/')){
                    if($MJTC_installedversion != ''){
                        $MJTC_installedversion = MJTC_majesticsupportphplib::MJTC_str_replace('.','', $MJTC_installedversion);
                    }
                    if($MJTC_newversion != ''){
                        $MJTC_newversion = MJTC_majesticsupportphplib::MJTC_str_replace('.','', $MJTC_newversion);
                    }
                    $this->getAddonUpdateSqlFromUpdateDir($MJTC_installedversion,$MJTC_newversion,$MJTC_plugin_path . '/sql/');
                    $MJTC_updatesdir = $MJTC_plugin_path.'/sql/';
                    if(MJTC_majesticsupportphplib::MJTC_preg_match('/majestic-support-[a-zA-Z]+/', $MJTC_updatesdir)){
                        MJTC_includer::MJTC_getModel('majesticsupport')->msRemoveAddonUpdatesFolder($MJTC_updatesdir);
                    }
                }else{
                    $this->getAddonUpdateSqlFromLive($MJTC_installedversion,$MJTC_newversion,$MJTC_plugin_slug);
                }
            }

        }else{
            $MJTC_result['error'] = esc_html(__('Addon Installation Failed','majestic-support'));
            $MJTC_result = wp_json_encode($MJTC_result);
            return $MJTC_result;
        }

        $MJTC_result['success'] = esc_html(__('Addon Installed Successfully','majestic-support'));
        $MJTC_result = wp_json_encode($MJTC_result);
        return $MJTC_result;
    }

    function install_plugin( $MJTC_plugin_zip ) {

        if (!current_user_can('install_plugins')) {
            $MJTC_result['error'] = esc_html(__('You do not have permission to install plugins.', 'majestic-support'));
            return wp_json_encode($MJTC_result);
        }

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
                $MJTC_result['error'] = esc_html(__('Addon installation failed','majestic-support')).'.';
                $MJTC_result['error'] .= " ".wp_kses(majesticsupport::MJTC_getVarValue($MJTC_unzipfile->get_error_message()), MJTC_ALLOWED_TAGS);
                $MJTC_result = wp_json_encode($MJTC_result);
                return $MJTC_result;
            } else {
                return true;
            }
        }else{
            $MJTC_error_string = is_wp_error($tmpfile) ? $tmpfile->get_error_message() : esc_html__('Unknown download error', 'majestic-support');
            $MJTC_result['error'] = esc_html(__('Addon Installation Failed, File download error','majestic-support')).'! '.esc_attr($MJTC_error_string);
            $MJTC_result = wp_json_encode($MJTC_result);
            return $MJTC_result;
        }
    }

    function verifytransactionkey($MJTC_transactionkey, $MJTC_url){
        $MJTC_message = 1;
        if($MJTC_transactionkey != ''){
            $MJTC_response = wp_remote_post( $MJTC_url );
            if( !is_wp_error($MJTC_response) && $MJTC_response['response']['code'] == 200 && isset($MJTC_response['body']) ){
                $MJTC_result = $MJTC_response['body'];
                $MJTC_result = json_decode($MJTC_result,true);
                if(is_array($MJTC_result) && isset($MJTC_result[0]) && $MJTC_result[0] == 0){
                    $MJTC_result['status'] = 0;
                } else{
                    $MJTC_result['status'] = 1;
                }
            }else{
                $MJTC_result = false;
                if(!is_wp_error($MJTC_response)){
                   $MJTC_error = $MJTC_response['response']['message'];
                }else{
                    $MJTC_error = $MJTC_response->get_error_message();
                }
            }
            if(is_array($MJTC_result) && isset($MJTC_result['status']) && $MJTC_result['status'] == 1 ){ // means everthing ok
                $MJTC_message = 1;
            }else{
                if(isset($MJTC_result[0]) && $MJTC_result[0] == 0){
                    $MJTC_error = $MJTC_result[1];
                }elseif(isset($MJTC_result['error']) && $MJTC_result['error'] != ''){
                    $MJTC_error = $MJTC_result['error'];
                }
                $MJTC_message = 0;
            }
        }else{
            $MJTC_message = 0;
            $MJTC_error = esc_html(__('Please insert activation key to proceed','majestic-support')).'!';
        }
        $MJTC_array['data'] = array();
        if ($MJTC_message == 0) {
            $MJTC_array['status'] = 0;
            $MJTC_array['message'] = $MJTC_error;
        } else {
            $MJTC_array['status'] = 1;
            $MJTC_array['message'] = 'success';
        }
        return $MJTC_array;
        
    }

    function MSAddonsAutoUpdate(){
        /*
            code for auto update check from configuration
        */

        $mjtc_addons_auto_update = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('mjtc_addons_auto_update');
        if( $mjtc_addons_auto_update != 1){
            return;
        }
        
        require_once MJTC_PLUGIN_PATH.'includes/addon-updater/msupdater.php';
        $MJTC_SUPPORTTICKETUpdater  = new MJTC_SUPPORTTICKETUpdater();
        $MJTC_cdnversiondata = $MJTC_SUPPORTTICKETUpdater->MJTC_getPluginVersionDataFromCDN();

        $majesticsupport_addons = $this->MJTC_getAddonsArray();

        $MJTC_installed_plugins = get_plugins();
        $MJTC_need_to_update = array();
        $MJTC_site_url = MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl();
        $MJTC_status_prefix = 'key_status_for_majestic-support_';
        $MJTC_final_addon_json_array = array();
        foreach ($majesticsupport_addons as $MJTC_key1 => $MJTC_value1) {
            $MJTC_matched = 0;
            $MJTC_version = "";
            foreach ($MJTC_installed_plugins as $MJTC_name => $MJTC_value) {
                $MJTC_install_plugin_name = MJTC_majesticsupportphplib::MJTC_str_replace(".php","",MJTC_majesticsupportphplib::MJTC_basename($MJTC_name));
                if($MJTC_key1 == $MJTC_install_plugin_name){
                    $MJTC_matched = 1;
                    $MJTC_version = $MJTC_value["Version"];
                    $MJTC_install_plugin_matched_name = $MJTC_install_plugin_name;
                }
            }
            if($MJTC_matched == 1){ //installed
                $MJTC_name = $MJTC_key1;
                $title = $MJTC_value1['title'];
                $MJTC_cdnavailableversion = "";
                foreach ($MJTC_cdnversiondata as $MJTC_cdnname => $MJTC_cdnversion) {
                    $MJTC_addon_json_array = array();
                    $MJTC_addon_json_final_array = array();
                    $MJTC_install_plugin_name_simple = MJTC_majesticsupportphplib::MJTC_str_replace("-", "", $MJTC_install_plugin_matched_name);
                    if($MJTC_cdnname == MJTC_majesticsupportphplib::MJTC_str_replace("-", "", $MJTC_install_plugin_matched_name)){
                        if($MJTC_cdnversion > $MJTC_version){ // new version available
                            $MJTC_status = 'update_available';
                            $MJTC_cdnavailableversion = $MJTC_cdnversion;
                            $MJTC_plugin_slug = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_name);
                            // get key status from local
                            $MJTC_token = get_option('transaction_key_for_'.esc_attr($MJTC_name));
                            $MJTC_key_local_status = get_option($MJTC_status_prefix . $MJTC_token);
                            if($MJTC_key_local_status == 1){
                                $MJTC_addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_name);
                                $MJTC_url = 'https://majesticsupport.com/setup/index.php?token='.esc_attr($MJTC_token).'&productcode='. wp_json_encode($MJTC_addon_json_array).'&domain='.$MJTC_site_url;
                                // verify token
                                $MJTC_verifytransactionkey = $this->verifytransactionkey($MJTC_token, $MJTC_url);
                                
                                if($MJTC_verifytransactionkey['status'] == 1){
                                    $MJTC_final_addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_name);
                                    $MJTC_addon_json_final_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_name);
                                    $MJTC_need_to_update[] = array("name" => $MJTC_name, "current_version" => $MJTC_version, "available_version" => $MJTC_cdnavailableversion, "plugin_slug" => $MJTC_plugin_slug );
                                    $MJTC_final_url = 'https://majesticsupport.com/setup/index.php?token='.esc_attr($MJTC_token).'&productcode='. wp_json_encode($MJTC_final_addon_json_array).'&domain='.$MJTC_site_url;
                                }
                            }
                        }
                    }    
                }
            }
        }
        $MJTC_token = "";
        if(!empty($MJTC_need_to_update)){
            $MJTC_installed = $this->install_plugin($MJTC_final_url);
            if ( !is_wp_error( $MJTC_installed ) && $MJTC_installed ) {
                // had to run two seprate loops to save token for all the addons even if some error is triggered by activation.

                // run update sql
                foreach($MJTC_need_to_update AS $MJTC_update){
                    $MJTC_installedversion = $MJTC_update["current_version"];
                    $MJTC_newversion = $MJTC_update["available_version"];
                    $MJTC_plugin_slug = $MJTC_update["plugin_slug"];
                    $MJTC_key = $MJTC_update["name"];
                    if ($MJTC_installedversion != $MJTC_newversion) {
                        $MJTC_optionname = 'ms-addon-'. $MJTC_plugin_slug .'s-version';
                        update_option($MJTC_optionname, $MJTC_newversion);
                        $MJTC_plugin_path = WP_CONTENT_DIR;
                        $MJTC_plugin_path = $MJTC_plugin_path.'/plugins/'.$MJTC_key.'/includes';
                        if(is_dir($MJTC_plugin_path . '/sql/') && is_readable($MJTC_plugin_path . '/sql/')){
                            if($MJTC_installedversion != ''){
                                $MJTC_installedversion = str_replace('.','', $MJTC_installedversion);
                            }
                            if($MJTC_newversion != ''){
                                $MJTC_newversion = str_replace('.','', $MJTC_newversion);
                            }
                            $this->getAddonUpdateSqlFromUpdateDir($MJTC_installedversion,$MJTC_newversion,$MJTC_plugin_path . '/sql/');
                            $MJTC_updatesdir = $MJTC_plugin_path.'/sql/';
                            if(MJTC_majesticsupportphplib::MJTC_preg_match('/majestic-support-[a-zA-Z]+/', $MJTC_updatesdir)){
                                $this->msRemoveAddonUpdatesFolder($MJTC_updatesdir);
                            }
                        }else{
                            $this->getAddonUpdateSqlFromLive($MJTC_installedversion,$MJTC_newversion,$MJTC_plugin_slug);
                        }
                    }
                }

            }else{
                return;
            }
        }
        return;
    }

    function MJTC_getAddonsArray(){
        return array(
            'majestic-support-actions' => array('title' => esc_html(__('Ticket Actions','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-agent' => array('title' => esc_html(__('Agents','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-aipoweredreply' => array('title' => esc_html(__('AI Powered Reply','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-instantfix' => array('title' => esc_html(__('Instant Fix','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-autoclose' => array('title' => esc_html(__('Ticket Auto Close','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-faq' => array('title' => esc_html(__('FAQs','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-helptopic' => array('title' => esc_html(__('Help Topic','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-maxticket' => array('title' => esc_html(__('Max Tickets','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-overdue' => array('title' => esc_html(__('Ticket Overdue','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-smtp' => array('title' => esc_html(__('SMTP','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-tickethistory' => array('title' => esc_html(__('Ticket History','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-useroptions' => array('title' => esc_html(__('User Options','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-mailchimp' => array('title' => esc_html(__('Mailchimp','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-export' => array('title' => esc_html(__('Export','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-announcement' => array('title' => esc_html(__('Announcements','majestic-support')), 'price' => 0, 'status' => 1),   
            'majestic-support-mail' => array('title' => esc_html(__('Internal Mail','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-note' => array('title' => esc_html(__('Private Note','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-cannedresponses' => array('title' => esc_html(__('Canned Response','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-woocommerce' => array('title' => esc_html(__('WooCommerce','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-privatecredentials'=> array('title' => esc_html(__('Private Credentials','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-envatovalidation' => array('title' => esc_html(__('Envato Validation','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-emailcc' => array('title' => esc_html(__('Email CC','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-feedback' => array('title' => esc_html(__('Feedback','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-knowledgebase' => array('title' => esc_html(__('Knowledge Base','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-mergeticket' => array('title' => esc_html(__('Merge Tickets','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-emailpiping' => array('title' => esc_html(__('Email Piping','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-timetracking' => array('title' => esc_html(__('Time Tracking','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-banemail' => array('title' => esc_html(__('Ban Email','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-notification' => array('title' => esc_html(__('Desktop Notification','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-download' => array('title' => esc_html(__('Downloads','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-agentautoassign' => array('title' => esc_html(__('Agent Auto Assign','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-multiform' => array('title' => esc_html(__('Multi Forms','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-dashboardwidgets' => array('title' => esc_html(__('Admin Widgets','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-widgets' => array('title' => esc_html(__('Front-end Widgets','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-paidsupport'  => array('title' => esc_html(__('Paid Support','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-easydigitaldownloads' => array('title' => esc_html(__('Easy Digital Downloads','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-multilanguageemailtemplates'  => array('title' => esc_html(__('Multi-Language Emails','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-ticketclosereason' => array('title' => esc_html(__('Ticket Closed Reason','majestic-support')), 'price' => 0, 'status' => 1),
            'majestic-support-autocleanup' => array('title' => esc_html(__('Auto Cleanup','majestic-support')), 'price' => 0, 'status' => 1),
        );
    }

}

?>
