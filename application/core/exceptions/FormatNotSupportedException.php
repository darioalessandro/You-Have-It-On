<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class FormatNotSupportedException extends MY_Exception{

	function __construct(){
		parent::__construct("exp_format_not_supported", 1);
	}
}
?>