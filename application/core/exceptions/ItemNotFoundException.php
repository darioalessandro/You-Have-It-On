<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_item_not_found", 41);
	}
}
?>