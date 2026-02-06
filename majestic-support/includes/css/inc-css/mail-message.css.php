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
	div.mjtc-support-mail-wrapper{float: left;width: 100%;margin-top: 5px;}
	div.mjtc-support-mails-btn-wrp{float: left;width: 100%;margin-top: 20px;padding: 0px 5px; }
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn{float: left;width:calc(100% / 3 - 10px);margin: 0px 5px;text-align: center;}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link{display: inline-block;float: left;width: 100%;padding: 15px;text-decoration: none;outline: 0;line-height: initial;}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link.active img{filter: invert(100%) !important;}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link:hover img{filter: invert(100%) !important;}
	div.mjtc-support-margin-top{margin-top: 50px !important;}
	th:first-child, td:first-child{padding-left: 10px !important;}
	img.mjtc-support-mail-img{vertical-align: sub;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select#to{background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / 2% no-repeat;line-height: initial;height: 60px;padding: 10px;}
	div.mjtc-support-top-search-wrp{float: left;width: 100%;}
	div.mjtc-support-search-heading-wrp{float: left;width: 100%; padding: 10px 10px 10px 0px;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-left{float: left;width: 70%;padding: 10px;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right{float: left;width: 30%;text-align: right;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn{display: inline-block;padding: 10px;text-decoration: none;outline: 0px;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn span.mjtc-support-add-img-wrp{display: inline-block;margin-right: 5px;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn span.mjtc-support-add-img-wrp img{vertical-align: text-bottom;}

	div.mjtc-support-post-reply-wrapper {float: left;width: 100%;margin-top: 20px;}
	div.mjtc-support-post-reply-wrapper div.mjtc-support-thread-heading{display: inline-block;width: 100%;padding: 15px;font-size: 18px;margin-bottom: 20px;line-height: initial;}
	div.mjtc-support-post-reply-box{margin-bottom: 20px;}
	div.mjtc-support-detail-box{float: left;width: 100%;}
	div.mjtc-support-detail-box div.mjtc-support-detail-left{float: left;width: 20%;padding: 20px 5px;}
	div.mjtc-support-detail-left div.mjtc-support-user-img-wrp{display:inline-block;width:100%;text-align: center;margin: 0 0 5px;height: 100px;position: relative;}
	div.mjtc-support-detail-left div.mjtc-support-user-img-wrp img.mjtc-support-staff-img{width: auto;max-width: 100%;max-height: 100%;height: auto;position: absolute;top: 0;left: 0;right: 0;bottom: 0;margin: auto;}
	div.mjtc-support-detail-left div.mjtc-support-user-name-wrp{display:inline-block;width:100%;text-align: center;margin: 5px 0px;line-height: initial;}
	div.mjtc-support-detail-left div.mjtc-support-user-email-wrp{display:inline-block;width:100%;text-align: center;margin: 5px 0px;line-height: initial;}
	div.mjtc-support-detail-box div.mjtc-support-detail-right{float: left;width: calc(100% - 21%);}
	div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp{float: left;width: 100%;position: relative;padding:15px;line-height: initial;}
	div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp.mjtc-support-min-height{min-height:292px;}
	div.mjtc-support-detail-right div.mjtc-support-row{float: left;width: 100%;padding: 0px 0 8px 0px;}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-title{display: inline-block;width:auto;margin: 0px 5px 0px 0px;}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value{display: inline-block;width:auto;}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value p {margin: 0;}
	div.mjtc-support-form-btn-wrp{float: left;width:calc(100% - 20px);margin: 0px 10px;text-align: center;padding: 25px 0px 10px 0px;}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{padding: 20px 10px;margin-right: 10px;min-width: 120px;border-radius: 0px;line-height: initial;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{display: inline-block; padding: 20px 10px;min-width: 120px;border-radius: 0px;line-height: initial;text-decoration: none;}


';
/*Code For Colors*/
$majesticsupport_css .= '
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link{background-color: '.$MJTC_color3.';border:1px solid  '.$MJTC_color5.'; color: '.$MJTC_color2.';}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link:hover{background-color: '.$MJTC_color1.';border:1px solid  '.$MJTC_color2.'; color: '.$MJTC_color7.';}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link.active{background-color: '.$MJTC_color1.' !important; border:1px solid  '.$MJTC_color2.' !important; color: '.$MJTC_color7.' !important;}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link img{display:inline-block;}
	div.mjtc-support-detail-box{border:1px solid  '.$MJTC_color5.';}
	div.mjtc-support-detail-box div.mjtc-support-detail-right div.mjtc-support-rows-wrp{color: '.$MJTC_color2	.';}
	div.mjtc-support-detail-box div.mjtc-support-detail-right{border-left:1px solid  '.$MJTC_color5.';}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-title{color: '.$MJTC_color1.';}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value span.mjtc-support-subject-link{color: '.$MJTC_color2.';}
	div.mjtc-support-detail-right div.mjtc-support-openclosed-box{color: '.$MJTC_color7.';}
	div.mjtc-support-detail-left div.mjtc-support-user-name-wrp{color: '.$MJTC_color4.';}
	div.mjtc-support-detail-left div.mjtc-support-user-email-wrp{color: '.$MJTC_color4.';}
	div.mjtc-support-detail-right div.mjtc-support-row div.mjtc-support-field-value{color: '.$MJTC_color4.';}
	div.mjtc-support-detail-right div.mjtc-support-right-bottom{background-color:#fef1e6;color: '.$MJTC_color4.';border-top:1px solid  '.$MJTC_color5.';}
	div.mjtc-support-detail-wrapper div.mjtc-support-action-btn-wrp div.mjtc-support-btn-box{background-color:#e7ecf2;border:1px solid  '.$MJTC_color5.';}
	div.mjtc-support-post-reply-wrapper div.mjtc-support-thread-heading{background-color:'.$MJTC_color2.';border:1px solid '.$MJTC_color5.';color:'.$MJTC_color7.';}
	div.mjtc-support-form-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{background-color:'.$MJTC_color1.' !important;color:'.$MJTC_color7.' !important;border: 1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button:hover {border-color:'.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{background: '.$MJTC_color2.';color:'.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button:hover{border-color: '.$MJTC_color1.';}


';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
