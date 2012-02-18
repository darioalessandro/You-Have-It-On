<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class DateException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_date_error", 14, "Error: <$error>");
	}
}
?>