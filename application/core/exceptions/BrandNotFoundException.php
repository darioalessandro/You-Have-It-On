<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BrandNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_brand_not_found", 34);
	}
}
?>