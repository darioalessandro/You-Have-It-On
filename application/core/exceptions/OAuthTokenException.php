<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthTokenException extends MY_Exception{

	function __construct(){
		parent::__construct("exp_oauth_token", 29);
	}
}
?>