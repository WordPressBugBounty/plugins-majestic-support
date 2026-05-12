<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wp_enqueue_script('majesticsupport-notify-app', MJTC_PLUGIN_URL . 'includes/js/firebase-app.js', array(), '1.0.0', true);
wp_enqueue_script('majesticsupport-notify-message', MJTC_PLUGIN_URL . 'includes/js/firebase-messaging.js', array(), '1.0.0', true);
wp_enqueue_script('majesticsupport-google-charts', MJTC_PLUGIN_URL . 'includes/js/google-charts.js', array(), '1.0.0', true);

wp_enqueue_style('majesticsupport-status-graph', MJTC_PLUGIN_URL . 'includes/css/status_graph.css', array(), '1.0.0');
do_action('MJTC_ticket-notify-generate-token');
MJTC_message::MJTC_getMessage();
?>
<?php
$majesticsupport_js ="
    google.load('visualization', '1', {packages: ['corechart']});
    google.setOnLoadCallback(drawStackChartHorizontal);
    google.setOnLoadCallback(drawTodayTicketsChart);

    function drawStackChartHorizontal() {
      var data = google.visualization.arrayToDataTable([
        ".
            wp_kses(majesticsupport::$_data['stack_chart_horizontal']['title'], MJTC_ALLOWED_TAGS).",".
            wp_kses(majesticsupport::$_data['stack_chart_horizontal']['data'], MJTC_ALLOWED_TAGS) ."
        ]);

        var view = new google.visualization.DataView(data);

        var options = {
            height: 300,
            chartArea: {
                width: '80%'
            },
            legend: {
                position: 'top',
            },
            curveType: 'function',
            colors: ['#B82B2B', '#621166', '#2168A2', '#159667'],
        };
        var chart = new google.visualization.AreaChart(document.getElementById('stack_chart_horizontal'));
        chart.draw(view, options);
    }

    function drawTodayTicketsChart() {
        var data = google.visualization.arrayToDataTable([
            ".
                wp_kses(majesticsupport::$_data['today_ticket_chart']['title'], MJTC_ALLOWED_TAGS).",".
                wp_kses(majesticsupport::$_data['today_ticket_chart']['data'], MJTC_ALLOWED_TAGS)."
            
        ]);

        var view = new google.visualization.DataView(data);

        var options = {
            height: 300,
            chartArea: {
                width: '70%',
                left: 30
            },
            legend: {
                position: 'right'
            },
            hAxis: {
                textPosition: 'none'
            },
            colors: ". wp_kses(majesticsupport::$_data['stack_chart_horizontal']['colors'], MJTC_ALLOWED_TAGS).",
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('today_ticket_chart'));
        chart.draw(view, options);
    }

";
//custom handle use because of this add script after chart library include
wp_register_script( 'majesticsupport-inlinescript-handle', false, array(), '1.0.0', true );
wp_enqueue_script( 'majesticsupport-inlinescript-handle' );

