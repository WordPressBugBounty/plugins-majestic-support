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
            $results_array = get_option('mjtc_import_counts');
            $plugin_label = 'SupportCandy';
            if(!empty(majesticsupport::$_data['import_for'])){
                $import_for = majesticsupport::$_data['import_for'];
                if($import_for == 1){
                    $plugin_label = 'SupportCandy';
                } elseif($import_for == 2){
                    $plugin_label = 'AwesomeSupport';
                } elseif($import_for == 3){
                    $plugin_label = 'FluentSupport';
                }
            }
            if(!empty($results_array)){ ?>
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
                        foreach ($results_array as $type => $MJTC_counts){
                            $label = ucwords(str_replace(['_', 'jobtype', 'jobapply'], [' ', 'Job Type', 'Job Application'], $type));
                            $imported = (int) $MJTC_counts['imported'];
                            $skipped  = (int) $MJTC_counts['skipped'];
                            $failed   = (int) $MJTC_counts['failed'];
                            if ($imported > 0 || $skipped > 0 || $failed > 0) {
                                if($label == 'Field') {
                                    $show_message = 1;
                                }
                                if($label == 'Priority') {
                                    $label = 'Priorities';
                                }elseif($label == 'Status') {
                                    $label = 'Statuses';
                                }else{
                                    $label = $label.'s';
                                }
                                ?>
                                <tr>
                                    <td><?php echo esc_html(majesticsupport::MJTC_getVarValue($label)); ?></td>

                                    <td class="ms-import-data-result-success">
                                        <?php echo esc_html( $imported .' '. __('imported.','majestic-support') ); ?>
                                    </td>

                                    <td class="ms-import-data-result-similar">
                                        <?php echo esc_html( $skipped .' '. __('skipped.','majestic-support') ); ?>
                                    </td>

                                    <td class="ms-import-data-result-failed">
                                        <?php echo esc_html( $failed .' '. __('failed.','majestic-support') ); ?>
                                    </td>
                                </tr>
                                <?php 
                            }
                        } ?>
                    </tbody>
                </table>
                <?php 
                if(!empty($show_message) && in_array('multiform', majesticsupport::$_active_addons)){ ?>
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
