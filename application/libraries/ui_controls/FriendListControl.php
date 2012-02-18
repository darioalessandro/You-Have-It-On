<?php defined('BASEPATH') OR exit('No direct script access allowed');

class FriendListControl extends controls{
	private $titulo 		= "Friends";
	private $items	 		= array();
	private $total_items	= 0;
	private $id 			= "ui_friends";
	private $class 			= "ui_friends";
	private $pagination_class = "ui_pag_link pag_friends";
	private $params			= array();
	private $view_actionbar	= true;
	private $num_cols		= 2;
	private $show_link_more	= true;
	
	function FriendListControl(){
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
			"params"		=> $this->params,
			"total_items"	=> $this->total_items,
			"pagination_class"	=> $this->pagination_class,
			"view_actionbar"	=> $this->view_actionbar,
			"num_cols"		=> $this->num_cols,
			"show_link_more"	=> $this->show_link_more
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}
		
				
		$this->html_text = $this->CI->load->view("ui_controls/FriendListControl", $this->getParams(), true);
	}
	
	function printHtml($print=null){ 
		if($print)
			echo $this->html_text;
		else
			return $this->html_text;
	}
}