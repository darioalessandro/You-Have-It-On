<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ActionBarControl extends controls{
	private $items 			= array();
	private $id 			= "action_bar_li";
	private $class 			= "action_bar_li";
	private $id_item 		= "action_bar";
	private $class_item		= "action_bar";
	private $class_bar		= "action_bar";
	
	function ActionBarControl(){
		parent::controls();
	}
	
	function setAction($data){
		$this->items = $data;
	}
	function getAction(){
		return $this->items;
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
			"items"			=> $this->items,
			"id"			=> $this->id,
			"class"			=> $this->class,
			"id_item"		=> $this->id_item,
			"class_item"	=> $this->class_item,
			"class_bar"		=> $this->class_bar
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}
		
		$this->html_text = $this->CI->load->view("ui_controls/ActionBarControl", $this->getParams(), true);
	}
	
	function printHtml($print=null){
		if($print == null)
			return $this->html_text;
		elseif($print)
			echo $this->html_text;
		else
			return $this->html_text;
	}
}