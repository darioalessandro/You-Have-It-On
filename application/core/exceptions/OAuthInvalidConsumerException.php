<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthInvalidConsumerException extends MY_Exception{

	function __construct(){
		parent::__construct("exp_oauth_invalid_consumer", 36);
	}
}
?>