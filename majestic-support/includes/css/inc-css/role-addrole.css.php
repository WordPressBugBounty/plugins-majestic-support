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
	form.mjtc-support-form{display:inline-block; width: 100%;margin-top: 5px;}
	div.mjtc-support-add-form-wrapper{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp{float: left;width: calc(100% / 2 - 10px);margin: 0px 5px; margin-bottom: 20px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp.mjtc-support-from-field-wrp-full-width{float: left;width: calc(100% / 1 - 10px); margin-bottom: 30px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title{text-transform: capitalize;float: left;width: 100%;margin-bottom: 5px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{float: left;width: 100%;border-radius: 0px;padding: 10px;line-height: initial;}
	
	span.mjtc-support-roles-section-heading-right{display: inline-block;float: right;}
	span.mjtc-support-roles-section-heading-right input#rad_alldepartmentaccess{vertical-align: baseline;}
	span.mjtc-support-roles-section-heading-right label{display: inline-block;margin:0px;vertical-align:middle;font-weight:normal;}
	
	div.mjtc-support-roles-wrapper{float: left;width: 100%;margin-top:20px;}
	div.mjtc-support-role-wrp{float: left;width: 100%; margin-top: 20px;margin-bottom: 20px;}
	div.mjtc-support-role-wrp div.mjtc-support-add-role-field-wrp{float: left;width: calc(100% / 3 - 10px);margin: 0px 5px 10px;padding: 10px;line-height: initial;}
	div.mjtc-support-role-wrp div.mjtc-support-add-role-field-wrp.mjtc-support-margin-bottom{margin-bottom: 10px;}
	
	input.mjtc-support-checkbox{vertical-align: baseline;}
	label.mjtc-support-label{display: inline-block;margin: 0px;vertical-align:middle;}
	
	div.mjtc-support-roles-list-wrapper{float: left;width: 100%;margin-top: 20px;}
	div.mjtc-support-add-role-field-wrp-top{margin: 0px !important;width: 100% !important;}
	div.mjtc-support-categories-heading-wrp{float: left;font-weight:bold;width: 100%;padding: 15px;line-height: initial;font-weight:700;}
	div.mjtc-support-form-btn-wrp{float: left;width:calc(100% - 20px);margin: 0px 10px;text-align: center;padding: 25px 0px 10px 0px;}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{padding: 20px 10px;margin-right: 10px;min-width: 120px;border-radius: 0px;line-height: initial;text-transform: capitalize;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{display: inline-block; padding: 20px 10px;min-width: 120px;border-radius: 0px;line-height: initial;text-decoration: none;}
	span.MJTC_help-block{font-size:14px;}
	span.MJTC_help-block{color:red;}

	

';
/*Code For Colors*/
$majesticsupport_css .= '

/* Add Form */
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title {color:'.$MJTC_color2.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{background-color:#fff;border:1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select.mjtc-support-form-field-select{background-color:'.$MJTC_color3.' !important;border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{background-color:'.$MJTC_color1.' !important;color:'.$MJTC_color7.' !important;border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button:hover{border-color:'.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{background: '.$MJTC_color2.';color:'.$MJTC_color7.';border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button:hover{border-color:'.$MJTC_color1.';}

	div.mjtc-support-categories-heading-wrp{background-color:'.$MJTC_color2.';color: '.$MJTC_color7.';border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-add-role-field-wrp{background-color:#fff;border:1px solid '.$MJTC_color5.';}
	label.mjtc-support-label{color:'.$MJTC_color4.';}


/* Add Form */
';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
