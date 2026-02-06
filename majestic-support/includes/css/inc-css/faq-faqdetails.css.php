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
	div.mjtc-support-top-search-wrp{float: left;width: 100%;}
	div.mjtc-support-search-heading-wrp{float: left;width: 100%;}
	div.mjtc-support-search-heading-wrp div.mjtc-support-heading-left{float: left;width: 100%;font-weight: 600;text-transform: capitalize;font-size: 26px;line-height: initial;}
	div.mjtc-support-knowledgebase-wrapper{float: left;width:100%;margin-top: 0px;}
	div.mjtc-support-knowledgebase-details{float: left;width: 100%;padding: 15px 0;line-height: 1.8;}
	div.mjtc-support-knowledgebase-details p {margin: 0;}
	div.mjtc-support-categories-wrp {float: left;width: 100%;margin-top: 25px;}
	div.mjtc-support-margin-bottom {margin-bottom: 20px;margin-top: 10px;}
	div.mjtc-support-categories-heading-wrp {float: left;font-weight:bold;width: 100%;padding: 15px;line-height: initial;}
';
/*Code For Colors*/
$majesticsupport_css .= '
div.mjtc-support-top-search-wrp{}
	div.mjtc-support-search-heading-wrp{color:'.$MJTC_color2.';}
	div.mjtc-support-knowledgebase-details{color:'.$MJTC_color4.';}
	div.mjtc-support-categories-heading-wrp {background-color: '.$MJTC_color3.';border: 1px solid '.$MJTC_color5.';color: '.$MJTC_color2.';}
';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
