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
/* Downloads */
	
	div.mjtc-support-download-wrapper{float: left;width: 100%;margin-top: 5px;}
	div.mjtc-support-top-search-wrp{float: left;width: 100%;}
	div.mjtc-support-search-fields-wrp{float: left;width: 100%;padding: 10px 5px;}
	
	form#majesticsupportform{float: left;width: 100%;}
	div.mjtc-support-fields-wrp{float: left;width: 75%;}
	div.mjtc-support-fields-wrp div.mjtc-support-form-field{float: left; width: calc(100% / 2 - 10px);margin: 0px 5px;position: relative;}
	div.mjtc-support-fields-wrp div.mjtc-support-form-field-download-search{width:75%;margin: 0px;}
	div.mjtc-support-fields-wrp div.mjtc-support-form-field input.mjtc-support-field-input{float: left;width: 100%;border-radius: 0px; padding: 10px;line-height: initial;height: 50px;}
	select.mjtc-support-select-field{float: left;width: 100%;border-radius: 0px;background: url('.esc_url(MJTC_PLUGIN_URL).'includes/images/selecticon.png) 96% / auto no-repeat #eee; padding: 10px;line-height: initial;height: 50px;}
	
	div.mjtc-support-search-form-btn-wrp{float: left;width: 25%; padding: 0px 5px;}
	div.mjtc-support-search-form-btn-wrp-download {width:25%;padding: 0px;margin-top: 0px;}
	div.mjtc-support-search-form-btn-wrp input {float: left;width: calc(100% / 2 );padding: 13px 0px;text-align: center;border-radius: unset;line-height: initial;height: 50px;}
	div.mjtc-support-search-form-btn-wrp input:last-child {margin-right: 0px;}
	div.mjtc-support-search-form-btn-wrp-download input.mjtc-search-button{float: left;width: calc(100% / 2 - 10px); padding: 17px 0px;text-align: center;margin: 0px 0px 0px 10px; border-radius: 0px; }
	div.mjtc-support-search-form-btn-wrp-download input.mjtc-reset-button{float: left;width: calc(100% / 2 - 10px); padding: 17px 0px;text-align: center; margin: 0px 0px 0px 10px; border-radius: 0px;}
	
	div.mjtc-support-download-content-wrp{float: left;width: 100%;margin-top: 30px;}
	div.mjtc-support-table-heading-wrp{float: left;width: 100%;}
	div.mjtc-support-table-heading-wrp div.mjtc-support-table-heading-left{float: left;width: 70%;padding: 15px 10px;line-height: initial;}
	div.mjtc-support-table-heading-wrp div.mjtc-support-table-heading-right{float: left;width: 30%;text-align: right;}
	div.mjtc-support-table-heading-wrp div.mjtc-support-table-heading-right a.mjtc-support-table-add-btn{display: inline-block;padding: 15px 25px;text-decoration: none;outline: 0px;line-height: initial;}
	div.mjtc-support-table-heading-wrp div.mjtc-support-table-heading-right a.mjtc-support-table-add-btn span.mjtc-support-table-add-img-wrp{display: inline-block;margin-right: 5px;}
	div.mjtc-support-table-heading-wrp div.mjtc-support-table-heading-right a.mjtc-support-table-add-btn span.mjtc-support-table-add-img-wrp img{vertical-align: text-bottom;}
	div.mjtc-support-table-wrp{float: left;width: 100%;padding: 0;}
	div.mjtc-support-table-wrp div.mjtc-support-table-header{float: left;width: 100%;margin-bottom: 15px;font-weight:bold;}
	div.mjtc-support-table-wrp div.mjtc-support-table-header div.mjtc-support-table-header-col{padding: 15px;text-align: center;line-height: initial;}
	div.mjtc-support-table-wrp div.mjtc-support-table-header div.mjtc-support-table-header-col:first-child{text-align: left;}
	div.mjtc-support-table-body{float: left;width: 100%;}
	div.mjtc-support-table-body div.mjtc-support-data-row{float: left;width: 100%;margin-bottom: 15px;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col{padding:20px 15px 10px 15px;text-align: center;line-height: initial;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col:first-child{text-align: left;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col .mjtc-support-title-anchor {display: inline-block;text-decoration: none;height: 25px;width: 95%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-weight: bold;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col .mjtc-support-table-action-btn {padding: 4px 5px 8px;margin: 0 2px;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col .mjtc-support-table-action-btn img {display:inline-block;}
	
	
	span.mjtc-support-display-block{display: none;}
	div.mjtc-support-attached-files-wrp{float: left;width: calc(100% / 2 - 10px);margin: 0px 5px;margin-top: 15px;} 
	div.mjtc_supportattachment{float: left;width: 70%;padding: 10px 5px;}
	a.mjtc-support-delete-attachment{display:inline-block;float: left;width: 30%;padding: 11px 5px;text-align: center;text-decoration: none;outline: 0px;}
	span.MJTC_help-block{font-size: 14px;}

	select ::-ms-expand {display:none !important;}
	select{-webkit-appearance:none !important;}
	
	
';
/*Code For Colors*/
$majesticsupport_css .= '
/* Downloads */
	div.mjtc-support-search-fields-wrp  {background: '.$MJTC_color3.';}
	div.mjtc-support-top-search-wrp{border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-table-heading-wrp{color:'.$MJTC_color2.';}
	div.mjtc-support-table-heading-wrp div.mjtc-support-table-heading-right a.mjtc-support-table-add-btn{background:'.$MJTC_color2.';color:'.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';}
	div.mjtc-support-table-heading-wrp div.mjtc-support-table-heading-right a.mjtc-support-table-add-btn:hover{border-color:'.$MJTC_color1.';}
	div.mjtc-support-fields-wrp div.mjtc-support-form-field input.mjtc-support-field-input{background-color:#fff;border:1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
	select.mjtc-support-select-field{background-color:#fff !important;border:1px solid '.$MJTC_color5.';color: '.$MJTC_color4.';}
	div.mjtc-support-search-form-btn-wrp input.mjtc-search-button{background: '.$MJTC_color1.' !important;color:'.$MJTC_color7.' !important;border: 1px solid '.$MJTC_color5.';margin-right:10px;width:calc(100% / 2 - 5px)}
	div.mjtc-support-search-form-btn-wrp input.mjtc-search-button:hover{border-color: '.$MJTC_color2.';}
	div.mjtc-support-search-form-btn-wrp input.mjtc-reset-button{background: '.$MJTC_color2.';color:'.$MJTC_color7.';border: 1px solid '.$MJTC_color5.';width:calc(100% / 2 - 5px)}
	div.mjtc-support-search-form-btn-wrp input.mjtc-reset-button:hover{border-color: '.$MJTC_color1.';}
	div.mjtc-support-table-header{background-color:'.$MJTC_color2.';color:'.$MJTC_color7.'; border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-table-header div.mjtc-support-table-header-col{color: '.$MJTC_color7.';}
	div.mjtc-support-table-header div.mjtc-support-table-header-col:last-child{}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col .mjtc-support-title-anchor{color: '.$MJTC_color2.';}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col .mjtc-support-title-anchor:hover {color: '.$MJTC_color1.';}
	div.mjtc-support-table-body div.mjtc-support-data-row{border:1px solid '.$MJTC_color5.';}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col{color: '.$MJTC_color4.';}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col:nth-child(4n){padding:12px;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col:last-child{padding:17px;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col .mjtc-support-table-action-btn {border:1px solid '.$MJTC_color5.';background: #fff;}
	div.mjtc-support-table-body div.mjtc-support-data-row div.mjtc-support-table-body-col .mjtc-support-table-action-btn:hover {border-color:'.$MJTC_color1.';}
	div.mjtc-support-download-wrapper div.mjtc-support-table-body div.mjtc-support-data-row {border:1px solid'.$MJTC_color5.';}
	th.mjtc-support-table-th{border-right:1px solid '.$MJTC_color5.';}
	tbody.mjtc-support-table-tbody{border:1px solid '.$MJTC_color5.';}
	td.mjtc-support-table-td{border-right:1px solid '.$MJTC_color5.';}
/* Downloads */
';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
