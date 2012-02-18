<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WebSiteNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_website_not_found", 47);
	}
}
?>