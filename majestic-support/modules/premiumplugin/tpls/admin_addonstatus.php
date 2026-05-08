<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
    require_once MJTC_PLUGIN_PATH.'includes/addon-updater/msupdater.php';
    $MJTC_SUPPORTTICKETUpdater  = new MJTC_SUPPORTTICKETUpdater();
    $MJTC_cdnversiondata = $MJTC_SUPPORTTICKETUpdater->MJTC_getPluginVersionDataFromCDN();
    $MJTC_not_installed = array();

    $majesticsupport_addons = MJTC_includer::MJTC_getModel('premiumplugin')->MJTC_getAddonsArray();
?>
<?php MJTC_message::MJTC_getMessage(); ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('admin_addons_status'); ?>
    	<div id="msadmin-data-wrp" class="msadmin-addons-list-data">
            <div class="msadmin-autoupdte-addons-title">
                <?php echo esc_html(__('Auto Update Add-Ons','majestic-support')); ?>
            </div>
            <div class="msadmin-autoupdte-addons-cardwrp">
                <div class="msadmin-autoupdte-addons-cardlogo">
                    <img alt="<?php echo esc_attr(__('Auto Update','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/addon-images/addons/icon.png" />
                </div>
                <div class="msadmin-autoupdte-addons-cardwrp-rightwrp">
                    <div class="msadmin-autoupdte-addons-card-title">
                        <?php echo esc_html(__('Addon will automatically update to the newest version','majestic-support')); ?>
                    </div>
                    <?php
                    $mjtc_addons_auto_update = majesticsupport::$_config['mjtc_addons_auto_update'];
                    if($mjtc_addons_auto_update == 1 ){ ?>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=majesticsupport_configuration&task=saveautoupdateconfiguration&action=mstask&mjtc_addons_auto_update=0'),'mjtc_configuration_nonce')); ?>" class="msadmin-autoupdte-addons-card-btn">
                            <?php echo esc_html(__('Auto Update','majestic-support')).': '.esc_html(__('On','majestic-support')); ?>
                        </a>
                        <?php
                    } else { ?>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=majesticsupport_configuration&task=saveautoupdateconfiguration&action=mstask&mjtc_addons_auto_update=1'),'mjtc_configuration_nonce')); ?>"  class="msadmin-autoupdte-addons-card-btn msadmin-autoupdte-addons-card-offbtn">
                            <?php echo esc_html(__('Auto Update','majestic-support')).': '.esc_html(__('Off','majestic-support')); ?>
                        </a>
                        <?php
                    } ?>
                </div>
            </div>
            <div class="msadmin-addons-alladdon-title">
                <?php echo esc_html(__('Add-Ons','majestic-support')); ?>
            </div>
    		<!-- admin addons status -->
            <div id="black_wrapper_translation"></div>
            <div id="mstran_loading">
                <div class="ms-css-spinner"></div>
            </div>
            <div class="msadmin-addons-list-wrp">
                <?php
                $MJTC_installed_plugins = get_plugins();
                ?>
                <?php
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
                        $MJTC_status = '';
                        if($MJTC_matched == 1){ //installed
                            $MJTC_name = $MJTC_key1;
                            $title = $MJTC_value1['title'];
                            $MJTC_img = MJTC_majesticsupportphplib::MJTC_str_replace("majestic-support-", "", $MJTC_key1).'.png';
                            $MJTC_cdnavailableversion = "";
                            foreach ($MJTC_cdnversiondata as $MJTC_cdnname => $MJTC_cdnversion) {
                                $MJTC_install_plugin_name_simple = MJTC_majesticsupportphplib::MJTC_str_replace("-", "", $MJTC_install_plugin_matched_name);
                                if($MJTC_cdnname == MJTC_majesticsupportphplib::MJTC_str_replace("-", "", $MJTC_install_plugin_matched_name)){
                                    if($MJTC_cdnversion > $MJTC_version){ // new version available
                                        $MJTC_status = 'update_available';
                                        $MJTC_cdnavailableversion = $MJTC_cdnversion;
                                    }else{
                                        $MJTC_status = 'updated';
                                    }
                                }    
                            }
                            mjtc_printAddoneStatus($MJTC_name, $title, $MJTC_img, $MJTC_version, $MJTC_status, $MJTC_cdnavailableversion);
                        }else{ // not installed
                            $MJTC_img = MJTC_majesticsupportphplib::MJTC_str_replace("majestic-support-", "", $MJTC_key1).'.png';
                            $MJTC_not_installed[] = array("name" => $MJTC_key1, "title" => $MJTC_value1['title'], "img" => $MJTC_img, "status" => 'not-installed', "version" => "---");
                        }
                    }
                    foreach ($MJTC_not_installed as $MJTC_notinstall_addon) {
                        mjtc_printAddoneStatus($MJTC_notinstall_addon["name"], $MJTC_notinstall_addon["title"], $MJTC_notinstall_addon["img"], $MJTC_notinstall_addon["version"], $MJTC_notinstall_addon["status"]);
                    }
                ?>
            </div>
		</div>
	</div>
