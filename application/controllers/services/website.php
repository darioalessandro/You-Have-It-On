<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Website extends Services{
	private $status = true;
	
	function Website(){
		parent::__construct();
	}
	
	
	/**
	 * Servicio que elimina una pagina web de la bd
	 */
	public function delete(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('delete');
		if($this->status == true){
			$this->load->model('website_model');
			$data = $this->website_model->deleteWeb(self::$params);
			if($data==false)
				throw new WebSiteNotFoundException();
			
			self::$msg_response = 'text_successful_process';
			return '';
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
    
	private function validate_delete(){		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'website_id', 
                     'label' => 'website_id', 
                     'rules' => 'required|numeric|is_natural_no_zero')
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