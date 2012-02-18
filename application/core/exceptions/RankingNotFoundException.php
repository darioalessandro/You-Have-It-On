<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RankingNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_ranking_not_found", 42);
	}
}
?>