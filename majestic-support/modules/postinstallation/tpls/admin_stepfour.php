<?php
if (!defined('ABSPATH')) die('Restricted Access');
$MJTC_ticketidsequence = array(
    (object) array('id' => '0', 'text' => esc_html(__('Random', 'majestic-support'))),
    (object) array('id' => '1', 'text' => esc_html(__('Sequential', 'majestic-support')))
    );
$type = array(
    (object) array('id' => '0', 'text' => esc_html(__('Days', 'majestic-support'))),
    (object) array('id' => '1', 'text' => esc_html(__('Hours', 'majestic-support')))
    );
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
                            <span class="tab_icon">
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
                        <li class="header-parts active">
                            <span class="tab_icon">
                                <span class="header-parts-number">
                                    <?php echo esc_html( ++$header_parts_number ); ?>
                                </span>
                                <span class="text active"><?php echo esc_html(__('Feedback Settings','majestic-support')); ?></span>
                            </span>
                        </li>
                        <li class="header-parts">
                            <span class="tab_icon">
                                <span class="header-parts-number">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </span>
                                <span class="text"><?php echo esc_html(__('Complete','majestic-support')); ?></span>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="post-installtion-content_wrapper_right">
                    <div class="mjtc-admin-title-installtion">
                        <span class="ms_heading">
                            <?php echo esc_html(__('Feedback Settings','majestic-support')); ?>
                            <?php
                                $MJTC_tran_opt = MJTC_includer::MJTC_getModel('majesticsupport')->getInstalledTranslationKey();
                                $MJTC_steps = esc_html(__('Step 4 of ','majestic-support'));
                                $MJTC_steps .= $header_parts_number;
                            ?>
                            <span class="heading-post-ins ms-config-steps"><?php echo esc_html($MJTC_steps); ?></span>
                        </span>
                        <div class="close-button-bottom">
                            <a href="admin.php?page=majesticsupport" class="close-button" title="<?php echo esc_attr(__('Skip Setup','majestic-support'));?>">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                            </a>
                        </div>
                    </div>
                    <form id="majesticsupport-form-ins" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_postinstallation&task=save&action=mstask"),"save")); ?>">
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Feedback Email Delay Type','majestic-support')); ?><?php echo esc_html(': ');?>
                            </div>
                            <div class="field">
                                 <?php echo wp_kses(MJTC_formfield::MJTC_select('feedback_email_delay_type', $type , isset(majesticsupport::$_data[0]['feedback_email_delay_type']) ? majesticsupport::$_data[0]['feedback_email_delay_type'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__('Set Email Delay Time','majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Feedback Email Delay','majestic-support')); ?><?php echo esc_html(': ');?>
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('feedback_email_delay', isset(majesticsupport::$_data[0]['feedback_email_delay']) ? majesticsupport::$_data[0]['feedback_email_delay'] : '', array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__('Set Email Delay','majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-button-part right-align">
                            <a class="back mjtc-btn mjtc-btn-primary" href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_postinstallation&mjslay=steptwo')); ?>">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
                                <?php echo esc_html(__('Back','majestic-support')); ?>
                            </a>
                            <a class="next-step mjtc-btn mjtc-btn-primary" href="#" onclick="document.getElementById('majesticsupport-form-ins').submit();" >
                                <?php echo esc_html(__('Next','majestic-support')); ?>
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'postinstallation_save'), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('step', 4), MJTC_ALLOWED_TAGS); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
