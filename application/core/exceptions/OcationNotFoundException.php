<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OcationNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_ocation_not_found", 43);
	}
}
?>