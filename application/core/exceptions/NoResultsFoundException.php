<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NoResultsFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_no_results_found", 37);
	}
}
?>