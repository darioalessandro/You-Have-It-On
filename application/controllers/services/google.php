<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Google extends Services{
	private $status = true;
	
	function Google(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('user_agent');
	}
	
	protected function login(){
		$params = array('title_page' => lang('txt_login_with_google'), 
						'url_login' => 'google/sigin?lang='.self::$params['lang'].'&go', 
						'msg_buttom' => lang('txt_login_with_google'));
		if($this->agent->is_mobile())
			$this->load->view('services/google_mobile_login', $params);
		else
			$this->load->view('services/google_login', $params);
	}
	
	protected function sigin(){
		$openid = new openid();
		
		if(!$openid->mode){
			if(isset(self::$params['go'])){
	            $openid->identity = $openid->google_identity;
	            header('Location: ' . $openid->authUrl());
			}
		}elseif($openid->mode == 'cancel')
	        throw new UserCanceledAuthException();
	    else{
	        if($openid->validate()){
	        	$this->load->model('user_model');
	        	$params = $openid->getAttributes();
	        	$params['token'] = $openid->getIdSession($openid->identity);
	        	
	        	$usr_id = $this->user_model->existUser($params, 'google');
	        	$url = $this->config->item('base_url').'services/oauth/register_user?consumer_key='.
						Sys::$consumer_key.'&user_id='.$usr_id.
						'&consumer_secret='.Sys::$consumer_secret.'&is_login=yes';
				header("Location: ".$url);
				exit;
	        	/*if($usr_id){
	        		header('Location: '.$this->config->item('base_url').'services/user/sigin?usr_id='.$usr_id.'&google_token='.$params['token']);
	        	}*/
	        }
	    }
	}
	
	protected function checkSession(){
		
		return $this->parseOutput(array("autentificado" => true));
	}
	
	
	private function parseOutput($data=array()){
		if($this->status==true){
			return $this->{'_trim_format_'.$this->getFormat()}(
					$this->{'_format_'.$this->getFormat()}($data)
					);
		}
    }
}