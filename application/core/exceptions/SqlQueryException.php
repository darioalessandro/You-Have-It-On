<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class SqlQueryException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_sqlquery_error", 15, "Error: <$error>");
	}
}
?>