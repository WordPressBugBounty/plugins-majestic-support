<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php MJTC_message::MJTC_getMessage(); ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('gdpr'); ?>
        <div id="msadmin-data-wrp">
            <?php if (!empty(majesticsupport::$_data[0])) { ?>
                <table id="majestic-support-table">
                    <tr class="majestic-support-table-heading">
                        <th class="left majestic-support-table-title"><?php echo esc_html(__('Field Title', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-subject"><?php echo esc_html(__('Field Text', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-required"><?php echo esc_html(__('Required', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-ordering"><?php echo esc_html(__('Ordering', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-linktype"><?php echo esc_html(__('Link Type', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-pagetitle"><?php echo esc_html(__('Link', 'majestic-support')); ?></th>
                        <th class="majestic-support-table-actions"><?php echo esc_html(__('Action', 'majestic-support')); ?></th>
                    </tr>
                    <?php
                    foreach (majesticsupport::$_data[0] AS $MJTC_field) {
                        $MJTC_required = ($MJTC_field->required == 1) ? __('Yes', 'majestic-support') : __('No', 'majestic-support');
                        $MJTC_class = ($MJTC_field->required == 1) ? 'majestic-support-yes' : 'majestic-support-no';
                        $MJTC_termsandconditions_text = '';
                        $MJTC_termsandconditions_linktype = '';
                        $MJTC_termsandconditions_link = '';
                        $MJTC_termsandconditions_page = '';
                        if(isset($MJTC_field->userfieldparams) && $MJTC_field->userfieldparams != '' ){
                            $MJTC_userfieldparams = json_decode($MJTC_field->userfieldparams,true);
                            $MJTC_termsandconditions_text = isset($MJTC_userfieldparams['termsandconditions_text']) ? $MJTC_userfieldparams['termsandconditions_text'] :'' ;
                            $MJTC_termsandconditions_linktype = isset($MJTC_userfieldparams['termsandconditions_linktype']) ? $MJTC_userfieldparams['termsandconditions_linktype'] :'' ;
                            $MJTC_termsandconditions_link = isset($MJTC_userfieldparams['termsandconditions_link']) ? $MJTC_userfieldparams['termsandconditions_link'] :'' ;
                            $MJTC_termsandconditions_page = isset($MJTC_userfieldparams['termsandconditions_page']) ? $MJTC_userfieldparams['termsandconditions_page'] :'' ;
                            if($MJTC_termsandconditions_linktype == 2){
                                $MJTC_page_title_link = get_the_title($MJTC_termsandconditions_page);
                            }else{
                                $MJTC_page_title_link = $MJTC_termsandconditions_link;
                            }
                        }?>
                        <tr class="mjtc-filter-form-data">
                            <td class="left majestic-support-table-title">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php echo esc_html(__('Field Title', 'majestic-support'));echo esc_html(" : "); ?>
                                </span>
                                <a href="?page=majesticsupport_gdpr&mjslay=addgdprfield&majesticsupportid=<?php echo esc_attr($MJTC_field->id); ?>" title="<?php echo esc_attr(__('Field Title','majestic-support')); ?>">
                                    <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_field->fieldtitle)); ?>
                                </a>
                            </td>
                            <td class="majestic-support-table-subject">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php echo esc_html(__('Field Text', 'majestic-support'));echo esc_html(" : "); ?>
                                </span>
                                <?php echo esc_html($MJTC_termsandconditions_text); ?>
                            </td>
                            <td class="majestic-support-table-required <?php echo esc_attr($MJTC_class); ?>">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php echo esc_html(__('Required', 'majestic-support'));echo esc_html(" : "); ?>
                                </span>
                                <span class="majestic-support-table-status-dot"></span><?php echo esc_html($MJTC_required); ?>
                            </td>
                            <td class="majestic-support-table-ordering">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php echo esc_html(__('Ordering', 'majestic-support')); echo esc_html(" : "); ?>
                                </span>
                                <?php  echo esc_html($MJTC_field->ordering); ?>
                            </td>
                            <td class="majestic-support-table-linktype">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php echo esc_html(__('Link Type', 'majestic-support')); echo esc_html(" : "); ?>
                                </span>
                                <?php if($MJTC_termsandconditions_linktype == 2){
                                    echo esc_html(__('WordPress Page','majestic-support'));
                                }else if($MJTC_termsandconditions_linktype == 1){
                                    echo esc_html(__('Direct URL','majestic-support'));
                                }else{
                                    echo esc_html(__('None','majestic-support'));
                                } ?>
                            </td>
                            <td class="majestic-support-table-pagetitle">
                                <span class="majestic-support-table-responsive-heading">
                                    <?php echo esc_html(__('Page Title or URL', 'majestic-support')); echo esc_html(" : "); ?>
                                </span>
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_page_title_link)); ?>
                            </td>
                            <td class="majestic-support-table-actions">
                                <a title="<?php echo esc_attr(__('Edit','majestic-support')); ?>" class="action-btn" href="?page=majesticsupport_gdpr&mjslay=addgdprfield&majesticsupportid=<?php echo esc_attr($MJTC_field->id); ?>">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></svg>
                                </a>
                                <a title="<?php echo esc_attr(__('Delete','majestic-support')); ?>" class="action-btn" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_gdpr&task=deletegdpr&action=mstask&gdprid='.esc_attr($MJTC_field->id),'delete-gdpr-'.esc_attr($MJTC_field->id))) ;?>">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    <?php
            }
                ?>
                </table>
        </div>
            <?php
        } else {
            MJTC_layout::MJTC_getNoRecordFound();
        }
        ?>
    </div>
</div>
