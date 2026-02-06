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
wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css');
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
                            <th><?php echo esc_html(__('Ordering', 'majestic-support')); ?></th>
                            <th class="left"><?php echo esc_html(__('Product', 'majestic-support')); ?></th>
                            <th><?php echo esc_html(__('Status', 'majestic-support')); ?></th>
                            <th><?php echo esc_html(__('Action', 'majestic-support')); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $MJTC_number = 0;
                        $MJTC_count = COUNT(majesticsupport::$_data[0]) - 1; //For zero base indexing
                        $MJTC_pagenum = MJTC_request::MJTC_getVar('pagenum', 'get', 1);
                        $MJTC_islastordershow = MJTC_pagination::MJTC_isLastOrdering(majesticsupport::$_data['total'], $MJTC_pagenum);
                        foreach (majesticsupport::$_data[0] AS $product) {
                            $status = ($product->status == 1) ? 'good.png' : 'close.png';
                            ?>

                            <tr id="id_<?php echo esc_attr($product->id); ?>">
                                <td class="mjtc-textaligncenter ms-order-grab-column">
                                    <span class="majestic-support-table-responsive-heading">
                                        <?php echo esc_html(__('Ordering', 'majestic-support')); echo esc_html(" : "); ?>
                                    </span>
                                    <img alt="<?php echo esc_html(__('grab','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . 'includes/images/list-full.png'?>"/>
                                </td>

                                <td class="left">
                                    <span class="majestic-support-table-responsive-heading">
                                        <?php
                                        echo esc_html(__('Title', 'majestic-support'));
                                        echo esc_html(" : ");
                                        ?>
                                    </span>
                                    <a title="<?php echo esc_attr(__('Product','majestic-support')); ?>" href="?page=majesticsupport_product&mjslay=addproduct&majesticsupportid=<?php echo esc_attr($product->id); ?>">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($product->product)); ?>
                                    </a>
                                </td>

                                <td>
                                    <span class="majestic-support-table-responsive-heading">
                                        <?php echo esc_html(__('Status', 'majestic-support'));
                                        echo esc_html(" : ");
                                        ?>
                                    </span>
                                    <a title="<?php echo esc_attr(__('Status','majestic-support')); ?>" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_product&task=changestatus&action=mstask&productid='. $product->id,'change-status-'.$product->id));?>">
                                        <img alt="<?php echo esc_attr(__('Status','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/<?php echo esc_attr($status); ?>" />
                                    </a>
                                </td>
                                <td>
                                    <a title="<?php echo esc_html(__('Edit','majestic-support')); ?>" class="action-btn" href="?page=majesticsupport_product&mjslay=addproduct&majesticsupportid=<?php echo esc_attr($product->id); ?>"><img alt="<?php echo esc_html(__('Edit','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/edit.png" /></a>&nbsp;&nbsp;
                                    <a title="<?php echo esc_html(__('Delete','majestic-support')); ?>" class="action-btn" onclick="return confirm('<?php echo esc_html(__('Are you sure you want to delete it?', 'majestic-support')); ?>');" href="<?php echo esc_url(wp_nonce_url('?page=majesticsupport_product&task=deleteproduct&action=mstask&productid='.esc_attr($product->id),'delete-product-'.$product->id));?>"><img alt="<?php echo esc_html(__('Delete','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/delete.png" /></a>
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
