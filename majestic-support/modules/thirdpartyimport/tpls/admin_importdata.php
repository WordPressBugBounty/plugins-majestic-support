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
            $MJTC_entity_counts = [];
            if($MJTC_count_for > 0 &&  !empty(majesticsupport::$_data['entity_counts'])){
                $MJTC_entity_counts = majesticsupport::$_data['entity_counts'];
            }
            // plugins for which we support importing data
            $MJTC_plguins_array = [];

            // plugin data
            $MJTC_plguins_array['awesome-support'] = [];
            $MJTC_plguins_array['awesome-support']['name'] = esc_html('Awesome Support');
            $MJTC_plguins_array['awesome-support']['path'] = "awesome-support/awesome-support.php"; // needed to check if plugin is active
            $MJTC_plguins_array['awesome-support']['internalid'] = 2; // value used to identfy the plugin

            // plugin data
            $MJTC_plguins_array['supportcandy'] = [];
            $MJTC_plguins_array['supportcandy']['name'] = esc_html('SupportCandy');
            $MJTC_plguins_array['supportcandy']['path'] = "supportcandy/supportcandy.php";  // needed to check if plugin is active
            $MJTC_plguins_array['supportcandy']['internalid'] = 1; // value used to identfy the plugin

            // plugin data
            $MJTC_plguins_array['fluent-support'] = [];
            $MJTC_plguins_array['fluent-support']['name'] = esc_html('Fluent Support');
            $MJTC_plguins_array['fluent-support']['path'] = "fluent-support/fluent-support.php"; // needed to check if plugin is active
            $MJTC_plguins_array['fluent-support']['internalid'] = 3; // value used to identfy the plugin


            foreach ($MJTC_plguins_array as $MJTC_plugin) {
                // check if Plugin is active
                if($MJTC_count_for != $MJTC_plugin['internalid']) {
                    $MJTC_extr_clss = 'ms-plugin-notinstalled';
                    if ( is_plugin_active( $MJTC_plugin['path'] )) {
                        $MJTC_extr_clss = '';
                    } ?>
                    <div class="ms-plugins-imprt-datasec <?php echo esc_attr($MJTC_extr_clss);?>">
                        <div class="ms-import-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.5 11H19V7c0-1.1-.9-2-2-2h-4V3.5C13 2.12 11.88 1 10.5 1S8 2.12 8 3.5V5H4c-1.1 0-1.99.9-1.99 2v3.8H3.5c1.49 0 2.7 1.21 2.7 2.7s-1.21 2.7-2.7 2.7H2V20c0 1.1.9 2 2 2h3.8v-1.5c0-1.49 1.21-2.7 2.7-2.7 1.49 0 2.7 1.21 2.7 2.7V22H17c1.1 0 2-.9 2-2v-4h1.5c1.38 0 2.5-1.12 2.5-2.5S21.88 11 20.5 11z"></path></svg>
                        </div>
                        <span class="ms-plugins-imprt-data-plgnnme"><?php echo esc_html($MJTC_plugin['name']); ?></span>
                        <div class="ms-import-status">
                            <span class="ms-status-dot"></span> <?php echo esc_html(__('Plugin not installed','majestic-support')); ?>
                        </div>
                        <div class="ms-import-status installed">
                            <span class="ms-status-dot"></span><?php echo esc_html(__('Plugin Installed','majestic-support')); ?> 
                        </div>
                        <?php if($MJTC_extr_clss != ''){ ?>
                            <span class="ms-plugins-imprt-databtn disabled">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"></path></svg>
                                <?php echo esc_html(__('Start Import','majestic-support')); ?>
                            </span>
                        <?php }else{ ?>
                            <a class="ms-plugins-imprt-databtn" href="<?php echo esc_url_raw(admin_url("admin.php?page=majesticsupport_thirdpartyimport&mjslay=importdata&selected_plugin=".$MJTC_plugin['internalid'])); ?>" title="<?php echo esc_attr(__('Fetch Data','majestic-support')); ?>"><?php echo esc_html(__('Fetch Data','majestic-support')); ?></a>
                        <?php } ?>
                    </div>
                    <?php
                } else {
                    if(!empty($MJTC_entity_counts)){ ?>
                        <div class="ms-singleplugin-imprt-data-sec">
                            <span class="ms-singleplugin-imprt-datatitle">
                                <?php echo esc_html($MJTC_plugin['name']); ?>
                            </span>
                            <?php foreach ($MJTC_entity_counts as $MJTC_entity_name => $MJTC_entity_val) {
                                $MJTC_entity_name = ucwords(str_replace('_', ' ', $MJTC_entity_name));
                                if($MJTC_entity_name == 'Priority' && $MJTC_entity_val > 1){
                                    $MJTC_entity_name = 'Priorities';
                                }elseif($MJTC_entity_name == 'Status' && $MJTC_entity_val > 1){
                                    $MJTC_entity_name = 'Statuses';
                                }elseif($MJTC_entity_val > 1){
                                    $MJTC_entity_name = $MJTC_entity_name.'s';
                                }
                                $MJTC_extr_clss = '';
                                if (in_array(strtolower($MJTC_entity_name), ['agent', 'agent role', 'agents', 'agent roles'])) {
                                    if(!in_array('agent', majesticsupport::$_active_addons)){
                                        $MJTC_extr_clss = 'ms-singleplugin-imprt-data-addonnot-instllwrp';
                                    }
                                } elseif (in_array(strtolower($MJTC_entity_name), ['canned response', 'canned responses'])) {
                                    if(!in_array('cannedresponses', majesticsupport::$_active_addons)){
                                        $MJTC_extr_clss = 'ms-singleplugin-imprt-data-addonnot-instllwrp';
                                    }
                                } ?>
                                <div class="ms-singleplugin-imprt-datadisc <?php echo esc_attr($MJTC_extr_clss);?>">
                                    <?php echo esc_html($MJTC_entity_val).'&nbsp;'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_entity_name)).'&nbsp;'.esc_html(__('found','majestic-support'));

                                    if (in_array(strtolower($MJTC_entity_name), ['ticket', 'tickets'])) {
                                        if($MJTC_plugin["internalid"] != 3 && !in_array('privatecredentials', majesticsupport::$_active_addons)){ ?>
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
                                    if($MJTC_extr_clss != ''){ ?>
                                        <span class="ms-singleplugin-imprt-data-addonnot-instll">
                                            <img class="ms-plugins-imprterror-image" alt="<?php echo esc_attr(__('icon','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/imprt-icon.png" />
                                            <?php echo esc_html(__('Addon not installed please install addon first.','majestic-support')); ?>
                                        </span>
                                        <?php
                                    } ?>
                                </div>
                                <?php
                            } ?>
                            <div class="ms-singleplugin-imprt-databtn-wrp">
                                <a class="ms-singleplugin-imprt-databtn" title="<?php echo esc_attr(__('Import Data','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_thirdpartyimport&task=importPluginData&action=mstask&&selected_plugin='.$MJTC_plugin["internalid"], 'importPluginData'));?>"><?php echo esc_html(__('Import Data','majestic-support')); ?></a>
                            </div>
                        </div><?php
                    } else { ?>
                        <div class="ms-singleplugin-imprt-data-sec">
                            <span class="ms-singleplugin-imprt-datatitle">
                                <?php echo esc_html($MJTC_plugin['name']); ?>
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
