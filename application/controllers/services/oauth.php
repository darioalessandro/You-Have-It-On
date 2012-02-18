<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oauth extends Services{
	private $usr_id;
	
	function Oauth(){
		parent::__construct();
	}
	
	protected function register_response(){
		if(isset(self::$params['error'])){
			throw new UserException(self::$params['error']);
		}else{
			if(isset(self::$params['is_login'])){
				$fin_redirect = '';
				if(isset(self::$params['import']))
					$fin_redirect = '&import=yes';
					
				header('Location: '.$this->config->item('base_url').'services/user/sigin?usr_id='.self::$params['user_id'].
					'&token='.self::$params['oauth_token'].$fin_redirect);
				exit;
			}else{
				return $this->parseOutput(array(
						"user_id" => self::$params['user_id'],
						"token" => self::$params['oauth_token']
					)
				);
			}
		}
	}
	
	protected function register_user(){
		if(isset(self::$params['oauth_verifier'])){
			
			if(self::$params['oauth_verifier'] == 'denied'){
				$uri = (strpos(self::$params['redirect_uri'], '?'))?
		        		self::$params['redirect_uri'].'&':
		        		self::$params['redirect_uri'].'?';
				$uri .= 'consumer_key='.self::$params['consumer_key'].'&usr_id='.self::$params['user_id'].'&error=denied access';
				
				header('Location: '.$uri);
				exit;
			}else{
				OAuthRequester::requestAccessToken(self::$params['consumer_key'], self::$params['oauth_token'], self::$params['user_id']);
				
				$token = $this->config->item('store_oauth')->checkAccess(self::$params['consumer_key'], self::$params['user_id']);
				if(is_array($token)){
					$uri = (strpos(self::$params['redirect_uri'], '?'))?
				        		self::$params['redirect_uri'].'&':
				        		self::$params['redirect_uri'].'?';
					$uri .= 'oauth_token='.$token['oct_token'].'&consumer_key='.self::$params['consumer_key'].'&usr_id='.$token['oct_usa_id_ref'];
				}
				header('Location: '.$uri);
				exit;
			}
		}
		
		//REGISTRAR EL CONSUMER REGISTRY
		$check_consumer = $this->config->item('store_oauth')->checkConsumer(
								self::$params['consumer_key'],
								self::$params['consumer_secret'], 
								self::$params['user_id']
						);
		if(count($check_consumer)==0){
			//Configuracion del servidor
			$server = array(
			    'consumer_key' => self::$params['consumer_key'],
			    'consumer_secret' => self::$params['consumer_secret'],
			    'server_uri' => $this->config->item('base_url').$this->config->item('oa_server'), //'oauth/server',
			    'signature_methods' => array('HMAC-SHA1', 'PLAINTEXT'),
			    'request_token_uri' => $this->config->item('base_url').$this->config->item('oa_request_token'), //'oauth/server?action=request_token',
			    'authorize_uri' => $this->config->item('base_url').$this->config->item('oa_authorize'), //'oauth/server?action=authorize',
			    'access_token_uri' => $this->config->item('base_url').$this->config->item('oa_access_token'), //'oauth/server?action=access_token'
			);
			$consumer_key = $this->config->item('store_oauth')->updateServer($server, self::$params['user_id']);
		}
		//requestoken
		$token = OAuthRequester::requestRequestToken(
					self::$params['consumer_key'], 
					self::$params['user_id']
				);
		
		$fin_redirect = '';
		if(isset(self::$params['is_login'])){
			$fin_redirect = '&is_login=yes';
		}
		if(isset(self::$params['import'])){
			$fin_redirect .= '&import=yes';
		}
		$redirect_uri = urlencode($this->config->item('base_url').'services/oauth/register_response.json?user_id='.self::$params['user_id'].$fin_redirect);
		$callback_uri = $this->config->item('base_url').'services/oauth/register_user?consumer_key='.
								rawurlencode(self::$params['consumer_key']).'&user_id='.self::$params['user_id'].
								'&redirect_uri='.$redirect_uri;
		
	    $uri = $this->config->item('base_url')."services/oauth/authorize_user?".
	    			'oauth_token='.rawurlencode($token['token']).'&user_id='.self::$params['user_id'].
	    			'&oauth_callback='.rawurlencode($callback_uri);
	    
	    header('Location: '.$uri);
		exit;
	}
	
	protected function authorize_user(){
		//autoriza el token
		$this->config->item('server_oauth')->authorizeVerify();
		$this->config->item('server_oauth')->authorizeFinish(true, self::$params['user_id']);
	}
	
	
	
	/**
	 * Este metodo comprueba si ya se configuro un servidor para el usuario que esta logeado y la
	 * app que esta solicitando la peticion. Si no se ha registro se registra y redirecciona accessToken 
	 * @consumer_key. llave de la aplicación.
	 * @consumer_secret. password de la aplicación.
	 */
	protected function access(){
		$this->validateAccess();
		
		$check_consumer = $this->config->item('store_oauth')->checkConsumer(
								self::$params['consumer_key'],
								self::$params['consumer_secret'], 
								$this->usr_id
						);
		
		if(count($check_consumer)==0){
			//Configuracion del servidor
			$server = array(
			    'consumer_key' => self::$params['consumer_key'],
			    'consumer_secret' => self::$params['consumer_secret'],
			    'server_uri' => $this->config->item('base_url').$this->config->item('oa_server'), //'oauth/server',
			    'signature_methods' => array('HMAC-SHA1', 'PLAINTEXT'),
			    'request_token_uri' => $this->config->item('base_url').$this->config->item('oa_request_token'), //'oauth/server?action=request_token',
			    'authorize_uri' => $this->config->item('base_url').$this->config->item('oa_authorize'), //'oauth/server?action=authorize',
			    'access_token_uri' => $this->config->item('base_url').$this->config->item('oa_access_token'), //'oauth/server?action=access_token'
			);
			$consumer_key = $this->config->item('store_oauth')->updateServer($server, $this->usr_id);
		}
		$uri = $this->config->item('base_url')."services/oauth/accesstoken?consumer_key=".
					self::$params['consumer_key']."&redirect_uri=".
					urlencode(self::$params['redirect_uri']);
		header('Location: '.$uri);
	}
	
	/**
	 * Este método es el que determina que hacer, compurba si el usuario ya otorgo permisos a la aplicación,
	 * si ya se otorgo regresa el token del usuario, si no realiza el proceso de generación del token
	 */
	protected function accessToken(){
		
		if(isset(self::$params['oauth_verifier'])){
			$this->validateUsrId();
			if(self::$params['oauth_verifier'] == 'denied'){
				
				$uri = (strpos(self::$params['redirect_uri'], '?'))?
		        		self::$params['redirect_uri'].'&':
		        		self::$params['redirect_uri'].'?';
				$uri .= 'consumer_key='.self::$params['consumer_key'].'&usr_id='.$this->usr_id.'&error=denied access';
				
				header('Location: '.$uri);
				exit;
			}else
				OAuthRequester::requestAccessToken(self::$params['consumer_key'], self::$params['oauth_token'], $this->usr_id);
		}else 
			$this->validateAccessServer();
		
		$token = $this->config->item('store_oauth')->checkAccess(self::$params['consumer_key'], $this->usr_id);
		if(is_array($token)){
			$uri = (strpos(self::$params['redirect_uri'], '?'))?
		        		self::$params['redirect_uri'].'&':
		        		self::$params['redirect_uri'].'?';
			$uri .= 'oauth_token='.$token['oct_token'].'&consumer_key='.self::$params['consumer_key'].'&usr_id='.$token['oct_usa_id_ref'];
		}else{
			$uri = $this->config->item('base_url')."services/oauth/server?action=access_server&consumer_key=".
					self::$params['consumer_key']."&redirect_uri=".
					urlencode(self::$params['redirect_uri']);
		}
		header('Location: '.$uri);
	}
	
	/**
	 * Es el servidor del oauth, se encarga de realizar la acción correspondiente.
	 * access_server. Es el que accede al request_token (mediante curl), redirecciona a la autorizacion.  
	 * request_token. Genera un token de tipo request, en ese punto aun no sirve para acceder a la api
	 * access_token. Genera un token de acceso, este ta sirve para que se acceda a la api
	 * authorize. Pide autorización al usuario para que la aplicación acceda a sus datos
	 * @throws OAuthServerException
	 */
	protected function server(){
		if(!isset(self::$params['action']))
			throw new OAuthServerException(lang('txt_oaces_server_val', 'action'));
			
		switch(self::$params['action']){
			case 'access_server':
				$this->validateAccessServer();
				
				$token = OAuthRequester::requestRequestToken(
					self::$params['consumer_key'], 
					$this->usr_id
				);
				//http%3A%2F%2Flocalhost%2Flml%2Fservices%2Foauth%2Fcallback
				//http%3A%2F%2Fwww.pelimaniaco.com%2Flml%2Fservices%2Foauth%2Fcallback
				//$callback_uri = 'http://localhost/oauth-php/ejem/consumer/callback.php?consumer_key='.rawurlencode($_GET['consumer_key']).'&usr_id='.intval($user_id);
				$callback_uri = $this->config->item('base_url').'services/oauth/accesstoken?consumer_key='.
								rawurlencode(self::$params['consumer_key']).'&redirect_uri='.urlencode(self::$params['redirect_uri']);
				
				if(!empty($token['authorize_uri'])){
				    if(strpos($token['authorize_uri'], '?'))
				        $uri = $token['authorize_uri'] . '&';
				    else
				        $uri = $token['authorize_uri'] . '?';
				    $uri .= 'oauth_token='.rawurlencode($token['token']).'&oauth_callback='.rawurlencode($callback_uri);
				}else
				   $uri = $callback_uri . '&oauth_token='.rawurlencode($token['token']);
				   
				header('Location: '.$uri);
				exit;
			case 'request_token':
				$this->config->item('server_oauth')->requestToken();
				exit;
			case 'access_token':
				$this->config->item('server_oauth')->accessToken();
				exit;
			case 'authorize':
				try{
					$this->validateUsrId();
					
					if($_SERVER['REQUEST_METHOD'] == 'POST'){
						$authorized = (self::$params['authorized']==lang('txt_oallow'))? true: false;
						$this->config->item('server_oauth')->authorizeVerify();
						$this->config->item('server_oauth')->authorizeFinish($authorized, $this->usr_id);
					}else{
						$info_app = $this->config->item('store_oauth')->getServerRegistry(self::$params['oauth_token']);
						$paramas = array_merge($info_app, 
							array(
								'uri' => '&oauth_token='.self::$params['oauth_token'].
										'&oauth_callback='.urlencode(self::$params['oauth_callback'])
							)
						);
						$this->load->helper(array('form', 'url'));
						$this->load->view('oauth/authorize_app', $paramas);
					}
				}catch (OAuthException2 $e){
					header('HTTP/1.1 400 Bad Request');
					header('Content-Type: text/plain');
					
					echo "Failed OAuth Request: " . $e->getMessage();
				}
				break;
			default:
				header('HTTP/1.1 500 Internal Server Error');
				header('Content-Type: text/plain');
				echo "Unknown request";
		}
	}
	
	/**
	 * Registra una app, carga las vistas y modelos para realizar dicha tarea. 
	 */
	protected function registerApp(){
		$this->validateUsrId();
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('MY_Form_validation');
		
		$this->my_form_validation->setArrayValidate(self::$params);
		
		$this->my_form_validation->set_rules('application_title', lang('txt_app_title'), 'required');
		$this->my_form_validation->set_rules('application_type', lang('txt_app_type'), 'required');
		
		if(isset(self::$params['register'])){
			if($this->my_form_validation->run() == false){
				$this->load->view('oauth/register_app');
			}else{
				$this->load->model('user_model');
				$user = $this->user_model->getUser($this->usr_id);
				self::$params['requester_name'] = trim($user->name.' '.$user->last_name);
				self::$params['requester_email'] = $user->email;
				self::$params['callback_uri'] = '';
				
				//Registramos la aplicacion 
				$key = $this->config->item('store_oauth')->updateConsumer(self::$params, $this->usr_id);
				
				//Obtenemos los datos del consumidor registrado
				$consumer = $this->config->item('store_oauth')->getConsumer($key, $this->usr_id);
				
				$this->load->view('oauth/register_app_success', $consumer);
			}
		}else
			$this->load->view('oauth/register_app');
	}
	
	
	
	/**
	 * Valida los parámetros requeridos del server.
	 * @throws OAuthAccessServerException
	 */
	private function validateAccessServer(){
		$this->validateUsrId();
		if(!isset(self::$params['consumer_key']))
			throw new OAuthAccessServerException(lang('txt_oaces_server_val', 'consumer_key'));
		
		if(!isset(self::$params['redirect_uri']))
			throw new OAuthAccessServerException(lang('txt_oaces_server_val', 'redirect_uri'));
	}
	
	private function validateAccess(){
		$this->validateAccessServer();
		if(!isset(self::$params['consumer_secret']))
			throw new OAuthAccessServerException(lang('txt_oaces_server_val', 'consumer_secret'));
	}
	
	private function validateUsrId(){
		$this->load->library('utilities');
		
		if(!isset($_COOKIE['lml_usr_id'])){
			setcookie('lml_redirect_uri', $this->utilities->getUrl(), time()+1200, '/');
			header("Location: ".$this->config->item('base_url').'services/user/login?lang='.self::$params['lang']);
		}else
			$this->usr_id = intval($_COOKIE['lml_usr_id']);
		
		if(!isset($this->usr_id))
			throw new OAuthAccessServerException(lang('txt_oaces_server_val', 'usr_id'));
		else{
			if($this->usr_id <= 0)
				throw new OAuthAccessServerException(lang('txt_oval_invalid', 'usr_id'));
		}
	}
	
	
	
	private function parseOutput($data=array(), $only_parse=false){
		if ($only_parse)
			return $this->{'_format_'.$this->getFormat()}($data);
		
		return $this->{'_trim_format_'.$this->getFormat()}(
				$this->{'_format_'.$this->getFormat()}($data)
				);
    }
}
?>