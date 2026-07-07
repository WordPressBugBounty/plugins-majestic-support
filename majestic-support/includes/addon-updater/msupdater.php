<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/* Update for custom plugins by joomsky */
class MJTC_SUPPORTTICKETUpdater {

	private $MJTC_api_key = '';
	private $MJTC_addon_update_data = array();
	private $MJTC_addon_update_data_errors = array();
	public $addon_installed_array = '';// it is public static bcz it is being used in extended class

	public $addon_installed_version_data = '';// it is public static bcz it is being used in extended class

	public function __construct() {
		$this->MJTC_updateIntilized();

		$transaction_key_array = array();
		$MJTC_addon_installed_array = array();
		foreach (majesticsupport::$_active_addons AS $MJTC_addon) {
			$MJTC_addon_installed_array[] = 'majestic-support-'.$MJTC_addon;
			$MJTC_option_name = 'transaction_key_for_majestic-support-'.$MJTC_addon;
			$transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);
			if(!in_array($transaction_key, $transaction_key_array)){
				$transaction_key_array[] = $transaction_key;
			}
		}
		$this->addon_installed_array = $MJTC_addon_installed_array;
		$this->MJTC_api_key = wp_json_encode($transaction_key_array);
	}

	// class constructor triggers this function. sets up intail hooks and filters to be used.
	public function MJTC_updateIntilized(  ) {
		add_action( 'admin_init', array( $this, 'MJTC_adminIntilization' ) );
		include_once( 'class-ms-server-calls.php' );
	}

	// admin init hook triggers this fuction. sets up admin specific hooks and filter
	public function MJTC_adminIntilization() {

		add_filter( 'plugins_api', array( $this, 'MJTC_pluginsAPI' ), 10, 3 );

		if ( current_user_can( 'update_plugins' ) ) {
			$this->MJTC_checkTriggers();
			add_action( 'admin_notices', array( $this, 'MJTC_checkUpdateNotice' ) );
			add_action( 'after_plugin_row', array( $this, 'MJTC_keyInput' ) );
		}
	}

	public function MJTC_keyInput( $MJTC_file ) {
		$MJTC_file_array = MJTC_majesticsupportphplib::MJTC_explode('/', $MJTC_file);
		$MJTC_addon_slug = $MJTC_file_array[0];
		if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_addon_slug, 'majestic-support-')){
			$MJTC_addon_name = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_addon_slug);
			if(isset($this->MJTC_addon_update_data[$MJTC_file]) || !in_array($MJTC_addon_name, majesticsupport::$_active_addons)){ // Only checking which addon have update version
				$MJTC_option_name = 'transaction_key_for_majestic-support-'.$MJTC_addon_name;
				$transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);
				$MJTC_verify_results = MJTC_includer::MJTC_getModel('premiumplugin')->activate( array(
		            'token'    => $transaction_key,
		            'plugin_slug'    => $MJTC_addon_name
		        ) );
		        if(isset($MJTC_verify_results['verfication_status']) && $MJTC_verify_results['verfication_status'] == 0){
		        	$MJTC_updateaddon_slug = MJTC_majesticsupportphplib::MJTC_str_replace("-", " ", $MJTC_addon_slug);
		        	$MJTC_message = MJTC_majesticsupportphplib::MJTC_strtoupper( MJTC_majesticsupportphplib::MJTC_substr( $MJTC_updateaddon_slug, 0, 2 ) ).MJTC_majesticsupportphplib::MJTC_substr(  MJTC_majesticsupportphplib::MJTC_ucwords($MJTC_updateaddon_slug), 2 ) .' authentication failed. Please insert valid key for authentication.';
		        	if(isset($this->MJTC_addon_update_data[$MJTC_file])){
		        		$MJTC_message = 'There is new version of '. wp_kses(MJTC_majesticsupportphplib::MJTC_strtoupper( MJTC_majesticsupportphplib::MJTC_substr( $MJTC_updateaddon_slug, 0, 2 ) ), MJTC_ALLOWED_TAGS).wp_kses(MJTC_majesticsupportphplib::MJTC_substr(  MJTC_majesticsupportphplib::MJTC_ucwords($MJTC_updateaddon_slug), 2 ), MJTC_ALLOWED_TAGS) .' avaible. Please insert valid activation key for updation.';
		        		remove_action('after_plugin_row_'.$MJTC_file,'wp_plugin_update_row');
					}
		        	include( 'views/html-key-input.php' );
		        	$MJTC_html = '
					<tr>
						<td class="plugin-update plugin-update colspanchange" colspan="3">
							<div class="update-message notice inline notice-error notice-alt"><p>'. esc_html($MJTC_message) .'</p></div>
						</td>
					</tr>';
					echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS) ;
		        }
			}
		}
	}

	public function MJTC_checkVersionUpdate( $MJTC_update_data ) {
		if ( empty( $MJTC_update_data->checked ) ) {
			return $MJTC_update_data;
		}
		$MJTC_response_version_data = get_transient('ms_addon_update_temp_data');
		$MJTC_response_version_data_cdn = get_transient('ms_addon_update_temp_data_cdn');

		if(isset($_SERVER) &&  $_SERVER['REQUEST_URI'] !=''){
            if(MJTC_majesticsupportphplib::MJTC_strstr( $_SERVER['REQUEST_URI'], 'plugins.php')) {
				$MJTC_response_version_data = get_transient('ms_addon_update_temp_data_plugins');
				$MJTC_response_version_data_cdn = get_transient('ms_addon_update_temp_data_plugins_cdn');
			 }
        }

		if($MJTC_response_version_data_cdn === false){
			$MJTC_cdnversiondata = $this->MJTC_getPluginVersionDataFromCDN();
			set_transient('ms_addon_update_temp_data_cdn', $MJTC_cdnversiondata, HOUR_IN_SECONDS * 6);
			set_transient('ms_addon_update_temp_data_plugins_cdn', $MJTC_cdnversiondata, 15);
		}else{
			$MJTC_cdnversiondata = $MJTC_response_version_data_cdn;
		}
		$MJTC_newversionfound = 0;
		if ( $MJTC_cdnversiondata) {
			if(is_object($MJTC_cdnversiondata) ){
				foreach ($MJTC_update_data->checked AS $MJTC_key => $MJTC_value) {
					$MJTC_c_key_array = MJTC_majesticsupportphplib::MJTC_explode('/', $MJTC_key);
					$MJTC_c_key = $MJTC_c_key_array[0];
					if($MJTC_c_key != ''){
						$MJTC_c_key = MJTC_majesticsupportphplib::MJTC_str_replace("-","",$MJTC_c_key);
					}
					$MJTC_newversion = $this->MJTC_getVersionFromLiveData($MJTC_cdnversiondata, $MJTC_c_key);
					if($MJTC_newversion){
						if(version_compare( $MJTC_newversion, $MJTC_value, '>' )){
							$MJTC_newversionfound = 1;
						}
					}
				}
			}
		}

		if($MJTC_newversionfound == 1){
			if($MJTC_response_version_data === false){
				$MJTC_response = $this->MJTC_getPluginVersionData();
				set_transient('ms_addon_update_temp_data', $MJTC_response, HOUR_IN_SECONDS * 6);
				set_transient('ms_addon_update_temp_data_plugins', $MJTC_response, 15);
			}else{
				$MJTC_response = $MJTC_response_version_data;
			}
			if ( $MJTC_response) {
				if(is_object($MJTC_response) ){
					if(isset($MJTC_response->addon_response_type) && $MJTC_response->addon_response_type == 'no_key'){
						foreach ($MJTC_update_data->checked AS $MJTC_key => $MJTC_value) {
							$MJTC_c_key_array = MJTC_majesticsupportphplib::MJTC_explode('/', $MJTC_key);
							$MJTC_c_key = $MJTC_c_key_array[0];
							if(isset($MJTC_response->addon_version_data->{$MJTC_c_key})){
								if(version_compare( $MJTC_response->addon_version_data->{$MJTC_c_key}, $MJTC_value, '>' )){
									$MJTC_transient_val = get_transient('ms_addon_hide_update_notice');
									if($MJTC_transient_val === false){
										set_transient('ms_addon_hide_update_notice', 1, DAY_IN_SECONDS );
									}
									$this->MJTC_addon_update_data[$MJTC_key] = $MJTC_response->addon_version_data->{$MJTC_c_key};
								}
							}
						}
					}else{// addon_response_type other than no_key
						foreach ($MJTC_update_data->checked AS $MJTC_key => $MJTC_value) {
							$MJTC_c_key_array = MJTC_majesticsupportphplib::MJTC_explode('/', $MJTC_key);
							$MJTC_c_key = $MJTC_c_key_array[0];
							if(isset($MJTC_response->MJTC_addon_update_data) && !empty($MJTC_response->MJTC_addon_update_data) && isset( $MJTC_response->MJTC_addon_update_data->{$MJTC_c_key})){
								if(version_compare( $MJTC_response->MJTC_addon_update_data->{$MJTC_c_key}->new_version, $MJTC_value, '>' )){
									$MJTC_update_data->response[ $MJTC_key ] = $MJTC_response->MJTC_addon_update_data->{$MJTC_c_key};
									$this->MJTC_addon_update_data[$MJTC_key] = $MJTC_response->MJTC_addon_update_data->{$MJTC_c_key};
								}
							}elseif(isset($MJTC_response->addon_version_data->{$MJTC_c_key})){
								if(version_compare( $MJTC_response->addon_version_data->{$MJTC_c_key}, $MJTC_value, '>' )){
									$MJTC_transient_val = get_transient('ms_addon_hide_update_expired_key_notice');
									if($MJTC_transient_val === false){
										set_transient('ms_addon_hide_update_expired_key_notice', 1, DAY_IN_SECONDS );
									}
									$this->MJTC_addon_update_data_errors[$MJTC_key] = $MJTC_response->addon_version_data->{$MJTC_c_key};
									$this->MJTC_addon_update_data[$MJTC_key] = $MJTC_response->addon_version_data->{$MJTC_c_key};
								}
							}else{ // set latest version from cdn data
								if ( $MJTC_cdnversiondata) {
									if(is_object($MJTC_cdnversiondata) ){
										$MJTC_c_key_plain = MJTC_majesticsupportphplib::MJTC_str_replace("-","",$MJTC_c_key);
										$MJTC_newversion = $this->MJTC_getVersionFromLiveData($MJTC_cdnversiondata, $MJTC_c_key_plain);
										if($MJTC_newversion){
											if(version_compare( $MJTC_newversion, $MJTC_value, '>' )){

												$MJTC_option_name = 'transaction_key_for_'.$MJTC_c_key;
												$transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);
												$MJTC_addon_json_array = array();
												$MJTC_addon_json_array[] = MJTC_majesticsupportphplib::MJTC_str_replace('majestic-support-', '', $MJTC_c_key);
												$MJTC_url = 'https://majesticsupport.com/setup/index.php?token='.$transaction_key.'&productcode='. wp_json_encode($MJTC_addon_json_array).'&domain='. site_url();

												// prepping data for seamless update of allowed addons
												$MJTC_plugin = new stdClass();
												$MJTC_plugin->id = 'w.org/plugins/majestic-support';
												$MJTC_addon_slug = $MJTC_c_key;
												$MJTC_plugin->name = $MJTC_addon_slug;
												$MJTC_plugin->plugin = $MJTC_addon_slug.'/'.$MJTC_addon_slug.'.php';
												$MJTC_plugin->slug = $MJTC_addon_slug;
												$MJTC_plugin->version = '1.0.1';
												$MJTC_addonwithoutslash = MJTC_majesticsupportphplib::MJTC_str_replace('-', '', $MJTC_addon_slug);
												$MJTC_plugin->new_version = $MJTC_newversion; 
												$MJTC_plugin->url = 'https://www.majesticsupport.com/';
												$MJTC_plugin->download_url = $MJTC_url;
												$MJTC_plugin->package = $MJTC_url;
												$MJTC_plugin->trunk = $MJTC_url;
												
												$MJTC_update_data->response[ $MJTC_key ] = $MJTC_plugin;
												$this->MJTC_addon_update_data[$MJTC_key] = $MJTC_plugin;
											}
										}

									}
								}
							}
						}
					}
				}
			}
		}// new version found	
		if(isset($MJTC_update_data->checked)){
			$this->addon_installed_version_data = $MJTC_update_data->checked;
		}
		return $MJTC_update_data;
	}

	public function MJTC_pluginsAPI( $MJTC_false, $MJTC_action, $MJTC_args ) {

		if (!isset( $MJTC_args->slug )) {
			return false;
		}

		if(MJTC_majesticsupportphplib::MJTC_strstr($MJTC_args->slug, 'majestic-support-')){
			$MJTC_response = $this->MJTC_getPluginInfo($MJTC_args->slug);
			if ($MJTC_response) {
				$MJTC_response->sections = json_decode(wp_json_encode($MJTC_response->sections),true);
				$MJTC_response->banners = json_decode(wp_json_encode($MJTC_response->banners),true);
				$MJTC_response->contributors = json_decode(wp_json_encode($MJTC_response->contributors),true);
				return $MJTC_response;
			}
		}else{
			return false;// to handle the case of plugins that need to check version data from wordpress repositry.
		}
	}

	public function MJTC_getPluginInfo($MJTC_addon_slug) {

		$MJTC_option_name = 'transaction_key_for_'.$MJTC_addon_slug;
		$transaction_key = MJTC_includer::MJTC_getModel('majesticsupport')->getAddonTransationKey($MJTC_option_name);

		if(!$transaction_key){
			die('transient');
			return false;
		}

		$MJTC_plugin_file_path = content_url().'/plugins/'.$MJTC_addon_slug.'/'.$MJTC_addon_slug.'.php';
		$MJTC_plugin_data = get_plugin_data($MJTC_plugin_file_path);

		$MJTC_response = MJTC_SupportTicketServerCalls::MJTC_PluginInformation( array(
			'plugin_slug'    => $MJTC_addon_slug,
			'version'        => $MJTC_plugin_data['Version'],
			'token'    => $transaction_key,
			'domain'          => site_url()
		) );
		if ( isset( $MJTC_response->errors ) ) {
			$this->handle_errors( $MJTC_response->errors );
		}

		// If everything is okay return the $MJTC_response
		if ( isset( $MJTC_response ) && is_object( $MJTC_response ) && $MJTC_response !== false ) {
			return $MJTC_response;
		}

		return false;
	}

	// does changes according to admin triggers.
	private function MJTC_checkTriggers() {
		if (isset($_POST['ms_addon_array_for_token']) && !empty($_POST['ms_addon_array_for_token'])) {
            $MJTC_nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!wp_verify_nonce($MJTC_nonce, 'update-plugins')) {
                return;
            }
        }
		if ( isset($_POST['ms_addon_array_for_token']) && ! empty( $_POST[ 'ms_addon_array_for_token' ])){
			$transaction_key = '';
			$MJTC_addon_name = '';
			foreach ($_POST['ms_addon_array_for_token'] as $MJTC_key => $MJTC_value) {
				if(isset($_POST[$MJTC_value.'_transaction_key']) && $_POST[$MJTC_value.'_transaction_key'] != ''){
					$transaction_key = majesticsupport::MJTC_sanitizeData($_POST[$MJTC_value.'_transaction_key']);// MJTC_sanitizeData() function uses wordpress santize functions
					$MJTC_addon_name = $MJTC_value;
					break;
				}
			}

			if($transaction_key != ''){
				$MJTC_token = $this->MJTC_getTokenFromTransactionKey( $transaction_key,$MJTC_addon_name);
				if($MJTC_token){
					foreach ($_POST['ms_addon_array_for_token'] as $MJTC_key => $MJTC_value) {
						update_option('transaction_key_for_'.$MJTC_value,$MJTC_token);
					}
				}else{
					update_option( 'ms-addon-key-error-message','Something went wrong');
				}
			}
		}else{
			foreach ($this->addon_installed_array as $MJTC_key) {
				if ( ! empty( $_GET[ 'dismiss-ms-addon-update-notice-'.$MJTC_key] ) ) {
					set_transient('dismiss-ms-addon-update-notice-'.$MJTC_key, 1, DAY_IN_SECONDS );
				}
			}
		}
	}

	public function MJTC_checkUpdateNotice( ) {
		include_once( 'views/html-update-availble.php' );
	}

	public function MJTC_getPluginVersionData() {
			$MJTC_response = MJTC_SupportTicketServerCalls::MJTC_PluginUpdateCheck($this->MJTC_api_key);
			if ( isset( $MJTC_response->errors ) ) {
				$this->msHandleErrors( $MJTC_response->errors );
			}

			// Set version variables
			if ( isset( $MJTC_response ) && is_object( $MJTC_response ) && $MJTC_response !== false ) {
				return $MJTC_response;
			}
		return false;
	}

	public function MJTC_getPluginVersionDataFromCDN() {
			$MJTC_response = MJTC_SupportTicketServerCalls::MJTC_PluginUpdateCheckFromCDN();
			if ( isset( $MJTC_response->errors ) ) {
				$this->msHandleErrors( $MJTC_response->errors );
			}

			// Set version variables
			if ( isset( $MJTC_response ) && is_object( $MJTC_response ) && $MJTC_response !== false ) {
				return $MJTC_response;
			}
		return false;
	}


	private function MJTC_getVersionFromLiveData($MJTC_data, $MJTC_addon_name){
		foreach ($MJTC_data as $MJTC_key => $MJTC_value) {
			if($MJTC_key == $MJTC_addon_name){
				return $MJTC_value;
			}
		}
		return;
	}
	public function MJTC_getPluginLatestVersionData() {
		$MJTC_response = MJTC_SupportTicketServerCalls::MJTC_GetLatestVersions();
		// Set version variables
		if ( isset( $MJTC_response ) && is_array( $MJTC_response ) && $MJTC_response !== false ) {
			return $MJTC_response;
		}
		return false;
	}

	public function MJTC_getTokenFromTransactionKey($transaction_key,$MJTC_addon_name) {
		$MJTC_response = MJTC_SupportTicketServerCalls::MJTC_GenerateToken($transaction_key,$MJTC_addon_name);
		// Set version variables
		if (is_array($MJTC_response) && isset($MJTC_response['verfication_status']) && $MJTC_response['verfication_status'] == 1 ) {
			return $MJTC_response['token'];
		}else{
			$MJTC_error_message = esc_html(__('Something went wrong. Please try again later.','majestic-support'));
			if(is_array($MJTC_response) && isset($MJTC_response['error'])){
				$MJTC_error_message = $MJTC_response['error'];
			}
			update_option( 'ms-addon-key-error-message',$MJTC_error_message);
		}
		return false;
	}
}
?>
