<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthSessionException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_session", 24, "Error: <$error>");
	}
}
?>