wp_add_inline_script('majesticsupport-inlinescript-handle',$majesticsupport_js);
$MJTC_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1);
?>  
<div id="msadmin-wrapper">        
    <!-- CUSTOMIZE MODAL (Moved inside wrapper for scoping) -->
    <div id="customizeModal" class="mjtc-admin-modal-overlay">
        <div class="mjtc-admin-modal-content">
            <div class="mjtc-admin-modal-header">
                <h3 class="mjtc-admin-font-bold" style="margin: 0; font-size: 1.5rem; color: var(--mjtc-admin-text-main);">
                    <?php echo esc_html(__('Customize Layout', 'majestic-support')); ?>
                </h3>
            </div>
            <div class="mjtc-admin-modal-body" id="modal-sortable-list">
                <?php
                    $MJTC_default_settings = [];

                    // 1. Stats Overview (Always first)
                    $MJTC_default_settings['sec-stats'] = [
                        'label' => __('Stats Overview', 'majestic-support'),
                        'status' => 1
                    ];

                    // 2. Welcome Banner (Conditional)
                    if (majesticsupport::$_data['update_avaliable_for_addons'] != 0) {
                        $MJTC_default_settings['sec-banner'] = [
                            'label' => __('Welcome Banner', 'majestic-support'),
                            'status' => 1
                        ];
                    }

                    // 3. Overdue Tickets (Conditional)
                    if (in_array('overdue', majesticsupport::$_active_addons)) {
                        $MJTC_default_settings['sec-overdue'] = [
                            'label' => __('Overdue Tickets', 'majestic-support'),
                            'status' => 1
                        ];
                    }

                    // 4. Standard Sections (Maintaining your specific order)
                    $MJTC_default_settings['sec-priority']    = ['label' => __('Unassigned & Quick Actions', 'majestic-support'), 'status' => 1];
                    $MJTC_default_settings['sec-recent']      = ['label' => __('Recent Tickets List', 'majestic-support'), 'status' => 1];
                    $MJTC_default_settings['sec-volume']      = ['label' => __('Ticket Volume & Status', 'majestic-support'), 'status' => 1];
                    $MJTC_default_settings['sec-analytics']   = ['label' => __('Analytics Breakdown', 'majestic-support'), 'status' => 1];
                    $MJTC_default_settings['sec-activity']    = ['label' => __('Activity & Premade Responses', 'majestic-support'), 'status' => 1];
                    $MJTC_default_settings['sec-vip']         = ['label' => __('VIP & Feedback', 'majestic-support'), 'status' => 1];
                    $MJTC_default_settings['sec-performance'] = ['label' => __('Performance & Timers', 'majestic-support'), 'status' => 1];
                    $MJTC_default_settings['sec-addons']      = ['label' => __('Recommended Addons', 'majestic-support'), 'status' => 1];

                    // 5. Import Data (Conditional - placed before the help guide)
                    if (
                        is_plugin_active('awesome-support/awesome-support.php') ||
                        is_plugin_active('supportcandy/supportcandy.php') ||
                        is_plugin_active('fluent-support/fluent-support.php')
                    ) {
                        $MJTC_default_settings['sec-import'] = [
                            'label' => __('Import Data', 'majestic-support'),
                            'status' => 1
                        ];
                    }

                    // 6. KB & Help (Always last)
                    $MJTC_default_settings['sec-kb-help'] = [
                        'label' => __('KB & Help Guide', 'majestic-support'),
                        'status' => 1
                    ];

                // 2. Get saved settings from database
                // The format saved is: ['sec-id' => 1, 'sec-id' => 0]
                $MJTC_saved_layout = get_option('mjtc_dashboard_layout');

                // 3. Determine the Loop Array
                // If we have saved data, use the saved order. 
                // If not, use the default settings order.
                if ( ! empty( $MJTC_saved_layout ) && is_array( $MJTC_saved_layout ) ) {
                    $MJTC_render_order = $MJTC_saved_layout;
                } else {
                    // Transform default settings into the simple ID => Status format for the loop
                    $MJTC_render_order = array_map(function($MJTC_item) { return $MJTC_item['status']; }, $MJTC_default_settings);
                }

                // 4. Render Rows
                foreach ($MJTC_render_order as $MJTC_section_id => $MJTC_is_visible) :
                    // Skip if for some reason the ID doesn't exist in our labels master list
                    if (!isset($MJTC_default_settings[$MJTC_section_id])) continue;

                    $MJTC_label = $MJTC_default_settings[$MJTC_section_id]['label'];
                    $MJTC_checked = ($MJTC_is_visible == 1) ? 'checked' : '';
                ?>
                    <div class="mjtc-admin-switch-row" draggable="true" data-target="<?php echo esc_attr($MJTC_section_id); ?>">
                        <div class="mjtc-modal-row-content">
                            <span class="mjtc-admin-drag-handle">☰</span>
                            <span class="mjtc-modal-item-name"><?php echo esc_html($MJTC_label); ?></span>
                        </div>
                        <label class="mjtc-admin-switch">
                            <input type="checkbox" <?php echo esc_attr($MJTC_checked); ?> data-target="<?php echo esc_attr($MJTC_section_id); ?>">
                            <span class="mjtc-admin-slider"></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mjtc-admin-modal-footer">
                <button class="mjtc-admin-btn mjtc-admin-btn-light" id="resetDefaults"><?php echo esc_html(__('Reset Default', 'majestic-support')); ?></button>
                <button class="mjtc-admin-btn mjtc-admin-btn-primary" id="saveCustomization"><?php echo esc_html(__('Done', 'majestic-support')); ?></button>
            </div>
        </div>
    </div>
    <div id="msadmin-leftmenu">
        <?php MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>

    <div id="msadmin-data">
        <!-- TOPBAR (Old ID: #msadmin-wrapper-top) -->
        <header id="msadmin-wrapper-top">
            <div class="msadmin-head-top-toogle-wrp">
                <button class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-icon" id="desktop-menu-toggle" style="flex-shrink: 0;margin-right:1rem;" title="Toggle Sidebar">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg>
                </button>
                <div class="mjtc-page-header">
                    <div>
                        <h1 id="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--mjtc-admin-text-main); margin: 0;">
                            <?php echo esc_html(__('Dashboard', 'majestic-support')); ?>
                        </h1>
                        <p style="font-size: 0.95rem; color: var(--mjtc-admin-text-secondary); margin: 4px 0 0;">
                            <?php echo esc_html(__("Monitor tickets, track agent activity, and manage your support system from one place.", 'majestic-support')); ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mjtc-topbar-actions">
                <button class="mjtc-admin-btn mjtc-admin-btn-light" id="btn-customize">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M3 17v2h6v-2H3zM3 5v2h10V5H3zm10 16v-2h8v-2h-8v-2h-2v6h2zM7 9v2H3v2h4v2h2V9H7zm14 4v-2H11v2h10zm-6-4h2V7h4V5h-4V3h-2v6z"/></svg>
                    <?php echo esc_html(__('Design', 'majestic-support')); ?>
                </button>
                <?php 
                    $MJTC_id='';
                    $MJTC_href="?page=majesticsupport_ticket&mjslay=addticket&formid=".esc_attr(MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId());
                    if(in_array('multiform', majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                        $MJTC_id="id=multiformpopup";
                        $MJTC_href='#';
                    }
                ?>
                <a href="<?php echo esc_url($MJTC_href); ?>" class="mjtc-admin-btn mjtc-admin-btn-primary" <?php echo esc_attr($MJTC_id); ?>>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="white"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    <?php echo esc_html(__('Create Ticket', 'majestic-support')); ?>
                </a>
            </div>
        </header>

        <div class="mjtc-admin-content-scroll">
            <div id="msadmin-data-wrp">
                <?php
                // START OF DYNAMIC RENDERING LOOP
                foreach ($MJTC_render_order as $MJTC_section_id => $MJTC_is_visible) :
                    $MJTC_visibility_style = ($MJTC_is_visible == 1) ? '' : 'display:none;';
                    $MJTC_visibility_class = ($MJTC_is_visible == 1) ? 'jsst-visible' : 'jsst-hidden';

                    // Use a Switch to determine which block of code to render
                    switch ($MJTC_section_id) :

                        case 'sec-stats': ?>
                            <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-stats" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <div class="mjtc-col-span-8">
                                    <div class="mjtc-hero-card">
                                        <h2 class="mjtc-hero-title">
                                            <?php echo esc_html(__('Welcome Back, Admin', 'majestic-support')); ?>
                                        </h2>
                                        <p class="mjtc-hero-subtitle">
                                            <?php
                                            $unassigned_tickets = count(majesticsupport::$_data['unassigned_tickets']);
                                            if($unassigned_tickets > 0){
                                                echo esc_html(__('All system metrics are performing optimally. You have', 'majestic-support')) .' '. esc_html($unassigned_tickets) . ' ' . esc_html(__('waiting in the queue—assign them promptly to maintain efficient support operations.', 'majestic-support'));
                                            } else {
                                                echo esc_html(__('System metrics are healthy. All tickets are currently assigned and being handled efficiently.', 'majestic-support'));
                                            }
                                            ?>
                                        </p>
                                        <div style="display:flex; gap:12px; position:relative; z-index:2;">
                                            <a href="?page=majesticsupport_ticket" class="mjtc-admin-btn mjtc-admin-btn-light" style="color:var(--mjtc-admin-primary);">
                                                <?php echo esc_html(__('Open Tickets', 'majestic-support')); ?>
                                            </a>
                                            <a href="?page=majesticsupport_reports&mjslay=overallreport" class="mjtc-admin-btn mjtc-admin-btn-hero">
                                                <?php echo esc_html(__('Overall Statistics', 'majestic-support')); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mjtc-col-span-4 mjtc-stats-2x2">
                                    <a class="mjtc-stat-cube mjtc-cube-blue mjtc-support-stats-link" href="?page=majesticsupport_ticket" data-tab-number="1">
                                        <div class="mjtc-stat-cube-icon">
                                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        </div>
                                        <div>
                                            <div class="mjtc-stat-cube-val">
                                                <?php echo esc_html(majesticsupport::$_data['ticket_total']['openticket']); ?>
                                            </div>
                                            <div class="mjtc-stat-cube-label">
                                                <?php echo esc_html(__('New Tickets', 'majestic-support')); ?>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="mjtc-stat-cube mjtc-cube-amber mjtc-support-stats-link" href="?page=majesticsupport_ticket" data-tab-number="1">
                                        <div class="mjtc-stat-cube-icon">
                                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                        </div>
                                        <div>
                                            <div class="mjtc-stat-cube-val">
                                                <?php echo esc_html(majesticsupport::$_data['ticket_total']['pendingticket']); ?>
                                            </div>
                                            <div class="mjtc-stat-cube-label">
                                                <?php echo esc_html(__('Pending Tickets', 'majestic-support')); ?>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="mjtc-stat-cube mjtc-cube-emerald mjtc-support-stats-link" href="?page=majesticsupport_ticket" data-tab-number="2">
                                        <div class="mjtc-stat-cube-icon">
                                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                        </div>
                                        <div>
                                            <div class="mjtc-stat-cube-val">
                                                <?php echo esc_html(majesticsupport::$_data['ticket_total']['answeredticket']); ?>
                                            </div>
                                            <div class="mjtc-stat-cube-label">
                                                <?php echo esc_html(__('Answered Tickets', 'majestic-support')); ?>
                                            </div>
                                        </div>
                                    </a>
                                    <?php if(in_array('overdue', majesticsupport::$_active_addons)){ ?>
                                        <a class="mjtc-stat-cube mjtc-cube-rose mjtc-support-stats-link" href="?page=majesticsupport_ticket" data-tab-number="3">
                                            <div class="mjtc-stat-cube-icon">
                                                <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                                            </div>
                                            <div>
                                                <div class="mjtc-stat-cube-val">
                                                    <?php echo esc_html(majesticsupport::$_data['ticket_total']['overdueticket']); ?>
                                                </div>
                                                <div class="mjtc-stat-cube-label">
                                                    <?php echo esc_html(__('Overdue Tickets', 'majestic-support')); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } else { ?>
                                        <a class="mjtc-stat-cube mjtc-cube-rose mjtc-support-stats-link" href="?page=majesticsupport_ticket" data-tab-number="5">
                                            <div class="mjtc-stat-cube-icon">
                                                <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                                            </div>
                                            <div>
                                                <div class="mjtc-stat-cube-val">
                                                    <?php echo esc_html(majesticsupport::$_data['ticket_total']['allticket']); ?>
                                                </div>
                                                <div class="mjtc-stat-cube-label">
                                                    <?php echo esc_html(__('All Tickets', 'majestic-support')); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-banner':
                            if (majesticsupport::$_data['update_avaliable_for_addons'] != 0) : ?>
                                <div class="mjtc-admin-welcome-banner <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-banner" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                    <div style="position: relative; z-index: 10;">
                                        <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0;">🚀 <?php echo esc_html(__("Addone update is Live!",'majestic-support')); ?></h2>
                                        <p style="opacity: 0.8; margin: 0.75rem 0 2rem; font-weight: 500;"><?php echo esc_html(__("Experience AI-powered responses and smoother analytics.",'majestic-support')); ?></p>
                                        <a href="?page=majesticsupport_premiumplugin&mjslay=addonstatus" class="mjtc-admin-btn mjtc-admin-btn-primary"><?php echo esc_html(__("View Addone Status",'majestic-support')); ?></a>
                                    </div>
                                    <div style="position: relative; z-index: 10; text-align: right;">
                                        <div style="font-size: 4rem; font-weight: 900; color: var(--mjtc-admin-primary); opacity: 0.1;"><?php echo esc_html(__("plugin",'majestic-support')).' v '.esc_html(majesticsupport::$_config['versioncode']); ?></div>
                                    </div>
                                </div>
                            <?php endif;
                            break;

                        case 'sec-overdue':
                            if(in_array('overdue', majesticsupport::$_active_addons)){ ?>
                                <div class="mjtc-cp-cnt-sec <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-overdue" style="margin-bottom: 1rem; border: 1px solid var(--mjtc-admin-accent-coral); <?php echo esc_attr($MJTC_visibility_style); ?>">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt" style="color: var(--mjtc-admin-accent-coral);"><?php echo esc_html(__('Overdue Tickets', 'majestic-support')); ?> ⚠️</h3>
                                        <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm mjtc-support-stats-link" href="?page=majesticsupport_ticket" data-tab-number="3">
                                            <?php echo esc_html(__('View All', 'majestic-support')); ?>
                                        </a>
                                    </div>
                                    <div>
                                        <?php if(count(majesticsupport::$_data['overdue_tickets']) > 0){
                                            foreach (majesticsupport::$_data['overdue_tickets'] AS $MJTC_overdue_ticket) { ?>
                                                <div class="mjtc-cp-tkt-list" style="background: #fff1f2;">
                                                    <div class="mjtc-tkt-row-left">
                                                        <div style="width: 40px; height: 40px; background: #ffe4e6; color: #be123c; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">!</div>
                                                        <div>
                                                            <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_overdue_ticket->id); ?>">
                                                                <?php
                                                                if (isset($MJTC_field_array['subject'])) {
                                                                    echo esc_html($MJTC_overdue_ticket->subject);
                                                                } ?>
                                                            </a>
                                                            <div class="mjtc-tkt-sub">
                                                                <?php echo esc_html(__("Ticket",'majestic-support')).' '.esc_html(majesticsupport::MJTC_getVarValue($MJTC_overdue_ticket->ticketid)); ?>
                                                                <?php
                                                                if (isset($MJTC_field_array['department']) && !empty($MJTC_overdue_ticket->departmentname)) { ?>
                                                                    <span title="<?php echo esc_attr(majesticsupport::MJTC_getVarValue($MJTC_field_array['department'])). " : "; ?>">
                                                                        <?php echo ' • '.esc_html(majesticsupport::MJTC_getVarValue($MJTC_overdue_ticket->departmentname)); ?>
                                                                    </span>
                                                                    <?php
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-tkt-row-right">
                                                        <span class="mjtc-admin-pill mjtc-admin-pill-rose" style="background-color: <?php echo esc_attr($MJTC_overdue_ticket->statusbgcolour)?>;color: <?php echo esc_attr($MJTC_overdue_ticket->statuscolour)?>;">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_overdue_ticket->statustitle)); ?>
                                                        </span>
                                                        <span class="mjtc-tkt-meta-bold">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_overdue_ticket->priority)); ?>
                                                        </span>
                                                        <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_overdue_ticket->id); ?>">
                                                            <?php echo esc_html(__("View Details",'majestic-support')); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } else { ?>
                                            <div class="ms_no_record">
                                                <?php echo esc_html(__("No Record Found",'majestic-support')); ?>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                                <?php
                            }
                            break;

                        case 'sec-priority': ?>
                            <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-priority" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <!-- Unassigned Tickets -->
                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Unassigned Tickets",'majestic-support')); ?>
                                            <?php 
                                            if (!empty(majesticsupport::$_data['unassigned_urgent'])) { ?>
                                                <span class="mjtc-admin-pill" style="background-color: <?php echo esc_attr(majesticsupport::$_data['unassigned_urgent']->prioritycolour); ?>; margin-left: 10px;color: #fff;">
                                                    <?php echo esc_html(majesticsupport::$_data['unassigned_urgent']->total_count).' '.esc_html(majesticsupport::MJTC_getVarValue(majesticsupport::$_data['unassigned_urgent']->priority)); ?>
                                                </span>
                                                <?php
                                            } ?>
                                        </h3>
                                    </div>
                                    <?php if( in_array('agent', majesticsupport::$_active_addons) ){ ?>
                                        <div>
                                            <?php if(count(majesticsupport::$_data['unassigned_tickets']) > 0){
                                                foreach (majesticsupport::$_data['unassigned_tickets'] AS $MJTC_unassigned_ticket) { ?>
                                                    <div class="mjtc-cp-tkt-list">
                                                        <div>
                                                            <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_unassigned_ticket->id); ?>">
                                                                <?php
                                                                if (isset($MJTC_field_array['subject'])) {
                                                                    echo esc_html($MJTC_unassigned_ticket->subject);
                                                                } ?>
                                                            </a>
                                                            <div class="mjtc-tkt-meta-bold" style="color: <?php echo esc_attr($MJTC_unassigned_ticket->prioritycolour); ?>">
                                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_unassigned_ticket->priority)); ?>
                                                            </div>
                                                        </div>
                                                        <a href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_unassigned_ticket->id); ?>#asgn-staff" class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm">
                                                            <?php echo esc_html(__("Assign",'majestic-support')); ?>
                                                        </a>
                                                    </div>
                                                    <?php
                                                }
                                            } else { ?>
                                                <div class="mjtc-cp-add-smart-reply">
                                                    <div class="mjtc-cp-add-smart-header">
                                                        <div class="mjtc-addon-icon">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                              <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-body">
                                                        <p>
                                                            <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-footer">
                                                        <a href="<?php echo esc_url($MJTC_href); ?>" class="mjtc-admin-menu-link" <?php echo esc_attr($MJTC_id); ?>>
                                                            <?php echo esc_html(__("Add Ticket",'majestic-support')); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="mjtc-cp-add-smart-reply">
                                            <div class="mjtc-cp-add-smart-header">
                                                <div class="mjtc-addon-icon">
                                                    <svg viewBox="0 0 24 24" fill="none">
                                                      <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="mjtc-cp-add-smart-body">
                                                <p>
                                                    <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                </p>
                                            </div>
                                            <div class="mjtc-cp-add-smart-footer">
                                                <a title="<?php echo esc_attr(__('Install Add-on','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://www.majesticsupport.com/product/agents/">
                                                    <?php echo esc_html(__("Install Add-on",'majestic-support')); ?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <!-- Quick Actions -->
                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Quick Actions",'majestic-support')); ?>
                                        </h3>
                                    </div>
                                    <div class="mjtc-admin-qa-grid">
                                        <a title="<?php echo esc_attr(__('Smart Replies', 'majestic-support')); ?>" class="mjtc-admin-qa-btn" href="?page=majesticsupport_smartreply">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
                                            <span class="mjtc-qa-text">
                                                <?php echo esc_html(__('Smart Replies', 'majestic-support')); ?>
                                            </span>
                                        </a>
                                        <?php if( in_array('multiform', majesticsupport::$_active_addons) ){ ?>
                                            <a title="<?php echo esc_attr(__('Multiforms','majestic-support')); ?>" class="mjtc-admin-qa-btn" href="?page=majesticsupport_multiform">
                                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
                                                <span class="mjtc-qa-text">
                                                    <?php echo esc_html(__('Multiforms', 'majestic-support')); ?>
                                                </span>
                                            </a>
                                        <?php } else { ?>
                                            <a title="<?php echo esc_attr(__('Field Ordering','majestic-support')); ?>" class="mjtc-admin-qa-btn" href="?page=majesticsupport_fieldordering&fieldfor=1">
                                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                                <span class="mjtc-qa-text">
                                                    <?php echo esc_html(__('Fields', 'majestic-support')); ?>
                                                </span>
                                            </a>
                                        <?php } ?>
                                        <a title="<?php echo esc_attr(__('Settings','majestic-support')); ?>" class="mjtc-admin-qa-btn" href="?page=majesticsupport_configuration&msconfigid=general">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                            <span class="mjtc-qa-text">
                                                <?php echo esc_html(__('Settings', 'majestic-support')); ?>
                                            </span>
                                        </a>
                                        <a title="<?php echo esc_attr(__('add missing users','majestic-support')); ?>" class="mjtc-admin-qa-btn" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_majesticsupport&task=addmissingusers&action=mstask','add-missing-users'));?>">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                                            <span class="mjtc-qa-text">
                                                <?php echo esc_html(__('Sync WP Users', 'majestic-support')); ?>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-recent': ?>
                            <div class="mjtc-cp-cnt-sec <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-recent" style="margin-bottom: 1rem; <?php echo esc_attr($MJTC_visibility_style); ?>">
                                <div class="mjtc-cp-cnt-title mjtc-recent-header" style="align-items: flex-start !important;">
                                    <div class="mjtc-recent-title-row">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Recent Tickets",'majestic-support')); ?>
                                        </h3>
                                        <a href="?page=majesticsupport_ticket" class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm">
                                            <?php echo esc_html(__("View All",'majestic-support')); ?>
                                        </a>
                                    </div>
                                    <div class="mjtc-admin-tab-container">
                                        <button class="mjtc-admin-tab-btn active" data-tab="all">
                                            <?php echo esc_html(__("All",'majestic-support')); ?>
                                        </button>
                                        <button class="mjtc-admin-tab-btn" data-tab="open">
                                            <?php echo esc_html(__("Open",'majestic-support')); ?>
                                        </button>
                                        <button class="mjtc-admin-tab-btn" data-tab="pending">
                                            <?php echo esc_html(__("Pending",'majestic-support')); ?>
                                        </button>
                                        <button class="mjtc-admin-tab-btn" data-tab="answered">
                                            <?php echo esc_html(__("Answered",'majestic-support')); ?>
                                        </button>
                                        <button class="mjtc-admin-tab-btn" data-tab="closed">
                                            <?php echo esc_html(__("Closed",'majestic-support')); ?>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- ALL Tab Content -->
                                <div id="tab-content-all">
                                    <?php
                                    if(count(majesticsupport::$_data['recent_all_tickets']) > 0){
                                        foreach (majesticsupport::$_data['recent_all_tickets'] AS $MJTC_ticket) { ?>
                                            <div class="mjtc-cp-tkt-list">
                                                <div class="mjtc-tkt-row-left">
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid, 'mjtc-admin-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                    <div>
                                                        <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                            <?php
                                                            if (isset($MJTC_field_array['subject'])) { ?>
                                                                <?php echo esc_html($MJTC_ticket->subject); ?>
                                                                <?php
                                                            } ?>
                                                        </a>
                                                        <div class="mjtc-tkt-sub">
                                                            <?php echo esc_html(__("Ticket",'majestic-support')).' '.esc_html($MJTC_ticket->ticketid); ?>
                                                            <?php
                                                            if (isset($MJTC_field_array['fullname'])) {
                                                                echo ' • '.esc_html($MJTC_ticket->name);
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mjtc-tkt-row-right">
                                                    <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                    </span>
                                                    <?php
                                                    if (isset($MJTC_field_array['priority'])) { ?>
                                                        <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;color: #fff;">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                        </span>
                                                        <?php
                                                    } ?>
                                                    <span class="mjtc-tkt-time" title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>">
                                                        <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support')); ?>
                                                    </span>
                                                    <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" title="<?php echo esc_attr(__('Reply','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>#reply-container">
                                                        <?php echo esc_html(__("Reply",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else { ?>
                                        <div class="mjtc-cp-add-smart-reply">
                                            <div class="mjtc-cp-add-smart-header">
                                                <div class="mjtc-addon-icon">
                                                    <svg viewBox="0 0 24 24" fill="none">
                                                      <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="mjtc-cp-add-smart-body">
                                                <p>
                                                    <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                </p>
                                            </div>
                                            <div class="mjtc-cp-add-smart-footer">
                                                <a href="<?php echo esc_url($MJTC_href); ?>" class="mjtc-admin-menu-link" <?php echo esc_attr($MJTC_id); ?>>
                                                    <?php echo esc_html(__("Add Ticket",'majestic-support')); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <?php
                                    } ?>
                                </div>

                                <!-- OPEN Tab Content -->
                                <div id="tab-content-open" class="mjtc-tab-hidden">
                                    <?php
                                    if(count(majesticsupport::$_data['recent_open_tickets']) > 0){
                                        foreach (majesticsupport::$_data['recent_open_tickets'] AS $MJTC_ticket) { ?>
                                            <div class="mjtc-cp-tkt-list">
                                                <div class="mjtc-tkt-row-left">
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid, 'mjtc-admin-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                    <div>
                                                        <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                            <?php
                                                            if (isset($MJTC_field_array['subject'])) { ?>
                                                                <?php echo esc_html($MJTC_ticket->subject); ?>
                                                                <?php
                                                            } ?>
                                                        </a>
                                                        <div class="mjtc-tkt-sub">
                                                            <?php echo esc_html(__("Ticket",'majestic-support')).' '.esc_html($MJTC_ticket->ticketid); ?>
                                                            <?php
                                                            if (isset($MJTC_field_array['fullname'])) {
                                                                echo ' • '.esc_html($MJTC_ticket->name);
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mjtc-tkt-row-right">
                                                    <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                    </span>
                                                    <?php
                                                    if (isset($MJTC_field_array['priority'])) { ?>
                                                        <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;color: #fff;">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                        </span>
                                                        <?php
                                                    } ?>
                                                    <span class="mjtc-tkt-time" title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>">
                                                        <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support')); ?>
                                                    </span>
                                                    <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" title="<?php echo esc_attr(__('Reply','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                        <?php echo esc_html(__("Reply",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else { ?>
                                        <div class="ms_no_record">
                                            <?php echo esc_html(__("No Record Found",'majestic-support')); ?>
                                        </div>
                                        <?php
                                    } ?>
                                </div>

                                <!-- PENDING Tab Content -->
                                <div id="tab-content-pending" class="mjtc-tab-hidden">
                                    <?php
                                    if(count(majesticsupport::$_data['recent_pending_tickets']) > 0){
                                        foreach (majesticsupport::$_data['recent_pending_tickets'] AS $MJTC_ticket) { ?>
                                            <div class="mjtc-cp-tkt-list">
                                                <div class="mjtc-tkt-row-left">
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid, 'mjtc-admin-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                    <div>
                                                        <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                            <?php
                                                            if (isset($MJTC_field_array['subject'])) { ?>
                                                                <?php echo esc_html($MJTC_ticket->subject); ?>
                                                                <?php
                                                            } ?>
                                                        </a>
                                                        <div class="mjtc-tkt-sub">
                                                            <?php echo esc_html(__("Ticket",'majestic-support')).' '.esc_html($MJTC_ticket->ticketid); ?>
                                                            <?php
                                                            if (isset($MJTC_field_array['fullname'])) {
                                                                echo ' • '.esc_html($MJTC_ticket->name);
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mjtc-tkt-row-right">
                                                    <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                    </span>
                                                    <?php
                                                    if (isset($MJTC_field_array['priority'])) { ?>
                                                        <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;color: #fff;">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                        </span>
                                                        <?php
                                                    } ?>
                                                    <span class="mjtc-tkt-time" title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>">
                                                        <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support')); ?>
                                                    </span>
                                                    <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" title="<?php echo esc_attr(__('Reply','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                        <?php echo esc_html(__("Reply",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else { ?>
                                        <div class="ms_no_record">
                                            <?php echo esc_html(__("No Record Found",'majestic-support')); ?>
                                        </div>
                                        <?php
                                    } ?>
                                </div>

                                <!-- ANSWERED Tab Content -->
                                <div id="tab-content-answered" class="mjtc-tab-hidden">
                                    <?php
                                    if(count(majesticsupport::$_data['recent_answered_tickets']) > 0){
                                        foreach (majesticsupport::$_data['recent_answered_tickets'] AS $MJTC_ticket) { ?>
                                            <div class="mjtc-cp-tkt-list">
                                                <div class="mjtc-tkt-row-left">
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid, 'mjtc-admin-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                    <div>
                                                        <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                            <?php
                                                            if (isset($MJTC_field_array['subject'])) { ?>
                                                                <?php echo esc_html($MJTC_ticket->subject); ?>
                                                                <?php
                                                            } ?>
                                                        </a>
                                                        <div class="mjtc-tkt-sub">
                                                            <?php echo esc_html(__("Ticket",'majestic-support')).' '.esc_html($MJTC_ticket->ticketid); ?>
                                                            <?php
                                                            if (isset($MJTC_field_array['fullname'])) {
                                                                echo ' • '.esc_html($MJTC_ticket->name);
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mjtc-tkt-row-right">
                                                    <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                    </span>
                                                    <?php
                                                    if (isset($MJTC_field_array['priority'])) { ?>
                                                        <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;color: #fff;">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                        </span>
                                                        <?php
                                                    } ?>
                                                    <span class="mjtc-tkt-time" title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>">
                                                        <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support')); ?>
                                                    </span>
                                                    <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" title="<?php echo esc_attr(__('Reply','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                        <?php echo esc_html(__("Reply",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else { ?>
                                        <div class="ms_no_record">
                                            <?php echo esc_html(__("No Record Found",'majestic-support')); ?>
                                        </div>
                                        <?php
                                    } ?>
                                </div>

                                <!-- CLOSED Tab Content -->
                                <div id="tab-content-closed" class="mjtc-tab-hidden">
                                    <?php
                                    if(count(majesticsupport::$_data['recent_closed_tickets']) > 0){
                                        foreach (majesticsupport::$_data['recent_closed_tickets'] AS $MJTC_ticket) { ?>
                                            <div class="mjtc-cp-tkt-list">
                                                <div class="mjtc-tkt-row-left">
                                                    <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid, 'mjtc-admin-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                    <div>
                                                        <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                            <?php
                                                            if (isset($MJTC_field_array['subject'])) { ?>
                                                                <?php echo esc_html($MJTC_ticket->subject); ?>
                                                                <?php
                                                            } ?>
                                                        </a>
                                                        <div class="mjtc-tkt-sub">
                                                            <?php echo esc_html(__("Ticket",'majestic-support')).' '.esc_html($MJTC_ticket->ticketid); ?>
                                                            <?php
                                                            if (isset($MJTC_field_array['fullname'])) {
                                                                echo ' • '.esc_html($MJTC_ticket->name);
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mjtc-tkt-row-right">
                                                    <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                    </span>
                                                    <?php
                                                    if (isset($MJTC_field_array['priority'])) { ?>
                                                        <span class="mjtc-admin-pill" style="background-color:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;color: #fff;">
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                        </span>
                                                        <?php
                                                    } ?>
                                                    <span class="mjtc-tkt-time" title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>">
                                                        <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support')); ?>
                                                    </span>
                                                    <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" title="<?php echo esc_attr(__('Reply','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_ticket->id); ?>">
                                                        <?php echo esc_html(__("Reply",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else { ?>
                                        <div class="ms_no_record">
                                            <?php echo esc_html(__("No Record Found",'majestic-support')); ?>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-volume': ?>
                            <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-volume" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <div class="mjtc-admin-col-8 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Ticket Volume", 'majestic-support')); ?>
                                        </h3>
                                        <span class="mjtc-admin-pill mjtc-admin-pill-blue">
                                            <?php echo esc_html(__("Last 7 Days", 'majestic-support')); ?>
                                        </span>
                                    </div>

                                    <div class="mjtc-admin-chart-bar-container">
                                        <?php 
                                        $MJTC_stats = majesticsupport::$_data['volume_stats'];
                                        $MJTC_max   = majesticsupport::$_data['volume_max'];
                                        
                                        foreach ($MJTC_stats as $MJTC_index => $MJTC_day_data) : 
                                            // Calculate percentage height
                                            $MJTC_height = ($MJTC_day_data['count'] / $MJTC_max) * 100;
                                            // Cap minimum height at 5% so the bar is always slightly visible
                                            $MJTC_display_height = max($MJTC_height, 5); 
                                            // Optional: Mark the current day or high-volume days as 'active'
                                            $MJTC_class = ($MJTC_height > 70) ? 'active' : ''; 
                                        ?>
                                            <div class="mjtc-admin-bar-col <?php echo esc_attr($MJTC_class); ?>" 
                                                 style="height: <?php echo (int)$MJTC_display_height; ?>%" 
                                                 title="<?php echo esc_attr($MJTC_day_data['count'] . ' ' . __('Tickets', 'majestic-support')); ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="mjtc-chart-labels-row">
                                        <?php foreach ($MJTC_stats as $MJTC_day_data) : ?>
                                            <span><?php echo esc_html($MJTC_day_data['day']); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="mjtc-admin-col-4 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt"><?php echo esc_html__("Ticket Status", 'majestic-support'); ?></h3>
                                    </div>
                                    
                                    <?php 
                                        $MJTC_d = majesticsupport::$_data['donut']; 
                                        // Create the CSS gradient string
                                        // Format: Color1 0% %1, Color2 %1 %2, Color3 %2 100%
                                        $MJTC_gradient = esc_html(sprintf(
                                            "conic-gradient(var(--mjtc-admin-primary) 0%% %d%%, var(--mjtc-admin-accent-mint) %d%% %d%%, #e5e7eb %d%% 100%%)",
                                            $MJTC_d['p_open'],
                                            $MJTC_d['p_open'],
                                            $MJTC_d['p_solved'],
                                            $MJTC_d['p_solved']
                                        ));
                                    ?>

                                    <div class="mjtc-donut-center-content">
                                        <div class="mjtc-admin-donut-wrapper" style="background: <?php echo esc_attr($MJTC_gradient); ?>;">
                                            <div class="mjtc-admin-donut-inner">
                                                <span style="font-size: 2.5rem; font-weight: 800; color: var(--mjtc-admin-text-main); letter-spacing: -0.05em;">
                                                    <?php echo esc_html($MJTC_d['active']); ?>
                                                </span>
                                                <span style="font-size: 0.75rem; color: var(--mjtc-admin-text-secondary); text-transform: uppercase; font-weight: 700;">
                                                    <?php echo esc_html__("Active", 'majestic-support'); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mjtc-chart-legend">
                                            <div class="mjtc-legend-item">
                                                <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--mjtc-admin-primary);"></span> 
                                                <?php echo esc_html($MJTC_d['open']); ?> <?php echo esc_html__("Open", 'majestic-support'); ?>
                                            </div>
                                            <div class="mjtc-legend-item">
                                                <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--mjtc-admin-accent-mint);"></span> 
                                                <?php echo esc_html($MJTC_d['solved']); ?> <?php echo esc_html__("Answered", 'majestic-support'); ?>
                                            </div>
                                            <div class="mjtc-legend-item">
                                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #e5e7eb;"></span> 
                                                <?php echo esc_html($MJTC_d['closed']); ?> <?php echo esc_html__("Closed", 'majestic-support'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-analytics': ?>
                            <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-analytics" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <!-- Priority Chart -->
                                <div class="mjtc-admin-col-4 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt"><?php echo esc_html__("By Priorities", 'majestic-support'); ?></h3>
                                    </div>
                                    
                                    <div class="mjtc-donut-center-content">
                                        <?php 
                                        $MJTC_p_data = majesticsupport::$_data['priority_donut'];
                                        $MJTC_gradient_parts = [];
                                        
                                        // Build the CSS Conic Gradient string dynamically
                                        if (!empty($MJTC_p_data)) {
                                            foreach ($MJTC_p_data as $MJTC_item) {
                                                $MJTC_gradient_parts[] = esc_html(sprintf(
                                                    "%s %d%% %d%%", 
                                                    esc_attr($MJTC_item['color']), 
                                                    $MJTC_item['start'], 
                                                    $MJTC_item['end']
                                                ));
                                            }
                                            // Add a neutral gray for the remaining percentage if any
                                            $MJTC_gradient_parts[] = "#e5e7eb " . end($MJTC_p_data)['end'] . "% 100%";
                                        } else {
                                            $MJTC_gradient_parts[] = "#e5e7eb 0% 100%";
                                        }
                                        
                                        $MJTC_full_gradient = "conic-gradient(" . implode(', ', $MJTC_gradient_parts) . ")";
                                        ?>

                                        <div class="mjtc-admin-donut-wrapper" style="width: 140px; height: 140px; background: <?php echo esc_attr($MJTC_full_gradient); ?>;">
                                            <div class="mjtc-admin-donut-inner" style="width: 100px; height: 100px;">
                                                <span style="font-weight: 700; color: var(--mjtc-admin-text-secondary); font-size: 0.75rem; text-transform: uppercase;">
                                                    <?php echo esc_html__("Priority", 'majestic-support'); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mjtc-analytics-list">
                                            <?php foreach ($MJTC_p_data as $MJTC_item) : ?>
                                                <div class="mjtc-analytics-row">
                                                    <span class="mjtc-analytics-label-group">
                                                        <span style="width:8px;height:8px;border-radius:2px;background: <?php echo esc_attr($MJTC_item['color']); ?>;"></span>
                                                        <?php echo esc_html($MJTC_item['label']); ?>
                                                    </span>
                                                    <span style="color: var(--mjtc-admin-text-main);">
                                                        <?php echo (int)$MJTC_item['perc']; ?>%
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                            
                                            <?php if (empty($MJTC_p_data)) : ?>
                                                <p style="text-align: center; font-size: 0.8rem; color: #94a3b8;">
                                                    <?php echo esc_html__("No data available", 'majestic-support'); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Department Chart -->
                                <div class="mjtc-admin-col-4 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt"><?php echo esc_html__("By Departments", 'majestic-support'); ?></h3>
                                    </div>
                                    <div class="mjtc-dept-chart-wrapper">
                                        <?php 
                                        $MJTC_dept_stats = majesticsupport::$_data['dept_stats'];
                                        $MJTC_colors = ['#60a5fa', 'var(--mjtc-admin-primary)', '#a78bfa']; // Sales Blue, Support Primary, Tech Purple
                                        if (!empty($MJTC_dept_stats)) { ?>
                                            <div style="display: flex; align-items: flex-end; gap: 1rem; height: 150px; border-bottom: 1px dashed #e5e7eb; padding-bottom: 5px;">
                                                <?php
                                                foreach ($MJTC_dept_stats as $MJTC_index => $MJTC_dept) {
                                                    // Use index to cycle through colors
                                                    $MJTC_bg_color = isset($MJTC_colors[$MJTC_index]) ? $MJTC_colors[$MJTC_index] : $MJTC_colors[0];
                                                    // Ensure a minimum height of 5% for visibility
                                                    $MJTC_display_height = max($MJTC_dept['perc'], 5); ?>
                                                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%;">
                                                        <div style="width: 100%; background: <?php echo esc_attr($MJTC_bg_color); ?>; height: <?php echo (int)$MJTC_display_height; ?>%; border-radius: 8px 8px 0 0; position: relative;">
                                                            <span style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); font-size: 10px; font-weight: bold; color: var(--mjtc-admin-text-secondary);">
                                                                <?php echo (int)$MJTC_dept['perc']; ?>%
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                } ?>
                                            </div>
                                            <?php 
                                        } else {
                                            // Empty state fallback
                                            echo '<p style="width:100%; text-align:center; color:#94a3b8; font-size:0.8rem;">'.esc_html__("No department data", "majestic-support").'</p>';
                                        }
                                        ?>
                                        <div class="mjtc-dept-labels-row">
                                            <?php foreach ($MJTC_dept_stats as $MJTC_dept) : ?>
                                                <span class="mjtc-dept-label">
                                                    <?php echo esc_html($MJTC_dept['name']); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Chart -->
                                <div class="mjtc-admin-col-4 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt"><?php echo esc_html__("By Products", 'majestic-support'); ?></h3>
                                    </div>
                                    <div class="mjtc-prod-chart-wrapper">
                                        <?php 
                                        $MJTC_prod_stats = majesticsupport::$_data['product_stats'];
                                        $MJTC_colors = ['var(--mjtc-admin-primary)', '#60a5fa', '#a78bfa'];
                                        
                                        if (!empty($MJTC_prod_stats)) :
                                            foreach ($MJTC_prod_stats as $MJTC_index => $MJTC_prod) : 
                                                $MJTC_bg_color = isset($MJTC_colors[$MJTC_index]) ? $MJTC_colors[$MJTC_index] : $MJTC_colors[0];
                                        ?>
                                            <div class="mjtc-admin-h-bar-item">
                                                <div class="mjtc-admin-h-bar-header">
                                                    <span class="mjtc-prod-text-muted"><?php echo esc_html($MJTC_prod['name']); ?></span>
                                                    <span class="mjtc-prod-text-main"><?php echo (int)$MJTC_prod['perc']; ?>%</span>
                                                </div>
                                                <div class="mjtc-admin-h-bar-track">
                                                    <div class="mjtc-admin-h-bar-fill" style="width: <?php echo (int)$MJTC_prod['perc']; ?>%; background: <?php echo esc_attr($MJTC_bg_color); ?>;"></div>
                                                </div>
                                            </div>
                                        <?php 
                                            endforeach; 
                                        else :
                                            echo '<p class="mjtc-prod-text-muted">'.esc_html__("No product data available", "majestic-support").'</p>';
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-activity': ?>
                            <div class="mjtc-activity-log-main-wrp mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-activity" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <!-- Live Activity Log -->
                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec mjtc-activity-log-wrp">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Activity Log",'majestic-support')); ?>
                                        </h3>
                                    </div>
                                    <div class="mjtc-activity-log-content-wrp" style="position: relative; padding-left: 10px; flex: 1; display: flex; flex-direction: column;">
                                        <?php if(in_array('tickethistory', majesticsupport::$_active_addons)) { ?>
                                            <?php if(count(majesticsupport::$_data['tickethistory']) > 0 ) { ?>
                                                <div class="mjtc-activity-log-dots-wrp" style="position: absolute; left: 4px; top: 10px; bottom: 10px; width: 2px; background: #f1f5f9;"></div>
                                                <?php
                                                foreach (majesticsupport::$_data['tickethistory'] AS $MJTC_history) { ?>
                                                    <div class="mjtc-activity-log-inner-wrp" style="position: relative; padding-left: 24px; margin-bottom: 24px;">
                                                        <div style="background-color: <?php echo esc_attr($MJTC_history->prioritycolour); ?>;position: absolute; left: -4px; top: 4px; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 1px #e5e7eb;"></div>
                                                        <a class="mjtc-log-text" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_history->id); ?>">
                                                            <strong><?php echo esc_html(__("Ticket",'majestic-support')).' #'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_history->ticketid)); ?></strong>
                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_history->message)); ?>
                                                        </a>
                                                        <div class="mjtc-log-meta">
                                                            <?php 
                                                            $MJTC_userName = MJTC_includer::MJTC_getObjectClass('user')->MJTC_getUserNameByUid($MJTC_history->uid);
                                                            if (!empty($MJTC_userName->display_name)) {
                                                                echo esc_html($MJTC_userName->display_name).' • ';
                                                            } elseif (!empty($MJTC_userName->user_nicename)) {
                                                                echo esc_html($MJTC_userName->user_nicename).' • ';
                                                            }
                                                            ?>
                                                            <span title="<?php echo esc_attr(date_i18n("d F, Y, H:i:s A", MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_history->message))); ?>">
                                                                <?php echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_history->datetime),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support')); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                            } else { ?>
                                                <div class="mjtc-cp-add-smart-reply">
                                                    <div class="mjtc-cp-add-smart-header">
                                                        <div class="mjtc-addon-icon">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                              <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-body">
                                                        <p>
                                                            <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-footer">
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } else {?>
                                            <div class="mjtc-cp-add-smart-reply">
                                                <div class="mjtc-cp-add-smart-header">
                                                    <div class="mjtc-addon-icon">
                                                        <svg viewBox="0 0 24 24" fill="none">
                                                          <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="mjtc-cp-add-smart-body">
                                                    <p>
                                                        <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                    </p>
                                                </div>
                                                <div class="mjtc-cp-add-smart-footer">
                                                    <a title="<?php echo esc_attr(__('Install Add-on','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://www.majesticsupport.com/product/ticket-history/">
                                                        <?php echo esc_html(__("Install Add-on",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>
                                </div>

                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Premade Responses",'majestic-support')); ?>
                                        </h3>
                                        <?php if(in_array('cannedresponses', majesticsupport::$_active_addons)) { ?>
                                            <a href="admin.php?page=majesticsupport_cannedresponses" class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm">
                                                <?php echo esc_html(__("Manage",'majestic-support')); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <?php if(in_array('cannedresponses', majesticsupport::$_active_addons)) { ?>
                                            <?php if(count(majesticsupport::$_data['cannedresponses']) > 0) {
                                                foreach (majesticsupport::$_data['cannedresponses'] AS $MJTC_cannedresponse) { ?>
                                                    <div class="mjtc-cp-tkt-list">
                                                        <div>
                                                            <a class="mjtc-tkt-subject" href="?page=majesticsupport_cannedresponses&mjslay=addpremademessage&majesticsupportid=<?php echo esc_attr($MJTC_cannedresponse->id); ?>">
                                                                <?php echo esc_html($MJTC_cannedresponse->title); ?>
                                                            </a>
                                                            <div class="mjtc-tkt-meta-bold">
                                                                <?php echo esc_html(wp_strip_all_tags($MJTC_cannedresponse->answer)); ?>
                                                            </div>
                                                        </div>
                                                        <a href="?page=majesticsupport_cannedresponses&mjslay=addpremademessage&majesticsupportid=<?php echo esc_attr($MJTC_cannedresponse->id); ?>" class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm">
                                                            <?php echo esc_html(__("View Details",'majestic-support')); ?>
                                                        </a>
                                                    </div>
                                                    <?php
                                                }
                                            } else { ?>
                                                <div class="mjtc-cp-add-smart-reply">
                                                    <div class="mjtc-cp-add-smart-header">
                                                        <div class="mjtc-addon-icon">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                              <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-body">
                                                        <p>
                                                            <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-footer">
                                                        <a href="?page=majesticsupport_cannedresponses&mjslay=addpremademessage" class="mjtc-admin-menu-link" <?php echo esc_attr($MJTC_id); ?>>
                                                            <?php echo esc_html(__("Add Premade Response",'majestic-support')); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <div class="mjtc-cp-add-smart-reply">
                                                <div class="mjtc-cp-add-smart-header">
                                                    <div class="mjtc-addon-icon">
                                                        <svg viewBox="0 0 24 24" fill="none">
                                                            <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="mjtc-cp-add-smart-body">
                                                    <p>
                                                        <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                    </p>
                                                </div>
                                                <div class="mjtc-cp-add-smart-footer">
                                                    <a title="<?php echo esc_attr(__('Install Add-on','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://majesticsupport.com/product/canned-responses/">
                                                        <?php echo esc_html(__("Install Add-on",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-vip': ?>
                            <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-vip" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("VIP Tickets", 'majestic-support')); ?>
                                            <span class="mjtc-admin-pill mjtc-admin-pill-purple" style="margin-left: 10px;">
                                                <?php echo esc_html__("WooCommerce", "majestic-support"); ?>
                                            </span>
                                        </h3>
                                    </div>
                                    <div>
                                        <?php 
                                        if(in_array('woocommerce', majesticsupport::$_active_addons)) {
                                            $MJTC_vip_tickets = majesticsupport::$_data['vip_tickets'];
                                            if (!empty($MJTC_vip_tickets)) :
                                                foreach ($MJTC_vip_tickets as $MJTC_vip) : 
                                            ?>
                                                <div class="mjtc-cp-tkt-list">
                                                    <div class="mjtc-vip-row">
                                                        <div class="mjtc-admin-avatar">
                                                            <?php echo wp_kses(MJTC_get_avatar($MJTC_ticket->uid, 'mjtc-admin-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                        </div>
                                                        <div>
                                                            <a class="mjtc-tkt-subject" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo (int)$MJTC_vip->id; ?>">
                                                                <?php echo esc_html($MJTC_vip->name); ?>
                                                            </a>
                                                            <div class="mjtc-tkt-sub">
                                                                <?php echo esc_html__( 'Order', 'majestic-support' ).' #' . esc_html( $MJTC_vip->wcorderid ); ?> • 
                                                                <?php echo esc_html($MJTC_vip->subject); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo (int)$MJTC_vip->id; ?>" class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm">
                                                        <?php echo esc_html__("View", "majestic-support"); ?>
                                                    </a>
                                                </div>
                                            <?php 
                                                endforeach; 
                                            else : ?>
                                                <p style="text-align: center; color: #94a3b8; padding: 20px 0;">
                                                    <?php echo esc_html__("No active VIP tickets found.", 'majestic-support'); ?>
                                                </p>
                                            <?php endif; ?>
                                        <?php } else { ?>
                                            <div class="mjtc-cp-add-smart-reply">
                                                <div class="mjtc-cp-add-smart-header">
                                                    <div class="mjtc-addon-icon">
                                                        <svg viewBox="0 0 24 24" fill="none">
                                                          <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="mjtc-cp-add-smart-body">
                                                    <p>
                                                        <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                    </p>
                                                </div>
                                                <div class="mjtc-cp-add-smart-footer">
                                                    <a title="<?php echo esc_attr(__('Install Add-on','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://majesticsupport.com/product/woocommerce/">
                                                        <?php echo esc_html(__("Install Add-on",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt"><?php echo esc_html(__("Feedback On Closed",'majestic-support')); ?></h3>
                                    </div>
                                    <div>
                                        <?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
                                            <?php if(count(majesticsupport::$_data['feedback']) > 0){
                                                foreach (majesticsupport::$_data['feedback'] AS $MJTC_feedback): ?>
                                                    <div class="mjtc-cp-tkt-list" style="align-items: flex-start;">
                                                        <div style="flex:1">
                                                            <div class="mjtc-recent-title-row mb-1">
                                                                <span class="mjtc-tkt-subject" style="font-size: 0.875rem;"><?php echo esc_html(__('Ticket', 'majestic-support') ) .' #'.esc_html($MJTC_feedback->trackingid); ?></span>
                                                                <div class="mjtc-rating-wrapper" title="<?php echo esc_attr($MJTC_feedback->rating); ?> / 5">
                                                                    <?php
                                                                    $MJTC_rating_val = (int)$MJTC_feedback->rating; // Ensure it's an integer
                                                                    
                                                                    for ($MJTC_i = 1; $MJTC_i <= 5; $MJTC_i++) {
                                                                        if ($MJTC_i <= $MJTC_rating_val) {
                                                                            // Solid Star (Gold)
                                                                            echo '<span style="color: #f59e0b; font-weight: 800;">★</span>';
                                                                        } else {
                                                                            // Empty Star (Light Gray)
                                                                            echo '<span style="color: #e2e8f0; font-weight: 800;">☆</span>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <p class="mjtc-tkt-sub" style="font-style: italic;">"<?php echo esc_html($MJTC_feedback->remarks); ?>"</p>
                                                        </div>
                                                    </div>
                                                    <?php
                                                endforeach;
                                            }else{ ?>
                                                <div class="mjtc-cp-add-smart-reply">
                                                    <div class="mjtc-cp-add-smart-header">
                                                        <div class="mjtc-addon-icon">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                              <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-body">
                                                        <p>
                                                            <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-footer">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else {?>
                                            <div class="mjtc-cp-add-smart-reply">
                                                <div class="mjtc-cp-add-smart-header">
                                                    <div class="mjtc-addon-icon">
                                                        <svg viewBox="0 0 24 24" fill="none">
                                                          <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="mjtc-cp-add-smart-body">
                                                    <p>
                                                        <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                    </p>
                                                </div>
                                                <div class="mjtc-cp-add-smart-footer">
                                                    <a title="<?php echo esc_attr(__('Install Add-on','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://majesticsupport.com/product/feedback/">
                                                        <?php echo esc_html(__("Install Add-on",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-performance': ?>
                            <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-performance" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <!-- Agent Stats -->
                                <div class="mjtc-admin-col-8 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__('Agents Performance', 'majestic-support')); ?>
                                        </h3>
                                    </div>
                                    <?php
                                    if (in_array('agent', majesticsupport::$_active_addons)) {
                                        if(count(majesticsupport::$_data['agents']) > 0){
                                            foreach (majesticsupport::$_data['agents'] AS $MJTC_agent) { ?>
                                                <div class="mjtc-cp-tkt-list">
                                                    <div class="mjtc-agent-row-left">
                                                        <div style="width: 36px; height: 36px; background: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                                            <?php echo wp_kses(MJTC_get_avatar($MJTC_agent->staffuid, 'mjtc-admin-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                        </div>
                                                        <a class="mjtc-tkt-subject" title="<?php echo esc_attr(__('View','majestic-support')); ?>" href="?page=majesticsupport_agent&mjslay=addstaff&majesticsupportid=<?php echo esc_attr($MJTC_agent->staffid); ?>">
                                                            <?php echo esc_html($MJTC_agent->staffname); ?>
                                                        </a>
                                                    </div>
                                                    <div class="mjtc-agent-stats-row">
                                                        <span><?php echo esc_html($MJTC_agent->total_assigned).' '.esc_html(__('Assigned', 'majestic-support')); ?></span>
                                                        <span style="color:#10b981;"><?php echo esc_html($MJTC_agent->total_resolved).' '.esc_html(__('Solved', 'majestic-support')); ?></span>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } else { ?>
                                            <div class="mjtc-cp-add-smart-reply">
                                                <div class="mjtc-cp-add-smart-header">
                                                    <?php echo esc_html(__("Agent",'majestic-support')); ?>
                                                </div>
                                                <div class="mjtc-cp-add-smart-body">
                                                    <p>
                                                        <?php echo esc_html(__("The system has no agents add an agent to maximize your productivity.",'majestic-support')); ?>
                                                    </p>
                                                </div>
                                                <div class="mjtc-cp-add-smart-footer">
                                                    <a title="<?php echo esc_attr(__('Add Agent','majestic-support')); ?>" class="mjtc-admin-menu-link" href="?page=majesticsupport_agent&mjslay=addstaff">
                                                        <?php echo esc_html(__("Add Agent",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                    <?php } else { ?>
                                        <div class="mjtc-support-admin-cp-tickets mjtc-smart-reply-border">
                                            <div class="mjtc-cp-add-smart-reply">
                                                <div class="mjtc-cp-add-smart-header">
                                                    <?php echo esc_html(__("Agent Add-on Not Installed",'majestic-support')); ?>
                                                </div>
                                                <div class="mjtc-cp-add-smart-body">
                                                    <p>
                                                        <?php echo esc_html(__("Don't limit yourself—install the Agent addon for maximum productivity.",'majestic-support')); ?>
                                                    </p>
                                                </div>
                                                <div class="mjtc-cp-add-smart-footer">
                                                    <a title="<?php echo esc_attr(__('agent addone','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://www.majesticsupport.com/product/agents/">
                                                        <?php echo esc_html(__("Install Agent Addon",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <!-- Timers -->
                                <div class="mjtc-admin-col-4 mjtc-cp-cnt-sec" style="background: linear-gradient(145deg, #111827 0%, #1f2937 100%); color: white;">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt" style="color: white;"><?php echo esc_html__("Active Timers", 'majestic-support'); ?></h3>
                                    </div>

                                    <?php
                                    if(in_array('timetracking', majesticsupport::$_active_addons)) {
                                        $MJTC_active_timers = majesticsupport::$_data['active_timers'];
                                        if (!empty($MJTC_active_timers)) :
                                            foreach ($MJTC_active_timers as $MJTC_index => $MJTC_timer) : 
                                                // Add border-bottom only to the first item to match your design
                                                $MJTC_border_style = ($MJTC_index === 0) ? 'border-bottom: 1px solid #374151; margin-bottom: 1.5rem; padding-bottom: 1.5rem;' : '';
                                                // Change timer color based on index for variety (Green then Blue)
                                                $MJTC_display_color = ($MJTC_index === 0) ? '#10b981' : '#60a5fa';
                                                $MJTC_shadow_color  = ($MJTC_index === 0) ? 'rgba(16,185,129,0.4)' : 'rgba(96,165,250,0.4)'; ?>
                                            <div class="mjtc-timer-row <?php echo ($MJTC_index === 0) ? 'mb-6 pb-6' : ''; ?>" style="<?php echo esc_attr($MJTC_border_style); ?> ; margin-bottom: 1.5rem; padding-bottom: 1.5rem;">
                                                <div>
                                                    <a class="mjtc-admin-font-bold" title="<?php echo esc_attr(__('View Details','majestic-support')); ?>" href="?page=majesticsupport_ticket&mjslay=ticketdetail&majesticsupportid=<?php echo esc_attr($MJTC_timer['ticket_id']); ?>">
                                                        <?php if($MJTC_timer['ticket_id'] > 0): ?>
                                                            #<?php echo esc_html($MJTC_timer['ticket_id']); ?> 
                                                        <?php endif; ?>
                                                        <?php echo esc_html($MJTC_timer['subject']); ?>
                                                    </a>
                                                    <div class="mjtc-tkt-sub mjtc-tkt-btm-sect" style="opacity: 1;">
                                                        <?php
                                                        echo esc_html__( 'Logged ', 'majestic-support' ) .
                                                             esc_html( $MJTC_timer['relative'] ) .' '.
                                                             esc_html__( 'ago', 'majestic-support' );
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="mjtc-timer-display" style="font-family: monospace; color: <?php echo esc_attr($MJTC_display_color); ?>; text-shadow: 0 0 10px <?php echo esc_attr($MJTC_shadow_color); ?>;">
                                                    <?php echo esc_html($MJTC_timer['display_time']); ?>
                                                </div>
                                            </div>
                                        <?php 
                                            endforeach; 
                                        else : ?>
                                            <p style="opacity: 0.5; text-align: center;"><?php echo esc_html__("No time logs found", 'majestic-support'); ?></p>
                                        <?php endif; ?>
                                    <?php } else { ?>
                                        <div class="mjtc-cp-add-smart-reply">
                                            <div class="mjtc-cp-add-smart-header">
                                                <div class="mjtc-addon-icon">
                                                    <svg viewBox="0 0 24 24" fill="none">
                                                      <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                      <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="mjtc-cp-add-smart-body">
                                                <p>
                                                    <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                </p>
                                            </div>
                                            <div class="mjtc-cp-add-smart-footer">
                                                <a title="<?php echo esc_attr(__('Install Add-on','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://majesticsupport.com/product/time-tracking/">
                                                    <?php echo esc_html(__("Install Add-on",'majestic-support')); ?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-addons': ?>
                            <div class="mjtc-cp-cnt-sec <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-addons" style="margin-bottom: 1rem; <?php echo esc_attr($MJTC_visibility_style); ?>">
                                <!-- here -->
                                <?php
                                /**
                                 * 1. PREPARE DATA
                                 * Initialize Updater to get CDN data for version checks
                                 */
                                require_once MJTC_PLUGIN_PATH . 'includes/addon-updater/msupdater.php';
                                $MJTC_SUPPORTTICKETUpdater = new MJTC_SUPPORTTICKETUpdater();
                                $MJTC_cdnversiondata = $MJTC_SUPPORTTICKETUpdater->MJTC_getPluginVersionDataFromCDN();

                                /**
                                 * 2. COMPLETE ADD-ONS MASTER LIST
                                 * This list contains every add-on from your reference code.
                                 * Format: [slug, Title, Description, Icon Filename, Color Variable]
                                 */
                                $MJTC_all_addons_list = [
                                    ['agent', __('Agents', 'majestic-support'), __('Manage support staff and assignments', 'majestic-support'), 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z', '--mjtc-admin-primary'],
                                    ['aipoweredreply', __('AI Powered Reply', 'majestic-support'), __('Generate smart responses with AI', 'majestic-support'), 'M19 1h-5v2h5v13H5V3h5V1H5c-1.1 0-2 .9-2 2v13c0 1.1.9 2 2 2h4l-3 3v1h12v-1l-3-3h4c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zM7.5 7l1-2.25L10.75 4 8.5 3l-1-2.25L6.25 3 4 4l2.25 1L7.5 7zm9.5 6l.75-1.75L19.5 10.5 17.75 9.75 17 8l-.75 1.75L14.5 10.5l1.75.75L17 13z', '--mjtc-admin-accent-coral'],
                                    ['autoclose', __('Auto Close Ticket', 'majestic-support'), __('Close inactive tickets automatically', 'majestic-support'), 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z', '--mjtc-admin-primary'],
                                    ['feedback', __('Feedback', 'majestic-support'), __('Collect customer satisfaction ratings', 'majestic-support'), 'M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z', '--mjtc-admin-accent-mint'],
                                    ['helptopic', __('Help Topics', 'majestic-support'), __('Organize tickets by specific topics', 'majestic-support'), 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z', '--mjtc-admin-primary'],
                                    ['note', __('Private Note', 'majestic-support'), __('Add internal notes for staff eyes only', 'majestic-support'), 'M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z', '--mjtc-admin-primary'],
                                    ['knowledgebase', __('Knowledge Base', 'majestic-support'), __('Create a self-service help portal', 'majestic-support'), 'M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z', '--mjtc-admin-accent-mint'],
                                    ['maxticket', __('Max Tickets', 'majestic-support'), __('Limit open tickets per user', 'majestic-support'), 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z', '--mjtc-admin-accent-coral'],
                                    ['mergeticket', __('Merge Tickets', 'majestic-support'), __('Combine duplicate tickets into one', 'majestic-support'), 'M17 20.41L18.41 19 15 15.59V13.5c0-1.61-1.1-2.96-2.59-3.37L15 4h-2l-2 5-2-5H7l2.59 6.13C8.1 10.54 7 11.89 7 13.5v2.09L3.59 19 5 20.41l3.5-3.5h7l3.5 3.5z', '--mjtc-admin-primary'],
                                    ['overdue', __('Overdue', 'majestic-support'), __('Track tickets past their SLA', 'majestic-support'), 'M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.58-5.42L6.17 5.17C4.23 6.82 3 9.26 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z', '--mjtc-admin-accent-coral'],
                                    ['smtp', __('SMTP', 'majestic-support'), __('Send emails via reliable SMTP', 'majestic-support'), 'M2 4v16h20V4H2zm18 4l-8 5-8-5V6l8 5 8-5v2z', '--mjtc-admin-primary'],
                                    ['tickethistory', __('Ticket History', 'majestic-support'), __('View audit logs of ticket changes', 'majestic-support'), 'M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z', '--mjtc-admin-primary'],
                                    ['cannedresponses', __('Premade Responses', 'majestic-support'), __('Save time with reusable templates', 'majestic-support'), 'M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z', '--mjtc-admin-accent-mint'],
                                    ['emailpiping', __('Email Piping', 'majestic-support'), __('Convert emails into tickets', 'majestic-support'), 'M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z', '--mjtc-admin-primary'],
                                    ['timetracking', __('Time Tracking', 'majestic-support'), __('Log time spent on each ticket', 'majestic-support'), 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z', '--mjtc-admin-accent-mint'],
                                    ['useroptions', __('User Options', 'majestic-support'), __('Advanced frontend user capabilities', 'majestic-support'), 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z', '--mjtc-admin-primary'],
                                    ['actions', __('Ticket Actions', 'majestic-support'), __('Bulk actions and automation tools', 'majestic-support'), 'M13 3h-2v10h2V3zm4.83 2.17l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.58-5.42L6.17 5.17C4.23 6.82 3 9.26 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.74-1.23-5.18-3.17-6.83z', '--mjtc-admin-primary'],
                                    ['announcement', __('Announcements', 'majestic-support'), __('Broadcast news to all users', 'majestic-support'), 'M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z', '--mjtc-admin-accent-mint'],
                                    ['banemail', __('Ban Email', 'majestic-support'), __('Block spam email addresses', 'majestic-support'), 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm4.31-3.1L5.69 5.69C7.04 4.63 8.74 4 10.5 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z', '--mjtc-admin-accent-coral'],
                                    ['notification', __('Desktop Notifications', 'majestic-support'), __('Get real-time browser alerts', 'majestic-support'), 'M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z', '--mjtc-admin-accent-coral'],
                                    ['export', __('Export', 'majestic-support'), __('Download ticket data to CSV/Excel', 'majestic-support'), 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z', '--mjtc-admin-primary'],
                                    ['download', __('Downloads', 'majestic-support'), __('Manage file attachments efficiently', 'majestic-support'), 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z', '--mjtc-admin-accent-mint'],
                                    ['faq', __('FAQs', 'majestic-support'), __('Build a frequently asked questions list', 'majestic-support'), 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z', '--mjtc-admin-accent-mint'],
                                    ['dashboardwidgets', __('Admin Widgets', 'majestic-support'), __('Dashboard stats for WP admin home', 'majestic-support'), 'M13 9h8L11 24v-9H4L14 0v9z', '--mjtc-admin-primary'],
                                    ['mail', __('Internal Mail', 'majestic-support'), __('Private staff-to-staff messaging', 'majestic-support'), 'M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z', '--mjtc-admin-primary'],
                                    ['widgets', __('Front-end Widgets', 'majestic-support'), __('Embed support forms anywhere', 'majestic-support'), 'M13 13v8h8v-8h-8zM3 21h8v-8H3v8zM3 3v8h8V3H3zm13.66 6.34L21 6.34 17.66 3 14.34 6.34 17.66 9.34z', '--mjtc-admin-accent-mint'],
                                    ['woocommerce', __('WooCommerce', 'majestic-support'), __('Show order info inside tickets', 'majestic-support'), 'M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z', '--mjtc-admin-accent-coral'],
                                    ['privatecredentials', __('Private Credentials', 'majestic-support'), __('Securely collect user login details', 'majestic-support'), 'M12 17c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm6-9h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM8.9 6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2H8.9V6z', '--mjtc-admin-accent-coral'],
                                    ['envatovalidation', __('Envato Validation', 'majestic-support'), __('Verify Envato purchase codes', 'majestic-support'), 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z', '--mjtc-admin-accent-mint'],
                                    ['mailchimp', __('Mailchimp', 'majestic-support'), __('Sync users to mailing lists', 'majestic-support'), 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM8 10c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm8 0c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm-4 6c-2.33 0-4.31-1.46-5.11-3.5h10.22c-.8 2.04-2.78 3.5-5.11 3.5z', '--mjtc-admin-accent-mint'],
                                    ['paidsupport', __('Paid Support', 'majestic-support'), __('Charge users for premium tickets', 'majestic-support'), 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.88 15.46L12 16.32l-1.88 1.14c-.34.2-.76-.11-.66-.49l.49-2.14-1.65-1.42c-.27-.23-.13-.68.23-.7l2.17-.19.86-2.01c.14-.33.6-.33.74 0l.86 2.01 2.17.19c.36.02.51.48.23.7l-1.65 1.42.49 2.14c.1.38-.32.69-.66.49z', '--mjtc-admin-accent-coral'],
                                    ['easydigitaldownloads', __('Easy Digital Download', 'majestic-support'), __('EDD integration for digital products', 'majestic-support'), 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z', '--mjtc-admin-accent-mint'],
                                    ['multilanguageemailtemplates', __('Multi-Language Emails', 'majestic-support'), __('Send notifications in user languages', 'majestic-support'), 'M12.87 15.07l-2.54-2.51.03-.03c1.74-1.94 2.98-4.17 3.71-6.53H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z', '--mjtc-admin-primary'],
                                    ['emailcc', __('Email CC', 'majestic-support'), __('CC additional users on ticket replies', 'majestic-support'), 'M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z', '--mjtc-admin-primary'],
                                    ['multiform', __('Multi Forms', 'majestic-support'), __('Create multiple custom ticket forms', 'majestic-support'), 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm3-4H7v-2h8v2zm0-4H7V7h8v2z', '--mjtc-admin-accent-mint'],
                                    ['agentautoassign', __('Agent Auto Assign', 'majestic-support'), __('Round-robin ticket assignment', 'majestic-support'), 'M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0 10c2.7 0 5.8 1.29 6 2H6c.23-.71 3.31-2 6-2m0-12C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z', '--mjtc-admin-primary'],
                                    ['ticketclosereason', __('Close Reasons', 'majestic-support'), __('Ask why a ticket is being closed', 'majestic-support'), 'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z', '--mjtc-admin-accent-coral'],
                                ];

                                $MJTC_show_count = 0; // The threshold counter
                                $MJTC_is_visible = !empty($MJTC_saved_settings['sec-addons']);
                                $MJTC_visibility_class = $MJTC_is_visible ? 'jsst-visible' : 'jsst-hidden';
                                ?>
                                <div class="mjtc-cp-cnt-title">
                                    <h3 class="mjtc-cp-cnt-title-txt"><?php esc_html_e('Recommended Add-ons', 'majestic-support'); ?></h3>
                                    <a href="?page=majesticsupport_premiumplugin&mjslay=addonstatus" class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm">
                                        <?php esc_html_e('View All', 'majestic-support'); ?>
                                    </a>
                                </div>
                                <div class="mjtc-admin-grid" style="padding-bottom: 0; gap: 1rem;">
                                    <?php 
                                    foreach ($MJTC_all_addons_list as $MJTC_addon) :
                                        // Stop if we have already rendered 3 cards
                                        if ($MJTC_show_count >= 3) break;

                                        $MJTC_slug  = $MJTC_addon[0];
                                        $title = $MJTC_addon[1];
                                        $MJTC_desc  = $MJTC_addon[2];
                                        $MJTC_svg_path = $MJTC_addon[3];
                                        $MJTC_color = $MJTC_addon[4];

                                        // SKIP LOGIC: If the addon is active, we don't show it in "Recommended"
                                        if (in_array($MJTC_slug, majesticsupport::$_active_addons)) {
                                            continue;
                                        }

                                        // Logic for Button (Check if file exists but is inactive)
                                        $MJTC_plugin_path = 'majestic-support-' . $MJTC_slug . '/majestic-support-' . $MJTC_slug . '.php';
                                        $MJTC_plugininfo  = mjtc_checkPluginInfo($MJTC_plugin_path);
                                        
                                        if ($MJTC_plugininfo['availability'] == "1") {
                                            // Plugin is downloaded but inactive
                                            $MJTC_btn_text = __('Activate Now', 'majestic-support');
                                            $MJTC_btn_url  = admin_url("plugins.php?s=majestic-support-{$MJTC_slug}&plugin_status=inactive");
                                            $MJTC_btn_class = "mjtc-admin-btn-primary"; // Highlight activation
                                        } else {
                                            // Plugin is not found on site
                                            $MJTC_btn_text = __('Install Now', 'majestic-support');
                                            // Use custom product mapping if slugs differ on your site
                                            $MJTC_btn_url  = "https://majesticsupport.com/product/{$MJTC_slug}/";
                                            $MJTC_btn_class = "mjtc-admin-btn-light";
                                        }

                                        $MJTC_show_count++;
                                        ?>
                                        <div class="mjtc-admin-col-4 mjtc-admin-addon-card-item mjtc-cp-cnt-sec" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 140px;">
                                            <div style="display: flex; gap: 1rem; align-items: center;">
                                                <div class="mjtc-admin-addon-icon-box" style="width: 48px; height: 48px; flex-shrink: 0; display:flex; align-items:center; justify-content:center; color: var(<?php echo esc_attr($MJTC_color); ?>); border-radius: 10px;">
                                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                                                        <path d="<?php echo esc_attr($MJTC_svg_path); ?>"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="mjtc-tkt-subject"><?php echo esc_html($title); ?></div>
                                                    <div class="mjtc-tkt-sub"><?php echo esc_html($MJTC_desc); ?></div>
                                                </div>
                                            </div>
                                            <div style="margin-top: 1rem;">
                                                <a href="<?php echo esc_url($MJTC_btn_url); ?>" class="mjtc-admin-btn <?php echo esc_attr($MJTC_btn_class); ?> mjtc-admin-btn-sm" style="width: 100%; text-decoration: none; text-align: center;">
                                                    <?php echo esc_html($MJTC_btn_text); ?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if ($MJTC_show_count === 0) : ?>
                                        <div class="mjtc-admin-col-12" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 2rem; text-align: center; color: #64748b;">
                                            <?php esc_html_e('You have all add-ons active! You are a power user.', 'majestic-support'); ?> 🚀
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php break;

                        case 'sec-import':
                            if ( is_plugin_active('awesome-support/awesome-support.php') ||
                                is_plugin_active('supportcandy/supportcandy.php') ||
                                is_plugin_active('fluent-support/fluent-support.php')
                            ) { ?>
                                <div class="mjtc-cp-cnt-sec <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-import" style="margin-bottom: 1rem; <?php echo esc_attr($MJTC_visibility_style); ?>">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Import Data",'majestic-support')); ?>
                                        </h3>
                                        <span class="mjtc-admin-pill mjtc-admin-pill-blue">
                                            <?php echo esc_html(__("Beta",'majestic-support')); ?>
                                        </span>
                                    </div>
                                    <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" style="padding-bottom:0; gap:1rem;">
                                        <?php
                                        if ( is_plugin_active('awesome-support/awesome-support.php')) { ?>
                                            <div class="mjtc-admin-col-4 mjtc-admin-addon-card-item mjtc-cp-cnt-sec">
                                                <div style="display: flex; gap: 1rem; align-items: center;">
                                                    <div class="mjtc-admin-addon-icon-box" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center; color: #172b4d;">
                                                        <!-- Zendesk Logo -->
                                                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                                                    </div>
                                                    <div>
                                                        <div class="mjtc-tkt-subject">
                                                            <?php echo esc_html(__("Awesome Support",'majestic-support')); ?>
                                                        </div>
                                                        <div class="mjtc-tkt-sub" style="color: #10b981; font-weight:700;">✔ 142 Tickets found</div>
                                                        <div class="mjtc-tkt-sub">3 Priorities mapped</div>
                                                    </div>
                                                </div>
                                                <button class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" style="margin-top: 1rem; width: 100%;">Import</button>
                                            </div>
                                            <?php
                                        }
                                        if ( is_plugin_active('supportcandy/supportcandy.php')) { ?>
                                            <div class="mjtc-admin-col-4 mjtc-admin-addon-card-item mjtc-cp-cnt-sec">
                                                <div style="display: flex; gap: 1rem; align-items: center;">
                                                    <div class="mjtc-admin-addon-icon-box" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center; color: #0052cc;">
                                                        <!-- Jira Logo -->
                                                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M11.53 2C11.53 2 12.33 2.13 12.33 3.61C12.33 5.09 11.53 5.21 11.53 5.21L6.2 8.79C6.2 8.79 5.4 8.67 5.4 7.21C5.4 5.73 6.2 5.6 6.2 5.6L11.53 2Z"/></svg>
                                                    </div>
                                                    <div>
                                                        <div class="mjtc-tkt-subject">
                                                            <?php echo esc_html(__("SupportCandy",'majestic-support')); ?>
                                                        </div>
                                                        <div class="mjtc-tkt-sub" style="color: #4f46e5; font-weight:700;">✔ 24 Issues found</div>
                                                        <div class="mjtc-tkt-sub">4 Departments mapped</div>
                                                    </div>
                                                </div>
                                                <button class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" style="margin-top: 1rem; width: 100%;">Import</button>
                                            </div>
                                            <?php
                                        }
                                        if ( is_plugin_active('fluent-support/fluent-support.php')) { ?>
                                            <div class="mjtc-admin-col-4 mjtc-admin-addon-card-item mjtc-cp-cnt-sec">
                                                <div style="display: flex; gap: 1rem; align-items: center;">
                                                    <div class="mjtc-admin-addon-icon-box" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center; color: #00a1e0;">
                                                        <!-- Salesforce Logo -->
                                                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19.07 10.66C18.66 6.64 15.25 3.5 11 3.5C7.4 3.5 4.38 5.74 3.12 8.91C1.32 9.66 0 11.39 0 13.5C0 16.54 2.46 19 5.5 19H18.5C21.54 19 24 16.54 24 13.5C24 10.94 22.25 8.77 19.92 8.16C19.64 9.1 19.26 9.95 19.07 10.66Z"/></svg>
                                                    </div>
                                                    <div>
                                                        <div class="mjtc-tkt-subject">
                                                            <?php echo esc_html(__("Fluent Support",'majestic-support')); ?>
                                                        </div>
                                                        <div class="mjtc-tkt-sub" style="color: #0ea5e9; font-weight:700;">✔ 45 Contacts found</div>
                                                        <div class="mjtc-tkt-sub">7 Accounts ready</div>
                                                    </div>
                                                </div>
                                                <button class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" style="margin-top: 1rem; width: 100%;">Import</button>
                                            </div>
                                            <?php 
                                        } ?>
                                    </div>
                                </div>
                            <?php }
                            break;

                        case 'sec-kb-help': ?>
                            <div class="mjtc-admin-grid <?php echo esc_attr($MJTC_visibility_class); ?>" id="sec-kb-help" style="<?php echo esc_attr($MJTC_visibility_style); ?>">
                                <!-- Top KB Articles -->
                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Top Knowledge Base Articles",'majestic-support')); ?>
                                        </h3>
                                        <?php if(in_array('knowledgebase', majesticsupport::$_active_addons)){ ?>
                                            <a class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" href="admin.php?page=majesticsupport_knowledgebase&mjslay=listarticles" title="<?php echo esc_attr(__('Knowledge Base' , 'majestic-support')); ?>">
                                                <?php echo esc_html(__("View All",'majestic-support')); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <?php if(in_array('knowledgebase', majesticsupport::$_active_addons)){ ?>
                                            <?php if(count(majesticsupport::$_data['knowledgebase']) > 0){
                                                foreach (majesticsupport::$_data['knowledgebase'] AS $MJTC_knowledgebase) { ?>
                                                    <div class="mjtc-cp-tkt-list">
                                                        <div>
                                                            <a class="mjtc-tkt-subject" href="?page=majesticsupport_knowledgebase&mjslay=addarticle&majesticsupportid=<?php echo esc_attr($MJTC_knowledgebase->id); ?>">
                                                                <?php echo esc_html($MJTC_knowledgebase->subject); ?>
                                                            </a>
                                                            <div class="mjtc-tkt-meta-bold"><?php echo esc_html($MJTC_knowledgebase->categoryname); ?></div>
                                                        </div>
                                                        <div class="mjtc-kb-row-meta">
                                                            <span class="mjtc-kb-views"><?php echo esc_html($MJTC_knowledgebase->created).' '.esc_html(__('Views', 'majestic-support')); ?> </span>
                                                            <a href="?page=majesticsupport_knowledgebase&mjslay=addarticle&majesticsupportid=<?php echo esc_attr($MJTC_knowledgebase->id); ?>" class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" style="padding: 4px 8px;">
                                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3zM5 5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7H5V5z"/></svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }else{ ?>
                                                <div class="mjtc-cp-add-smart-reply">
                                                    <div class="mjtc-cp-add-smart-header">
                                                        <div class="mjtc-addon-icon">
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                              <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                              <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-body">
                                                        <p>
                                                            <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mjtc-cp-add-smart-footer">
                                                        <a href="?page=majesticsupport_knowledgebase&mjslay=addarticle" class="mjtc-admin-menu-link" <?php echo esc_attr($MJTC_id); ?>>
                                                            <?php echo esc_html(__("Add Knowledge Base",'majestic-support')); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else {?>
                                            <div class="mjtc-cp-add-smart-reply">
                                                <div class="mjtc-cp-add-smart-header">
                                                    <div class="mjtc-addon-icon">
                                                        <svg viewBox="0 0 24 24" fill="none">
                                                          <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                          <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="mjtc-cp-add-smart-body">
                                                    <p>
                                                        <?php echo esc_html(__("The best support plugin for the majestic support has everything you need.",'majestic-support')); ?>
                                                    </p>
                                                </div>
                                                <div class="mjtc-cp-add-smart-footer">
                                                    <a title="<?php echo esc_attr(__('Install Add-on','majestic-support')); ?>" class="mjtc-admin-menu-link" href="https://majesticsupport.com/product/knowledge-base/">
                                                        <?php echo esc_html(__("Install Add-on",'majestic-support')); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Quick Help Guide -->
                                <div class="mjtc-admin-col-6 mjtc-cp-cnt-sec">
                                    <div class="mjtc-cp-cnt-title">
                                        <h3 class="mjtc-cp-cnt-title-txt">
                                            <?php echo esc_html(__("Quick Help Guide",'majestic-support')); ?>
                                        </h3>
                                    </div>
                                    <div>
                                        <a class="mjtc-cp-tkt-list" target="_blank" href="https://www.youtube.com/watch?v=lHAacpG-O0M&t=16s&ab_channel=MajesticSupport">
                                            <div class="mjtc-help-row">
                                                <div class="mjtc-admin-addon-icon-box" style="width: 36px; height: 36px; display:flex; align-items:center; justify-content:center; color: var(--mjtc-admin-primary);">
                                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="mjtc-tkt-subject"><?php echo esc_html(__("Getting Started",'majestic-support')); ?></div>
                                                    <div class="mjtc-tkt-sub"><?php echo esc_html(__("Installation & Setup",'majestic-support')); ?></div>
                                                </div>
                                            </div>
                                            <span class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" target="_blank" href="https://www.youtube.com/watch?v=lHAacpG-O0M&t=16s&ab_channel=MajesticSupport">
                                                <?php echo esc_html(__("Watch",'majestic-support')); ?>
                                            </span>
                                        </a>
                                        <a class="mjtc-cp-tkt-list" target="_blank" href="https://www.youtube.com/watch?v=8dIMdKuTLx4&t=6s&ab_channel=MajesticSupport">
                                            <div class="mjtc-help-row">
                                                <div class="mjtc-admin-addon-icon-box" style="width: 36px; height: 36px; display:flex; align-items:center; justify-content:center; color: var(--mjtc-admin-accent-mint);">
                                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="mjtc-tkt-subject"><?php echo esc_html(__("Fields Manager",'majestic-support')); ?></div>
                                                    <div class="mjtc-tkt-sub"><?php echo esc_html(__("Fields Manager",'majestic-support')); ?></div>
                                                </div>
                                            </div>
                                            <span class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" target="_blank" href="https://www.youtube.com/watch?v=8dIMdKuTLx4&t=6s&ab_channel=MajesticSupport">
                                                <?php echo esc_html(__("Watch",'majestic-support')); ?>
                                            </span>
                                        </a>
                                        <!-- Added Missing Quick Help Item -->
                                        <a class="mjtc-cp-tkt-list" target="_blank" href="https://www.youtube.com/watch?v=JrdZLoGiHsA&ab_channel=MajesticSupport">
                                            <div class="mjtc-help-row">
                                                <div class="mjtc-admin-addon-icon-box" style="width: 36px; height: 36px; display:flex; align-items:center; justify-content:center; color: var(--mjtc-admin-accent-coral);">
                                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="mjtc-tkt-subject"><?php echo esc_html(__("Notifications",'majestic-support')); ?></div>
                                                    <div class="mjtc-tkt-sub"><?php echo esc_html(__("Setup System Emails",'majestic-support')); ?></div>
                                                </div>
                                            </div>
                                            <span class="mjtc-admin-btn mjtc-admin-btn-light mjtc-admin-btn-sm" target="_blank" href="https://www.youtube.com/watch?v=JrdZLoGiHsA&ab_channel=MajesticSupport">
                                                <?php echo esc_html(__("Watch",'majestic-support')); ?>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php break;

                    endswitch;
                endforeach; 
                ?>
            </div>
        </div>

        <?php
        $majesticsupport_js ="
            jQuery(document).ready(function() {
                jQuery('span.dashboard-icon').find('span.download').hover(function() {
                    jQuery(this).find('span').toggle('slide');
                }, function() {
                    jQuery(this).find('span').toggle('slide');
                });

                jQuery('a.mjtc-support-stats-link').click(function(e) {
                    e.preventDefault();
                    var list = jQuery(this).attr('data-tab-number');
                    var oldUrl = jQuery(this).attr('href');
                    var newUrl = oldUrl + '&list=' + list;
                    window.location.href = newUrl;
                });
            });

        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        ?>  
    </div>
</div>
<?php
function mjtc_printAddoneStatus($MJTC_key1,$MJTC_cdnversion){
    $MJTC_matched = 0;
    $MJTC_version = "";
    $MJTC_installed_plugins = get_plugins();
    foreach ($MJTC_installed_plugins as $MJTC_name => $MJTC_value) {
        $MJTC_install_plugin_name = MJTC_majesticsupportphplib::MJTC_str_replace(".php","",MJTC_majesticsupportphplib::MJTC_basename($MJTC_name));
        if($MJTC_key1 == $MJTC_install_plugin_name){
            $MJTC_matched = 1;
            $MJTC_version = $MJTC_value["Version"];
            $MJTC_install_plugin_matched_name = $MJTC_install_plugin_name;
        }
    }
    if($MJTC_matched == 1){ //installed
        $MJTC_name = $MJTC_key1;
        $title = 'auto close';
        $MJTC_img = MJTC_majesticsupportphplib::MJTC_str_replace("majestic-support-", "", $MJTC_key1).'.png';
        if($MJTC_cdnversion > $MJTC_version){ // new version available
            $MJTC_status = 'update_available';
        }else{
            $MJTC_status = 'updated';
        }
    } else {
        $MJTC_status = '';
    }


    $MJTC_addoneinfo = MJTC_includer::MJTC_getModel('premiumplugin')->MJTC_checkAddoneInfo($MJTC_name);
    if ($MJTC_status == 'update_available') {
        $MJTC_wrpclass = 'ms-admin-addon-status ms-admin-addons-status-update-wrp';
        $MJTC_btnclass = 'ms-admin-addons-update-btn';
        $MJTC_btntxt = 'Update Now';
        $MJTC_btnlink = 'id="ms-admin-addons-update" data-for="'.esc_attr($MJTC_name).'"';
        $msg = '<span id="ms-admin-addon-status-cdnversion">'.esc_html(__('New Update Version','majestic-support'));
        $msg .= '';
        $msg .= esc_html(__('is Available','majestic-support')).'</span>';
    } elseif ($MJTC_status == 'expired') {
        $MJTC_wrpclass = 'ms-admin-addon-status ms-admin-addons-status-expired-wrp';
        $MJTC_btnclass = 'ms-admin-addons-expired-btn';
        $MJTC_btntxt = 'Expired';
        $MJTC_btnlink = '';
        $msg = '';
    } elseif ($MJTC_status == 'updated') {
        $MJTC_wrpclass = 'ms-admin-addon-status';
        $MJTC_btnclass = 'ms-admin-addons-updated-btn';
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
    <div class="mjtc-cp-addon-msg-wrp">
        <span class="mjtc-cp-addon-msg '.esc_attr($MJTC_btnclass).'">
            <a '.esc_attr($MJTC_btnlink).' >'.esc_html(majesticsupport::MJTC_getVarValue($MJTC_btntxt)).'</a>
        </span>
    </div>';
    echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
}
$majesticsupport_js ="
jQuery(document).ready(function() {
    jQuery(document).on('click', 'a.mjtc-btn-install-now', function() {
        jQuery(this).attr('disabled', true);
        jQuery(this).html('Installing.....!');
        jQuery(this).removeClass('mjtc-btn-install-now');
        var pluginslug = jQuery(this).attr('data-slug');
        var buttonclass = jQuery(this).attr('class');
        jQuery(this).addClass('mjtc-installing-effect');
        if (pluginslug != '') {
            jQuery.post(ajaxurl, {
                action: 'mjsupport_ajax',
                mjsmod: 'majesticsupport',
                task: 'installPluginFromAjax',
                pluginslug: pluginslug,
                '_wpnonce': '". esc_attr(wp_create_nonce('install-plugin-ajax'))."'
            }, function(data) {
                if (data == 1) {
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).attr('disabled',
                        false);
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).html('Active Now');
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).addClass(
                        'mjtc-btn-active-now mjtc-btn-green');
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).removeClass(
                        'mjtc-installing-effect');
                } else {
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).attr('disabled',
                        false);
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).html(
                        'Please try again');
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).addClass(
                        'mjtc-btn-install-now');
                    jQuery('span.mjtc-product-install-btn a.' + buttonclass).removeClass(
                        'mjtc-installing-effect');
                }
            });
        }
    });

    jQuery(document).on('click', 'a.mjtc-btn-active-now', function() {
        jQuery(this).attr('disabled', true);
        jQuery(this).html('Activating.....!');
        jQuery(this).removeClass('mjtc-btn-active-now');
        var pluginslug = jQuery(this).attr('data-slug');
        var buttonclass = jQuery(this).attr('class');
        if (pluginslug != '') {
            jQuery.post(ajaxurl, {
                action: 'mjsupport_ajax',
                mjsmod: 'majesticsupport',
                task: 'activatePluginFromAjax',
                pluginslug: pluginslug,
                '_wpnonce': '". esc_attr(wp_create_nonce('activate-plugin-ajax'))."'
            }, function(data) {
                if (data == 1) {
                    jQuery('a[data-slug=' + pluginslug + ']').html('Activated');
                    jQuery('a[data-slug=' + pluginslug + ']').addClass('mjtc-btn-activated');
                    window.location.reload();
                }
            });
        }
    });
});

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
    $majesticsupport_js = "
    jQuery(document).ready(function($) {
        // Tab Logic
        jQuery('.mjtc-admin-tab-btn').click(function() {
            jQuery('.mjtc-admin-tab-btn').removeClass('active');
            jQuery(this).addClass('active');
            jQuery('div[id^=\"tab-content-\"]').addClass('mjtc-tab-hidden');
            const target = jQuery(this).data('tab');
            jQuery('#tab-content-' + target).removeClass('mjtc-tab-hidden');
        });

        // Toggle Sections Logic
        jQuery('.mjtc-admin-switch input').change(function() {
            const targetId = jQuery(this).data('target');
            const isChecked = jQuery(this).is(':checked');
            if (isChecked) {
                jQuery('#' + targetId).show(); 
            } else {
                jQuery('#' + targetId).hide();
            }
        });

        // Modal Logic
        jQuery('#btn-customize').click(function() {
            jQuery('#customizeModal').addClass('active');
        });
        jQuery('#closeModal').click(function() {
            jQuery('#customizeModal').removeClass('active');
        });

        // Drag and Drop Logic
        const list = document.getElementById('modal-sortable-list');
        let draggingEle;
        
        [].slice.call(list.querySelectorAll('.mjtc-admin-switch-row')).forEach(function(item) {
            item.addEventListener('dragstart', function(e) {
                draggingEle = e.target;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', e.target.innerHTML);
                item.classList.add('dragging');
            });
            item.addEventListener('dragend', function(e) {
                item.classList.remove('dragging');
            });
        });

        list.addEventListener('dragover', function(e) {
            e.preventDefault();
            const dragging = document.querySelector('.dragging');
            const siblings = [...list.querySelectorAll('.mjtc-admin-switch-row:not(.dragging)')];
            const nextSibling = siblings.find(sibling => {
                return e.clientY <= sibling.offsetTop + sibling.offsetHeight / 2;
            });
            list.insertBefore(dragging, nextSibling);
        });

        jQuery('#saveCustomization').click(function() {
            const btn = jQuery(this); // ✅ store reference
            const layoutSettings = {};
            
            btn.text('".esc_js(__('Saving...','majestic-support'))."').prop('disabled', true);

            // 1. Get visibility and order from the modal list
            jQuery('#modal-sortable-list .mjtc-admin-switch-row').each(function() {
                const targetId = jQuery(this).data('target');
                const isVisible = jQuery(this).find('input[type=\"checkbox\"]').is(':checked') ? 1 : 0;
                layoutSettings[targetId] = isVisible;
                
                // 2. Apply order visually to the dashboard immediately
                jQuery('#msadmin-data-wrp').append(jQuery('#' + targetId));
                
                // 3. Apply visibility immediately        
                if (isVisible) {
                    jQuery('#' + targetId).show();
                } else {
                    jQuery('#' + targetId).hide();
                }
            });

            // 4. Send to Database via AJAX
            jQuery.post(ajaxurl, {
                action: 'mjsupport_ajax',
                mjsmod: 'majesticsupport',
                task: 'mjtc_save_dashboard_layout',
                layout: layoutSettings,
            }, function(data) {
                if (data == 1) {
                    // delay before reverting back
                    setTimeout(function() {
                        jQuery('#saveCustomization').text('".esc_js(__('Done','majestic-support'))."').prop('disabled', false);
                    }, 1500);

                    jQuery('#customizeModal').removeClass('active');
                } else {
                    alert('Error saving settings. Please try again.');
                    jQuery('#saveCustomization').text('".esc_js(__('Done','majestic-support'))."').prop('disabled', false);
                }

            });

        });

        // Reset Defaults Button
        jQuery('#resetDefaults').click(function() {
            // 1. Reset Visibility
            jQuery('.mjtc-admin-switch input').prop('checked', true).trigger('change');
            
            // 2. Reset Order
            const defaultOrder = [
                'sec-stats', 'sec-banner', 'sec-overdue', 'sec-priority', 'sec-recent', 
                'sec-volume', 'sec-analytics', 'sec-activity', 
                'sec-vip', 'sec-performance', 'sec-addons', 'sec-import', 'sec-kb-help'
            ];

            defaultOrder.forEach(function(id) {
                const modalItem = jQuery('#modal-sortable-list').find('.mjtc-admin-switch-row[data-target=\"' + id + '\"]');
                if (modalItem.length) {
                    jQuery('#modal-sortable-list').append(modalItem);
                }
                const MJTC_widgetSection = jQuery('#' + id);
                if (MJTC_widgetSection.length) {
                    jQuery('#msadmin-data-wrp').append(MJTC_widgetSection);
                }
            });
        });

        jQuery('#close-banner').click(function() {
            jQuery('#sec-banner').slideUp(300);
        });

        // Trigger Animation
        setTimeout(function() {
            jQuery('.mjtc-admin-bar-col').each(function() {});
        }, 100);
    });";
    wp_add_inline_script( 'majestic-support-cmain-js', $majesticsupport_js );
?>  
