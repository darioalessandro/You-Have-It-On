<?php defined('BASEPATH') OR exit('No direct script access allowed');

abstract class controls{
	protected $CI;
	protected $html_text;
	
	function controls(){
		$this->CI =& get_instance();
	}
	
	abstract function ini($data=null);
	abstract function printHtml($print=null);
	
}