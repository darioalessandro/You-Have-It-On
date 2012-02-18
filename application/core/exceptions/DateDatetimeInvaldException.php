<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class DateDatetimeInvaldException extends MY_Exception{

	function __construct($date){
		parent::__construct("exp_date_datetime_invald", 7, "Date <$date>");
	}
}
?>