<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_user_not_found", 4);
	}
}
?>