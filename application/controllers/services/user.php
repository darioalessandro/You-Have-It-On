<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Services{
	private $status = true;
	
	function User(){
		parent::__construct();
	}
	
	/**
	 * Servicio de login de usuario.
	 */
	protected function login_user(){
		$this->checkConsumerKey();
		
		$response_errors = $this->validate('login');
		if($this->status == true){
			$this->load->model('user_model');
			$res_save = $this->user_model->login(self::$params);
			if(is_array($res_save))
				return $this->parseOutput($res_save);
			else 
				throw new UserNotFoundException();
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	
	/**
	 * Servicio de registro de usuario.
	 */
	protected function register(){
		$response_errors = $this->validate('register');
		if($this->status == true){
			$this->load->model('user_model');
			$res_save = $this->user_model->saveInfoUser(self::$params);
			if($res_save==false)
				throw new UserAlreadyExistsException();
			else{
				$url = $this->config->item('base_url').'services/oauth/register_user?consumer_key='.
						self::$params['consumer_key'].'&user_id='.$res_save.
						'&consumer_secret='.self::$params['consumer_secret'];
				header("Location: ".$url);
				exit;
			}
			//return $this->parseOutput(array("user_id" => $res_save));
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que obtiene la informacion base del usuario que coincida con el 
	 * oauth_token y el consumer_key
	 */
	protected function query(){
		$this->checkAccessToken();
		$this->load->model('user_model');
		$data = $this->user_model->getInfoUser(self::$params);
		
		return $this->parseOutput($data);
	}
	
	/**
	 * Servicio que actualiza la información base del usuario que coincida con el 
	 * oauth_token y el consumer_key
	 */
	public function edit(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('edit');
		$aerrors = array();
		foreach ($response_errors as $key => $item){
			$aerrors[] = array(
				'code' => 300,
				'message' => $item,
				'field' => $key
			);
		}
		
		$this->load->model('user_model');
		$res = $this->user_model->getIdUserToken(self::$params);
		if($res->num_rows() > 0){
			//informacion del usuario
			$data = $res->row_array();
		
			if($this->status == true){
				$resp = $this->user_model->updateInfoUser(self::$params, $data['id']);
				if($resp==false)
					throw new StatesNotFoundException();
				else
					return $this->parseOutput(array('user_id' => $data['id']));
			}else{
				self::$response_error = true; //regresa el json comose envia de aqui
				return $this->parseOutput(
					array('status' => 
						array('code' => 300, 'messages' => $aerrors), 'user_id' => $data['id']
					)
				, true);
			}
		}
	}
	
	/**
	 * Servicio que regresa la lista de amigos del usuario especificado
	 * @throws UserNotFoundException
	 * @throws UserValidateRegisterException
	 */
	public function friends(){
		$this->checkAccessToken();
		$response_errors = $this->validate('friends');
		if($this->status == true){
			$this->load->model('user_model');
			$data = $this->user_model->getFriendsUser(self::$params);
			if($data==false)
				throw new UserNotFoundException();
			else
				return $this->parseOutput($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que envía solicitudes para agregar amigos y también responde dichas solicitudes
	 * aceptándolas o denegándolas
	 * @throws UserException
	 * @throws UserValidateRegisterException
	 */
	public function friend_invite(){
		$this->checkAccessToken();
		$response_errors = $this->validate('friend_invite');
		if($this->status == true){
			$this->load->model('user_model');
			if(self::$params['action']=='invite')
				$data = $this->user_model->inviteFriend(self::$params);
			else
				$data = $this->user_model->responseFriend(self::$params);
			
			$msg = '';
			if($data>0){
				switch ($data){
					case 1: $msg = 'txt_user_invited_success'; break;
					case 2: $msg = 'txt_user_invited_accept_success'; break;
					case 3: $msg = 'txt_user_invited_deny_success'; break;
				}
				self::$msg_response = $msg;
				return '';
			}else{
				switch ($data){
					case -1: $msg = lang('txt_field_not_found', 'user_id'); break;
					case -2: $msg = lang('txt_field_not_found', 'friend_id'); break;
					case -3: $msg = lang('txt_user_your_friend'); break;
					case -4: $msg = lang('txt_user_invitation_pending'); break;
					case -5: $msg = lang('txt_user_no_invitation'); break;
				}
				throw new UserException($msg);
			}
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que quita de la lista de amigos al usuario indicado
	 */
	public function friend_remove(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('friend_remove');
		if($this->status){
			$this->load->model('user_model');
			$res = $this->user_model->getIdUserToken(self::$params);
			if($res->num_rows() > 0){				
				$resp = $this->user_model->removeFriend(self::$params);
				self::$msg_response = 'text_successful_process';
				return '';
			}
		}else 
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	
	/**
	 * Servicio que actualiza la configuracion del usuario que hace la peticion 
	 * oauth_token y el consumer_key
	 */
	public function config(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('config');
		if($this->status){
			$this->load->model('user_model');
			$res = $this->user_model->getIdUserToken(self::$params);
			if($res->num_rows() > 0){
				//informacion del usuario
				$data = $res->row_array();
				self::$params['user_id'] = $data['id'];
				
				$resp = $this->user_model->configUser(self::$params);
				self::$msg_response = 'text_successful_process';
				return '';
			}
		}else 
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que obtiene la configuracion del usuario del 
	 * oauth_token y el consumer_key
	 */
	public function get_config(){
		$this->checkAccessToken();
		
		//informacion del usuario
		$this->load->model('user_model');
		$res = $this->user_model->getIdUserToken(self::$params);
		$data = $res->row_array();
		self::$params['user_id'] = $data['id'];
		
		$resp = $this->user_model->getConfigUser(self::$params);
		return $this->parseOutput($resp);
	}
	
	
	/**
	 * SERVICIOS DE LIKE
	 * Servicio que agrega y modifica los like y unlike de stores
	 * @throws UserException
	 * @throws UserValidateException
	 */
	public function like_store(){
		$this->checkAccessToken();
		
		self::$params['table_compare'] = 'store';
		self::$params['table'] = 'users_like_store';
		self::$params['field'] = 'store_id';
		
		$response_errors = $this->validate('like');
		if($this->status == true){
			$this->load->model('like_model');
			$data = $this->like_model->{self::$params['action']}(self::$params);
			
			$this->like_response($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que agrega y modifica los like y unlike de brand
	 * @throws UserException
	 * @throws UserValidateException
	 */
	public function like_nums(){
		$this->checkAccessToken();
		
		$this->load->model('like_model');
		$data = $this->like_model->num_likes(self::$params);
		
		return $this->parseOutput($data);
	}
	
	/**
	 * Servicio que agrega y modifica los like y unlike de brand
	 * @throws UserException
	 * @throws UserValidateException
	 */
	public function like_brand(){
		$this->checkAccessToken();
		
		self::$params['table_compare'] = 'brands';
		self::$params['table'] = 'users_like_brand';
		self::$params['field'] = 'brand_id';
		
		$response_errors = $this->validate('like');
		if($this->status == true){
			$this->load->model('like_model');
			$data = $this->like_model->{self::$params['action']}(self::$params);
			
			$this->like_response($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que agrega y modifica los like y unlike de items
	 * @throws UserException
	 * @throws UserValidateException
	 */
	public function like_items(){
		$this->checkAccessToken();
		
		self::$params['table_compare'] = 'items';
		self::$params['table'] = 'users_like_items';
		self::$params['field'] = 'item_id';
		
		$response_errors = $this->validate('like');
		if($this->status == true){
			$this->load->model('like_model');
			$data = $this->like_model->{self::$params['action']}(self::$params);
			
			$this->like_response($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que agrega y modifica los like y unlike de postyle
	 * @throws UserException
	 * @throws UserValidateException
	 */
	public function like_postyle(){
		$this->checkAccessToken();
		
		self::$params['table_compare'] = 'postyles';
		self::$params['table'] = 'users_like_postyle';
		self::$params['field'] = 'postyle_id';
		
		$response_errors = $this->validate('like');
		if($this->status == true){
			
			$this->load->model('like_model');
			$data = $this->like_model->{self::$params['action']}(self::$params);
			
			$this->like_response($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	private function like_response($data){
		$msg = '';
		if($data>0){
			if($data==1)
				self::$msg_response = 'txt_like_success';
			elseif($data==2)
				self::$msg_response = 'txt_unlike_success';
			else
				self::$msg_response = 'txt_neutro_success';
			return '';
		}else{
			switch ($data){
				case -1: $msg = lang('txt_field_not_found', 'user_id'); break;
				case -2: $msg = lang('txt_field_not_found', self::$params['field']); break;
				case -3: $msg = lang('txt_like_added'); break;
				case -4: $msg = lang('txt_like_not_exist'); break;
			}
			throw new UserException($msg);
		}
	}
	
	
	
	/**
     * Permite incorporar las validaciones bases que apliquen para todos los servicios.
     * Validaciones mas espesificas tendran que ser en cada servicio en particular.
     * @author gama
     */
    private function validate($val_method){
    	if(method_exists($this, 'validate_'.$val_method)){
    		$this->load->library('MY_Form_validation');
    		return $this->{'validate_'.$val_method}();
    	}
    }
    
	private function validate_login(){
    	//datos de prueba
    	//self::$params['email'] = 'gama@gmail.com';
    	//self::$params['password'] = '81dc9bdb52d04dc20036dbd8313ed055';
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'email', 
                     'label' => 'email', 
                     'rules' => 'required|valid_email'),
    			array('field' => 'password', 
                     'label' => 'password', 
                     'rules' => 'required|max_length[32]')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
	private function validate_like(){
    	//datos de prueba
    	/*self::$params['user_id'] = '2';
    	self::$params['postyle_id'] = '1';
    	self::$params['action'] = 'like';*/
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'user_id', 
                     'label' => 'user_id', 
                     'rules' => 'required|numeric'),
    			array('field' => self::$params['field'], 
                     'label' => self::$params['field'], 
                     'rules' => 'required|numeric'),
    			array('field' => 'action', 
                     'label' => 'action', 
                     'rules' => 'required|expression:/^(like|unlike|neutro)$/')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
	private function validate_friend_invite(){
    	//datos de prueba
    	/*self::$params['user_id'] = '4';
    	self::$params['friend_id'] = '2';
    	self::$params['action'] = 'response';
    	self::$params['action_response'] = 'deny';*/
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$rules = array(
    			array('field' => 'user_id', 
                     'label' => 'user_id', 
                     'rules' => 'required|numeric'),
    			array('field' => 'friend_id', 
                     'label' => 'friend_id', 
                     'rules' => 'required|numeric'),
    			array('field' => 'action', 
                     'label' => 'action', 
                     'rules' => 'required|expression:/^(response|invite)$/')
    	);
    	if(isset(self::$params['action']))
    		if(self::$params['action']=='response')
    		$rules[] = array('field' => 'action_response', 
                     'label' => 'action_response', 
                     'rules' => 'required|expression:/^(accept|deny)$/');
    	
    	$validate->set_rules($rules);
    	
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
    private function validate_friends(){
    	//datos de prueba
    	//self::$params['user_id'] = '2';
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'user_id', 
                     'label' => lang('txt_user_id'), 
                     'rules' => 'required|numeric'),
    			array('field' => 'pending', 
                     'label' => 'pending', 
                     'rules' => 'expression:/^(0|1)$/'),
    			array('field' => 'all', 
                     'label' => 'all', 
                     'rules' => 'expression:/^(0|1)$/'),
    			array('field' => 'filter_name', 
                     'label' => 'filter_name', 
                     'rules' => 'max_length[100]')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
	private function validate_friend_remove(){
    	//datos de prueba
    	/*self::$params['user_id'] = '2';
    	self::$params['friend_id'] = '11';*/
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$rules = array(
    			array('field' => 'user_id', 
                     'label' => 'user_id', 
                     'rules' => 'required|numeric'),
    			array('field' => 'friend_id', 
                     'label' => 'friend_id', 
                     'rules' => 'required|numeric')
    	);
    	$validate->set_rules($rules);
    	
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
    private function validate_edit(){
    	//datos de prueba
    	/*self::$params['name'] = 'gamaliel';
    	self::$params['last_name'] = 'mendoza';
    	self::$params['sex'] = 'm';
    	self::$params['email'] = 'gama@gmail.com';
    	self::$params['password'] = md5('1234');
    	self::$params['birthday'] = '23423423';
    	self::$params['state'] = 197;*/
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'name', 
                     'label' => lang('txt_name'), 
                     'rules' => 'max_length[100]'),
    			array('field' => 'last_name', 
                     'label' => lang('txt_last_name'), 
                     'rules' => 'max_length[100]'),
    			array('field' => 'sex', 
                     'label' => lang('txt_sex'), 
                     'rules' => 'exact_length[1]|expression:/^(f|m)$/'),
    			array('field' => 'email', 
                     'label' => lang('txt_email'), 
                     'rules' => 'valid_email|max_length[100]'),
    			array('field' => 'password', 
                     'label' => lang('txt_password'), 
                     'rules' => 'max_length[32]'),
    			array('field' => 'birthday', 
                     'label' => lang('txt_birthday'), 
                     'rules' => 'numeric'),
    			array('field' => 'state', 
                     'label' => lang('txt_state'), 
                     'rules' => 'numeric')
    	));
    	$this->status = $validate->run();
    	if(isset(self::$params['birthday'])){
    		$fecha = new Fecha();
    		if(self::$params['birthday'] >= $fecha->getTimeStamp()){
    			$this->status = false;
    		}
    	}
    	
    	return $validate->_error_array;
    }
    
    private function validate_register(){
    	//datos de prueba
    	/*self::$params['sex'] = 'm';
    	self::$params['email'] = 'calis4@gmail.com';
    	self::$params['password'] = md5('1234');*/
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'name', 
                     'label' => 'name', 
                     'rules' => 'required|max_length[100]'),
    			array('field' => 'sex', 
                     'label' => lang('txt_sex'), 
                     'rules' => 'required|exact_length[1]|expression:/^(f|m)$/'),
    			array('field' => 'email', 
                     'label' => lang('txt_email'), 
                     'rules' => 'required|valid_email|max_length[100]'),
    			array('field' => 'password', 
                     'label' => lang('txt_password'), 
                     'rules' => 'required|min_length[6]|max_length[32]'),
    			array('field' => 'confirm_password', 
                     'label' => 'confirm_password', 
                     'rules' => 'max_length[32]'),
    			array('field' => 'consumer_key', 
                     'label' => 'consumer_key', 
                     'rules' => 'required'),
    			array('field' => 'consumer_secret', 
                     'label' => 'consumer_secret', 
                     'rules' => 'required')
    	));
    	$this->status = $validate->run();
    	
    	if(isset(self::$params["confirm_password"])){
    		if(self::$params["confirm_password"] != self::$params["password"]){
    			$this->status = false;
    			$validate->_error_array['values'] = lang('txt_password_confirm');
    		}
    	}
    	
    	if($this->status == true){
    		if(self::$params['sex']=='f' || self::$params['sex']=='m')
    			$this->status = true;
    		else{
    			$this->status = false;
    		}
    	}
    	return $validate->_error_array;
    }
    
	private function validate_config(){
    	//datos de prueba
    	//self::$params['config_id'] = array(1,2,3,4);
    	//self::$params['values'] = array('spa', '1', '1', '1');
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);

    	
    	$this->status = $this->validate_id_value($validate);
    	if($this->status == false)
    		$validate->_error_array['values'] = lang('txt_uconf_value_error');
    	
    	return $validate->_error_array;
    }
    
	/**
     * Valida los id de configuracion y los valores
     * @param ref $validate
     */
    private function validate_id_value(&$validate){
    	$this->load->database();
    	$bad = true;
    	if(isset(self::$params['config_id'])){
    		if(is_array(self::$params['config_id']) && count(self::$params['config_id']) > 0){
		    	foreach(self::$params['config_id'] as $key => $id_val){
		    		if(!isset(self::$params['values'][$key])){
		    			$bad = false;
		    			break;
		    		}
		    		
		    		if($validate->expression($id_val, '/^(\d+)$/') == false)
		    			$bad = false;
		    		
		    		$len = strlen(self::$params['values'][$key]);
		    		if($len < 1 || $len > 30)
		    			$bad = false;
		    		
		    		if($bad){
			    		$res = $this->db->query("SELECT * FROM config WHERE id = ".$id_val);
		    			if($res->num_rows() > 0){
		    				$data = $res->row_array(); //falta validar lo de unique
		    				if($validate->expression(self::$params['values'][$key], $data['validation']) == false)
		    					$bad = false;
		    				else
		    					self::$params['unique'][$key] = $data['unique'];
		    			}else{ 
		    				unset(self::$params['config_id'][$key]);
		    				unset(self::$params['values'][$key]);
		    			}
		    			$res->free_result();
		    		}
		    		
		    		if($bad==false)
		    			break;
		    	}
    		}else 
    			$bad = false;
    	}else 
    		$bad = false;
    	return $bad;
    }
    
    
    
    
    
	/**
	 * 
	 * Para el acceso a los datos
	 */
	protected function login(){
		if(!isset($_COOKIE['lml_usr_id'])){
			$this->load->helper(array('form', 'url'));
			$this->load->library('MY_Form_validation');
			$this->my_form_validation->setArrayValidate(self::$params);
			
			$this->my_form_validation->set_rules('email', lang('txt_login_email'), 'required|valid_email');
			$this->my_form_validation->set_rules('password', lang('txt_login_password'), 'required');
			
			if(isset(self::$params['login'])){
				if($this->my_form_validation->run() == false){
					//$this->load->view('services/login', array('lang' => self::$params['lang']));
					header("Location: ".$this->config->item('base_url').'?lang='.self::$params['lang']);
				}else{
					$this->load->model('user_model');
					//$usr_id = $this->user_model->loginUser(self::$params);
					self::$params["consumer_key"] = Sys::$consumer_key;
					self::$params["password"] = md5(self::$params["password"]);
					$usr_id = $this->user_model->login(self::$params);
					if(is_array($usr_id))
						header("Location: ".$this->config->item('base_url').'services/user/sigin?token='.$usr_id["token"].'&usr_id='.$usr_id["user_id"].'&lang='.self::$params['lang']);
					else{
						/*$this->my_form_validation->_error_array['usuario'] = lang('txt_user_not_found');
						$this->load->view('services/login', array('lang' => self::$params['lang']));*/
						header("Location: ".$this->config->item('base_url').'?lang='.self::$params['lang']);
					}
				}
			}else
				$this->load->view('services/login', array('lang' => self::$params['lang']));
		}else{
			if(isset($_COOKIE['lml_redirect_uri'])){
				$url = $_COOKIE['lml_redirect_uri'];
				setcookie('lml_redirect_uri', '', time()-1200, '/');
				header("Location: ".$url);
			}
			$this->load->view('services/logout', array('lang' => self::$params['lang']));
		}
	}
	
	protected function logout(){
		setcookie('lml_usr_id', '', time()-(3600*24*Sys::$destroy_session), '/');
		setcookie('lml_usr_token', '', time()-(3600*24*Sys::$destroy_session), '/');
		header("Location: ".$this->config->item('base_url').'?lang='.self::$params['lang']);
	}
	
	protected function sigin(){
		if(isset(self::$params['usr_id'])){
			setcookie('lml_usr_id', self::$params['usr_id'], time()+(3600*24*Sys::$destroy_session), '/');
		}
		if(isset(self::$params['token'])){
			setcookie('lml_usr_token', self::$params['token'], time()+(3600*24*Sys::$destroy_session), '/');
		}
		setcookie('lml_lang', Sys::getLang(), time()+(3600*24*Sys::$destroy_session), '/');
		
		$fin_redirect = '';
		if(isset(self::$params['import']))
			$fin_redirect = '&import=yes';
		header("Location: ".$this->config->item('base_url').'?lang='.Sys::getLang().$fin_redirect);
	}
	
	
	private function parseOutput($data=array(), $only_parse=false){
		if ($only_parse)
			return $this->{'_format_'.$this->getFormat()}($data);
		if($this->status==true){
			return $this->{'_trim_format_'.$this->getFormat()}(
					$this->{'_format_'.$this->getFormat()}($data)
					);
		}
    }
}