<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('importdata'); ?>
        <div id="msadmin-data-wrp">
            <?php
            $MJTC_count_for = majesticsupport::$_data['count_for'];
            $entity_counts = [];
            if($MJTC_count_for > 0 &&  !empty(majesticsupport::$_data['entity_counts'])){
                $entity_counts = majesticsupport::$_data['entity_counts'];
            }
            // plugins for which we support importing data
            $plguins_array = [];

            // plugin data
            $plguins_array['awesome-support'] = [];
            $plguins_array['awesome-support']['name'] = esc_html('Awesome Support');
            $plguins_array['awesome-support']['path'] = "awesome-support/awesome-support.php"; // needed to check if plugin is active
            $plguins_array['awesome-support']['internalid'] = 2; // value used to identfy the plugin

            // plugin data
            $plguins_array['supportcandy'] = [];
            $plguins_array['supportcandy']['name'] = esc_html('SupportCandy');
            $plguins_array['supportcandy']['path'] = "supportcandy/supportcandy.php";  // needed to check if plugin is active
            $plguins_array['supportcandy']['internalid'] = 1; // value used to identfy the plugin

            // plugin data
            $plguins_array['fluent-support'] = [];
            $plguins_array['fluent-support']['name'] = esc_html('Fluent Support');
            $plguins_array['fluent-support']['path'] = "fluent-support/fluent-support.php"; // needed to check if plugin is active
            $plguins_array['fluent-support']['internalid'] = 3; // value used to identfy the plugin


            foreach ($plguins_array as $plugin) {
                // check if Plugin is active
                if($MJTC_count_for != $plugin['internalid']) {
                    $extr_clss = 'ms-plugin-notinstalled';
                    if ( is_plugin_active( $plugin['path'] )) {
                        $extr_clss = '';
                    } ?>
                    <div class="ms-plugins-imprt-datasec <?php echo esc_attr($extr_clss);?>">
                        <span class="ms-plugins-imprt-data-plgnnme"><?php echo esc_html($plugin['name']); ?></span>
                        <?php if($extr_clss != ''){ ?>
                            <span class="ms-plugins-imprt-databtn">
                                <img class="ms-plugins-imprterror-image" alt="<?php echo esc_html(__('icon','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/imprt-icon.png" />
                                <?php echo esc_html(__('Plugin not installed','majestic-support')); ?>
                            </span>
                        <?php }else{ ?>
                            <a class="ms-plugins-imprt-databtn" href="<?php echo esc_url_raw(admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importdata&selected_plugin=".$plugin['internalid'])); ?>" title="<?php echo esc_html(__('Fetch Data','majestic-support')); ?>"><?php echo esc_html(__('Fetch Data','majestic-support')); ?></a>
                        <?php } ?>
                    </div>
                    <?php
                } else {
                    if(!empty($entity_counts)){ ?>
                        <div class="ms-singleplugin-imprt-data-sec">
                            <span class="ms-singleplugin-imprt-datatitle">
                                <?php echo esc_html($plugin['name']); ?>
                            </span>
                            <?php foreach ($entity_counts as $entity_name => $entity_val) {
                                $entity_name = ucwords(str_replace('_', ' ', $entity_name));
                                if($entity_name == 'Priority' && $entity_val > 1){
                                    $entity_name = 'Priorities';
                                }elseif($entity_name == 'Status' && $entity_val > 1){
                                    $entity_name = 'Statuses';
                                }elseif($entity_val > 1){
                                    $entity_name = $entity_name.'s';
                                }
                                $extr_clss = '';
                                if (in_array(strtolower($entity_name), ['agent', 'agent role', 'agents', 'agent roles'])) {
                                    if(!in_array('agent', majesticsupport::$_active_addons)){
                                        $extr_clss = 'ms-singleplugin-imprt-data-addonnot-instllwrp';
                                    }
                                } elseif (in_array(strtolower($entity_name), ['canned response', 'canned responses'])) {
                                    if(!in_array('cannedresponses', majesticsupport::$_active_addons)){
                                        $extr_clss = 'ms-singleplugin-imprt-data-addonnot-instllwrp';
                                    }
                                } ?>
                                <div class="ms-singleplugin-imprt-datadisc <?php echo esc_attr($extr_clss);?>">
                                    <?php echo esc_html($entity_val).'&nbsp;'.esc_html(majesticsupport::MJTC_getVarValue($entity_name)).'&nbsp;'.esc_html(__('found','majestic-support'));

                                    if (in_array(strtolower($entity_name), ['ticket', 'tickets'])) {
                                        if($plugin["internalid"] != 3 && !in_array('privatecredentials', majesticsupport::$_active_addons)){ ?>
                                            <br>
                                            <span class="ms-import-data-addon-message"><?php echo esc_html(__('Private Credentials Addon missing, ticket private credentials data will not be imported!','majestic-support')); ?></span>
                                            <?php
                                        }
                                        if(!in_array('tickethistory', majesticsupport::$_active_addons)){ ?>
                                            <br>
                                            <span class="ms-import-data-addon-message"><?php echo esc_html(__('Ticket History Addon missing, full ticket history will not be imported!','majestic-support')); ?></span>
                                            <?php
                                        }
                                        if(!in_array('timetracking', majesticsupport::$_active_addons)){ ?>
                                            <br>
                                            <span class="ms-import-data-addon-message"><?php echo esc_html(__('Time Tracking Addon missing, ticket time tracking data will not be imported!','majestic-support')); ?></span>
                                            <?php
                                        }
                                        if(!in_array('note', majesticsupport::$_active_addons)){ ?>
                                            <br>
                                            <span class="ms-import-data-addon-message"><?php echo esc_html(__('Note Addon missing, ticket internal note  data will not be imported!','majestic-support')); ?></span>
                                            <?php
                                        }
                                    }
                                    if($extr_clss != ''){ ?>
                                        <span class="ms-singleplugin-imprt-data-addonnot-instll">
                                            <img class="ms-plugins-imprterror-image" alt="<?php echo esc_html(__('icon','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/imprt-icon.png" />
                                            <?php echo esc_html(__('Addon not installed please install addon first.','majestic-support')); ?>
                                        </span>
                                        <?php
                                    } ?>
                                </div>
                                <?php
                            } ?>
                            <div class="ms-singleplugin-imprt-databtn-wrp">
                                <a class="ms-singleplugin-imprt-databtn" title="<?php echo esc_html(__('Import Data','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_thirdpartyimport&task=importPluginData&action=mstask&&selected_plugin='.$plugin["internalid"], 'importPluginData'));?>"><?php echo esc_html(__('Import Data','majestic-support')); ?></a>
                            </div>
                        </div><?php
                    } else { ?>
                        <div class="ms-singleplugin-imprt-data-sec">
                            <span class="ms-singleplugin-imprt-datatitle">
                                <?php echo esc_html($plugin['name']); ?>
                            </span>
                            <div class="ms-singleplugin-imprt-datadisc">
                                <?php echo esc_html(__('No Data Found!','majestic-support')); ?>
                            </div>
                        </div>
                        <?php
                    }
                }
            } ?>
        </div>
    </div>
</div>
