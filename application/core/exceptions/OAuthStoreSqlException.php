<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class OAuthStoreSqlException extends MY_Exception{

	function __construct($error){
		parent::__construct("exp_oauth_store_sql", 26, "Error: <$error>");
	}
}
?>