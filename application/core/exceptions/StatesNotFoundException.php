<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StatesNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_states_not_found", 33);
	}
}
?>