<?php

if(!defined('ABSPATH'))
	die('Restricted Access');

class MJTC_statusesTable extends MJTC_table {

	public $id = '';
	public $status = '';
	public $statuscolour = '';
	public $statusbgcolour = '';
	public $sys = '';
	public $ordering = '';

	function __construct() {
		parent::__construct('statuses', 'id'); // tablename, primarykey
	}

}

?>
