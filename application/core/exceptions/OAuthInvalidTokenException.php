<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthInvalidTokenException extends MY_Exception{

	function __construct(){
		parent::__construct("exp_oauth_invalid_token", 27);
	}
}
?>