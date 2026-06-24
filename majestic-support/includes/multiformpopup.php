<?php

if (!defined('ABSPATH'))
    die('Restricted Access');
if(!in_array('multiform', majesticsupport::$_active_addons)){
    return false;
} ?>
<div id="multiformpopupblack" style="display:none;"></div>
<div id="multiformpopup" class="" style="display:none;">
    <!-- Select User Popup -->
    <div class="ms-multiformpopup-header">
        <div class="multiformpopup-header-text">
            <?php echo esc_html(__('Select Form','majestic-support')); ?>
        </div>
        <div class="multiformpopup-header-close-img">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 18 18"></path></svg>
        </div>
    </div>
    <div id="records">
        <div id="records-inner">
            <div class="mjtc-staff-searc-desc">
                <?php echo esc_html(__('No Record Found','majestic-support')); ?>
            </div>
        </div>
    </div>
</div>
    <!-- add loading for multiform -->
<div id="mstran_loading">
    <div class="ms-css-spinner"></div>
</div>
<?php
$majesticsupport_js ="
    jQuery(document).ready(function ($) {
        jQuery('a#multiformpopup').click(function (e) {
            e.preventDefault();
            var url = jQuery('a#multiformpopup').prop('href');
            jQuery('div#multiformpopupblack').show();
            var ajaxurl = '". esc_url(admin_url('admin-ajax.php'))."';
            jsShowLoading();
            jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'multiform', task: 'getmultiformlistajax', url: url, '_wpnonce':'". esc_attr(wp_create_nonce("get-multi-form-list-ajax"))."'}, function(data) {
                if (data) {
                    jsHideLoading();
                    jQuery('div#records').html('');
                    jQuery('div#records').html(data);
                    jQuery('div#multiformpopup').slideDown('slow');
                }
            });
        });

        jQuery('div#multiformpopupblack , div.multiformpopup-header-close-img').click(function(e) {
            jQuery('div#multiformpopup').slideUp('slow', function() {
                jQuery('div#multiformpopupblack').hide();
            });
        });
    });

    function MJTC_makeFormSelected(divelement) {
        jQuery('div.mjtc-support-multiform-row').removeClass('selected');
        jQuery(divelement).addClass('selected');
    }

    function MJTC_makeMultiFormUrl(id) {
        var oldUrl = jQuery('a.mjtc-multiformpopup-link').attr('id'); // Get current url
        var opt = '?';
        var found = oldUrl.search('&');
        if (found > 0) {
            opt = '&';
        }
        var found = oldUrl.search('[\?\]');
        if (found > 0) {
            opt = '&';
        }
        var newUrl = oldUrl + opt + 'formid=' + id; // Create new url
        window.location.href = newUrl;
    }

    function jsShowLoading(){
        jQuery('div#mstran_loading').css('display', 'flex');
    }

    function jsHideLoading(){
        jQuery('div#mstran_loading').hide();
    }

";
wp_add_inline_script('majestic-support-cmain-js',$majesticsupport_js);
?>