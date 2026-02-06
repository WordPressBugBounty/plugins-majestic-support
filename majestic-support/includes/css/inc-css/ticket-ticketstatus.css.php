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
/* Ticket Status */
	form.mjtc-support-form{display:inline-block; width: 100%;}
	div.mjtc-support-checkstatus-wrp{float: left;width: 100%;}
	div.mjtc-support-checkstatus-wrp div.mjtc-support-checkstatus-field-wrp{float: left;width: calc(100% / 2 - 10px); margin:0px 5px;margin-bottom: 25px;}
	div.mjtc-support-field-title{float: left;width: 100%;margin-bottom: 10px;}
	div.mjtc-support-field-wrp{float: left;width: 100%;}
	div.mjtc-support-field-wrp input.mjtc-support-form-input-field{float: left;width: 100%;border-radius: 0px;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-form-btn-wrp{float: left;width:calc(100% - 20px);margin: 0px 10px;text-align: center;padding: 25px 0px 10px 0px;}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{padding: 20px 10px;margin-right: 10px;min-width: 120px;border-radius: 0px;line-height: initial;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{display: inline-block; padding: 20px 10px;min-width: 120px;border-radius: 0px;line-height: initial;text-decoration: none;}


';
/*Code For Colors*/
$majesticsupport_css .= '

/*Ticket Status*/
	div.mjtc-support-field-wrp input.mjtc-support-form-input-field{background-color:#fff; border:1px solid '.$MJTC_color5.';color:'.$MJTC_color4.';}
	div.mjtc-support-field-title{color:'.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{background-color:'.$MJTC_color1.' !important;color:'.$MJTC_color7.' !important;border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button:hover{border-color:'.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{background-color:'.$MJTC_color2.';color:'.$MJTC_color7.';border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button:hover{border-color:'.$MJTC_color1.';}

/*Ticket Status*/

';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
