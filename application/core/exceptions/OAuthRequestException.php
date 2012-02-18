<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthRequestException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_request", 25, "Error: <$error>");
	}
}
?>