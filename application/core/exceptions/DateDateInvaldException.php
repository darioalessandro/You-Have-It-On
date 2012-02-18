<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class DateDateInvaldException extends MY_Exception{

	function __construct($date){
		parent::__construct("exp_date_date_invald", 9, "Date <$date>");
	}
}
?>