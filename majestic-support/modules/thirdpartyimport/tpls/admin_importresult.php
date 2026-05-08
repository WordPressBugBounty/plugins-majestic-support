<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// MJTC_message::MJTC_getMessage(); ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('importresult'); ?>
        <div id="msadmin-data-wrp">
            <?php
            $MJTC_results_array = get_option('mjtc_import_counts');
            $MJTC_plugin_label = 'SupportCandy';
            if(!empty(majesticsupport::$_data['import_for'])){
                $MJTC_import_for = majesticsupport::$_data['import_for'];
                if($MJTC_import_for == 1){
                    $MJTC_plugin_label = 'SupportCandy';
                } elseif($MJTC_import_for == 2){
                    $MJTC_plugin_label = 'AwesomeSupport';
                } elseif($MJTC_import_for == 3){
                    $MJTC_plugin_label = 'FluentSupport';
                }
            }
            if(!empty($MJTC_results_array)){ ?>
                <table class="ms-import-data-result-import-table" id="ms-import-data-result-table">
                    <thead>
                        <tr>
                            <th style="width:50%;"><?php echo  esc_html(__('Entity','majestic-support')); ?></th>
                            <th style="text-align: center;background-color: #006D3A;width:16.6%;"><?php echo  esc_html(__('Imported','majestic-support')); ?></th>
                            <th style="text-align: center;background-color: #A75424;width:16.6%;"><?php echo  esc_html(__('Similar Found','majestic-support')); ?></th>
                            <th style="text-align: center;background-color: #891518;width:16.6%;"><?php echo  esc_html(__('Not Imported','majestic-support')); ?></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($MJTC_results_array as $type => $MJTC_counts){
                            $MJTC_label = ucwords(str_replace(['_', 'jobtype', 'jobapply'], [' ', 'Job Type', 'Job Application'], $type));
                            $MJTC_imported = (int) $MJTC_counts['imported'];
                            $MJTC_skipped  = (int) $MJTC_counts['skipped'];
                            $MJTC_failed   = (int) $MJTC_counts['failed'];
                            if ($MJTC_imported > 0 || $MJTC_skipped > 0 || $MJTC_failed > 0) {
                                if($MJTC_label == 'Field') {
                                    $MJTC_show_message = 1;
                                }
                                if($MJTC_label == 'Priority') {
                                    $MJTC_label = 'Priorities';
                                }elseif($MJTC_label == 'Status') {
                                    $MJTC_label = 'Statuses';
                                }else{
                                    $MJTC_label = $MJTC_label.'s';
                                }
                                ?>
                                <tr>
                                    <td><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_label)); ?></td>

                                    <td class="ms-import-data-result-success">
                                        <?php echo esc_html( $MJTC_imported .' '. __('Imported','majestic-support') ); ?>
                                    </td>

                                    <td class="ms-import-data-result-similar">
                                        <?php echo esc_html( $MJTC_skipped .' '. __('Skipped','majestic-support') ); ?>
                                    </td>

                                    <td class="ms-import-data-result-failed">
                                        <?php echo esc_html( $MJTC_failed .' '. __('Failed','majestic-support') ); ?>
                                    </td>
                                </tr>
                                <?php 
                            }
                        } ?>
                    </tbody>
                </table>
                <?php 
                if(!empty($MJTC_show_message) && in_array('multiform', majesticsupport::$_active_addons)){ ?>
                    <div class="ms-import-data-addon-messagewrp">
                        <span class="ms-import-data-addon-message">
                            <?php echo  esc_html(__('Fields are only available in the default form.','majestic-support')); ?>
                        </span>
                    </div>
                    <?php 
                }
            } ?>
        </div>
    </div>
</div>
