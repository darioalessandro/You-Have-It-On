<?php defined('BASEPATH') OR exit('No direct script access allowed');

class search extends Services{
	private $status = true;
	
	function States(){
		parent::__construct();
	}
	
	
	/**
	 * Servicio que regresa un listado de tiendas, paginadas y se pueden espesificar mas filtros
	 * para obtener tiendas espesificas
	 * @throws NoResultsFoundException
	 * @throws UserValidateException
	 */
	public function query(){
		$this->checkConsumerKey();
		
		$response_errors = $this->validate('query');
		if($this->status == true){
			//obtiene datos de un usario del token
			if(isset(self::$params['oauth_token'])){
				if(self::$params['oauth_token'] != ''){
					$this->load->model('user_model');
					$res_usr = $this->user_model->getIdUserToken(self::$params);
					$data_usr = $res_usr->row_array();
					self::$params['token_user_id'] = $data_usr['id'];
				}
			}
			
			$this->load->model('search_model');
			$data = $this->search_model->search(self::$params);
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
    
	private function validate_query(){		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'search_text', 
                     'label' => 'search_text', 
                     'rules' => 'required|max_length[100]')
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