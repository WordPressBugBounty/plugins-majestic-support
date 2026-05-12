<?php
if (!defined('ABSPATH')) die('Restricted Access');
$MJTC_c = MJTC_request::MJTC_getVar('page',null,'jsjobs');
$MJTC_c = MJTC_majesticsupportphplib::MJTC_str_replace('majesticsupport_', '', $MJTC_c);
$MJTC_layout = MJTC_request::MJTC_getVar('mjslay');
$MJTC_ff = MJTC_request::MJTC_getVar('fieldfor');
$MJTC_for = MJTC_request::MJTC_getVar('for');
$majesticsupport_js ='
    jQuery( function() {
        jQuery( ".accordion" ).accordion({
            heightStyle: "content",
            collapsible: true,
            active: true,
        });
    });
    ';
    wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);

    $majesticsupport_js = "
    jQuery(document).ready(function($) {
        // Sidebar Toggle Logic
        jQuery('#mjtc-admin-sidebar-toggle, #desktop-menu-toggle').click(function() {
            jQuery('#msadmin-leftmenu').toggleClass('collapsed');
            if (jQuery('#msadmin-leftmenu').hasClass('collapsed')) {
                jQuery('.mjtc-admin-nav-item.mjtc-admin-nav-has-submenu').removeClass('expanded');
                jQuery('.msadmin-sidebar-submenu').slideUp(200);
            }
        });

        // Logo Click Logic (New)
        jQuery('.mjtc-admin-logo-container').click(function() {
            if (jQuery('#msadmin-leftmenu').hasClass('collapsed')) {
                jQuery('#mjtc-admin-sidebar-toggle, #desktop-menu-toggle').click();
            }
        });

        // Submenu Logic
        jQuery('.mjtc-admin-nav-item.mjtc-admin-nav-has-submenu').on('click', function(e) {

            e.preventDefault();

            const performAction = () => {

                if (jQuery(this).hasClass('expanded')) {

                    jQuery(this).removeClass('expanded active-parent');
                    jQuery(this).next('.msadmin-sidebar-submenu').stop(true, true).slideUp(200);

                } else {

                    // Close others EXCEPT current
                    jQuery('.mjtc-admin-nav-item.mjtc-admin-nav-has-submenu.expanded')
                        .not(jQuery(this))
                        .removeClass('expanded active-parent')
                        .next('.msadmin-sidebar-submenu')
                        .stop(true, true)
                        .slideUp(200);

                    jQuery(this).addClass('expanded active-parent');
                    jQuery(this).next('.msadmin-sidebar-submenu').stop(true, true).slideDown(200);
                }
            };

            if (jQuery('#msadmin-leftmenu').hasClass('collapsed')) {

                jQuery('#msadmin-leftmenu').removeClass('collapsed');

                setTimeout(() => {
                    performAction();
                }, 200);

            } else {
                performAction();
            }
        });
            
        // Find any submenu that contains an 'active' list item
        jQuery('.msadmin-sidebar-submenu').each(function() {
            if (jQuery(this).find('li.active').length > 0) {
                
                // 1. Force the submenu to display
                jQuery(this).show(); 
                
                // 2. Add active classes to the parent container (the main menu item)
                jQuery(this).closest('.treeview').addClass('active menu-open');
                
                // 3. If using jQuery UI Accordion, ensure the aria attributes are correct
                jQuery(this).attr('aria-hidden', 'false');
                jQuery(this).addClass('ui-accordion-content-active');
            }
        });
    });";
    wp_add_inline_script( 'majestic-support-cmain-js', $majesticsupport_js );
?>
<div id="msadmin-logo">
    <div class="mjtc-admin-logo-container">
        <img class="mjtc-admin-logo-icon " alt="<?php echo esc_attr(majesticsupport::$_config['title']); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/logo.png" />
        <a title="<?php echo esc_attr(majesticsupport::$_config['title']); ?>" class="ms-anchor" href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport'));?>">
            Majestic Support
        </a>
    </div>
    <button id="mjtc-admin-sidebar-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5"></path>
    </svg>
    </button>
