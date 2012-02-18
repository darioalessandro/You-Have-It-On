<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class DateYearInvaldException extends MY_Exception{

	function __construct($date){
		parent::__construct("exp_date_year_invald", 12, "Year <$date>");
	}
}
?>