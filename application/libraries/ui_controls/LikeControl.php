<?php defined('BASEPATH') OR exit('No direct script access allowed');

class LikeControl extends controls{
	private $i_like	 		= "";
	private $id 			= "";
	private $class 			= "";
	
	function LikeControl(){
		parent::controls();
	}
	
	function setILike($data){
		$this->i_like = $data;
	}
	function getILike(){
		return $this->i_like;
	}
	
	function setId($data){
		$this->id = $data;
	}
	function getId(){
		return $this->id;
	}
	
	function setClass($data){
		$this->class = $data;
	}
	function getClass(){
		return $this->class;
	}
	
	private function getParams(){
		$data = array(
			"i_like"		=> $this->i_like,
			"id"			=> $this->id,
			"class"			=> $this->class
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}
		
				
		$this->html_text = $this->CI->load->view("ui_controls/LikeControl", $this->getParams(), true);
	}
	
	function printHtml($print=null){ 
		if($print)
			echo $this->html_text;
		else
			return $this->html_text;
	}
}