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
	div.mjtc-support-mails-btn-wrp{float: left;width: 100%;margin-top: 20px;padding: 0px 5px; }
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn{float: left;width:calc(100% / 3 - 10px);margin: 0px 5px;text-align: center;}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link{display: inline-block;float: left;width: 100%;padding: 15px;text-decoration: none;outline: 0;line-height: initial;}
	form.mjtc-support-form{display:inline-block; width: 100%; margin-top: 5px;}
	div.mjtc-support-add-form-wrapper{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp{float: left;width: calc(100% / 2 - 10px);margin: 0px 5px; margin-bottom: 20px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp.mjtc-support-from-field-wrp-full-width{float: left;width: calc(100% / 1 - 10px); margin-bottom: 30px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title{text-transform: capitalize;float: left;width: 100%;margin-bottom: 5px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{float: left;width: 100%;border-radius: 0px;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select.mjtc-support-form-field-select{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 97% / 4% no-repeat #eee;background-size:auto;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-form-btn-wrp{float: left;width:calc(100% - 20px);margin: 0px 10px;text-align: center;padding: 25px 0px 10px 0px;}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{padding: 20px 10px;margin-right: 10px;min-width: 120px;border-radius: 0px;line-height: initial;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{display: inline-block; padding: 20px 10px;min-width: 120px;border-radius: 0px;line-height: initial;text-decoration: none;}
	span.MJTC_help-block{font-size:13px;color:red;bottom: -30px;}
	

	select ::-ms-expand {display:none !important;}
	select{-webkit-appearance:none !important;}
';
/*Code For Colors*/
$majesticsupport_css .= '

/* Add Form */
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title {color: '.$MJTC_color2.';}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link{background-color: '.$MJTC_color3.';border:1px solid  '.$MJTC_color5.'; color: '.$MJTC_color2.';}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link:hover{background-color: '.$MJTC_color1.';border:1px solid  '.$MJTC_color2.'; color: '.$MJTC_color7.';}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link:hover img{ filter: invert(100%);}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link.active{background-color: '.$MJTC_color1.' !important; border:1px solid  '.$MJTC_color2.' !important; color: '.$MJTC_color7.' !important;}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link.active img{filter: invert(100%) !important;}
	div.mjtc-support-mails-btn-wrp div.mjtc-support-mail-btn a.mjtc-add-link img{display:inline-block;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{background-color:#fff;border:1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select.mjtc-support-form-field-select{background-color:#fff !important;border:1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div.mjtc-support-form-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{background-color:'.$MJTC_color1.' !important;color:'.$MJTC_color7.' !important;border: 1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button:hover{border-color:'.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{background: '.$MJTC_color2.';color:'.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button:hover{border-color:'.$MJTC_color1.';}
/* Add Form */


';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