</div>

<?php
function mjtc_printAddoneStatus($MJTC_name, $title, $MJTC_img, $MJTC_version, $MJTC_status, $MJTC_cdnavailableversion = ''){
    $MJTC_addoneinfo = MJTC_includer::MJTC_getModel('premiumplugin')->MJTC_checkAddoneInfo($MJTC_name);
    if ($MJTC_status == 'update_available') {
        $MJTC_wrpclass = 'ms-admin-addon-status ms-admin-addons-status-update-wrp';
        $MJTC_btnclass = 'ms-admin-addons-update-btn';
        $MJTC_btntxt = 'Update Now';
        //$MJTC_btnlink = 'id="ms-admin-addons-update" data-for="'.esc_attr($MJTC_name).'"';
		$MJTC_btnlink = 'id=ms-admin-addons-update data-for='.esc_attr($MJTC_name).'';
        $msg = '<span id="ms-admin-addon-status-cdnversion">'.esc_html(__('New Update Version','majestic-support'));
        $msg .= '<span>'." ".$MJTC_cdnavailableversion." ".'</span>';
        $msg .= esc_html(__('is Available','majestic-support')).'</span>';
    } elseif ($MJTC_status == 'expired') {
        $MJTC_wrpclass = 'ms-admin-addon-status ms-admin-addons-status-expired-wrp';
        $MJTC_btnclass = 'ms-admin-addons-expired-btn';
        $MJTC_btntxt = 'Expired';
        $MJTC_btnlink = '';
        $msg = '';
    } elseif ($MJTC_status == 'updated') {
        $MJTC_wrpclass = 'ms-admin-addon-status';
        $MJTC_btnclass = '';
        $MJTC_btntxt = 'Updated';
        $MJTC_btnlink = '';
        $msg = '';
    } else {
        $MJTC_wrpclass = 'ms-admin-addon-status';
        $MJTC_btnclass = 'ms-admin-addons-buy-btn';
        $MJTC_btntxt = 'Buy Now';
        $MJTC_btnlink = 'href="https://majesticsupport.com/add-ons/"';
        $msg = '';
    }
    $MJTC_html = '
    <div class="'.esc_attr($MJTC_wrpclass).'" id="'.esc_attr($MJTC_name).'">
        <div class="ms-addon-status-image-wrp">
            <img alt="Addone image" src="'.esc_url(MJTC_PLUGIN_URL).'includes/images/admincp/addon/'.esc_attr($MJTC_img).'" />
        </div>
        <div class="ms-admin-addon-status-title-wrp">
            <h2>'. esc_html(majesticsupport::MJTC_getVarValue($title)) .'</h2>
            <a class="'. esc_attr($MJTC_addoneinfo["actionClass"]) .'" href="'. esc_url($MJTC_addoneinfo["url"]) .'">
                '. esc_html(majesticsupport::MJTC_getVarValue($MJTC_addoneinfo["action"])) .'
            </a>
            '.wp_kses($msg, MJTC_ALLOWED_TAGS).'
        </div>
        <div class="ms-admin-addon-status-addonstatus-wrp">
            <span>'. esc_html(__('Status','majestic-support')).': ' .'</span>
            <span class="ms-admin-adons-status-Active" href="#">
                '. esc_html(majesticsupport::MJTC_getVarValue($MJTC_addoneinfo["status"])) .'
            </span>
        </div>
        <div class="ms-admin-addon-status-addonsversion-wrp">
            <span id="ms-admin-addon-status-cversion">
                '. esc_html(__('Version','majestic-support')).': 
                <span>
                    '. esc_html($MJTC_version) .'
                </span>
            </span>
        </div>
        <div class="msadmin-addon-status-addonstatusbtn-wrp">
            <a '.esc_attr($MJTC_btnlink).' class="'.esc_attr($MJTC_btnclass).'">'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_btntxt)) .'</a>
        </div>
        <div class="msadmin-addon-status-msg msadmin_success">
            <img src="'. esc_url(MJTC_PLUGIN_URL) .'includes/images/admincp/addon/success.png" />
            <span class="msadmin-addon-status-msg-txt"></span>
        </div>
        <div class="msadmin-addon-status-msg msadmin_error">
            <img src="'. esc_url(MJTC_PLUGIN_URL) .'includes/images/admincp/addon/error.png" />
            <span class="msadmin-addon-status-msg-txt"></span>
        </div>
    </div>';
        echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
    }

