<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthServerException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_server", 22, "Error: <$error>");
	}
}
?>