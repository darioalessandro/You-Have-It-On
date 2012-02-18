<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthRegisterServerException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_register_server", 20, "Error: <$error>");
	}
}
?>