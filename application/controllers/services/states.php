<?php defined('BASEPATH') OR exit('No direct script access allowed');

class States extends Services{
	private $status = true;
	
	function States(){
		parent::__construct();
	}
	
	
	/**
	 * Servicio que regresa la lista de estados de un pais determinado
	 * @throws UserNotFoundException
	 * @throws UserValidateRegisterException
	 */
	public function query(){
		$this->checkConsumerKey();
		
		$response_errors = $this->validate('query');
		if($this->status == true){
			$this->load->model('states_model');
			$data = $this->states_model->getStates(self::$params);
			if($data==false)
				throw new StatesNotFoundException();
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
    	//datos de prueba
    	//self::$params['country'] = 'mx';
    	
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'country', 
                     'label' => 'country', 
                     'rules' => 'required|exact_length[2]')
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