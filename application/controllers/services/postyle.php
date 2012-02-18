<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Postyle extends Services{
	private $status = true;
	
	function Postyle(){
		parent::__construct();
	}
	
	/**
	 * Servicio que agrega postyle a la base de datos. permite agregar imagenes al postyle (opcional)
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
				
				//si hay 1 archivo valido lo sube al server
				$images = array();
				if(count($config) == 1 || isset(self::$params["image_url"])){
					$this->load->library('my_upload', $config);
					
					if(isset(self::$params["image_url"]) && count($config) != 1){
						$carga_url = self::$params["image_url"];
						$conf_url = array(
							'resize' => array('b','m','s')
						);
					}else{
						$carga_url = '';
						$conf_url = null;
					}
					
					$images = $this->my_upload->upload($carga_url, $conf_url);
					if(isset($images[0]['code']))
						if($images[0]['code'] == 301)
							throw new UserValidateException(lang("txt_image_max1_error"));
				}else
					throw new UserValidateException(lang("txt_image_max1_error"));
				
				//obtiene datos de un usario del token
				$this->load->model('user_model');
				$res_usr = $this->user_model->getIdUserToken(self::$params);
				$data_usr = $res_usr->row_array();
				self::$params['user_id'] = $data_usr['id'];
				
				$this->load->model('postyle_model');
				$data = $this->postyle_model->savePostyle(self::$params, $images);
				if($data == -1)
					throw new RankingNotFoundException();
				elseif($data == -2)
					throw new OcationNotFoundException();
				else
					return $this->parseOutput($data);
			}else
				throw new UserValidateException(implode(',',$response_errors));
		//}
			
		//$this->load->helper(array('form', 'url'));
		//$this->load->view('postyle_form');
	}
	
	
	/**
	 * Servicio que modifica la informacion de una tienda. Las imagenes son opcionales, pero si hay, las sube
	 * y las agrega a la bd, al igual que los horarios
	 */
	/*public function edit(){
		$this->checkAccessToken();
		
		if(isset($_POST['enviar'])){
			$response_errors = $this->validate('edit');
			if($this->status == true){
				//si llegan archivos configuramos la libreria
				$config = $this->configImg();
				
				//si hay 1 archivo valido lo sube al server
				$images = array();
				if(count($config) == 1){
					$this->load->library('my_upload', $config);
					$images = $this->my_upload->upload();
				}
				
				//obtiene datos de un usario del token
				$this->load->model('user_model');
				$res_usr = $this->user_model->getIdUserToken(self::$params);
				$data_usr = $res_usr->row_array();
				self::$params['user_id'] = $data_usr['id'];
				
				$this->load->model('postyle_model');
				$data = $this->postyle_model->updatePostyle(self::$params, $images);
				if($data == -1)
					throw new RankingNotFoundException();
				elseif($data == -2)
					throw new OcationNotFoundException();
				else
					return $this->parseOutput($data);
			}else
				throw new UserValidateException(implode(',',$response_errors));
		}
			
		$this->load->helper(array('form', 'url'));
		$this->load->view('postyle_form');
	
	}*/
	
	/**
	 * Servicio que regresa un listado de postyles, paginadas y se pueden espesificar mas filtros
	 * para obtener postyles espesificas
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
			self::$params['user_id'] = $data_usr['id'];
			
			$this->load->model('postyle_model');
			$data = $this->postyle_model->getPostyles(self::$params);
			if($data==false)
				throw new NoResultsFoundException();
			else
				return $this->parseOutput($data);
		}else
			throw new UserValidateException(implode(',',$response_errors));
	
	}
	
	/**
	 * Servicio que habilita o deshabilita un postyle
	 * @throws PostyleNotFoundException
	 * @throws NotHavePermissionException
	 * @throws UserValidateException
	 */
	public function disable(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('disable');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['user_id'] = $data_usr['id'];
				
			$this->load->model('postyle_model');
			$data = $this->postyle_model->disable(self::$params);
			
			if($data===false)
				throw new PostyleNotFoundException();
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
	 * Servicio que califica a un postyle, hace uso de triggers en la bd
	 * @throws PostyleNotFoundException
	 * @throws RankingNotFoundException
	 * @throws UserValidateException
	 */
	public function rank(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('rank');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['user_id'] = $data_usr['id'];
				
			$this->load->model('postyle_model');
			$data = $this->postyle_model->rank(self::$params);
			
			if($data === -1)
				throw new PostyleNotFoundException();
			elseif($data == -2)
				throw new RankingNotFoundException();
			
			self::$msg_response = 'text_successful_process';
			return '';
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * SERVICIOS DE COMMENT
	 * Servicio que agrega un comentario a un postyle
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
				
			$this->load->model('postyle_model');
			$data = $this->postyle_model->comment(self::$params);
			
			if($data===false)
				throw new PostyleNotFoundException();
			
			self::$msg_response = 'text_successful_process';
			return '';
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	/**
	 * Servicio que habilita o deshabilita comentarios de postyle
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
				
			$this->load->model('postyle_model');
			$data = $this->postyle_model->disable_comment(self::$params);
			
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
	 * 
	 * Servicio que regresa el listado de comentarios de un postyle
	 * @throws UserValidateException
	 */
	public function get_comments(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('get_comment');
		if($this->status == true){
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['token_user_id'] = $data_usr['id'];
			
			$this->load->model('postyle_model');
			$data = $this->postyle_model->get_comments(self::$params);
			
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
    			array('field' => 'description', 
                     'label' => 'description', 
                     'rules' => 'max_length[3000]'),
    			array('field' => 'ocation_id', 
                     'label' => 'ocation_id', 
                     'rules' => 'numeric|is_natural'),
    			array('field' => 'ocation_label', 
                     'label' => 'ocation_label', 
                     'rules' => 'max_length[100]'),
    			array('field' => 'location_lat', 
                     'label' => 'location_lat', 
                     'rules' => 'numeric'),
    			array('field' => 'location_lon', 
                     'label' => 'location_lon', 
                     'rules' => 'numeric')/*,
    			array('field' => 'image_url', 
                     'label' => 'image_url', 
                     'rules' => 'expression:/^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?$/')*/
    	));
    	$this->status = $validate->run();
    	
    	if(!isset($_FILES['images'])){
	    	if(isset(self::$params["image_url"])){
	    		if(Sys::urlValid(self::$params["image_url"]) == false){
	    			$validate->_error_array['image_url'] = lang('txt_oval_invalid', "image_url");
	    			$this->status = false;
	    		}
	    	}
    	}
    	
		$bad = $this->validate_tags($validate); //tags de los items
		if($bad==false){
    		$validate->_error_array['horary'] = lang('txt_item_tags_error');
    		$this->status = false;
    	}
    	
		$bad = $this->validate_tags($validate, 'user'); //tags de los usuarios
		if($bad==false){
    		$validate->_error_array['tags_users'] = lang('txt_item_tags_error');
    		$this->status = false;
    	}
    	
    	return $validate->_error_array;
    }
    
	private function validate_edit(){
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'postyle_id', 
                     'label' => 'postyle_id', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'description', 
                     'label' => 'description', 
                     'rules' => 'max_length[3000]'),
    			array('field' => 'ocation_id', 
                     'label' => 'ocation_id', 
                     'rules' => 'numeric|is_natural'),
    			array('field' => 'location_lat', 
                     'label' => 'location_lat', 
                     'rules' => 'numeric'),
    			array('field' => 'location_lon', 
                     'label' => 'location_lon', 
                     'rules' => 'numeric')
    	));
    	$this->status = $validate->run();
    	
		$bad = $this->validate_tags($validate);
		if($bad==false){
    		$validate->_error_array['horary'] = lang('txt_item_tags_error');
    		$this->status = false;
    	}
    	
    	return $validate->_error_array;
    }
    
    /**
     * Valida los vectores de tags e items.
     * @param ref $validate
     */
    private function validate_tags(&$validate, $type_val='item'){
    	$bad = true;
    	$name_id = 'items_id'; $all_fields = '';
    	if($type_val=='user'){
    		$name_id = 'users_id';
			$all_fields = 'usr';
    	}
    	
    	if(isset(self::$params[$name_id])){
	    	foreach(self::$params[$name_id] as $key => $item){
	    		if(!isset(self::$params['tag_'.$all_fields.'description'][$key]) || !isset(self::$params['tag_'.$all_fields.'width'][$key]) 
	    			|| !isset(self::$params['tag_'.$all_fields.'height'][$key]) || !isset(self::$params['tag_'.$all_fields.'x'][$key])
	    			|| !isset(self::$params['tag_'.$all_fields.'y'][$key])){
	    			$bad = false;
	    			break;
	    		}
	    		
	    		if($validate->expression($item, '/^(\d+)$/') == false)
	    			$bad = false;
	    		if(self::$params['tag_'.$all_fields.'description'][$key] == '')
	    			$bad = false;
	    		if(self::$params['tag_'.$all_fields.'width'][$key]<0 || self::$params['tag_'.$all_fields.'width'][$key]>100)
	    			$bad = false;
	    		if($validate->expression(self::$params['tag_'.$all_fields.'width'][$key], '/^(\d+)$/') == false)
	    			$bad = false;
	    		if(self::$params['tag_'.$all_fields.'height'][$key]<0 || self::$params['tag_'.$all_fields.'height'][$key]>100)
	    			$bad = false;
	    		if($validate->expression(self::$params['tag_'.$all_fields.'height'][$key], '/^(\d+)$/') == false)
	    			$bad = false;
	    		if(self::$params['tag_'.$all_fields.'x'][$key]<0 || self::$params['tag_'.$all_fields.'x'][$key]>100)
	    			$bad = false;
	    		if($validate->expression(self::$params['tag_'.$all_fields.'x'][$key], '/^(\d+)$/') == false)
	    			$bad = false;
	    		if(self::$params['tag_'.$all_fields.'y'][$key]<0 || self::$params['tag_'.$all_fields.'y'][$key]>100)
	    			$bad = false;
	    		if($validate->expression(self::$params['tag_'.$all_fields.'y'][$key], '/^(\d+)$/') == false)
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
		//self::$params['filter_postyle_id'] = '2';
		//self::$params['return_comments'] = '1';
		//self::$params['filter_user_id'] = '8';
		//self::$params['filter_ranking_id'] = '3';
		//self::$params['filter_location_lat'] = '-102.123';
		//self::$params['filter_location_lon'] = '19.2333';
		//self::$params['filter_ocation_id'] = '1';
		//self::$params['filter_items'] = array(4);
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'filter_postyle_id', 
                     'label' => 'filter_postyle_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_ranking_id', 
                     'label' => 'filter_ranking_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_ocation_id', 
                     'label' => 'filter_ocation_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_user_id', 
                     'label' => 'filter_user_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_location_lat', 
                     'label' => 'filter_location_lat', 
                     'rules' => 'numeric'),
    			array('field' => 'filter_location_lon', 
                     'label' => 'filter_location_lon', 
                     'rules' => 'numeric'),
    			array('field' => 'return_comments', 
                     'label' => 'return_comments', 
                     'rules' => 'expression:/^(0|1)$/'),
    			array('field' => 'filter_order', 
                     'label' => 'filter_order', 
                     'rules' => 'expression:/^(desc|asc)$/'),
    			array('field' => 'filter_both', 
                     'label' => 'filter_both', 
                     'rules' => 'expression:/^(0|1)$/')
    	));
    	$this->status = $validate->run();
		if(isset(self::$params['result_items_per_page'])){
    		if(self::$params['result_items_per_page'] > 30){
    			$this->status = false;
    			$validate->_error_array['result_items_per_page'] = lang('less_than', 'result_items_per_page', '31');
    		}
    	}
    	
    	if($this->valParamasExist(array('filter_location_lat', 'filter_location_lon'))==false){
    		$this->status = false;
    		$validate->_error_array['depende'] = lang('txt_dependence_other_fields');
    	}
    	
		$bad = $this->validate_items($validate);
		if($bad==false){
    		$validate->_error_array['items'] = lang('txt_postyle_items_error');
    		$this->status = false;
    	}
    	
    	return $validate->_error_array;
    }
	/**
     * Valida los vectores de items.
     * @param ref $validate
     */
    private function validate_items(&$validate){
    	$bad = true;
    	if(isset(self::$params['filter_items'])){
	    	foreach(self::$params['filter_items'] as $key => $item){
	    		if($validate->expression($item, '/^(\d+)$/') == false)
	    			$bad = false;
	    		
	    		if($bad==false)
	    			break;
	    	}
    	}
    	return $bad;
    }
    
    
	private function validate_disable(){
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'postyle_id', 
                     'label' => 'postyle_id', 
                     'rules' => 'required|is_natural_no_zero'),
    			array('field' => 'enable', 
                     'label' => 'enable', 
                     'rules' => 'required|expression:/^(0|1)$/')
    	));
    	$this->status = $validate->run();
    	return $validate->_error_array;
    }
    
    private function validate_rank(){
    	//parametros de prueba
		//self::$params['postyle_id'] = '4';
		//self::$params['ranking_id'] = '4';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'postyle_id', 
                     'label' => 'postyle_id', 
                     'rules' => 'required|is_natural_no_zero'),
    			array('field' => 'ranking_id', 
                     'label' => 'ranking_id', 
                     'rules' => 'required|is_natural_no_zero')
    	));
    	$this->status = $validate->run();
    	if(self::$params['ranking_id']>10){
	    	$this->status = false;
	    	$validate->_error_array['rank'] = lang('less_than', 'ranking_id', 4);
    	}
	    			
    	return $validate->_error_array;
    }
    
    private function validate_comment(){
    	//parametros de prueba
		//self::$params['postyle_id'] = '2';
		//self::$params['comment'] = 'pessspa Que guapa saliste hee!!';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'postyle_id', 
                     'label' => 'postyle_id', 
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
		//self::$params['postyle_id'] = '2';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'postyle_id', 
                     'label' => 'postyle_id', 
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
						'min_dimensions' => '150x150',
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