<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$majesticsupport_js ="
    function resetFrom() {
        document.getElementById('title').value = '';
        document.getElementById('majesticsupportform').submit();
    }
    jQuery(document).ready(function () {
        jQuery('table#majestic-support-table tbody').sortable({
            handle : '.ms-order-grab-column',
            update  : function () {
                jQuery('.mjtc-form-button').slideDown('slow');
                var abc =  jQuery('table#majestic-support-table tbody').sortable('serialize');
                jQuery('input#fields_ordering_new').val(abc);
            }
        });
    });
";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css', array(), '1.0.0');
MJTC_message::MJTC_getMessage(); ?>
<div id="msadmin-wrapper">
    <div id="msadmin-leftmenu">
        <?php  MJTC_includer::MJTC_getClassesInclude('msadminsidemenu'); ?>
    </div>
    <div id="msadmin-data">
        <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageTitle('products'); ?>
        <div id="msadmin-data-wrp">
            <form class="mjtc-filter-form" name="majesticsupportform" id="majesticsupportform" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_product&mjslay=products"),"products")); ?>">
                <?php echo wp_kses(MJTC_formfield::MJTC_text('title', majesticsupport::$_data['filter']['title'], array('placeholder' => esc_html(__('Product', 'majestic-support')),'class' => 'mjtc-form-input-field')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('go', esc_html(__('Search', 'majestic-support')), array('class' => 'button mjtc-form-search')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_button(esc_html(__('Reset', 'majestic-support')), esc_html(__('Reset', 'majestic-support')), array('class' => 'button mjtc-form-reset', 'onclick' => 'resetFrom();')), MJTC_ALLOWED_TAGS); ?>
                <?php echo wp_kses(MJTC_formfield::MJTC_select('pagesize', array((object) array('id'=>20,'text'=>20), (object) array('id'=>50,'text'=>50), (object) array('id'=>100,'text'=>100)), majesticsupport::$_data['filter']['pagesize'],esc_html(__("Records per page",'majestic-support')), array('class' => 'mjtc-form-select-field mjtc-right','onchange'=>'document.majesticsupportform.submit();')), MJTC_ALLOWED_TAGS); ?>
            </form>
            <?php if (!empty(majesticsupport::$_data[0])) { ?>
                <form class="msadmin-form" method="post" action="<?php echo esc_url(wp_nonce_url(admin_url("admin.php?page=majesticsupport_majesticsupport&task=saveordering"),"save-ordering")); ?>">
                    <table id="majestic-support-table">
                        <thead>
                        <tr class="majestic-support-table-heading">
                            <th class="majestic-support-table-ordering"><?php echo esc_html(__('Ordering', 'majestic-support')); ?></th>
                            <th class="left majestic-support-table-title"><?php echo esc_html(__('Product', 'majestic-support')); ?></th>
                            <th class="majestic-support-table-status"><?php echo esc_html(__('Status', 'majestic-support')); ?></th>
                            <th class="majestic-support-table-actions"><?php echo esc_html(__('Action', 'majestic-support')); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $MJTC_number = 0;
                        $MJTC_count = COUNT(majesticsupport::$_data[0]) - 1; //For zero base indexing
                        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum', 'get', 1);
                        $MJTC_islastordershow = MJTC_pagination::MJTC_isLastOrdering(majesticsupport::$_data['total'], $MJTC_pagenum);
                        foreach (majesticsupport::$_data[0] AS $MJTC_product) {
                            $MJTC_status = ($MJTC_product->status == 1) ? __('Active', 'majestic-support') : __('Disabled', 'majestic-support');
                            $MJTC_class = ($MJTC_product->status == 1) ? 'majestic-support-active' : 'majestic-support-disabled';
                            ?>

                            <tr id="id_<?php echo esc_attr($MJTC_product->id); ?>">
                                <td class="mjtc-textaligncenter ms-order-grab-column majestic-support-table-ordering">
                                    <span class="majestic-support-table-responsive-heading">
                                        <?php echo esc_html(__('Ordering', 'majestic-support')); echo esc_html(" : "); ?>
                                    </span>
                                    <div class="ms-grab-handle" title="Drag to reorder">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                                    </div>
                                </td>

                                <td class="left majestic-support-table-title">
                                    <span class="majestic-support-table-responsive-heading">
                                        <?php
                                        echo esc_html(__('Title', 'majestic-support'));
                                        echo esc_html(" : ");
                                        ?>
                                    </span>
                                    <a title="<?php echo esc_attr(__('Product','majestic-support')); ?>" href="?page=majesticsupport_product&mjslay=addproduct&majesticsupportid=<?php echo esc_attr($MJTC_product->id); ?>">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_product->product)); ?>
                                    </a>
                                </td>

                                <td class="majestic-support-table-status <?php echo esc_attr($MJTC_class); ?>">
                                    <span class="majestic-support-table-responsive-heading">
                                        <?php echo esc_html(__('Status', 'majestic-support'));
                                        echo esc_html(" : ");
                                        ?>
                                    </span>
                                    <a title="<?php echo esc_attr(__('Status','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_product&task=changestatus&action=mstask&productid='. $MJTC_product->id,'change-status-'.$MJTC_product->id));?>">
                                        <span class="majestic-support-table-status-dot"></span><?php echo esc_html($MJTC_status); ?>
                                    </a>
                                </td>
                                <td class="majestic-support-table-actions">
                                    <a title="<?php echo esc_attr(__('Edit','majestic-support')); ?>" class="action-btn majestic-support-table-edit-action" href="?page=majesticsupport_product&mjslay=addproduct&majesticsupportid=<?php echo esc_attr($MJTC_product->id); ?>">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></svg>
                                    </a>
                                    <a title="<?php echo esc_attr(__('Delete','majestic-support')); ?>" class="action-btn majestic-support-table-delete-action" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_product&task=deleteproduct&action=mstask&productid='.esc_attr($MJTC_product->id),'delete-product-'.$MJTC_product->id));?>">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        <?php
                        $MJTC_number++;
                    }
                    ?>
                    </tbody>
                    </table>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('fields_ordering_new', '123'), MJTC_ALLOWED_TAGS); ?>
                       <?php echo wp_kses(MJTC_formfield::MJTC_hidden('form_request', 'majesticsupport'), MJTC_ALLOWED_TAGS); ?>
                       <?php echo wp_kses(MJTC_formfield::MJTC_hidden('ordering_for', 'product'), MJTC_ALLOWED_TAGS); ?>
                       <?php echo wp_kses(MJTC_formfield::MJTC_hidden('pagenum_for_ordering', MJTC_request::MJTC_getVar('pagenum', 'get', 1)), MJTC_ALLOWED_TAGS); ?>
                       <div class="mjtc-form-button" style="display: none;">
                           <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('save', esc_html(__('Save Ordering', 'majestic-support')), array('class' => 'button mjtc-form-save')), MJTC_ALLOWED_TAGS); ?>
                       </div>
                   </form>
                <?php
                if (majesticsupport::$_data[1]) {
                    $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                }
            } else {
                MJTC_layout::MJTC_getNoRecordFound();
            }
            ?>
        </div>
    </div>
</div>
