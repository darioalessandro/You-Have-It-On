<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Attr extends Services{
	private $status = true;
	
	function States(){
		parent::__construct();
	}
	
	
	/**
	 * Servicio que agrega atributos para usarlas en las categorias
	 * @throws UserValidateException
	 */
	public function add(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('add');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['user_id'] = $data_usr['id'];
			
			$this->load->model('attr_model');
			$data = $this->attr_model->saveAttr(self::$params);
			
			self::$msg_response = 'text_successful_process';
			return '';
		}else
			throw new UserValidateException(implode(',',$response_errors));
	}
	
	
	/**
	 * Servicio que regresa un listado de atributos, paginadas y se pueden espesificar mas filtros
	 * para obtener atributos espesificos
	 * @throws NoResultsFoundException
	 * @throws UserValidateException
	 */
	public function query(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('query');
		if($this->status == true){
			$this->load->model('attr_model');
			$data = $this->attr_model->getAttrs(self::$params);
			if($data==false)
				throw new NoResultsFoundException();
			else
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
		//self::$params['name'] = 'Material';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'name', 
                     'label' => 'name', 
                     'rules' => 'required|max_length[100]')
    	));
    	$this->status = $validate->run();
    	
    	/*if(!isset(self::$params['name'])){
    		if(!isset(self::$params['code_name'])){
    			$validate->_error_array['params'] = lang('txt_attr_required', 'name', 'code_name');
    			$this->status = false;
    		}elseif(self::$params['code_name'] == ''){
    			$validate->_error_array['params'] = lang('txt_attr_required', 'name', 'code_name');
    			$this->status = false;
    		}
    	}elseif(self::$params['name'] == ''){
    		$validate->_error_array['params'] = lang('txt_attr_required', 'name', 'code_name');
    		$this->status = false;
    	}*/
    	
    	return $validate->_error_array;
    }
    
    
	private function validate_query(){
		//parametros de prueba
		//self::$params['result_items_per_page'] = '10';
		//self::$params['result_page'] = '1';
		//self::$params['filter_attr_id'] = '1';
		//self::$params['filter_name'] = 'p';
		//self::$params['filter_lang'] = 'spa';
		//self::$params['filter_type'] = '0';
		//self::$params['filter_category_id'] = '8';
		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'filter_attr_id', 
                     'label' => 'filter_attr_id', 
                     'rules' => 'numeric|is_natural_no_zero'),
    			array('field' => 'filter_name', 
                     'label' => 'filter_name', 
                     'rules' => 'max_length[100]'),
    			array('field' => 'filter_lang', 
                     'label' => 'filter_lang', 
                     'rules' => 'max_length[3]'),
    			array('field' => 'filter_type', 
                     'label' => 'filter_type', 
                     'rules' => 'expression:/^(0|1|2)$/'),
    			array('field' => 'filter_category_id', 
                     'label' => 'filter_category_id', 
                     'rules' => 'numeric|is_natural_no_zero')
    	));
    	$this->status = $validate->run();
		if(isset(self::$params['result_items_per_page'])){
    		if(self::$params['result_items_per_page'] > 30){
    			$this->status = false;
    			$validate->_error_array['result_items_per_page'] = lang('less_than', 'result_items_per_page', '31');
    		}
    	}
    	
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