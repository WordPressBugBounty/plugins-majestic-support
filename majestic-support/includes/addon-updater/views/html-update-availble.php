<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!empty($this->addon_installed_array)){
	$MJTC_new_transient_flag = 0;
	$MJTC_response = get_transient('ms_addon_update_flag');
	if(!$MJTC_response){
		$MJTC_response = $this->MJTC_getPluginLatestVersionData();
		set_transient('ms_addon_update_flag',$MJTC_response,HOUR_IN_SECONDS * 6);
		$MJTC_new_transient_flag = 1;
	}
	if(!empty($MJTC_response)){
		foreach ($this->addon_installed_array as $MJTC_addon) {
			if(!isset($MJTC_response[$MJTC_addon])){
				continue;
			}
			$MJTC_plugin_file_path = content_url().'/plugins/'.$MJTC_addon.'/'.$MJTC_addon.'.php';

			$MJTC_plugin_data = get_plugin_data($MJTC_plugin_file_path);
			$MJTC_transient_val = get_transient('dismiss-ms-addon-update-notice-'.$MJTC_addon);
			if($MJTC_new_transient_flag == 1){
				delete_transient('dismiss-ms-addon-update-notice-'.$MJTC_addon);
			}
			if(!$MJTC_transient_val){
				if (version_compare( $MJTC_response[$MJTC_addon], $MJTC_plugin_data['Version'], '>' ) ) { ?>
					<div class="updated">
						<p class="wpjm-updater-dismiss" style="float:right;"><a href="<?php echo esc_url( add_query_arg( 'dismiss-ms-addon-update-notice-' . sanitize_title( $MJTC_addon ), '1' ) ); ?>"><?php __( 'Hide notice','majestic-support' ); ?></a></p>
						<p><?php printf( wp_kses('<a href="%s">New Version is avaible</a> for "%s".', admin_url('plugins.php'), esc_html( $MJTC_plugin_data['Name'] ), MJTC_ALLOWED_TAGS) ); ?></p>
					</div>
				<?php }
			}
		}
	}

}

if(get_option( 'ms-addon-key-error-message', '' ) != ''){
	$MJTC_html = '<div class="notice notice-error is-dismissible"><p>'. esc_html(get_option( 'ms-addon-key-error-message')) .'</p></div>';
	echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
	delete_option( 'ms-addon-key-error-message' );
}
?>
