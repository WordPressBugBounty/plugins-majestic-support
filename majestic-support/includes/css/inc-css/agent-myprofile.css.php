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
	/* My Profile */
	div.mjtc-support-downloads-wrp{float: left;width: 100%;margin-top: 5px;}
	div.mjtc-support-downloads-wrp div.mjtc-support-downloads-heading-wrp{float: left;width: 100%;padding: 15px;line-height: initial;font-weight:bold;}
	div.mjtc-support-profile-wrp{float: left;width: 100%;margin-top: 30px;}
	div.mjtc-support-profile-wrp div.mjtc-support-profile-left{float: left;width: 200px;text-align: center;}
	div.mjtc-support-profile-wrp div.mjtc-support-profile-left div.mjtc-support-user-img-wrp{float: left; width: 100%;height: 200px;position: relative;}
	div.mjtc-support-profile-wrp div.mjtc-support-profile-left div.mjtc-support-user-img-wrp img.profile-image{position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: auto;max-width: 100%;max-height:100%;}
	div.mjtc-support-profile-wrp div.mjtc-support-profile-right{float: left;width: calc(100% - 200px - 20px); margin:0px 0px 0px 20px;}
	div.mjtc-support-from-field{position: relative;}
	img.mjtc-support-profile-form-img{position:absolute; top:8px; right:12px; bottom:0;cursor:pointer;}
	div#showhidemouseover{position: relative;display: inline-block;margin-top: 30px;min-width: 170px;text-align: center;margin-bottom: 15px;}
	label.mjtc-support-file-upload-label{display: block;padding:11px 0px; border-radius: 2px;transition: background .3s; font-weight: unset;}
	input.mjtc-support-upload-input{position: absolute;left: 0;top: 0;right: 0;bottom: 0;width: 0;height: 100%;opacity: 0;cursor: pointer;}
	span.mjtc-support-input-field-style{display: inline-block;float: left;width: 100%;padding: 11px 5px;}
	textarea{border-radius: unset !important;}

	div.mjtc-support-add-form-wrapper{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp{float: left;width: calc(100% / 2 - 10px);margin: 0px 5px; margin-bottom: 20px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp.mjtc-support-from-field-wrp-full-width{float: left;width: calc(100% / 1 - 10px); margin-bottom: 30px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title{float: left;width: 100%;margin-bottom: 5px;text-transform: capitalize;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{float: left;width: 100%;border-radius: 0px;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select#categoryid{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 96% / 4% no-repeat;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select.mjtc-support-form-field-select{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 96% / 4% no-repeat #eee;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field.mjtc-support-from-field-wrp-full-width select#status{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / auto no-repeat;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp.mjtc-support-from-field-wrp-full-width div.mjtc-support-from-field select#status{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / auto no-repeat;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select#status{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 96% / 4% no-repeat ;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select#parentid{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 96% / 4% no-repeat ;padding: 10px;line-height: initial;height: 50px;}

	div.mjtc-support-form-btn-wrp{float: left;width:calc(100% - 20px);margin: 0px 10px;text-align: center;padding: 25px 0px 10px 0px;}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{padding: 20px 10px;margin-right: 10px;min-width: 120px;border-radius: 0px;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{display: inline-block; padding: 14px 10px;margin-right: 10px;min-width: 120px;border-radius: 0px;}

	span.MJTC_help-block{font-size:14px;}
span.MJTC_help-block{color:red;}
';
/*Code For Colors*/
$majesticsupport_css .= '
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{background-color:#fff;border:1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select.mjtc-support-form-field-select{background-color:#fff !important;border:1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div.mjtc-support-form-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{background-color:'.$MJTC_color2.' !important;color:'.$MJTC_color7.' !important;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{background: #606062;color:'.$MJTC_color7.';}
	label.mjtc-support-file-upload-label{background-color:'.$MJTC_color2.';border:1px solid '.$MJTC_color2.'; color:'.$MJTC_color7.';}
	span.mjtc-support-input-field-style{background-color:'.$MJTC_color3.';border:1px solid '.$MJTC_color5.'; color:'.$MJTC_color4.';}
	input.mjtc-support-white-background{background-color:'.$MJTC_color7.' !important;}
	textarea{background-color:'.$MJTC_color3.' !important;border:1px solid '.$MJTC_color5.' !important;; color:'.$MJTC_color4.' !important;}
	div.mjtc-support-profile-wrp div.mjtc-support-profile-left div.mjtc-support-user-img-wrp{background-color:#fff;}
	input.mjtc-support-recaptcha{background-color:'.$MJTC_color3.' !important;border:1px solid '.$MJTC_color5.' !important;}
	div.mjtc-support-downloads-wrp div.mjtc-support-downloads-heading-wrp{background-color: '.$MJTC_color2.';border:1px solid '.$MJTC_color5.';color: '.$MJTC_color7.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title {color: '.$MJTC_color2.';}


';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
