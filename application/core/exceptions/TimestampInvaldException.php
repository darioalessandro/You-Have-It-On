<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class TimestampInvaldException extends MY_Exception{

	function __construct($date){
		parent::__construct("exp_timestamp_invald", 10, "Timestamp <$date>");
	}
}
?>