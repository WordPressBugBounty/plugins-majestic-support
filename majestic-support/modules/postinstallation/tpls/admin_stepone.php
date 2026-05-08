<?php
if (!defined('ABSPATH')) die('Restricted Access');
$MJTC_yesno = array(
    (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
    (object) array('id' => '2', 'text' => esc_html(__('No', 'majestic-support')))
    );
$MJTC_showhide = array(
    (object) array('id' => '1', 'text' => esc_html(__('Yes', 'majestic-support'))),
    (object) array('id' => '0', 'text' => esc_html(__('No', 'majestic-support')))
    );
$MJTC_date_format = array(
    (object) array('id' => 'd-m-Y', 'text' => esc_html(__('DD-MM-YYYY' , 'majestic-support'))),
    (object) array('id' => 'm-d-Y', 'text' => esc_html(__('MM-DD-YYYY' , 'majestic-support'))),
    (object) array('id' => 'Y-m-d', 'text' => esc_html(__('YYYY-MM-DD' , 'majestic-support')))
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
                        <li class="header-parts">
                            <span class="tab_icon">
                                <span class="header-parts-number">
                                    <?php echo esc_html( ++$header_parts_number ); ?>
                                </span>
                                <span class="text"><?php echo esc_html(__('Ticket Settings','majestic-support')); ?></span>
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
                            <?php echo esc_html(__('General Settings','majestic-support')); ?>
                            <?php
                                $MJTC_steps = esc_html(__('Step 1 of ','majestic-support'));
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
                                <?php echo esc_html(__('Title','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('title', isset(majesticsupport::$_data[0]['title']) ? majesticsupport::$_data[0]['title'] : '', array('class' => 'inputbox ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Enter the site title",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Data Directory','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('data_directory', isset(majesticsupport::$_data[0]['data_directory']) ? majesticsupport::$_data[0]['data_directory'] : '', array('class' => 'inputbox ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("You need to rename the existing data directory in the file system before changing the data directory name",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Date Format','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('date_format', $MJTC_date_format , isset(majesticsupport::$_data[0]['date_format']) ? majesticsupport::$_data[0]['date_format'] : '' , esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS)?>
                            </div>
                            <div class="desc"><?php echo esc_html(__('Date format for plugin','majestic-support'));?> </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Ticket Auto Close','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('ticket_auto_close', isset(majesticsupport::$_data[0]['ticket_auto_close']) ? majesticsupport::$_data[0]['ticket_auto_close'] : '', array('class' => 'inputbox ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Ticket auto-close if user does not respond within given days",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Show Breadcrumbs','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('show_breadcrumbs', $MJTC_showhide , isset(majesticsupport::$_data[0]['show_breadcrumbs']) ? majesticsupport::$_data[0]['show_breadcrumbs'] : '', '' , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__('Show navigation in breadcrumbs','majestic-support')); ?>&nbsp;
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('File maximum size','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_text('file_maximum_size', isset(majesticsupport::$_data[0]['file_maximum_size']) ? majesticsupport::$_data[0]['file_maximum_size'] : '', array('class' => 'inputbox ms-postsetting', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__("Upload file size in KB's",'majestic-support')); ?>
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('File Extension','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_textarea('file_extension', isset(majesticsupport::$_data[0]['file_extension']) ? majesticsupport::$_data[0]['file_extension'] : '', array('class' => 'inputbox mjtc-textarea', 'data-validation' => 'required')), MJTC_ALLOWED_TAGS) ?>
                            </div>
                            <div class="desc">
                                <?php echo esc_html(__('Show navigation in breadcrumbs','majestic-support')); ?>&nbsp;
                            </div>
                        </div>
                        <div class="pic-config">
                            <div class="title">
                                <?php echo esc_html(__('Show count on tickets','majestic-support'));?>:
                            </div>
                            <div class="field">
                                <?php echo wp_kses(MJTC_formfield::MJTC_select('count_on_myticket', $MJTC_yesno , isset(majesticsupport::$_data[0]['count_on_myticket']) ? majesticsupport::$_data[0]['count_on_myticket'] : '', esc_html(__('Select Type', 'majestic-support')) , array('class' => 'inputbox ms-postsetting mjtc-select ms-postsetting ')), MJTC_ALLOWED_TAGS);?>
                            </div>
                        </div>
                        <div class="pic-button-part right-align">
                            <a class="next-step mjtc-btn mjtc-btn-primary" href="#" onclick="document.getElementById('majesticsupport-form-ins').submit();" >
                                <?php echo esc_html(__('Next Setup','majestic-support')); ?>
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'postinstallation_save'), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('step', 1), MJTC_ALLOWED_TAGS); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
