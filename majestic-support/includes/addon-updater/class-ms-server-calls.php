<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MJTC_SupportTicketServerCalls extends MJTC_SUPPORTTICKETUpdater{

	private static $MJTC_server_url = 'https://majesticsupport.com/setup/index.php';

	public static function MJTC_PluginUpdateCheck($MJTC_token_arrray_json) {
		$MJTC_args = array(
			'request' => 'pluginupdatecheck',
			'token' => $MJTC_token_arrray_json,
			'domain' => MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl()
		);

		$MJTC_url = self::$MJTC_server_url . '?' . http_build_query( $MJTC_args, '', '&' );
		$MJTC_request = wp_remote_get($MJTC_url);

		if ( is_wp_error( $MJTC_request ) || wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
			$MJTC_error_message = 'pluginupdatecheck case returned error';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}

		$MJTC_response = wp_remote_retrieve_body( $MJTC_request );
		$MJTC_response = json_decode($MJTC_response);

		if ( is_object( $MJTC_response ) ) {
			return $MJTC_response;
		} else {
			$MJTC_error_message = 'pluginupdatecheck case returned data which was not correct';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}
	}

	public static function MJTC_PluginUpdateCheckFromCDN() {

		$MJTC_url = "https://d2k6fm08zy0hmd.cloudfront.net/addonslatestversions.txt";
		$MJTC_request = wp_remote_get($MJTC_url);

		if ( is_wp_error( $MJTC_request ) || wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
			$MJTC_error_message = 'pluginupdatecheck cdn case returned error';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}

		$MJTC_response = wp_remote_retrieve_body( $MJTC_request );
		$MJTC_response = json_decode($MJTC_response);

		if ( is_object( $MJTC_response ) ) {
			return $MJTC_response;
		} else {
			$MJTC_error_message = 'pluginupdatecheck cdn case returned data which was not correct';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}
	}

	public static function MJTC_GenerateToken($transaction_key,$MJTC_addon_name) {
			$MJTC_args = array(
				'request' => 'generatetoken',
				'transactionkey' => $transaction_key,
				'productcode' => $MJTC_addon_name,
				'domain' => MJTC_includer::MJTC_getModel('majesticsupport')->getSiteUrl()
			);

			$MJTC_url = self::$MJTC_server_url . '?' . http_build_query( $MJTC_args, '', '&' );
			$MJTC_request = wp_remote_get($MJTC_url);
			if ( is_wp_error( $MJTC_request ) || wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
				$MJTC_error_message = 'generatetoken case returned error';
				MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
				return array('error'=>$MJTC_error_message);
			}

			$MJTC_response = wp_remote_retrieve_body( $MJTC_request );
			$MJTC_response = json_decode($MJTC_response,true);

			if ( is_array( $MJTC_response ) ) {
				return $MJTC_response;
			} else {
				$MJTC_error_message = 'generatetoken case returned data which was not correct';
				MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
				return array('error'=>$MJTC_error_message);
			}
			return false;
		}


	public static function MJTC_GetLatestVersions() {
		$MJTC_args = array(
				'request' => 'getlatestversions'
			);
		$MJTC_request = wp_remote_get( 'https://majesticsupport.com/appsys/addoninfo/index.php' . '?' . http_build_query( $MJTC_args, '', '&' ) );

		if ( is_wp_error( $MJTC_request ) || wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
			$MJTC_error_message = 'getlatestversions case returned error';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}

		$MJTC_response = wp_remote_retrieve_body( $MJTC_request );
		$MJTC_response = json_decode($MJTC_response,true);
		if ( is_array( $MJTC_response ) ) {
			return $MJTC_response;
		} else {
			$MJTC_error_message = 'getlatestversions case returned data which was not correct';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}
	}

	public static function MJTC_PluginInformation( $MJTC_args ) {
		$MJTC_defaults = array(
			'request'        => 'plugininformation',
			'plugin_slug'    => '',
			'version'        => '',
			'token'    => '',
			'domain'          => site_url()
		);

		$MJTC_args    = wp_parse_args( $MJTC_args, $MJTC_defaults );
		$MJTC_request = wp_remote_get( 'https://majesticsupport.com/appsys/addoninfo/index.php' . '?' . http_build_query( $MJTC_args, '', '&' ) );

		if ( is_wp_error( $MJTC_request ) || wp_remote_retrieve_response_code( $MJTC_request ) != 200 ) {
			$MJTC_error_message = 'plugininformation case returned data error';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}
		$MJTC_response = wp_remote_retrieve_body( $MJTC_request );

		$MJTC_response = json_decode($MJTC_response);

		if ( is_object( $MJTC_response ) ) {
			return $MJTC_response;
		} else {
			$MJTC_error_message = 'plugininformation case returned data which is not correct';
			MJTC_includer::MJTC_getModel('systemerror')->addSystemError($MJTC_error_message);
			return false;
		}
	}
}
