<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class MethodNotExistException extends MY_Exception{

	function __construct(){
		parent::__construct("exp_method_not_exist", 3);
	}
}
?>