?>
<?php
$majesticsupport_js ="
    jQuery(document).ready(function(){
        jQuery(document).on('click', 'a#ms-admin-addons-update', function(){
            jsShowLoading();
            var dataFor = jQuery(this).attr('data-for');
            var cdnVer = jQuery('#'+ dataFor +' #ms-admin-addon-status-cdnversion span').text();
            var currentVer = jQuery('#'+ dataFor +' #ms-admin-addon-status-cversion span').text();
            var cdnVersion = cdnVer.trim();
            var currentVersion = currentVer.trim();
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'premiumplugin', task: 'downloadandinstalladdonfromAjax', dataFor:dataFor, currentVersion:currentVersion, cdnVersion:cdnVersion, '_wpnonce':'". esc_attr(wp_create_nonce("download-and-install-addon"))."'}, function (data) {
                if (data) {
                    jsHideLoading();
                    data = JSON.parse(data);
                    if(data['error']){
                        jQuery('#' + dataFor).css('background-color', '#fff');
                        jQuery('#' + dataFor).css('border-color', '#FF4F4E');
                        jQuery('#' + dataFor + ' .ms-admin-addon-status-title-wrp span').hide();
                        jQuery('#' + dataFor + ' .msadmin-addon-status-msg.msadmin_error').show();
                        jQuery('#' + dataFor + ' .msadmin-addon-status-msg.msadmin_error span.msadmin-addon-status-msg-txt').html(data['error']);
                        jQuery('#' + dataFor + ' .msadmin-addon-status-msg.msadmin_error').slideDown('slow');
                    } else if(data['success']) {
                        jQuery('#' + dataFor).css('background-color', '#fff');
                        jQuery('#' + dataFor).css('border-color', '#0C6E45');
                        jQuery('#' + dataFor + ' a#ms-admin-addons-update').hide();
                        jQuery('#' + dataFor + ' .ms-admin-addon-status-title-wrp span').hide();
                        jQuery('#' + dataFor + ' .msadmin-addon-status-msg.msadmin_success').show();
                        jQuery('#' + dataFor + ' .msadmin-addon-status-msg.msadmin_success span.msadmin-addon-status-msg-txt').html(data['success']);
                        jQuery('#' + dataFor + ' .msadmin-addon-status-msg.msadmin_success').slideDown('slow');
                    }
                }
            });
        });
    });
    function jsShowLoading(){
        jQuery('div#black_wrapper_translation').show();
        jQuery('div#mstran_loading').css('display', 'flex');
    }

    function jsHideLoading(){
        jQuery('div#black_wrapper_translation').hide();
        jQuery('div#mstran_loading').hide();
    }

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>  
