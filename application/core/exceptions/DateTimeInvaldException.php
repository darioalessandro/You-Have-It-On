<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class DateTimeInvaldException extends MY_Exception{

	function __construct($date){
		parent::__construct("exp_date_time_invald", 11, "Time <$date>");
	}
}
?>