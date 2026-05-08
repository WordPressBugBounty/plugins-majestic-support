<?php
if (!defined('ABSPATH')) die('Restricted Access');
$MJTC_tran_opt = MJTC_includer::MJTC_getModel('majesticsupport')->getInstalledTranslationKey();
$header_parts_number = 0;
 ?>
<div id="mjtc-spt-admin-wrapper">
    <div id="mjtc-spt-cparea">
        <div id="ms-main-wrapper" class="post-installation">
            <div class="post-installtion-content-wrapper">
                <div class="post-installtion-content-header">
                    <div class="post-installtion-content-header_logo_img_section">
                
                        <img alt="<?php echo esc_attr(__('Image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL).'includes/images/postinstallation/logo.png';?>" />
                    </div>
                    <ul class="update-header-img">
                        <li class="header-parts active">
                            <span class="tab_icon">
                                <span class="header-parts-number">
                                    <?php echo esc_html( ++$header_parts_number ); ?>
                                </span>
                                <span class="text"><?php echo esc_html(__('General Settings','majestic-support')); ?></span>
                            </span>
                        </li>
                        <li class="header-parts active">
                            <aspan class="tab_icon">
                                <span class="header-parts-number">
                                    <?php echo esc_html( ++$header_parts_number ); ?>
                                </span>
                                <span class="text"><?php echo esc_html(__('Ticket Settings','majestic-support')); ?></span>
                            </span>
                        </li>
                        <li class="header-parts active">
                           <span class="tab_icon">
                                <span class="header-parts-number">
                                    <?php echo esc_html( ++$header_parts_number ); ?>
                                </span>
                                <span class="text"><?php echo esc_html(__('System Emails','majestic-support')); ?></span>
                            </span>
                        </li>
                        <?php if(MJTC_includer::MJTC_getModel('majesticsupport')->getInstalledTranslationKey()){ ?>
                            <li class="header-parts active">
                                <span class="tab_icon">
                                    <span class="header-parts-number">
                                        <?php echo esc_html( ++$header_parts_number ); ?>
                                    </span>
                                    <span class="text"><?php echo esc_html(__('Translation','majestic-support')); ?></span>
                                </span>
                            </li>
                        <?php } ?>
                        <?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
                            <li class="header-parts active">
                                <span class="tab_icon">
                                    <span class="header-parts-number">
                                        <?php echo esc_html( ++$header_parts_number ); ?>
                                    </span>
                                    <span class="text"><?php echo esc_html(__('Feedback Settings','majestic-support')); ?></span>
                                </span>
                            </li>
                        <?php } ?>
                        <li class="header-parts active">
                            <span class="tab_icon">
                                <span class="header-parts-number">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </span>
                                <span class="text active"><?php echo esc_html(__('Complete','majestic-support')); ?></span>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="post-installtion-content_wrapper_right">
                    <div class="mjtc-admin-title-installtion">
                        <span class="ms_heading">
                            <?php echo esc_html(__('Settings Complete','majestic-support')); ?>
                        </span>
                        <div class="close-button-bottom">
                            <a href="admin.php?page=majesticsupport" class="close-button" title="<?php echo esc_attr(__('Skip Setup','majestic-support'));?>">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                            </a>
                        </div>
                    </div>
                    <form id="majesticsupport-form-ins" method="post" action="#">
                        <div class="ms_img_wrp">
                            <img  src="<?php echo esc_url(MJTC_PLUGIN_URL).'includes/images/postinstallation/complete-icon.png';?>" alt="Seting Log" title="Setting Logo">
                        </div>
                        <div class="ms_text_below_img">
                            <?php echo esc_html(__('Setting you applied has been saved successfully.','majestic-support'));?>
                        </div>
                        <div class="pic-button-part  right-align">
                            <a class="next-step finish mjtc-btn mjtc-btn-primary" href="?page=majesticsupport">
                                <?php echo esc_html(__('Finish','majestic-support')); ?>
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