</div>
<ul class="msadmin-sidebar-menu tree " data-widget="tree" id="accordion">
    <div class="mjtc-admin-leftmenu-section-category-wrp">
        <div class="mjtc-admin-nav-label">
            <?php echo esc_html(__('Overview', 'majestic-support')); ?>
        </div>
        <li class="treeview mjtc-admin-nav-group accordion <?php if(($MJTC_c == 'majesticsupport' && $MJTC_layout != 'shortcodes') || $MJTC_c == 'systemerror' || $MJTC_c == 'slug') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport" title="<?php echo esc_attr(__('Dashboard' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span class="ms_text"><?php echo esc_html(__('Dashboard' , 'majestic-support')); ?> </span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'majesticsupport' && ($MJTC_layout == 'controlpanel' || $MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport" title="<?php echo esc_attr(__('Dashboard', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Dashboard', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'majesticsupport' && $MJTC_layout == 'aboutus') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport&mjslay=aboutus" title="<?php echo esc_attr(__('About Us','majestic-support')); ?>">
                        <?php echo esc_html(__('About Us','majestic-support')); ?>
                    </a>
                </li>
                <?php /*?>
                <li class="<?php if($MJTC_c == 'majesticsupport' && $MJTC_layout == 'translations') echo esc_attr('active'); ?>">
                    <a href="#" title="<?php echo esc_attr(__('Translations','majestic-support')); ?>">
                        <?php echo esc_html(__('Translations','majestic-support')); ?>
                    </a>
                </li>
                <?php */?>
                <li class="<?php if($MJTC_c == 'systemerror') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_systemerror" title="<?php echo esc_attr(__('System Errors', 'majestic-support')); ?>">
                        <?php echo esc_html(__('System Errors', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'slug' && ($MJTC_layout == 'slug')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="admin.php?page=majesticsupport_slug&mjslay=slug" title="<?php echo esc_attr(__('Slugs','majestic-support')); ?>">
                        <?php echo esc_html(__('Slugs','majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'reports') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="?page=majesticsupport_reports&mjslay=overallreport" title="<?php echo esc_attr(__('Reports' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                <span class="ms_text"><?php echo esc_html(__('Reports' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'reports' && ($MJTC_layout == 'overallreport')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_reports&mjslay=overallreport" title="<?php echo esc_attr(__('Overall Statistics','majestic-support')); ?>">
                        <?php echo esc_html(__('Overall Statistics','majestic-support')); ?>
                    </a>
                </li>
                <?php if ( in_array('agent',majesticsupport::$_active_addons)) { ?>
                    <li class="<?php if($MJTC_c == 'reports' && ($MJTC_layout == 'staffreport') || ($MJTC_layout == 'staffdetailreport')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_reports&mjslay=staffreport" title="<?php echo esc_attr(__('Agent Reports', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Agent Reports', 'majestic-support')); ?>
                        </a>
                    </li>
                <?php } ?>
                <li class="<?php if($MJTC_c == 'reports' && ($MJTC_layout == 'departmentreport') || ($MJTC_layout == 'departmentdetailreport')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_reports&mjslay=departmentreport" title="<?php echo esc_attr(__('Department Reports','majestic-support')); ?>">
                        <?php echo esc_html(__('Department Reports','majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'reports' && ($MJTC_layout == 'userreport') || ($MJTC_layout == 'userdetailreport')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_reports&mjslay=userreport" title="<?php echo esc_attr(__('User Reports', 'majestic-support')); ?>">
                        <?php echo esc_html(__('User Reports', 'majestic-support')); ?>
                    </a>
                </li>
                <?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
                    <li class="<?php if($MJTC_c == 'reports' && ($MJTC_layout == 'satisfactionreport')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_reports&mjslay=satisfactionreport" title="<?php echo esc_attr(__('Satisfaction Report', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Satisfaction Report', 'majestic-support')); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </li>
    </div>
    <div class="mjtc-admin-leftmenu-section-category-wrp">
        <div class="mjtc-admin-nav-label">
            <?php echo esc_html(__('Management', 'majestic-support')); ?>
        </div>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'ticket' || ($MJTC_c == 'fieldordering' && $MJTC_ff == 1 || $MJTC_c == 'export' || $MJTC_c == 'multiform' || $MJTC_c == 'ticketclosereason') ) echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_ticket" title="<?php echo esc_attr(__('Tickets' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                <span class="ms_text"><?php echo esc_html(__('Tickets' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <?php 
                    $MJTC_id='';
                    $MJTC_href="?page=majesticsupport_ticket&mjslay=addticket&formid=".esc_attr(MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId());
                    if(in_array('multiform', majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                        $MJTC_id="id=multiformpopup";
                        $MJTC_href='#';
                    }
                ?>
                <li class="<?php if($MJTC_c == 'ticket' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_ticket" title="<?php echo esc_attr(__('Tickets', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Tickets', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'ticket' && ($MJTC_layout == 'addticket')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" <?php echo esc_attr($MJTC_id); ?> href="<?php echo esc_url($MJTC_href); ?>" data-ticketurl="?page=majesticsupport_ticket&mjslay=addticket&formid=<?php echo esc_attr(MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId()) ?>" title="<?php echo esc_attr(__('Create Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Create Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <?php if(!in_array('multiform', majesticsupport::$_active_addons)){ ?>
                    <li class="<?php if($MJTC_c == 'fieldordering' && $MJTC_ff == 1) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_fieldordering&fieldfor=1&formid=<?php echo esc_attr(MJTC_includer::MJTC_getModel('ticket')->getDefaultMultiFormId()) ?>" title="<?php echo esc_attr(__('Fields', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Fields', 'majestic-support')); ?>
                        </a>
                    </li>
                <?php } ?>
                <?php if(in_array('export', majesticsupport::$_active_addons)){ ?>
                    <li class="<?php if($MJTC_c == 'export') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_export" title="<?php echo esc_attr(__('Export', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Export', 'majestic-support')); ?>
                        </a>
                    </li>
                <?php } ?>
                <?php if(in_array('multiform', majesticsupport::$_active_addons)){ ?>
                    <li class="<?php if($MJTC_c == 'multiform' || ($MJTC_c == 'fieldordering' && $MJTC_ff == 1)) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_multiform" title="<?php echo esc_attr(__('Multiforms', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Multiforms', 'majestic-support')); ?>
                        </a>
                    </li>
                <?php }else{ ?>
                    <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-multiform/majestic-support-multiform.php');
                        if($MJTC_plugininfo['availability'] == "1"){
                            $MJTC_text = $MJTC_plugininfo['text'];
                            $MJTC_url = "plugins.php?s=majestic-support-multiform&plugin_status=inactive";
                        }elseif($MJTC_plugininfo['availability'] == "0"){
                            $MJTC_text = $MJTC_plugininfo['text'];
                            $MJTC_url = "https://majesticsupport.com/product/multiform/";
                        }
                    ?>
                    <li>
                        <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-submenu-link" title="<?php echo esc_attr($MJTC_text); ?>">
                            <div class="mjtc-admin-nav-item mjtc-locked mjtc-submenu">
                                <span class="ms_text"><?php echo esc_html(__('Multiforms' , 'majestic-support')); ?></span>
                                <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                            </div>
                        </a>
                    </li>
                <?php } ?>
                <?php if(in_array('ticketclosereason', majesticsupport::$_active_addons)){ ?>
                    <li class="<?php if($MJTC_c == 'ticketclosereason') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_ticketclosereason" title="<?php echo esc_attr(__('Ticket Close Reasons', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Ticket Close Reasons', 'majestic-support')); ?>
                        </a>
                    </li>
                <?php }else{ ?>
                    <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-ticketclosereason/majestic-support-ticketclosereason.php');
                        if($MJTC_plugininfo['availability'] == "1"){
                            $MJTC_text = $MJTC_plugininfo['text'];
                            $MJTC_url = "plugins.php?s=majestic-support-ticketclosereason&plugin_status=inactive";
                        }elseif($MJTC_plugininfo['availability'] == "0"){
                            $MJTC_text = $MJTC_plugininfo['text'];
                            $MJTC_url = "https://majesticsupport.com/product/ticketclosereason/";
                        }
                    ?>
                    <li>
                        <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-submenu-link" title="<?php echo esc_attr($MJTC_text); ?>">
                            <div class="mjtc-admin-nav-item mjtc-locked mjtc-submenu">
                                <span class="ms_text"><?php echo esc_html(__('Ticket Close Reasons' , 'majestic-support')); ?></span>
                                <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                            </div>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </li>
        <?php if ( in_array('agent',majesticsupport::$_active_addons)) { ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'agent' || $MJTC_c == 'agentautoassign') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_agent" title="<?php echo esc_attr(__('Agents' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Agents' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'agent' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_agent" title="<?php echo esc_attr(__('Agents' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Agents', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'agent' && ($MJTC_layout == 'addstaff')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_agent&mjslay=addstaff" title="<?php echo esc_attr(__('Add Agent' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Agent', 'majestic-support')); ?>
                        </a>
                    </li>
                    <?php if(in_array('agentautoassign', majesticsupport::$_active_addons)){ ?>
                        <li class="<?php if($MJTC_c == 'agentautoassign') echo esc_attr('active'); ?>">
                            <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_agentautoassign" title="<?php echo esc_attr(__('Agent Auto Assign', 'majestic-support')); ?>">
                                <?php echo esc_html(__('Agent Auto Assign', 'majestic-support')); ?>
                            </a>
                        </li>
                    <?php }else{ ?>
                        <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-agentautoassign/majestic-support-agentautoassign.php');
                            if($MJTC_plugininfo['availability'] == "1"){
                                $MJTC_text = $MJTC_plugininfo['text'];
                                $MJTC_url = "plugins.php?s=majestic-support-agentautoassign&plugin_status=inactive";
                            }elseif($MJTC_plugininfo['availability'] == "0"){
                                $MJTC_text = $MJTC_plugininfo['text'];
                                $MJTC_url = "https://majesticsupport.com/product/agentautoassign/";
                            }
                        ?>
                        <li>
                            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-submenu-link" title="<?php echo esc_attr($MJTC_text); ?>">
                                <div class="mjtc-admin-nav-item mjtc-locked mjtc-submenu">
                                    <span class="ms_text"><?php echo esc_html(__('Auto Assign' , 'majestic-support')); ?></span>
                                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                                </div>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } else { ?>
            <?php
                $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-agent/majestic-support-agent.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-agent&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/agents/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Agents' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <?php if ( in_array('agent',majesticsupport::$_active_addons)) { ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'role') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_role" title="<?php echo esc_attr(__('Agent Roles' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Agent Roles' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'role' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_role" title="<?php echo esc_attr(__('Roles' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Roles', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'role' && ($MJTC_layout == 'addrole')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_role&mjslay=addrole" title="<?php echo esc_attr(__('Add Agent Role' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Agent Role', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-agent/majestic-support-agent.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-agent&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/agents/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Agent Roles' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'department') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_department" title="<?php echo esc_attr(__('Departments' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="2"></line><line x1="15" y1="22" x2="15" y2="2"></line><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="18" x2="20" y2="18"></line></svg>
                <span class="ms_text"><?php echo esc_html(__('Departments' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'department' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_department" title="<?php echo esc_attr(__('Departments' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Departments', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'department' && ($MJTC_layout == 'adddepartment')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_department&mjslay=adddepartment" title="<?php echo esc_attr(__('Add Department' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add Department', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'product') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_product" title="<?php echo esc_attr(__('Products' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                <span class="ms_text"><?php echo esc_html(__('Products' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'product' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_product" title="<?php echo esc_attr(__('Products' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Products', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'product' && ($MJTC_layout == 'addproduct')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_product&mjslay=addproduct" title="<?php echo esc_attr(__('Add Product' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add Product', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'feedback'  || ($MJTC_c == 'fieldordering' && $MJTC_ff == 2) ) echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="?page=majesticsupport_feedback&mjslay=feedbacks" title="<?php echo esc_attr(__('Feedback' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Feedback' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'feedback' && ($MJTC_layout == 'feedbacks')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_feedback&mjslay=feedbacks" title="<?php echo esc_attr(__('Feedback' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Feedback', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'fieldordering' && $MJTC_ff == 2) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_fieldordering&fieldfor=2" title="<?php echo esc_attr(__('Feedback Fields' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Feedback Fields', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-feedback/majestic-support-feedback.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-feedback&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/feedback/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Feedback' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'smartreply') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_smartreply" title="<?php echo esc_attr(__('Smart Replies' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                <span class="ms_text"><?php echo esc_html(__('Smart Replies' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'smartreply' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_smartreply" title="<?php echo esc_attr(__('Smart Reply', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Smart Replies', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'smartreply' && ($MJTC_layout == 'addsmartreply')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_smartreply&mjslay=addsmartreply" title="<?php echo esc_attr(__('Add Smart Reply', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add Smart Reply', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <?php if(in_array('cannedresponses', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'cannedresponses') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_cannedresponses" title="<?php echo esc_attr(__('Premade Responses' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Premade Responses' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'cannedresponses' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_cannedresponses" title="<?php echo esc_attr(__('Premade Responses' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Premade Responses', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'cannedresponses' && ($MJTC_layout == 'addpremademessage')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_cannedresponses&mjslay=addpremademessage" title="<?php echo esc_attr(__('Add Premade Response' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Premade Response', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-cannedresponses/majestic-support-cannedresponses.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-cannedresponses&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/canned-responses/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Premade Responses' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
    </div>
    <div class="mjtc-admin-leftmenu-section-category-wrp">
        <div class="mjtc-admin-nav-label">
            <?php echo esc_html(__('Configuration', 'majestic-support')); ?>
        </div>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'configuration') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="?page=majesticsupport_configuration&msconfigid=general" title="<?php echo esc_attr(__('Settings' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span class="ms_text"><?php echo esc_html(__('Settings' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'configuration' && $MJTC_layout != 'cronjoburl') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_configuration&msconfigid=general" title="<?php echo esc_attr(__('Settings' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Settings', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'configuration' && $MJTC_layout == 'cronjoburl') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_configuration&mjslay=cronjoburl" title="<?php echo esc_attr(__('Cron Job URLs' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Cron Job URLs', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'themes') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="?page=majesticsupport_themes" title="<?php echo esc_attr(__('Colors' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path><path d="M2 2l7.586 7.586"></path><circle cx="11" cy="11" r="2"></circle></svg>
                <span class="ms_text"><?php echo esc_html(__('Colors' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'themes' && ($MJTC_layout == 'themes')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_themes&mjslay=themes" title="<?php echo esc_attr(__('Colors', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Colors', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'priority') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_priority" title="<?php echo esc_attr(__('Priorities' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                <span class="ms_text"><?php echo esc_html(__('Priorities' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'priority' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_priority" title="<?php echo esc_attr(__('Priorities' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Priorities', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'priority' && ($MJTC_layout == 'addpriority')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_priority&mjslay=addpriority" title="<?php echo esc_attr(__('Add Priority' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add Priority', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'status') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_status" title="<?php echo esc_attr(__('Ticket Statuses' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="5" width="22" height="14" rx="7" ry="7"></rect><circle cx="16" cy="12" r="3"></circle></svg>
                <span class="ms_text"><?php echo esc_html(__('Ticket Statuses' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'status' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_status" title="<?php echo esc_attr(__('Ticket Statuses' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Ticket Statuses', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'status' && ($MJTC_layout == 'addstatus')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_status&mjslay=addstatus" title="<?php echo esc_attr(__('Add Ticket Status' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add Ticket Status', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'premiumplugin' || $MJTC_layout == 'addonstatus') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_premiumplugin" title="<?php echo esc_attr(__('Addons' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                <span class="ms_text"><?php echo esc_html(__('Addons' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'premiumplugin' && ($MJTC_layout == 'step1') || ($MJTC_layout == 'step2') || ($MJTC_layout == 'step3') || ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_premiumplugin&mjslay=step1" title="<?php echo esc_attr(__('Install Add-ons', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Install Add-ons', 'majestic-support')); ?>
                    </a>    
                </li>
                <li class="<?php if($MJTC_c == 'premiumplugin' && $MJTC_layout == 'addonstatus') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_premiumplugin&mjslay=addonstatus" title="<?php echo esc_attr(__('Add-ons Status', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add-ons Status', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'premiumplugin' && $MJTC_layout == 'updatekey') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_premiumplugin&mjslay=updatekey" title="<?php echo esc_attr(__('Update Key', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Update Key', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'premiumplugin' && ($MJTC_layout == 'addonfeatures')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_premiumplugin&mjslay=addonfeatures" title="<?php echo esc_attr(__('Add-ons List', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add-ons List', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'majesticsupport' && $MJTC_layout == 'shortcodes') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="?page=majesticsupport_shortcodes" title="<?php echo esc_attr(__('Shortcodes' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                <span class="ms_text"><?php echo esc_html(__('Shortcodes' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'majesticsupport' && $MJTC_layout == 'shortcodes') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport&mjslay=shortcodes" title="<?php echo esc_attr(__('Shortcodes', 'majestic-support'));; ?>">
                        <?php echo esc_html(__('Shortcodes', 'majestic-support'));; ?>
                    </a>
                </li>

            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'thirdpartyimport') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_thirdpartyimport" title="<?php echo esc_attr(__('Import Data' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <span class="ms_text"><?php echo esc_html(__('Import Data' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'thirdpartyimport' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_thirdpartyimport" title="<?php echo esc_attr(__('Import Data' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Import Data', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'gdpr') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_gdpr&mjslay=gdprfields" title="<?php echo esc_attr(__('GDPR' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span class="ms_text"><?php echo esc_html(__('GDPR' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'gdpr' && ($MJTC_layout == 'gdprfields' || $MJTC_layout == 'addgdprfield')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_gdpr&mjslay=gdprfields" title="<?php echo esc_attr(__('GDPR Fields', 'majestic-support')); ?>">
                        <?php echo esc_html(__('GDPR Fields', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'gdpr' && ($MJTC_layout == 'erasedatarequests')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_gdpr&mjslay=erasedatarequests" title="<?php echo esc_attr(__('Erase Data Requests', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Erase Data Requests', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'majesticsupport' && $MJTC_layout == 'help') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="?page=majesticsupport&mjslay=help" title="<?php echo esc_attr(__('Help','majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                <span class="ms_text"><?php echo esc_html(__('Help','majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'majesticsupport' && $MJTC_layout == 'help') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport&mjslay=help" title="<?php echo esc_attr(__('Help','majestic-support')); ?>">
                        <?php echo esc_html(__('Help','majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
    </div>
    <div class="mjtc-admin-leftmenu-section-category-wrp">
        <div class="mjtc-admin-nav-label">
            <?php echo esc_html(__('EMAIL SETTINGS', 'majestic-support')); ?>
        </div>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'email') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_email" title="<?php echo esc_attr(__('System Emails' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M22 7l-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                <span class="ms_text"><?php echo esc_html(__('System Emails' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'email' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_email" title="<?php echo esc_attr(__('System Emails' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('System Emails', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'email' && ($MJTC_layout == 'addemail')) echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_email&mjslay=addemail" title="<?php echo esc_attr(__('Add Email' , 'majestic-support')); ?>">
                        <?php echo esc_html(__('Add Email', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <?php if(in_array('mail', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'mail') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_mail" title="<?php echo esc_attr(__('Mail' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span class="ms_text"><?php echo esc_html(__('Mail' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
               <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'mail') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_mail" title="<?php echo esc_attr(__('Mail' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Mail', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-mail/majestic-support-mail.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-mail&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/internal-mail/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span class="ms_text"><?php echo esc_html(__('Mail' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'emailtemplate') echo esc_attr('active'); ?>">
            <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_emailtemplate" title="<?php echo esc_attr(__('Email Templates' , 'majestic-support')); ?>">
                <svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                <span class="ms_text"><?php echo esc_html(__('Email Templates' , 'majestic-support')); ?></span>
                <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                <span class="ms_active"></span>
            </a>
            <ul class="msadmin-sidebar-submenu treeview-menu">
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'tk-nw') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=tk-nw" title="<?php echo esc_attr(__('New Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('New Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'sntk-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=sntk-tk" title="<?php echo esc_attr(__('Agent Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Agent Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'ew-sm') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=ew-sm" title="<?php echo esc_attr(__('New Agent', 'majestic-support')); ?>">
                        <?php echo esc_html(__('New Agent', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'rs-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=rs-tk" title="<?php echo esc_attr(__('Reassign Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Reassign Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'cl-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=cl-tk" title="<?php echo esc_attr(__('Close Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Close Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'dl-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=dl-tk" title="<?php echo esc_attr(__('Delete Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Delete Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'mo-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=mo-tk" title="<?php echo esc_attr(__('Mark Overdue', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Mark Overdue', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'be-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=be-tk" title="<?php echo esc_attr(__('Ban Email', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Ban Email', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'be-trtk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=be-trtk" title="<?php echo esc_attr(__('Ban email try to create ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Ban email try to create ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'dt-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=dt-tk" title="<?php echo esc_attr(__('Department Transfer', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Department Transfer', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'ebct-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=ebct-tk" title="<?php echo esc_attr(__('Ban Email and Close Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Ban Email and Close Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'ube-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=ube-tk" title="<?php echo esc_attr(__('Unban Email', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Unban Email', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'rsp-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=rsp-tk" title="<?php echo esc_attr(__('Response Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Response Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'rpy-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=rpy-tk" title="<?php echo esc_attr(__('Reply Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Reply Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'tk-ew-ad') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=tk-ew-ad" title="<?php echo esc_attr(__('New Ticket Admin Alert', 'majestic-support')); ?>">
                        <?php echo esc_html(__('New Ticket Admin Alert', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'lk-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=lk-tk" title="<?php echo esc_attr(__('Lock Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Lock Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'ulk-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=ulk-tk" title="<?php echo esc_attr(__('Unlock Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Unlock Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'minp-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=minp-tk" title="<?php echo esc_attr(__('In Progress Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('In Progress Ticket', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'pc-tk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=pc-tk" title="<?php echo esc_attr(__('Ticket Priority Is Changed By', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Ticket Priority Is Changed By', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'ml-ew') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=ml-ew" title="<?php echo esc_attr(__('New Mail Received', 'majestic-support')); ?>">
                        <?php echo esc_html(__('New Mail Received', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'ml-rp') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=ml-rp" title="<?php echo esc_attr(__('New Mail Message Received', 'majestic-support')); ?>">
                        <?php echo esc_html(__('New Mail Message Received', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'fd-bk') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=fd-bk" title="<?php echo esc_attr(__('Feedback Email To User', 'majestic-support')); ?>">
                        <?php echo esc_html(__('Feedback Email To User', 'majestic-support')); ?>
                    </a>
                </li>
                <li class="<?php if($MJTC_c == 'emailtemplate' && $MJTC_for == 'no-rp') echo esc_attr('active'); ?>">
                    <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailtemplate&for=no-rp" title="<?php echo esc_attr(__('User Reply On Closed Ticket', 'majestic-support')); ?>">
                        <?php echo esc_html(__('User Reply On Closed Ticket', 'majestic-support')); ?>
                    </a>
                </li>
            </ul>
        </li>
        <?php if(in_array('emailpiping', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'emailpiping') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="?page=majesticsupport_emailpiping" title="<?php echo esc_attr(__('Email Piping' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span class="ms_text"><?php echo esc_html(__('Email Piping' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'emailpiping') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailpiping" title="<?php echo esc_attr(__('Email Piping', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Email Piping', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php }else{ ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-emailpiping/majestic-support-emailpiping.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-emailpiping&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/email-piping/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span class="ms_text"><?php echo esc_html(__('Email Piping' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <?php if(in_array('emailcc', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'emailcc') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_emailcc" title="<?php echo esc_attr(__('Emial CC' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Email CC' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'emailcc' && $MJTC_layout != 'addemailcc') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailcc" title="<?php echo esc_attr(__('Emial CC' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Email CC', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'emailcc' && $MJTC_layout == 'addemailcc') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_emailcc&mjslay=addemailcc" title="<?php echo esc_attr(__('Add Emial CC', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Email CC', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-emailcc/majestic-support-emailcc.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-emailcc&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/email-cc/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Email CC' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <?php if(in_array('banemail', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'banemail' || $MJTC_c == 'banemaillog') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_banemail" title="<?php echo esc_attr(__('Banned Emails' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('Banned Emails' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'banemail') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_banemail" title="<?php echo esc_attr(__('Banned Emails' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Banned Emails', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'banemaillog') echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_banemaillog" title="<?php echo esc_attr(__('Banned Email Log List', 'majestic-support')); ?>">
                            <?php echo esc_html(__('Banned Email Log List', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-banemail/majestic-support-banemail.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-banemail&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/ban-email/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('Ban Emails' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
    </div>
    <div class="mjtc-admin-leftmenu-section-category-wrp">
        <div class="mjtc-admin-nav-label">
            <?php echo esc_html(__('CONTENT', 'majestic-support')); ?>
        </div>
        <!-- here here -->
        <?php if(in_array('knowledgebase', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'knowledgebase' && ($MJTC_layout == 'listarticles' || $MJTC_layout == 'addarticle')) echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_knowledgebase&mjslay=listarticles" title="<?php echo esc_attr(__('Knowledge Base' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Knowledge Base' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'knowledgebase' && ($MJTC_layout == 'listarticles')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_knowledgebase&mjslay=listarticles" title="<?php echo esc_attr(__('Knowledge Base' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Knowledge Base', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'knowledgebase' && ($MJTC_layout == 'addarticle')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_knowledgebase&mjslay=addarticle" title="<?php echo esc_attr(__('Add Knowledge Base' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Knowledge Base', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'knowledgebase' && ($MJTC_layout == 'listcategories' || $MJTC_layout == 'addcategory')) echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_knowledgebase&mjslay=listcategories" title="<?php echo esc_attr(__('Categories','majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('Categories','majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'knowledgebase' && ($MJTC_layout == 'listcategories')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_knowledgebase&mjslay=listcategories" title="<?php echo esc_attr(__('Categories','majestic-support')); ?>">
                            <?php echo esc_html(__('Categories', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'knowledgebase' && ($MJTC_layout == 'addcategory')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_knowledgebase&mjslay=addcategory" title="<?php echo esc_attr(__('Add Category','majestic-support')); ?>">
                            <?php echo esc_html(__('Add Category', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-knowledgebase/majestic-support-knowledgebase.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-knowledgebase&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/knowledge-base/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Categories' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('Knowledge Base' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <?php if(in_array('faq', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'faq') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_faq" title="<?php echo esc_attr(__('FAQs' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('FAQs' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'faq' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_faq" title="<?php echo esc_attr(__("FAQs" , 'majestic-support')); ?>">
                            <?php echo esc_html(__("FAQs", 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'faq' && ($MJTC_layout == 'addfaq')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_faq&mjslay=addfaq" <?php echo esc_html(__('Add FAQ' , 'majestic-support')); ?>>
                            <?php echo esc_html(__( 'Add FAQ', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-faq/majestic-support-faq.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-faq&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/faq/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('FAQs' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <?php if(in_array('announcement', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'announcement') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_announcement" title="<?php echo esc_attr(__('Announcements' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 1 8 8 8 8 0 0 1-8 8H8.4L4 22l1.6-4.8A8 8 0 0 1 12 2z"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Announcements' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'announcement' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_announcement" title="<?php echo esc_attr(__('Announcements' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Announcements', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'announcement' && ($MJTC_layout == 'addannouncement')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_announcement&mjslay=addannouncement" title="<?php echo esc_attr(__('Add Announcement' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Announcement', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-announcement/majestic-support-announcement.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-announcement&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/announcements/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 1 8 8 8 8 0 0 1-8 8H8.4L4 22l1.6-4.8A8 8 0 0 1 12 2z"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Announcements' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <?php if(in_array('download', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'download') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_download" title="<?php echo esc_attr(__('Downloads' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Downloads' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'download' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_download" title="<?php echo esc_attr(__('Downloads' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Downloads', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'download' && ($MJTC_layout == 'adddownload')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_download&mjslay=adddownload" title="<?php echo esc_attr(__('Add Download' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Download', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-download/majestic-support-download.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-download&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/download/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"></path></svg>
                    <span class="ms_text"><?php echo esc_html(__('Download' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
        <?php if(in_array('helptopic', majesticsupport::$_active_addons)){ ?>
            <li class="treeview mjtc-admin-nav-group accordion <?php if($MJTC_c == 'helptopic') echo esc_attr('active'); ?>">
                <a class="mjtc-admin-nav-item mjtc-admin-nav-has-submenu" href="admin.php?page=majesticsupport_helptopic" title="<?php echo esc_attr(__('Help Topics' , 'majestic-support')); ?>">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('Help Topics' , 'majestic-support')); ?></span>
                    <svg class="mjtc-admin-chevron-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path></svg>
                    <span class="ms_active"></span>
                </a>
                <ul class="msadmin-sidebar-submenu treeview-menu">
                    <li class="<?php if($MJTC_c == 'helptopic' && ($MJTC_layout == '')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_helptopic" title="<?php echo esc_attr(__('Help Topics' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Help Topics', 'majestic-support')); ?>
                        </a>
                    </li>
                    <li class="<?php if($MJTC_c == 'helptopic' && ($MJTC_layout == 'addhelptopic')) echo esc_attr('active'); ?>">
                        <a class="mjtc-admin-submenu-link" href="?page=majesticsupport_helptopic&mjslay=addhelptopic" tite="<?php echo esc_attr(__('Add Help Topic' , 'majestic-support')); ?>">
                            <?php echo esc_html(__('Add Help Topic', 'majestic-support')); ?>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } else { ?>
            <?php $MJTC_plugininfo = mjtc_checkPluginInfo('majestic-support-helptopic/majestic-support-helptopic.php');
                if($MJTC_plugininfo['availability'] == "1"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "plugins.php?s=majestic-support-helptopic&plugin_status=inactive";
                }elseif($MJTC_plugininfo['availability'] == "0"){
                    $MJTC_text = $MJTC_plugininfo['text'];
                    $MJTC_url = "https://majesticsupport.com/product/helptopic/";
                }
            ?>
            <a href="<?php echo esc_url($MJTC_url); ?>" class="mjtc-admin-nav-group" title="<?php echo esc_attr($MJTC_text); ?>">
                <div class="mjtc-admin-nav-item mjtc-locked has-submenu">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span class="ms_text"><?php echo esc_html(__('Help Topics' , 'majestic-support')); ?></span>
                    <svg class="mjtc-lock-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path></svg>
                </div>
            </a>
        <?php } ?>
</ul>
<?php if(in_array('multiform', majesticsupport::$_active_addons)){ ?>
    <div id="multiformpopupblack" style="display:none;"></div>
    <div id="multiformpopup" class="" style="display:none;"><!-- Select User Popup -->
        <div class="ms-multiformpopup-header">
            <div class="multiformpopup-header-text">
                <?php echo esc_html(__('Select Form','majestic-support')); ?>
            </div>
            <div class="multiformpopup-header-close-img">
                <img src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png">
            </div>
        </div>
        <div id="records">
            <div id="records-inner">
                <div class="mjtc-staff-searc-desc">
                    <?php echo esc_html(__('No Record Found','majestic-support')); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- add loading for multiform -->
    <div id="mstran_loading">
        <div class="ms-css-spinner"></div>
    </div>
<?php } ?>
<?php
$majesticsupport_js ="
    var cookielist = document.cookie.split(';');
    for (var i=0; i<cookielist.length; i++) {
        if (cookielist[i].trim() == 'ms_collapse_admin_menu=1') {
            jQuery('#msadmin-wrapper').addClass('menu-collasped-active');
            break;
        }
    }

    jQuery(document).ready(function(){

        var pageWrapper = jQuery('#msadmin-wrapper');
        var sideMenuArea = jQuery('#msadmin-leftmenu');

        jQuery('#msadmin-menu-toggle').on('click', function () {

            if (pageWrapper.hasClass('menu-collasped-active')) {
                pageWrapper.removeClass('menu-collasped-active');
                document.cookie = 'ms_collapse_admin_menu=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
            }else{
                pageWrapper.addClass('menu-collasped-active');
                document.cookie = 'ms_collapse_admin_menu=1; expires=Sat, 01 Jan 2050 00:00:00 UTC; path=/';
            }

        });

        // to set anchor link active on menu collpapsed
        jQuery('.msadmin-leftmenu .msadmin-sidebar-menu li.treeview mjtc-admin-nav-group accordion a').on('click', function() {
            if (!(pageWrapper.hasClass('menu-collasped-active'))) {
                window.location.href = jQuery(this).attr('href');
            }
        })
    });
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
<?php
$majesticsupport_js ="
    jQuery(document).ready(function ($) {

        jQuery('a#multiformpopup').click(function (e) {
            e.preventDefault();
            var url = jQuery('a#multiformpopup').data('ticketurl');
            jQuery('div#multiformpopupblack').show();
            var ajaxurl ='".esc_url(admin_url('admin-ajax.php'))."';
            jsShowLoading();
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'multiform', task: 'getmultiformlistajax', url:url, '_wpnonce':'". esc_attr(wp_create_nonce("get-multi-form-list-ajax"))."'}, function (data) {
                if(data){
                    jsHideLoading();
                    jQuery('div#records').html('');
                    jQuery('div#records').html(data);
                    // setUserLink(); generate error
                    jQuery('div#multiformpopup').slideDown('slow');
                }
            });
        });

        jQuery('div#multiformpopupblack , div.multiformpopup-header-close-img').click(function (e) {
            jQuery('div#multiformpopup').slideUp('slow', function () {
                jQuery('div#multiformpopupblack').hide();
            });
        });
    });

    function MJTC_makeFormSelected(divelement){
        jQuery('div.mjtc-support-multiform-row').removeClass('selected');
        jQuery(divelement).addClass('selected');  
    }

    function MJTC_makeMultiFormUrl(id){
        var oldUrl = jQuery('a.mjtc-multiformpopup-link').attr('id'); // Get current url
        var newUrl = oldUrl+'&formid='+id; // Create new url
        window.location.href = newUrl;
    }

    function jsShowLoading(){
        jQuery('div#mstran_loading').css('display', 'flex');
    }

    function jsHideLoading(){
        jQuery('div#mstran_loading').hide();
    }
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
