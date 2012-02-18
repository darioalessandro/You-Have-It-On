<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PostyleControl extends controls{
	private $titulo 		= "Postyles";
	private $items	 		= array();
	private $id 			= "ui_postyle";
	private $class 			= "postyle";
	private $id_lis 		= "li-postyle";
	private $class_lis		= "li-postyle";
	private $action_bar		= "";
	private $num_columns	= 2;
	private $params			= array();
	private $width_commentbox	= 600;
	
	function PostyleControl(){
		parent::controls();
	}
	
	function setTitulo($text){
		$this->titulo = $text;
	}
	function getTitulo(){
		return $this->titulo;
	}
	
	function setItems($data){
		$this->items = $data;
	}
	function getItems(){
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
			"titulo"		=> $this->titulo,
			"items"			=> $this->items,
			"id"			=> $this->id,
			"class"			=> $this->class,
			"id_lis"		=> $this->id_lis,
			"class_lis"		=> $this->class_lis,
			"num_cols"		=> $this->num_columns,
			"action_bar"	=> $this->action_bar,
			"params"		=> $this->params,
			"width_commentbox"	=> $this->width_commentbox
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}
		
		$this->params["result_items_per_page"] = "3";
		$this->params["result_page"] = "1";
		$this->params["postyle_id"] = "0";
		$this->params["link_all_comment"] = "1";
		
				
		$this->html_text = $this->CI->load->view("ui_controls/PostyleControl", $this->getParams(), true);
	}
	
	function printHtml($print=null){ 
		if($print)
			echo $this->html_text;
		else
			return $this->html_text;
	}
}