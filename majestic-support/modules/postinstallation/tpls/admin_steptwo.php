<?php
if (!defined('ABSPATH')) die('Restricted Access');
$MJTC_yesno = array(
    (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('No', 'majestic-support')))
    );
$MJTC_ticketidsequence = array(
    (object) array('id' => '1', 'text' => esc_html(__('Random', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('Sequential', 'majestic-support')))
    );
$MJTC_owncaptchaoparend = array(
    (object) array('id' => '2', 'text' => esc_html('2')),
    (object) array('id' => '3', 'text' => esc_html('3'))
    );
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
                            <span class="tab_icon">
                                <span class="header-parts-number">
                                    <?php echo esc_html( ++$header_parts_number ); ?>
                                </span>
                                <span class="text active"><?php echo esc_html(__('Ticket Settings','majestic-support')); ?></span>
                            </span>
                        </li>
                        <li class="header-parts">
                            <span class="tab_icon">
                                <span class="header-parts-number">
                                    <?php echo esc_html( ++$header_parts_number ); ?>
                                </span>
                                <span class="text"><?php echo esc_html(__('System Emails','majestic-support')); ?></span>
                            </span>
                        </li>
                        <?php if(MJTC_includer::MJTC_getModel('majesticsupport')->getInstalledTranslationKey()){ ?>
                            <li class="header-parts">
                                <span class="tab_icon">
                                    <span class="header-parts-number">
                                        <?php echo esc_html( ++$header_parts_number ); ?>
                                    </span>
                                    <span class="text"><?php echo esc_html(__('Translation','majestic-support')); ?></span>
                                </span>
                            </li>
                        <?php } ?>
                        <?php if(in_array('feedback', majesticsupport::$_active_addons)){ ?>
                            <li class="header-parts">
                                <span class="tab_icon">
                                    <span class="header-parts-number">
                                        <?php echo esc_html( ++$header_parts_number ); ?>
                                    </span>
                                    <span class="text"><?php echo esc_html(__('Feedback Settings','majestic-support')); ?></span>
                                </span>
                            </li>
                        <?php } ?>
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
                            <?php echo esc_html(__('Ticket Settings','majestic-support')); ?>
                            <?php
                                $MJTC_steps = esc_html(__('Step 2 of ','majestic-support'));
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
                                <?php echo esc_html(__('Visitor can create ticket','majestic-support')); ?><?php echo esc_html(': ');?>
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('visitor_can_create_ticket', $MJTC_yesno , isset(majesticsupport::$_data[0]['visitor_can_create_ticket']) ? majesticsupport::$_data[0]['visitor_can_create_ticket'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Enable / Disable Open Ticket",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Ticket ID sequence','majestic-support')); ?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('ticketid_sequence', $MJTC_ticketidsequence , isset(majesticsupport::$_data[0]['ticketid_sequence']) ? majesticsupport::$_data[0]['ticketid_sequence'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Set ticket id sequential or random",'majestic-support')); ?>&nbsp;
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Maximum Tickets','majestic-support')); ?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('maximum_tickets', isset(majesticsupport::$_data[0]['maximum_tickets']) ? majesticsupport::$_data[0]['maximum_tickets'] : '', array('class' => 'inputbox ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Set Maximum Ticket Per user",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Maximum Open Tickets','majestic-support')); ?>:
                            </div>
                            <div class="field">
                               <?php echo wp_kses(MJTC_formfield::MJTC_text('maximum_open_tickets', isset(majesticsupport::$_data[0]['maximum_open_tickets']) ? majesticsupport::$_data[0]['maximum_open_tickets'] : '', array('class' => 'inputbox ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Maximum Open Tickets",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Reopen ticket within Days','majestic-support')); ?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('reopen_ticket_within_days', isset(majesticsupport::$_data[0]['reopen_ticket_within_days']) ? majesticsupport::$_data[0]['reopen_ticket_within_days'] : '', array('class' => 'inputbox ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("The ticket can be reopened within a given number of days",'majestic-support')); ?>&nbsp;
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Show Captcha to visitor on ticket form','majestic-support')); ?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('show_captcha_on_visitor_from_ticket', $MJTC_yesno , isset(majesticsupport::$_data[0]['show_captcha_on_visitor_from_ticket']) ? majesticsupport::$_data[0]['show_captcha_on_visitor_from_ticket'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Enable / Disable Captcha on Ticket Form",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Own captcha operands','majestic-support')); ?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('owncaptcha_totaloperand', $MJTC_owncaptchaoparend , isset(majesticsupport::$_data[0]['owncaptcha_totaloperand']) ? majesticsupport::$_data[0]['owncaptcha_totaloperand'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                               <?php echo esc_html(__("Select the total operands to be given",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Own captcha subtraction answer positive','majestic-support')); ?><?php echo esc_html(': ');?>
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('owncaptcha_subtractionans', $MJTC_yesno , isset(majesticsupport::$_data[0]['owncaptcha_subtractionans']) ? majesticsupport::$_data[0]['owncaptcha_subtractionans'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                               <?php echo esc_html(__("Enable / Disable Own Captcha subtraction",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Enable Print Ticket','majestic-support')); ?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('print_ticket_user', $MJTC_yesno , isset(majesticsupport::$_data[0]['print_ticket_user']) ? majesticsupport::$_data[0]['print_ticket_user'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                               <?php echo esc_html(__("Enable / Disable Print Ticket",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-button-part right-align">
                            <a class="back mjtc-btn mjtc-btn-primary" href="<?php echo esc_url(admin_url('admin.php?page=majesticsupport_postinstallation&mjslay=stepone')); ?>">
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
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('step', 2), MJTC_ALLOWED_TAGS); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
