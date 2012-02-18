<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthAccessServerException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_access_server", 21, "Error: <$error>");
	}
}
?>