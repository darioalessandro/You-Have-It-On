<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends Services{
	private $status = true;
	
	function States(){
		parent::__construct();
	}
	
	/*Metodo que carga imagenes de google map a las tiendas
	public function cargaMapa(){
		$this->load->model('store_model');
		$data = $this->store_model->cargaMapa();
		
		self::$msg_response = 'text_successful_process';
		return '';
	}*/
	
	/**
	 * Servicio que agrega tiendas a la base de datos. permite agregar imagenes a la tienda (opcional)
	 * Permite agregar horarios a la tienda (opcional)
	 * @throws UserValidateException
	 */
	public function add(){
		$this->checkAccessToken();
		
		//if(isset($_POST['enviar'])){
			$response_errors = $this->validate('add');
			if($this->status == true){
				//si llegan archivos configuramos la libreria
				$config = $this->configImg();
				
				//si hay archivos validos para subir ejecutamos la libreria de subir
				$images = array();
				if(count($config)>0){
					$this->load->library('my_upload', $config);
					$images = $this->my_upload->upload();
				}
				
				//obtiene datos de un usario del token
				$this->load->model('user_model');
				$res_usr = $this->user_model->getIdUserToken(self::$params);
				$data_usr = $res_usr->row_array();
				self::$params['user_id'] = $data_usr['id'];
				
				$this->load->model('store_model');
				$data = $this->store_model->saveStore(self::$params, $images);
				
				return $this->parseOutput($data);
			}else
				throw new UserValidateException(implode(',',$response_errors));
		//}
			
		//$this->load->helper(array('form', 'url'));
		//$this->load->view('store_form');
	}
	
	
	/**
	 * Servicio que modifica la informacion de una tienda. Las imagenes son opcionales, pero si hay, las sube
	 * y las agrega a la bd, al igual que los horarios
	 */
	public function edit(){
		$this->checkAccessToken();
		
		//if(isset($_POST['enviar'])){
			$response_errors = $this->validate('edit');
			if($this->status == true){
				//si llegan archivos configuramos la libreria
				$config = $this->configImg();
				
				//si hay archivos validos para subir ejecutamos la libreria de subir
				$images = array();
				if(count($config)>0){
					$this->load->library('my_upload', $config);
					$images = $this->my_upload->upload();
				}
				
				//obtiene datos de un usario del token
				$this->load->model('user_model');
				$res_usr = $this->user_model->getIdUserToken(self::$params);
				$data_usr = $res_usr->row_array();
				self::$params['user_id'] = $data_usr['id'];
				
				$this->load->model('store_model');
				$data = $this->store_model->updateStore(self::$params, $images);
				if($data == -1)
					throw new NotHavePermissionException();
				elseif($data == -2)
					throw new StoreNotFoundException();
				else
					return $this->parseOutput($data);
			}else
				throw new UserValidateException(implode(',',$response_errors));
		//}
			
		//$this->load->helper(array('form', 'url'));
		//$this->load->view('store_form');
	
	}
	
	/**
	 * Servicio que regresa un listado de tiendas, paginadas y se pueden espesificar mas filtros
	 * para obtener tiendas espesificas
	 * @throws NoResultsFoundException
	 * @throws UserValidateException
	 */
	public function query(){
		//$this->checkAccessToken();
		$this->checkConsumerKey();
		
		$response_errors = $this->validate('query');
		if($this->status == true){
			self::$params['token_user_id'] = '0';
			if(isset(self::$params["oauth_token"])){
				if(self::$params["oauth_token"] != ''){
					//obtiene datos de un usario del token
					$this->load->model('user_model');
					$res_usr = $this->user_model->getIdUserToken(self::$params);
					$data_usr = $res_usr->row_array();
					self::$params['token_user_id'] = $data_usr['id'];
				}
			}
			
			
			$this->load->model('store_model');
			$data = $this->store_model->getStores(self::$params);
			if($data==false)
				throw new NoResultsFoundException();
			else
				return $this->parseOutput($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	
	}
	
	/**
	 * Servicio que elimina (desactiva) imagenes de un brand
	 * @throws ImageNotFoundException
	 * @throws UserValidateException
	 */
	public function remove_image(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('remove_image');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['user_id'] = $data_usr['id'];
				
			$this->load->model('store_model');
			$data = $this->store_model->removeImage(self::$params);
			
			if($data===false)
				throw new ImageNotFoundException();
			elseif($data === -1)
				throw new NotHavePermissionException();
			elseif($data == 1)
				self::$msg_response = 'txt_enabled_success';
			else
				self::$msg_response = 'txt_disabled_success';
			return '';
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	
	
	/**
	 * SERVICIOS DE COMMENT
	 * Servicio que agrega un comentario a brand
	 * @throws ImageNotFoundException
	 * @throws UserValidateException
	 */
	public function comment(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('comment');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['user_id'] = $data_usr['id'];
				
			$this->load->model('store_model');
			$data = $this->store_model->comment(self::$params);
			
			if($data===false)
				throw new StoreNotFoundException();
			
			self::$msg_response = 'text_successful_process';
			return '';
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que habilita o deshabilita comentarios de brand
	 * @throws PostyleNotFoundException
	 * @throws NotHavePermissionException
	 * @throws UserValidateException
	 */
	public function disable_comment(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('disable_comment');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['user_id'] = $data_usr['id'];
				
			$this->load->model('store_model');
			$data = $this->store_model->disable_comment(self::$params);
			
			if($data===false)
				throw new CommentNotFoundException();
			elseif($data === -1)
				throw new NotHavePermissionException();
			elseif($data == 1)
				self::$msg_response = 'txt_enabled_success';
			else
				self::$msg_response = 'txt_disabled_success';
			return '';
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que regresa el listado de comentarios de un brand
	 * @throws UserValidateException
	 */
	public function get_comments(){
		$this->checkConsumerKey();
		
		$response_errors = $this->validate('get_comment');
		if($this->status == true){
			$this->load->model('store_model');
			$data = $this->store_model->get_comments(self::$params);
			
			return $this->parseOutput($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
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
    
	private function validate_add(){
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'name', 
                     'label' => 'name', 
                     'rules' => 'required|max_length[100]'),
    			array('field' => 'description', 
                     'label' => 'description', 
                     'rules' => 'max_length[5000]'),
    			array('field' => 'lat', 
                     'label' => 'lat', 
                     'rules' => 'required|numeric'),
    			array('field' => 'lon', 
                     'label' => 'lon', 
                     'rules' => 'required|numeric'),
    			array('field' => 'store_id', 
                     'label' => 'store_id', 
                     'rules' => 'is_natural'),
    			array('field' => 'location_description', 
                     'label' => 'location_description', 
                     'rules' => 'max_length[200]'),
    			array('field' => 'location_street_name', 
                     'label' => 'location_street_name', 
                     'rules' => 'max_length[200]'),
    			array('field' => 'location_city', 
                     'label' => 'location_city', 
                     'rules' => 'max_length[60]'),
    			array('field' => 'location_state', 
                     'label' => 'location_state', 
                     'rules' => 'max_length[60]'),
    			array('field' => 'location_postal_code', 
                     'label' => 'location_postal_code', 
                     'rules' => 'max_length[20]'),
    			array('field' => 'location_country', 
                     'label' => 'location_country', 
                     'rules' => 'max_length[60]'),
    			array('field' => 'brand_id', 
                     'label' => 'brand_id', 
                     'rules' => 'is_natural|numeric')
    	));
    	$this->status = $validate->run();
    	
		$bad = $this->validate_schedules($validate);
		if($bad==false){
    		$validate->_error_array['horary'] = lang('txt_horary_error');
    		$this->status = false;
    	}
    	
		$bad = $this->validate_vectors($validate);
		if($bad!==true){
    		$validate->_error_array['attr_val'] = lang('txt_vectors_error', $bad);
    		$this->status = false;
    	}
    	
    	return $validate->_error_array;
    }
    
	private function validate_edit(){
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'store_id', 
                     'label' => 'store_id', 
                     'rules' => 'required|is_natural'),
    			array('field' => 'name', 
                     'label' => 'name', 
                     'rules' => 'max_length[100]'),
    			array('field' => 'description', 
                     'label' => 'description', 
                     'rules' => 'max_length[5000]'),
    			array('field' => 'lat', 
                     'label' => 'lat', 
                     'rules' => 'numeric'),
    			array('field' => 'lon', 
                     'label' => 'lon', 
                     'rules' => 'numeric'),
    			array('field' => 'principal', 
                     'label' => 'principal', 
                     'rules' => 'expression:/^(0|1)$/'),
    			array('field' => 'location_description', 
                     'label' => 'location_description', 
                     'rules' => 'max_length[200]'),
    			array('field' => 'location_street_name', 
                     'label' => 'location_street_name', 
                     'rules' => 'max_length[200]'),
    			array('field' => 'location_city', 
                     'label' => 'location_city', 
                     'rules' => 'max_length[60]'),
    			array('field' => 'location_state', 
                     'label' => 'location_state', 
                     'rules' => 'max_length[60]'),
    			array('field' => 'location_postal_code', 
                     'label' => 'location_postal_code', 
                     'rules' => 'max_length[20]'),
    			array('field' => 'location_country', 
                     'label' => 'location_country', 
                     'rules' => 'max_length[60]'),
    			array('field' => 'brand_id', 
                     'label' => 'brand_id', 
                     'rules' => 'is_natural|numeric')
    	));
    	$this->status = $validate->run();
    	
    	$bad = $this->validate_schedules($validate, true);
		if($bad==false){
    		$validate->_error_array['horary'] = lang('txt_horary_error');
    		$this->status = false;
    	}
    	
		$bad = $this->validate_vectors($validate);
		if($bad!==true){
    		$validate->_error_array['attr_val'] = lang('txt_vectors_error', $bad);
    		$this->status = false;
    	}
    	
    	return $validate->_error_array;
    }
    
	/**
     * Valida los vectores website, phone_number.
     * @param ref $validate
     */
    private function validate_vectors(&$validate, $edit=false){
    	$bad = true;
    	    	
    	if(isset(self::$params['website'])){
    		if(is_array(self::$params['website']) && count(self::$params['website']) > 0){
		    	foreach(self::$params['website'] as $key => $value){
		    		if(strlen($value) > 130 || strlen($value) < 1){
		    			$bad = 'website';
		    			break;
		    		}
		    	}
    		}
    	}
    	if($bad !== true)
    		return $bad;
    	
    	if(isset(self::$params['phone_number'])){
    		if(is_array(self::$params['phone_number']) && count(self::$params['phone_number']) > 0){
		    	foreach(self::$params['phone_number'] as $key => $value){
		    		if(strlen($value) > 30 || strlen($value) < 1){
		    			$bad = 'phone_number';
		    			break;
		    		}
		    	}
    		}
    	}
    	
    	return $bad;
    }
    
    /**
     * Valida los horarios en agregar y modificar tienda.
     * @param ref $validate
     */
    private function validate_schedules(&$validate, $edit=false){
    	$bad = true;
    	if(isset(self::$params['day_id'])){
	    	foreach(self::$params['day_id'] as $key => $day_id){
	    		if(!isset(self::$params['open_hour'][$key]) || !isset(self::$params['close_hour'][$key])){
	    			$bad = false;
	    			break;
	    		}
	    		if($edit){
	    			if(!isset(self::$params['days_range_id'][$key])){
	    				$bad = false;
	    				break;
	    			}
	    		}
	    		
    			if($day_id<1 || $day_id>7)
	    			$bad = false;
	    		if($validate->expression($day_id, '/^(\d+)$/') == false)
	    			$bad = false;
	    		if($edit)
		    		if($validate->expression(self::$params['days_range_id'][$key], '/^((\d+)|)$/') == false)
		    			$bad = false;
	    		if($validate->expression(self::$params['open_hour'][$key], '/^\d{1,2}:\d{1,2}:\d{1,2}$/') == false)
	    			$bad = false;
	    		if($validate->expression(self::$params['close_hour'][$key], '/^\d{1,2}:\d{1,2}:\d{1,2}$/') == false &&
	    			self::$params['close_hour'][$key] != '')
	    			$bad = false;
	    		
	    		if($bad==false)
	    			break;
	    	}
    	}
    	return $bad;
    }
    
	private function validate_query(){
		//parametros de prueba
		//self::$params['result_items_per_page'] = '10';
		//self::$params['result_page'] = '1';
		//self::$params['filter_store_location_id'] = '1';
		//self::$params['return_comments'] = '1';
		//self::$params['filter_name'] = 'ma';
		//self::$params['filter_search_text'] = 'ma';
		//self::$params['filter_lat'] = '19.254432';
		//self::$params['filter_lon'] = '-103.744873';
		//self::$params['filter_radio'] = '300';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'filter_store_location_id', 
                     'label' => 'filter_store_location_id', 
                     'rules' => 'numeric'),
    			array('field' => 'filter_name', 
                     'label' => 'filter_name', 
                     'rules' => 'max_length[100]'),
    			array('field' => 'filter_search_text', 
                     'label' => 'filter_search_text', 
                     'rules' => 'max_length[100]'),
    			array('field' => 'filter_lat', 
                     'label' => 'filter_lat', 
                     'rules' => 'numeric|max_length[20]'),
    			array('field' => 'filter_lon', 
                     'label' => 'filter_lon', 
                     'rules' => 'numeric|max_length[20]'),
    			array('field' => 'filter_radio', 
                     'label' => 'filter_radio', 
                     'rules' => 'numeric'),
    			array('field' => 'return_comments', 
                     'label' => 'return_comments', 
                     'rules' => 'expression:/^(0|1)$/')
    	));
    	$this->status = $validate->run();
		if(isset(self::$params['result_items_per_page'])){
    		if(self::$params['result_items_per_page'] > 30){
    			$this->status = false;
    			$validate->_error_array['result_items_per_page'] = lang('less_than', 'result_items_per_page', '31');
    		}
    	}
    	
    	if($this->valParamasExist(array('filter_lat', 'filter_lon', 'filter_radio'))==false){
    		$this->status = false;
    		$validate->_error_array['result_items_per_page'] = lang('txt_dependence_other_fields');
    	}
    	
    	return $validate->_error_array;
    }
    
	private function validate_remove_image(){
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'image_id', 
                     'label' => 'image_id', 
                     'rules' => 'required|numeric'),
    			array('field' => 'enable', 
                     'label' => 'enable', 
                     'rules' => 'required|expression:/^(0|1)$/')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
    private function validate_comment(){
    	//parametros de prueba
		//self::$params['store_id'] = '2';
		//self::$params['comment'] = 'que chida marca!!';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'store_id', 
                     'label' => 'store_id', 
                     'rules' => 'required|is_natural_no_zero'),
    			array('field' => 'comment', 
                     'label' => 'comment', 
                     'rules' => 'required|max_length[5000]')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
	private function validate_disable_comment(){
		//parametros de prueba
		//self::$params['comment_id'] = '2';
		//self::$params['enable'] = '1';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'comment_id', 
                     'label' => 'comment_id', 
                     'rules' => 'required|is_natural_no_zero'),
    			array('field' => 'enable', 
                     'label' => 'enable', 
                     'rules' => 'required|expression:/^(0|1)$/')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
	private function validate_get_comment(){
		//parametros de prueba
		//self::$params['result_items_per_page'] = '10';
		//self::$params['result_page'] = '1';
		//self::$params['store_id'] = '2';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'store_id', 
                     'label' => 'store_id', 
                     'rules' => 'required|is_natural_no_zero')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
    
    /**
     * Valida campos que dependen de otros.
     * @param array $val_params. Contiene los parametros que se enviaron por post y get
     */
    private function valParamasExist($val_params){
    	foreach($val_params as $val){
    		if(isset(self::$params[$val])){
    			if(self::$params[$val]!=''){
    				foreach($val_params as $val1){
    					if(!isset(self::$params[$val1]))
    						return false;
    					if(self::$params[$val1]=='')
    						return false;
    				}
    			}
    		}
    	}
    	return true;
    }
    
	private function configImg(){
   		$config = array();
   		if(isset($_FILES['images'])){
			foreach($_FILES['images']['name'] as $key => $file){
				if($file != ''){
					$config[] = array(
						'input_file' => 'images',
						'key' => $key,
						'size' => 5120,
						'dimensions' => '2272x2272',
						'min_dimensions' => '50x50',
						'format' => array('gif','png','jpg'),
						'resize' => array('b','m','s')
					);
				}
			}
   		}
		return $config;
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