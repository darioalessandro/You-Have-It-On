<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class ServiceNotExistException extends MY_Exception{

	function __construct(){
		parent::__construct("exp_service_not_exist", 2);
	}
}
?>