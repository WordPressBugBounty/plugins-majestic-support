<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class majesticsupportController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $MJTC_module = MJTC_request::MJTC_getVar('mjsmod', null, 'majesticsupport');
        MJTC_includer::MJTC_include_file($MJTC_module);
    }

}

$majesticsupportController = new majesticsupportController();
?>
