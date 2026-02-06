<?php

if(!defined('ABSPATH'))
	die('Restricted Access');

class MJTC_productsTable extends MJTC_table {

	public $id = '';
	public $product = '';
	public $status = '';
	public $ordering = '';

	function __construct() {
		parent::__construct('products', 'id'); // tablename, primarykey
	}

}

?>
