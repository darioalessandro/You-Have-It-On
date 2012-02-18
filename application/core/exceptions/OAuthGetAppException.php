<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthGetAppException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_get_app", 19, "Error: <$error>");
	}
}
?>