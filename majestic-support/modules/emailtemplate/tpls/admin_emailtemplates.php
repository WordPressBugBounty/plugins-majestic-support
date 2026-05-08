<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php MJTC_message::MJTC_getMessage(); ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('emailtemplates'); ?>
        <?php
            $MJTC_nonce_id = isset(majesticsupport::$_data[0]->id) ? majesticsupport::$_data[0]->id : '';
        ?>
        <div id="msadmin-data-wrp">
            <div class="masdmin-data-email-tmpltewrp">
                <div class="mjtc-email-menu">
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'tk-nw') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=tk-nw" title="<?php echo esc_attr(__('New Ticket','majestic-support')); ?>"><?php echo esc_html(__('New Ticket', 'majestic-support')); ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'sntk-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=sntk-tk" title="<?php echo esc_attr(__('Agent Ticket','majestic-support')); ?>"><?php echo esc_html(__('Agent Ticket', 'majestic-support')); ?><?php if (!in_array('agent', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'ew-sm') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=ew-sm" title="<?php echo esc_attr(__('New Agent','majestic-support')); ?>"><?php echo esc_html(__('New Agent', 'majestic-support')); ?><?php if (!in_array('agent', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'rs-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=rs-tk" title="<?php echo esc_attr(__('Reassign Ticket','majestic-support')); ?>"><?php echo esc_html(__('Reassign Ticket', 'majestic-support')); ?><?php if (!in_array('agent', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'cl-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=cl-tk" title="<?php echo esc_attr(__('Close Ticket','majestic-support')); ?>"><?php echo esc_html(__('Close Ticket', 'majestic-support')); ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'dl-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=dl-tk" title="<?php echo esc_attr(__('Delete Ticket','majestic-support')); ?>"><?php echo esc_html(__('Delete Ticket', 'majestic-support')); ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'mo-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=mo-tk" title="<?php echo esc_attr(__('Mark Overdue','majestic-support')); ?>"><?php echo esc_html(__('Mark Overdue', 'majestic-support')); ?><?php if (!in_array('overdue', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'be-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=be-tk" title="<?php echo esc_attr(__('Ban Email','majestic-support')); ?>"><?php echo esc_html(__('Ban Email', 'majestic-support')); ?><?php if (!in_array('banemail', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'be-trtk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=be-trtk" title="<?php echo esc_attr(__('Ban Email Try To Create Ticket','majestic-support')); ?>"><?php echo esc_html(__('Ban Email Try To Create Ticket', 'majestic-support')); ?><?php if (!in_array('banemail', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'dt-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=dt-tk" title="<?php echo esc_attr(__('Department Transfer','majestic-support')); ?>"><?php echo esc_html(__('Department Transfer', 'majestic-support')); ?><?php if (!in_array('actions', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'ebct-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=ebct-tk" title="<?php echo esc_attr(__('Ban Email and Close Ticket', 'majestic-support')); ?>"><?php echo esc_html(__('Ban Email and Close Ticket', 'majestic-support')); ?><?php if (!in_array('banemail', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'ube-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=ube-tk" title="<?php echo esc_attr(__('Unban Email', 'majestic-support')); ?>"><?php echo esc_html(__('Unban Email', 'majestic-support')); ?><?php if (!in_array('banemail', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'rsp-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=rsp-tk" title="<?php echo esc_attr(__('Response Ticket', 'majestic-support')); ?>"><?php echo esc_html(__('Response Ticket', 'majestic-support')); ?><?php if (!in_array('agent', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'rpy-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=rpy-tk" title="<?php echo esc_attr(__('Reply Ticket', 'majestic-support')); ?>"><?php echo esc_html(__('Reply Ticket', 'majestic-support')); ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'tk-ew-ad') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=tk-ew-ad" title="<?php echo esc_attr(__('New Ticket Admin Alert', 'majestic-support')); ?>"><?php echo esc_html(__('New Ticket Admin Alert', 'majestic-support')); ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'lk-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=lk-tk" title="<?php echo esc_attr(__('Lock Ticket', 'majestic-support')); ?>"><?php echo esc_html(__('Lock Ticket', 'majestic-support')); ?><?php if (!in_array('actions', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'ulk-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=ulk-tk" title="<?php echo esc_attr(__('Unlock Ticket', 'majestic-support')); ?>"><?php echo esc_html(__('Unlock Ticket', 'majestic-support')); ?><?php if (!in_array('actions', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'minp-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=minp-tk" title="<?php echo esc_attr(__('In Progress Ticket', 'majestic-support')); ?>"><?php echo esc_html(__('In Progress Ticket', 'majestic-support')); ?><?php if (!in_array('actions', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'pc-tk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=pc-tk" title="<?php echo esc_attr(__('Ticket Priority Is Changed By', 'majestic-support')); ?>"><?php echo esc_html(__('Ticket Priority Is Changed By', 'majestic-support')); ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'ml-ew') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=ml-ew" title="<?php echo esc_attr(__('New Mail Received', 'majestic-support')); ?>"><?php echo esc_html(__('New Mail Received', 'majestic-support')); ?><?php if (!in_array('mail', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'ml-rp') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=ml-rp" title="<?php echo esc_attr(__('New Mail Message Received', 'majestic-support')); ?>"><?php echo esc_html(__('New Mail Message Received', 'majestic-support')); ?><?php if (!in_array('mail', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'fd-bk') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=fd-bk" title="<?php echo esc_attr(__('Feedback Email To User', 'majestic-support')); ?>"><?php echo esc_html(__('Feedback Email To User', 'majestic-support')); ?><?php if (!in_array('feedback', majesticsupport::$_active_addons)) { ?><span style="color: red;"> *</span><?php } ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'no-rp') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=no-rp" title="<?php echo esc_attr(__('User Reply On Closed Ticket', 'majestic-support')); ?>"><?php echo esc_html(__('User Reply On Closed Ticket', 'majestic-support')); ?></a></span>
                    <span class="mjtc-email-menu-link <?php if (majesticsupport::$_data[1] == 'del-data') echo esc_attr('selected'); ?>"><a class="mjtc-email-link" href="?page=majesticsupport_emailtemplate&for=del-data" title="<?php echo esc_attr(__('Data Deleted', 'majestic-support')); ?>"><?php echo esc_html(__('Data Deleted', 'majestic-support')); ?></a></span>
                </div>
                <!-- default template -->
                <?php
                $MJTC_class = 'mjtc-custom-email-body';
                $MJTC_showformdata = false;
                if (!in_array(majesticsupport::$_data[1], ['del-data','ml-rp','ml-ew','rpy-tk','rsp-tk','ube-tk','be-trtk','be-tk','dl-tk','ew-sm']) && in_array('multiform', majesticsupport::$_active_addons)) {
                    $MJTC_showformdata = true;
                }
                if ($MJTC_showformdata || in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
                    $MJTC_class = 'mjtc-custom-email-body'; ?>
                    <div class="mjtc-support-email-templates-wrapper">
                        <div class="mjtc-support-default-template-section">
                            <div class="mjtc-support-default-template-title">
                                <?php echo esc_html(__('Default Email Template', 'majestic-support')); ?>
                            </div>
                            <div class="mjtc-support-default-template-description">
                                <?php
                                if (in_array('multiform', majesticsupport::$_active_addons) && in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
                                    echo esc_html(__('This template is used for all forms and languages unless a specific custom template is created.', 'majestic-support'));
                                } elseif (in_array('multiform', majesticsupport::$_active_addons)) {
                                    echo esc_html(__('This template is used for all forms unless a specific form template is created.', 'majestic-support'));
                                } else {
                                    echo esc_html(__('This template is used for all languages unless a specific language template is created.', 'majestic-support'));
                                } ?>
                            </div>
                            <a href="?page=majesticsupport_emailtemplate&for=<?php echo esc_attr(majesticsupport::$_data[1]); ?>&defaultTemp=1" class="mjtc-support-button mjtc-support-view-edit-default">
                                <?php echo esc_html(__('View/Edit Default Template', 'majestic-support')); ?>
                            </a>
                        </div>

                        <div class="mjtc-support-form-specific-templates-section">
                        <div class="mjtc-support-form-specific-templates-title">
                            <?php
                            if (in_array('multiform', majesticsupport::$_active_addons) && in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
                                echo esc_html(__('Form & Language-Specific Email Templates', 'majestic-support'));
                            } elseif (in_array('multiform', majesticsupport::$_active_addons)) {
                                echo esc_html(__('Form-Specific Email Templates', 'majestic-support'));
                            } else {
                                echo esc_html(__('Language-Specific Email Templates', 'majestic-support'));
                            } ?>
                        </div>
                        <div class="mjtc-support-form-specific-templates-description">
                            <?php
                            if (in_array('multiform', majesticsupport::$_active_addons) && in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
                                echo esc_html(__('Customize email content for individual form, for specific language, or a combination of both. If no custom template exists for a given form or language, the default template will be used.', 'majestic-support'));
                            } elseif (in_array('multiform', majesticsupport::$_active_addons)) {
                                echo esc_html(__('Customize email content for individual form. If no custom template exists for a form, the default template will be used.', 'majestic-support'));
                            } else {
                                echo esc_html(__('Customize email content for specific language. If no custom template exists for a language, the default template will be used.', 'majestic-support'));
                            } ?>
                        </div>
                        <div class="mjtc-support-form-selection-wrapper">
                            <form class="mjtc-support-form" method="post" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'emailtemplate', 'task'=>'savecustomemailtemplate')),"save-form-email-template")); ?>">
                                <?php
                                if ($MJTC_showformdata) {
                                    echo wp_kses(MJTC_formfield::MJTC_select('multiformid', MJTC_includer::MJTC_getModel('multiform')->getMultiFormForCombobox(majesticsupport::$_data[0]->templatefor), '', esc_html(__('Select A Form', 'majestic-support')), array('class' => 'inputbox one mjtc-support-select-form')), MJTC_ALLOWED_TAGS);                            
                                }
                                if (in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) {
                                    echo wp_kses(MJTC_formfield::MJTC_select("language_id", MJTC_includer::MJTC_getModel("multilanguageemailtemplates")->getLangForCombobox() ,'',__("Select A Language", "majestic-support"), array("class" => "inputbox one mjtc-support-select-form")), MJTC_ALLOWED_TAGS);
                                } ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Create Template', 'majestic-support')), array('class' => 'mjtc-support-button mjtc-support-create-edit-template')), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', ''), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('templatefor', majesticsupport::$_data[0]->templatefor), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('for', majesticsupport::$_data[1]), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'emailtemplate_savecustomemailtemplate'), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                            </form>
                        </div>
                            <?php
                            if (!empty(majesticsupport::$_data[0]->multiTemplates)) { ?>
                                <div class="mjtc-support-existing-custom-templates">
                                    <div class="mjtc-support-existing-custom-templates-title">
                                        <?php echo esc_html(__('Existing Custom Templates', 'majestic-support')); ?>
                                    </div>
                                    <table class="mjtc-support-custom-templates-table" id="ms-import-data-result-table">
                                        <thead>
                                            <tr>
                                                <?php
                                                if ($MJTC_showformdata) { ?>
                                                    <th><?php echo esc_html(__('Form Name', 'majestic-support')); ?></th>
                                                    <th><?php echo esc_html(__('Department Name', 'majestic-support')); ?></th>
                                                    <?php
                                                }
                                                if (in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) { ?>
                                                    <th><?php echo esc_html(__('Language', 'majestic-support')); ?></th>
                                                    <?php
                                                } ?>
                                                <th><?php echo esc_html(__('Actions', 'majestic-support')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach (majesticsupport::$_data[0]->multiTemplates as $MJTC_key => $MJTC_multiTemplate) {
                                                if (empty($MJTC_multiTemplate->formname) && empty($MJTC_multiTemplate->language)) {
                                                    continue;
                                                } ?>
                                                <tr>
                                                    <?php
                                                    if ($MJTC_showformdata) { ?>
                                                        <td><?php echo esc_html($MJTC_multiTemplate->formname); ?></td>
                                                        <td><?php echo esc_html($MJTC_multiTemplate->departmentname); ?></td>
                                                        <?php
                                                    }
                                                    if (in_array('multilanguageemailtemplates', majesticsupport::$_active_addons)) { ?>
                                                        <td>
                                                            <?php 
                                                            if (!empty($MJTC_multiTemplate->language_name)) {
                                                                echo esc_html($MJTC_multiTemplate->language_name);
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php
                                                    } ?>
                                                    <td>
                                                        <a href="?page=majesticsupport_emailtemplate&for=<?php echo esc_attr(majesticsupport::$_data[1]); ?>&formid=<?php echo esc_html($MJTC_multiTemplate->formid); ?>&langcode=<?php echo esc_html($MJTC_multiTemplate->language); ?>" class="mjtc-support-action-link mjtc-support-edit"><?php echo esc_html(__('Edit', 'majestic-support')); ?></a>
                                                        <span class="mjtc-support-action-separator">|</span>
                                                        <a onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete?', 'majestic-support')); ?>'); " href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_emailtemplate&task=deleteformemailtemplate&action=mstask&templateid='.esc_attr($MJTC_multiTemplate->template_id).'&for='.esc_attr(majesticsupport::$_data[1]).'&source='.esc_attr($MJTC_multiTemplate->source),'delete-template-'.$MJTC_multiTemplate->template_id));?>" class="mjtc-support-action-link mjtc-support-delete"><?php echo esc_html(__('Delete', 'majestic-support')); ?></a>
                                                        <span class="mjtc-support-action-separator">|</span>
                                                        <a href="?page=majesticsupport_emailtemplate&for=<?php echo esc_attr(majesticsupport::$_data[1]); ?>&formid=<?php echo esc_html($MJTC_multiTemplate->formid); ?>&langcode=<?php echo esc_html($MJTC_multiTemplate->language); ?>" class="mjtc-support-action-link mjtc-support-preview"><?php echo esc_html(__('Preview', 'majestic-support')); ?></a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php 
                            } ?>
                        </div>
                    </div>
                    <?php
                } ?>
                <!-- custom template -->
                <form method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("?page=majesticsupport_emailtemplate&task=saveemailtemplate"),"save-email-template-".$MJTC_nonce_id)); ?>">
                    <div class="mjtc-email-body <?php echo esc_attr($MJTC_class); ?>">
                        <!-- Now add the Dropdown for the Languages -->
                        <?php
                        if (!empty(majesticsupport::$_data[0]->language_name) || !empty(majesticsupport::$_data[0]->multiformname)) { ?>
                            <div class="mjtc-support-card-wrapper">
                                <?php
                                if (!empty(majesticsupport::$_data[0]->multiformname)) { ?>
                                    <div class="mjtc-support-card-item">
                                        <div class="mjtc-support-card-icon">
                                            <img alt="<?php echo esc_attr(__('Configuration','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/left-icons/menu/system-email.png" />
                                        </div>
                                        <div class="mjtc-support-card-content">
                                            <div class="mjtc-support-card-label">
                                                <?php echo esc_html(__('Form', 'majestic-support')).':'; ?>
                                            </div>
                                            <div class="mjtc-support-card-value mjtc-support-card-value-bold">
                                                <?php echo esc_html(majesticsupport::$_data[0]->multiformname); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                if (!empty(majesticsupport::$_data[0]->language_name)) { ?>
                                    <div class="mjtc-support-card-item">
                                        <div class="mjtc-support-card-icon">
                                            <img alt="<?php echo esc_attr(__('Configuration','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/left-icons/menu/download.png" />
                                        </div>
                                        <div class="mjtc-support-card-content">
                                            <div class="mjtc-support-card-label">
                                                <?php echo esc_html(__('Language:', 'majestic-support')); ?>
                                            </div>
                                            <div class="mjtc-support-card-value mjtc-support-card-value-bold">
                                                <?php echo esc_html(majesticsupport::$_data[0]->language_name); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                            <?php
                        } ?>
                        <?php /*echo wp_kses(apply_filters( 'ms_get_multilanguage_dropdown',''), MJTC_ALLOWED_TAGS);*/ ?>
                        <div class="mjtc-form-wrapper">
                            <div class="a-mjtc-form-title"><?php echo esc_html(__('Subject', 'majestic-support')); ?></div>
                            <div class="a-mjtc-form-field"><?php echo wp_kses(MJTC_formfield::MJTC_text('subject', majesticsupport::$_data[0]->subject, array('class' => 'inputbox', 'style' => 'width:100%;')), MJTC_ALLOWED_TAGS) ?></div>
                        </div>
                        <div class="mjtc-form-wrapper">
                            <div class="a-mjtc-form-title"><?php echo esc_html(__('Body', 'majestic-support')); ?></div>
                            <div class="a-mjtc-form-field">
                                <?php 
                                $settings = array(
                                    'media_buttons' => false,
                                    'tinymce' => array(
                                        // This injects the CSS directly into the editor iframe
                                        'content_style' => ".mce-item-table { border: none !important; width: 100% !important; border-collapse: collapse !important; } .mce-item-table td { border: none !important; }"
                                    ),
                                );
                                wp_editor(majesticsupport::$_data[0]->body, 'body', $settings); 
                                ?>  
                            </div>
                        </div>
                        <div class="mjtc-email-parameters">
                            <div class="mjtc-email-parameter-heading"><?php echo esc_html(__('Parameters', 'majestic-support')); ?></div>
                            <?php
                            if (majesticsupport::$_data[1] == 'tk-nw') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{HELP_TOPIC} : <?php echo esc_html(__('Help Topic', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL} : <?php echo esc_html(__('Email', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{MESSAGE} : <?php echo esc_html(__('Message', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'sntk-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{HELP_TOPIC} : <?php echo esc_html(__('Help Topic', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL} : <?php echo esc_html(__('Email', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{MESSAGE} : <?php echo esc_html(__('Message', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'ew-md') {
                                ?>
                                <span class="mjtc-email-paramater">{DEPARTMENT_TITLE} : <?php echo esc_html(__('Department title', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'ew-gr') {
                                ?>
                                <span class="mjtc-email-paramater">{GROUP_TITLE} : <?php echo esc_html(__('Group Title', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'ew-sm') {
                                ?>
                                <span class="mjtc-email-paramater">{AGENT_NAME} : <?php echo esc_html(__('Agent name', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'ew-ht') {
                                ?>
                                <span class="mjtc-email-paramater">{HELPTOPIC_TITLE} : <?php echo esc_html(__('Help topic title', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT_TITLE} : <?php echo esc_html(__('Department title', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'rs-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{AGENT_NAME} : <?php echo esc_html(__('Agent name', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field) ;?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'cl-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{FEEDBACKURL} : <?php echo esc_html(__('Feedback URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                    if($MJTC_field->userfieldtype != 'file'){ ?>
                                        <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                        <?php
                                    }
                                }
                            } elseif (majesticsupport::$_data[1] == 'dl-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'mo-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'be-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{EMAIL_ADDRESS} : <?php echo esc_html(__('Email Address', 'majestic-support')); ?></span>
                                <?php

                            } elseif (majesticsupport::$_data[1] == 'be-trtk') {
                                ?>
                                <span class="mjtc-email-paramater">{EMAIL_ADDRESS} : <?php echo esc_html(__('Email Address', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'dt-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT_TITLE} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'ebct-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL_ADDRESS} : <?php echo esc_html(__('Email Address', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETID} : <?php echo esc_html(__('Ticket ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'ube-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{EMAIL_ADDRESS} : <?php echo esc_html(__('Email Address', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'rsp-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL} : <?php echo esc_html(__('Email', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{MESSAGE} : <?php echo esc_html(__('Message', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'rpy-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL} : <?php echo esc_html(__('Email', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{MESSAGE} : <?php echo esc_html(__('Message', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'tk-ew-ad') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL} : <?php echo esc_html(__('Email', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{MESSAGE} : <?php echo esc_html(__('Message', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'lk-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL} : <?php echo esc_html(__('Email', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'ulk-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{EMAIL} : <?php echo esc_html(__('Email', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'minp-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'pc-tk') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKINGID} : <?php echo esc_html(__('Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY_TITLE} : <?php echo esc_html(__('Priority', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKETURL} : <?php echo esc_html(__('Ticket URL', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_HISTORY} : <?php echo esc_html(__('Ticket History', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'ml-ew') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{AGENT_NAME} : <?php echo esc_html(__('Agent name', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{MESSAGE} : <?php echo esc_html(__('Message', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'ml-rp') {
                                ?>
                                <span class="mjtc-email-paramater">{SUBJECT} : <?php echo esc_html(__('Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{AGENT_NAME} : <?php echo esc_html(__('Agent name', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{MESSAGE} : <?php echo esc_html(__('Message', 'majestic-support')); ?></span>
                                <?php
                            } elseif (majesticsupport::$_data[1] == 'fd-bk') {
                                ?>
                                <span class="mjtc-email-paramater">{USER_NAME} : <?php echo esc_html(__('User Name', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TICKET_SUBJECT} : <?php echo esc_html(__('Ticket Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{TRACKING_ID} : <?php echo esc_html(__('Ticket Tracking ID', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{CLOSE_DATE} : <?php echo esc_html(__('Close Date', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'no-rp') {
                                ?>
                                <span class="mjtc-email-paramater">{TICKET_SUBJECT} : <?php echo esc_html(__('Ticket Subject', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{DEPARTMENT} : <?php echo esc_html(__('Department', 'majestic-support')); ?></span>
                                <span class="mjtc-email-paramater">{PRIORITY} : <?php echo esc_html(__('Ticket Priority', 'majestic-support')); ?></span>
                                <?php foreach (majesticsupport::$_data[2] as $MJTC_field ) {
                                        if($MJTC_field->userfieldtype != 'file'){ ?>
                                            <span class="mjtc-email-paramater">{<?php echo esc_html($MJTC_field->field);?>} : <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?></span>
                                <?php   }
                                    }
                            } elseif (majesticsupport::$_data[1] == 'del-data') {
                                ?>
                                <span class="mjtc-email-paramater">{USERNAME} : <?php echo esc_html(__('Username', 'majestic-support')); ?></span>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="mjtc-form-button">
                            <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Save Email Template', 'majestic-support')), array('class' => 'button mjtc-form-save')), MJTC_ALLOWED_TAGS); ?>
                            <a href="admin.php?page=majesticsupport" class="mjtc-form-cancel"><?php echo esc_html(__('Cancel','majestic-support')); ?></a>
                        </div>
                        <?php
                        if(count(majesticsupport::$_active_addons) < 37 ){  ?>
                            <div class="mjtc-sugestion-alert-wrp mjtc-email-msg">
                                <div class="mjtc-sugestion-alert">
                                    <strong>
                                        <?php echo esc_html(__('Note:', 'majestic-support')); ?>
                                    </strong>
                                    <?php echo esc_html(__('Features marked with', 'majestic-support')); ?>
                                    <span>*</span>
                                    <?php echo esc_html(__('are only available with its own addon.', 'majestic-support')); ?>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>

                    <?php
                    $majesticsupport_js ="
                        jQuery(document).ready(function(){
                            jQuery('#save').click(function(){
                                var subject = jQuery('#subject').val();
                                var body = jQuery('#body').val();
                                if(subject=='' && body==''){
                                    alert('Please Fill the Subject and body');
                                    return false;
                                }
                            });
                        });
                    ";
                    wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
                    if (!empty(majesticsupport::$_data[0]->language_id)) {
                        $MJTC_language_id = majesticsupport::$_data[0]->language_id;
                    } else {
                        $MJTC_language_id = '';
                    }
                    ?>

                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('id', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('created', majesticsupport::$_data[0]->created), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('templatefor', majesticsupport::$_data[0]->templatefor), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('multiformid', majesticsupport::$_data[0]->multiformid), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('for', majesticsupport::$_data[1]), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('action', 'emailtemplate_saveemailtemplate'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('callfor', 'emailtemplate'), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('multitemp_id', majesticsupport::$_data[0]->id), MJTC_ALLOWED_TAGS); ?>
                    <?php echo wp_kses(MJTC_formfield::MJTC_hidden('lang_id', $MJTC_language_id), MJTC_ALLOWED_TAGS); ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$majesticsupport_js ="
    function scrollToFormByParam() {
        const urlParams = new URLSearchParams(window.location.search);
        const formId = urlParams.get('formid');
        const langCode = urlParams.get('langcode');
        const defaultTemp = urlParams.get('defaultTemp');

        if (formId || defaultTemp || langCode) {
            const target = jQuery('.mjtc-email-body');

            if (target.length) {
                // Define how many pixels before the target you want to scroll
                const offsetPixels = 60; // You can change this value to your preference

                jQuery('html, body').animate({
                    scrollTop: target.offset().top - offsetPixels
                }, 600);
            }
        }
    }

    jQuery(document).ready(function($) {
        jQuery('select#lang_id').prop('disabled', true);
        jQuery.validate();
        scrollToFormByParam();

        // Optional: re-run scroll when popstate triggers (back/forward buttons)
        $(window).on('popstate', scrollToFormByParam);
    });
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>
