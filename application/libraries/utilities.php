<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilities{
	
	public function getUrl(){
		$proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		return $proto.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
}
?>