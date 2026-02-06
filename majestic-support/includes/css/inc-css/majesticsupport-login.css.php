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
/* Login Page */
	div.mjtc-support-login-wrapper{float: left;width: 100%;margin: 0 !important;}
	div.mjtc-support-login-wrapper div.mjtc-support-login{float: left;width: 100%;}
	div.mjtc-support-login-wrapper div.mjtc-support-login form#loginform-custom{width:100%;float: left;padding: 10px;margin: 0px;}
	form#loginform-custom p.login-username{width:calc(50% - 10px);float:left;margin-right:10px !important;margin-bottom: 15px;}
	form#loginform-custom p.login-username label{font-weight: unset;margin-bottom: 7px;}
	form#loginform-custom p.login-password{width:50%;float:left;margin-bottom: 15px!important;}
	form#loginform-custom p.login-password label{font-weight: unset;margin-bottom: 7px;}
	form#loginform-custom p.login-remember label{font-weight: unset;margin-bottom: 7px;}
	form#loginform-custom p.login-remember {margin-top: 10px !important;}
	form#loginform-custom p.login-remember label input#rememberme{vertical-align: baseline;}
	form#loginform-custom p.login-submit{width:100%;float:left;padding:20px 0px;text-align: center;margin-top:15px !important;}
	form#loginform-custom p.login-username input#user_login{border-radius: unset;width:100%;padding: 10px;height: 50px;}
	form#loginform-custom p.login-password input#user_pass{border-radius: unset;width:100%;padding: 10px;height: 50px;}
	form#loginform-custom p.login-submit input#wp-submit{min-width: 120px;border-radius: unset;padding: 20px 10px;line-height: initial;}
	span.MJTC_help-block{font-size:14px;}
	span.MJTC_help-block{color:red;}
	div.ms-main-up-wrapper a:link:hover{color:blue;text-decoration:underline;}
	div.ms-main-up-wrapper a:hover{color:blue;text-decoration:underline;}
	div.ms-main-up-wrapper a{margin-left:5px;}
';
/*Code For Colors*/
$majesticsupport_css .= '
	/* Login Page */
		form#loginform-custom p.login-username label{color:'.$MJTC_color2.';}
		form#loginform-custom p.login-submit{border-top:2px solid '.$MJTC_color2.';}
		form#loginform-custom p.login-username input#user_login{background-color:#fff; border:1px solid '.$MJTC_color5.';color:'.$MJTC_color4.';}
		form#loginform-custom p.login-password input#user_pass{background-color:#fff; border:1px solid '.$MJTC_color5.';color:'.$MJTC_color4.';}
		form#loginform-custom p.login-submit input#wp-submit{background-color:'.$MJTC_color1.';color:'.$MJTC_color7.';border:1px solid '.$MJTC_color5.';}
		form#loginform-custom p.login-submit input#wp-submit:hover{border-color:'.$MJTC_color2.';}
		form#loginform-custom p.login-remember {color:'.$MJTC_color2.';}
	/* Login Page */
';


wp_add_inline_style('majesticsupport-main-css',$majesticsupport_css);


?>
