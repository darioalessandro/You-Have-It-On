<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Facebook extends Services{
	private $CI;
	private $status = true;
	private $app_id;
	private $app_secret;
	private $redirect_url = "";
	private $permissions = 'offline_access,user_photos,publish_stream,user_hometown,user_about_me,email,read_friendlists';
	
	function Facebook(){
		parent::__construct();
		$this->load->helper('url');
		$this->redirect_url = $this->config->item('base_url').'services/facebook/sigin';
		$this->load->library('user_agent');
		$this->load->database();
		
		$this->app_id = Sys::$facebook_app_id;
		$this->app_secret = Sys::$facebook_app_secret;
	}
	
	protected function login(){
		/*$params = array('app_id' => $this->app_id,
						'title_page' => lang('txt_login_with_facebook'), 
						'url_login' => 'facebook/sigin?lang='.self::$params['lang'].'&go');
		
	    $my_url = $this->config->item('base_url').'facebook/sigin?lang=eng';*/
		
	    if(empty($_REQUEST["code"])) {
	    	$dialog_url = ($this->agent->is_mobile())?
	        	"http://m.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($this->redirect_url)."&scope=".$this->permissions:
	    		"http://www.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($this->redirect_url)."&scope=".$this->permissions;
	        	
			header("Location: ".$dialog_url);
	    }
	}
	
	protected function sigin(){
		if(empty(self::$params["code"])) {
	        $dialog_url = ($this->agent->is_mobile())?
	        	"http://m.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($this->redirect_url)."&scope=".$this->permissions:
	    		"http://www.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($this->redirect_url)."&scope=".$this->permissions;
			header("Location: ".$dialog_url);
	    }elseif(!isset(self::$params['error_reason'])){
			$token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$this->app_id."&redirect_uri=".
			 				$this->redirect_url."&client_secret=".$this->app_secret."&code=".self::$params['code'];
        	$access_token = file_get_contents($token_url);
        	
		    $graph_url = "https://graph.facebook.com/me?".$access_token;
		    
		    $user = (array)json_decode(file_get_contents($graph_url));
		    $user['token'] = $access_token;
		    
		    $this->load->model('user_model');
		    $usr = $this->user_model->existUser($user, 'facebook', true, true);
		    $import = $usr['action']=='save'? '&import=yes': '';
		    $url = $this->config->item('base_url').'services/oauth/register_user?consumer_key='.
					Sys::$consumer_key.'&user_id='.$usr['user_id'].
					'&consumer_secret='.Sys::$consumer_secret.'&is_login=yes'.$import;
			header("Location: ".$url);
			exit;
	    	/*if($usr_id){
        		header('Location: '.$this->config->item('base_url').'services/user/sigin?usr_id='.$usr_id.'&facebook_token='.$user['token']);
        	}*/
		}else 
			echo self::$params['error_description'];
		
	}

	/**
	 * Importar los contactos de facebook de cada usuaior
	 * @param unknown_type $user_id
	 */
	public function importFriends($user_id=null){
		$this->checkAccessToken();
		
		//informacion del usuario
		$this->load->model('user_model');
		if($user_id == null){
			$res = $this->user_model->getIdUserToken(self::$params);
			$data = $res->row_array();
			self::$params['user_id'] = $data['id'];
		}else
			self::$params['user_id'] = $user_id;
		
		//Obtenemos los amigos de facebook y buscamos en el sistema si hay amigos de facebook 
		//agregamos la amistad a la braba
		$this->load->library('my_facebook');
		$friends = $this->my_facebook->getListFrieds(self::$params['user_id']);
		$param = array(
			'fb_friends' => $friends,
			'user_id' => self::$params['user_id']
		);
		$this->user_model->joinFriendFb($param);
		
		self::$msg_response = 'text_successful_process';
		return '';
	}
	
	/**
	 * Sincroniza los contactos de facebook con los del sistema para cada usuario
	 */
	public function importFriendsAllUser(){
		$this->checkAccessToken();
		self::$params['result_items_per_page'] = isset(self::$params['result_items_per_page'])? self::$params['result_items_per_page']: 5;
		self::$params['result_page'] = isset(self::$params['result_page'])? self::$params['result_page']: 1;
		$pag = self::$params['result_page'];
		
		$query = Sys::pagination("SELECT id FROM users WHERE status = 1 AND `fb_user_id` IS NOT NULL", self::$params, true);
		
		$res = $this->db->query($query['query']);
		foreach ($res->result_array() as $row){
			$this->importFriends($row['id']);
		}
		
		//self::$msg_response = 'text_successful_process';
		return $this->parseOutput(array('resultado' => 'Se sincronizaron '.self::$params['result_items_per_page'].' usuarios de '.
			$query['total_rows'].' (pagina '.$pag.')'));
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
    
	private function validate_importFriends(){
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
                     'rules' => 'expression:/^(0|1)$/')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
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
	
	
	
	
	
	
	/**
	 * SERVICIOS QUE PERMITEN PUBLICAR LAS COSAS EN FACEBOOK
	 * Se autentifica en facebook
	 * @param string $redirect_url
	 */
	protected function authFacebook($redirect_url){
		if(empty(self::$params["code"])) {
	        $dialog_url = ($this->agent->is_mobile())?
	        	"http://m.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($redirect_url)."&scope=".$this->permissions:
	    		"http://www.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($redirect_url)."&scope=".$this->permissions;
			header("Location: ".$dialog_url);
			exit;
	    }
	    return true;
	}
	
	/**
	 * Obtiene un accessToken de facebook
	 * @param string $redirect_url
	 */
	protected function getToken($redirect_url, $trim=false){
		$token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$this->app_id."&redirect_uri=".
			 				$redirect_url."&client_secret=".$this->app_secret."&code=".self::$params['code'];
        $access_token = file_get_contents($token_url);
        
        if($trim)
        	$access_token = explode("&", str_replace("access_token=", "", $access_token));
        
		return $access_token;
	}
	
	/**
	 * Publica un postyle en facebook
	 * @param entero $postyle_id
	 */
	public function postyle($postyle_id=0, $user_id){
		$postyle_id = ($postyle_id>0? $postyle_id: self::$params['postyle_id']);
		/*$redirect_url = $this->config->item('base_url').'services/facebook/postyle?postyle_id='.$postyle_id;
		
		if($this->authFacebook($redirect_url) == true){
			$access_token = $this->getToken($redirect_url, true);
			var_dump($access_token);*/
		
		$res_usr = $this->db->query("SELECT * FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = str_replace('access_token=', '', $data_usr->token_facebook);
			
			//obtenemos los datos del postyle
			$res = $this->db->query("SELECT p.id, p.description, i.file_name, IFNULL(o.name, '') AS name 
				FROM postyles AS p INNER JOIN postyle_imgs AS pi ON p.id = pi.postyle_id INNER JOIN images AS i ON i.id = pi.image_id 
				LEFT JOIN ocation AS o ON o.id = p.ocation_id WHERE p.id = ".$postyle_id);
			if($res->num_rows() > 0){
				$data = $res->row();
				
				$graph_url = 'https://graph.facebook.com/me/feed';
				// ARRAY CON LA INFORMACION DEL postyle QUE SE ENVIARA A FB
				$data_foto = array(
					'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
					'message' 		=> '',
					'picture' 		=> $urlImagen = UploadFile::urlBig().$data->file_name,		// IMAGEN DEL postyle
					'link' 			=> Sys::lml_get_url("postyle", $data->id),
					'name' 			=> $data->name,
					'caption' 		=> '',
					'description' 	=> $data->description
				);
				
				var_dump($this->curlExec($graph_url, $data_foto));
				
				/*$graph_url = 'https://graph.facebook.com/me/photos';
				
				//obtenemos las tags
				$res = $this->db->query("SELECT id, description, x, y FROM postyle_tags WHERE postyle_id = ".$postyle_id);
				$tags = '';
				foreach($res->result() as $item){
					$tags .= ',{"tag_uid":"","tag_text":"'.$item->description.'","x":'.$item->x.',"y":'.$item->y.'}';
				}
				$tags = '['.substr($tags, 1, strlen($tags)).']';
				
				//Preparamos la imagen para enviarla a fb
				$urlImagen = UploadFile::urlBig().$data->file_name;
				$imagenTmp = tempnam('.', 'xhttp-tmp-'); // CREA UNA RUTA TEMPORAL PARA LA IMAGEN
				file_put_contents($imagenTmp, file_get_contents($urlImagen)); //crea la imagen temporal
				$img = '@'.$imagenTmp;
				
				// ARRAY CON LA INFORMACION DEL postyle QUE SE ENVIARA A FB
				$data_foto = array(
					'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
					'message' 		=> $data->description,
					'source' 		=> $img, 					// IMAGEN DEL postyle
					'tags' 			=> $tags
				);
				
				var_dump($this->curlExec($graph_url, $data_foto));
				
				unlink(str_replace('@', '', $img)); //eliminamos la imagen temporal*/
			}
		}
	}
	
	private function curlExec($graph_url, $data){
		$handle = curl_init($graph_url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		
		$respuesta = json_decode(curl_exec($handle)); // OBTIENE EL RESULTADO DEL ENVIO DE DATOS DEL EVENTO
		
		curl_close($handle);
		
		return $respuesta;
	}
}