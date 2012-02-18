<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdsControl extends controls{
	private $title			= array();
	private $url_img		= "";
	private $url_link		= "";
	private $text			= "";
	private $width_img		= 140;
	
	function AdsControl(){
		parent::controls();
	}
		
	private function getParams(){
		$data = array(
			"title"			=> $this->title,
			"url_img"		=> $this->url_img,
			"text"			=> $this->text,
			"width_img"		=> $this->width_img,
			"url_link"		=> $this->url_link
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}
		
		$this->html_text = $this->CI->load->view("ui_controls/AdsControl", $this->getParams(), true);
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