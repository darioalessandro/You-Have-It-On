<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Twitter extends Services{
	private $CI;
	private $app_id;
	private $app_secret;
	private $redirect_url = "";
	
	function Twitter(){
		parent::__construct();
		$this->load->helper('url');
		$this->redirect_url = $this->config->item('base_url').'services/twitter/sigin';
		$this->load->library('user_agent');
		$this->load->database();
		
		$this->app_id = Sys::$twitter_app_id;
		$this->app_secret = Sys::$twitter_app_secret;
	}
	
	protected function sigin(){
		if(isset(self::$params['user_id'])){
			if(is_numeric(self::$params['user_id'])){
				$twitter = new tmhoauth(array(
					'consumer_key'    => $this->app_id,
					'consumer_secret' => $this->app_secret
				));
				
				if(isset(self::$params['oauth_verifier'])){
					$twitter->config['user_token']  = $_COOKIE['lml_tw_token'];
					$twitter->config['user_secret'] = $_COOKIE['lml_tw_token_secret'];
		  
					$twitter->request('POST', $twitter->url('oauth/access_token', ''), array(
						'oauth_verifier' => self::$params['oauth_verifier']
					));
					
					$access_token = $twitter->extract_params($twitter->response['response']);
					$this->load->model('user_model');
				    $this->user_model->joinTwitter(self::$params['user_id'], $access_token);
					
					setcookie('lml_tw_token', '', time()-1200, '/');
					setcookie('lml_tw_token_secret', '', time()-1200, '/');
					header("Location: ".$this->config->item('base_url').'services/user/login?lang='.self::$params['lang']);
				}else{
					$code = $twitter->request('POST', $twitter->url('oauth/request_token', ''), array(
						'oauth_callback' => $this->redirect_url.'?user_id='.self::$params['user_id'].'&lang='.self::$params['lang']
					));
					
					if($code == 200){
						$tw_oauth = $twitter->extract_params($twitter->response['response']);
						setcookie('lml_tw_token', $tw_oauth['oauth_token'], time()+1200, '/');
						setcookie('lml_tw_token_secret', $tw_oauth['oauth_token_secret'], time()+1200, '/');
						$method = 'authenticate';
						$force  = ''; //isset($_REQUEST['force']) ? '&force_login=1' : '';
						$forcewrite = ''; //isset($_REQUEST['force_write']) ? '&oauth_access_type=write' : '';
						$forceread  = ''; //isset($_REQUEST['force_read']) ? '&oauth_access_type=read' : '';
						header("Location: ".$twitter->url("oauth/{$method}", '').
											"?oauth_token={$tw_oauth['oauth_token']}{$force}{$forcewrite}{$forceread}");
					}else{
						// error
						$twitter->pr(htmlentities($twitter->response['response']));
					}
				}
			}else 
				echo lang('txt_user_id_req');
		}else 
			echo lang('txt_user_id_req');
	}
}