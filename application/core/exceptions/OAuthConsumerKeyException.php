<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthConsumerKeyException extends MY_Exception{

	function __construct(){
		parent::__construct("exp_oauth_consumer_key", 28);
	}
}
?>