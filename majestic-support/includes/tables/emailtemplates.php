<?php

if(!defined('ABSPATH'))
	die('Restricted Access');

class MJTC_emailtemplatesTable extends MJTC_table {

	public $id = '';
	public $templatefor = '';
	public $title = '';
	public $subject = '';
	public $body = '';
	public $created = '';
	public $status = '';
	public $multiformid = '';

	function __construct() {
		parent::__construct('emailtemplates', 'id'); // tablename, primarykey
	}

}

?>
