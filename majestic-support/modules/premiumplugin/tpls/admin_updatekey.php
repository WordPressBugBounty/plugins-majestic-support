<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="msadmin-wrapper" class="msadmin-add-on-page-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('addonslist'); ?>
        <div id="msadmin-data-wrp">
            <form class="msadmin-update-key-form" id="jsticketfrom" action="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=majesticsupport_premiumplugin&task=updatetransactionkey&action=mstask'),"update-transaction-key")); ?>" method="post">
                <div class="msadmin-update-key-wrp">
                    <div class="msadmin-update-key-section">
                        <h2 class="msadmin-update-key-title"><?php echo esc_html(__("JS Helpdesk Activation Key", 'majestic-support')); ?></h2>
                        <input id="transactionkey" name="transactionkey" required type="text" placeholder="<?php echo esc_attr(__( "XXXXX-XXXXX-XXXXX-XXXXX", 'majestic-support' )); ?>" value="<?php echo isset( majesticsupport::$_data['token'] ) ? esc_attr( majesticsupport::$_data['token'] ) : ''; ?>">
                    </div>
                    <div class="msadmin-update-key-custom-errormsgwrp">
                    <?php
                        MJTC_message::MJTC_getMessage(); ?>
                    </div>
                    <?php
                    if (!empty(majesticsupport::$_data['extra_addons'])) { ?>
                        <div class="msadmin-update-key-errormsgwrp">
                            <img alt="<?php echo esc_html(__("Info", 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/icon.png" />
                            <?php echo esc_html(__("The highlighted addons are not included in your current license. Please adjust your selection accordingly.", 'majestic-support')); ?>
                        </div>
                        <?php
                    } ?>
                    <div class="msadmin-update-key-slctall-addonswrp">
                        <span class="msadmin-update-key-slctall-addon-title"><?php echo esc_html(__("Select Addons to Update with New Activation Key", 'majestic-support')); ?></span>
                        <div class="msadmin-update-key-slctall-addon-checkbox-wrp">
                            <input class="msadmin-update-key-checkbox"id="select-all" type="checkbox">
                            <?php echo esc_html(__("Select All Addons", 'majestic-support')); ?>
                        </div>
                    </div>
                    <?php
                    $addon_array = [];

                    $all_plugins = get_plugins();
                    $extra_addons = majesticsupport::$_data['extra_addons'];
                    $allowed_addons = majesticsupport::$_data['allowed_addons'];
                    

                    foreach ($all_plugins as $plugin_file => $plugin_data) {
                        // Match plugin directory or main file starting with 'majestic-support-'
                        if (MJTC_majesticsupportphplib::MJTC_strpos($plugin_file, 'majestic-support-') === 0) {
                            $slug = MJTC_majesticsupportphplib::MJTC_dirname($plugin_file); // Gets 'majestic-support-actions'
                            $addon_array[$slug] = $plugin_data;
                        }
                    }
                    ?>
                    <div class="msadmin-update-key-all-addons-wrp">
                        <?php 
                        if (!empty($addon_array)) {
                            $majesticsupport_addons = MJTC_includer::MJTC_getModel('premiumplugin')->MJTC_getAddonsArray();
                            foreach ($addon_array as $MJTC_key => $MJTC_value) {
                                $error_class = '';
                                $isChecked = false;
                                if (!empty($extra_addons)) {
                                    if(MJTC_majesticsupportphplib::MJTC_strpos($extra_addons, $MJTC_key) !== false) {
                                        $error_class = 'msadmin-update-key-single-addon-red';
                                    }
                                }
                                if (!empty($allowed_addons)) {
                                    if(MJTC_majesticsupportphplib::MJTC_strpos($allowed_addons, $MJTC_key) !== false) {
                                        $isChecked = true;
                                    }
                                } ?>
                                <div class="msadmin-update-key-single-addon <?php echo esc_attr($error_class); ?>">
                                    <input id="addon-<?php echo esc_attr( $MJTC_key ); ?>" name="<?php echo esc_attr( $MJTC_key ); ?>" class="msadmin-update-key-checkbox" type="checkbox" <?php echo $isChecked ? 'checked' : ''; ?>>
                                    <?php
                                    if (!empty($majesticsupport_addons[$MJTC_value['TextDomain']]['title'])) {
                                        echo esc_html($majesticsupport_addons[$MJTC_value['TextDomain']]['title']);
                                    } else {
                                        echo esc_html(MJTC_majesticsupportphplib::MJTC_str_replace('Majestic Support ', '', $MJTC_value['Name']));    
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        } else { ?>
                            <div class="msadmin-update-key-no-addon-msg">
                                <?php echo esc_html(__("No Addon Installed!", 'majestic-support')); ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="msadmin-update-key-infomsgwrp">
                        <img alt="<?php echo esc_html(__("Info", 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/addon-images/info-icon.png" />
                        <?php echo esc_html(__("This will replace the old key with the new one.", 'majestic-support')); ?>
                    </div>
                    <div class="msadmin-update-key-updtebtn-wrp">
                        <button class="msadmin-update-key-updtebtn" type="submit"><?php echo esc_html(__("Update Key", 'majestic-support')); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$majesticsupport_js = "
jQuery(document).ready(function() {
    jQuery(\"#select-all\").on(\"change\", function() {
        var isChecked = jQuery(this).is(\":checked\");
        jQuery(\".msadmin-update-key-checkbox\").prop(\"checked\", isChecked);
    });

    jQuery(\".msadmin-update-key-checkbox\").on(\"change\", function() {
        var allChecked = jQuery(\".msadmin-update-key-checkbox\").length === jQuery(\".msadmin-update-key-checkbox:checked\").length;
        jQuery(\"#select-all\").prop(\"checked\", allChecked);
    });
});
";

wp_add_inline_script('majestic-support-cmain-js', $majesticsupport_js);
?>
