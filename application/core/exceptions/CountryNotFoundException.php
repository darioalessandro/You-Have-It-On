<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CountryNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_country_not_found", 32);
	}
}
?>