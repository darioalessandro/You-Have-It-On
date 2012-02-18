<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StoreNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_store_not_found", 39);
	}
}
?>