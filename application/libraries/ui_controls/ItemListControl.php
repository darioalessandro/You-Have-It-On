<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ItemListControl extends controls{
	private $titulo 		= "Postyles";
	private $items	 		= array();
	private $categorys		= array();
	private $id 			= "ui_item";
	private $class 			= "ui_item";
	private $action_bar		= "";
	private $params			= array();
	private $pagination_class = "ui_pag_link pag_items";
	private $total_items	= 0;
	private $num_cols		= 1;
	private $cat_per_page	= 2;
	private $cat_num_cols	= 2;
	private $detailed_item	= true;
	private $open_detail_win_click = false; 
	
	function ItemListControl(){
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
			"categorys"		=> $this->categorys,
			"id"			=> $this->id,
			"class"			=> $this->class,
			"action_bar"	=> $this->action_bar,
			"params"		=> $this->params,
			"pagination_class"	=> $this->pagination_class,
			"total_items"	=> $this->total_items,
			"num_cols"		=> $this->num_cols,
			"cat_per_page"	=> $this->cat_per_page,
			"cat_num_cols"	=> $this->cat_num_cols,
			"detailed_item"	=> $this->detailed_item,
			"open_detail_win_click" => $this->open_detail_win_click
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}		
				
		$this->html_text = $this->CI->load->view("ui_controls/ItemListControl", $this->getParams(), true);
	}
	
	function printHtml($print=null){ 
		if($print)
			echo $this->html_text;
		else
			return $this->html_text;
	}
}