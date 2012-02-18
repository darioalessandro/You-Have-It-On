<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item extends Services{
	private $status = true;
	
	function Item(){
		parent::__construct();
	}
	
	/**
	 * Servicio que agrega items a la base de datos. permite agregar imagenes al item (opcional)
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
				
				$this->load->model('item_model');
				$data = $this->item_model->saveItem(self::$params, $images);
				switch($data){
					case -1: throw new BrandNotFoundException(); break;
					case -2: throw new StoreNotFoundException(); break;
					default:
						return $this->parseOutput($data);
				}
			}else
				throw new UserValidateException(implode(',',$response_errors));
		//}
			
		//$this->load->helper(array('form', 'url'));
		//$this->load->view('item_form');
	}
	
	
	/**
	 * Servicio que modifica la informacion de una item. Las imagenes son opcionales, pero si hay, las sube
	 * y las agrega a la bd
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
				
				$this->load->model('item_model');
				$data = $this->item_model->updateItem(self::$params, $images);
				switch($data){
					case -1: throw new NotHavePermissionException(); break;
					case -2: throw new ItemNotFoundException(); break;
					case -3: throw new BrandNotFoundException(); break;
					case -4: throw new StoreNotFoundException(); break;
					default:
						return $this->parseOutput($data); 
				}
			}else
				throw new UserValidateException(implode(',',$response_errors));
		//}
			
		//$this->load->helper(array('form', 'url'));
		//$this->load->view('item_form');
	
	}
	
	/**
	 * Servicio que regresa un listado de items, paginadas y se pueden espesificar mas filtros
	 * para obtener tiendas espesificas
	 * @throws NoResultsFoundException
	 * @throws UserValidateException
	 */
	public function query(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('query');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['token_user_id'] = $data_usr['id'];
			
			$this->load->model('item_model');
			$data = $this->item_model->getItems(self::$params);
			if($data==false)
				throw new NoResultsFoundException();
			else
				return $this->parseOutput($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	
	}
	
	/**
	 * Servicio que elimina (desactiva) imagenes de un item
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
				
			$this->load->model('item_model');
			$data = $this->item_model->removeImage(self::$params);
			
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
	 * Servicio que agrega un comentario a los items
	 * @throws ItemNotFoundException
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
				
			$this->load->model('item_model');
			$data = $this->item_model->comment(self::$params);
			
			if($data===false)
				throw new ItemNotFoundException();
			
			self::$msg_response = 'text_successful_process';
			return '';
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que habilita o deshabilita comentarios de items
	 * @throws CommentNotFoundException
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
				
			$this->load->model('item_model');
			$data = $this->item_model->disable_comment(self::$params);
			
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
	 * Servicio que regresa el listado de comentarios de un item
	 * @throws UserValidateException
	 */
	public function get_comments(){
		$this->checkConsumerKey();
		
		$response_errors = $this->validate('get_comment');
		if($this->status == true){
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['token_user_id'] = $data_usr['id'];
			
			$this->load->model('item_model');
			$data = $this->item_model->get_comments(self::$params);
			
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
    			array('field' => 'label', 
                     'label' => 'label', 
                     'rules' => 'required|max_length[300]'),
    			array('field' => 'brand_id', 
                     'label' => 'brand_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'bought_in', 
                     'label' => 'bought_in', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'price', 
                     'label' => 'price', 
                     'rules' => 'numeric'),
    			array('field' => 'brand_name', 
                     'label' => 'brand_name', 
                     'rules' => 'max_length[200]'),
    			array('field' => 'bought_label', 
                     'label' => 'bought_label', 
                     'rules' => 'max_length[100]'),
    			array('field' => 'bought_lat', 
                     'label' => 'bought_lat', 
                     'rules' => 'numeric|max_length[20]'),
    			array('field' => 'bought_lon', 
                     'label' => 'bought_lon', 
                     'rules' => 'numeric|max_length[20]')
    	));
    	$this->status = $validate->run();
    	
		$bad = $this->validate_attr_value($validate);
		if($bad==false){
    		$validate->_error_array['attr_val'] = lang('txt_attr_value_error');
    		$this->status = false;
    	}
    	
    	return $validate->_error_array;
    }
    
	private function validate_edit(){
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'item_id', 
                     'label' => 'item_id', 
                     'rules' => 'required|is_natural_no_zero'),
    			array('field' => 'label', 
                     'label' => 'label', 
                     'rules' => 'required|max_length[300]'),
    			array('field' => 'brand_id', 
                     'label' => 'brand_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'bought_in', 
                     'label' => 'bought_in', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'price', 
                     'label' => 'price', 
                     'rules' => 'numeric')
    	));
    	$this->status = $validate->run();
    	
    	$bad = $this->validate_attr_value($validate, true);
		if($bad==false){
    		$validate->_error_array['attr_val'] = lang('txt_attr_value_error');
    		$this->status = false;
    	}
    	
    	return $validate->_error_array;
    }
    
    /**
     * Valida las propiedades y valores de un item.
     * @param ref $validate
     */
    private function validate_attr_value(&$validate, $edit=false){
    	$bad = true;
    	if(isset(self::$params['cat_attr_id'])){
    		if(is_array(self::$params['cat_attr_id']) && count(self::$params['cat_attr_id']) > 0){
		    	foreach(self::$params['cat_attr_id'] as $key => $cat_attr_id){
		    		if(!isset(self::$params['value'][$key])){
		    			$bad = false;
		    			break;
		    		}
		    		
		    		if($validate->expression($cat_attr_id, '/^(\d+)$/') == false)
		    			$bad = false;
		    		
		    		$len = strlen(self::$params['value'][$key]);
		    		if($len < 1 || $len > 200)
		    			$bad = false;
		    		
		    		if($bad==false)
		    			break;
		    	}
    		}else 
    			$bad = false;
    	}
    	
    	return $bad;
    }
    
	private function validate_query(){
		//parametros de prueba
		//self::$params['result_items_per_page'] = '5';
		//self::$params['result_page'] = '1';
		//self::$params['filter_item_id'] = '4';
		//self::$params['return_comments'] = '1';
		//self::$params['filter_store_location_id'] = '9';
		//self::$params['filter_brand_id'] = '13';
		//self::$params['filter_user_id'] = '2';
		//self::$params['filter_label'] = 'p';
		//self::$params['filter_search_text'] = 'ma';
		//self::$params['filter_lat'] = '19.254432';
		//self::$params['filter_lon'] = '-103.744873';
		//self::$params['filter_radio'] = '100';
		//self::$params['filter_category_id'] = '4';
		//self::$params['filter_postyle_id'] = '2';
		//self::$params['get_light_result'] = '1';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'filter_item_id', 
                     'label' => 'filter_item_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_label', 
                     'label' => 'filter_label', 
                     'rules' => 'max_length[200]'),
    			array('field' => 'filter_store_location_id', 
                     'label' => 'filter_store_location_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_brand_id', 
                     'label' => 'filter_brand_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_category_id', 
                     'label' => 'filter_category_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_user_id', 
                     'label' => 'filter_user_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
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
                     'rules' => 'expression:/^(0|1)$/'),
    			array('field' => 'filter_postyle_id', 
                     'label' => 'filter_postyle_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'get_light_result', 
                     'label' => 'get_light_result', 
                     'rules' => 'expression:/^(0|1)$/'),
    			array('field' => 'only_my_items', 
                     'label' => 'only_my_items', 
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
		//self::$params['item_id'] = '2';
		//self::$params['comment'] = 'chido tu item!!';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'item_id', 
                     'label' => 'item_id', 
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
		//self::$params['comment_id'] = '3';
		//self::$params['enable'] = '0';
		
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
		//self::$params['item_id'] = '4';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'item_id', 
                     'label' => 'item_id', 
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