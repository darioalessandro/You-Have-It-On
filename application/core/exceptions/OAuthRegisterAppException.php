<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthRegisterAppException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_register_app", 18, "Error: <$error>");
	}
}
?>