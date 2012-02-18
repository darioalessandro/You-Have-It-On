<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MiniItemControl extends controls{
	private $params			= array();
	private $items 			= array();
	private $width_control	= 530;
	
	function MiniItemControl(){
		parent::controls();
	}
	
	private function getParams(){
		$data = array(
			"params"		=> $this->params,
			"items"			=> $this->items,
			"width_control"	=> $this->width_control
		);
		return $data;
	}
	
	function ini($params=null){
		if(is_array($params)){
			foreach($params as $key => $param)
				$this->{$key} = $param;
		}
		
		$this->html_text = $this->CI->load->view("ui_controls/MiniItemControl", $this->getParams(), true);
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