<?php defined('BASEPATH') OR exit('No direct script access allowed');

class notification extends Services{
	private $status = true;
	
	function notification(){
		parent::__construct();
	}
	
	public function send(){
		$this->checkAccessToken();
		
		//obtiene datos de un usario del token
		$this->load->model('user_model');
		$res_usr = $this->user_model->getIdUserToken(self::$params);
		$data_usr = $res_usr->row_array();
		self::$params['token_user_id'] = $data_usr['id'];
		
		$this->load->model('notification_model');
		$data = $this->notification_model->sendNotifications(self::$params);
		
		return $this->parseOutput($data);
	}
	
	public function see(){
		$this->checkAccessToken();
		
		//obtiene datos de un usario del token
		$this->load->model('user_model');
		$res_usr = $this->user_model->getIdUserToken(self::$params);
		$data_usr = $res_usr->row_array();
		self::$params['token_user_id'] = $data_usr['id'];
		
		$this->load->model('notification_model');
		$data = $this->notification_model->updateSee(self::$params);
		
		return $this->parseOutput($data);
	}
	
	/**
	 * Servicio que obtiene el numero de notificaciones que tiene el usuario
	 * del token
	 */
	public function num_notification(){
		$this->checkAccessToken();
		
		//obtiene datos de un usario del token
		$this->load->model('user_model');
		$res_usr = $this->user_model->getIdUserToken(self::$params);
		$data_usr = $res_usr->row_array();
		self::$params['token_user_id'] = $data_usr['id'];
		
		$this->load->model('notification_model');
		$data = $this->notification_model->getNumNotification(self::$params);
		
		return $this->parseOutput($data);
	}
	
	
	public function get(){
		$this->checkAccessToken();
		
		$response_errors = $this->validate('get');
		if($this->status == true){
			//obtiene datos de un usario del token
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken(self::$params);
			$data_usr = $res_usr->row_array();
			self::$params['token_user_id'] = $data_usr['id'];
			
			$this->load->model('notification_model');
			$data = $this->notification_model->getNotifications(self::$params);
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
    
	private function validate_get(){		
    	$validate = new MY_Form_validation();
    	$validate->setArrayValidate(self::$params);
    	
    	$validate->set_rules(array(
    			array('field' => 'result_items_per_page', 
                     'label' => 'result_items_per_page', 
                     'rules' => 'required|numeric|is_natural_no_zero'),
    			array('field' => 'result_page', 
                     'label' => 'result_page', 
                     'rules' => 'required|numeric'),
    			array('field' => 'output_format', 
                     'label' => 'output_format', 
                     'rules' => 'expression:/^(0|1|2)$/')
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