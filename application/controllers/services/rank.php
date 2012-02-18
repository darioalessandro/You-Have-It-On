<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rank extends Services{
	private $status = true;
	
	function Rank(){
		parent::__construct();
	}
	
	
	/**
	 * Servicio que regresa la lista de rankings
	 * @throws UserNotFoundException
	 * @throws UserValidateRegisterException
	 */
	public function query(){
		$this->checkConsumerKey();
		
		$this->load->model('rank_model');
		$data = $this->rank_model->getRanks(self::$params);
		
		return $this->parseOutput($data);
	
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