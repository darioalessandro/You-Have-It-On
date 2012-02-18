<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserAlreadyExistsException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_user_already_exists", 30);
	}
}
?>