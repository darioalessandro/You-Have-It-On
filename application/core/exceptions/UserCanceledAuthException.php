<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserCanceledAuthException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_user_canceled_auth", 8);
	}
}
?>