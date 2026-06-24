<?php
if(!defined('ABSPATH'))
    die('Restricted Access');
wp_enqueue_script('majesticsupport-notify-app', MJTC_PLUGIN_URL . 'includes/js/firebase-app.js', array(), '1.0.0', true);
wp_enqueue_script('majesticsupport-notify-message', MJTC_PLUGIN_URL . 'includes/js/firebase-messaging.js', array(), '1.0.0', true);
wp_enqueue_script('majesticsupport-google-charts', MJTC_PLUGIN_URL . 'includes/js/google-charts.js', array(), '1.0.0', true);
do_action('MJTC_ticket-notify-generate-token');
wp_enqueue_style('majesticsupport-status-graph', MJTC_PLUGIN_URL . 'includes/css/status_graph.css', array(), '1.0.0');

if(isset(majesticsupport::$_data['stack_chart_horizontal'])){
    $majesticsupport_js ="
    google.load('visualization', '1', {
        packages: ['corechart']
    });
    google.setOnLoadCallback(drawStackChartHorizontal);

    function drawStackChartHorizontal() {
        var data = google.visualization.arrayToDataTable([
            ".
                wp_kses(majesticsupport::$_data['stack_chart_horizontal']['title'], MJTC_ALLOWED_TAGS).",".
                wp_kses(majesticsupport::$_data['stack_chart_horizontal']['data'], MJTC_ALLOWED_TAGS)."
        ]);

        var view = new google.visualization.DataView(data);

        var options = {
            height: 571,
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
    }";
    //custom handle use because of this add script after chart library include
    wp_register_script( 'majesticsupport-inlinescript-handle', array(), '1.0.0',array(),'1.0.0',true);
    wp_enqueue_script( 'majesticsupport-inlinescript-handle' );

    wp_add_inline_script('majesticsupport-inlinescript-handle',$majesticsupport_js);
}
$majesticsupport_js ="
    jQuery(document).ready(function($) {
        $('#msadmin-menu-toggle').click(function(){
        $('.mjtc-cp-left').slideToggle(500);
      });
        jQuery('div#mjtc-support-main-black-background,span#mjtc-support-popup-close-button').click(function() {
            jQuery('div#mjtc-support-main-popup').slideUp();
            setTimeout(function() {
                jQuery('div#mjtc-support-main-black-background').hide();
            }, 600);

        });

        jQuery('a.mjtc-support-link').click(function(e) {
            e.preventDefault();
            var list = jQuery(this).attr('data-tab-number');
            var oldUrl = jQuery(this).attr('href'); // Get current url
            var opt = '?';
            var found = oldUrl.search('&');
            if (found > 0) {
                opt = '&';
            }
            var found = oldUrl.search('[\?\]');
            if (found > 0) {
                opt = '&';
            }
            var newUrl = oldUrl + opt + 'list=' + list; // Create new url
            window.location.href = newUrl;
        });
    });

    function getDownloadById(value, nonce) {
        ajaxurl = '". esc_url(admin_url('admin-ajax.php')) ."';
        jQuery.post(ajaxurl, {action: 'mjsupport_ajax', downloadid: value, mjsmod: 'download', task: 'getDownloadById',mspageid: ". get_the_ID().", '_wpnonce': nonce}, function (data) {
            if (data) {
                var obj = jQuery.parseJSON(data);
                jQuery('div#mjtc-support-main-content').html(MJTC_msDecodeHTML(obj.data));
                jQuery('span#mjtc-support-popup-title').html(obj.title);
                jQuery('div#mjtc-support-main-downloadallbtn').html(MJTC_msDecodeHTML(obj.downloadallbtn));
                jQuery('div#mjtc-support-main-black-background').show();
                jQuery('div#mjtc-support-main-popup').slideDown('slow');
            }
        });
    }
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>  

<div class="ms-main-up-wrapper">
    <?php

if (majesticsupport::$_config['offline'] == 2) {
    MJTC_message::MJTC_getMessage();
    $MJTC_agent_flag = 0;
    if(in_array('agent',majesticsupport::$_active_addons)){
        if (MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
            $MJTC_agent_flag = 1;
        }
    }
    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
        $MJTC_linkname = 'staff';
    } else {
        $MJTC_linkname = 'user';
    }

    $MJTC_data = isset(majesticsupport::$_data[0]) ? majesticsupport::$_data[0] : array();
    ?>
    <div class="mjtc-cp-main-wrp">
        <div class="mjtc-cp-wrapper">
            <!-- Sidebar -->
            <div class="mjtc-support-cp-overlay" id="mobile-overlay"></div>
            <aside class="mjtc-cp-left" id="sidebar">
                <?php if (is_user_logged_in()) { ?>
                    <?php
                    if (majesticsupport::$_config['tplink_profile_'. $MJTC_linkname] == 1) {
                        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {  ?>
                            <div class="mjtc-support-cp-sidebar-header">
                                <div class="mjtc-support-cp-user-profile">
                                    <div class="mjtc-support-cp-user-avatar">
                                        <?php echo wp_kses(MJTC_get_avatar(majesticsupport::$_data[0]['agentid'], 'mjtc-support-cp-user-avatar'), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <div class="user-info">
                                        <div class="mjtc-cp-user-name">
                                            <?php echo esc_html(majesticsupport::$_data[0]['agentname']); ?>
                                        </div>
                                        <div class="mjtc-cp-user-role">
                                            <?php echo esc_html(majesticsupport::$_data[0]['agentrole']); ?>
                                        </div>
                                    </div>
                                </div>
                                <button class="mjtc-support-cp-close-sidebar" id="close-sidebar-btn" aria-label="Close sidebar">
                                    <svg viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                                </button>
                            </div>
                            <?php
                        } else { ?>
                            <div class="mjtc-support-cp-sidebar-header">
                                <div class="mjtc-support-cp-user-profile">
                                    <div class="mjtc-support-cp-user-avatar">
                                        <?php echo wp_kses(MJTC_get_avatar(majesticsupport::$_data[0]['userid'], 'mjtc-support-cp-user-avatar'), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <div class="user-info">
                                        <div class="mjtc-cp-user-name">
                                            <?php echo esc_html(majesticsupport::$_data[0]['username']); ?>
                                        </div>
                                        <div class="mjtc-cp-user-role">
                                            <?php echo esc_html(majesticsupport::$_data[0]['useremail']); ?>
                                        </div>
                                    </div>
                                </div>
                                <button class="mjtc-support-cp-close-sidebar" id="close-sidebar-btn" aria-label="Close sidebar">
                                    <svg viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                                </button>
                            </div>
                            <?php
                        }
                    }
                } else {
                    if (majesticsupport::$_config['tplink_profile_user'] == 1) { ?>
                        <div class="mjtc-support-cp-sidebar-header">
                            <div class="mjtc-support-cp-user-profile">
                                <div class="mjtc-support-cp-user-avatar">
                                    <?php
                                     echo wp_kses(MJTC_get_avatar(0, 'mjtc-support-cp-user-avatar'), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <div class="user-info">
                                    <div class="mjtc-cp-user-name">
                                        <?php echo esc_html(__('Hello Visitor!', 'majestic-support')); ?>
                                    </div>
                                </div>
                            </div>
                            <button class="mjtc-support-cp-close-sidebar" id="close-sidebar-btn" aria-label="Close sidebar">
                                <svg viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                            </button>
                        </div>
                        <?php
                    }
                } ?>
                <!-- cp links for user -->
                <?php
                if ($MJTC_agent_flag == 0) { ?>
                    <nav class="mjtc-menu-links-wrp">
                        <!-- Dashboard Links -->
                        <ul>
                            <?php
                            $MJTC_count = 0;
                            if (majesticsupport::$_config['cplink_openticket_user'] == 1):
                                $MJTC_ajaxid = "";
                                $MJTC_count++;
                                if(in_array('multiform',majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                                    //show popup in case of multiform
                                    $MJTC_ajaxid = "id=multiformpopup";
                                }
                                // controller add default form id, if single form
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod' => 'ticket', 'mjslay' => 'addticket')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Submit Ticket', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class, $MJTC_ajaxid);
                            endif;
                            if (majesticsupport::$_config['cplink_myticket_user'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>';
                                $MJTC_menu_title =  esc_html(__('My Tickets', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            if (majesticsupport::$_config['cplink_checkticketstatus_user'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketstatus')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M4 7h16v4a2 2 0 0 1 0 4v4H4v-4a2 2 0 0 1 0-4V7z"></path><line x1="9" y1="12" x2="15" y2="12"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Ticket Status', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            if (in_array('announcement', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_announcements_user'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'announcements')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="m12 8-9.04 9.06a2.82 2.82 0 1 0 3.98 3.98L16 12"></path><path d="m13 13 4 4"></path><path d="m13 9 4-4"></path><path d="M18 15h.01"></path><path d="M21 12v.01"></path><path d="M18 9h.01"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Announcements', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            if (in_array('download', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_downloads_user'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'download', 'mjslay'=>'downloads')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Downloads', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            if (in_array('faq', majesticsupport::$_active_addons) &&  majesticsupport::$_config['cplink_faqs_user'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'faq', 'mjslay'=>'faqs')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                                $MJTC_menu_title =  esc_html(__("FAQs", 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            if (in_array('knowledgebase', majesticsupport::$_active_addons) &&  majesticsupport::$_config['cplink_knowledgebase_user'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'userknowledgebase')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Knowledge Base', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            if (majesticsupport::$_config['cplink_erasedata_user'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'gdpr', 'mjslay'=>'adderasedatarequest')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('User Data', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            apply_filters( 'mjtc_support_ticket_frontend_controlpanel_left_menu_custom_links_middle',$MJTC_count);
                            if (majesticsupport::$_config['cplink_login_logout_user'] == 1){
                                $MJTC_count++;
                                $MJTC_loginval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_login_link');
                                $MJTC_loginlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('login_link');
                                    if ($MJTC_loginval == 3){
                                        $MJTC_hreflink = wp_login_url();
                                    }
                                    else if ($MJTC_loginval == 2 && $MJTC_loginlink != ""){
                                        $MJTC_hreflink = $MJTC_loginlink;
                                    }else{
                                        $MJTC_hreflink= majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'login'));
                                    }
                                    if (!is_user_logged_in()):
                                        $MJTC_menu_url = $MJTC_hreflink;
                                        $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>';
                                        $MJTC_menu_title =  esc_html(__('Log In', 'majestic-support'));
                                        $MJTC_class = 'mjtc-support-cp-menu';
                                        mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                                    endif;
                                if (is_user_logged_in()):
                                    $MJTC_menu_url = wp_logout_url( home_url() );
                                    $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>';
                                    $MJTC_menu_title =  esc_html(__('Log Out', 'majestic-support'));
                                    $MJTC_class = 'mjtc-support-cp-menu';
                                    mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                                endif;
                            }
                            if (majesticsupport::$_config['cplink_register_user'] == 1){
                                $MJTC_registerval = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('set_register_link');
                                $MJTC_registerlink = MJTC_includer::MJTC_getModel('configuration')->getConfigValue('register_link');
                                if ($MJTC_registerval == 3){
                                    $MJTC_hreflink = wp_registration_url();
                                }else if ($MJTC_registerval == 2 && $MJTC_registerlink != ""){
                                    $MJTC_hreflink = $MJTC_registerlink;
                                }else{
                                    $MJTC_hreflink= majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'userregister'));
                                }
                                if (!is_user_logged_in()):
                                    $MJTC_count++;
                                    $MJTC_is_enable = get_option('users_can_register'); /*check to make sure user registration is enabled*/
                                    if ($MJTC_is_enable) {// only show the registration form if allowed
                                        $MJTC_menu_url = esc_url($MJTC_hreflink);
                                        $MJTC_image_path = '
                                        <svg viewBox="0 0 24 24">
                                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                          <circle cx="12" cy="7" r="4"></circle>
                                          <line x1="20" y1="8" x2="20" y2="14"></line>
                                          <line x1="17" y1="11" x2="23" y2="11"></line>
                                        </svg>';
                                        $MJTC_menu_title =  esc_html(__('Register', 'majestic-support'));
                                        $MJTC_class = 'mjtc-support-cp-menu';
                                        mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                                    }
                                endif;
                            } ?>
                        </ul>
                    </nav>
                    <?php
                } ?>
                <!-- cp links for agent -->
                <?php
                if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {  ?>
                    <nav class="mjtc-menu-links-wrp">
                        <ul>
                            <!-- Dashboard Links -->
                            <?php
                            $MJTC_count = 0;
                            if (majesticsupport::$_config['cplink_openticket_staff'] == 1):
                                $MJTC_ajaxid = "";
                                $MJTC_count++;
                                if(in_array('multiform',majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                                    //show popup in case of multiform
                                    $MJTC_ajaxid = "id=multiformpopup";
                                }
                                // controller add default form id, if single form
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffaddticket')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>';
                                $MJTC_menu_title =  esc_html(__('Submit Ticket', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class, $MJTC_ajaxid);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_myticket_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('My Tickets', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_ticketclosereasons_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticketclosereason', 'mjslay'=>'ticketclosereasons')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Ticket Close Reasons', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_smartreply_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'mjslay'=>'smartreplies')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Smart Reply', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_staff_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffs')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Agents', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_roles_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'role', 'mjslay'=>'roles')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Agent Roles', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_department_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'department', 'mjslay'=>'departments')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Departments', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('knowledgebase', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_category_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'stafflistcategories')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Categories', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('knowledgebase', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_kbarticle_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'stafflistarticles')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Knowledge Base', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('download', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_download_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'download', 'mjslay'=>'staffdownloads')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Downloads', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('announcement', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_announcement_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'staffannouncements')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="m12 8-9.04 9.06a2.82 2.82 0 1 0 3.98 3.98L16 12"></path><path d="m13 13 4 4"></path><path d="m13 9 4-4"></path><path d="M18 15h.01"></path><path d="M21 12v.01"></path><path d="M18 9h.01"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Announcements', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('faq', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_faq_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'faq', 'mjslay'=>'stafffaqs')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                                $MJTC_menu_title =  esc_html(__("FAQs", 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('helptopic', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_helptopic_agent'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'helptopic', 'mjslay'=>'agenthelptopics')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                                $MJTC_menu_title =  esc_html(__("Help Topics", 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;

                            if (in_array('cannedresponses', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_cannedresponses_agent'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'cannedresponses', 'mjslay'=>'agentcannedresponses')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__("Premade Responses", 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;

                            if (in_array('mail', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_mail_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'mail', 'mjslay'=>'inbox')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>';
                                $MJTC_menu_title =  esc_html(__('Mail', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;

                            if (in_array('banemail', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_banemail_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'banemail', 'mjslay'=>'banemails')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Banned Emails', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;

                            if (majesticsupport::$_config['cplink_staff_report_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'reports', 'mjslay'=>'staffreports')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Agent Reports', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;

                            if (majesticsupport::$_config['cplink_department_report_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'reports', 'mjslay'=>'departmentreports')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Department Reports', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('feedback', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_feedback_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'feedback', 'mjslay'=>'feedbacks')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('Agent Feedback', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_myprofile_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'myprofile')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
                                $MJTC_menu_title =  esc_html(__('My Profile', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_erasedata_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'gdpr', 'mjslay'=>'adderasedatarequest')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>';
                                $MJTC_menu_title =  esc_html(__('User Data', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (in_array('export', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_export_ticket_staff'] == 1):
                                $MJTC_count++;
                                $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'export', 'mjslay'=>'export')));
                                $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>';
                                $MJTC_menu_title =  esc_html(__('Export Ticket', 'majestic-support'));
                                $MJTC_class = 'mjtc-support-cp-menu';
                                mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                            endif;
                            
                            if (majesticsupport::$_config['cplink_login_logout_staff'] == 1){
                                if (!is_user_logged_in()):
                                    $MJTC_count++;
                                    $MJTC_menu_url = $MJTC_hreflink;
                                    $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M15 21h4a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-4"></path><polyline points="8 7 3 12 8 17"></polyline><line x1="3" y1="12" x2="15" y2="12"></line></svg>';
                                    $MJTC_menu_title =  esc_html(__('Log In', 'majestic-support'));
                                    $MJTC_class = 'mjtc-support-cp-menu';
                                    mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                                endif;
                                if (is_user_logged_in()):
                                    $MJTC_count++;
                                    $MJTC_menu_url = wp_logout_url( home_url() );
                                    $MJTC_image_path = '<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>';
                                    $MJTC_menu_title =  esc_html(__('Log Out', 'majestic-support'));
                                    $MJTC_class = 'mjtc-support-cp-menu';
                                    mjtc_printMenuLink($MJTC_menu_title, $MJTC_menu_url, $MJTC_image_path, $MJTC_class);
                                endif;
                            } ?>
                        </ul>
                    </nav>
                    <?php
                }
                if ($MJTC_count == 0) {
                    $majesticsupport_js ="
                        jQuery('.mjtc-cp-left').addClass('mjtc-dash-menu-link-hide');
                        jQuery('.mjtc-cp-right').addClass('mjtc-cp-right-fullwidth');

                    ";
                    wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
                }
            ?>
            </aside>
            <div class="mjtc-cp-right">
                    <div class="mjtc-support-wrapper">
                        <header class="mjtc-support-top-sec">
                            <div class="mjtc-support-cp-header-left">
                                <button class="mjtc-support-cp-mobile-menu-btn" id="mobile-toggle">
                                    <!-- fa-bars -->
                                    <svg class="svg-icon" viewBox="0 0 448 512"><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"></path></svg>
                                </button>
                                <div class="mjtc-support-cp-page-title">
                                    <h1 class="mjtc-support-main-heading">
                                        <?php echo esc_html(__("Welcome",'majestic-support')); ?>
                                        <?php
                                        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                                            echo ' '.esc_html(__("Back",'majestic-support')).', '.esc_html(majesticsupport::$_data[0]['agentname']);
                                        } else if (is_user_logged_in()) {
                                            echo ' '.esc_html(__("Back",'majestic-support')).', '.esc_html(majesticsupport::$_data[0]['username']);
                                        } ?>
                                    </h1>
                                    <p>
                                        <?php echo esc_html(__("Here's what's happening in your support queue today.",'majestic-support')); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mjtc-support-cp-header-actions">
                                <?php
                                if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                                    $MJTC_tkt_url = majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket'));
                                }else{
                                    $MJTC_tkt_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket'));
                                }
                                if (majesticsupport::$_config['tplink_tickets_'. $MJTC_linkname] == 1) { ?>
                                    <a class="mjtc-support-cp-btn mjtc-support-cp-btn-secondary mjtc-support-link" href="<?php echo esc_url($MJTC_tkt_url); ?>" data-tab-number="4">
                                        <svg viewBox="0 0 576 512"><path d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zm64 320H64V320c35.3 0 64 28.7 64 64zM64 192V128h64c0 35.3-28.7 64-64 64zM448 384c0-35.3 28.7-64 64-64v64H448zm64-192c-35.3 0-64-28.7-64-64h64v64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"></path></svg> <span><?php echo esc_html(__("All Tickets",'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                }
                                if (majesticsupport::$_config['tplink_openticket_'. $MJTC_linkname] == 1) {
                                    $MJTC_id = "";
                                    if(in_array('multiform',majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                                        //show popup in case of multiform
                                        $MJTC_id = "id=multiformpopup";
                                    }
                                    ?>
                                    <a <?php echo esc_attr($MJTC_id); ?>
                                        href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket'))); ?>" class="mjtc-support-button">
                                        <!-- fa-plus -->
                                        <svg class="svg-icon" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"></path></svg> <span><?php echo esc_html(__("Submit Ticket",'majestic-support')); ?></span>
                                    </a>
                                    <?php
                                } ?>
                            </div>
                        </header>
                    </div>
                    <!-- count boxes -->
                    <?php
                    if(majesticsupport::$_config['cplink_totalcount_'. $MJTC_linkname] == 1){
                        $MJTC_open_percentage = 0;
                        $MJTC_close_percentage = 0;
                        $MJTC_answered_percentage = 0;
                        $MJTC_overdue_percentage = 0;
                        $MJTC_allticket_percentage = 0;
                        if(isset($MJTC_data['count']) && $MJTC_data['count']['allticket'] > 0){ //to avoid division by zero error
                            $MJTC_open_percentage = round(($MJTC_data['count']['openticket'] / $MJTC_data['count']['allticket']) * 100);
                            $MJTC_close_percentage = round(($MJTC_data['count']['closedticket'] / $MJTC_data['count']['allticket']) * 100);
                            $MJTC_answered_percentage = round(($MJTC_data['count']['answeredticket'] / $MJTC_data['count']['allticket']) * 100);
                            if(isset($MJTC_data['count']['overdue'])){
                                $MJTC_overdue_percentage = round(($MJTC_data['count']['overdue'] / $MJTC_data['count']['allticket']) * 100);
                            }
                            $MJTC_allticket_percentage = 100;
                        }
                        if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()){
                            $MJTC_tkt_url = majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket'));
                        }else{
                            $MJTC_tkt_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket'));
                        } ?><!-- Stats Grid -->
                        <section class="mjtc-support-cp-stats-grid">
                            <a class="mjtc-support-cp-stat-card mjtc-support-link" href="<?php echo esc_url($MJTC_tkt_url); ?>" data-tab-number="4">
                                <div class="mjtc-support-cp-stat-icon blue">
                                    <svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                </div>
                                <div class="mjtc-support-cp-stat-content">
                                    <h3>
                                        <?php
                                        if(majesticsupport::$_config['count_on_myticket'] == 1){
                                            if(!is_user_logged_in()) {
                                                echo esc_html(__("NA",'majestic-support'));
                                            } else {
                                                $MJTC_allticket = 0;
                                                if(isset($MJTC_data['count']['allticket'])){
                                                    $MJTC_allticket = $MJTC_data['count']['allticket'];
                                                }
                                                echo esc_html($MJTC_allticket);
                                            }
                                        }
                                        ?>
                                    </h3>
                                    <p>
                                        <?php echo esc_html(__("Total Tickets",'majestic-support')); ?>
                                    </p>
                                </div>
                            </a>
                            <a class="mjtc-support-cp-stat-card mjtc-support-link" href="<?php echo esc_url($MJTC_tkt_url); ?>" data-tab-number="1">
                                <div class="mjtc-support-cp-stat-icon green">
                                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                </div>
                                <div class="mjtc-support-cp-stat-content">
                                    <h3>
                                        <?php
                                        if(majesticsupport::$_config['count_on_myticket'] == 1) {
                                            if(!is_user_logged_in()) {
                                                echo esc_html(__("NA",'majestic-support'));
                                            } else {
                                                $MJTC_openticket = 0;
                                                if(isset($MJTC_data['count']['openticket'])){
                                                    $MJTC_openticket = $MJTC_data['count']['openticket'];
                                                }
                                                echo esc_html($MJTC_openticket);
                                            }
                                        }
                                        ?>
                                    </h3>
                                    <p>
                                        <?php echo esc_html(__("Open Tickets",'majestic-support')); ?>
                                    </p>
                                </div>
                            </a>
                            <a class="mjtc-support-cp-stat-card mjtc-support-link" href="<?php echo esc_url($MJTC_tkt_url); ?>" data-tab-number="3">
                                <div class="mjtc-support-cp-stat-icon amber">
                                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </div>
                                <div class="mjtc-support-cp-stat-content">
                                    <h3>
                                        <?php
                                        if(majesticsupport::$_config['count_on_myticket'] == 1) { 
                                            if(!is_user_logged_in()) {
                                                echo esc_html(__("NA",'majestic-support'));
                                            } else {
                                                $MJTC_answeredticket = 0;
                                                if(isset($MJTC_data['count']['answeredticket'])){
                                                    $MJTC_answeredticket = $MJTC_data['count']['answeredticket'];
                                                }
                                                echo esc_html($MJTC_answeredticket);
                                            }
                                        }
                                        ?>
                                    </h3>
                                    <p>
                                        <?php echo esc_html(__("Answered Tickets",'majestic-support')); ?>
                                    </p>
                                </div>
                            </a>
                            <?php if(isset($MJTC_data['count']['overdue'])){ ?>
                                <a class="mjtc-support-cp-stat-card mjtc-support-link" href="<?php echo esc_url($MJTC_tkt_url); ?>" data-tab-number="5">
                                    <div class="mjtc-support-cp-stat-icon red">
                                        <svg viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-2.072-4.143-3-6 1.95 3.147 3.95 6.147 6 9.5A2.5 2.5 0 0 0 15.5 14.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2"></path><path d="M12 2a9 9 0 0 0-9 9 9.75 9.75 0 0 0 1.5 5.5"></path><path d="M12 22a9 9 0 0 0 9-9 9.75 9.75 0 0 0-1.5-5.5"></path></svg>
                                    </div>
                                    <div class="mjtc-support-cp-stat-content">
                                        <h3>
                                            <?php
                                            if(majesticsupport::$_config['count_on_myticket'] == 1) {
                                                if(!is_user_logged_in()) {
                                                    echo esc_html(__("NA",'majestic-support'));
                                                } else {
                                                    $MJTC_overdue = 0;
                                                    if(isset($MJTC_data['count']['overdue'])){
                                                        $MJTC_overdue = $MJTC_data['count']['overdue'];
                                                    }
                                                    echo esc_html($MJTC_overdue);
                                                }
                                            }
                                            ?>
                                        </h3>
                                        <p>
                                            <?php echo esc_html(__("Overdue Tickets",'majestic-support')); ?>
                                        </p>
                                    </div>
                                </a>
                            <?php } else { ?>
                                <a class="mjtc-support-cp-stat-card mjtc-support-link" href="<?php echo esc_url($MJTC_tkt_url); ?>" data-tab-number="2">
                                    <div class="mjtc-support-cp-stat-icon gray">
                                        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    </div>
                                    <div class="mjtc-support-cp-stat-content">
                                        <h3>
                                            <?php
                                            if(majesticsupport::$_config['count_on_myticket'] == 1) {
                                                if(!is_user_logged_in()) {
                                                    echo esc_html(__("NA",'majestic-support'));
                                                } else {
                                                    $MJTC_closedticket = 0;
                                                    if(isset($MJTC_data['count']['closedticket'])){
                                                        $MJTC_closedticket = $MJTC_data['count']['closedticket'];
                                                    }
                                                    echo esc_html($MJTC_closedticket);
                                                }
                                            }
                                            ?>
                                        </h3>
                                        <p>
                                            <?php echo esc_html(__("Closed Tickets",'majestic-support')); ?>
                                        </p>
                                    </div>
                                </a>
                            <?php } ?>
                        </section>
                        <?php
                    }?>
                    <!-- Content Grid (Tickets + Sidebar) -->
                    <div class="mjtc-support-cp-grid">
                        <!-- Left Column: Tickets & Team -->
                        <section class="mjtc-support-cp-column-stack">
                            <?php
                            if(!is_user_logged_in()) {
                                if(majesticsupport::$_config['cplink_latesttickets_user'] == 1){
                                    ?>
                                    <div class="mjtc-support-cp-card">
                                        <div class="mjtc-support-cp-tabs-header">
                                            <div class="mjtc-support-cp-tab-btn active" data-tab="tab-all-active-tickets"><?php echo esc_html(__("Recent Active Tickets",'majestic-support')); ?></div>
                                        </div>

                                        <!-- Tab 1: Recent Active Tickets -->
                                        <div id="tab-all-active-tickets" class="mjtc-support-cp-tab-pane active">
                                            <div class="mjtc-support-cp-ticket-table-container">
                                                <?php 
                                                $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket'));
                                                $MJTC_redirect_url = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_redirect_url);
                                                MJTC_layout::MJTC_getUserGuest($MJTC_redirect_url); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } ?>
                            <?php
                            if(isset($MJTC_data['agent-tickets']) && majesticsupport::$_config['cplink_latesttickets_staff'] == 1){
                                $MJTC_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1);
                                $MJTC_show_field = MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForListing(1);
                                ?>
                                <div class="mjtc-support-cp-card">
                                    <div class="mjtc-support-cp-tabs-header">
                                        <div class="mjtc-support-cp-tab-btn active" data-tab="tab-all-active-tickets"><?php echo esc_html(__("Recent Active Tickets",'majestic-support')); ?></div>
                                        <div class="mjtc-support-cp-tab-btn" data-tab="tab-assigned-tickets"><?php echo esc_html(__("My Assigned Tickets",'majestic-support')); ?></div>
                                    </div>

                                    <!-- Tab 1: Recent Active Tickets -->
                                    <div id="tab-all-active-tickets" class="mjtc-support-cp-tab-pane active">
                                        <div class="mjtc-support-cp-ticket-table-container">
                                            <?php if (!empty($MJTC_data['agent-tickets'])) { ?>
                                                <table class="mjtc-support-cp-ticket-table" id="table-active-tickets">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo esc_html(__("Ticket Details",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("Status",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("Priority",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("Updated",'majestic-support')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $MJTC_fields_array = array(); // Array for form fields
                                                        $MJTC_show_on_listing_arrays = array(); // Array for visible form fields
                                                        if (!empty($MJTC_data['agent-tickets'])) {
                                                            foreach($MJTC_data['agent-tickets'] as $MJTC_ticket){
                                                                // Check if the form fields are already array
                                                                if (!isset($MJTC_fields_array[$MJTC_ticket->multiformid])) {
                                                                    $MJTC_fields_array[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, $MJTC_ticket->multiformid);
                                                                }
                                                                if (!isset($MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid])) {
                                                                    $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForListing(1, $MJTC_ticket->multiformid);
                                                                }
                                                                // Now use the cached field array
                                                                $MJTC_field_array = $MJTC_fields_array[$MJTC_ticket->multiformid];
                                                                $MJTC_show_on_listing_array = $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid];
                                                                $MJTC_ticketviamail = '';
                                                                if ($MJTC_ticket->ticketviaemail == 1) {
                                                                    $MJTC_ticketviamail = esc_html(__('Created via Email', 'majestic-support'));
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td data-label="<?php echo esc_attr__('Ticket Details', 'majestic-support'); ?>">
                                                                        <?php
                                                                        if (isset($MJTC_field_array['subject'])) { ?>
                                                                            <a class="mjtc-support-cp-ticket-subject" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_ticket->id))); ?>">
                                                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->subject)); ?>
                                                                            </a>
                                                                            <?php
                                                                        } ?>
                                                                        <span class="mjtc-support-cp-ticket-id">
                                                                            #<?php echo esc_html($MJTC_ticket->ticketid); ?> • 
                                                                            <span class="mjtc-support-cp-ticket-name">
                                                                                <?php echo esc_html($MJTC_ticket->name); ?>
                                                                            </span>
                                                                        </span>
                                                                    </td>
                                                                    <td data-label="<?php echo esc_attr__('Status', 'majestic-support'); ?>">
                                                                        <span class="mjtc-support-cp-badge" style="background: <?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color: <?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                                        </span>
                                                                    </td>
                                                                    <td data-label="<?php echo esc_attr__('Priority', 'majestic-support'); ?>">
                                                                        <?php
                                                                        if (!empty($MJTC_show_on_listing_array['priority'])) { ?>
                                                                            <span class="mjtc-support-cp-badge" style="color: <?php echo esc_attr($MJTC_ticket->prioritycolour); ?>; font-weight: 600; font-size: .9em;">
                                                                                <span class="mjtc-support-cp-priority-dot" style="background: <?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;"></span>
                                                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                                            </span>
                                                                            <?php
                                                                        } ?>
                                                                    </td>
                                                                    <td data-label="<?php echo esc_attr__('Updated', 'majestic-support'); ?>" class="mjtc-support-cp-ticket-updated">
                                                                        <?php
                                                                        if (!empty($MJTC_ticket->updated) && $MJTC_ticket->updated != '0000-00-00 00:00:00') {
                                                                            echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->updated),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support'));
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        } else { ?>
                                                            <tr>
                                                                <td colspan="4">
                                                                    <div class="mjtc-support-cp-empty-state">
                                                                        <!-- Icon: Ticket Simple -->
                                                                        <svg viewBox="0 0 576 512"><path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64v38.5c0 17.4 14.1 31.5 31.5 31.5c17.4 0 31.5-14.1 31.5-31.5V128c0-35.3 28.7-64 64-64H512c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H320c-35.3 0-64-28.7-64-64V345.5c0-17.4-14.1-31.5-31.5-31.5c-17.4 0-31.5 14.1-31.5 31.5V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z"></path></svg>
                                                                        <div><?php echo esc_html__('No tickets found.', 'majestic-support'); ?></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        } ?>
                                                    </tbody>
                                                </table>
                                                <?php 
                                            } else { ?>
                                                <div class="mjtc-support-cp-empty-state">
                                                    <svg viewBox="0 0 576 512"><path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64v38.5c0 17.4 14.1 31.5 31.5 31.5c17.4 0 31.5-14.1 31.5-31.5V128c0-35.3 28.7-64 64-64H512c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H320c-35.3 0-64-28.7-64-64V345.5c0-17.4-14.1-31.5-31.5-31.5c-17.4 0-31.5 14.1-31.5 31.5V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z"></path></svg>
                                                    <div><?php echo esc_html__('No ticket found yet.', 'majestic-support'); ?></div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                    </div>

                                    <!-- Tab 2: My Assigned Tickets -->
                                    <div id="tab-assigned-tickets" class="mjtc-support-cp-tab-pane">
                                        <div class="mjtc-support-cp-card-header">
                                            <div class="mjtc-support-cp-card-header-actions">
                                                <span class="mjtc-support-cp-badge mjtc-support-cp-badge-neutral" style="margin-right: 12px;">
                                                    <?php
                                                    if (majesticsupport::$_data['assigned_pending_tickets']) {
                                                        echo esc_html(majesticsupport::$_data['assigned_pending_tickets']).' '. esc_html(__('Pending', 'majestic-support'));
                                                    } ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mjtc-support-cp-ticket-table-container">
                                            <?php
                                            if (!empty($MJTC_data['agent-tickets'])) { ?>
                                                <table class="mjtc-support-cp-ticket-table" id="table-assigned-tickets">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo esc_html(__("Ticket Details",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("State",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("SLA Due",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("Quick Action",'majestic-support')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $MJTC_fields_array = array(); // Array for form fields
                                                        $MJTC_show_on_listing_arrays = array(); // Array for visible form fields
                                                        foreach($MJTC_data['agent-tickets'] as $MJTC_ticket){
                                                            // Check if the form fields are already array
                                                            if (!isset($MJTC_fields_array[$MJTC_ticket->multiformid])) {
                                                                $MJTC_fields_array[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, $MJTC_ticket->multiformid);
                                                            }
                                                            if (!isset($MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid])) {
                                                                $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForListing(1, $MJTC_ticket->multiformid);
                                                            }
                                                            // Now use the cached field array
                                                            $MJTC_field_array = $MJTC_fields_array[$MJTC_ticket->multiformid];
                                                            $MJTC_show_on_listing_array = $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid];
                                                            $MJTC_ticketviamail = '';
                                                            if ($MJTC_ticket->ticketviaemail == 1) {
                                                                $MJTC_ticketviamail = esc_html(__('Created via Email', 'majestic-support'));
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td data-label="<?php echo esc_attr__('Ticket Details', 'majestic-support'); ?>">
                                                                    <?php
                                                                    if (isset($MJTC_field_array['subject'])) { ?>
                                                                        <a class="mjtc-support-cp-ticket-subject" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_ticket->id))); ?>">
                                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->subject)); ?>
                                                                        </a>
                                                                        <?php
                                                                    } ?>
                                                                    <span class="mjtc-support-cp-ticket-id">
                                                                        #<?php echo esc_html($MJTC_ticket->ticketid); ?> • 
                                                                        <span style="color: var(--mjtc-color-1); font-weight: 500;">
                                                                            <?php echo esc_html($MJTC_ticket->name); ?>
                                                                        </span>
                                                                    </span>
                                                                </td>
                                                                <td data-label="<?php echo esc_attr__('State', 'majestic-support'); ?>">
                                                                    <span class="mjtc-support-cp-badge" style="background: <?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color: <?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                                    </span>
                                                                </td>
                                                                <?php
                                                                if (!empty($MJTC_show_on_listing_array['priority'])) { ?>
                                                                    <td data-label="<?php echo esc_attr__('SLA Due', 'majestic-support'); ?>">
                                                                        <span style="color: <?php echo esc_attr($MJTC_ticket->prioritycolour); ?>; font-weight: 600; font-size: .9em;">
                                                                            <span class="mjtc-support-cp-priority-dot" style="background: <?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;"></span>
                                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                                        </span>
                                                                    </td>
                                                                    <?php
                                                                } ?>
                                                                <td data-label="<?php echo esc_attr__('Quick Action', 'majestic-support'); ?>" style="color: var(--mjtc-color-4); font-size: .87em;">
                                                                    <a class="mjtc-support-cp-btn mjtc-support-cp-btn-secondary" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_ticket->id))); ?>">
                                                                        <?php echo esc_html(__('Reply', 'majestic-support')); ?>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            <?php
                                            } else { ?>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="mjtc-support-cp-empty-state">
                                                            <!-- Icon: Ticket Simple -->
                                                            <svg viewBox="0 0 576 512"><path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64v38.5c0 17.4 14.1 31.5 31.5 31.5c17.4 0 31.5-14.1 31.5-31.5V128c0-35.3 28.7-64 64-64H512c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H320c-35.3 0-64-28.7-64-64V345.5c0-17.4-14.1-31.5-31.5-31.5c-17.4 0-31.5 14.1-31.5 31.5V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z"></path></svg>
                                                            <div><?php echo esc_html__('No tickets found.', 'majestic-support'); ?></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <?php
                            if(isset($MJTC_data['user-tickets']) && majesticsupport::$_config['cplink_latesttickets_user'] == 1) {
                                ?>
                                <div class="mjtc-support-cp-card">
                                    <div class="mjtc-support-cp-tabs-header">
                                        <div class="mjtc-support-cp-tab-btn active" data-tab="tab-all-active-tickets"><?php echo esc_html(__("Recent Active Tickets",'majestic-support')); ?></div>
                                    </div>

                                    <!-- Tab 1: Recent Active Tickets -->
                                    <div id="tab-all-active-tickets" class="mjtc-support-cp-tab-pane active">
                                        <div class="mjtc-support-cp-ticket-table-container">
                                            <?php if (!empty($MJTC_data['user-tickets'])) { ?>
                                                <table class="mjtc-support-cp-ticket-table" id="table-active-tickets">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo esc_html(__("Ticket Details",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("Status",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("Priority",'majestic-support')); ?></th>
                                                            <th><?php echo esc_html(__("Updated",'majestic-support')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $MJTC_fields_array = array(); // Array for form fields
                                                        $MJTC_show_on_listing_arrays = array(); // Array for visible form fields
                                                        if (!empty($MJTC_data['user-tickets'])) {
                                                            foreach($MJTC_data['user-tickets'] as $MJTC_ticket){
                                                                // Check if the form fields are already array
                                                                if (!isset($MJTC_fields_array[$MJTC_ticket->multiformid])) {
                                                                    $MJTC_fields_array[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, $MJTC_ticket->multiformid);
                                                                }
                                                                if (!isset($MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid])) {
                                                                    $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForListing(1, $MJTC_ticket->multiformid);
                                                                }
                                                                // Now use the cached field array
                                                                $MJTC_field_array = $MJTC_fields_array[$MJTC_ticket->multiformid];
                                                                $MJTC_show_on_listing_array = $MJTC_show_on_listing_arrays[$MJTC_ticket->multiformid];
                                                                $MJTC_ticketviamail = '';
                                                                if ($MJTC_ticket->ticketviaemail == 1) {
                                                                    $MJTC_ticketviamail = esc_html(__('Created via Email', 'majestic-support'));
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td data-label="<?php echo esc_attr__('Ticket Details', 'majestic-support'); ?>">
                                                                        <?php
                                                                        if (isset($MJTC_field_array['subject'])) { ?>
                                                                            <a class="mjtc-support-cp-ticket-subject" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_ticket->id))); ?>">
                                                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->subject)); ?>
                                                                            </a>
                                                                            <?php
                                                                        } ?>
                                                                        <span class="mjtc-support-cp-ticket-id">
                                                                            #<?php echo esc_html($MJTC_ticket->ticketid); ?> • 
                                                                            <span style="color: var(--mjtc-color-1); font-weight: 500;">
                                                                                <?php echo esc_html($MJTC_ticket->name); ?>
                                                                            </span>
                                                                        </span>
                                                                    </td>
                                                                    <td data-label="<?php echo esc_attr__('Status', 'majestic-support'); ?>">
                                                                        <span class="mjtc-support-cp-badge" style="background: <?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>; color: <?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                                                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->statustitle)); ?>
                                                                        </span>
                                                                    </td>
                                                                    <td data-label="<?php echo esc_attr__('Priority', 'majestic-support'); ?>">
                                                                        <?php
                                                                        if (!empty($MJTC_show_on_listing_array['priority'])) { ?>
                                                                            <span style="color: <?php echo esc_attr($MJTC_ticket->prioritycolour); ?>; font-weight: 600; font-size: .9em;">
                                                                                <span class="mjtc-support-cp-priority-dot" style="background: <?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;"></span>
                                                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                                                            </span>
                                                                            <?php
                                                                        } ?>
                                                                    </td>
                                                                    <td data-label="<?php echo esc_attr__('Updated', 'majestic-support'); ?>" style="color: var(--mjtc-color-4); font-size: .87em;">
                                                                        <?php
                                                                        if (!empty($MJTC_ticket->updated) && $MJTC_ticket->updated != '0000-00-00 00:00:00') {
                                                                            echo esc_html(human_time_diff(MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->updated),MJTC_majesticsupportphplib::MJTC_strtotime(date_i18n("Y-m-d H:i:s")))).' '. esc_html(__('ago', 'majestic-support'));
                                                                        } ?>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        } else { ?>
                                                            <tr>
                                                                <td colspan="4">
                                                                    <div class="mjtc-support-cp-empty-state">
                                                                        <!-- Icon: Ticket Simple -->
                                                                        <svg viewBox="0 0 576 512"><path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64v38.5c0 17.4 14.1 31.5 31.5 31.5c17.4 0 31.5-14.1 31.5-31.5V128c0-35.3 28.7-64 64-64H512c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H320c-35.3 0-64-28.7-64-64V345.5c0-17.4-14.1-31.5-31.5-31.5c-17.4 0-31.5 14.1-31.5 31.5V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z"></path></svg>
                                                                        <div><?php echo esc_html__('No tickets found.', 'majestic-support'); ?></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        } ?>
                                                    </tbody>
                                                </table>
                                                <?php 
                                            } else { ?>
                                                <div class="mjtc-support-cp-empty-state">
                                                    <svg viewBox="0 0 576 512"><path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64v38.5c0 17.4 14.1 31.5 31.5 31.5c17.4 0 31.5-14.1 31.5-31.5V128c0-35.3 28.7-64 64-64H512c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H320c-35.3 0-64-28.7-64-64V345.5c0-17.4-14.1-31.5-31.5-31.5c-17.4 0-31.5 14.1-31.5 31.5V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z"></path></svg>
                                                    <div><?php echo esc_html__('No ticket found yet.', 'majestic-support'); ?></div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <!-- Recent Customer Feedback -->
                            <?php
                            if (majesticsupport::$_config['cplink_recent_feedback_staff'] == 1 && in_array('feedback', majesticsupport::$_active_addons) && in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {  ?>
                                <div class="mjtc-support-cp-card">
                                    <div class="mjtc-support-cp-card-header">
                                        <div class="mjtc-support-cp-card-title"><?php echo esc_html__('Recent Feedback', 'majestic-support'); ?></div>
                                        <span style="font-size: .8em; color: #92400e; background: #fef3c7; padding: 4px 8px; border-radius: 6px;">
                                            <?php echo esc_html__('Avg:', 'majestic-support') . ' ' . esc_html(majesticsupport::$_data['avg_feedback'] ?? '0.0'); ?> 
                                            <svg class="filled" style="color: #fbbf24; width:12px; height:12px;" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        </span>
                                    </div>
                                    <div class="mjtc-support-cp-feedback-list">
                                        <?php 
                                        $MJTC_feedbacks = majesticsupport::$_data['recent_feedback'] ?? [];
                                        if (!empty($MJTC_feedbacks)) :
                                            foreach ($MJTC_feedbacks as $MJTC_feedback) : 
                                                $MJTC_rating = (int)$MJTC_feedback->rating;
                                        ?>
                                            <div class="mjtc-support-cp-feedback-item">
                                                <div class="mjtc-support-cp-feedback-header">
                                                    <div class="mjtc-support-cp-star-rating">
                                                        <?php 
                                                        for ($MJTC_i = 1; $MJTC_i <= 5; $MJTC_i++) {
                                                            $MJTC_star_color = ($MJTC_i <= $MJTC_rating) ? '#fbbf24' : '#e5e7eb';
                                                            echo '<svg style="width: 14px; height: 14px; fill: '.esc_html($MJTC_star_color) .';" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="mjtc-support-cp-feedback-date">
                                                        <?php echo esc_html(human_time_diff(strtotime($MJTC_feedback->created), current_time('timestamp'))) . ' ' . esc_html__('ago', 'majestic-support'); ?>
                                                    </div>
                                                </div>
                                                <div class="mjtc-support-cp-feedback-text">
                                                    "<?php echo esc_html($MJTC_feedback->remarks); ?>"
                                                </div>
                                                <div class="mjtc-support-cp-feedback-user">
                                                    <?php
                                                    if ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {  ?>
                                                        <svg style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                        <?php
                                                    } else { ?>
                                                        <svg style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2;" viewBox="0 0 24 24"><path d="M4 7h16v4a2 2 0 0 1 0 4v4H4v-4a2 2 0 0 1 0-4V7z"></path><line x1="9" y1="12" x2="15" y2="12"></line></svg>
                                                        <?php
                                                    } ?>
                                                    <?php echo esc_html($MJTC_feedback->customer_name); ?>
                                                </div>
                                            </div>
                                        <?php 
                                            endforeach; 
                                        else : ?>
                                            <div class="mjtc-support-cp-empty-state">
                                                <!-- Icon: Comment Dots (Regular) -->
                                                <svg viewBox="0 0 512 512"><path d="M123.6 391.3c12.9-9.4 29.6-11.8 44.6-6.4c26.5 9.6 56.2 15.1 87.8 15.1c124.7 0 208-80.5 208-160s-83.3-160-208-160S48 160.5 48 240c0 32 12.4 61.7 34 85.3c6.3 6.9 7.8 16.9 4.1 25.5l-21.7 50.3c-1.7 3.9-2.7 8.1-2.9 12.4v.8c.1 6.3 3.3 12.2 8.4 15.6s11.8 4 17.2 1.8l68.5-27.4zM512 240c0 113.6-114.6 192-232 216V456v.8c0 14.8-12 26.8-26.8 26.8c-2.4 0-4.8-.3-7.2-1l-50.4 20.2c-29.5 11.8-62.8-5.3-70.1-36c-.5-2.3-1-4.7-1.3-7.1C55.2 429.3 0 351.6 0 240C0 107.5 114.6 0 256 0S512 107.5 512 240zM176 256a24 24 0 1 0 0-48 24 24 0 1 0 0 48zm80 0a24 24 0 1 0 0-48 24 24 0 1 0 0 48zm80 0a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"></path></svg>
                                                <div><?php echo esc_html__('No recent feedback found.', 'majestic-support'); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                            // Weekly Leaderboard
                            if ( majesticsupport::$_config['cplink_weekly_leaderboard_staff'] == 1 && in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {  ?>
                                <div class="mjtc-support-cp-card">
                                    <div class="mjtc-support-cp-card-header">
                                        <div class="mjtc-support-cp-card-title"><?php echo esc_html__('Weekly Leaderboard', 'majestic-support'); ?></div>
                                        <div class="mjtc-tkt-sub"><?php echo esc_html__('Top Agents', 'majestic-support'); ?></div>
                                    </div>
                                    
                                    <div class="mjtc-support-cp-leaderboard-list">
                                        <?php 
                                        $MJTC_leaderboard = majesticsupport::$_data['leaderboard_agents'] ?? [];
                                        $MJTC_uid = MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid();
                                        $MJTC_staffid = MJTC_includer::MJTC_getModel('agent')->getStaffId($MJTC_uid);
                                        if (!empty($MJTC_leaderboard)) :
                                            foreach ($MJTC_leaderboard as $MJTC_index => $MJTC_agent) : 
                                                $MJTC_rank = $MJTC_index + 1;
                                                $MJTC_full_name = esc_html($MJTC_agent->firstname . ' ' . $MJTC_agent->lastname); ?>
                                                <div class="mjtc-support-cp-leaderboard-item">
                                                    <div class="mjtc-support-cp-rank mjtc-support-cp-rank-<?php echo esc_attr( (int) $MJTC_rank ); ?>">
                                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                                            <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                                            <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                                            <path d="M4 22h16"></path>
                                                            <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                                                            <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                                                            <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                                                        </svg>
                                                    </div>
                                                    
                                                    <div class="mjtc-support-cp-leaderboard-user">
                                                        <?php echo wp_kses(MJTC_get_avatar($MJTC_agent->staffuid, 'mjtc-support-cp-user-avatar'), MJTC_ALLOWED_TAGS); ?>
                                                        <div class="user-name">
                                                            <?php echo esc_html($MJTC_full_name); ?>
                                                            <?php if($MJTC_agent->id == $MJTC_staffid) echo ' <small>(' . esc_html__('You', 'majestic-support') . ')</small>'; ?>
                                                        </div>
                                                    </div>

                                                    <div class="mjtc-support-cp-leaderboard-score">
                                                        <?php echo esc_html((int)$MJTC_agent->solved_count); ?> 
                                                        <span><?php echo esc_html__('Solved', 'majestic-support'); ?></span>
                                                    </div>
                                                </div>
                                                <?php
                                            endforeach;
                                        else : ?>
                                            <div class="mjtc-support-cp-empty-state">
                                                <!-- Icon: History -->
                                                <svg viewBox="0 0 576 512"><path d="M400 0H176c-26.5 0-48.1 21.8-47.1 48.2c.2 5.3 .4 10.6 .7 15.8H24C10.7 64 0 74.7 0 88c0 92.6 33.5 157 78.5 200.7c44.3 43.1 98.3 64.8 138.1 75.8c23.4 6.5 39.4 26 39.4 45.6c0 20.9-17 37.9-37.9 37.9H192c-17.7 0-32 14.3-32 32s14.3 32 32 32H384c17.7 0 32-14.3 32-32s-14.3-32-32-32H357.9C337 448 320 431 320 410.1c0-19.6 15.9-39.2 39.4-45.6c39.9-11 93.9-32.7 138.2-75.8C542.5 245 576 180.6 576 88c0-13.3-10.7-24-24-24H446.7c.3-5.2 .5-10.4 .7-15.8C448.1 21.8 426.5 0 400 0zM48.9 112h84.4c9.1 90.1 29.2 150.3 51.9 190.6c-24.9-11-50.8-26.5-73.2-48.3c-32-31.1-58-76-63.1-142.3zM400 64c5.9 0 11.6 .3 17.1 .8c-.5 5.2-.9 10.2-1.2 15.2H160.1c-.3-5-.7-10-1.2-15.2c5.5-.5 11.1-.8 17.1-.8H400zM390.8 302.6c22.6-40.3 42.8-100.5 51.9-190.6h84.4c-5.1 66.3-31.1 111.2-63.1 142.3c-22.4 21.8-48.3 37.3-73.2 48.3z"></path></svg>
                                                <div><?php echo esc_html__("Leaderboard is empty for this week.", 'majestic-support'); ?></div>
                                            </div>
                                            <?php
                                        endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                            if ( majesticsupport::$_config['cplink_vip_clients_watchlist_staff'] == 1 && in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {  ?>
                                <!-- NEW: VIP Clients Watchlist -->
                                <div class="mjtc-support-cp-card">
                                    <div class="mjtc-support-cp-card-header">
                                        <div class="mjtc-support-cp-card-title"><?php echo esc_html__('VIP Clients Watchlist', 'majestic-support'); ?></div>
                                        <?php 
                                        $MJTC_vip_list = majesticsupport::$_data['vip_watchlist'] ?? [];
                                        $MJTC_vip_count = count($MJTC_vip_list);
                                        ?>
                                        <span class="mjtc-support-cp-badge mjtc-support-cp-badge-neutral">
                                            <?php echo esc_html( (int) $MJTC_vip_count . ' ' . __( 'Active', 'majestic-support' ) ); ?>
                                        </span>
                                    </div>
                                    <div class="mjtc-support-cp-vip-list">
                                        <?php if (!empty($MJTC_vip_list)) : ?>
                                            <?php foreach ($MJTC_vip_list as $MJTC_vip) : ?>
                                                <div class="mjtc-support-cp-vip-card">
                                                    <div class="mjtc-support-cp-vip-info">
                                                        <h5><?php echo esc_html($MJTC_vip->name); ?></h5>
                                                        <span><?php echo esc_html__('Ticket', 'majestic-support') . ' #' . esc_html($MJTC_vip->ticketid); ?></span>
                                                    </div>
                                                    <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_vip->id))); ?>" class="mjtc-support-cp-btn-icon-only">
                                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                                            <polyline points="12 5 19 12 12 19"></polyline>
                                                        </svg>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <div class="mjtc-support-cp-empty-state" style="width: 100%;">
                                                <!-- Icon: Eye Slash -->
                                                <svg viewBox="0 0 640 512"><path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.8 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.8 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z"></path></svg>
                                                <div><?php echo esc_html__('No VIP clients currently watched.', 'majestic-support'); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Resources Center (Tabs) -->
                                <?php
                            }
                            if ((in_array('faq', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestfaqs_'. $MJTC_linkname] == 1) || (in_array('announcement', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestannouncements_'. $MJTC_linkname] == 1) || (in_array('download', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestdownloads_'. $MJTC_linkname] == 1) || (in_array('knowledgebase', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestkb_'. $MJTC_linkname] == 1) ) { ?>
                                <div class="mjtc-support-cp-card">
                                    <div class="mjtc-support-cp-tabs-header">
                                        <?php if(in_array('knowledgebase', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestkb_'. $MJTC_linkname] == 1): 
                                            ?>
                                            <div class="mjtc-support-cp-tab-btn active" data-tab="tab-kb"><?php echo esc_html__("Knowledge Base", "majestic-support"); ?></div>
                                        <?php endif; ?>

                                        <?php if(in_array('download', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestdownloads_'. $MJTC_linkname] == 1): ?>
                                            <div class="mjtc-support-cp-tab-btn" data-tab="tab-downloads"><?php echo esc_html__("Downloads", "majestic-support"); ?></div>
                                        <?php endif; ?>

                                        <?php if(in_array('announcement', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestannouncements_'. $MJTC_linkname] == 1): ?>
                                            <div class="mjtc-support-cp-tab-btn" data-tab="tab-announcements"><?php echo esc_html__("Announcements", "majestic-support"); ?></div>
                                        <?php endif; ?>

                                        <?php if(in_array('faq', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestfaqs_'. $MJTC_linkname] == 1): ?>
                                            <div class="mjtc-support-cp-tab-btn" data-tab="tab-faqs"><?php echo esc_html__("FAQs", "majestic-support"); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Tab 1: Knowledge Base -->
                                    <?php if(in_array('knowledgebase', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestkb_'. $MJTC_linkname] == 1) { ?>
                                        <div id="tab-kb" class="mjtc-support-cp-tab-pane active">
                                            <div class="mjtc-support-cp-article-list">
                                                <?php
                                                if (!empty($MJTC_data['latest-articles'])) {
                                                    foreach($MJTC_data['latest-articles'] as $MJTC_article): ?>
                                                        <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase', 'mjslay'=>'articledetails', 'majesticsupportid'=>$MJTC_article->articleid))); ?>" class="mjtc-support-cp-article-item">
                                                            <div class="mjtc-support-cp-article-icon">
                                                                <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                                            </div>
                                                            <div class="mjtc-support-cp-article-content">
                                                                <div><?php echo esc_html($MJTC_article->subject); ?></div>
                                                                <div>
                                                                    <?php
                                                                    echo esc_html__( 'Updated', 'majestic-support' ) . ' ' .
                                                                         esc_html( human_time_diff( strtotime( $MJTC_article->created ), current_time( 'timestamp' ) ) ) . ' ' .
                                                                         esc_html__( 'ago', 'majestic-support' );
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <span style="font-size:.8em; color:var(--mjtc-color-1);">
                                                                <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                                            </span>
                                                        </a>
                                                        <?php
                                                    endforeach;
                                                } else { ?>
                                                    <div class="mjtc-support-cp-empty-state">
                                                        <svg viewBox="0 0 576 512"><path d="M249.6 471.5c10.8 3.8 22.4-4.1 22.4-15.5V78.6c0-4.2-1.6-8.4-5-11C247.4 52 202.4 32 144 32C93.5 32 46.3 45.3 18.1 56.1C6.8 60.5 0 71.7 0 83.8V454.1c0 11.9 12.8 20.2 24.1 16.5C55.6 460.1 105.5 448 144 448c33.9 0 79 14 105.6 23.5zm76.8 0C353 462 398.1 448 432 448c38.5 0 88.4 12.1 119.9 22.6c11.3 3.8 24.1-4.6 24.1-16.5V83.8c0-12.1-6.8-23.3-18.1-27.6C529.7 45.3 482.5 32 432 32c-58.4 0-103.4 20-123 35.6c-3.3 2.6-5 6.8-5 11V456c0 11.4 11.7 19.3 22.4 15.5z"></path></svg>
                                                        <div><?php echo esc_html__('No articles available.', 'majestic-support'); ?></div>
                                                    </div>
                                                    <?php
                                                } ?>
                                            </div>
                                            <?php
                                            if (!empty($MJTC_data['latest-articles'])) { ?>
                                                <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'knowledgebase','mjslay'=>'userknowledgebase'))); ?>">
                                                    <button class="mjtc-support-cp-btn mjtc-support-cp-btn-secondary" style="width: 100%; margin-top: 16px;"><?php echo esc_html__("Browse All Articles", "majestic-support"); ?></button>
                                                </a>
                                                <?php
                                            } ?>
                                        </div>
                                    <?php } ?>

                                    <!-- Tab 2: Downloads -->
                                    <?php if(in_array('download', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestdownloads_'. $MJTC_linkname] == 1) { ?>
                                        <div id="tab-downloads" class="mjtc-support-cp-tab-pane">
                                            <?php
                                            if (!empty($MJTC_data['latest-downloads'])) {
                                                foreach($MJTC_data['latest-downloads'] as $MJTC_download): 
                                                    $MJTC_nonce = wp_create_nonce("get-download-by-id-".$MJTC_download->downloadid); ?>
                                                    <div class="mjtc-support-cp-download-item">
                                                        <div class="mjtc-support-cp-download-info">
                                                            <div class="mjtc-support-cp-file-icon">
                                                                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                            </div>
                                                            <div>
                                                                <div style="font-size: .9em; font-weight:500; cursor:pointer;" onclick="getDownloadById(<?php echo esc_js($MJTC_download->downloadid) ?>, '<?php echo esc_js($MJTC_nonce) ?>')">
                                                                    <?php echo esc_html($MJTC_download->title); ?>
                                                                </div>
                                                                <div style="font-size:.8em; color:var(--mjtc-color-4);"><?php echo esc_html(__('Files', 'majestic-support')).' ('.esc_html($MJTC_download->totalattachment).')'; ?></div>
                                                            </div>
                                                        </div>
                                                        <button class="mjtc-support-cp-btn mjtc-support-cp-btn-secondary" onclick="getDownloadById(<?php echo esc_js($MJTC_download->downloadid) ?>, '<?php echo esc_js($MJTC_nonce) ?>')">
                                                            <svg viewBox="0 0 24 24" width="16" height="16"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                                <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'download','mjslay'=>'downloads'))); ?>">
                                                    <button class="mjtc-support-cp-btn mjtc-support-cp-btn-secondary" style="width: 100%; margin-top: 16px;"><?php echo esc_html__("View All Downloads", "majestic-support"); ?></button>
                                                </a>
                                                <?php
                                            } else { ?>
                                                <div class="mjtc-support-cp-empty-state">
                                                    <svg viewBox="0 0 512 512"><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zM432 456c-13.3 0-24-10.7-24-24s10.7-24 24-24s24 10.7 24 24s-10.7 24-24 24z"></path></svg>
                                                    <div><?php echo esc_html__('No downloads available.', 'majestic-support'); ?></div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                    <?php } ?>
                                    
                                    <!-- Tab 3: Announcements -->
                                    <?php if(in_array('announcement', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestannouncements_'. $MJTC_linkname] == 1) { ?>
                                        <div id="tab-announcements" class="mjtc-support-cp-tab-pane">
                                            <?php
                                            if (!empty($MJTC_data['latest-announcements'])) {
                                                foreach($MJTC_data['latest-announcements'] as $MJTC_announcement): ?>
                                                    <div class="mjtc-support-cp-announcement-item">
                                                        <div class="mjtc-support-cp-announcement-meta">
                                                            <span>
                                                                <svg style="width:12px; height:12px; margin-right:4px;" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> 
                                                                <?php echo esc_html(date_i18n(get_option('date_format'), strtotime(esc_html($MJTC_announcement->created)))); ?>
                                                            </span>
                                                            <span class="mjtc-support-cp-badge mjtc-support-cp-badge-warning">Important</span>
                                                        </div>
                                                        <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement', 'mjslay'=>'announcementdetails', 'majesticsupportid'=>$MJTC_announcement->id))); ?>">
                                                            <div class="mjtc-support-cp-announcement-title"><?php echo esc_html($MJTC_announcement->title); ?></div>
                                                        </a>
                                                        <div style="font-size: .85em; color:var(--mjtc-color-4);">
                                                            <?php echo esc_html(MJTC_majesticsupportphplib::MJTC_strip_tags($MJTC_announcement->description)); ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'announcement','mjslay'=>'announcements'))); ?>">
                                                    <button class="mjtc-support-cp-btn mjtc-support-cp-btn-secondary" style="width: 100%; margin-top: 16px;"><?php echo esc_html__("View All Announcements", "majestic-support"); ?></button>
                                                </a>
                                                <?php
                                            } else { ?>
                                                <div class="mjtc-support-cp-empty-state">
                                                    <svg viewBox="0 0 576 512"><path d="M569.5 73C582.5 91.5 579.5 117.2 562.6 132.3L512 177.3V334.7L562.6 379.7C579.5 394.8 582.5 420.5 569.5 439C558.1 455.1 539.1 464 519.5 464H512C494.3 464 480 449.7 480 432V80C480 62.3 494.3 48 512 48H519.5C539.1 48 558.1 56.9 569.5 73H569.5zM0 160C0 142.3 14.3 128 32 128H76.7C124.9 128 170.8 147.1 204.9 181.2L262.1 238.4C268.3 244.6 272 253.1 272 262V336C272 362.5 250.5 384 224 384H32C14.3 384 0 369.7 0 352V160zM224 416C224 433.7 209.7 448 192 448H48C21.5 448 0 469.5 0 496C0 504.8 7.2 512 16 512H192C236.2 512 272 476.2 272 432V416H224zM320 128C320 110.3 334.3 96 352 96H400C417.7 96 432 110.3 432 128V384C432 401.7 417.7 416 400 416H352C334.3 416 320 401.7 320 384V128z"></path></svg>
                                                    <div><?php echo esc_html__('No new announcements.', 'majestic-support'); ?></div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                    <?php } ?>
                                    
                                    <!-- Tab 4: FAQs -->
                                    <?php if(in_array('faq', majesticsupport::$_active_addons) && majesticsupport::$_config['cplink_latestfaqs_'. $MJTC_linkname] == 1) { ?>
                                        <div id="tab-faqs" class="mjtc-support-cp-tab-pane">
                                            <?php
                                            if (!empty($MJTC_data['latest-faqs'])) {
                                                foreach($MJTC_data['latest-faqs'] as $MJTC_faq): ?>
                                                    <div class="mjtc-support-cp-faq-item">
                                                        <div class="mjtc-support-cp-faq-question">
                                                            <?php echo esc_html($MJTC_faq->subject); ?>
                                                            <svg viewBox="0 0 24 24" width="18" height="18"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                        </div>
                                                        <div class="mjtc-support-cp-faq-answer">
                                                          <?php echo esc_html(MJTC_majesticsupportphplib::MJTC_strip_tags($MJTC_faq->content)); ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'faq','mjslay'=>'faqs'))); ?>">
                                                    <button class="mjtc-support-cp-btn mjtc-support-cp-btn-secondary" style="width: 100%; margin-top: 16px;"><?php echo esc_html__("View All FAQs", "majestic-support"); ?></button>
                                                </a>
                                                <?php 
                                            } else { ?>
                                                <div class="mjtc-support-cp-empty-state">
                                                    <!-- Icon: Ticket Simple -->
                                                    <svg viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"></path></svg>
                                                    <div><?php echo esc_html__('No FAQs configured.', 'majestic-support'); ?></div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                            } ?>
                        </section>
                        <!-- Right Column: Actions & Widgets -->
                        <aside class="mjtc-support-cp-column-stack widgets-column">
                            <!-- Quick Actions -->
                            <?php 
                            if (majesticsupport::$_config['cplink_quick_actions_'. $MJTC_linkname] == 1) { ?>
                                <div class="mjtc-support-cp-card">
                                    <div class="mjtc-support-cp-card-header">
                                        <div class="mjtc-support-cp-card-title">
                                            <?php echo esc_html(__('Quick Actions', 'majestic-support')); ?>
                                        </div>
                                    </div>
                                    <div class="mjtc-support-cp-action-list">
                                        <?php
                                        $MJTC_ajaxid = "";
                                        if(in_array('multiform',majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                                            //show popup in case of multiform
                                            $MJTC_ajaxid = "id=multiformpopup";
                                        }
                                        if(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) {
                                            $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffaddticket')));
                                        } else {
                                            $MJTC_menu_url = esc_url(majesticsupport::makeUrl(array('mjsmod' => 'ticket', 'mjslay' => 'addticket')));
                                        } ?>
                                        <a href="<?php echo esc_url($MJTC_menu_url); ?>" <?php echo esc_attr($MJTC_ajaxid); ?>>
                                            <svg class="svg-icon" viewBox="0 0 512 512"><path d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"></path></svg>
                                            <?php echo esc_html(__('Submit Ticket', 'majestic-support')); ?>
                                        </a>
                                        <?php 
                                        if(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) { ?>
                                            <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'smartreply', 'mjslay'=>'smartreplies')));?>">
                                                <svg class="svg-icon" viewBox="0 0 640 512"><path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM625 177L497 305c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L591 143c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg>
                                                <?php echo esc_html(__('Smart Reply', 'majestic-support')); ?>
                                            </a>
                                            <?php
                                        } else { ?>
                                            <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketstatus'))); ?>">
                                                <svg class="svg-icon" viewBox="0 0 640 512"><path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM625 177L497 305c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L591 143c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg>
                                                <?php echo esc_html(__('Ticket Status', 'majestic-support')); ?>
                                            </a>
                                            <?php
                                        }
                                        if (in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff() && in_array('cannedresponses', majesticsupport::$_active_addons)) { ?>
                                            <a class="mjtc-support-link" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'cannedresponses', 'mjslay'=>'agentcannedresponses'))); ?>" data-tab-number="2">
                                                <svg class="svg-icon" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg>
                                                <?php echo esc_html(__('Premade Responses', 'majestic-support')); ?>
                                            </a>
                                            <?php
                                        } else { ?>
                                            <a class="mjtc-support-link" href="<?php echo esc_url($MJTC_tkt_url); ?>" data-tab-number="2">
                                                <svg class="svg-icon" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg>
                                                <?php echo esc_html(__('Closed Archive', 'majestic-support')); ?>
                                            </a>
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                                <?php
                            } ?>

                            <!-- Ticket Categories - DYNAMIC -->
                            <?php if (is_user_logged_in()) {
                                // Define a vibrant, professional color palette for dynamic departments
                                $MJTC_dynamic_palette = [
                                    '#291abc', // Indigo
                                    '#10b981',                   // Green
                                    '#f59e0b',                   // Amber
                                    '#a78bfa',                   // Purple
                                    '#f43f5e',                   // Rose
                                    '#0ea5e9'                    // Sky Blue
                                ];
                                if (majesticsupport::$_config['cplink_ticket_departments_'. $MJTC_linkname] == 1) { ?>
                                    <div class="mjtc-support-cp-card">
                                        <div class="mjtc-support-cp-card-header">
                                            <div class="mjtc-support-cp-card-title"><?php echo esc_html__('Ticket Departments', 'majestic-support'); ?></div>
                                        </div>
                                        <div class="mjtc-support-cp-category-list" id="category-list-container">
                                            <?php 
                                            if (!empty(majesticsupport::$_data[0]['department-tickets'])) {
                                                foreach (majesticsupport::$_data[0]['department-tickets'] as $MJTC_index => $MJTC_cat) { 
                                                    // Pick color based on position in the list
                                                    $MJTC_color = isset($MJTC_dynamic_palette[$MJTC_index]) ? $MJTC_dynamic_palette[$MJTC_index] : '#94a3b8'; ?>
                                                    <div class="mjtc-support-cp-progress-item">
                                                        <div class="mjtc-support-cp-progress-header">
                                                            <span style="display:flex; align-items:center;">
                                                                <span style="min-width:8px; width:8px; height:8px; border-radius:50%; background:<?php echo esc_attr($MJTC_color); ?>; margin-right:8px; display:inline-block;"></span>
                                                                <span style="color: var(--mjtc-admin-text-main); font-weight: 500;"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_cat['name'])); ?></span>
                                                            </span>
                                                            <span style="font-weight: 600; color: var(--mjtc-admin-text-secondary); font-size: .8em;"><?php echo esc_html( (int)$MJTC_cat['perc'] ); ?>%</span>
                                                        </div>
                                                        <div class="mjtc-support-cp-progress-bg">
                                                            <div class="mjtc-support-cp-progress-fill" 
                                                                 style="width: <?php echo esc_attr( (int)$MJTC_cat['perc'] ); ?>%; background-color: <?php echo esc_attr($MJTC_color); ?>;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                } 
                                            } else { ?>
                                                <div class="mjtc-support-cp-empty-state" style="padding: 16px 0;">
                                                    <!-- Icon: Chart Simple (Bar Chart) -->
                                                    <svg viewBox="0 0 448 512"><path d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H208c-26.5 0-48-21.5-48-48V80zM32 272c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V272zM352 32c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H400c-26.5 0-48-21.5-48-48V32z"></path></svg>
                                                    <div><?php echo esc_html__('No department data available', 'majestic-support'); ?></div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                    </div>
                                    <?php 
                                }
                                if (majesticsupport::$_config['cplink_recent_activity_'. $MJTC_linkname] == 1) { ?>
                                    <!-- Recent Activity -->
                                    <div class="mjtc-support-cp-card">
                                        <div class="mjtc-support-cp-card-header">
                                            <div class="mjtc-support-cp-card-title"><?php echo esc_html__('Recent Activity', 'majestic-support'); ?></div>
                                        </div>
                                        <div class="mjtc-support-cp-activity-feed">
                                            <?php 
                                            // Using the history data fetched with permission logic
                                            $MJTC_activity_history = majesticsupport::$_data['action_history'] ?? [];
                                            
                                            if (!empty($MJTC_activity_history)) :
                                                foreach ($MJTC_activity_history as $MJTC_activity) : 
                                                    // Determine dot color based on priority or generic default
                                                    $MJTC_dot_color = !empty($MJTC_activity->prioritycolour) ? $MJTC_activity->prioritycolour : '#6b7280'; ?>
                                                    <div class="mjtc-support-cp-activity-item">
                                                        <div class="mjtc-support-cp-activity-dot" style="background: <?php echo esc_attr($MJTC_dot_color); ?>;"></div>
                                                        <div>
                                                            <div style="font-size: .9em; font-weight: 500;">
                                                                <a href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_activity->ticket_id))); ?>" style="text-decoration: none; color: inherit;">
                                                                    <?php
                                                                        echo esc_html__( 'Ticket', 'majestic-support' ).' #'
                                                                        . esc_html( (int) $MJTC_activity->ticket_id )
                                                                        . ' '
                                                                        . esc_html( $MJTC_activity->message );
                                                                    ?>
                                                                </a>
                                                            </div>
                                                            
                                                            <div style="font-size: .8em; color: var(--mjtc-admin-text-secondary);">
                                                                <?php 
                                                                    echo esc_html(human_time_diff(strtotime($MJTC_activity->datetime), current_time('timestamp'))) . ' ' . esc_html__('ago', 'majestic-support'); 
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php 
                                                endforeach; 
                                            else : ?>
                                                <div class="mjtc-support-cp-empty-state">
                                                    <svg viewBox="0 0 512 512"><path d="M75 75L41 41C25.9 25.9 0 36.6 0 57.9V168c0 13.3 10.7 24 24 24H134.1c21.4 0 32.1-25.9 17-41l-30.8-30.8C155 85.5 203 64 256 64c106 0 192 86 192 192s-86 192-192 192c-40.8 0-78.6-12.7-109.7-34.4c-14.5-10.1-34.2-6.6-44.6 7.9s-6.6 34.2 7.9 44.6C151.2 495 201.7 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C185.3 0 121.3 28.7 75 75zm181 53c-13.3 0-24 10.7-24 24V256c0 6.4 2.5 12.5 7 17l72 72c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-65-65V152c0-13.3-10.7-24-24-24z"></path></svg>
                                                    <div><?php echo esc_html__('No recent activity found.', 'majestic-support'); ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php 
                                }
                                if (majesticsupport::$_config['cplink_daily_velocity_'. $MJTC_linkname] == 1) { ?>
                                    <!-- Daily Velocity Card -->
                                    <div class="mjtc-support-cp-card mjtc-support-cp-dailyvelocity">
                                        <!-- Decorative bg circle -->
                                        <div class="mjtc-support-cp-deco-circle"></div>
                                        <div class="mjtc-support-cp-content">
                                            <div class="mjtc-support-cp-top">
                                                <div>
                                                    <div class="mjtc-support-cp-label"><?php echo esc_html__('Daily Velocity', 'majestic-support'); ?></div>
                                                    <div class="mjtc-support-cp-value"><?php echo esc_html(majesticsupport::$_data['velocity_perc']); ?>%</div>
                                                </div>
                                                <div class="mjtc-support-cp-icon">
                                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="#fcd34d">
                                                        <path d="M11 21h-1l1-7H7.5c-.58 0-.57-.32-.38-.66.19-.34.05-.08.07-.12C8.48 10.94 10.42 7.54 13.14 2H19c.58 0 .57.32.38.66-.19.34-.05.08-.07.12C15.52 13.06 13.58 16.46 10.86 22h.14z"></path>
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Progress Segmented -->
                                            <div class="mjtc-support-cp-progress">
                                                <?php 
                                                for($MJTC_i=1; $MJTC_i<=4; $MJTC_i++) {
                                                    // Highlight segments based on percentage (25% per block)
                                                    $MJTC_opacity = (majesticsupport::$_data['velocity_perc'] >= ($MJTC_i * 25)) ? '1' : '0.3';
                                                    echo '<div class="mjtc-support-cp-progress-bar" style="opacity:'.esc_html($MJTC_opacity) .';"></div>';
                                                }
                                                ?>
                                            </div>

                                            <!-- 3 Column Stats -->
                                            <div class="mjtc-support-cp-footer">
                                                <div class="mjtc-support-cp-footer-col">
                                                    <div class="mjtc-support-cp-footer-val">
                                                        <?php echo esc_html(majesticsupport::$_data['solved_count']); ?>
                                                    </div>
                                                    <div class="mjtc-support-cp-footer-label"><?php echo esc_html(__("Solved",'majestic-support')); ?></div>
                                                </div>
                                                <div class="mjtc-support-cp-footer-sep"></div>
                                                <?php
                                                if(in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) { ?>
                                                    <div style="text-align:center;">
                                                        <div style="font-weight:700; font-size: .95em;">
                                                            <?php echo esc_html(majesticsupport::$_data['avg_time_display']); ?>
                                                        </div>
                                                        <div style="font-size:.7em; opacity:0.7;"><?php echo esc_html(__("Avg Time",'majestic-support')); ?></div>
                                                    </div>
                                                    <div style="width:1px; background:rgba(255,255,255,0.15);"></div>
                                                    <div style="text-align:center;">
                                                        <div style="font-weight:700; font-size: .95em;">
                                                            <?php echo esc_html(majesticsupport::$_data['display_rating']); ?>
                                                        </div>
                                                        <div style="font-size:.7em; opacity:0.7;"><?php echo esc_html(__("Rating",'majestic-support')); ?></div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div style="text-align:center;">
                                                        <div style="font-weight:700; font-size: .95em;">
                                                            <?php echo esc_html(majesticsupport::$_data['answered_tickets']); ?>
                                                        </div>
                                                        <div style="font-size:.7em; opacity:0.7;"><?php echo esc_html(__("Answered",'majestic-support')); ?></div>
                                                    </div>
                                                    <div style="width:1px; background:rgba(255,255,255,0.15);"></div>
                                                    <div style="text-align:center;">
                                                        <div style="font-weight:700; font-size: .95em;">
                                                            <?php echo esc_html(majesticsupport::$_data['pending_tickets']); ?>
                                                        </div>
                                                        <div style="font-size:.7em; opacity:0.7;"><?php echo esc_html(__("Pending",'majestic-support')); ?></div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </aside>
                    </div>
                    <!-- latest user tickets -->
                </div>
                <!-- latest agent tickets -->
            </div>
        </div>


        <div id="mjtc-support-main-black-background" style="display:none;"></div>
          <div id="mjtc-support-main-popup" style="display:none;">
            <div class="mjtc-support-popup-header">
                <span id="mjtc-support-popup-title"></span>
                <span id="mjtc-support-popup-close-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 18 18"></path></svg></span>
            </div>
            <div id="mjtc-support-main-content"></div>
            <div id="mjtc-support-main-downloadallbtn"></div>
        </div>

        <?php
    // Permission setting for notification
    } else {
        MJTC_layout::MJTC_getSystemOffline();
    }

    function mjtc_printMenuLink($title, $MJTC_url, $MJTC_image_path, $MJTC_class, $MJTC_ajaxid=""){
        $MJTC_html = '
        <li class="mjtc-support-cp-nav-item">
            <a class="'.esc_attr($MJTC_class).'" href="'.esc_url($MJTC_url).'" '.esc_attr($MJTC_ajaxid).'>
                '.$MJTC_image_path.'
                '.esc_html($title).'
            </a>
        </li>';
        echo  wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
        return;
    }
    if(in_array('multiform', majesticsupport::$_active_addons)){
        include_once(MJTC_PLUGIN_PATH . 'includes/multiformpopup.php');
    }
    $majesticsupport_js ="
        jQuery(document).ready(function ($) {
            // Scoped Tab Switching Logic
            jQuery('.mjtc-support-cp-tab-btn').click(function() {
                var tabId = jQuery(this).data('tab');
                var MJTC_card = jQuery(this).closest('.mjtc-support-cp-card'); // Find parent card
                
                // Update Buttons only within this card
                MJTC_card.find('.mjtc-support-cp-tab-btn').removeClass('active');
                jQuery(this).addClass('active');
                
                // Update Content only within this card
                MJTC_card.find('.mjtc-support-cp-tab-pane').removeClass('active');
                jQuery('#' + tabId).addClass('active');
            });
            // Mobile Menu Toggle
            jQuery('#mobile-toggle').on('click', function() {
                jQuery('#sidebar').addClass('open'); 
                jQuery('#mobile-overlay').addClass('open');
            });
            jQuery('#mobile-overlay, #close-sidebar-btn').on('click', function() {
                jQuery('#sidebar').removeClass('open');
                jQuery('#mobile-overlay').removeClass('open');
            });

            // Close sidebar when clicking outside on mobile
            jQuery(document).on('click', function(e) {
                if (jQuery(window).width() <= 768) {
                    if (!jQuery(e.target).closest('#sidebar').length && !jQuery(e.target).closest('#mobile-toggle').length) {
                        jQuery('#sidebar').removeClass('open');
                    }
                }
            });

            // FAQ Accordion Logic
            jQuery('.mjtc-support-cp-faq-question').click(function() {
                var item = jQuery(this).parent();
                item.toggleClass('open');
            });
        });
    ";
    // Moved this code to a dedicated file to prevent errors when the header is hidden.
    wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
    ?>
</div>


