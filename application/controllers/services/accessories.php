<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accessories extends Services{
	private $status = true;
	
	function States(){
		parent::__construct();
	}
	
	
	/**
	 * SERVICIOS DE COMMENT
	 * Servicio que agrega un comentario a los accesorios
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
				
			$this->load->model('accessories_model');
			$data = $this->accessories_model->comment(self::$params);
			
			if($data===false)
				throw new UserNotFoundException();
			
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
				
			$this->load->model('accessories_model');
			$data = $this->accessories_model->disable_comment(self::$params);
			
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
	 * Servicio que regresa el listado de comentarios de un postyle
	 * @throws UserValidateException
	 */
	public function get_comments(){
		$this->checkConsumerKey();
		
		$response_errors = $this->validate('get_comment');
		if($this->status == true){
			$this->load->model('accessories_model');
			$data = $this->accessories_model->get_comments(self::$params);
			
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
    
 	private function validate_comment(){
    	//parametros de prueba
		//self::$params['accessories_user_id'] = '3';
		//self::$params['comment'] = 'chida tu coleccion!!';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'accessories_user_id', 
                     'label' => 'accessories_user_id', 
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
		//self::$params['user_id'] = '3';
		
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
                     'label' => 'user_id', 
                     'rules' => 'required|is_natural_no_zero')
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
}