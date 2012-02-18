<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_category_not_found", 40);
	}
}
?>