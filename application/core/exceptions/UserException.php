<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserException extends MY_Exception{
	
	function __construct($message=''){
		parent::__construct("exp_user", 31, $message);
	}
}
?>