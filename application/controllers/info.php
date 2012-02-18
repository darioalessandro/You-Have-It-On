<?php
class info extends CI_Controller{
	private $token = '';
	
	function __construct(){
		parent::__construct();
	}
	
	/*public function _remap($method){
		$this->token = Sys::getTokenVal();
		if($this->token == false){
			if($method != 'index'){
				setcookie('lml_goto', Sys::getCurrentUrl(), time()+1200, '/');
			}
			$this->phome();
		}else{
			if(isset($_COOKIE["lml_goto"])){ //redireccionar a alguna url despues de logearse
				$url = $_COOKIE["lml_goto"];
				setcookie('lml_goto', '', time()-1200, '/');
				header("Location: ".$url);
			}else if($method=="index")
		    	$this->verMuro();
		    else
		    	$this->{$method}();
		}
	}*/

	
	function terms(){		
		$this->load->library('user_agent');
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$this->load->model('user_model');
		
		$params = array(
			"consumer_secret"=>			Sys::$consumer_secret,
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token
		);
		
		$params["title"] = lang("terms_and_conditions")." - ".Sys::$title_web;
		
		echo $this->load->view("ui_controls/header_home", $params, true);
		echo $this->load->view("ui_controls/info/".Sys::getLang()."/terms", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	
	
	
	public function checkAccessToken(){
		$result = $this->config->item('store_oauth')->checkAccessToken(
					Sys::$consumer_key, 
					$this->token);
				
		if($result)
			return true;
		else 
			header("Location: ".$this->config->item('base_url')."services/user/logout?lang=".Sys::getLang());
	}
}
?>