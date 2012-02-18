<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserValidateException extends MY_Exception{
	
	function __construct($message=''){
		parent::__construct("exp_user_validate", 5, $message);
	}
}
?>