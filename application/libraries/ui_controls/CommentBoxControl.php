<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CommentBoxControl extends controls{
	private $titulo 			= "";
	private $comments 			= array();
	private $id 				= "ui_comments";
	private $class 				= "comment";
	private $id_titulo 			= "title_comment";
	private $class_titulo		= "title_comment";
	private $link_all_comment	= "1";
	private $show_comment_textbox = false;
	private $can_post			= true;
	private $width_box			= 600;
	private $action_method		= '';
	private $content_parent		= 'div.postyle';
	private $id_obj_comment		= 'postyle_id';
	private $params				= array();
	private $is_of_postyle		= false;
	
	function CommentBoxControl(){
		parent::controls();
	}
	
	function setTitulo($text){
		$this->titulo = $text;
	}
	function getTitulo(){
		return $this->titulo;
	}
	
	function setComments($data){
		$this->comments = $data;
	}
	function getComments(){
		return $this->comments;
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
			"titulo"			=> $this->titulo,
			"items"				=> $this->comments,
			"id"				=> $this->id,
			"class"				=> $this->class,
			"id_titulo"			=> $this->id_titulo,
			"class_titulo"		=> $this->class_titulo,
			"link_all_comment"	=> $this->link_all_comment,
			"show_comment_textbox"	=> $this->show_comment_textbox,
			"can_post"			=> $this->can_post,
			"width_box"			=> $this->width_box,
			"action_method"		=> $this->action_method,
			"content_parent"	=> $this->content_parent,
			"id_obj_comment"	=> $this->id_obj_comment,
			"params"			=> $this->params,
			"is_of_postyle"		=> $this->is_of_postyle
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}
		
		$this->html_text = $this->CI->load->view("ui_controls/CommentBoxControl", $this->getParams(), true);
	}
	
	function printHtml($print=null){
		if($print)
			echo $this->html_text;
		else
			return $this->html_text;
	}
}