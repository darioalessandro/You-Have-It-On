<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImageNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_image_not_found", 35);
	}
}
?>