<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper"><?php
if (majesticsupport::$_config['offline'] == 2) {
    if (majesticsupport::$_data['permission_granted'] == 1) {
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
            if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                if (majesticsupport::$_data['staff_enabled']) { ?>

    <?php
    $MJTC_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');
    $mjtc_scriptdateformat = MJTC_includer::MJTC_getModel('majesticsupport')->MJTC_getDateFormat();
    wp_enqueue_script('majesticsupport-google-charts', MJTC_PLUGIN_URL . 'includes/js/google-charts.js', array(), '1.0.0', true);
    wp_register_script( 'majesticsupport-google-charts-handle', false, array(), '1.0.0', true );
    wp_enqueue_script( 'majesticsupport-google-charts-handle' );
    $majesticsupport_js ="
        jQuery(document).ready(function($) {
            $('.custom_date').datepicker({
                dateFormat: '". esc_html($mjtc_scriptdateformat) ."'
            });
        });
        google.load('visualization', '1', {
            packages: ['corechart']
        });
        google.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', '". esc_html(__('Dates','majestic-support'))."');
            data.addColumn('number', '". esc_html(__('New','majestic-support'))."');
            data.addColumn('number', '". esc_html(__('Answered','majestic-support'))."');
            data.addColumn('number', '". esc_html(__('Pending','majestic-support'))."');
            data.addColumn('number', '". esc_html(__('Overdue','majestic-support'))."');
            data.addColumn('number', '". esc_html(__('Closed','majestic-support'))."');
            data.addRows([
                ". majesticsupport::$_data['line_chart_json_array']."
            ]);

            var options = {
                colors: ['#159667', '#2168A2', '#f39f10', '#B82B2B', '#3D355A'],
                curveType: 'function',
                legend: {
                    position: 'bottom'
                },
                pointSize: 6,
                // This line will make you select an entire row of data at a time
                focusTarget: 'category',
                chartArea: {
                    width: '90%',
                    top: 50
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
            chart.draw(data, options);
        }

    ";
    wp_add_inline_script('majesticsupport-google-charts-handle',$majesticsupport_js);
    $majesticsupport_js ="
        function resetFrom() {
            document.getElementById('ms-date-start').value = '';
            document.getElementById('ms-date-end').value = '';
            return true;
        }
    ";
    wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
    include_once(MJTC_PLUGIN_PATH . 'includes/header.php');
    ?>
    <div class="mjtc-support-top-sec-header">
        <img class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>"
            src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
        <div class="mjtc-support-top-sec-left-header">
            <div class="mjtc-support-main-heading">
                <?php echo esc_html(__("Agent Reports",'majestic-support')); ?>
            </div>
            <div class="mjtc-support-sub-heading"><?php echo esc_html(__("Monitor agent productivity with comprehensive reports on tickets handled, response times, and resolutions.",'majestic-support')); ?></div>
        </div>
    </div>
    <div class="mjtc-support-cont-main-wrapper">
        <div class="mjtc-support-cont-wrapper mjtc-support-cont-wrapper-color">
            <div class="mjtc-support-staff-report-wrapper">
                <div class="mjtc-support-top-search-wrp">
            <div class="mjtc-support-search-fields-wrp">
                <form class="mjtc-filter-form" name="majesticsupportform" id="majesticsupportform" method="POST" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'reports', 'mjslay'=>'staffreports')),"reports")); ?>">
                    <?php
                    $MJTC_curdate = date_i18n('Y-m-d');
                    $MJTC_enddate = date_i18n('Y-m-d', MJTC_majesticsupportphplib::MJTC_strtotime("now -1 month"));
                    $MJTC_date_start = !empty(majesticsupport::$_data['filter']['ms-date-start']) ? majesticsupport::$_data['filter']['ms-date-start'] : $MJTC_curdate;
                    $MJTC_date_end = !empty(majesticsupport::$_data['filter']['ms-date-end']) ? majesticsupport::$_data['filter']['ms-date-end'] : $MJTC_enddate;
                    ?>
                    <div class="mjtc-support-fields-wrp mjtc-support-staffreports-fields-overall-wrp">
                        <div class="mjtc-support-form-field">
                            <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-date-start', date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_start)), array('class' => 'custom_date mjtc-support-field-input','placeholder' => esc_html(__('Start Date','majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                        </div>
                        <div class="mjtc-support-form-field">
                            <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-date-end', date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_date_end)), array('class' => 'custom_date mjtc-support-field-input','placeholder' => esc_html(__('End Date','majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                        </div>
                    </div>
                    <div class="mjtc-support-search-form-btn-wrp">
                        <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-go', esc_html(__('Search', 'majestic-support')), array('class' => 'mjtc-search-button', 'onclick' => 'return addSpaces();')), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-reset', esc_html(__('Reset', 'majestic-support')), array('class' => 'mjtc-reset-button', 'onclick' => 'return resetFrom();')), MJTC_ALLOWED_TAGS); ?>

                    </div>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mjtcslay', 'staffreports'), MJTC_ALLOWED_TAGS); ?>
                </form>
            </div>
        </div>
        <div class="mjtc-support-downloads-wrp">
            <div class="mjtc-support-downloads-heading-wrp">
                <?php echo esc_html(__('Reports Statistics', 'majestic-support')) ?>
            </div>
            <div id="curve_chart" style="height:400px;width:100%; float: left;"></div>
            <div class="mjtc-admin-report-box-wrapper">
                <div class="mjtc-col-md-2 mjtc-admin-box box1">
                    <div class="mjtc-col-md-4 mjtc-admin-box-image">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div class="mjtc-col-md-8 mjtc-admin-box-content">
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-number">
                            <?php echo esc_html(majesticsupport::$_data['ticket_total']['openticket']); ?></div>
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-label">
                            <?php echo esc_html(__('New','majestic-support')); ?></div>
                    </div>
                    <div class="mjtc-col-md-12 mjtc-admin-box-label"></div>
                </div>
                <div class="mjtc-col-md-2 mjtc-admin-box jscol-half-offset box2">
                    <div class="mjtc-col-md-4 mjtc-admin-box-image">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </div>
                    <div class="mjtc-col-md-8 mjtc-admin-box-content">
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-number">
                            <?php echo esc_html(majesticsupport::$_data['ticket_total']['answeredticket']); ?></div>
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-label">
                            <?php echo esc_html(__('Answered','majestic-support')); ?></div>
                    </div>
                    <div class="mjtc-col-md-12 mjtc-admin-box-label"></div>
                </div>
                <div class="mjtc-col-md-2 mjtc-admin-box jscol-half-offset box3">
                    <div class="mjtc-col-md-4 mjtc-admin-box-image">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="mjtc-col-md-8 mjtc-admin-box-content">
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-number">
                            <?php echo esc_html(majesticsupport::$_data['ticket_total']['pendingticket']); ?></div>
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-label">
                            <?php echo esc_html(__('Pending','majestic-support')); ?></div>
                    </div>
                    <div class="mjtc-col-md-12 mjtc-admin-box-label"></div>
                </div>
                <?php if(in_array('overdue', majesticsupport::$_active_addons)){ ?>
                    <div class="mjtc-col-md-2 mjtc-admin-box jscol-half-offset box4">
                        <div class="mjtc-col-md-4 mjtc-admin-box-image">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </div>
                        <div class="mjtc-col-md-8 mjtc-admin-box-content">
                            <div class="mjtc-col-md-12 mjtc-admin-box-content-number">
                                <?php echo esc_html(majesticsupport::$_data['ticket_total']['overdueticket']); ?></div>
                            <div class="mjtc-col-md-12 mjtc-admin-box-content-label">
                                <?php echo esc_html(__('Overdue','majestic-support')); ?></div>
                        </div>
                        <div class="mjtc-col-md-12 mjtc-admin-box-label"></div>
                    </div>
                <?php } ?>
                <div class="mjtc-col-md-2 mjtc-admin-box jscol-half-offset box5">
                    <div class="mjtc-col-md-4 mjtc-admin-box-image">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="mjtc-col-md-8 mjtc-admin-box-content">
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-number">
                            <?php echo esc_html(majesticsupport::$_data['ticket_total']['closeticket']); ?></div>
                        <div class="mjtc-col-md-12 mjtc-admin-box-content-label">
                            <?php echo esc_html(__('Closed','majestic-support')); ?></div>
                    </div>
                    <div class="mjtc-col-md-12 mjtc-admin-box-label"></div>
                </div>
            </div>
        </div>
        <div class="mjtc-support-downloads-wrp">
            <div class="mjtc-support-downloads-heading-wrp">
                <?php echo esc_html(__('Agent Reports', 'majestic-support')) ?>
            </div>
            <?php
            if(!empty(majesticsupport::$_data['staffs_report'])){
                foreach(majesticsupport::$_data['staffs_report'] AS $MJTC_agent){ ?>
            <div class="mjtc-admin-staff-wrapper">
                <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'reports','mjslay'=>'staffdetailreport','ms-id'=>$MJTC_agent->id,'ms-date-start'=>majesticsupport::$_data['filter']['ms-date-start'],'ms-date-end'=>majesticsupport::$_data['filter']['ms-date-end']))); ?>"
                    class="mjtc-admin-staff-anchor-wrapper">
                    <div class="nopadding mjtc-festaffreport-img">
                        <div class="mjtc-report-staff-image-wrapper">
                            <?php
                            echo wp_kses_post(MJTC_get_avatar($MJTC_agent->uid, 'mjtc-report-staff-pic')); ?>
                        </div>
                        <div class="mjtc-report-staff-cnt-wrapper">
                            <div class="mjtc-report-staff-name">
                                <?php
                                        if($MJTC_agent->firstname && $MJTC_agent->lastname){
                                            $MJTC_agentname = $MJTC_agent->firstname . ' ' . $MJTC_agent->lastname;
                                        }else{
                                            $MJTC_agentname = $MJTC_agent->display_name;
                                        }
                                        echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_agentname));
                                    ?>
                            </div>
                            <div class="mjtc-report-staff-username">
                                <?php
                                        if($MJTC_agent->display_name){
                                            $MJTC_username = $MJTC_agent->display_name;
                                        }else{
                                            $MJTC_username = $MJTC_agent->user_nicename;
                                        }
                                        echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_username));
                                    ?>
                            </div>
                            <div class="mjtc-report-staff-email">
                                <?php
                                        if($MJTC_agent->email){
                                            $MJTC_email = $MJTC_agent->email;
                                        }else{
                                            $MJTC_email = $MJTC_agent->user_email;
                                        }
                                        echo esc_html($MJTC_email);
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="mjtc-festaffreport-data">
                        <div class="mjtc-col-md-2 mjtc-col-md-offset-1 mjtc-admin-report-box box1">
                            <span class="mjtc-report-box-number"><?php echo esc_html($MJTC_agent->openticket); ?></span>
                            <span class="mjtc-report-box-title"><?php echo esc_html(__('New','majestic-support')); ?></span>
                            <div class="mjtc-report-box-color"></div>
                        </div>
                        <div class="mjtc-col-md-2 mjtc-admin-report-box box2">
                            <span class="mjtc-report-box-number"><?php echo esc_html($MJTC_agent->answeredticket); ?></span>
                            <span class="mjtc-report-box-title"><?php echo esc_html(__('Answered','majestic-support')); ?></span>
                            <div class="mjtc-report-box-color"></div>
                        </div>
                        <div class="mjtc-col-md-2 mjtc-admin-report-box box3">
                            <span class="mjtc-report-box-number"><?php echo esc_html($MJTC_agent->pendingticket); ?></span>
                            <span class="mjtc-report-box-title"><?php echo esc_html(__('Pending','majestic-support')); ?></span>
                            <div class="mjtc-report-box-color"></div>
                        </div>
                        <?php if(in_array('overdue', majesticsupport::$_active_addons)){ ?>
                            <div class="mjtc-col-md-2 mjtc-admin-report-box box4">
                                <span class="mjtc-report-box-number"><?php echo esc_html($MJTC_agent->overdueticket); ?></span>
                                <span class="mjtc-report-box-title"><?php echo esc_html(__('Overdue','majestic-support')); ?></span>
                                <div class="mjtc-report-box-color"></div>
                            </div>
                        <?php } ?>
                        <div class="mjtc-col-md-2 mjtc-admin-report-box box5">
                            <span class="mjtc-report-box-number"><?php echo esc_html($MJTC_agent->closeticket); ?></span>
                            <span class="mjtc-report-box-title"><?php echo esc_html(__('Closed','majestic-support')); ?></span>
                            <div class="mjtc-report-box-color"></div>
                        </div>
                    </div>
                </a>
            </div>
            <?php
                }
                if (majesticsupport::$_data[1]) {
                    $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                }
            }
            ?>
            <?php
                } else {
                    MJTC_layout::MJTC_getStaffMemberDisable();
                }
            } else {
                MJTC_layout::MJTC_getNotStaffMember();
            }
        } else {
            $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'reports','mjslay'=>'staffreports'));
            $MJTC_redirect_url = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_redirect_url);
            MJTC_layout::MJTC_getUserGuest($MJTC_redirect_url);
        }
    } else { // User permission not granted
        MJTC_layout::MJTC_getPermissionNotGranted();
    }
} else {
    MJTC_layout::MJTC_getSystemOffline();
} ?>
        </div>
    </div>
</div>
</div>
</div>
