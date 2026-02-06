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

	form.mjtc-support-form{display:inline-block; width: 100%; padding: 20px 0 10px;}
	div.mjtc-support-add-form-wrapper{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp{float: left;width: calc(100% / 2 - 10px);margin: 0px 5px; margin-bottom: 20px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp.mjtc-support-from-field-wrp-full-width{float: left;width: calc(100% / 1 - 10px); margin-bottom: 30px; }
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title{text-transform: capitalize;float: left;width: 100%;margin-bottom: 5px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field{float: left;width: 100%;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{float: left;width: 100%;border-radius: 0px;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select.mjtc-support-form-field-select{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 96% / 4% no-repeat #eee;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field.mjtc-support-from-field-wrp-full-width select#status{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / 2% no-repeat;padding: 10px;line-height: initial;height: 50px;}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp.mjtc-support-from-field-wrp-full-width div.mjtc-support-from-field select#status{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 98% / 2% no-repeat;padding: 10px;line-height: initial;height: 50px;}

	div.mjtc-support-radio-btn-wrp{float: left;width: 100%;padding: 11px}
	div.mjtc-support-radio-btn-wrp input.mjtc-support-form-field-radio-btn{margin-right: 5px; vertical-align: top;}
	div.mjtc-support-radio-btn-wrp label#forsendmail{margin: 0px;display: inline-block; margin-right: 30px;}


	div.mjtc-support-form-btn-wrp{float: left;width:calc(100% - 20px);margin: 0px 10px;text-align: center;padding: 25px 0px 10px 0px;}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{padding: 20px 10px;margin-right: 10px;min-width: 120px;border-radius: 0px;line-height: initial;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{display: inline-block; padding: 20px 10px;min-width: 120px;border-radius: 0px;line-height: initial;text-decoration: none;}

	div.mjtc-support-append-signature-wrp{float: left;width: calc(100% / 2 - 25px); margin-right:25px;margin-bottom: 20px;}
	div.mjtc-support-append-signature-wrp.mjtc-support-append-signature-wrp-full-width{width: 100%;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-append-field-title{float: left;width: 100%;margin-bottom: 15px;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-append-field-wrp{float: left;width: 100%;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box{float: left;width: calc(100% / 3 - 10px);margin: 0px 5px;padding: 11px;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box.mjtc-support-signature-radio-box-full-width{width: 100%;}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box label#forcanappendsignature{margin: 0px;display: inline-block;}

	span.MJTC_help-block{font-size:14px;}
	span.MJTC_help-block{color:red;}

	select ::-ms-expand {display:none !important;}
	select{-webkit-appearance:none !important;}
	.majestic-support-outgoing-email-message{font-size:12px;}
	div.mjtc-support-top-search-wrp.second-style{float: left;width: 100%;}
	div.mjtc-support-search-heading-wrp{float: left;width: 100%; padding: 10px;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-left{float: left;width: 70%;padding: 15px 10px;line-height: initial;font-weight:bold;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right{float: left;width: 30%;text-align: right;font-weight:bold;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn{display: inline-block;padding: 15px 25px;text-decoration: none;outline: 0px;line-height: initial;text-transform:capitalize;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn span.mjtc-support-add-img-wrp{display: inline-block;margin-right: 5px;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn span.mjtc-support-add-img-wrp img{vertical-align: text-bottom;}

	div.mjtc-support-top-search-wrp.second-style{margin-top:20px;}

';
/*Code For Colors*/
$majesticsupport_css .= '
/* Add Form */
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field-title {color:'.$MJTC_color2.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field input.mjtc-support-form-field-input{background-color:#fff;border:1px solid '.$MJTC_color5.';color:'.$MJTC_color4.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select#categoryid{background-color:'.$MJTC_color3.';border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select.mjtc-support-form-field-select{background-color:'.$MJTC_color3.' !important;border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-add-form-wrapper div.mjtc-support-from-field-wrp div.mjtc-support-from-field select#status{background-color:'.$MJTC_color3.' !important;border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{background-color:'.$MJTC_color2.' !important;color:'.$MJTC_color7.' !important;}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{background: #606062;color:'.$MJTC_color7.';}
	div.mjtc-support-radio-btn-wrp{background-color:'.$MJTC_color3.';border:1px solid '.$MJTC_color5.';}
	span.tk_attachments_addform{background-color:'.$MJTC_color2.';color:'.$MJTC_color7.';}
	div.mjtc-support-append-signature-wrp div.mjtc-support-signature-radio-box{border:1px solid '.$MJTC_color5.';background-color:'.$MJTC_color3.';}

	div.mjtc-support-field-wrp input.mjtc-support-form-input-field{background-color:'.$MJTC_color3.'; border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp{border-top:2px solid '.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button{background-color:'.$MJTC_color1.' !important;color:'.$MJTC_color7.' !important;border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp input.mjtc-support-save-button:hover {border-color:'.$MJTC_color2.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button{background: '.$MJTC_color2.';color:'.$MJTC_color7.';border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-form-btn-wrp a.mjtc-support-cancel-button:hover{border-color: '.$MJTC_color1.';}

	div.mjtc-support-top-search-wrp{border:1px solid  '.$MJTC_color5.';}
	div.mjtc-support-search-heading-wrp{background-color: '.$MJTC_color2.';color: '.$MJTC_color7.';}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn{background: #fff;color: '.$MJTC_color2.';}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-right a.mjtc-support-add-download-btn:hover{color: '.$MJTC_color1.';}

	div.mjtc-support-search-heading-wrp.second-style{background-color: '.$MJTC_color3.';color: '.$MJTC_color2.';}
	div.mjtc-support-search-heading-wrp.second-style div.mjtc-support-heading-right a.mjtc-support-add-download-btn{background: '.$MJTC_color4.';color: '.$MJTC_color7.';}



';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
