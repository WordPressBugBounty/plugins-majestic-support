<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
    <?php
if (majesticsupport::$_config['offline'] == 2) {
    if (majesticsupport::$_data['permission_granted'] == 1) {
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
            if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                if (majesticsupport::$_data['staff_enabled']) {
                    wp_enqueue_script('majesticsupport-google-charts', MJTC_PLUGIN_URL . 'includes/js/google-charts.js', array(), '1.0.0', true);
                    wp_register_script( 'majesticsupport-google-charts-handle', false, array(), '1.0.0', true );
                    wp_enqueue_script( 'majesticsupport-google-charts-handle' );
                    $majesticsupport_js ='';
                    $chart_data = rtrim(majesticsupport::$_data['pie3d_chart1'], ',');
                    if(!empty($chart_data)){ 
                        $majesticsupport_js = "
                        // Use the modern loader
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawPie3d1Chart);";
                    }
                    $majesticsupport_js .="
                        function drawPie3d1Chart() {
                            // Ensure the data array is valid
                            var data = google.visualization.arrayToDataTable([
                                ['" . esc_js(__('Departments','majestic-support')) . "', '" . esc_js(__('Tickets By Departments','majestic-support')) . "'],
                                " . $chart_data . "
                            ]);

                            var options = {
                                title: '" . esc_js(__('Tickets By Departments','majestic-support')) . "',
                                chartArea: {
                                    left: '10%',
                                    top: '10%',
                                    width: '80%',
                                    height: '80%'
                                },
                                pieHole: 0.4,
                                is3D: true // Added this since your variable suggests a 3D chart
                            };

                            var chartElement = document.getElementById('pie3d_chart1');
                            if (chartElement) {
                                var chart = new google.visualization.PieChart(chartElement);
                                chart.draw(data, options);
                            }
                        }
                    ";
    wp_add_inline_script('majesticsupport-google-charts-handle',$majesticsupport_js);
    include_once(MJTC_PLUGIN_PATH . 'includes/header.php'); ?>
    <div class="mjtc-support-top-sec-header">
        <img class="mjtc-transparent-header-img1" alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>"
            src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
        <div class="mjtc-support-top-sec-left-header">
            <div class="mjtc-support-main-heading">
                <?php echo esc_html(__("Department Reports",'majestic-support')); ?>
            </div>
            <div class="mjtc-support-sub-heading"><?php echo esc_html(__("Track and analyze performance metrics for each support department to optimize team efficiency.",'majestic-support')); ?></div>
        </div>
    </div>
    <div class="mjtc-support-cont-main-wrapper">
        <div class="mjtc-support-cont-wrapper mjtc-support-cont-wrapper-color">
            <div class="mjtc-support-downloads-wrp">
                <div class="mjtc-support-downloads-heading-wrp">
                    <?php echo esc_html(__('Department Reports', 'majestic-support')) ?>
                </div>
                <?php if(!empty(majesticsupport::$_data['departments_report'])){
                    if(!empty($chart_data)){ ?>
                        <div class="mjtc-col-md-12 mjtc-support-download-content-wrp-mtop">
                            <div id="pie3d_chart1" style="height:400px;width:100%; float: left;">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="mjtc-support-downloads-wrp">
                        <div class="mjtc-support-downloads-heading-wrp">
                            <?php echo esc_html(__('Ticket Status By Departments', 'majestic-support')) ?>
                        </div>
                        <?php foreach(majesticsupport::$_data['departments_report'] AS $MJTC_department){ ?>
                            <div class="mjtc-admin-staff-wrapper mjtc-departmentlist">
                                <div class="mjtc-col-md-4 nopadding mjtc-festaffreport-img">
                                    <div class="mjtc-col-md-12 msposition-reletive">
                                        <div class="departmentname">
                                            <?php
                                                echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_department->departmentname));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mjtc-col-md-8 nopadding mjtc-festaffreport-data">
                                    <div class="mjtc-col-md-2 mjtc-col-md-offset-1 mjtc-admin-report-box box1">
                                        <span
                                            class="mjtc-report-box-number"><?php echo esc_html($MJTC_department->openticket); ?></span>
                                        <span class="mjtc-report-box-title"><?php echo esc_html(__('New','majestic-support')); ?></span>
                                        <div class="mjtc-report-box-color"></div>
                                    </div>
                                    <div class="mjtc-col-md-2 mjtc-admin-report-box box2">
                                        <span
                                            class="mjtc-report-box-number"><?php echo esc_html($MJTC_department->answeredticket); ?></span>
                                        <span
                                            class="mjtc-report-box-title"><?php echo esc_html(__('Answered','majestic-support')); ?></span>
                                        <div class="mjtc-report-box-color"></div>
                                    </div>
                                    <div class="mjtc-col-md-2 mjtc-admin-report-box box3">
                                        <span
                                            class="mjtc-report-box-number"><?php echo esc_html($MJTC_department->pendingticket); ?></span>
                                        <span
                                            class="mjtc-report-box-title"><?php echo esc_html(__('Pending','majestic-support')); ?></span>
                                        <div class="mjtc-report-box-color"></div>
                                    </div>
                                    <?php if(in_array('overdue', majesticsupport::$_active_addons)){ ?>
                                        <div class="mjtc-col-md-2 mjtc-admin-report-box box4">
                                            <span class="mjtc-report-box-number">
                                                <?php echo esc_html($MJTC_department->overdueticket); ?>
                                            </span>
                                            <span class="mjtc-report-box-title">
                                                <?php echo esc_html(__('Overdue','majestic-support')); ?>
                                            </span>
                                            <div class="mjtc-report-box-color"></div>
                                        </div>
                                    <?php } ?>
                                    <div class="mjtc-col-md-2 mjtc-admin-report-box box5">
                                        <span
                                            class="mjtc-report-box-number"><?php echo esc_html($MJTC_department->closeticket); ?></span>
                                        <span
                                            class="mjtc-report-box-title"><?php echo esc_html(__('Closed','majestic-support')); ?></span>
                                        <div class="mjtc-report-box-color"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                    if (majesticsupport::$_data[1]) {
                        $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                        echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                    }?>

            </div>
            <?php
                        }else{
                             MJTC_layout::MJTC_getNoRecordFound();
                            }
                        }
                 else {
                    MJTC_layout::MJTC_getStaffMemberDisable();
                }
            } else {
                MJTC_layout::MJTC_getNotStaffMember();
            }
        } else {
            $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'reports','mjslay'=>'departmentreports'));
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
