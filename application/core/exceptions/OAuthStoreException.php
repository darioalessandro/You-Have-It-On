<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthStoreException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_store", 23, "Error: <$error>");
	}
}
?>