<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommentNotFoundException extends MY_Exception{
	
	function __construct(){
		parent::__construct("exp_comment_not_found", 45);
	}
}
?>