<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// if header is calling later
MJTC_includer::MJTC_getModel('majesticsupport')->checkIfMainCssFileIsEnqued();
$MJTC_color1 = majesticsupport::$_colors['color1'];
$MJTC_color2 = majesticsupport::$_colors['color2'];
$MJTC_color3 = majesticsupport::$_colors['color3'];
$MJTC_color4 = majesticsupport::$_colors['color4'];
$MJTC_color5 = majesticsupport::$_colors['color5'];
$MJTC_color6 = majesticsupport::$_colors['color6'];
$MJTC_color7 = majesticsupport::$_colors['color7'];
$MJTC_color8 = majesticsupport::$_colors['color8'];
$MJTC_color9 = majesticsupport::$_colors['color9'];


$majesticsupport_css = '';

/*Code for Css*/
$majesticsupport_css .= '
/* Tickets Details*/
	div.mjtc-support-ticket-detail-wrapper{float: left;width: 100%;padding: 25px;}
	div.mjtc-support-detail-wrapper{float: left;width: 100%; padding: 0px}
	div.mjtc-support-detail-box{float: left;width: 100%;}
	div.mjtc-support-detail-box div.mjtc-support-detail-left{float: left;width: 20%;padding: 20px 5px;}
	div.mjtc-support-detail-left div.mjtc-support-user-img-wrp{display:inline-block;width:100px;text-align: center;margin: 0px 20px;height: 100px;position: relative;border-radius: 50%;}
	div.mjtc-support-detail-left div.mjtc-support-user-img-wrp img{width: 100px;max-width: 100%;max-height: 100%;height: 100px;position: absolute;top: 0;left: 0;right: 0;bottom: 0;margin: 0 auto;border-radius: 50%;}
	div.mjtc-support-detail-left div.mjtc-support-user-name-wrp{display:inline-block;width:100%;text-align: center;margin: 5px 0px;}
	div.mjtc-support-detail-left div.mjtc-support-user-email-wrp{display:inline-block;width:100%;text-align: center;margin: 5px 0px;}
	div.mjtc-support-detail-box div.mjtc-support-detail-right{float: left;width: calc(100% - 21%);}
	div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrapper{float: left;}
	div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp{float: left;width: 100%;position: relative;padding:20px 20px 0px 20px;}
	div.mjtc-support-detail-right div.mjtc-support-row{float: left;width: 100%;padding: 0px 0 8px 0px;}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-title{display: inline-block;width:auto;margin: 0px 5px 0px 0px;}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value{display: inline-block;width:auto;}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value .mjtc-support-field-value-t {display: inline-block;}
	div.mjtc-support-status-note{display: inline-block;}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value.mjtc-support-priorty{padding: 3px;min-width: 120px;text-align: center;}
	div.mjtc-support-detail-right div.mjtc-support-openclosed-box{display: inline-block;position: absolute;padding: 20px 5px; text-align: center;right: 10px;font-weight:bold;min-width: 80px;}
	div.mjtc-support-detail-right div.mjtc-support-right-bottom{display: inline-block;float: left;width: 100%;padding:10px 0px 0px 20px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-action-btn-wrp{display: inline-block;float: left;width: 100%; padding:8px 5px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-action-btn-wrp div.mjtc-support-btn-box{display: inline-block;float: left;min-width:89px;text-align: center;margin-right: 5px;margin-left: 5px; margin-bottom: 5px;margin-top: 5px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-action-btn-wrp div.mjtc-support-btn-box a.mjtc-button{display: inline-block;width: 100%;padding: 5px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-more-actions-btn-wrp{display: inline-block;width: 100%;float: left;z-index: 9;text-align: center;}
	div#action-div div.mjtc-row{display: inline-block; border-top: 1px solid #ddeeee;width:60%;margin:0px 20%;padding-top: 10px;margin-top: 10px;}
	div#userpopupforchangepriority{position: fixed;top:50%;left:50%;width:40%;max-height:55%;z-index: 99999;overflow-y: auto; overflow-x: hidden;text-align: left;transform: translate(-50%,-50%);}
	div#userpopupforchangepriority div.mjtc-support-priorty-header{float: left;width: 100%;padding: 20px 15px;font-weight: bold;font-size: 18px;position: relative;}
	div#userpopupforchangepriority div.mjtc-support-priorty-header span.close-history{position: absolute;top: 22px;right: 16px;background:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/close-icon-white.png) no-repeat;background-size: 100%;width:25px;height: 25px;cursor: pointer;font-weight: bold;font-size: 18px;}
	div#userpopupforchangepriority div.mjtc-support-priorty-fields-wrp{float: left;width: 100%;}
	div#userpopupforchangepriority div.mjtc-support-priorty-fields-wrp div.mjtc-support-select-priorty{float: left;width: 100%;text-align: center;padding: 35px 20px;}
	div#userpopupforchangepriority div.mjtc-support-priorty-fields-wrp div.mjtc-support-select-priorty select#priority{width: 80%;border-radius: 0;float: none;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / auto no-repeat #eee;padding: 10px;}
	div#userpopupforchangepriority div.mjtc-support-priorty-fields-wrp div.mjtc-support-select-priorty select#prioritytemp{width: 80%;border-radius: 0;float: none;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / auto no-repeat #eee;padding: 0 10px;}
	div#popupforagenttransfer{position: fixed;top:50%;left:50%;width:40%;max-height:70%;z-index: 99999;overflow-y: auto; overflow-x: hidden;text-align: left;transform: translate(-50%,-50%);}
	div#popupforagenttransfer form {float: left;width: 100%;padding: 30px;}
	div#popupfordepartmenttransfer{position: fixed;top:50%;left:50%;width:40%;max-height:70%;z-index: 99999;overflow-y: auto; overflow-x: hidden;text-align: left;transform: translate(-50%,-50%);}
	div#popupfordepartmenttransfer form {float: left;width: 100%;padding: 30px;}
	div#userpopupforchangestatus{position: fixed;top:50%;left:50%;width:40%;max-height:70%;z-index: 99999;overflow-y: auto; overflow-x: hidden;text-align: left;transform: translate(-50%,-50%);}
	div#userpopupforchangestatus form {float: left;width: 100%;padding: 30px;}
	div#ticketclosereason {position: fixed;top:50%;left:50%;width:40%;max-height:70%;z-index: 99999;overflow-y: auto; overflow-x: hidden;text-align: left;transform: translate(-50%,-50%);}
	div#ticketclosereason div.ticket-close-reason-header {float: left;width: 100%;padding: 18px 30px;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-header {float: left;width: 100%;padding: 10px 0;font-size: 16px;color: '.$MJTC_color2.';font-weight: 500;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body {float: left;width: 100%;padding: 5px 0;font-size: 15px;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body .ms-formfield-radio-button-wrap {float: left;width: 100%;padding: 0;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body .ms-formfield-radio-button-wrap input[type=radio] {width:17px;height:17px;vertical-align:middle;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body input[type=checkbox]{width: 1.8rem;height: 1.8rem;vertical-align:middle;float: left;margin: 13px 10px 10px 0 !important;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body label{float: left;width: calc(100% - 35px);padding: 10px 0 12px;font-weight:normal;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body .ms-formfield-radio-button-wrap label {display: inline-block;font-weight:normal;float:unset;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body .ms-popup-other-reason-box-wrp {float:left;display: none;width:100%;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-body .ms-popup-other-reason-box {float: left;width: 80%;border: 1px solid '.$MJTC_color5.';padding: 10px;min-height: 200px;}
	div#ticketclosereason div.ticket-close-reason-header div.close-reason-footer {float: left;width: 100%;padding: 10px 0;font-size: 14px;color: '.$MJTC_color2.';}
	div#ticketclosereason div.ticket-close-reason-footer {float: left;width: 100%;margin-top: 20px;padding: 18px 30px;border-top: 1px solid '.$MJTC_color5.';}
	div#ticketclosereason div.ticket-close-reason-footer .ticket-close-reason-cancel-btn {background: '.$MJTC_color1.';color: '.$MJTC_color7.';border: 1px solid #'.$MJTC_color5.';}
	div#ticketclosereason div.ticket-close-reason-footer .ticket-close-reason-submit-btn {background: '.$MJTC_color3.';color: '.$MJTC_color4.';border: 1px solid '.$MJTC_color5.';}
	div#ticketclosereason div.ticket-close-reason-footer .ticket-close-reason-btn {float: right;padding: 13px;text-align: center;font-size: 16px !important;min-width: 120px;margin: 0 5px;cursor: pointer;}
	div#popupforinternalnote{position: fixed;top:50%;left:50%;width:40%;max-height:70%;z-index: 99999;overflow-y: auto; overflow-x: hidden;text-align: left;transform: translate(-50%,-50%);}
	div#popupforinternalnote form {float: left;width: 100%;padding: 30px;}
	div.mjtc-support-priorty-btn-wrp{width: calc(100% - 20px);float: left;text-align: center; padding: 15px 0px;margin: 0px 10px;}
	div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-save{min-width:140px; padding: 15px 5px;line-height: initial;border-radius: 0;}
	div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-cancel{min-width:140px; padding: 15px 5px;line-height: initial;border-radius: 0;font-weight:bold;}
	div.mjtc-support-post-reply-wrapper {float: left;width: 100%;margin-top: 20px;}
	div.mjtc-support-post-reply-wrapper div.mjtc-support-thread-heading{display: inline-block;width: 100%;padding: 13px 15px;font-size: 18px;margin-bottom: 20px;}
	div.mjtc-support-post-reply-box{margin-bottom: 20px;}
	div.mjtc-support-attachments-wrp{display: inline-block;width:100%;padding: 15px 0px 10px 0px;}
	div.mjtc-support-attachments-wrp div.mjtc_supportattachment{display: inline-block;width:100%;padding: 5px 5px;margin: 0px 0px 10px;float:left;line-height: initial;}
	div.mjtc-support-attachments-wrp div.mjtc_supportattachment span.mjtc_supportattachment_fname {display: inline-block;padding: 7px 0;width: 60%;height: 25px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
	div.mjtc-support-attachments-wrp div.mjtc_supportattachment span.mjtc-support-download-file-title{padding: 8px 0px;display: inline-block;float: left;width: 55%;overflow: hidden;height: 36px;text-overflow: ellipsis;white-space: nowrap;}
	div.mjtc-support-attachments-wrp div.mjtc_supportattachment a.mjtc-download-button{display: inline-block;width: auto;padding: 3px 3px;text-align: center;float: right;text-decoration: none !important;margin: 2px 2px;height:31px;}
	div.mjtc-support-attachments-wrp div.mjtc_supportattachment a.mjtc-download-button img.mjtc-support-download-img{vertical-align:unset;}
	div.mjtc-support-attachments-wrp div.mjtc_supportattachment a.mjtc-download-button img {height: 23px;}
	div.mjtc-support-attachments-wrp a.mjtc-all-download-button{display: inline-block;padding: 9px 5px;text-align: center;min-width: 145px;text-decoration: none;line-height: initial;}
	div.mjtc-support-attachments-wrp a.mjtc-all-download-button img.mjtc-support-all-download-img{vertical-align: baseline;margin-right: 5px;}
	div.mjtc-support-edit-options-wrp{float: left;width: calc(100% - 40px);padding: 15px 0px;margin: 5px 20px 0px;}
	div.mjtc-support-edit-options-wrp a.mjtc-button{display: inline-block;width:auto;padding: 5px;margin-right: 5px;}
	div.mjtc-support-edit-options-wrp a.mjtc-button img{display: inline-block;max-width:100%;height:auto;}
	div.mjtc-support-edit-options-wrp .mjtc-support-thread-time {display: inline-block;}
	div.mjtc-support-field-value p {margin: 0px;line-height: 30px;}
	div.mjtc-support-time-stamp-wrp{float: left;width: calc(100% - 40px);margin: 5px 20px 0px;}
	div.mjtc-support-time-stamp-wrp span.mjtc-support-ticket-created-date{display: inline-block;float: left;padding: 10px 0px;}
	/* Post Reply Section */
	div.mjtc-support-reply-forms-wrapper {float:left;width: 100%;}
	div.mjtc-support-reply-forms-wrapper div.mjtc-support-reply-forms-heading{display: inline-block;width: 100%;padding:20px;line-height:initial;font-weight:bold;font-size: 18px;margin-bottom: 20px;}
	div.mjtc-support-reply-forms-wrapper div.mjtc-support-post-reply{display: inline-block;width: 100%;}
	div.mjtc-support-reply-forms-wrapper div.mjtc-support-post-reply div.mjtc-support-reply-field-wrp{display: inline-block;width: 100%;}
	div.mjtc-support-reply-attachments{display: inline-block;width: 100%;margin-bottom: 20px;}
	div.mjtc-support-reply-attachments div.mjtc-attachment-field-title{display: inline-block;width: 100%;padding: 15px 0 5px 0px;}
	div.mjtc-support-reply-attachments div.mjtc-attachment-field{display: inline-block;width: 100%;}
	div.tk_attachment_value_wrapperform{float: left;width:100%;padding:0px 0px;}
	div.tk_attachment_value_wrapperform span.tk_attachment_value_text{float: left;width: calc(100% / 2 - 10px);padding: 5px 5px;margin: 5px 5px;position: relative;}
	div.tk_attachment_value_wrapperform span.tk_attachment_value_text input.mjtc-attachment-inputbox{width: 100%;max-width: 100%;max-height:100%;}
	span.tk_attachment_value_text span.tk_attachment_remove{background: url("'.esc_url(MJTC_PLUGIN_URL).'includes/images/close.png") no-repeat;background-size: 100% 100%;position: absolute;width: 25px;height: 25px;top: 5px;right:6px;cursor: pointer;}
	span.tk_attachments_configform{display: inline-block;float:left;line-height: 25px;margin-top: 10px;width: 100%; font-size: 14px;}
	span.tk_attachments_addform{position: relative;display: inline-block;padding: 8px 10px;cursor: pointer;margin-top: 10px;min-width: 120px;text-align: center;line-height: initial;text-transform: capitalize;}
	div.mjtc-support-closeonreply-wrp{float: left;width: 100%; margin-bottom: 10px;}
	div.mjtc-support-closeonreply-wrp div.mjtc-support-closeonreply-title{float: left;width: 100%;margin-bottom: 10px;}
	div.mjtc-support-closeonreply-wrp div.mjtc-form-title-position-reletive-left{width: 50%;padding: 10px;float: left;}
	div.mjtc-support-closeonreply-wrp div.mjtc-form-title-position-reletive-left #closeonreply1{margin-right:5px;}
	div.mjtc-support-reply-form-button-wrp{float: left;width: 100%;text-align: center;padding: 20px 0px 0px;margin-top: 40px;}
	div.mjtc-support-reply-form-button-wrp input.mjtc-support-save-button{min-width:150px;padding: 15px 20px;border-radius: 0px;line-height: unset;line-height: initial;}
	div.mjtc-support-reply-form-button-wrp a.mjtc-support-cancel-button{min-width:150px;padding: 14px 20px;border-radius: 0px;display: inline-block;line-height: initial;}
	div.replyFormStatus{width: 50%;padding: 10px;}
	div.replyFormStatus{width: 50%;padding: 10px;}
	/* Tabs Section */
	div.mjtc-support-tabs-wrapper{float: left;width: 100%;}
	div.mjtc-support-tabs-wrapper ul.mjtc-support-ul-style{float: left;width: 100%;list-style: none;padding: 10px 0px 0px;}
	div.mjtc-support-tabs-wrapper li.mjtc-support-li-style{display: inline-block;float: left;margin-right:10px;border-bottom: 0;}
	div.mjtc-support-tabs-wrapper li.mjtc-support-li-style a.mjtc-support-tab-link{display: inline-block;padding: 15px 20px;text-decoration: none;outline:0;width: 100%;}
	div.mjtc-support-tabs-wrapper li.mjtc-support-li-style a.mjtc-support-tab-link img.mjtc-support-tab-img{}
	div.mjtc-support-tabs-wrapper div.mjtc-support-inner-tab{float: left;width: 100%;}
	div.mjtc-support-inner-tab div.mjtc-support-post-reply-wrp{float: left;width: 100%;}
	div.mjtc-support-premade-msg-wrp{float: left;width: 100%;margin-top: 20px;}
	div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-title{float: left;width: 100%;margin-bottom: 7px;}
	div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-wrp{float: left;width: 100%;}
	div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-wrp select.mjtc-support-premade-select{display: inline-block;width: 50%;float: left;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 96% / 4% no-repeat #eee;padding: 10px;line-height: initial;height: 50px;}
	span.mjtc-support-apend-radio-btn{display: inline-block;float: left;width: auto;padding: 10px;margin-left: 5px;height: 50px;}
	span.mjtc-support-apend-radio-btn input#append_premade1display{vertical-align: middle;}
	input#append_premade1{vertical-align: baseline;margin-right:5px;}
	label#forappend_premade{display: inline-block; margin: 0px;}
	div.mjtc-support-text-editor-wrp{display: inline-block;float: left;width: 100%;margin-top: 20px;}
	div.mjtc-support-append-signature-wrp{float: left;width: 100%;margin-bottom: 20px;}
	div.mjtc-support-append-signature-wrp.mjtc-support-append-signature-wrp-full-width{width: 100%;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-append-field-title{float: left;width: 100%;margin-bottom: 15px;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-append-field-wrp{float: left;width: 100%;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box{float: left;margin: 0px 5px;padding: 11px;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box.mjtc-support-signature-radio-box-full-width{width: 100%;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box input#ownsignature1{margin-right: 5px; vertical-align: baseline; }
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box label#forownsignature{margin: 0px 0 0 3px;display: inline-block;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box input#departmentsignature1{margin-right: 5px; vertical-align: baseline;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box label#fordepartmentsignature{margin:0px 0 0 3px;display: inline-block;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box label#forcanappendsignature{margin: 0px;display: inline-block;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box input#nonesignature1{margin-right: 5px; vertical-align: baseline;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box label#fornonesignature{margin:0px 0 0 3px; ;display: inline-block;}
	div.mjtc-support-assigntome-wrp{float: left;width: calc(100% / 2);margin-bottom: 15px;}
	div.mjtc-support-assigntome-wrp div.mjtc-support-assigntome-field-title{float: left;width: 100%;margin-bottom: 10px;}
	div.mjtc-support-assigntome-wrp div.mjtc-support-assigntome-field-wrp{float: left;width: 100%;padding: 11px 10px;}
	div.mjtc-support-assigntome-wrp div.mjtc-support-assigntome-field-wrp input#assigntome1{margin-right: 5px; vertical-align: baseline;}
	div.mjtc-support-assigntome-wrp div.mjtc-support-assigntome-field-wrp label#forassigntome{margin: 0px;display: inline-block;text-transform:capitalize;}
	div.mjtc-support-internalnote-wrp{float: left;width: 100%;margin-top: 20px;}
	div.mjtc-support-internalnote-wrp div.mjtc-support-internalnote-field-title{float: left;width: 100%;margin-bottom: 7px;}
	div.mjtc-support-internalnote-wrp div.mjtc-support-internalnote-field-wrp{float: left;width: 100%;}
	div.mjtc-support-internalnote-wrp div.mjtc-support-internalnote-field-wrp input.mjtc-support-internalnote-input{border-radius: 0px;width:100%;}
	div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-wrp select#departmentid{display: inline-block;width: 100%;float: left;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / auto no-repeat !important;}
	div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-wrp select#staffid{display: inline-block;width: 100%;float: left;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / auto no-repeat #eee;}
	/* Pop up Sections */
	div#userpopupblack{background: rgba(0,0,0,0.5);position: fixed;width: 100%;height: 100%;top:0px;left:0px;z-index: 9999;}
	div.mjtc-support-popup-row{float: left;width: 100%;margin: 0;}
	form#userpopupsearch{margin-bottom: 0px;}
	form#userpopupsearch div.search-center div.mjtc-search-value{padding: 0 5px;}
	form#userpopupsearch div.search-center div.mjtc-search-value input{min-height: 28px;}
	form#userpopupsearch div.search-center div.mjtc-search-value-button{padding: 0;}
	form#userpopupsearch div.search-center div.mjtc-search-value-button div.mjtc-button{padding: 0 5px; width: 50%; float: left; display: inline-block;}
	form#userpopupsearch{margin-bottom: 10px;float: left;width: 100%;}
	form#userpopupsearch div.search-center{width:99%;margin-left:4px;font-size:15px;float:left;font-weight: bold;}
	form#userpopupsearch div.search-center-history{width:100%;font-size:17px;float:left;padding: 20px 10px; font-weight: bold;}
	form#userpopupsearch div.search-center input{width: 100% !important;padding: 17px 15px;}
	form#userpopupsearch div.search-center-heading{padding:10px 0px 10px 10px;margin-bottom: 10px;}
	form#userpopupsearch div.search-center span.close{position: absolute;top:10px;right: 10px;background-image:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/ticketdetailicon/popup-close.png);background-size: 100%;width:20px;height: 20px;opacity: 1;}
	form#userpopupsearch div.search-center-history span.close-history{position: absolute;top: 22px;right: 16px;background:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/close-icon-white.png) no-repeat;background-size: 100%;width:25px;height: 25px;cursor: pointer;}
	div#usercredentailspopup div.mjtc-support-usercredentails-header span.close-credentails{position: absolute;top: 22px;right: 16px;background:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/close-icon-white.png) no-repeat;background-size: 100%;width:25px;height: 25px;cursor: pointer;}
	div#userpopup{position: fixed;top:50%;left:50%;width:60%; max-height: 50%; padding-top:0px;z-index: 99999;overflow-y: auto; overflow-x: hidden;transform: translate(-50%,-50%);}
	.mjtc-support-textalign-center{text-align: center;}
	div.ms-popup-background{background: rgba(0,0,0,0.5);position: fixed;width: 100%;height: 100%;top:0px;left:0px;z-index: 9989;}
	div.internalnote-popup-background{background: rgba(0,0,0,0.5);position: fixed;width: 100%;height: 100%;top:0px;left:0px;z-index: 9988;}
	div.ms-popup-wrapper{position: fixed;top:50%;left:50%;width:40%;z-index: 1000000;overflow-y: auto; overflow-x: hidden;display: inline-block;max-height:60%;transform: translate(-50%,-50%);}
	div.ms-merge-popup-wrapper{width:50%;max-height:70%;}
	div.ms-popup-header{width:100%;font-size:17px;float:left;padding: 20px 10px; font-weight: bold;text-transform: capitalize;}
	div.popup-header-close-img{position: absolute;top:25px;right: 25px;background-image:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/close-icon-white.png);background-size: 100%;width:25px;height: 25px;opacity: 1;cursor: pointer;}
	div.internalnote-popup-header-close-img{position: absolute;top:25px;right: 25px;background-image:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/close-icon-white.png);background-size: 100%;width:25px;height: 25px;opacity: 1;cursor: pointer;}
	img.popup-header-close-img{position: absolute;top:25px;right: 25px;background-image:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/close-icon-white.png);background-size: 100%;width:25px;height: 25px;opacity: 1;cursor: pointer;}
	div.ms-popup-wrapper input{margin-bottom:0px; }
	div.ms-popup-wrapper input#edited_time{font-size: 16px;width: 100%;}
	div.ms-popup-wrapper input#systemtime{width: 100%;}
	div.ms-popup-wrapper textarea{width: 100%;}
	div.ms-popup-wrapper div.mjtc-form-button-wrapper{text-align: center;border-top: 1px solid #e0dce0;width: 94%;margin: 0px 3%;margin-top: 20px;}
	div.ms-popup-wrapper div.mjtc-form-button-wrapper input.button{display: inline-block;float: none;padding: 5px 20px;border-radius: 2px;margin-top: 15px;margin-bottom: 15px;min-width: 100px;}
	div.ms-popup-wrapper div.mjtc-form-button-wrapper input.mjtc-merge-cancel-btn{padding: 16px 10px;min-width: 120px;border-radius: unset;}
	div.ms-popup-wrapper div.mjtc-form-button-wrapper input.mjtc-merge-save-btn{padding: 16px 10px;min-width: 120px;border-radius: unset;}
	div.mjtc-support-edit-form-wrp{float: left;width: 100%; padding: 20px 20px;}
	div.mjtc-support-edit-form-wrp div.mjtc-support-form-field-wrp{float: left;width: 100%;}
	div.mjtc-support-edit-field-title{float: left;width: 100%;margin-bottom: 5px;}
	div.mjtc-support-edit-field-wrp{float: left;width: 100%;margin-bottom: 10px;}
	div.mjtc-support-popup-search-wrp{float: left;width: 100%;padding: 30px 5px 15px;}
	div.mjtc-support-search-top{float: left;width: 100%;}
	div.mjtc-support-search-top div.mjtc-support-search-left{float: left;width: 70%;}
	div.mjtc-support-search-top div.mjtc-support-search-left div.mjtc-support-search-fields-wrp{float: left;width: 100%;padding: 0px}
	div.mjtc-support-search-top div.mjtc-support-search-left div.mjtc-support-search-fields-wrp input.mjtc-support-search-input-fields{float: left;width: calc(100% / 3 - 10px);margin:0px 5px;padding: 11px 5px;border-radius: 0px}
	div.mjtc-support-search-top div.mjtc-support-search-right{float: left;width: 30%;}
	div.mjtc-support-search-top div.mjtc-support-search-right div.mjtc-support-search-btn-wrp{float: left;width: 100%;}
	div.mjtc-support-search-top div.mjtc-support-search-right div.mjtc-support-search-btn-wrp input.mjtc-support-search-btn{width: calc(100% / 2 - 5px);padding: 17px;border-radius: 0px;}
	div.mjtc-support-search-top div.mjtc-support-search-right div.mjtc-support-search-btn-wrp input.mjtc-support-reset-btn{width: calc(100% / 2 - 5px);padding: 17px;border-radius: 0px;}
	div.ms_userlink.selected
	div.mjtc-support-detail-wrapper div.mjtc-support-openclosed{font-size:24px;text-align: center; line-height: 60px;height: 60px;white-space: nowrap;padding-left: 5px;padding-right: 5px; overflow: hidden;text-overflow: ellipsis}
	div.mjtc-support-detail-wrapper div.mjtc-support-topbar{padding: 0px 0px 10px 0px;margin: 10px 5px 15px 5px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-topbar div.mjtc-openclosed{padding:0px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-topbar div.mjtc-last-left{padding:0px 5px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-topbar div.mjtc-last-left div.mjtc-support-value{padding:0px;}
	div.mjtc-support-detail-wrapper div.mjtc-mid-ticketdetail-part{padding:0px 5px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-middlebar{margin: 2px 0px;}
	div.mjtc-support-detail-wrapper div.mjtc-margin-bottom{margin-bottom: 10px;}
	div.mjtc-support-detail-wrapper div.mjtc-button-margin{margin-top: 15px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-moredetail{margin-bottom: 10px;display:inline-block;}
	div.mjtc-support-detail-wrapper div.mjtc-support-moredetail div.mjtc-support-data-value{margin-bottom: 10px; min-height: 22px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-requester{margin:0px 15px;font-size: 16px;padding-bottom: 5px;margin-bottom: 10px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-bottombar{margin:10px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-bottombar img{width:20px;height:20px;}
	div.mjtc-support-detail-wrapper div.mjtc-support-bottombar img.mjtc-showdetail{float:left;margin-right:5px;-webkit-transform: rotate(180deg);-moz-transform: rotate(180deg);-o-transform: rotate(180deg);-ms-transform: rotate(180deg);transition:all .3s;}
	div.mjtc-support-detail-wrapper div.mjtc-support-bottombar img.mjtc-hidedetail{float:left;margin-right:5px;-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-o-transform: rotate(0deg);-ms-transform: rotate(0deg);transition:all .3s;}
	label#forcloseonreply{display: inline-block;margin: 0px;}
	#records{float: left;width: 100%;padding: 0px 10px;}
	#records .ms-ticket-empty-msg{float: left;width: 100%;padding-bottom: 10px;text-align: center;}
	th:first-child, td:first-child{padding-left: 10px !important;}

	/*Merge Form Css*/
	div.mjtc-support-merge-ticket-wrapper{float: left;width: 100%;padding: 20px;}
	div.mjtc-merge-form-wrapper{padding-bottom: 20px;border-bottom: 1px solid lightgrey;}
	div.mjtc-supports-list-wrp{float: left;width: 100%;padding-top: 10px;padding-bottom: 25px;}
	div.mjtc-merge-form-title{padding:0px; }
	div.mjtc-merge-padding{padding: 15px;}
	div.mjtc-merge-ticket{float: left;width: 100%;padding: 5px;}
	span.mjtc-bold-text{font-weight: bold;display: inline-block;}
	div.mjtc-bold-text{font-weight: bold;display: inline-block;}
	div.mjtc-merge-form-title.mjtc-bold-text{padding:20px;line-height:initial;width: 100%;}
	div.mjtc-merge-form-wrp{float: left;width: 73%;}
	div.mjtc-merge-form-value{float: left;width:calc(100% / 2 - 5px);padding: 0;margin-right: 5px;margin-top: 10px;}
	div.mjtc-merge-form-value input.inputbox{width: 100%;padding: 11px !important;height:55px;}
	div.mjtc-merge-form-btn-wrp{float: left;width: 27%;}
	div.mjtc-view-tickets{float: left;width: calc(100% - 30px);margin-top: 23px;margin-left: 15px; margin-right: 15px; padding: 10px 0px 0px 0px;}
	div.mjtc-view-last-tickets{border-top:0px !important; margin-top: 0px !important; padding-top: 0px !important;}
	span.mjtc-merge-btn{float: left;display: inline-block;width:calc(100% / 2);margin-right:5px;}
	span.mjtc-merge-btn:last-child{margin-right:0px;width:calc(100% / 2 - 5px);}
	input.mjtc-merge-button{padding: 13px 5px !important; width: 100% !important;margin: 10px auto;line-height: unset !important;border-radius: unset !important;}
	div.mjtc-recently-viewed{width:100%; float: left;}
	div.mjtc-margin{margin-bottom: 20px;margin-top: 10px;}
	div.mjtc-merge-ticket-overlay{position: relative;}
	div.mjtc-merge-ticket-overlay:hover .mjtc-over-lay{opacity: 1;}
	div.mjtc-merge-ticket-overlay .mjtc-over-lay{height:100%;background:rgba(0,0,0,.5);text-align:center;padding:0;opacity:0;-webkit-transition: opacity .25s ease;-moz-transition: opacity .25s ease;position: absolute;top: 0;left: 0;width: 100%;}
	a.mjtc-merge-btn{position: absolute;top: 50%; left: 50%;transform: translate(-50%,-50%);display: inline-block;padding: 10px 10px;min-width: 110px;text-decoration: none;}
	input.mjtc-merge-field{padding: 5px 5px !important;border-radius: 0px !important;}
	span.mjtc-edit-msg-heading{display: inline-block;float: left;width: auto;font-size: 13px;margin: 5px auto; }
	textarea.mjtc-merge-field{border-radius: 0;padding-left: 5px;}
	span.mjtc-heading{display: inline-block;float: left;width: 100%;font-weight: bold;margin-top: 25px;padding-top: 15px;}
	span.mjtc-heading-text{margin-top: 0px !important; border-top: 0px !important;padding-top: 0px !important;}
	span.mjtc-sub-heading{display: inline-block;width: 100%;float: left;font-size: 12px;margin: 5px auto;}
	div.mjtc-form-button-wrapper-merge{border-top:none !important ;margin-top: 0px !important;float: left;}
	div.ms_userpages{border: 1px solid #f1f1fc;width: calc(100% - 30px);display: inline-block;text-align: center;vertical-align: middle;margin: 0 15px;}
	.ms_userlink{display: inline-block;padding: 5px 15px; margin-right: 5px; background: -moz-linear-gradient(top, #ffffff 0%, #f2f2f2 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f2f2f2));background: -webkit-linear-gradient(top, #ffffff 0%,#f2f2f2 100%);background: -o-linear-gradient(top, #ffffff 0%,#f2f2f2 100%);background: -ms-linear-gradient(top, #ffffff 0%,#f2f2f2 100%);background: linear-gradient(to bottom, #ffffff 0%,#f2f2f2 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#ffffff", endColorstr="#f2f2f2",GradientType=0 );color: #373435;border: 1px solid #b8b8b8;}
	.ms_userlink.selected{background: '.$MJTC_color7.';color: '.$MJTC_color2.';border: 1px solid '.$MJTC_color1.';}
	.mjtc-text-align-right{text-align: right;float: right;margin-right: 0px;}
	.mjtc-text-align-right.next{background: '.$MJTC_color1.';color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color1.';}
	.mjtc-text-align-left{text-align: left;float: left;}
	.mjtc-text-align-left.prev{background: '.$MJTC_color2.';color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color2.';}
	.mjtc-no-padding{padding-right: 0 !important;padding-left: 0!important;}
	div.mjtc-support-wrapper{margin:8px 0px;padding-left: 0px;padding-right: 0px;}
	.mjtc-ticket-priority{margin-top:40px;margin-left:5px;margin-right:10px;    width: calc(25% - 120px);}
	.mjtc-ticket-priority .mjtc-col-md-6.mjtc-col-xs-12 {width: auto;padding: 0;}
	div.mjtc-support-wrapper div.mjtc-support-pic{margin: 10px 0px;padding: 0px;padding: 0px 10px;text-align: center;position: relative;float: left;width: 120px;height: 96px;}
	div.mjtc-support-wrapper div.mjtc-support-pic img {width: auto;max-width: 100px;max-height: 93px;height: auto;position: absolute;left: 0;right: 0;bottom: 0;top: 0;margin: auto;}
	#mergeticketselection div.mjtc-support-wrapper div.mjtc-support-pic img {border-radius: 50%;}
	div.mjtc-support-wrapper div.mjtc-support-data{position: relative;padding: 20px 0px;width: 45%;}
	div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-status{position: absolute;top:41%;right:2%;padding: 10px 10px;border-radius: 20px;font-size: 10px;line-height: 1;font-weight: bold;}
	div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-status img.ticketstatusimage{position: absolute;top:0px;}
	div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-status img.ticketstatusimage.one{left:-25px;}
	div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-status img.ticketstatusimage.two{left:-50px;}
	div.mjtc-support-wrapper div.mjtc-support-data1{margin:0px 0px;padding: 17px 15px;width: 30%;}
	div.mjtc-support-wrapper div.mjtc-support-data1 div.mjtc-row {float: left;width: 100%;}
	div.mjtc-support-wrapper div.mjtc-support-data1 div.mjtc-row .mjtc-col-md-6 {display: inline-block;margin-right: 5px;width: auto;padding: 0;}
	div.mjtc-support-wrapper div.mjtc-support-bottom-line{position:absolute;display: inline-block;width:90%;margin:0 5%;height:1px;left:0px;bottom: 0px;}
	div.mjtc-support-wrapper div.mjtc-support-toparea{position: relative;padding:0px;}
	.mjtc-col-xs-12.mjtc-col-md-12.mjtc-support-toparea{display:flex;}
	div.mjtc-support-wrapper div.mjtc-support-bottom-data-part{padding: 0px;margin-bottom: 10px;}
	div.mjtc-support-wrapper div.mjtc-support-bottom-data-part a.button{float:right;margin-left: 10px;padding:0px 20px;line-height: 30px;height:32px;}
	div.mjtc-support-wrapper div.mjtc-support-bottom-data-part a.button img{height:16px;margin-right:5px;}
	span.mjtc-support-wrapper-textcolor{display: inline-block;padding: 5px 10px;min-width: 85px;text-align: center;}

	/* Timer CSS */
	div.ms-ticket-detail-timer-wrapper{display: inline-block;width: 100%;line-height: initial;}
	div.ms-ticket-detail-timer-wrapper div.timer-left{;float: left;font-weight: bold;padding: 20px;}
	div.ms-ticket-detail-timer-wrapper div.timer-right{float: right;}
	div.ms-ticket-detail-timer-wrapper div.timer-right div.timer-total-time{padding: 20px;float: left;font-size: 15px;}
	div.ms-ticket-detail-timer-wrapper div.timer-right div.timer{padding:20px 5px;font-weight: bold;float: left;min-width: 120px;text-align: center;font-size:17px; }
	div.ms-ticket-detail-timer-wrapper div.timer-right div.timer-buttons{float: left;padding: 10px 15px;}
	div.ms-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button{float: left;display: inline-block;margin-left: 5px;cursor: pointer;}
	div.ms-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button img{padding: 5px;display: inline-block;float: left;}

	select ::-ms-expand {display:none !important;}
	select{-webkit-appearance:none !important;}

	div#usercredentailspopup{}
	div#usercredentailspopup{position: fixed;top:50%;left:50%;width:60%;z-index: 99999;overflow-x: hidden;text-align: left;transform: translate(-50%,-50%);}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp{height:450px;overflow-y:auto;}
	div#usercredentailspopup div.mjtc-support-usercredentails-header{float: left;width: 100%;padding: 20px 10px;font-weight: bold;font-size: 18px;position: relative;}
	div#usercredentailspopup div.mjtc-support-usercredentails-header span.close-history{position: absolute;top: 22px;right: 16px;background:url('.esc_url(MJTC_PLUGIN_URL).'includes/images/ticketdetailicon/popup-close.png) no-repeat;background-size: 100%;width:20px;height: 20px;cursor: pointer;float: left;width: 100%;padding: 20px 5px;font-weight: bold;font-size: 18px;}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp{float: left;width: 100%;padding: 30px;}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp div.mjtc-support-select-usercredentails{float: left;width: 100%;padding: 10px 10px 0px;}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp div.mjtc-support-select-usercredentails label{float: left;width: 100%;padding-bottom: 5px}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp div.mjtc-support-select-usercredentails input.inputbox{width: 100%;display:inline-block;}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp div.mjtc-support-select-usercredentails textarea.inputbox{width: 100%;display:inline-block;}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp{width: calc(100% - 20px);float: left;text-align: center; padding: 15px 0px;margin: 0px 10px;}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp input.mjtc-support-usercredentails-save{min-width:150px; padding: 15px 5px;}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp input.mjtc-support-usercredentails-cancel{min-width:150px; padding: 15px 5px;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp{display:inline-block;width:100%;padding:30px;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single{display:inline-block;width:100%;padding: 15px 10px 10px;margin-bottom: 15px;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single:last-child{margin-bottom: 1px;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-title{display:inline-block;width:100%;font-weight:bold;color: '.$MJTC_color2.';padding-bottom: 15px;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data{display:inline-block;width:100%;float:left;padding-bottom:10px;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data.mjtc-support-usercredentail-data-full-width{display:inline-block;width:100%;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data .mjtc-support-usercredentail-data-label{display:inline-block;color: '.$MJTC_color2.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data .mjtc-support-usercredentail-data-value{display:inline-block;color: '.$MJTC_color4.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data-button-wrap{display:inline-block;width:100%;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data-button-wrap .mjtc-support-usercredentail-data-button-edit {display:inline-block;padding:10px 15px;border-radius:0px;min-width: 120px;text-align:center; background: '.$MJTC_color1.';color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data-button-wrap .mjtc-support-usercredentail-data-button-edit:hover {border-color: '.$MJTC_color2.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data-button-wrap .mjtc-support-usercredentail-data-button-delete {display:inline-block;padding:10px 15px;border-radius:0px;text-align:center; min-width: 120px;background: '.$MJTC_color2.';color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single .mjtc-support-usercredentail-data-button-wrap .mjtc-support-usercredentail-data-button-delete:hover {border-color: '.$MJTC_color1.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentail-data-add-new-button-wrap{display:inline-block;width:100%;margin-top:20px;}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentail-data-add-new-button-wrap .mjtc-support-usercredentail-data-add-new-button {display:inline-block;padding:10px 15px;border-radius:0px;background: '.$MJTC_color2.';border: 1px solid '.$MJTC_color5.';color: '.$MJTC_color7.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentail-data-add-new-button-wrap .mjtc-support-usercredentail-data-add-new-button:hover {border-color: '.$MJTC_color1.';}
	div#usercredentailspopup .mjtc-support-usercredentails-wrp .mjtc-support-usercredentails-single{background:  #fff;border: 1px solid  '.$MJTC_color5.';}
	div#usercredentailspopup{background:  #fff;border: 1px solid  '.$MJTC_color5.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-header{background:  '.$MJTC_color1.';color: '.$MJTC_color7.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp  div.mjtc-support-select-usercredentails label {color: '.$MJTC_color2.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp div.mjtc-support-select-usercredentails input.inputbox{background-color: #fff;border:1px solid  '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-fields-wrp div.mjtc-support-select-usercredentails textarea.inputbox{background-color: #fff;border:1px solid  '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp{border-top:2px solid  '.$MJTC_color2.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp input.mjtc-support-usercredentails-save{background-color: '.$MJTC_color1.';color: '.$MJTC_color7.';border:1px solid  '.$MJTC_color5.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp input.mjtc-support-usercredentails-save:hover {border-color: '.$MJTC_color2.';}
	div#usercredentailspopup div.mjtc-support-usercreden1tails-btn-wrp input.mjtc-support-usercredentails-cancel{background-color: '.$MJTC_color2.';color: '.$MJTC_color7.';border:1px solid  '.$MJTC_color5.';}
	div#usercredentailspopup div.mjtc-support-usercreden1tails-btn-wrp input.mjtc-support-usercredentails-cancel:hover{border-color: '.$MJTC_color1.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp input.mjtc-support-usercredentails-cancel{background-color: '.$MJTC_color2.';color: '.$MJTC_color7.';border:1px solid  '.$MJTC_color5.';}
	div#usercredentailspopup div.mjtc-support-usercredentails-btn-wrp input.mjtc-support-usercredentails-cancel:hover{border-color: '.$MJTC_color1.';}

	/*new css*/
	.mjtc-sprt-det-left {float: left;width: 70%;padding-right: 30px;}
	.mjtc-sprt-det-cnt {float: left;width: 100%;margin-bottom: 20px;}
	.mjtc-sprt-det-user {float: left;width: 100%;padding: 15px;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-image {float: left;width: 100px;height: 80px;text-align: center;border-radius: 100%;position: relative;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-image img {border-radius: 100%;display: inline-block;margin: 0 auto;position: absolute;top: 0;right: 0;bottom: 0;left: 0;max-width: 100%;max-height: 100%;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt {float: left;width: calc(100% - 100px);padding: 0 0 0 10px;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data {float: left;width: 100%;padding-bottom: 8px;line-height: initial;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data:last-child {padding-bottom: 0;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data.name {font-size: 15px;text-decoration: underline;text-transform: capitalize;line-height: initial;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data.subject {font-weight: bold;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data.agent-email{word-break: break-all;}
	.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data .mjtc-sprt-det-user-val a.mjtc-sprt-det-ticket-title{text-decoration: underline;}
	.mjtc-sprt-det-other-tkt {float: left;width: 100%;padding: 15px;line-height: initial;}
	.mjtc-sprt-det-other-tkt .mjtc-sprt-det-other-tkt .mjtc-sprt-det-other-tkt-btn {display: inline-block;text-decoration: underline;}
	.mjtc-sprt-det-tkt-msg {float: left;width: 100%;padding: 15px;line-height: 1.8;}
	.mjtc-sprt-det-tkt-msg p {line-height: 1.8;}
	.mjtc-sprt-det-actn-btn-wrp {float: left;width: 100%;padding: 15px;}
	.mjtc-sprt-det-actn-btn-wrp .mjtc-sprt-det-actn-btn {float: left;padding: 5px;margin: 2px;text-decoration: none !important;line-height: initial;cursor:pointer;}
	.mjtc-sprt-det-actn-btn-wrp .mjtc-sprt-det-actn-btn img{display: inline-block;max-width: 100%;height: auto;}
	.mjtc-sprt-det-actn-btn-wrp .mjtc-sprt-det-actn-btn span {display: inline-block;vertical-align: middle;}
	.mjtc-sprt-det-right {float: left;width: 30%;}
	.mjtc-sprt-det-right .mjtc-sprt-det-cnt {padding: 15px;}
	.mjtc-sprt-det-hdg {float: left;width: 100%;padding-bottom: 15px;}
	.mjtc-sprt-det-hdg .mjtc-sprt-det-hdg-txt {float: left;font-size: 20px;line-height: initial;}
	.mjtc-sprt-det-hdg .mjtc-sprt-det-hdg-btn {float: right;line-height: initial;}
	.mjtc-sprt-det-status {float: left;width: 100%;padding: 25px 10px;margin-bottom: 15px;text-align: center;font-size: 24px;font-weight: bold;line-height: initial;}
	.mjtc-sprt-det-close-reason-wrp {float: left;width: 100%;padding: 20px 10px 0;margin: -15px 0 15px;}
	.mjtc-sprt-det-info-cnt {float: left;width: 100%;}
	.mjtc-sprt-det-info-data {float: left;width: 100%;padding-bottom: 10px;line-height: initial;}
	.mjtc-sprt-det-info-data .mjtc-sprt-det-info-tit {float: left;margin-right: 8px;}
	.mjtc-sprt-det-info-data .mjtc-sprt-det-info-val {float: left;}
	.mjtc-sprt-det-copy-id {display: inline-block;margin-left: 3px;text-decoration: underline !important;cursor:pointer;}
	.mjtc-sprt-det-tkt-prty-txt {float: left;width: 100%;text-align: center;padding: 15px;color: #fff;margin-bottom: 10px;font-size: 18px;line-height: initial;}
	.mjtc-sprt-det-tkt-prty-error-txt {float: left;font-size: 15px;line-height: initial;}
	.mjtc-sprt-det-tkt-asgn-cnt .mjtc-sprt-det-hdg .mjtc-sprt-det-hdg-txt {font-size: 15px;}
	.mjtc-sprt-det-tkt-asgn-cnt .mjtc-sprt-det-user {padding: 10px 0;}
	.mjtc-sprt-det-trsfer-dep {float: left;width: 100%;padding: 15px 0 7px;}
	.mjtc-sprt-det-trsfer-dep .mjtc-sprt-det-trsfer-dep-txt {float: left;width: 75%;}
	.mjtc-sprt-det-trsfer-dep .mjtc-sprt-det-trsfer-dep-txt .mjtc-sprt-det-trsfer-dep-txt-tit {display: inline-block;}
	.mjtc-sprt-det-trsfer-dep .mjtc-sprt-det-hdg-btn {float: right;text-align: right;}
	.mjtc-sprt-det-right .mjtc-sprt-det-user-tkts {padding: 0;}
	.mjtc-sprt-det-user-tkts .mjtc-sprt-det-hdg {padding: 15px;}
	.mjtc-sprt-det-usr-tkt-list {float: left;width: 100%;}
	.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-user:last-child {border-bottom: 0;padding-bottom: 20px;}
	.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data.name {text-decoration: none;font-size: inherit;}
	.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-user .mjtc-sprt-det-user-image {height: 60px;width: 80px;}
	.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-user .mjtc-sprt-det-user-image img {height: 60px;width: 60px;}
	.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-user .mjtc-sprt-det-user-cnt {width: calc(100% - 80px);}
	.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-prty {float: left;padding: 5px 10px;margin-right: 3px;font-size: 14px;font-weight: bold;text-transform: uppercase;margin-bottom: 5px;}
	.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-status {float: left;padding: 5px 10px;margin: 0;width: auto;font-size: 14px;font-weight: normal;}
	.mjtc-sprt-det-title {float: left;width: 100%;padding: 20px;font-size: 20px;margin-bottom: 20px;line-height: initial;font-weight:bold;}
	.mjtc-support-thread {float: left;width: 100%;padding: 20px;margin-bottom: 20px;}
	.mjtc-support-thread .mjtc-support-thread-image {float: left;width: 100px;height: 80px;border-radius: 100%;text-align: center;position: relative;}
	.mjtc-support-thread .mjtc-support-thread-image img {border-radius: 100%;height: 80px;width: 80px;display: inline-block;margin: 0 auto;position: absolute;top: 0;right: 0;bottom: 0;left: 0;max-width: 100%;max-height: 100%;}
	.mjtc-support-thread .mjtc-support-thread-cnt {float: left;width: calc(100% - 100px);padding: 10px 0 0 15px;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data {float: left;width: 100%;padding-bottom: 8px;line-height: initial;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data:last-child {padding-bottom: 0;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-person {float: left;text-transform: capitalize;font-size: 15px;text-decoration:underline;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-read-status-wrp {float: right;margin-left: 10px;cursor: pointer;position: relative;margin-top: -10px;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-read-status-btn {float: right;padding: 5px 5px;border: 1px solid #ebecec;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-read-status-detail {position: absolute;top: 45px;right: 0;color: #6c757d;border: 1px solid #ebecec;box-shadow: 0px 3px 6px #aaa7a7;padding: 0px 8px;padding: 10px;min-width: 350px;display: none;background-color: #FFF;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-read-status-detail .mjtc-support-thread-read-status-row {float: left;width: 100%;color: #575455;line-height: 1.8;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-note {float: left;text-transform: capitalize;padding-top: 5px;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-email {float: left;text-transform: capitalize;padding-top: 5px;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-date {float: right;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-time {float: left;margin-left: 10px;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data.note-msg {line-height: 1.8;}
	.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data.note-msg p {line-height: 1.8;margin: 10px 0 20px;}
	.mjtc-support-thread .mjtc-support-thread-cnt-btm {float: left;width: 100%;padding: 10px 0 0;}
	.mjtc-support-thread .mjtc-support-thread-cnt-btm .mjtc-support-thread-date {float: left;padding: 10px 0;line-height: initial;}
	.mjtc-support-thread .mjtc-support-thread-actions {float: right;}
	.mjtc-support-thread .mjtc-support-thread-actions .mjtc-support-thread-actn-btn {display: inline-block;margin: 0 2px;padding: 5px;text-decoration: none;line-height: initial;}
	.mjtc-support-thread .mjtc-support-thread-actions .mjtc-support-thread-actn-btn span {display: inline-block;margin-left: 2px;vertical-align: middle;line-height: initial;}
	.mjtc-sprt-det-timer-wrp {float: left;width: 100%;padding: 15px 0;}
	.mjtc-sprt-det-timer-wrp .timer {float: left;width: 100%;font-size: 50px;line-height: initial;}
	.mjtc-sprt-det-timer-wrp .timer .timer-box {display: inline-block;width: calc(100% / 3 - 24px);padding: 20px 15px;text-align: center;}
	.mjtc-sprt-det-timer-wrp .timer-buttons {float: left;width: 100%;text-align: center;padding: 20px 0;}
	.mjtc-sprt-det-timer-wrp .timer-buttons .timer-button {display: inline-block;cursor: pointer;padding: 9px 18px;}
	.mjtc-sprt-det-timer-wrp .timer-total-time  {float: left;width: 100%;}
	.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-title {float: left;margin-right: 5px;}
	.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-value {float: left;width: 100%;font-size: 40px;line-height: initial;}
	.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-value .timer-box {display: inline-block;width: calc(100% / 3 - 10px);padding: 10px;text-align: center;float: left;margin-right: 15px;position: relative;}
	.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-value .timer-box:last-child {margin-right: 0;}
	.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-value .timer-box::after {content: \':\';display: block;position: absolute;top: 10px;right: -14px;}
	.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-value .timer-box:last-child::after {display: none;}
	.mjtc-support-thread-add-btn {float: left;width: 100%;margin-bottom: 20px;}
	.mjtc-support-thread-add-btn .mjtc-support-thread-add-btn-link {display: inline-block;text-decoration: none !important;padding: 7px 12px;line-height: initial;}
	.mjtc-support-thread-add-btn .mjtc-support-thread-add-btn-link img {margin-right: 3px;display:inline-block;}
	.mjtc-support-thread-add-btn .mjtc-support-thread-add-btn-link span {display: inline-block;vertical-align: middle;}
	.mjtc-det-tkt-frm {float: left;width: 100%;}
	.ms-ticket-detail-timer-wrapper {float: left;width: 100%;margin-bottom: 20px;}
	.ms-ticket-detail-timer-wrapper .timer-left {float: left;font-weight: bold;padding: 20px;}
	.ms-ticket-detail-timer-wrapper .timer-right {float: right;}
	.ms-ticket-detail-timer-wrapper .timer-total-time {float: left;font-size: 15px;}
	.ms-ticket-detail-timer-wrapper .timer {padding: 20px 5px;font-weight: bold;float: left;min-width: 120px;text-align: center;font-size: 17px;}
	.ms-ticket-detail-timer-wrapper .timer-buttons {float: left;padding: 10px 15px;}
	.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button {float: left;margin-left: 5px;cursor: pointer;}
	.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button img {padding: 5px;}
	.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button img.default-hide {display: none;}
	.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button:hover img.default-hide {display: block;}
	.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button:hover img.default-show {display: none;}
	.mjtc-det-tkt-form .mjtc-form-wrapper {float: left;width: 100%;margin-bottom: 20px;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-title {float: left;width: 100%;margin-bottom: 8px;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-value {float: left;width: 100%;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-value .mjtc-admin-popup-input-field {display: inline-block;width: 100%;height: 45px;padding: 10px;margin: 0;box-shadow: unset;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-value .mjtc-admin-popup-select-field {display: inline-block;width: 100%;height: 45px;padding: 10px;margin: 0;box-shadow: unset;background-image: url(../images/selecticon.png);background-repeat: no-repeat;background-position: calc(100% - 15px);-webkit-appearance: none;-moz-appearance: none;appearance: none;background-size: 16px;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .tk_attachment_value_wrapperform .tk_attachment_value_text {float: left;width: calc(33.33% - 5px);margin-right: 5px;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .tk_attachments_configform {float: left;width: 100%;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .tk_attachments_addform {display: inline-block;padding: 10px 15px;line-height: inherit;border-radius: 0;height: auto;cursor: pointer;margin: 10px 0;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .ms-formfield-radio-button-wrap {float: left;padding: 10px;height: 45px;margin-right: 5px;width: calc(33.33% - 5px);}
	.mjtc-det-tkt-form .mjtc-form-wrapper .ms-formfield-radio-button-wrap input {margin-right: 5px;margin-top: 1px;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-sprt-det-perm-msg {float: left;margin: 0 5px 5px 0;}
	.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-sprt-det-perm-msg a {display: inline-block;width: 100%;text-decoration: none;padding: 10px;}
	#ms-note-edit-form {float: left;width: 100%;padding: 25px;}
	#ms-note-edit-form .mjtc-form-wrapper {margin-bottom: 15px;}
	#ms-note-edit-form .mjtc-form-wrapper .mjtc-form-title {margin-bottom: 7px;}
	#ms-note-edit-form div.mjtc-form-button-wrapper input.button {min-width: 140px;padding: 15px 5px;line-height: initial;border-radius: 0;}
	div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp .mjtc-support-field-value.name {text-decoration:underline;}
	div.mjtc-support-assigned-tome input#assignedtome1{margin-right: 5px; vertical-align: middle;}
	div.mjtc-support-assigned-tome label#forassignedtome{margin: 0px;display: inline-block;text-transform: capitalize;}
	/* ticket detail woocommerce */
	.mjtc-sprt-wc-order-box {float: left;width: 100%;}
	.mjtc-sprt-wc-order-box .mjtc-sprt-wc-order-item {float: left;width: 100%;padding-bottom: 10px;}
	.mjtc-sprt-wc-order-box .mjtc-sprt-wc-order-item .mjtc-sprt-wc-order-item-title {float: left;margin-right: 8px;}
	.mjtc-sprt-wc-order-box .mjtc-sprt-wc-order-item .mjtc-sprt-wc-order-item-value {float: left;}
	div.mjtc-support-wrapper div.mjtc-support-data a.mjtc-support-merge-ticket-title {font-weight:600;text-decoration:underline;}
	div.mjtc-support-padding-xs.mjtc-support-body-data-elipses{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding: 0;width: 85%;}

	/*ticket histort*/
	.mjtc-support-history-table-wrp table {width: 100%;margin: 20px 0;}
	.mjtc-support-history-table-wrp table th,
	.mjtc-support-history-table-wrp table td {width: 25%;padding: 10px;}
	.mjtc-support-history-table-wrp table th:last-child,
	.mjtc-support-history-table-wrp table td:last-child {width: 50%;padding-left: 15px;}

';
/*Code For Colors*/
$majesticsupport_css .= '


/* Ticket Details*/
		div.mjtc-support-wrapper{border:1px solid '.$MJTC_color5.';box-shadow: 0 8px 6px -6px #dedddd;}
		div.mjtc-support-wrapper:hover div.mjtc-support-bottom-line{background:'.$MJTC_color2.';}
		div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-status{color:#FFFFFF;}
		div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-title{color:'.$MJTC_color2.';}
		div.mjtc-support-wrapper div.mjtc-support-data a.mjtc-support-merge-ticket-title {color:'.$MJTC_color1.';}
		a.mjtc-support-title-anchor:hover{color:'.$MJTC_color2.' !important;}
		div.mjtc-support-wrapper div.mjtc-support-data span.mjtc-support-value{color:'.$MJTC_color4.';}
		div.mjtc-support-wrapper div.mjtc-support-bottom-line{background:'.$MJTC_color2.';}
		div.mjtc-support-assigned-tome{border:1px solid '.$MJTC_color5.';background-color:'.$MJTC_color3.';}
		div.mjtc-support-sorting span.mjtc-support-sorting-link a{background:#373435;color:'.$MJTC_color7.';}
		div.mjtc-support-sorting span.mjtc-support-sorting-link a.selected,
		div.mjtc-support-sorting span.mjtc-support-sorting-link a:hover{background: '.$MJTC_color2.';}
	/* My Tickets $ Staff My Tickets*/

		div.mjtc-support-detail-wrapper{border:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-detail-box{border-bottom:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp{background-color:  '.$MJTC_color3.';}
		div.mjtc-support-detail-box div.mjtc-support-detail-right{background: #fff;}
		div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp{background: #fff;}
		div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-title{color: '.$MJTC_color1.';}
		div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value span.mjtc-support-subject-link{color: '.$MJTC_color2.';}
		div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp .mjtc-support-field-value {color: '.$MJTC_color4.';}
		div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp .mjtc-support-field-value-t {color: '.$MJTC_color2.';}
		div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp .mjtc-support-field-value.name {color: '.$MJTC_color1.';}
		.mjtc-sprt-det-trsfer-dep .mjtc-sprt-det-hdg-btn {color: '.$MJTC_color1.';}
		.mjtc-sprt-det-trsfer-dep .mjtc-sprt-det-hdg-btn:hover {color: '.$MJTC_color2.';}
		div.mjtc-support-detail-right div.mjtc-support-row .mjtc-support-title{color: '.$MJTC_color1.';}
		div.mjtc-support-detail-right div.mjtc-support-row .mjtc-support-value span.mjtc-support-subject-link{color: '.$MJTC_color2.';}

		div.mjtc-support-detail-right div.mjtc-support-openclosed-box{color: '.$MJTC_color7.';}
		div.mjtc-support-detail-right div.mjtc-support-right-bottom{background-color:#fef1e6;color: '.$MJTC_color4.';border-top:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-detail-wrapper div.mjtc-support-action-btn-wrp div.mjtc-support-btn-box{background-color:#e7ecf2;border:1px solid  '.$MJTC_color5.';}
		div#userpopupforchangepriority{background:  #fff;border: 1px solid  '.$MJTC_color5.';}
		div#userpopupforchangepriority div.mjtc-support-priorty-header{background:  '.$MJTC_color1.';color: '.$MJTC_color7.';}
		div#userpopupforchangepriority div.mjtc-support-priorty-fields-wrp div.mjtc-support-select-priorty select#priority{background-color: '.$MJTC_color3.';border:1px solid  '.$MJTC_color5.';}
		div#userpopupforchangepriority div.mjtc-support-priorty-fields-wrp div.mjtc-support-select-priorty select#prioritytemp{background-color: #fff;border:1px solid  '.$MJTC_color5.';}
		div#userpopupforchangepriority div.mjtc-support-priorty-btn-wrp{border-top:2px solid  '.$MJTC_color2.';}
		div#popupforagenttransfer {background:  #fff;border: 1px solid  '.$MJTC_color5.';}
		div#popupfordepartmenttransfer {background:  #fff;border: 1px solid  '.$MJTC_color5.';}
		div#userpopupforchangestatus {background:  #fff;border: 1px solid  '.$MJTC_color5.';}
		div#ticketclosereason {background:  #fff;border: 1px solid  '.$MJTC_color5.';}
		div#popupforinternalnote {background: #fff;border: 1px solid  '.$MJTC_color5.';}
		div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-save{background-color: '.$MJTC_color1.';color: '.$MJTC_color7.';border: 1px solid  '.$MJTC_color2.';font-weight:bold;}
		div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-save:hover{border-color: '.$MJTC_color2.';}
		div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-cancel{background-color:'.$MJTC_color2.';color: '.$MJTC_color7.';border: 1px solid  '.$MJTC_color2.';}
		div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-cancel:hover{border-color: '.$MJTC_color1.';}
		div.mjtc-support-post-reply-wrapper div.mjtc-support-thread-heading{background-color:#e7ecf2;border:1px solid  '.$MJTC_color5.';color: '.$MJTC_color4.';}
		div.mjtc-support-post-reply-box{border:1px solid  '.$MJTC_color5.';box-shadow: 0 0 3px 2px rgba(162, 162, 162, 0.71);background: #fff;}
		div.mjtc-support-white-background{background-color: '.$MJTC_color7.';}
		div.mjtc-support-background{background-color: '.$MJTC_color3.';}
		div.mjtc-support-attachments-wrp{border-top:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-attachments-wrp div.mjtc_supportattachment{border:1px solid  '.$MJTC_color5.';background-color: '.$MJTC_color7.';}
		div.mjtc-support-attachments-wrp div.mjtc_supportattachment a.mjtc-download-button{background-color: '.$MJTC_color3.';color: '.$MJTC_color4.';border:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-attachments-wrp a.mjtc-all-download-button{background-color: '.$MJTC_color1.';color: '.$MJTC_color7.';border:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-attachments-wrp a.mjtc-all-download-button:hover{border-color: '.$MJTC_color2.';}
		.mjtc-support-thread .mjtc-support-thread-cnt-btm .mjtc-support-thread-date{color: '.$MJTC_color4.';}
		div.mjtc-support-edit-options-wrp{border-top:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-edit-options-wrp a.mjtc-button{background-color: '.$MJTC_color3.';border:1px solid  '.$MJTC_color5.';font-size:15px;}
		div.mjtc-support-edit-options-wrp a.mjtc-button img{width:20px;}
		div.mjtc-support-edit-options-wrp .mjtc-support-thread-time {color: '.$MJTC_color4.';}
		div.mjtc-support-time-stamp-wrp span.mjtc-support-ticket-created-date {color: '.$MJTC_color4.';}
		div.mjtc-support-detail-left div.mjtc-support-user-name-wrp {color: '.$MJTC_color2.';}
		div.ms-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button{background-color: '.$MJTC_color2.';color: '.$MJTC_color7.';}
		div.ms-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button:hover {background-color: '.$MJTC_color1.';}
		div.ms-ticket-detail-timer-wrapper{border:1px solid  '.$MJTC_color5.';background: '.$MJTC_color3.';}
		div.ms-ticket-detail-timer-wrapper div.timer-right div.timer{}
		div.ms-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button.selected{background:'.$MJTC_color1.';border: 1px solid '.$MJTC_color5.';}
		select#premadeid{background-color: #fff !important;border:1px solid  '.$MJTC_color5.';}
		div.mjtc-support-time-stamp-wrp{border-top:1px solid  '.$MJTC_color5.';}
		/*Post Reply Section*/
			div.mjtc-support-reply-forms-wrapper div.mjtc-support-reply-forms-heading{background-color: '.$MJTC_color2.';border: 1px solid '.$MJTC_color5.';color: '.$MJTC_color7.';}
			div.tk_attachment_value_wrapperform{border: 1px solid  '.$MJTC_color5.';background:  #fff;}
			span.tk_attachment_value_text{border: 1px solid  '.$MJTC_color5.';background-color: '.$MJTC_color7.';}
			div.mjtc-support-reply-form-button-wrp{border-top: 2px solid  '.$MJTC_color2.';}
			span.tk_attachments_configform{color: '.$MJTC_color4.'}
			div.mjtc-support-reply-attachments div.mjtc-attachment-field-title{color: '.$MJTC_color2.'}
			div.mjtc-support-reply-form-button-wrp input.mjtc-support-save-button{background-color: '.$MJTC_color1.' !important;color: '.$MJTC_color7.' !important;border: 1px solid '.$MJTC_color5.';}
			div.mjtc-support-reply-form-button-wrp input.mjtc-support-save-button:hover{border-color: '.$MJTC_color2.';}
			div.mjtc-support-reply-form-button-wrp a.mjtc-support-cancel-button{background-color:#48484a;color: '.$MJTC_color7.'}
			div.mjtc-support-tabs-wrapper ul.mjtc-support-ul-style{background-color:#e7ecf2;border:1px solid #DEDFE0;border-bottom:2px solid  '.$MJTC_color2.'; }
			div.mjtc-support-tabs-wrapper li.mjtc-support-li-style{background-color: '.$MJTC_color7.';border:1px}
			div.mjtc-support-tabs-wrapper li.mjtc-support-li-style a.mjtc-support-tab-link{color: '.$MJTC_color4.';}
			div.mjtc-support-tabs-wrapper li.mjtc-support-li-style a.mjtc-support-tab-link:hover{background-color: '.$MJTC_color2.';color: '.$MJTC_color7.';}
			div.mjtc-support-tabs-wrapper li.mjtc-support-li-style.ui-tabs-active a.mjtc-support-tab-link{background-color: '.$MJTC_color2.';color: '.$MJTC_color7.';}
			div.mjtc-support-tabs-wrapper li.mjtc-support-li-style a.mjtc-support-tab-link:focus{background-color: '.$MJTC_color2.';color: '.$MJTC_color7.';}
			div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-wrp select.mjtc-support-premade-select{background-color: '.$MJTC_color3.';border:1px solid  '.$MJTC_color5.';}
			div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-wrp select#staffid {background: #fff;}
			div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-wrp select#departmentid {background: #fff;}
			span.mjtc-support-apend-radio-btn{border:1px solid  '.$MJTC_color5.';background-color: #fff;}
			div.mjtc-support-append-signature-wrp div.mjtc-support-append-field-title {color: '.$MJTC_color2.';}
			div.mjtc-support-premade-msg-wrp div.mjtc-support-premade-field-title{color: '.$MJTC_color2.';}
			div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box{border:1px solid  '.$MJTC_color5.';background-color: #fff;}
			div.mjtc-support-assigntome-wrp div.mjtc-support-assigntome-field-wrp{border:1px solid  '.$MJTC_color5.';background-color: #fff;}
			div.mjtc-support-closeonreply-wrp div.mjtc-form-title-position-reletive-left{border:1px solid  '.$MJTC_color5.';background-color: #fff;color: '.$MJTC_color2.';}
			div.mjtc-support-internalnote-wrp div.mjtc-support-internalnote-field-wrp input.mjtc-support-internalnote-input{border:1px solid  '.$MJTC_color5.';background-color: '.$MJTC_color3.';}
			span.tk_attachments_addform{background-color:'.$MJTC_color1.';color:'.$MJTC_color7.';border:1px solid '.$MJTC_color5.';}
			span.tk_attachments_addform:hover{border-color:'.$MJTC_color2.';}
			div#userpopupforchangepriority div.mjtc-support-priorty-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
			div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-save{background-color:'.$MJTC_color1.';color:'.$MJTC_color7.';border:1px solid  '.$MJTC_color5.';}
			div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-save:hover{border-color:'.$MJTC_color2.';}
			div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-cancel{background-color:'.$MJTC_color2.';color:'.$MJTC_color7.';border:1px solid  '.$MJTC_color5.';}
			div.mjtc-support-priorty-btn-wrp input.mjtc-support-priorty-cancel:hover{border-color:'.$MJTC_color1.';}
		/*Post Reply Section*/

		/*Pop Up Section*/
			form#userpopupsearch div.search-center-history{background:  '.$MJTC_color1.';color: '.$MJTC_color7.';}
			div.ms-popup-header{background:  '.$MJTC_color1.';color: '.$MJTC_color7.';}
			div#userpopup{background:  '.$MJTC_color7.';}
			div.ms-popup-wrapper{background-color: '.$MJTC_color7.';}
			div.mjtc-support-search-top div.mjtc-support-search-left div.mjtc-support-search-fields-wrp input.mjtc-support-search-input-fields{border:1px solid  '.$MJTC_color5.';background-color: '.$MJTC_color3.';}
			div.mjtc-support-search-top div.mjtc-support-search-right div.mjtc-support-search-btn-wrp input.mjtc-support-search-btn{background:  '.$MJTC_color2.';color: '.$MJTC_color7.';}
			div.mjtc-support-search-top div.mjtc-support-search-right div.mjtc-support-search-btn-wrp input.mjtc-support-reset-btn{background: #606062;color: '.$MJTC_color7.';}
		/*Pop Up Section*/

		/* Merge Ticket */
			div.mjtc-merge-ticket{background-color:  '.$MJTC_color7.';}
			div.mjtc-support-merge-white-bg{background-color:  '.$MJTC_color7.';}
			span.mjtc-img-wrp{border: 1px solid  '.$MJTC_color5.';}
			span.mjtc-support-info2{border-left: 1px solid  '.$MJTC_color5.';}
			div.mjtc-supports-list-wrp{border-top: 2px solid  '.$MJTC_color5.';background-color: '.$MJTC_color3.';border-bottom: 1px solid  '.$MJTC_color2.';}
			div.mjtc-merge-form-value input.inputbox{border: 1px solid  '.$MJTC_color5.';}
			div.mjtc-view-tickets{border-top: 1px solid  '.$MJTC_color2.';}
			span.mjtc-merge-btn input.mjtc-search{background-color:'.$MJTC_color1.' !important;color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
			span.mjtc-merge-btn input.mjtc-search:hover{border-color: '.$MJTC_color2.';}
			span.mjtc-merge-btn input.mjtc-cancel{background-color:'.$MJTC_color2.' !important;color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
			span.mjtc-merge-btn input.mjtc-cancel:hover{border-color: '.$MJTC_color1.';}
			input.mjtc-merge-cancel-btn{background-color:'.$MJTC_color2.' !important;color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
			input.mjtc-merge-cancel-btn:hover{border-color: '.$MJTC_color1.';}
			div.ms-popup-wrapper div.mjtc-form-button-wrapper input.mjtc-merge-save-btn{background-color: '.$MJTC_color2.' !important;}
			a.mjtc-merge-btn{color:  '.$MJTC_color7.' !important;background-color: '.$MJTC_color2.' !important;}
			a.mjtc-merge-btn:hover{color:  '.$MJTC_color7.' !important;background-color: '.$MJTC_color2.' !important;}
			span.mjtc-support-wrapper-textcolor{color:'.$MJTC_color7.';}
		/* Merge Ticket */

		/*new css*/
		.mjtc-sprt-det-cnt {background: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data.name {color: '.$MJTC_color1.';}
		.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data.subject {color: '.$MJTC_color2.';}
		.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data span.mjtc-sprt-det-user-tit {color: '.$MJTC_color2.';}
		.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data .mjtc-sprt-det-user-val a.mjtc-sprt-det-ticket-title{color: '.$MJTC_color1.';}
		.mjtc-sprt-det-other-tkt {background: #fef1e6;border: 1px solid '.$MJTC_color5.';border-left: 0;border-right: 0;}
		.mjtc-sprt-det-other-tkt .mjtc-sprt-det-other-tkt-btn {color: '.$MJTC_color1.';}
		.mjtc-sprt-det-tkt-msg {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-tkt-msg p {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-actn-btn-wrp {border-top: 1px solid '.$MJTC_color5.';}
		.mjtc-sprt-det-actn-btn-wrp .mjtc-sprt-det-actn-btn {border: 1px solid '.$MJTC_color5.';background: '.$MJTC_color3.';}
		.mjtc-sprt-det-actn-btn-wrp .mjtc-sprt-det-actn-btn:hover {border-color:  '.$MJTC_color2.';}
		.mjtc-sprt-det-actn-btn-wrp .mjtc-sprt-det-actn-btn span {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-hdg .mjtc-sprt-det-hdg-txt {color: '.$MJTC_color2.';}
		.mjtc-sprt-det-hdg .mjtc-sprt-det-hdg-btn {color: '.$MJTC_color1.';}
		.mjtc-sprt-det-hdg .mjtc-sprt-det-hdg-btn:hover {color: '.$MJTC_color2.';}
		.mjtc-sprt-det-status {background: #5bb02f;color: '.$MJTC_color7.';}
		.mjtc-sprt-det-close-reason-wrp {border: 1px solid #ed1c24;}
		.mjtc-sprt-det-info-data .mjtc-sprt-det-info-tit {color: '.$MJTC_color2.';}
		.mjtc-sprt-det-info-data .mjtc-sprt-det-info-val {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-copy-id {color: '.$MJTC_color1.' !important;}
		.mjtc-sprt-det-tkt-prty-txt {color: '.$MJTC_color7.';}
		.mjtc-sprt-det-tkt-prty-error-txt {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-tkt-asgn-cnt .mjtc-sprt-det-hdg .mjtc-sprt-det-hdg-txt {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-trsfer-dep {border-top: 1px solid '.$MJTC_color5.';}
		.mjtc-sprt-det-trsfer-dep .mjtc-sprt-det-trsfer-dep-txt {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-trsfer-dep .mjtc-sprt-det-trsfer-dep-txt span.mjtc-sprt-det-trsfer-dep-txt-tit {color: '.$MJTC_color2.';}
		.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-user {border-bottom: 1px solid '.$MJTC_color5.';}
		.mjtc-sprt-det-user .mjtc-sprt-det-user-cnt .mjtc-sprt-det-user-data .mjtc-sprt-det-user-val {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-prty {color: '.$MJTC_color7.';}
		.mjtc-sprt-det-usr-tkt-list .mjtc-sprt-det-status {color: '.$MJTC_color4.';background: '.$MJTC_color3.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-sprt-det-title {color: '.$MJTC_color7.';background: '.$MJTC_color2.';}
		.mjtc-support-thread {border: 1px solid '.$MJTC_color5.';background: '.$MJTC_color7.';}
		.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-person {color: '.$MJTC_color1.';}
		.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-note {color: '.$MJTC_color4.';}
		.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-email {color: '.$MJTC_color4.';}
		.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-date {color: '.$MJTC_color4.';}
		.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data .mjtc-support-thread-time {color: '.$MJTC_color4.';}
		.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data.note-msg {color: '.$MJTC_color4.';}
		.mjtc-support-thread .mjtc-support-thread-cnt .mjtc-support-thread-data.note-msg p {color: '.$MJTC_color4.';}
		.mjtc-support-thread .mjtc-support-thread-cnt-btm {border-top: 1px solid '.$MJTC_color5.';}
		.mjtc-support-thread .mjtc-support-thread-actions .mjtc-support-thread-actn-btn {color: '.$MJTC_color4.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-support-thread .mjtc-support-thread-actions .mjtc-support-thread-actn-btn:hover {border-color: '.$MJTC_color2.';}
		.mjtc-sprt-det-time-tracker {background: '.$MJTC_color3.';}
		.mjtc-sprt-det-timer-wrp .timer {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-timer-wrp .timer .timer-box {color: '.$MJTC_color4.';background: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-sprt-det-timer-wrp .timer-buttons .timer-button {background: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
		.mjtc-sprt-det-timer-wrp .timer-buttons .timer-button.active {background: '.$MJTC_color2.';color: '.$MJTC_color7.';}
		.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-title {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-value {color: '.$MJTC_color4.';}
		.mjtc-sprt-det-timer-wrp .timer-total-time .timer-total-time-value .timer-box {color: '.$MJTC_color4.';background: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-support-thread-add-btn .mjtc-support-thread-add-btn-link {color: '.$MJTC_color7.';background: '.$MJTC_color1.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-support-thread-add-btn .mjtc-support-thread-add-btn-link:hover {border-color: '.$MJTC_color1.';color: '.$MJTC_color7.';}
		.ms-ticket-detail-timer-wrapper {background: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
		.ms-ticket-detail-timer-wrapper .timer-total-time {color: '.$MJTC_color4.';}
		.ms-ticket-detail-timer-wrapper .timer {color: '.$MJTC_color4.';}
		.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button {background: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
		.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button:hover {background: '.$MJTC_color2.';}
		.ms-ticket-detail-timer-wrapper .timer-buttons .timer-button.selected {background: '.$MJTC_color2.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-title {color: '.$MJTC_color4.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-value {color: '.$MJTC_color4.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-value .mjtc-admin-popup-input-field {color: '.$MJTC_color4.';background: '.$MJTC_color3.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-form-value .mjtc-admin-popup-select-field {color: '.$MJTC_color4.';background: '.$MJTC_color3.';border: 1px solid '.$MJTC_color5.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .tk_attachment_value_wrapperform .tk_attachment_value_text {background: '.$MJTC_color7.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .tk_attachments_addform {border: 1px solid '.$MJTC_color5.';color: '.$MJTC_color7.';background: '.$MJTC_color2.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .ms-formfield-radio-button-wrap {color: '.$MJTC_color4.';border: 1px solid '.$MJTC_color5.';background: '.$MJTC_color7.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-sprt-det-perm-msg a {border: 1px solid '.$MJTC_color5.';background: '.$MJTC_color7.';color: '.$MJTC_color4.';}
		.mjtc-det-tkt-form .mjtc-form-wrapper .mjtc-sprt-det-perm-msg a:hover {border-color: '.$MJTC_color2.';background: '.$MJTC_color7.';color: '.$MJTC_color4.';}
		#ms-note-edit-form div.mjtc-form-button-wrapper input#ppppok {background-color:'.$MJTC_color1.';color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
		#ms-note-edit-form div.mjtc-form-button-wrapper input#ppppok:hover {border-color: '.$MJTC_color2.';}
	#ms-note-edit-form div.mjtc-form-button-wrapper input#cancele {background-color:'.$MJTC_color2.';color: '.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
	#ms-note-edit-form div.mjtc-form-button-wrapper input#cancele:hover {border-color: '.$MJTC_color1.';}
		div.ms-popup-wrapper div.mjtc-form-button-wrapper input.button{color:#fff;}
		/* ticket detail woocommerce */
	.mjtc-sprt-wc-order-box .mjtc-sprt-wc-order-item .mjtc-sprt-wc-order-item-title {color: '.$MJTC_color2.';}
	.mjtc-sprt-wc-order-box .mjtc-sprt-wc-order-item .mjtc-sprt-wc-order-item-value {color: '.$MJTC_color4.';}
	div.mjtc-support-wrapper div.mjtc-support-data1 div.mjtc-row .mjtc-col-md-6:first-child {color: '.$MJTC_color2.';}
	div.mjtc-support-wrapper div.mjtc-support-data1 div.mjtc-row .mjtc-col-md-6:last-child {color: '.$MJTC_color4.';}

	/*ticket histort*/
	.mjtc-support-history-table-wrp table,
	.mjtc-support-history-table-wrp table th,
	.mjtc-support-history-table-wrp table td {border: 1px solid '.$MJTC_color5.';}
	.mjtc-support-history-table-wrp table th{background-color:'.$MJTC_color2.';color:'.$MJTC_color7.';}

	/* Ticket Details*/
	.mjtc-support-ai-powered-reply-title {color: ' . $MJTC_color2 . ';}
	.mjtc-support-ai-powered-reply-text {color: ' . $MJTC_color4 . ';}
	@media (max-width: 480px) {
	    .mjtc-support-ai-powered-reply-wrapper{flex-wrap:wrap;}
	}
	.mjtc-support-ai-powered-reply-wrapper {display: flex;align-items: center;gap: 20px;background-color: #d9ffe7;border: 1px solid #16a34a;padding: 15px;margin: 20px 0;}
	.mjtc-support-ai-powered-reply-wrapper .mjtc-support-ai-powered-reply-content-wrp{display:flex;flex-wrap:wrap;align-items:center;gap:10px;flex:1 1 auto;}
	.mjtc-support-ai-powered-reply-wrapper .mjtc-support-ai-powered-reply-content-wrp .mjtc-support-ai-powered-reply-content{margin-right:auto;}
	.mjtc-support-ai-powered-reply-icon img { width: 50px; }
	.mjtc-support-ai-powered-reply-title { font-weight: 600; color: ' . esc_attr($MJTC_color2) . ';  }
	.mjtc-support-ai-powered-reply-text {font-size: 15px; }
	.mjtc-support-ai-powered-reply-action a {background-color: #15803d;color: #fff !important;padding: 10px 10px;text-decoration: none;font-weight: 600;transition: background-color 0.2s ease;display:flex;align-items:center;border-radius: 8px;}
	.mjtc-support-ai-powered-reply-action a:hover {background-color: #025621;}
    /* ticket detail AI Powered Reply */ 
    .mjtc-support-container {background-color: #ffffff;padding: 24px;border-radius: 12px;box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);width: 100%;max-width: 960px;margin-top: 20px;margin-bottom: 20px;display: none;}
    .js-tkt-det-hdg .mjtc-support-ai-reply-status-control-label{font-weight: 600;color: #1f2937;font-family: inherit;}
	label.mjtc-support-ai-reply-status-control-label {font-size: 20px !important;}
	.mjtc-support-info-icon-wrapper {position: relative;display: inline-block;cursor: help;padding: 0 5px;}
	.mjtc-support-info-icon {display: flex;flex-wrap: wrap;font-size: 18px;color: #6c757d;transition: color 0.2s ease;}
	.mjtc-support-info-icon::before, .status-segment::before {content: "";top: calc(100% + 13px);left: 50%;transform: translateX(-50%) rotate(45deg);width: 14px;height: 35px;background-color: ' . $MJTC_color3 . ';z-index: 1000;}
	.mjtc-support-info-icon img {width: 20px;}
	.mjtc-support-info-icon img {height: 18px;cursor: pointer;opacity: 0.6;transition: opacity 0.2s ease;}
	.mjtc-support-info-icon::after, .mjtc-support-info-icon::before, .status-segment::after, .status-segment::before {box-sizing: border-box;content: attr(data-tooltip);position: absolute;top: calc(100% + 20px);left: -25px;transform: translateX(-50%);background-color: #343a40;color: #fff;padding: 12px 18px;border-radius: 10px;font-size: 13.5px;line-height: 1.5;white-space: normal;width: 200px;text-align: center;opacity: 0;visibility: hidden;transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;z-index: 1000;box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);}
	div.mjtc-support-segmented-control-wrp {padding: 15px 0;width: 100%;font-weight: 600;text-align: center;border-radius: 8px;}
	div.mjtc-support-segmented-control {border: 1px solid #d1d1d1;border-radius: 6px;overflow: hidden;width: 100%;max-width: 100%;box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);flex-wrap: wrap;}
	.mjtc-support-segmented-control {position: relative;padding: 4px;}
	.mjtc-support-segmented-control::before {content: "";top: 4px;left: 4px;height: calc(100% - 8px);width: calc((100% - 8px) / 3);background-color: #3b82f6;border-radius: 7px;box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);z-index: 1;}
	.js-tkt-det-tkt-prty-txt.mjtc-support-segmented-control-wrp button.mjtc-support-segmented-control-option {border: 1px solid #d1d1d1;}
	button.mjtc-support-segmented-control-option.mjtc-support-default.active {background-color: #808080;color: #ffffff;}
	button.mjtc-support-segmented-control-option.active {box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);}
    /* Headings */
    /* Section containers (e.g., Current Ticket, Matching Tickets, Replies) */
    .mjtc-support-section {padding: 16px;border-radius: 8px;/*margin-bottom: 24px;*/border: 1px solid;}
    .mjtc-support-current-ticket-section {background-color: #eff6ff;border-color: #bfdbfe;}
    .mjtc-support-current-ticket-title {display: none;}
    .mjtc-support-current-ticket-id {display: none;}
    .mjtc-support-current-ticket-description {font-size: 14px;color: #1d4ed8;}
    .mjtc-support-post-reply-section .mjtc-support-section-heading {font-size: 20px;font-weight: 600;color: #4a5568;margin-bottom: 16px;}
    .mjtc-support-textarea {width: 100%;padding: 16px;border: 1px solid '. $MJTC_color5 .';border-radius: 8px;resize: vertical;min-height: 150px;transition: border-color 0.2s ease, box-shadow 0.2s ease;}
    .mjtc-support-textarea:focus {outline: none;border-color: #3b82f6;box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);}
    .mjtc-support-button-group {display: flex;flex-direction: column;gap: 12px;margin-top: 16px;}
    @media (min-width: 640px) {.mjtc-support-button-group {flex-direction: row;gap: 16px;margin-top: 0;}}
    .mjtc-support-button {display: block;margin-bottom: 25px;padding: 12px 24px;border-radius: 8px;font-weight: 500;transition: background-color 0.2s ease, box-shadow 0.2s ease;box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);color: #ffffff;border: none;cursor: pointer;text-decoration: none;}
    .mjtc-support-button:hover {text-decoration: none !important;}
    .mjtc-support-post-reply-button {background-color: #2563eb;}
    .mjtc-support-post-reply-button:hover {background-color: #1d4ed8;}
    .mjtc-support-ai-reply-button-wrp {display: inline-block;width: 100%;margin-top: 20px;}
    .mjtc-support-ai-reply-button {background-color: #16a34a;}
    .mjtc-support-ai-reply-button:hover {background-color: #15803d;}
    .mjtc-support-matching-tickets-section {background-color: #f5f3ff;border-color: #ddd6fe;}
    .mjtc-support-selected-tickets-header {display: flex;align-items: center;justify-content: space-between;margin-bottom: 10px;flex-wrap: wrap;gap: 10px;}
    .mjtc-support-matching-tickets-section .mjtc-support-section-heading {color: #6d28d9;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;max-width: 60%;margin: 15px 0 10px;font-weight: bold;line-height: initial;}
    .mjtc-support-list {list-style: none;padding: 8px;margin: 0 -8px;display: flex;flex-direction: column;gap: 12px;max-height: 500px;overflow-y: auto;}
    .mjtc-support-list::-webkit-scrollbar {width: 8px;}
    .mjtc-support-list::-webkit-scrollbar-track {background: #f1f1f1;border-radius: 10px;}
    .mjtc-support-list::-webkit-scrollbar-thumb {background: #cbd5e1;border-radius: 10px;}
    .mjtc-support-list::-webkit-scrollbar-thumb:hover {background: #94a3b8;}
    .mjtc-support-list-item {background-color: #ffffff;padding: 16px;border-radius: 8px;box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);cursor: pointer;transition: background-color 0.15s ease-in-out;}
    .mjtc-support-list-item:hover {background-color: #f9fafb;}
    .mjtc-support-list-item p {margin: 0;}
    .mjtc-support-list-item .mjtc-support-title {font-weight: 500;margin-top: 4px;margin-bottom: 4px;}
    .mjtc-support-list-item .mjtc-support-id {display: -webkit-box;-webkit-line-clamp: 5;-webkit-box-orient: vertical;overflow: hidden;}
    .mjtc-support-selected-replies-section {background-color: #ecfdf5;border-color: #a7f3d0;}
    .mjtc-support-selected-replies-header {display: flex;align-items: center;justify-content: space-between;margin-bottom: 10px;}
    .mjtc-support-selected-replies-header .mjtc-support-section-heading {font-size: 22px;font-weight: 600;color: #047857;margin-bottom: 0;margin-top: 0;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;max-width: 50%;line-height: initial;font-family: inherit;}
    .mjtc-support-close-button {/*background-color: #1E40AF;*/background-color: #16A34A;color: #FFFFFF;padding: 8px 16px;border-radius: 8px;font-weight: 500;transition: background-color 0.2s ease;cursor: pointer;border: none;}
    .mjtc-support-close-button:hover {background-color: #2b2b2b;}
    #mjtc-support-close-tickets-btn {background-color: #6d28d9;color: #FFF;}
    #mjtc-support-close-tickets-btn:hover {background-color: #2b2b2b;}
    .mjtc-support-replies-content {display: flex;flex-direction: column;gap: 24px;max-height: 500px;overflow-y: auto;padding: 8px;margin: 0 -8px;}
    /* Custom scrollbar for reply content */
    .reply-content::-webkit-scrollbar {width: 8px;}
    .reply-content::-webkit-scrollbar-track {background: #f1f1f1;border-radius: 10px;}
    .reply-content::-webkit-scrollbar-thumb {background: #cbd5e1;border-radius: 10px;}
    .reply-content::-webkit-scrollbar-thumb:hover {background: #94a3b8;}
    .mjtc-support-reply-item {background-color: #ffffff;padding: 20px;border-radius: 8px;box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);border: 1px solid #a7f3d0;}
    .mjtc-support-reply-header {display: flex;justify-content: space-between;align-items: center;margin-bottom: 12px;}
    .mjtc-support-reply-header .mjtc-support-reply-id {font-weight: 600;color: #059669;}
    .mjtc-support-reply-header .mjtc-support-reply-timestamp {font-size: 14px;}
    .mjtc-support-reply-text {line-height: 1.625;display: -webkit-box;-webkit-line-clamp: 10;-webkit-box-orient: vertical;overflow: hidden;}
    .mjtc-support-reply-actions {display: flex;gap: 8px;margin-top: 10px; /* Space between text and action buttons */justify-content: flex-end; /* Align buttons to the right */}
    .mjtc-support-reply-action-btn {background-color: #4CAF50; /* Green */color: white;padding: 8px 12px;border: none;border-radius: 5px;cursor: pointer;font-size: 14px;transition: background-color 0.2s ease;}
    .mjtc-support-reply-action-btn.copy-btn {background-color: #ff9800; /* Orange */}
    .mjtc-support-reply-action-btn.copy-btn:hover {background-color: #2b2b2b;}
    .mjtc-support-reply-action-btn.append-btn {background-color: #1578e8;}
    .mjtc-support-reply-action-btn.append-btn:hover {background-color: #2b2b2b;}
    /* Dropdown style */
    .mjtc-support-filter-group {display: flex;align-items: center;gap: 10px;margin-left: auto;margin-right: 5px;margin-bottom: 0px;}
    .mjtc-support-filter-label {font-weight: 500;}
    .mjtc-support-filter-select {padding: 8px 12px;border: 1px solid '. $MJTC_color5 .';border-radius: 8px;background-color: white;cursor: pointer;font-size: 14px;appearance: none; /* Remove default dropdown arrow */-webkit-appearance: none;-moz-appearance: none;background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2C118.8L146.2%2C259.6L5.4%2C118.8z%22%2F%3E%3C%2Fsvg%3E"); /* Custom arrow */background-repeat: no-repeat;background-position: right 10px center;background-size: 12px;transition: border-color 0.2s ease, box-shadow 0.2s ease;}
    .mjtc-support-filter-select:focus {outline: none;border-color: #3b82f6;box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);}
    /* Modal styles */
    .mjtc-support-modal {position: fixed;inset: 0;background-color: rgba(0, 0, 0, 0.5);display: flex;align-items: center;justify-content: center;z-index: 50;}
    .mjtc-support-modal-content {background-color: #ffffff;padding: 24px;border-radius: 12px;box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);max-width: 384px;width: 100%;text-align: center;border: 1px solid '. $MJTC_color5 .';}
    .mjtc-support-modal-message {font-size: 18px;font-weight: 500;color: #1a202c;margin-bottom: 16px;}
    .mjtc-support-modal-close-button {background-color: #2563eb;color: #ffffff;padding: 8px 20px;border-radius: 8px;font-weight: 500;transition: background-color 0.2s ease, box-shadow 0.2s ease;cursor: pointer;border: none;}
    .mjtc-support-modal-close-button:hover {background-color: #1d4ed8;}
    /* Utility for hiding elements */
    .mjtc-support-hidden {display: none;}
    /* Marked as AI-Powered Reply Feature  */
    div.mjtc-support-ai-reply-status-wrapper {float: left;width: 100%;padding: 15px 0px 0px;box-sizing: border-box;}
    div.mjtc-support-ai-reply-status-wrapper label {display: inline-block;margin-bottom: 10px;font-weight: 600;color: #444;}
    div.mjtc-support-segmented-control-wrp {padding: 15px 0;}
    div.mjtc-support-segmented-control {display: flex;border: 1px solid '. $MJTC_color5 .';border-radius: 6px;overflow: hidden;width: 100%;max-width: 100%;box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);flex-wrap: wrap;}
    button.mjtc-support-segmented-control-option {flex: 1;padding: 10px 18px;border: none;background-color: #f6f7f7;color: #40464d;cursor: pointer;font-size: 16px !important;font-weight: 500 !important;transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;outline: none;text-align: center;white-space: nowrap;line-height: 1.3em !important;}
    button.mjtc-support-segmented-control-option:not(:last-child) {border-right: 1px solid #c3c4c7;}
    button.mjtc-support-segmented-control-option:hover {background-color: #e0e0e0;}
    button.mjtc-support-segmented-control-option.active {box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);font-weight: 700;}
    button.mjtc-support-segmented-control-option.mjtc-support-default.active {background-color: #808080;color: #ffffff;}
    button.mjtc-support-segmented-control-option.mjtc-support-enable.active {background-color: #28a745;color: #ffffff;}
    button.mjtc-support-segmented-control-option.mjtc-support-disable.active {background-color: #dc3545;color: #ffffff;}
    button.mjtc-support-segmented-control-option:focus {box-shadow: 0 0 0 2px #2196f3;}
    .js-tkt-det-tkt-prty-txt.mjtc-support-segmented-control-wrp button.mjtc-support-segmented-control-option {padding: 10px 0 !important;border:1px solid '.$MJTC_color5.';}
    label.mjtc-support-ai-reply-status-control-label {font-size: 20px !important;}
    .mjtc-support-info-icon-wrapper {position: relative;display: inline-block;cursor: help;padding: 0 5px;}
    .mjtc-support-info-icon {font-size: 18px;color: #6c757d;transition: color 0.2s ease;}
    .mjtc-support-info-icon-wrapper.tooltip-active .mjtc-support-info-icon::after, .mjtc-support-info-icon-wrapper.tooltip-active .mjtc-support-info-icon::before {opacity: 1;visibility: visible;display:flex;flex-wrap:wrap;}
    .mjtc-support-info-icon {display:flex;flex-wrap:wrap; font-size: 18px;color: #6c757d;transition: color 0.2s ease;}
    .mjtc-support-info-icon::after, .mjtc-support-info-icon::before, .status-segment::after, .status-segment::before {box-sizing: border-box;content: attr(data-tooltip);position: absolute;top: calc(100% + 20px);left: -25px;transform: translateX(-50%);background-color: #343a40;color: #fff;padding: 12px 18px;border-radius: 10px;font-size: 13.5px;line-height: 1.5;white-space: normal;width: 200px;text-align: center;opacity: 0;visibility: hidden;transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;z-index: 1000;box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);}
    .mjtc-support-info-icon::before, .status-segment::before {content: "";top: calc(100% + 13px);left: 50%;transform: translateX(-50%) rotate(45deg);width: 14px;height: 35px;background-color: #343a40;z-index: 1000;}
    .mjtc-support-info-icon img {width: 20px;}
    /* AI Reply */
    .js-tkt-det-tkt-prty div.mjtc-support-segmented-control-wrp {border-top: 1px solid ' . $MJTC_color5 . ';}
    .mjtc-support-container {border: 1px solid ' . $MJTC_color5 . ';}
    .mjtc-support-list-item {border: 1px solid ' . $MJTC_color5 . ';}
    .mjtc-support-list-item .mjtc-support-title {color: ' . $MJTC_color2 . ';}
    .mjtc-support-ai-powered-reply-title {color: ' . $MJTC_color2 . ';}
    .mjtc-support-ai-powered-reply-text {color: ' . $MJTC_color4 . ';}
    .mjtc-support-list-item .mjtc-support-id {color: ' . $MJTC_color4 . ';}
    .mjtc-support-filter-label {color: ' . $MJTC_color2 . ';}
    .mjtc-support-filter-select {color: ' . $MJTC_color2 . ';}
    .mjtc-support-reply-text {color: ' . $MJTC_color4 . ';}
    .mjtc-support-reply-header .mjtc-support-reply-timestamp {color: ' . $MJTC_color4 . ';}
    /*
     * =================================================================
     * MATCHING TICKETS SECTION
     * =================================================================
     */
    #mjtc-support-matching-tickets-list {list-style: none;padding: 0;margin: 0;overflow-y: auto;padding-right: 10px;flex-grow: 1;max-height: 60vh;}
    /* Custom Scrollbar for Webkit browsers */
    #mjtc-support-matching-tickets-list::-webkit-scrollbar,
    #mjtc-support-selected-ticket-replies-content::-webkit-scrollbar {width: 6px;}
    #mjtc-support-matching-tickets-list::-webkit-scrollbar-track,
    #mjtc-support-selected-ticket-replies-content::-webkit-scrollbar-track {background: #f1f1f1;border-radius: 10px;}
    #mjtc-support-matching-tickets-list::-webkit-scrollbar-thumb,
    #mjtc-support-selected-ticket-replies-content::-webkit-scrollbar-thumb {background: #adb5bd;border-radius: 10px;}
    #mjtc-support-matching-tickets-list::-webkit-scrollbar-thumb:hover,
    #mjtc-support-selected-ticket-replies-content::-webkit-scrollbar-thumb:hover {background: #6c757d;}
    /* Individual Ticket Card */
    .mjtc-support-list-item {background-color: #ffffff;border: 1px solid '. $MJTC_color5 .';border-radius: 12px;padding: 20px;margin-bottom: 15px;cursor: pointer;transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;}
    .mjtc-support-list-item:hover {transform: translateY(-3px);box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);}
    .content-area ol li, .content-area ul li {margin: 0 0 .25rem 1rem;border-left: 4px solid ' . $MJTC_color1 . ';}
    .mjtc-support-list-item.selected {border-left: 4px solid ' . $MJTC_color1 . ';background-color: #f0f8ff;border-color: ' . $MJTC_color1 . ';}
    .mjtc-support-list-item .mjtc-support-id {color: #6c757d;margin: 0 0 10px 0;}
    .mjtc-support-list-item .mjtc-support-title {font-weight: 600;color: #343a40;margin: 0 0 10px 0;}
    /* Ticket Description (with multi-line ellipsis) */
    .mjtc-support-list-item p.mjtc-support-description {color: #495057;line-height: 1.6;margin-bottom: 0;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;overflow: hidden;}
    /*
     * =================================================================
     * SELECTED REPLIES SECTION
     * =================================================================
     */
    #mjtc-support-selected-ticket-replies-content {overflow-y: auto;padding-right: 10px;flex-grow: 1;max-height: 60vh;}
    /* Individual Reply Card (Assumed Structure) */
    .mjtc-support-reply-item {background-color: #ffffff;border-radius: 12px;padding: 20px;margin-bottom: 15px;box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);}
    .mjtc-support-reply-id {font-weight: 600;color: #343a40;}
    .mjtc-support-reply-timestamp {color: #6c757d;}
    .mjtc-support-reply-text {color: #495057;line-height: 1.6;}
    .mjtc-support-reply-actions button {padding: 6px 15px;border: none;border-radius: 8px;font-weight: 600;cursor: pointer;transition: opacity 0.2s ease;}
    .mjtc-support-reply-actions button:hover {opacity: 0.85;}
    div#black_wrapper_ai_reply {background: rgba(0,0,0,0.6);position: fixed;width: 100%;height: 100%;top: 0px;left: 0px;z-index: 9989;}


	';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
