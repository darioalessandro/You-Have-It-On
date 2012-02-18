<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PhoneNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_phone_not_found", 48);
	}
}
?>