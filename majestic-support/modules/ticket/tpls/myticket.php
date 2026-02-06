<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="ms-main-up-wrapper">
<?php
wp_enqueue_style('status-graph', MJTC_PLUGIN_URL . 'includes/css/status_graph.css');
if (majesticsupport::$_config['offline'] == 2) {
    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('majesticsupport-jquery-ui-css', MJTC_PLUGIN_URL . 'includes/css/jquery-ui-smoothness.css');

        $majesticsupport_js ="
        ajaxurl = '". esc_url(admin_url('admin-ajax.php')) ."';
        jQuery(document).ready(function($) {
            $('.custom_date').datepicker({
                dateFormat: 'yy-mm-dd'
            });";
            if(isset(majesticsupport::$_data['filter']['combinesearch'])){
                $combinesearch = majesticsupport::$_data['filter']['combinesearch'];
            } else {
                $combinesearch = '';
            }
            $majesticsupport_js .= "
            var combinesearch = '". $combinesearch ."';
            if (combinesearch == true) {
                doVisible();
                $('#mjtc-filter-wrapper-toggle-area').show();
            }
            jQuery('#mjtc-search-filter-toggle-btn').click(function(event) {
                event.preventDefault();
                if (jQuery('#mjtc-filter-wrapper-toggle-area').is(':visible')) {
                    jQuery('#mjtc-search-filter-toggle-btn').text(
                        \"". esc_html(__("Show All",'majestic-support'))."\");
                } else {
                    jQuery('#mjtc-search-filter-toggle-btn').text(
                        \"". esc_html(__("Show Less",'majestic-support'))."\");
                }
                jQuery('#mjtc-filter-wrapper-toggle-search').toggle();
                jQuery('#mjtc-filter-wrapper-toggle-area').toggle();
            });

            jQuery('select.mjtc-support-sorting-select').on('change', function(e) {
                e.preventDefault();
                var sortby = jQuery('.mjtc-support-sorting-select option:selected').val();
                jQuery('input#sortby').val(sortby);
                jQuery('form#majesticsupportform').submit();
            });
            jQuery('a.mjtc-admin-sort-btn').on('click', function(e) {
                e.preventDefault();
                var sortby = jQuery('.mjtc-support-sorting-select option:selected').val();
                jQuery('input#sortby').val(sortby);
                jQuery('form#majesticsupportform').submit();
            });
            jQuery('a.mjtc-myticket-link').click(function(e) {
                e.preventDefault();
                var list = jQuery(this).attr('data-tab-number');
                jQuery('input#list').val(list);
                jQuery('form#majesticsupportform').submit();
            });
            jQuery('span.mjtc-support-closedby-wrp').hover(
                function(e){
                    jQuery(this).find('span.mjtc-support-closed-date').css('display','inline-block');
                },
                function(e){
                    jQuery(this).find('span.mjtc-support-closed-date').css('display','none');
                }
            );

            function doVisible() {
                $('#mjtc-filter-wrapper-toggle-search').hide();
                $('.mjtc-filter-wrapper-toggle-ticketid').show();
                $('#mjtc-filter-wrapper-toggle-area').show();
                $('#mjtc-filter-wrapper-toggle-minus').show();
                $('#mjtc-filter-wrapper-toggle-plus').hide();
            }
        });

        function resetForm() {
            var form = jQuery('form#majesticsupportform');
            form.find('input[type=text], input[type=email], input[type=password], textarea').val('');
            form.find('input:checkbox').removeAttr('checked');
            form.find('select').prop('selectedIndex', 0);
            form.find('input[type=\'radio\']').prop('checked', false);
            return true;
        }";

        wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
    }
        MJTC_message::MJTC_getMessage();
        include_once(MJTC_PLUGIN_PATH . 'includes/header.php');
    if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) {
        $list = isset(majesticsupport::$_search['ticket']) ? majesticsupport::$_search['ticket']['list'] : 1;
        $open = ($list == 1) ? 'active' : '';
        $answered = ($list == 2) ? 'active' : '';
        $overdue = ($list == 3) ? 'active' : '';
        $myticket = ($list == 4) ? 'active' : '';
        $field_array = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1);
        $search_field_array = MJTC_includer::MJTC_getModel('fieldordering')->getUserSystemFieldsForSearch();
        $open_percentage = 0;
        $MJTC_close_percentage = 0;
        $answered_percentage = 0;
        $allticket_percentage = 0;
        if(isset(majesticsupport::$_data['count']) && isset(majesticsupport::$_data['count']['allticket']) && majesticsupport::$_data['count']['allticket'] != 0){
            $open_percentage = round((majesticsupport::$_data['count']['openticket'] / majesticsupport::$_data['count']['allticket']) * 100);
            $MJTC_close_percentage = round((majesticsupport::$_data['count']['closedticket'] / majesticsupport::$_data['count']['allticket']) * 100);
            $answered_percentage = round((majesticsupport::$_data['count']['answeredticket'] / majesticsupport::$_data['count']['allticket']) * 100);
        }
        if(isset(majesticsupport::$_data['count']) && isset(majesticsupport::$_data['count']['allticket']) && majesticsupport::$_data['count']['allticket'] != 0){
            $allticket_percentage = 100;
        }
    }
        ?>
        <div class="mjtc-support-top-sec-header">
            <img class="mjtc-transparent-header-img1" alt="<?php echo esc_html(__('image', 'majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL); ?>includes/images/tp-image.png" />
            <div class="mjtc-support-top-sec-left-header">
                <div class="mjtc-support-main-heading">
                    <?php echo esc_html(__("My Tickets",'majestic-support')); ?>
                </div>
                <?php MJTC_includer::MJTC_getModel('majesticsupport')->getPageBreadcrumps('mytickets'); ?>
            </div>
            <div class="mjtc-support-top-sec-right-header">
                <?php
                $id = "";
                if(in_array('multiform',majesticsupport::$_active_addons) && majesticsupport::$_config['show_multiform_popup'] == 1){
                    //show popup in case of multiform
                    $id = "id=multiformpopup";
                }?>
                <a <?php echo esc_attr($id); ?> href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket'))); ?>" class="mjtc-support-button-header">
                    <?php echo esc_html(__("Submit Ticket",'majestic-support')); ?>
                </a>
            </div>
        </div>
        <div class="mjtc-support-cont-main-wrapper mjtc-support-cont-main-wrapper-with-btn">    
        <?php
        if (MJTC_includer::MJTC_getObjectClass('user')->MJTC_uid() != 0) { ?>
            <div class="mjtc-support-cont-wrapper1 mjtc-support-ticket-wrapper">
                <!-- Top Circle Count Boxes -->
                <div class="mjtc-row mjtc-support-top-cirlce-count-wrp">
                    <div class="mjtc-col-xs-12 mjtc-col-md-2 mjtc-myticket-link mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-green mjtc-myticket-link <?php echo esc_attr($open); ?>" href="#" data-tab-number="1">
                            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($open_percentage); ?>">
                                <div class="mjtc-mr-rp" data-progress="<?php echo esc_attr($open_percentage); ?>">
                                    <div class="circle">
                                        <div class="mask full">
                                            <div class="fill mjtc-support-open"></div>
                                        </div>
                                        <div class="mask half">
                                            <div class="fill mjtc-support-open"></div>
                                            <div class="fill fix"></div>
                                        </div>
                                        <div class="shadow"></div>
                                    </div>
                                    <div class="inset">
                                    </div>
                                </div>
                            </div>
                            <span class="mjtc-support-circle-count-text mjtc-support-green">
                                <?php
                                    echo esc_html(__('Open', 'majestic-support'));
                                    if(majesticsupport::$_config['count_on_myticket'] == 1) {
                                    $MJTC_data = ' ( ' . esc_html(majesticsupport::$_data['count']['openticket']) . ' )';
                                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                }
                            ?>
                            </span>
                        </a>
                    </div>
                    <div class="mjtc-col-xs-12 mjtc-col-md-2 mjtc-myticket-link mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-red mjtc-myticket-link <?php echo esc_attr($answered); ?>" href="#" data-tab-number="2">
                            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($MJTC_close_percentage); ?>" >
                                <div class="mjtc-mr-rp" data-progress="<?php echo esc_attr($MJTC_close_percentage); ?>">
                                    <div class="circle">
                                        <div class="mask full">
                                            <div class="fill mjtc-support-close"></div>
                                        </div>
                                        <div class="mask half">
                                            <div class="fill mjtc-support-close"></div>
                                            <div class="fill fix"></div>
                                        </div>
                                        <div class="shadow"></div>
                                    </div>
                                    <div class="inset">
                                    </div>
                                </div>
                            </div>
                            <span class="mjtc-support-circle-count-text mjtc-support-red">
                                <?php
                                    echo esc_html(__('Closed', 'majestic-support'));
                                    if(majesticsupport::$_config['count_on_myticket'] == 1){
                                    $MJTC_data = ' ( ' . esc_html(majesticsupport::$_data['count']['closedticket']) . ' )';
                                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                }
                            ?>
                            </span>
                        </a>
                    </div>
                    <div class="mjtc-col-xs-12 mjtc-col-md-2 mjtc-myticket-link mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-brown mjtc-myticket-link <?php echo esc_attr($overdue); ?>" href="#" data-tab-number="3">
                            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($answered_percentage); ?>" >
                                <div class="mjtc-mr-rp" data-progress="<?php echo esc_attr($answered_percentage); ?>">
                                    <div class="circle">
                                        <div class="mask full">
                                            <div class="fill mjtc-support-answer"></div>
                                        </div>
                                        <div class="mask half">
                                            <div class="fill mjtc-support-answer"></div>
                                            <div class="fill fix"></div>
                                        </div>
                                        <div class="shadow"></div>
                                    </div>
                                    <div class="inset">
                                    </div>
                                </div>
                            </div>
                            <span class="mjtc-support-circle-count-text mjtc-support-brown">
                                <?php
                                echo esc_html(__('Answered', 'majestic-support'));
                                if(majesticsupport::$_config['count_on_myticket'] == 1){
                                    $MJTC_data = ' ( ' . esc_html(majesticsupport::$_data['count']['answeredticket']) . ' )';
                                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                }
                            ?>
                            </span>
                        </a>
                    </div>
                    <div class="mjtc-col-xs-12 mjtc-col-md-2 mjtc-myticket-link mjtc-support-myticket-link-myticket">
                        <a class="mjtc-support-blue mjtc-myticket-link <?php echo esc_attr($myticket); ?>" href="#"
                            data-tab-number="4">
                            <div class="mjtc-support-cricle-wrp" data-per="<?php echo esc_attr($allticket_percentage); ?>">
                                <div class="mjtc-mr-rp" data-progress="<?php echo esc_attr($allticket_percentage); ?>">
                                    <div class="circle">
                                        <div class="mask full">
                                            <div class="fill mjtc-support-allticket"></div>
                                        </div>
                                        <div class="mask half">
                                            <div class="fill mjtc-support-allticket"></div>
                                            <div class="fill fix"></div>
                                        </div>
                                        <div class="shadow"></div>
                                    </div>
                                    <div class="inset">
                                    </div>
                                </div>
                            </div>
                            <span class="mjtc-support-circle-count-text mjtc-support-blue">
                                <?php
                                echo esc_html(__('All Tickets', 'majestic-support'));
                                if(majesticsupport::$_config['count_on_myticket'] == 1){
                                    $MJTC_data = ' ( ' . esc_html(majesticsupport::$_data['count']['allticket']) . ' )';
                                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                                }
                            ?>
                            </span>
                        </a>
                    </div>
                </div>
                <!-- Search Form -->
        <div class="mjtc-support-search-wrp">
            <div class="mjtc-support-form-wrp">
                <form class="mjtc-filter-form" name="majesticsupportform" id="majesticsupportform" method="POST" action="<?php echo esc_url(wp_nonce_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket')),"my-ticket")); ?>">
                    <div class="mjtc-filter-wrapper">
                        <div class="mjtc-filter-form-fields-wrp mjtc-col-md-7" id="mjtc-filter-wrapper-toggle-search">
                            <?php
                            $emailaddress = '';
                                if(!empty($search_field_array['email'])) {
                                    $emailaddress = ' ' . esc_html(__('Or', 'majestic-support')) . ' ' . esc_html(majesticsupport::MJTC_getVarValue($search_field_array['email']));
                                }
                                $MJTC_subject = '';
                                if(!empty($search_field_array['subject'])) {
                                    $MJTC_subject = ' ' . esc_html(__('Or', 'majestic-support')) . ' ' . esc_html(majesticsupport::MJTC_getVarValue($search_field_array['subject']));
                                }
                                echo wp_kses(MJTC_formfield::MJTC_text('ms-ticketsearchkeys', isset(majesticsupport::$_data['filter']['ticketsearchkeys']) ? majesticsupport::$_data['filter']['ticketsearchkeys'] : '', array('class' => 'mjtc-support-input-field','placeholder' => esc_html(__('Ticket ID', 'majestic-support')) . $emailaddress . $MJTC_subject)), MJTC_ALLOWED_TAGS); ?>
                            </div>
                            <div id="mjtc-filter-wrapper-toggle-area" class="mjtc-filter-wrapper-toggle-ticketid">
                                <div class="mjtc-col-md-3 mjtc-filter-form-fields-wrp mjtc-filter-wrapper-toggle-ticketid">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-ticket', isset(majesticsupport::$_data['filter']['ticketid']) ? majesticsupport::$_data['filter']['ticketid'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => esc_html(__('Ticket ID', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <?php 
                                if (!empty($search_field_array['subject'])) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                         <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-subject', isset(majesticsupport::$_data['filter']['subject']) ? majesticsupport::$_data['filter']['subject'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($search_field_array['subject']))), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php
                                } ?>
                                <?php 
                                if (!empty($search_field_array['fullname'])) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-from', isset(majesticsupport::$_data['filter']['from']) ? majesticsupport::$_data['filter']['from'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => esc_html(__('From', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php 
                                }
                                if (!empty($search_field_array['phone'])) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-phone', isset(majesticsupport::$_data['filter']['phone']) ? majesticsupport::$_data['filter']['phone'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => esc_html(majesticsupport::MJTC_getVarValue($search_field_array['phone'])))), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php 
                                }
                                if (!empty($search_field_array['product'])) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-productid', MJTC_includer::MJTC_getModel('product')->getProductForCombobox(), isset(majesticsupport::$_data['filter']['productid']) ? majesticsupport::$_data['filter']['productid'] : '', esc_html(__('Select', 'majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($search_field_array['product']))), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php
                                }
                                if (!empty($search_field_array['department'])) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-departmentid', MJTC_includer::MJTC_getModel('department')->getDepartmentForCombobox(), isset(majesticsupport::$_data['filter']['departmentid']) ? majesticsupport::$_data['filter']['departmentid'] : '', esc_html(__('Select', 'majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($search_field_array['department']))), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php
                                }
                                if(!empty($search_field_array['helptopic']) && in_array('helptopic', majesticsupport::$_active_addons)) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-helptopicid', MJTC_includer::MJTC_getModel('helptopic')->getHelpTopicsForCombobox(), isset(majesticsupport::$_data['filter']['helptopicid']) ? majesticsupport::$_data['filter']['helptopicid'] : '', esc_html(__('Select', 'majestic-support')).' '.majesticsupport::MJTC_getVarValue($search_field_array['helptopic'])), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php
                                }
                                if(!empty($search_field_array['email'])) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-email', isset(majesticsupport::$_data['filter']['email']) ? majesticsupport::$_data['filter']['email'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($search_field_array['email']))), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php
                                }
                                if(!empty($search_field_array['priority'])) { ?>
                                    <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                        <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-priorityid', MJTC_includer::MJTC_getModel('priority')->getPriorityForCombobox(), isset(majesticsupport::$_data['filter']['priorityid']) ? majesticsupport::$_data['filter']['priorityid'] : '', esc_html(__('Select', 'majestic-support')).' '.esc_attr(majesticsupport::MJTC_getVarValue($search_field_array['priority']))), MJTC_ALLOWED_TAGS); ?>
                                    </div>
                                    <?php
                                } ?>
                                <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-datestart', isset(majesticsupport::$_data['filter']['datestart']) ? majesticsupport::$_data['filter']['datestart'] : '', array('class' => 'custom_date mjtc-support-input-field', 'placeholder' => esc_html(__('Start Date', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-dateend', isset(majesticsupport::$_data['filter']['dateend']) ? majesticsupport::$_data['filter']['dateend'] : '', array('class' => 'custom_date mjtc-support-input-field', 'placeholder' => esc_html(__('End Date', 'majestic-support')))), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <?php if(class_exists('WooCommerce') && in_array('woocommerce', majesticsupport::$_active_addons) && !empty($search_field_array['wcorderid'])){  ?>
                                <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-orderid', isset(majesticsupport::$_data['filter']['orderid']) ? majesticsupport::$_data['filter']['orderid'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($search_field_array['wcorderid']))), MJTC_ALLOWED_TAGS); ?>
                                </div>

                                <?php
                                }
                                if(!empty($field_array['eddorderid']) && in_array('easydigitaldownloads', majesticsupport::$_active_addons) && class_exists('Easy_Digital_Downloads') && !empty($search_field_array['eddorderid'])){  ?>
                                <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_text('ms-eddorderid', isset(majesticsupport::$_data['filter']['eddorderid']) ? majesticsupport::$_data['filter']['eddorderid'] : '', array('class' => 'mjtc-support-input-field', 'placeholder' => majesticsupport::MJTC_getVarValue($search_field_array['eddorderid']))), MJTC_ALLOWED_TAGS); ?>
                                </div>

                                <?php
                                } ?>
                                <div class="mjtc-col-md-3 mjtc-filter-field-wrp">
                                    <?php echo wp_kses(MJTC_formfield::MJTC_select('ms-status', MJTC_includer::MJTC_getModel('status')->getStatusForFilter(), isset(majesticsupport::$_data['filter']['status']) ? majesticsupport::$_data['filter']['status'] : '', esc_html(__('Select Status', 'majestic-support'))), MJTC_ALLOWED_TAGS); ?>
                                </div>
                                <?php
                                $customfields = MJTC_includer::MJTC_getObjectClass('customfields')->userFieldsForSearch(1);
                                    foreach ($customfields as $field) {
                                        MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_formCustomFieldsForSearch($field, $k);
                                    }
                                ?>
                            </div>
                            <div class="mjtc-col-md-5 mjtc-filter-button-wrp">
                                <a href="#" class="mjtc-search-filter-btn" id="mjtc-search-filter-toggle-btn">
                                    <?php echo esc_html(__('Show All','majestic-support')); ?>
                                </a>
                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-go', esc_html(__('Search', 'majestic-support')), array('class' => 'mjtc-support-filter-button mjtc-support-search-btn')), MJTC_ALLOWED_TAGS); ?>
                                <?php echo wp_kses(MJTC_formfield::MJTC_submitbutton('ms-reset', esc_html(__('Reset', 'majestic-support')), array('class' => 'mjtc-support-filter-button mjtc-support-reset-btn', 'onclick' => 'return resetForm();')), MJTC_ALLOWED_TAGS); ?>
                            </div>
                        </div>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('sortby', isset(majesticsupport::$_data['filter']['sortby']) ? majesticsupport::$_data['filter']['sortby'] :'' ), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('list', $list), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('MS_form_search', 'MS_SEARCH'), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mspageid', get_the_ID()), MJTC_ALLOWED_TAGS); ?>
                        <?php echo wp_kses(MJTC_formfield::MJTC_hidden('mjtcslay', 'myticket'), MJTC_ALLOWED_TAGS); ?>
                    </form>
                </div>
            </div>
            <!-- Sorting Wrapper -->
            <?php
            $link = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket','list'=> majesticsupport::$_data['list']));
            if (majesticsupport::$_sortorder == 'ASC')
                $img = "sorting-1.png";
            else
                $img = "sorting-2.png";
            ?>
            <div class="mjtc-support-sorting mjtc-col-md-12">
                <div class="mjtc-support-sorting-left">
                    <div class="mjtc-support-sorting-heading">
                        <?php 
                        $list = majesticsupport::$_data['list'];
                        if($list == 1){
                            echo esc_html(__('Open','majestic-support')).' ';
                        } elseif ($list == 2){
                            echo esc_html(__('Closed','majestic-support')).' ';
                        } elseif ($list == 3){
                            echo esc_html(__('Answered','majestic-support')).' ';
                        } elseif ($list == 5){
                            echo esc_html(__('Overdue','majestic-support')).' ';
                        } elseif ($list == 4){
                            echo esc_html(__('All','majestic-support')).' ';
                        }?>
                        <?php echo esc_html(__('Tickets','majestic-support')); ?>
                    </div>
                </div>
                <div class="mjtc-support-sorting-right">
                    <div class="mjtc-support-sort">
                        <select class="mjtc-support-sorting-select">
                            <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['subject'])); ?>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['subject']); ?>"
                                <?php if (majesticsupport::$_sorton == 'subject') echo esc_html('selected') ?>>
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['subject'])); ?></option>
                            <?php
                            if (!empty($field_array['priority'])) { ?>
                                <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['priority']); ?>"
                                <?php if (majesticsupport::$_sorton == 'priority') echo esc_html('selected') ?>>
                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['priority'])); ?></option>
                            <?php } ?>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['ticketid']); ?>"
                                <?php if (majesticsupport::$_sorton == 'ticketid') echo esc_html('selected') ?>>
                                <?php echo esc_html(__("Ticket ID",'majestic-support')); ?></option>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['isanswered']); ?>"
                                <?php if (majesticsupport::$_sorton == 'isanswered') echo esc_html('selected') ?>>
                                <?php echo esc_html(__("Answered",'majestic-support')); ?></option>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['status']); ?>"
                                <?php if (majesticsupport::$_sorton == 'status') echo esc_html('selected') ?>>
                                <?php echo esc_html(__("Status",'majestic-support')); ?></option>
                            <option value="<?php echo esc_attr(majesticsupport::$_sortlinks['created']); ?>"
                                <?php if (majesticsupport::$_sorton == 'created') echo esc_html('selected') ?>>
                                <?php echo esc_html(__("Created",'majestic-support')); ?></option>
                        </select>
                        <a href="#" class="mjtc-admin-sort-btn" title="<?php echo esc_attr(__('sort','majestic-support')); ?>">
                            <img alt="<?php echo esc_html(__('sort','majestic-support')); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . 'includes/images/' . esc_attr($img) ?>">
                        </a>
                    </div>
                </div>
            </div>
            <?php
            if (!empty(majesticsupport::$_data[0])) {
                $fields_array = array(); // Array for form fields
                $show_on_listing_arrays = array(); // Array for visible form fields
                foreach (majesticsupport::$_data[0] AS $MJTC_ticket) {
                    // Check if the form fields are already array
                    if (!isset($fields_array[$MJTC_ticket->multiformid])) {
                        $fields_array[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldTitleByFieldfor(1, $MJTC_ticket->multiformid);
                    }
                    if (!isset($show_on_listing_arrays[$MJTC_ticket->multiformid])) {
                        $show_on_listing_arrays[$MJTC_ticket->multiformid] = MJTC_includer::MJTC_getModel('fieldordering')->getFieldsForListing(1, $MJTC_ticket->multiformid);
                    }
                    // Now use the cached field array
                    $field_array = $fields_array[$MJTC_ticket->multiformid];
                    $show_on_listing_array = $show_on_listing_arrays[$MJTC_ticket->multiformid];
                    $MJTC_ticketviamail = '';
                    if ($MJTC_ticket->ticketviaemail == 1)
                        $MJTC_ticketviamail = esc_html(__('Created via Email', 'majestic-support'));
                    ?>
                    <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-wrapper">
                        <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-toparea">
                            <div class="mjtc-col-xs-2 mjtc-col-md-2 mjtc-support-pic">
                                <?php /* if (in_array('agent',majesticsupport::$_active_addons) && $MJTC_ticket->staffphoto) { ?>
                                    <img class="mjtc-support-staff-img" src="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'agent','task'=>'getStaffPhoto','action'=>'mstask','majesticsupportid'=> $MJTC_ticket->staffid ,'mspageid'=>get_the_ID())));?> ">
                                <?php } else { */
                                    echo wp_kses(ms_get_avatar($MJTC_ticket->uid), MJTC_ALLOWED_TAGS);
                                // } ?>
                            </div>
                            <div class="mjtc-col-xs-10 mjtc-col-md-6 mjtc-col-xs-10 mjtc-support-data mjtc-nullpadding">
                                <?php 
                                if (!empty($show_on_listing_array['fullname'])) {?>
                                <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses name">
                                    <span class="mjtc-support-value"><?php echo esc_html($MJTC_ticket->name); ?></span>
                                    <?php if ($MJTC_ticket->status == 5 && majesticsupport::$_config['show_closedby_on_user_tickets'] == 1) { ?>
                                        <span class="mjtc-support-closedby-wrp">
                                            <span class="mjtc-support-closedby">
                                                <?php echo esc_html(MJTC_includer::MJTC_getModel('ticket')->getClosedBy($MJTC_ticket->closedby)); ?>
                                            </span>
                                            <?php 
                                            if ($MJTC_ticket->closed != '0000-00-00 00:00:00') {?>
                                                <span class="mjtc-support-closed-date">
                                                    <?php echo esc_html(__("Closed on", 'majestic-support')). " " . esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->closed))); ?>
                                                </span>
                                                <?php 
                                            } ?>
                                        </span>
                                        <?php
                                    } ?>
                                </div>
                              <?php
                                } ?>
                                <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                    <a class="mjtc-support-title-anchor" href="<?php echo esc_url(majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'ticketdetail','majesticsupportid'=> $MJTC_ticket->id))); ?>"><?php echo esc_html($MJTC_ticket->subject); ?></a>
                                </div>
                                <?php
                                foreach ($show_on_listing_array AS $field_field => $field_title) {
                                    switch ($field_field) {
                                        case 'department': ?>
                                            <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                <span class="mjtc-support-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['department'])); ?>&nbsp;:&nbsp;</span>
                                                <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->departmentname)); ?></span>
                                            </div>
                                            <?php
                                            break;
                                        case 'email': ?>
                                            <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                <span class="mjtc-support-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['email'])); ?>&nbsp;:&nbsp;</span>
                                                <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->email)); ?></span>
                                            </div>
                                            <?php
                                            break;
                                        case 'phone':?>
                                            <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                <span class="mjtc-support-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['phone'])); ?>&nbsp;:&nbsp;</span>
                                                <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->phone)); ?></span>
                                            </div>
                                            <?php
                                            break;
                                        case 'product':  ?>
                                            <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                <span class="mjtc-support-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['product'])); ?>&nbsp;:&nbsp;</span>
                                                <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->producttitle)); ?></span>
                                            </div>
                                            <?php
                                            break;
                                        case 'helptopic': 
                                            if (in_array('helptopic', majesticsupport::$_active_addons)) { ?>
                                                <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                    <span class="mjtc-support-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['helptopic'])); ?>&nbsp;:&nbsp;</span>
                                                    <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->topic)); ?></span>
                                                </div>
                                            <?php
                                            }
                                            break;
                                        case 'eddorderid': ?>
                                            <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                <span class="mjtc-support-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['eddorderid'])); ?>&nbsp;:&nbsp;</span>
                                                <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->eddorderid)); ?></span>
                                            </div>
                                            <?php
                                            break;
                                        case 'eddproductid':
                                            if(!in_array('easydigitaldownloads', majesticsupport::$_active_addons)){
                                                break;
                                            }
                                            if(!class_exists('Easy_Digital_Downloads')){
                                                break;
                                            } ?>
                                            <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                                <span class="mjtc-support-field-title"><?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['eddproductid'])); ?>&nbsp;:&nbsp;</span>
                                                <span class="mjtc-support-value"><?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->eddproductid)); ?></span>
                                            </div>
                                            <?php
                                            break;
                                        default:
                                            break;
                                    }
                                }
                                majesticsupport::$_data['custom']['ticketid'] = $MJTC_ticket->id;
                                $customfields = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_userFieldsData(1, 1);
                                foreach ($customfields as $field) {
                                    if ($field->userfieldtype != 'termsandconditions') {
                                        $ret = MJTC_includer::MJTC_getObjectClass('customfields')->MJTC_showCustomFields($field,1, $MJTC_ticket->params);
                                        ?>
                                        <div class="mjtc-col-xs-12 mjtc-col-md-12 mjtc-support-padding-xs mjtc-support-body-data-elipses">
                                            <span class="mjtc-support-field-title"><?php echo esc_html($ret['title']); ?>&nbsp;:&nbsp;</span>
                                            <span class="mjtc-support-value"><?php echo wp_kses($ret['value'], MJTC_ALLOWED_TAGS); ?></span>
                                        </div>
                                        <?php
                                    }
                                }
                                if ($MJTC_ticket->ticketviaemail == 1){  ?>
                                    <span class="mjtc-support-value mjtc-support-creade-via-email-spn"><?php echo esc_html($MJTC_ticketviamail); ?></span>
                                <?php }
                                if (!empty($show_on_listing_array['priority'])) { ?>
                                    <span class="mjtc-support-wrapper-textcolor mjtc-support-overall-textcolor-wrapper" style="background:<?php echo esc_attr($MJTC_ticket->prioritycolour); ?>;">
                                        <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->priority)); ?>
                                    </span>
                                    <?php
                                } ?>
                                <span class="mjtc-support-status mjtc-support-overall-status-wrapper" style="background-color: <?php echo esc_attr($MJTC_ticket->statusbgcolour); ?>;color:<?php echo esc_attr($MJTC_ticket->statuscolour); ?>;">
                                    <?php
                                    $MJTC_counter = 'one';
                                    if ($MJTC_ticket->lock == 1) { ?>
                                        <img class="ticketstatusimage <?php echo esc_attr($MJTC_counter);
                                        $MJTC_counter = 'two'; ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . "includes/images/lock.png"; ?>" title="<?php echo esc_attr(__('The ticket is locked', 'majestic-support')); ?>" />
                                        <?php
                                    }
                                    if ($MJTC_ticket->isoverdue == 1) { ?>
                                        <img class="ticketstatusimage <?php echo esc_attr($MJTC_counter); ?>" src="<?php echo esc_url(MJTC_PLUGIN_URL) . "includes/images/over-due.png"; ?>" title="<?php echo esc_attr(__('This ticket is marked as overdue', 'majestic-support')); ?>" />
                                    <?php
                                    }
                                    echo esc_html($MJTC_ticket->statustitle); ?>
                                </span>
                            </div>
                            <div class="mjtc-col-xs-12 mjtc-col-md-4 mjtc-support-data1 mjtc-support-padding-left-xs">
                                <div class="mjtc-support-data-row">
                                    <div class="mjtc-support-data-tit"><?php echo esc_html(__('Ticket ID', 'majestic-support')). ': '; ?>
                                    </div>
                                    <div class="mjtc-support-data-val"><?php echo esc_html($MJTC_ticket->ticketid); ?></div>
                                </div>
                                <?php if (empty($MJTC_ticket->lastreply) || $MJTC_ticket->lastreply == '0000-00-00 00:00:00') { ?>
                                <div class="mjtc-support-data-row">
                                    <div class="mjtc-support-data-tit"><?php echo esc_html(__('Created', 'majestic-support')). ': '; ?></div>
                                    <div class="mjtc-support-data-val">
                                        <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->created))); ?>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <div class="mjtc-support-data-row">
                                    <div class="mjtc-support-data-tit"><?php echo esc_html(__('Last Reply', 'majestic-support')). ': '; ?>
                                    </div>
                                    <div class="mjtc-support-data-val">
                                        <?php echo esc_html(date_i18n(majesticsupport::$_config['date_format'], MJTC_majesticsupportphplib::MJTC_strtotime($MJTC_ticket->lastreply))); ?>
                                    </div>
                                </div>
                                <?php }
                                if (in_array('agent',majesticsupport::$_active_addons) && majesticsupport::$_config['show_assignto_on_user_tickets'] == 1) { ?>
                                        <div class="mjtc-support-data-row">
                                            <div class="mjtc-support-data-tit">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($field_array['assignto'])). ': '; ?></div>
                                            <div class="mjtc-support-data-val">
                                                <?php echo esc_html(majesticsupport::MJTC_getVarValue($MJTC_ticket->staffname)); ?>
                                            </div>
                                        </div>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                if (majesticsupport::$_data[1]) {
                    $MJTC_data = '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post(majesticsupport::$_data[1]) . '</div></div>';
                    echo wp_kses($MJTC_data, MJTC_ALLOWED_TAGS);
                }
            } else { // Record Not FOund
                MJTC_layout::MJTC_getNoRecordFound();
            }
        } else {// User is guest
            ?>
            <div class="mjtc-support-cont-wrapper mjtc-support-cont-wrapper-color">
            <?php
                $MJTC_redirect_url = majesticsupport::makeUrl(array('mjsmod'=>'ticket','mjslay'=>'myticket'));
                $MJTC_redirect_url = MJTC_majesticsupportphplib::MJTC_safe_encoding($MJTC_redirect_url);
                MJTC_layout::MJTC_getUserGuest($MJTC_redirect_url); ?>
            </div>
            <?php
        }
    } else { // System is offline
        MJTC_layout::MJTC_getSystemOffline();
    }?>
    </div>
</div>
</div>
