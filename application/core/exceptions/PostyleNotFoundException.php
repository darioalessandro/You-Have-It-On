<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PostyleNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_postyle_not_found", 44);
	}
}
?>