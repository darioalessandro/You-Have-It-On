<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class DateInvaldException extends MY_Exception{

	function __construct($date){
		parent::__construct("exp_date_invald", 13, "Date <$date>");
	}
}
?>