<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotHavePermissionException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_not_have_permission", 38);
	}
}
?>