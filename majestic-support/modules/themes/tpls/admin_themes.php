<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wp_enqueue_script('iris');
wp_enqueue_style('majesticsupport-main-css', MJTC_PLUGIN_URL . 'includes/css/style.css', array(), '1.0.0');
wp_enqueue_style('majesticsupport-status-graph', MJTC_PLUGIN_URL . 'includes/css/status_graph.css', array(), '1.0.0');
MJTC_message::MJTC_getMessage();
?>
<style type="text/css">
    <?php $MJTC_color1 = majesticsupport::$_data[0]['color1'];
    $MJTC_color2 = majesticsupport::$_data[0]['color2'];
    $MJTC_color3 = majesticsupport::$_data[0]['color3'];
    $MJTC_color4 = majesticsupport::$_data[0]['color4'];
    $MJTC_color5 = majesticsupport::$_data[0]['color5'];
    $MJTC_color6 = majesticsupport::$_data[0]['color6'];
    $MJTC_color7 = majesticsupport::$_data[0]['color7'];
    $MJTC_color8 = majesticsupport::$_data[0]['color8'];

    echo '
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-filter-button{height:40px;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-blue{color:#1b08c8;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-green, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-green.mjtc-myticket-link .mjtc-support-card-percentage-duration-wrp svg{color:'.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-brown{color:rgb(249 115 22 / var(--tw-text-opacity, 1));}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-red{color:rgb(5 150 105 / var(--tw-text-opacity, 1));}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-padding-xs a{color: '.esc_attr($MJTC_color2).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-padding-xs a:hover{color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-ticketsclassctive a{background-color:' . esc_attr($MJTC_color1) . '17; color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active{border-color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-right-wrp svg{color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-left-wrp .mjtc-support-circle-count-text{color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-percentage{color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-percentage svg{color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active {background-color:' . esc_attr($MJTC_color1) . '17;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-bg svg{color:  '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-search-btn{background-color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-cp-menu-link{color: '.esc_attr($MJTC_color4).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn{color: '.esc_attr($MJTC_color4).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn:hover{color: '.esc_attr($MJTC_color1).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn:hover svg{color: inherit;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data .mjtc-support-last-reply-badge{color: '.esc_attr($MJTC_color2). '80;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-filter-form .mjtc-support-input-field{height:40px;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val{color: '.esc_attr($MJTC_color2).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-hover-actions a{color:'.esc_attr($MJTC_color5).';}
    #msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-profile-wrp .ms-profile-name-wrp .ms-profile-email, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-unassign-value, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data-tit{color:'.esc_attr($MJTC_color5).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-staff-logo-wrp svg{color:'.esc_attr($MJTC_color5). 'b3;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-replied-messages{color:'.esc_attr($MJTC_color4).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-replied-messages svg{color:'.esc_attr($MJTC_color5).';}
    #msadmin-wrapper .mjtc-filter-form{border:unset;}
    #msadmin-wrapper div.ms-main-up-wrapper, #msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-menu-wrp, #msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-loginlogoutclass a, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-search-wrp, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-ticket-wrapper, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data .mjtc-support-last-reply-badge, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-pic {border-color:'.esc_attr($MJTC_color6).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-filter-button{color:'.esc_attr($MJTC_color7).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-filter-button.mjtc-support-reset-btn{color:transparent;}
    #msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-menu-wrp{background-color:'.esc_attr($MJTC_color7).';}
    #msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-loginlogoutclass a{background-color:'.esc_attr($MJTC_color7).';color:#ef4444;}
    #msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-loginlogoutclass a:hover{background-color:'.esc_attr($MJTC_color1).';color:#ffffff;}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-top-sec-right-header a{color:'.esc_attr($MJTC_color7).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn{background-color:'.esc_attr($MJTC_color7).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-sorting-select, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-admin-sort-btn{background-color:'.esc_attr($MJTC_color7).';}
    #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-ticket-wrapper, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-pic, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-staff-logo-wrp{background-color:'.esc_attr($MJTC_color7).';}
    ';
    ?>

</style>
<div class="msadmin-wrapper-main-overall-wrapper-for-all">
    <div id="msadmin-wrapper">
        <div id="msadmin-leftmenu">
            <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu');?>
        </div>
        <div id="msadmin-data">
            <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('themes');?>
            <div id="msadmin-data-wrp">
                <?php do_action('MJTC_theme_colors_message','majestic-support');?>
                <div class="mjtc-color-picker-wrapper">
                    <div id="theme_heading">
                        <div class="left_side">
                            <span class="job_sharing_text">
                                <?php echo esc_html(__('Color Chooser', 'majestic-support'));?>
                            </span>
                        </div>
                        <div class="right_side">
                            <a href="#" id="preset_theme">
                                <img alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/preset_theme.png" />
                                <span class="theme_presets_theme">
                                    <?php echo esc_html(__('Preset Theme', 'majestic-support'));?>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="mjtc_theme_section mjtc_theme_section-wrapper-width">
                        <form action="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=majesticsupport_themes&task=savetheme'),"save-theme"));?>" method="POST" name="adminForm" id="adminForm">
                            <span class="mjtc_theme_heading">
                                <?php echo esc_html(__('Color Chooser','majestic-support'));?>
                            </span>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 1','majestic-support'));?>
                                </span>
                                <input type="text" name="color1" id="color1" value="<?php echo esc_attr(majesticsupport::$_data[0]['color1']); ?>" style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color1']); ?>;" maxlength="15" />
                                <span class="color_location">
                                    <?php echo esc_html(__('Primary Color', 'majestic-support')); ?>,
                                    <?php echo esc_html(__('Button Color', 'majestic-support')); ?>
                                </span>
                            </div>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 2', 'majestic-support'));?>
                                </span>
                                <input type="text" name="color2" id="color2" value="<?php echo esc_attr(majesticsupport::$_data[0]['color2']);?>" style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color2']); ?>;" maxlength="15" />
                                <span class="color_location">
                                    <?php echo esc_html(__('Secondary Color', 'majestic-support')); ?>,
                                    <?php echo esc_html(__('Button Color', 'majestic-support')); ?>,
                                    <?php echo esc_html(__('Heading Text', 'majestic-support')); ?>
                                </span>
                            </div>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 3', 'majestic-support'));?>
                                </span>
                                <input type="text" name="color3" id="color3" value="<?php echo esc_attr(majesticsupport::$_data[0]['color3']);?>" style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color3']);?>;" maxlength="15" />
                                <span class="color_location">
                                    <?php echo esc_html(__('Content Background Color', 'majestic-support'));?>
                                </span>
                            </div>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 4', 'majestic-support'));?>
                                </span>
                                <input type="text" name="color4" id="color4" value="<?php echo esc_attr(majesticsupport::$_data[0]['color4']);?>" style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color4']);?>;" maxlength="15" />
                                <span class="color_location">
                                        <?php echo esc_html(__('Body Text Color', 'majestic-support'));?>
                                </span>
                            </div>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 5','majestic-support'));?>
                                </span>
                                <input type="text" name="color5" id="color5" value="<?php echo esc_attr(majesticsupport::$_data[0]['color5']); ?>" style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color5']);?>;" maxlength="15" />
                                <span class="color_location">
                                    <?php echo esc_html(__('Body Text Second Color','majestic-support'));?>
                                </span>
                            </div>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 6', 'majestic-support'));?>
                                </span>
                                <input type="text" name="color6" id="color6" value="<?php echo esc_attr(majesticsupport::$_data[0]['color6']); ?>" style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color6']);?>;" maxlength="15" />
                                <span class="color_location">
                                    <?php echo esc_html(__('Border Color', 'majestic-support'));?>
                                </span>
                            </div>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 7', 'majestic-support'));?>
                                </span>
                                <input type="text" name="color7" id="color7"
                                    value="<?php echo esc_attr(majesticsupport::$_data[0]['color7']);?>"
                                    style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color7']);?>;" maxlength="15" />
                                <span class="color_location">
                                    <?php echo esc_html(__('Top Header Text Color', 'majestic-support')); ?>,
                                    <?php echo esc_html(__(' Tickets background color', 'majestic-support')); ?>
                                </span>
                            </div>
                            <div class="color_portion">
                                <span class="color_title">
                                    <?php echo esc_html(__('Color 8', 'majestic-support'));?>
                                </span>
                                <input type="text" name="color8" id="color8"
                                    value="<?php echo esc_attr(majesticsupport::$_data[0]['color8']);?>"
                                    style="background:<?php echo esc_attr(majesticsupport::$_data[0]['color8']);?>;" maxlength="15" />
                                <span class="color_location">
                                    <?php echo esc_html(__('Button Gradient Color', 'majestic-support')); ?>
                                </span>
                            </div>
                            <div class="color_submit_button">
                                <input type="hidden" name="form_request" value="majesticsupport" />
                                <input type="submit" value="<?php echo esc_attr(__('Save Colors', 'majestic-support'));?>" />
                            </div>
                        </form>
                    </div>
                    <div id="mjtc_jobapply_main_wrapper" style="display:none;">
                        <div id="mjtc_job_wrapper">
                            <span class="mjtc_job_controlpanelheading">
                                <?php echo esc_html(__('Preset Theme', 'majestic-support')); ?>
                                <div class="popup-header-close-img">
                                    <img src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/close-icon-white.png">
                                </div>
                            </span>
                            <div class="mjtc_theme_wrapper">
                                <div class="theme_platte">
                                    <div class="color_wrapper">
                                        <div class="color 1" style="background:#291abc;"></div>
                                        <div class="color 2" style="background:#0f172a;"></div>
                                        <div class="color 3" style="background:#f3f4f6;"></div>
                                        <div class="color 4" style="background:#6c7381;"></div>
                                        <div class="color 5" style="background:#94a3b8;"></div>
                                        <div class="color 6" style="background:#e7e7e7;"></div>
                                        <div class="color 7" style="background:#ffffff;"></div>
                                        <div class="color 8" style="background:#2DA1CB;"></div>
                                        <span class="theme_name">
                                            <?php echo esc_html(__('Blue Jeans','majestic-support'));?>
                                        </span>
                                        <img class="preview" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/themes/preview1.png" />
                                        <a href="#" class="preview"></a>
                                        <a href="#" class="set_theme"></a>
                                    </div>
                                </div>
                                <div class="theme_platte">
                                    <div class="color_wrapper">
                                        <div class="color 1" style="background:#CF020D;"></div>
                                        <div class="color 2" style="background:#9F3233;"></div>
                                        <div class="color 3" style="background:#F5F2F5;"></div>
                                        <div class="color 4" style="background:#636363;"></div>
                                        <div class="color 5" style="background:#D1D1D1;"></div>
                                        <div class="color 6" style="background:#E7E7E7;"></div>
                                        <div class="color 7" style="background:#FFFFFF;"></div>
                                        <div class="color 8" style="background:#2DA1CB;"></div>
                                        <span class="theme_name">
                                            <?php echo esc_html(__('Red', 'majestic-support'));?>
                                        </span>
                                        <img class="preview"
                                            src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/themes/preview2.png" />
                                        <a href="#" class="preview"></a>
                                        <a href="#" class="set_theme"></a>
                                    </div>
                                </div>
                                <div class="theme_platte">
                                    <div class="color_wrapper">
                                        <div class="color 1" style="background:#00A37A;"></div>
                                        <div class="color 2" style="background:#2B2B2B;"></div>
                                        <div class="color 3" style="background:#F5F2F5;"></div>
                                        <div class="color 4" style="background:#636363;"></div>
                                        <div class="color 5" style="background:#D1D1D1;"></div>
                                        <div class="color 6" style="background:#E7E7E7;"></div>
                                        <div class="color 7" style="background:#FFFFFF;"></div>
                                        <div class="color 8" style="background:#2DA1CB;"></div>
                                        <span class="theme_name">
                                            <?php echo esc_html(__('Mint','majestic-support'));?>
                                        </span>
                                        <img class="preview" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/themes/preview3.png" />
                                        <a href="#" class="preview"></a>
                                        <a href="#" class="set_theme"></a>
                                    </div>
                                </div>
                                <div class="theme_platte">
                                    <div class="color_wrapper">
                                        <div class="color 1" style="background:#7F05AB;"></div>
                                        <div class="color 2" style="background:#590478;"></div>
                                        <div class="color 3" style="background:#F5F2F5;"></div>
                                        <div class="color 4" style="background:#636363;"></div>
                                        <div class="color 5" style="background:#D1D1D1;"></div>
                                        <div class="color 6" style="background:#E7E7E7;"></div>
                                        <div class="color 7" style="background:#FFFFFF;"></div>
                                        <div class="color 8" style="background:#2DA1CB;"></div>
                                        <span class="theme_name">
                                            <?php echo esc_html(__('Lavender','majestic-support'));?>
                                        </span>
                                        <img class="preview" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/themes/preview4.png" />
                                        <a href="#" class="preview"></a>
                                        <a href="#" class="set_theme"></a>
                                    </div>
                                </div>
                                <div class="theme_platte">
                                    <div class="color_wrapper">
                                        <div class="color 1" style="background:#CF520F;"></div>
                                        <div class="color 2" style="background:#2B2B2B;"></div>
                                        <div class="color 3" style="background:#F5F2F5;"></div>
                                        <div class="color 4" style="background:#636363;"></div>
                                        <div class="color 5" style="background:#D1D1D1;"></div>
                                        <div class="color 6" style="background:#E7E7E7;"></div>
                                        <div class="color 7" style="background:#FFFFFF;"></div>
                                        <div class="color 8" style="background:#2DA1CB;"></div>
                                        <span class="theme_name">
                                            <?php echo esc_html(__('Orange','majestic-support'));?>
                                        </span>
                                        <img class="preview" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/themes/preview5.png" />
                                        <a href="#" class="preview"></a>
                                        <a href="#" class="set_theme"></a>
                                    </div>
                                </div>
                                <div class="theme_platte">
                                    <div class="color_wrapper">
                                        <div class="color 1" style="background:#4C8C03;"></div>
                                        <div class="color 2" style="background:#2B2B2B;"></div>
                                        <div class="color 3" style="background:#F5F2F5;"></div>
                                        <div class="color 4" style="background:#636363;"></div>
                                        <div class="color 5" style="background:#D1D1D1;"></div>
                                        <div class="color 6" style="background:#E7E7E7;"></div>
                                        <div class="color 7" style="background:#FFFFFF;"></div>
                                        <div class="color 8" style="background:#2DA1CB;"></div>
                                        <span class="theme_name">
                                            <?php echo esc_html(__('Grass','majestic-support'));?>
                                        </span>
                                        <img class="preview" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/themes/preview6.png" />
                                        <a href="#" class="preview"></a>
                                        <a href="#" class="set_theme"></a>
                                    </div>
                                </div>
                                <div class="theme_platte">
                                    <div class="color_wrapper">
                                        <div class="color 1" style="background:#4C4952;"></div>
                                        <div class="color 2" style="background:#4C4952;"></div>
                                        <div class="color 3" style="background:#F5F2F5;"></div>
                                        <div class="color 4" style="background:#636363;"></div>
                                        <div class="color 5" style="background:#D1D1D1;"></div>
                                        <div class="color 6" style="background:#E7E7E7;"></div>
                                        <div class="color 7" style="background:#FFFFFF;"></div>
                                        <div class="color 8" style="background:#2DA1CB;"></div>
                                        <span class="theme_name">
                                            <?php echo esc_html(__('Black','majestic-support'));?>
                                        </span>
                                        <img class="preview" src="<?php echo esc_url(MJTC_PLUGIN_URL);?>includes/images/themes/preview7.png" />
                                        <a href="#" class="preview"></a>
                                        <a href="#" class="set_theme"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mjtc_effect_preview mjtc_effect_preview-overall-wrapper">
                    <span class="mjtc_effect_preview_heading">
                        <?php echo esc_html(__('Color Effect Preview', 'majestic-support')); ?>
                    </span>
                    <main class="span12" role="main" id="content">
                        <div class="ms-main-up-wrapper">
                            <div id="popup-record-data" style="width:100%;"></div>
                            <div id="ms-header-main-wrapper">
                                <div id="ms-header" class="">
                                    <div id="ms-tabs-wrp" class="">
                                        <div class="ms-tabs-profile-wrp">
                                            <div class="ms-profile-img-wrp">
                                                <img decoding="async" alt="image"
                                                    src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/user.png"class="">
                                            </div>
                                            <div class="ms-profile-name-wrp">
                                                <span class="ms-profile-name">John Wick</span>
                                                <span class="ms-profile-email">johnwick@gmail.com</span>
                                            </div>
                                        </div>
                                        <div class="ms-tabs-menu-wrp">
                                            <div class="ms-header-tab mjtc-support-homeclass">
                                                <a class="mjtc-cp-menu-link"
                                                    href="#">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" class="outline-icon">
                                                        <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"></path>
                                                    </svg>
                                                    <?php echo esc_html(__('Dashboard','majestic-support'));?>
                                                </a>
                                            </div>
                                            <div class="ms-header-tab mjtc-support-openticketclass">
                                                <a id="" class="mjtc-cp-menu-link"
                                                    href="#">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" class="outline-icon">
                                                        <path d="M12 5v14M5 12h14"></path>
                                                    </svg>
                                                    <?php echo esc_html(__('Submit Ticket','majestic-support'));?>
                                                </a>
                                            </div>
                                            <div class="ms-header-tab mjtc-support-myticket  mjtc-support-ticketsclassctive">
                                                <a class="mjtc-cp-menu-link"
                                                    href="#">
                                                    <svg viewBox="0 0 24 24" width="16" height="16" class="outline-icon">
                                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                                        </path>
                                                        <polyline points="22,6 12,13 2,6"></polyline>
                                                    </svg>
                                                    <?php echo esc_html(__('My Tickets','majestic-support'));?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="ms-tabs-menu-rightwrp">
                                            <div class="ms-header-tab mjtc-support-loginlogoutclass">
                                                <a class="mjtc-cp-menu-link"
                                                    href="#">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" class="outline-icon">
                                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                        <polyline points="16 17 21 12 16 7"></polyline>
                                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                                    </svg>
                                                    <?php echo esc_html(__('Log out','majestic-support'));?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mjtc-transparent-header"></div>
                            </div>
                            <!-- add loading for multiform -->
                            <div id="mstran_loading">
                                <div class="ms-css-spinner"></div>
                            </div>
                            <div class="mjtc-support-top-sec-header">
                                <img decoding="async" class="mjtc-transparent-header-img1" alt="image"
                                    src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png">
                                <div class="mjtc-support-top-sec-left-header">
                                    <div class="mjtc-support-main-heading">
                                     <?php echo esc_html(__('My Tickets','majestic-support'));?></div>
                                    <div class="mjtc-support-sub-heading"><?php echo esc_html(__('Manage, track, and resolve all your support tickets in one place.','majestic-support'));?>
                                    </div>
                                </div>
                                <div class="mjtc-support-top-sec-right-header">
                                    <a id="multiformpopup"
                                        href="#"
                                        class="mjtc-support-button-header">
                                        <?php echo esc_html(__('Submit Ticket','majestic-support'));?> </a>
                                </div>
                            </div>
                            <div class="mjtc-support-cont-main-wrapper mjtc-support-cont-main-wrapper-with-btn">
                                <div class="mjtc-support-cont-wrapper1 mjtc-support-tickets-main-wrapper">
                                    <!-- Top Circle Count Boxes -->
                                    <div class="mjtc-row mjtc-support-top-cirlce-count-wrp">
                                        <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                                            <a class="mjtc-support-blue mjtc-myticket-link " href="#" data-tab-number="4">
                                                <svg viewBox="0 0 24 24" class="mjtc-support-card-bgsvg">
                                                    <path
                                                        d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z">
                                                    </path>
                                                </svg>
                                                <div class="mjtc-support-card-left-wrp">
                                                    <div class="mjtc-support-card-title">
                                                         <?php echo esc_html(__('All Tickets','majestic-support'));?></div>
                                                    <span class="mjtc-support-circle-count-text mjtc-support-blue">
                                                        4 </span>
                                                </div>
                                                <div class="mjtc-support-card-right-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                        class="w-6 h-6">
                                                        <path
                                                            d="M11.644 1.59a.75.75 0 01.712 0l9.75 5.25a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25a.75.75 0 010-1.32l9.75-5.25z">
                                                        </path>
                                                        <path
                                                            d="M3.265 10.602l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13 1.37.739a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.71 0l-9.75-5.25a.75.75 0 010-1.32l1.37-.738z">
                                                        </path>
                                                        <path
                                                            d="M10.933 19.231l-7.668-4.13-1.37.739a.75.75 0 000 1.32l9.75 5.25c.221.12.489.12.71 0l9.75-5.25a.75.75 0 000-1.32l-1.37-.738-7.668 4.13a2.25 2.25 0 01-2.134 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="mjtc-support-card-percentage-duration-wrp">
                                                    <span class="mjtc-support-card-percentage">
                                                        <svg viewBox="0 0 24 24" width="10" height="10">
                                                            <path
                                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM6.5 9L10 5.5 13.5 9H11v4H9V9H6.5zm11 6L14 18.5 10.5 15H13v-4h2v4h2.5z">
                                                            </path>
                                                        </svg>
                                                        <?php echo esc_html(__('All Records','majestic-support'));?> </span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                                            <a class="mjtc-support-green mjtc-myticket-link active" href="#" data-tab-number="1">
                                                <svg class="mjtc-support-card-bgsvg" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776">
                                                    </path>
                                                </svg>
                                                <div class="mjtc-support-card-left-wrp">
                                                    <div class="mjtc-support-card-title">
                                                        <?php echo esc_html(__('Open Tickets ','majestic-support'));?></div>
                                                    <span class="mjtc-support-circle-count-text mjtc-support-green">
                                                        3 </span>
                                                </div>
                                                <div class="mjtc-support-card-right-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="mjtc-support-card-percentage-duration-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                        class="w-3 h-3 text-yellow-300">
                                                        <path
                                                            d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.283 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z">
                                                        </path>
                                                    </svg>
                                                    <?php echo esc_html(__('Action Required ','majestic-support'));?>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                                            <a class="mjtc-support-brown mjtc-myticket-link " href="#" data-tab-number="3">
                                                <svg viewBox="0 0 24 24" class="mjtc-support-card-bgsvg">
                                                    <path
                                                        d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z">
                                                    </path>
                                                </svg>
                                                <div class="mjtc-support-card-left-wrp">
                                                    <div class="mjtc-support-card-title">
                                                         <?php echo esc_html(__('Answered Tickets','majestic-support'));?></div>
                                                    <span class="mjtc-support-circle-count-text mjtc-support-brown">
                                                        2 </span>
                                                </div>
                                                <div class="mjtc-support-card-right-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="mjtc-support-card-percentage-duration-wrp">
                                                    <span class="mjtc-support-card-percentage">
                                                        <svg viewBox="0 0 24 24" width="10" height="10">
                                                            <path
                                                                d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zm-4-5l-4-4V4h8v3.5l-4 4z">
                                                            </path>
                                                        </svg>
                                                         <?php echo esc_html(__('Waiting Response','majestic-support'));?></span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="mjtc-myticket-link-wrap mjtc-support-myticket-link-myticket">
                                            <a class="mjtc-support-red mjtc-myticket-link " href="#" data-tab-number="2">
                                                <svg viewBox="0 0 24 24" class="mjtc-support-card-bgsvg">
                                                    <path
                                                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z">
                                                    </path>
                                                </svg>
                                                <div class="mjtc-support-card-left-wrp">
                                                    <div class="mjtc-support-card-title">
                                                          <?php echo esc_html(__('Closed Tickets','majestic-support'));?></div>
                                                    <span class="mjtc-support-circle-count-text mjtc-support-red">
                                                        1 </span>
                                                </div>
                                                <div class="mjtc-support-card-right-wrp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                        class="w-6 h-6">
                                                        <path fill-rule="evenodd"
                                                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="mjtc-support-card-percentage-duration-wrp">
                                                    <span class="mjtc-support-card-percentage">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                            class="w-3 h-3">
                                                            <path fill-rule="evenodd"
                                                                d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        <?php echo esc_html(__('Case Closed','majestic-support'));?></span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Search Form -->
                                    <div class="mjtc-support-search-wrp">
                                        <div class="mjtc-support-form-wrp">
                                            <form class="mjtc-filter-form" name="majesticsupportform" id="majesticsupportform" method="POST"
                                                action="http://192.168.10.18/arslan/plugin/Majestic_Support_newdesign/?page_id=8&amp;mjsmod=ticket&amp;mjslay=myticket&amp;_wpnonce=56bcb6a84c">
                                                <div class="mjtc-filter-wrapper">
                                                    <div class="mjtc-filter-wrapper-toggle-search-wrapper">
                                                        <div class="mjtc-filter-form-fields-wrp" id="mjtc-filter-wrapper-toggle-search">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                                class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                                                <path fill-rule="evenodd"
                                                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            <input type="text" name="ms-ticketsearchkeys" id="ms-ticketsearchkeys" value=""
                                                                class="mjtc-support-input-field"
                                                                placeholder="<?php echo esc_attr(__('Ticket ID Or Email Address Or Subject','majestic-support'));?>">
                                                        </div>
                                                        <div id="mjtc-filter-wrapper-toggle-area" class="mjtc-filter-wrapper-toggle-ticketid">
                                                            <div class="mjtc-filter-form-fields-wrp">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                    stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5">
                                                                    </path>
                                                                </svg>
                                                                <input type="text" name="ms-ticket" id="ms-ticket" value=""
                                                                    class="mjtc-support-input-field" placeholder="<?php echo esc_attr(__('Ticket ID','majestic-support'));?>">
                                                            </div>
                                                        </div>
                                                        <div class="mjtc-filter-button-wrp">
                                                            <div class="mjtc-filter-button-lftwrp">
                                                                <a href="#" class="mjtc-search-filter-btn" id="mjtc-search-filter-toggle-btn">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                        fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z"
                                                                            clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    <?php echo esc_html(__('Advanced Filters','majestic-support'));?> <svg id="mjtc-filterArrow"
                                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                        fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                                            clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </a>
                                                                <div class="mjtc-support-filter-reset-btn-wrp">
                                                                    <input type="submit" name="ms-reset" id="ms-reset" value="Reset"
                                                                        class="mjtc-support-filter-button mjtc-support-reset-btn"
                                                                        onclick="return resetForm();"> <svg xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 20 20" fill="currentColor"
                                                                        class="mjtc-support-filter-reset-btnicon">
                                                                        <path fill-rule="evenodd"
                                                                            d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z"
                                                                            clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <input type="submit" name="ms-go" id="ms-go" value="<?php echo esc_attr(__('Search','majestic-support'));?>"
                                                                class="mjtc-support-filter-button mjtc-support-search-btn">
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-filter-form-dynamic-fields">
                                                        <div class="mjtc-filter-form-dynamic-fields-inner-wrp">
                                                            <div class="mjtc-filter-field-wrp">
                                                                <label class="mjtc-filter-field-label">
                                                                     <?php echo esc_html(__('Subject','majestic-support'));?></label>
                                                                <div class="mjtc-filter-inputfield-wrp">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                        stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"></path>
                                                                    </svg>
                                                                    <input type="text" name="ms-subject" id="ms-subject" value=""
                                                                        class="mjtc-support-input-field" placeholder="<?php echo esc_attr(__('Subject','majestic-support'));?>">
                                                                </div>
                                                            </div>
                                                            <div class="mjtc-filter-field-wrp">
                                                                <label class="mjtc-filter-field-label"><?php echo esc_html(__('From','majestic-support'));?></label>
                                                                <div class="mjtc-filter-inputfield-wrp">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                        stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z">
                                                                        </path>
                                                                    </svg>
                                                                    <input type="text" name="ms-from" id="ms-from" value=""
                                                                        class="mjtc-support-input-field" placeholder="From">
                                                                </div>
                                                            </div>
                                                            <div class="mjtc-filter-field-wrp">
                                                                <label class="mjtc-filter-field-label">
                                                                   <?php echo esc_html(__(' Phone ','majestic-support'));?></label>
                                                                <div class="mjtc-filter-inputfield-wrp">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                        stroke-width="1.5" stroke="currentColor" class="input-icon">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z">
                                                                        </path>
                                                                    </svg>
                                                                    <input type="text" name="ms-phone" id="ms-phone" value=""
                                                                        class="mjtc-support-input-field" placeholder="Phone">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="sortby" id="sortby" value=""> <input type="hidden" name="list"
                                                    id="list" value="1"> <input type="hidden" name="MS_form_search" id="MS_form_search"
                                                    value="MS_SEARCH"> <input type="hidden" name="mspageid" id="mspageid" value="8"> <input
                                                    type="hidden" name="mjtcslay" id="mjtcslay" value="myticket">
                                            </form>
                                        </div>
                                    </div>
                                    <!-- Sorting Wrapper -->
                                    <div class="mjtc-support-sorting">
                                        <div class="mjtc-support-sorting-left">
                                            <div class="mjtc-support-sorting-heading">
                                                <?php echo esc_html(__('Open Tickets ','majestic-support'));?></div>
                                        </div>
                                        <div class="mjtc-support-sorting-right">
                                            <div class="mjtc-support-sort">
                                                <select class="mjtc-support-sorting-select">
                                                    <?php echo esc_html(__('Subject','majestic-support'));?><option value="subjectdesc">
                                                        <?php echo esc_html(__('Subject','majestic-support'));?></option>
                                                    <option value="prioritydesc">
                                                        <?php echo esc_html(__('Priority','majestic-support'));?></option>
                                                    <option value="ticketiddesc">
                                                        <?php echo esc_html(__('Ticket ID','majestic-support'));?></option>
                                                    <option value="isanswereddesc">
                                                        <?php echo esc_html(__('Answered ','majestic-support'));?></option>
                                                    <option value="statusasc" selected="">
                                                        <?php echo esc_html(__('Status','majestic-support'));?></option>
                                                    <option value="createddesc">
                                                        <?php echo esc_html(__('Created','majestic-support'));?></option>
                                                </select>
                                                <a href="#" class="mjtc-admin-sort-btn" title="sort">
                                                    <img decoding="async" alt="sort"
                                                        src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/sorting-2.png">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mjtc-support-ticket-wrapper ">
                                        <div class="mjtc-support-priority-lftbrder" style="background:#d35454;"></div>
                                        <div class="mjtc-support-toparea">

                                            <div class="mjtc-support-pic">
                                                <img decoding="async" alt="image"
                                                    src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/user/user1.png"
                                                    class="">
                                            </div>
                                            <div class="mjtc-support-data mjtc-nullpadding">
                                                <div class="mjtc-support-last-reply-badge mjtc-badge-default">
                                                    <svg viewBox="0 0 24 24" width="10" height="10" class="fill-current">
                                                        <path
                                                            d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z">
                                                        </path>
                                                    </svg>
                                                    <?php echo esc_html(__('Last Reply','majestic-support'));?>: 19-02–2026
                                                </div>
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <div class="mjtc-support-data-row">
                                                        <span class="mjtc-support-value mjtc-support-name" style="cursor:pointer;"
                                                            onclick="setFromNameFilter('user@gmail.com');">Sarah Jenkins</span>
                                                        <span class="mjtc-support-data-val mjtc-support-ticketid">
                                                            <span class="mjtc-support-ticketiddot">•</span>
                                                            G2qRQLPDH </span>
                                                        <span class="mjtc-support-status" style="background-color:#7ed7fb;color:#186e83;">
                                                            <?php echo esc_html(__('Replied ','majestic-support'));?></span>
                                                        <span class="mjtc-support-wrapper-textcolor" style="background:#d35454;">
                                                             <?php echo esc_html(__('Urgent','majestic-support'));?></span></span>
                                                    </div>
                                                </div>
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <a class="mjtc-support-title-anchor"
                                                        href="#"><?php echo esc_html(__('API Webhook Integration Timeout Issue','majestic-support'));?></a>
                                                </div>
                                                <div class="mjtc-support-body-data-discription">
                                                   <?php echo esc_html(__('Our webhooks are failing to deliver to your endpoints since the update last night. We are seeing constant 504 Gateway Timeouts in our server logs.','majestic-support'));?></div>
                                                <div class="mjtc-support-body-data-btmwrp">
                                                    <div class="mjtc-support-data-row">
                                                        <div class="mjtc-support-data-val">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                                <title><?php echo esc_html(__('Created','majestic-support'));?></title>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5">
                                                                </path>
                                                            </svg>
                                                            17-02-2026
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                            <title><?php echo esc_html(__('Department','majestic-support'));?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21">
                                                            </path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(__('Technical Support','majestic-support'));?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mjtc-support-data1 mjtc-support-padding-left-xs">
                                                <div class="mjtc-support-data-row">
                                                    <div class="flex items-center gap-1.5 mb-2">
                                                        <a class="mjtc-support-replied-messages"
                                                            href="#">
                                                            <svg viewBox="0 0 24 24" width="14" height="14" class="fill-current opacity-70">
                                                                <path
                                                                    d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z">
                                                                </path>
                                                            </svg>
                                                            <span class="mjtc-support-replied-messages-count">3</span>
                                                        </a>
                                                        <div class="mjtc-support-ticket-status-icon-wrp">
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-support-data-tit">
                                                         <?php echo esc_html(__('Assign To','majestic-support'));?></div>
                                                    <div class="mjtc-support-data-val">
                                                        <span class="mjtc-support-staff-logo-wrp">
                                                            <img decoding="async" alt="image"
                                                                src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/user/agent2.png"
                                                                class=""> </span>
                                                        Alex M.
                                                    </div>
                                                    <div class="mjtc-support-hover-actions">
                                                        <a class="mjtc-support-action-btn-sm mjtc-support-danger" title="Delete Ticket"
                                                            onclick="return confirm('Are you sure you want to delete this ticket');"
                                                            href="#"
                                                            data-ticketid="13">
                                                            <svg viewBox="0 0 24 24" width="14" height="14">
                                                                <path
                                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <a onclick="showTicketCloseReasons(13, 'tZ3n8TwmB')" title="Close Ticket"
                                                            class="mjtc-support-action-btn-sm mjtc-support-success">
                                                            <svg viewBox="0 0 24 24" width="14" height="14">
                                                                <path
                                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 13.17l7.59-7.59L19 7l-9 9z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <div class="mjtc-support-divider-vertical mjtc-support-hidden-mobile"
                                                            style="height: 12px; margin: 0 4px;"></div>
                                                        <a href="#"
                                                            class="mjtc-support-action-btn-sm mjtc-support-hidden-mobile" title="<?php echo esc_attr(__("View Details",'majestic-support')); ?>">
                                                            <svg viewBox="0 0 24 24" width="12" height="12">
                                                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mjtc-support-ticket-wrapper ">
                                        <div class="mjtc-support-priority-lftbrder" style="background:#864434;"></div>
                                        <div class="mjtc-support-toparea">

                                            <div class="mjtc-support-pic">
                                                <img decoding="async" alt="image"
                                                    src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/user/user2.png"
                                                    class="">
                                            </div>
                                            <div class="mjtc-support-data mjtc-nullpadding">
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <div class="mjtc-support-data-row">
                                                        <span class="mjtc-support-value mjtc-support-name" style="cursor:pointer;"
                                                            onclick="setFromNameFilter('user@gmail.com');">Tech Solutions Inc.</span>
                                                        <span class="mjtc-support-data-val mjtc-support-ticketid">
                                                            <span class="mjtc-support-ticketiddot">•</span>
                                                            XCPG9nkpR </span>
                                                        <span class="mjtc-support-status" style="background-color: #a7f3d0;color:#047857;">
                                                            <?php echo esc_html(__('New ','majestic-support'));?></span>
                                                        <span class="mjtc-support-wrapper-textcolor" style="background:#864434;">
                                                            <?php echo esc_html(__('Low','majestic-support'));?> </span>
                                                    </div>
                                                </div>
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <a class="mjtc-support-title-anchor"
                                                        href="#"><?php echo esc_html(__('Request to change account email address','majestic-support'));?></a>
                                                </div>
                                                <div class="mjtc-support-body-data-discription">
                                                    <?php echo esc_html(__('I no longer have access to my old email address and would like to update it to a new one. Please guide me through the process or update it from your side if possible. ','majestic-support'));?> </div>
                                                <div class="mjtc-support-body-data-btmwrp">
                                                    <div class="mjtc-support-data-row">
                                                        <div class="mjtc-support-data-val">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                                <title>Created</title>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5">
                                                                </path>
                                                            </svg>
                                                            19-02-2026
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                            <title><?php echo esc_html(__('Department','majestic-support'));?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21">
                                                            </path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(__('Account Management','majestic-support'));?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mjtc-support-data1 mjtc-support-padding-left-xs">
                                                <div class="mjtc-support-data-row">
                                                    <div class="flex items-center gap-1.5 mb-2">
                                                        <a class="mjtc-support-replied-messages"
                                                            href="#">
                                                            <svg viewBox="0 0 24 24" width="14" height="14" class="fill-current opacity-70">
                                                                <path
                                                                    d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z">
                                                                </path>
                                                            </svg>
                                                            <span class="mjtc-support-replied-messages-count">0</span>
                                                        </a>
                                                        <div class="mjtc-support-ticket-status-icon-wrp">
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-support-data-tit">
                                                        <?php echo esc_html(__('Assign To ','majestic-support'));?></div>
                                                    <div class="mjtc-support-data-val">
                                                        <span class="mjtc-support-staff-logo-wrp mjtc-support-staff-empty-logo-wrp">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="20" height="20"
                                                                fill="currentColor" aria-hidden="true" focusable="false">
                                                                <path
                                                                    d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm96 48h-8.7c-22.2 10.2-46.8 16-71.3 16s-49.1-5.8-71.3-16H160C71.6 304 0 375.6 0 464v16c0 17.7 14.3 32 32 32h352c17.7 0 32-14.3 32-32v-16c0-88.4-71.6-160-160-160zm288-112h-48v-48c0-17.7-14.3-32-32-32s-32 14.3-32 32v48h-48c-17.7 0-32 14.3-32 32s14.3 32 32 32h48v48c0 17.7 14.3 32 32 32s32-14.3 32-32v-48h48c17.7 0 32-14.3 32-32s-14.3-32-32-32z">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                        <span class="mjtc-support-unassign-value"><?php echo esc_html(__('Unassigned','majestic-support'));?></span>
                                                    </div>
                                                    <div class="mjtc-support-hover-actions">
                                                        <a class="mjtc-support-action-btn-sm mjtc-support-danger" title="Delete Ticket"
                                                            onclick="return confirm('Are you sure you want to delete this ticket');"
                                                            href="#"
                                                            data-ticketid="17">
                                                            <svg viewBox="0 0 24 24" width="14" height="14">
                                                                <path
                                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <a onclick="showTicketCloseReasons(17, 'VXcjN742B')" title="Close Ticket"
                                                            class="mjtc-support-action-btn-sm mjtc-support-success">
                                                            <svg viewBox="0 0 24 24" width="14" height="14">
                                                                <path
                                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 13.17l7.59-7.59L19 7l-9 9z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <div class="mjtc-support-divider-vertical mjtc-support-hidden-mobile"
                                                            style="height: 12px; margin: 0 4px;"></div>
                                                        <a href="#"
                                                            class="mjtc-support-action-btn-sm mjtc-support-hidden-mobile" title="<?php echo esc_attr(__("View Details",'majestic-support')); ?>">
                                                            <svg viewBox="0 0 24 24" width="12" height="12">
                                                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mjtc-support-ticket-wrapper ">
                                        <div class="mjtc-support-priority-lftbrder" style="background:#bd6403;"></div>
                                        <div class="mjtc-support-toparea">

                                            <div class="mjtc-support-pic">
                                                <img decoding="async" alt="image"
                                                    src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/user/user.png"
                                                    class="">
                                            </div>
                                            <div class="mjtc-support-data mjtc-nullpadding">
                                                <div class="mjtc-support-last-reply-badge mjtc-badge-default">
                                                    <svg viewBox="0 0 24 24" width="10" height="10" class="fill-current">
                                                        <path
                                                            d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z">
                                                        </path>
                                                    </svg><?php echo esc_html(__('Last Reply','majestic-support')).':';?> 20-02–2026
                                                </div>
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <div class="mjtc-support-data-row">
                                                        <span class="mjtc-support-value mjtc-support-name" style="cursor:pointer;"
                                                            onclick="setFromNameFilter('user@gmail.com');">David Chen</span>
                                                        <span class="mjtc-support-data-val mjtc-support-ticketid">
                                                            <span class="mjtc-support-ticketiddot">•</span>
                                                            dB23PCQKr </span>
                                                        <span class="mjtc-support-status" style="background-color: #186e83;color: #7ed7fb;">
                                                             <?php echo esc_html(__('Waiting Reply','majestic-support'));?></span>
                                                        <span class="mjtc-support-wrapper-textcolor" style="background:#bd6403;">
                                                             <?php echo esc_html(__('High','majestic-support'));?></span>
                                                    </div>
                                                </div>
                                                <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <a class="mjtc-support-title-anchor"
                                                        href="#"><?php echo esc_html(__('Cannot access billing portal to download invoice ','majestic-support'));?>
                                                        
                                                    </a>
                                                </div>
                                                <div class="mjtc-support-body-data-discription">
                                                    <?php echo esc_html(__("Hi, I'm trying to download my latest invoice for accounting, but the portal keeps throwing a 500 internal server error. Can someone look into this immediately?",'majestic-support'));?></div>
                                                <div class="mjtc-support-body-data-btmwrp">
                                                    <div class="mjtc-support-data-row">
                                                        <div class="mjtc-support-data-val">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                                <title><?php echo esc_html(__('Created','majestic-support'));?></title>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5">
                                                                </path>
                                                            </svg>
                                                            19-02-2026
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                                            <title><?php echo esc_html(__('Department','majestic-support'));?></title>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21">
                                                            </path>
                                                        </svg>
                                                        <span class="mjtc-support-value"><?php echo esc_html(__('Billing','majestic-support'));?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mjtc-support-data1 mjtc-support-padding-left-xs">
                                                <div class="mjtc-support-data-row">
                                                    <div class="flex items-center gap-1.5 mb-2">
                                                        <a class="mjtc-support-replied-messages"
                                                            href="#">
                                                            <svg viewBox="0 0 24 24" width="14" height="14" class="fill-current opacity-70">
                                                                <path
                                                                    d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z">
                                                                </path>
                                                            </svg>
                                                            <span class="mjtc-support-replied-messages-count">2</span>
                                                        </a>
                                                        <div class="mjtc-support-ticket-status-icon-wrp">
                                                        </div>
                                                    </div>
                                                    <div class="mjtc-support-data-tit">
                                                        <?php echo esc_html(__('Assign To ','majestic-support'));?></div>
                                                    <div class="mjtc-support-data-val">
                                                        <span class="mjtc-support-staff-logo-wrp">
                                                            <img decoding="async" alt="image"
                                                                src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/user/agent1.png"
                                                                class=""> </span>
                                                        Emma W.
                                                    </div>
                                                    <div class="mjtc-support-hover-actions">
                                                        <a class="mjtc-support-action-btn-sm mjtc-support-danger" title="Delete Ticket"
                                                            onclick="return confirm('Are you sure you want to delete this ticket');"
                                                            href="#"
                                                            data-ticketid="18">
                                                            <svg viewBox="0 0 24 24" width="14" height="14">
                                                                <path
                                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <a onclick="showTicketCloseReasons(18, 'MCjRZmWDJ')" title="Close Ticket"
                                                            class="mjtc-support-action-btn-sm mjtc-support-success">
                                                            <svg viewBox="0 0 24 24" width="14" height="14">
                                                                <path
                                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 13.17l7.59-7.59L19 7l-9 9z">
                                                                </path>
                                                            </svg>
                                                        </a>
                                                        <div class="mjtc-support-divider-vertical mjtc-support-hidden-mobile"
                                                            style="height: 12px; margin: 0 4px;"></div>
                                                        <a href="#"
                                                            class="mjtc-support-action-btn-sm mjtc-support-hidden-mobile" title="<?php echo esc_attr(__("View Details",'majestic-support')); ?>">
                                                            <svg viewBox="0 0 24 24" width="12" height="12">
                                                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
                <div class="color_submit_button">
                    <a class="mjtc-color-submit-button" href="#" onclick="document.getElementById('adminForm').submit();" >
                        <?php echo esc_html(__('Save Colors','majestic-support')); ?>
                    </a>
                    <div class="mjtc-sugestion-alert-wrp">
                        <div class="mjtc-sugestion-alert">
                            <strong><?php echo esc_html(__('Note','majestic-support')).":";?></strong>
                            <?php echo esc_html(__('If the colors have been saved but the user-side colors are still the same, it is advised to clear the cache.','majestic-support'));?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $majesticsupport_js ="
            jQuery(document).ready(function () {
                makeColorPicker('". majesticsupport::$_data[0]['color1']."', '". majesticsupport::$_data[0]['color2']."', '". majesticsupport::$_data[0]['color3']."', '". majesticsupport::$_data[0]['color4']."', '". majesticsupport::$_data[0]['color5']."', '". majesticsupport::$_data[0]['color6']."', '". majesticsupport::$_data[0]['color7']."', '". majesticsupport::$_data[0]['color8']."');
            });
            function makeColorPicker(color1, color2, color3, color4, color5, color6, color7, color8) {
            
                jQuery('input#color1').iris({
                    color: color1,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        var r = parseInt(hex.slice(1, 3), 16);
                        var g = parseInt(hex.slice(3, 5), 16);
                        var b = parseInt(hex.slice(5, 7), 16);
                        var rgbaColor = 'rgba(' + r + ', ' + g + ', ' + b + ', 0.09)';
                    jQuery('input#color1').css('background-color', hex);
                        jQuery('div.ms-main-up-wrapper .ms-header-tab.mjtc-support-ticketsclassctive a').css('background-color', rgbaColor);
                        jQuery('div.ms-main-up-wrapper .ms-header-tab.mjtc-support-ticketsclassctive a').css('color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-search-btn').css('background-color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active').css('background-color', rgbaColor);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active').css('border-color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-right-wrp svg').css('color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-left-wrp .mjtc-support-circle-count-text').css('color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-percentage').css('color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-percentage svg').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-green').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-green.mjtc-myticket-link .mjtc-support-card-percentage-duration-wrp svg').css('color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-cp-menu-link').mouseover(function () {
                            jQuery(this).css('color', jQuery('input#color1').val());
                        }).mouseout(function () {
                            jQuery(this).css('color', jQuery('input#color4').val());
                        });
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-padding-xs a').mouseover(function () {
                            jQuery(this).css('color', jQuery('input#color1').val());
                        }).mouseout(function () {
                            jQuery(this).css('color', jQuery('input#color2').val());
                        });
                        
                        
                    }
                });
                jQuery('input#color2').iris({
                    color: color2,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('input#color2').css('background-color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-main-heading').css('color', jQuery('input#color2').val());
                        jQuery('div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-profile-wrp .ms-profile-name-wrp .ms-profile-name').css('color', jQuery('input#color2').val());
                        jQuery('div.ms-main-up-wrapper .mjtc-support-button-header').css('backgroundColor', jQuery('input#color2').val());
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-sorting-heading').css('color', jQuery('input#color2').val());
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-padding-xs a').css('color', jQuery('input#color2').val());
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val').css('color', jQuery('input#color2').val());
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-filter-form .mjtc-support-input-field').css('color', jQuery('input#color2').val());
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-filter-form .mjtc-support-input-field::placeholder').css('color', jQuery('input#color4').val());
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data .mjtc-support-last-reply-badge').css('color', jQuery('input#color2').val() + '80')
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data .mjtc-support-last-reply-badge svg').css('color', jQuery('input#color2').val() + '80')
                        
                        
                        
                    }
                });
                jQuery('input#color3').iris({
                    color: color3,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('input#color3').css('background-color', hex);
                    }
                });
                jQuery('input#color4').iris({
                    color: color4,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('input#color4').css('background-color', hex);
                        jQuery('div.mjtc-support-breadcrumb-wrp .breadcrumb li a').css('color', hex);
                        jQuery('div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-title').css('color', hex);
                        jQuery('div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-value').css('color', hex);
                        jQuery('div.mjtc-support-search-wrp div.mjtc-support-form-wrp form.mjtc-filter-form div.mjtc-filter-wrapper div.mjtc-filter-form-fields-wrp input.mjtc-support-input-field').css('color', jQuery('input#color4').val());
                        jQuery('div.mjtc-filter-button-wrp .mjtc-search-filter-btn').css('color', hex);
                        jQuery('.name span.mjtc-support-value').css('color', hex);
                        jQuery('div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-replied-messages').css('color', hex);
                        jQuery('div.mjtc-support-wrapper div.mjtc-support-data1 div.mjtc-support-data-row .mjtc-support-data-val').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-cp-menu-link').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-sub-heading').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-sorting-select').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper div.mjtc-support-filter-reset-btn-wrp').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-card-title').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-body-data-discription').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data-val').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-body-data-btmwrp .mjtc-support-data-val svg').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-value').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper div.mjtc-support-data.mjtc-nullpadding .mjtc-support-body-data-elipses svg').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn').mouseover(function () {
                            jQuery(this).css('color', jQuery('input#color1').val());
                        }).mouseout(function () {
                            jQuery(this).css('color', jQuery('input#color4').val());
                        });
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper div.mjtc-support-filter-reset-btn-wrp').mouseover(function () {
                            jQuery(this).css('color', 'red');
                        }).mouseout(function () {
                            jQuery(this).css('color', jQuery('input#color4').val());
                        });
                        
                    }
                });
                jQuery('input#color5').iris({
                    color: color5,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('input#color5').css('background-color', hex);
                        jQuery('div.mjtc-support-wrapper').css('border-color', hex);
                        jQuery('div.mjtc-support-wrapper div.mjtc-support-pic').css('border-color', hex);
                        jQuery('div.mjtc-support-wrapper div.mjtc-support-data1').css('border-color', hex);
                        jQuery('div.mjtc-support-assigned-tome').css('border-color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-replied-messages svg').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-staff-logo-wrp svg').css('color', jQuery('input#color5').val() + '90')
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-hover-actions a').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-profile-wrp .ms-profile-name-wrp .ms-profile-email, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-unassign-value, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data-tit').css('color', hex);
                        
                    }
                });
                jQuery('input#color6').iris({
                    color: color6,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('input#color6').css('background-color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper, #msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-menu-wrp, #msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-loginlogoutclass a, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-search-wrp, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-ticket-wrapper, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data .mjtc-support-last-reply-badge, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-pic').css('border-color', hex);
                    }
                });
                jQuery('input#color7').iris({
                    color: color7,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('input#color7').css('background-color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-filter-button').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-filter-button.mjtc-support-reset-btn').css('color', 'transparent');
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-menu-wrp').css('background-color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-loginlogoutclass a').css('background-color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-top-sec-right-header a').css('color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn').css('background-color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-sorting-select, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-admin-sort-btn').css('background-color', hex);
                        jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-ticket-wrapper, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-pic, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-staff-logo-wrp').css('background-color', hex);
                       
                    }
                });
                jQuery('input#color8').iris({
                    color: color8,
                    onShow: function (colpkr) {
                        jQuery(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function (colpkr) {
                        jQuery(colpkr).fadeOut(500);
                        return false;
                    },
                    change: function (c_event, ui) {
                        hex = ui.color.toString();
                        jQuery('input#color8').css('background-color', hex);
                    }
                });

                var sel_string = '#color1, #color2, #color3, #color4, #color5, #color6, #color7, #color8';

                jQuery(sel_string).click(function (event) {
                    jQuery(sel_string).iris('hide');
                        jQuery(this).iris('show');
                        return false;
                    });
                }

        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        ?>
        <div id="black_wrapper_jobapply" style="display:none;"></div>
        <?php
        $majesticsupport_js ="
            jQuery(document).ready(function () {
                jQuery('a#preset_theme').click(function (e) {
                    e.preventDefault();
                    jQuery('div#mjtc_jobapply_main_wrapper').fadeIn();
                    jQuery('div#black_wrapper_jobapply').fadeIn();
                });
                jQuery('div#black_wrapper_jobapply').click(function () {
                    jQuery('div#mjtc_jobapply_main_wrapper').fadeOut();
                    jQuery('div#black_wrapper_jobapply').fadeOut();
                });
                jQuery('a.preview').each(function (index, element) {
                    jQuery(this).hover(function () {
                        if (index > 2)
                            jQuery(this).parent().find('img.preview').css('top', '-110px');
                        jQuery(jQuery(this).parent().find('img.preview')).show();
                    }, function () {
                        jQuery(jQuery(this).parent().find('img.preview')).hide();
                    });
                });
                jQuery('a.set_theme').each(function (index, element) {
                    jQuery(this).click(function (e) {
                        e.preventDefault();
                        var div = jQuery(this).parent();
                        var color1 = rgb2hex(jQuery(div.find('div.1')).css('backgroundColor'));
                        var color2 = rgb2hex(jQuery(div.find('div.2')).css('backgroundColor'));
                        var color3 = rgb2hex(jQuery(div.find('div.3')).css('backgroundColor'));
                        var color4 = rgb2hex(jQuery(div.find('div.4')).css('backgroundColor'));
                        var color5 = rgb2hex(jQuery(div.find('div.5')).css('backgroundColor'));
                        var color6 = rgb2hex(jQuery(div.find('div.6')).css('backgroundColor'));
                        var color7 = rgb2hex(jQuery(div.find('div.7')).css('backgroundColor'));
                        var color8 = rgb2hex(jQuery(div.find('div.8')).css('backgroundColor'));
                        jQuery('input#color1').val(color1).css('backgroundColor', color1);
                        jQuery('input#color2').val(color2).css('backgroundColor', color2);
                        jQuery('input#color3').val(color3).css('backgroundColor', color3);
                        jQuery('input#color4').val(color4).css('backgroundColor', color4);
                        jQuery('input#color5').val(color5).css('backgroundColor', color5);
                        jQuery('input#color6').val(color6).css('backgroundColor', color6);
                        jQuery('input#color7').val(color7).css('backgroundColor', color7);
                        jQuery('input#color8').val(color8).css('backgroundColor', color8);
                        themeSelectionEffect();
                        jQuery('div#mjtc_jobapply_main_wrapper').fadeOut();
                        jQuery('div#black_wrapper_jobapply').fadeOut();
                    });
                });
                jQuery(document).delegate('div#mjtc_jobapply_main_wrapper .popup-header-close-img', 'click', function (e) {
                    jQuery('div#mjtc_jobapply_main_wrapper').fadeOut();
                    jQuery('div#black_wrapper_jobapply').fadeOut();
                });
            });
            function rgb2hex(rgb) {
                rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
                function hex(x) {
                    return ('0' + parseInt(x).toString(16)).slice(-2);
                }
                return '#' + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
            }
            function themeSelectionEffect() {
                jQuery('input.mjtc-support-search-btn').mouseover(function () {
                    jQuery('input.mjtc-support-search-btn').css('borderColor', jQuery('input#color1').val());
                    jQuery('input.mjtc-support-search-btn').css('color', jQuery('input#color7').val());
                    jQuery('input.mjtc-support-search-btn').css('backgroundColor', jQuery('input#color1').val());
                }).mouseout(function () {
                    jQuery('input.mjtc-support-search-btn').css('borderColor', jQuery('input#color1').val());
                    jQuery('input.mjtc-support-search-btn').css('color', jQuery('input#color7').val());
                    jQuery('input.mjtc-support-search-btn').css('backgroundColor', jQuery('input#color1').val());
                });
                jQuery('div.mjtc-support-wrapper').mouseover(function () {
                    jQuery(this).css('borderColor', jQuery('input#color2').val());
                }).mouseout(function () {
                    jQuery(this).css('borderColor', jQuery('input#color5').val());
                });
                jQuery('button.mjtc-support-reset-btn').mouseover(function () {
                    jQuery('button.mjtc-support-reset-btn').css('borderColor', jQuery('input#color2').val());
                    jQuery('button.mjtc-support-reset-btn').css('color', jQuery('input#color2').val());
                    jQuery('button.mjtc-support-reset-btn').css('backgroundColor', jQuery('input#color7').val());
                }).mouseout(function () {
                    jQuery('button.mjtc-support-reset-btn').css('borderColor', jQuery('input#color2').val());
                    jQuery('button.mjtc-support-reset-btn').css('color', jQuery('input#color7').val());
                    jQuery('button.mjtc-support-reset-btn').css('backgroundColor', jQuery('input#color2').val());
                });
                jQuery('div#ms-header div.ms-header-tab a').each(function () {
                    jQuery(this).css('color', jQuery('input#color4').val())
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-ticketsclassctive a').each(function () {
                    jQuery(this).css('color', jQuery('input#color1').val())
                    jQuery(this).css('backgroundColor', jQuery('input#color1 ').val() + '17')
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active').each(function () {
                    jQuery(this).css('backgroundColor', jQuery('input#color1 ').val() + '17')
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-right-wrp svg').each(function () {
                    jQuery(this).css('color', jQuery('input#color1 ').val())
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active .mjtc-support-card-left-wrp .mjtc-support-circle-count-text').each(function () {
                    jQuery(this).css('color', jQuery('input#color1 ').val())
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active').each(function () {
                    jQuery(this).css('border-color', jQuery('input#color1 ').val())
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link').mouseover(function () {
                        jQuery(this).css('border-color', jQuery('input#color1').val());
                }).mouseout(function () {
                        jQuery(this).css('border-color', jQuery('input#color6').val());
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active').mouseover(function () {
                        jQuery(this).css('border-color', jQuery('input#color1').val());
                }).mouseout(function () {
                        jQuery(this).css('border-color', jQuery('input#color1').val());
                });
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-green, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-green.mjtc-myticket-link .mjtc-support-card-percentage-duration-wrp svg').each(function () {
                    jQuery(this).css('color', jQuery('input#color1 ').val())
                });
                jQuery('.mjtc-support-top-sec-header').css('backgroundColor', jQuery('input#color7').val());                
                jQuery('.mjtc-support-cont-main-wrapper .mjtc-support-cont-wrapper, .mjtc-support-cont-wrapper1').css('borderColor', jQuery('input#color5').val());
                jQuery('div.mjtc-support-top-cirlce-count-wrp').css('borderColor', jQuery('input#color5').val());
                jQuery('div.mjtc-myticket-link a.mjtc-myticket-link').css('borderColor', jQuery('input#color5').val());
                jQuery('div.mjtc-support-search-wrp div.mjtc-support-form-wrp form.mjtc-filter-form div.mjtc-filter-wrapper div.mjtc-filter-form-fields-wrp input.mjtc-support-input-field').css('borderColor', jQuery('input#color5').val());
                jQuery('div.mjtc-support-search-wrp div.mjtc-support-form-wrp form.mjtc-filter-form div.mjtc-filter-wrapper div.mjtc-filter-form-fields-wrp input.mjtc-support-input-field').css('color', jQuery('input#color4').val());
                jQuery('div.mjtc-filter-button-wrp .mjtc-search-filter-btn').css('borderColor', jQuery('input#color5').val());
                jQuery('div.mjtc-filter-button-wrp .mjtc-search-filter-btn').css('color', jQuery('input#color4').val());
                jQuery('input.mjtc-support-search-btn').css('borderColor', jQuery('input#color5').val());
                jQuery('input.mjtc-support-search-btn').css('backgroundColor', jQuery('input#color1').val());
                jQuery('input.mjtc-support-search-btn').css('color', jQuery('input#color7').val());
                // jQuery('.mjtc-support-reset-btn').css('backgroundColor', jQuery('input#color2').val());
                // jQuery('.mjtc-support-reset-btn').css('borderColor', jQuery('input#color5').val());
                // jQuery('div.mjtc-support-sorting').css('backgroundColor', jQuery('input#color2').val());
                // jQuery('div.mjtc-support-sorting').css('color', jQuery('input#color2').val());
                // jQuery('div.mjtc-support-sorting').css('borderColor', jQuery('input#color5').val());
                jQuery('div.mjtc-support-wrapper').css('borderColor', jQuery('input#color5').val());
                jQuery('.name span.mjtc-support-value').css('color', jQuery('input#color4').val());
                // jQuery('div.mjtc-support-data .mjtc-support-title-anchor').css('color', jQuery('input#color1').val());
                jQuery('div.mjtc-support-data span.mjtc-support-title').css('color', jQuery('input#color2').val());
                jQuery('div.mjtc-support-data span.mjtc-support-value').css('color', jQuery('input#color4').val());
                jQuery('div.mjtc-support-wrapper div.mjtc-support-data1 div.mjtc-support-data-row .mjtc-support-data-tit').css('color', jQuery('input#color2').val());
                jQuery('div.mjtc-support-wrapper div.mjtc-support-data1 div.mjtc-support-data-row .mjtc-support-data-val').css('color', jQuery('input#color4').val());
                jQuery('.mjtc-support-breadcrumps span').css('color', jQuery('input#color7').val());
                jQuery('.mjtc-support-ticket-detail-wrapper-color, .mjtc-support-cont-wrapper-color').css('color', '#fff');
                jQuery('div.mjtc-support-top-cirlce-count-wrp').css('backgroundColor', jQuery('input#color7').val());
                jQuery('div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-status').css('borderColor', jQuery('input#color5').val());
                jQuery('select.mjtc-support-sorting-select').css('borderColor', jQuery('input#color5').val());
                jQuery('div.mjtc-support-search-wrp').css('borderColor', jQuery('input#color5').val());
                jQuery('select.mjtc-support-sorting-select').css('color', jQuery('input#color4').val());
                jQuery('.mjtc-support-button, .mjtc-support-top-sec-right-header mjtc-support-button-header.mjtc-support-button-overall-wrapper').css('color', jQuery('input#color7').val());
                jQuery('mjtc-support-search-btn').css('color', jQuery('input#color7').val());
                jQuery('.mjtc-support-wrapper-textcolor.prorty').css('color', jQuery('input#color7').val());
                // new
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-sub-heading').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-sorting-select').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper div.mjtc-support-filter-reset-btn-wrp').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-card-title').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-body-data-discription').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data-val').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data-val svg').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-value').css('color', jQuery('input#color4').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-hover-actions a').css('color', jQuery('input#color5').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-profile-wrp .ms-profile-name-wrp .ms-profile-email, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-unassign-value, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data-tit, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-row .mjtc-support-replied-messages svg').css('color', jQuery('input#color5').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper, #msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-menu-wrp, #msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-loginlogoutclass a, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-search-wrp, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-ticket-wrapper, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data .mjtc-support-last-reply-badge, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-pic').css('border-color', jQuery('input#color6').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-filter-button').css('color', jQuery('input#color7').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper #ms-tabs-wrp .ms-tabs-menu-wrp').css('background-color', jQuery('input#color7').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .ms-header-tab.mjtc-support-loginlogoutclass a').css('background-color', jQuery('input#color7').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-top-sec-right-header a').css('color', jQuery('input#color7').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-search-filter-btn').css('background-color', jQuery('input#color7').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper a.mjtc-myticket-link.active').css('border-color', jQuery('input#color1').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-sorting-select, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-admin-sort-btn').css('background-color', jQuery('input#color7').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-ticket-wrapper, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-pic, #msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-data1 .mjtc-support-data-val .mjtc-support-staff-logo-wrp').css('background-color', jQuery('input#color7').val());
                jQuery('#msadmin-wrapper div.ms-main-up-wrapper .mjtc-support-cont-main-wrapper .mjtc-support-filter-button.mjtc-support-reset-btn').css('color', 'transparent');

            }

        ";
        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
        ?>
    </div>
</div>
