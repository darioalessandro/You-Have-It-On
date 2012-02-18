<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends CI_Controller{
	protected $request = NULL;
	protected static $params = array();
	protected static $response_error = false;
	protected static $msg_response = '';
	private $controller = NULL;
	private $controller_name = '';
	private $supported_formats = array(
		'json' => 'application/json'
	);
	
	function Services(){
		parent::__construct();
		Sys::$url_base = $this->config->item('base_url');
		
		$this->load->database();
		$zone = Sys::getLocalTimezone();
		if($zone!=false){
			date_default_timezone_set($zone["region"]);
			$this->db->query("SET time_zone = '".$zone["offset"]."';");
		}
	}
	
	function _remap($controller, $params = array()){
		try {
			//$this->load->database();
			
			$this->controller_name = $controller;
			$this->getParameters($params);
			$this->detectLanguage();
			$this->loadLanguage();
			$this->validate();
			
			$response = $this->controller->{self::$params['method']}();
			
			if($response <> '' || self::$msg_response != ''){
				$this->response($this->parseOutput($response));
			}
		}catch(Exception $e){
			$this->response($this->parseOutput($e));
		}
	}
	
	protected function response($data=''){
		$this->request->format = $this->getFormat();
		
		$this->output->set_header('Content-type: '.$this->request->format);
        $this->output->set_output($data);
    }
    
    /**
     * El metodo se compone de dos partes, el nombre del metodo a ejecutar del servicio y el 
     * formato en que tiene que ser debueltos los datos.
     * @param $method <string> Metodo que se ejecutara en el servicio espesificado
     * @return <string> Nombre del formato
     */
    private function detectFormat($method=''){
    	$parse = explode('.', $method);
    	$num = count($parse);
    	if($num==2){
    		self::$params['method'] = $parse[0];
    		self::$params['format'] = $parse[1];
    	}else
    		self::$params['format'] = $this->config->item('format');
    }
    
    /**
     * 
     * @author gama
     */
    private function detectLanguage(){
    	if(isset(self::$params['lang'])){
    		if(file_exists(APPPATH.'/language/'.self::$params['lang'].'/'))
    			self::$params['lang'] = self::$params['lang'];
    		else 
    			self::$params['lang'] = $this->config->item('lang');
    	}else 
    		self::$params['lang'] = $this->config->item('lang');
    	
    	self::$params['lang'] = (isset(self::$params['lang']))? self::$params['lang']: $this->config->item('lang');
    	Sys::$idioma_load = self::$params['lang'];
    }
    
    /**
     * Permite incorporar las validaciones bases que apliquen para todos los servicios.
     * Validaciones mas espesificas tendran que ser en cada servicio en particular.
     * @author gama
     */
    private function validate(){
    	//que el formato de salida sea soportado por el sistema
    	if(!isset($this->supported_formats[self::$params['format']]))
    		throw new FormatNotSupportedException();
    	
    	//que exista el servicio solicitado
    	if(file_exists('application/controllers/services/'.$this->controller_name.'.php')){
    		$this->load->file('application/controllers/services/'.$this->controller_name.'.php', false);
    		$this->controller = new $this->controller_name();
    		//que exista el metodo a ejecutar de dicho servicio
    		if(!method_exists($this->controller, self::$params['method']))
    			throw new MethodNotExistException();
    	}else
    		throw new ServiceNotExistException();
    }
    
    /**
     * Obtiene los parametros (GET y POST) y ejecuta los metodos de limpieza de datos,
     * obtiene el metodo a ejecutar y el formato de salida.
     * @param $params <array> Conjunto de parametos GET
     * @author gama
     */
    private function getParameters($params=array()){
    	$con = 1;
    	foreach ($params as $key => $value){
    		if($key===0)
    			self::$params['method'] = strtolower($value);
    		else{
    			$value = explode(':', strtolower($value));
    			if(count($value)>1)
    				self::$params[$value[0]] = $value[1];
    			else{
    				self::$params['get'.$con] = $value[0];
    				$con++;
    			}
    		}
    	}
    	if(!isset(self::$params['method']))
    		self::$params['method'] = '';
    	
    	self::$params = array_merge(self::$params, $_POST, $_GET);
    	$this->cleanParameters();
    	
    	$this->detectFormat(self::$params['method']);
    }
    
    /**
     * regresa el formato de salida espesificado por el usuario y si no es soportado por
     * el sistema regresa el formato por default
     * @return format <string>
     * @author gama
     */
    protected function getFormat(){
    	if(!isset($this->supported_formats[self::$params['format']]))
    		return $this->config->item('format');
    	return self::$params['format'];
    }
    
    /**
     * Escapa todos los parametros contenidos en self::$params
     * @author gama
     */
    private function cleanParameters(){
    	foreach (self::$params as $key => $value)
    		self::$params[$key] = Sys::limpiarTexto($this->security->xss_clean($value));
    }
	
    private function parseOutput($data){
    	if(is_object($data))
    		return $this->{'_parseOutError_'.$this->getFormat()}($data); 
    	else
    		return $this->{'_parseOut_'.$this->getFormat()}($data);
    }
    
    private function loadLanguage(){
    	Sys::loadLanguage(self::$params['lang']);
    }
    
    
    
	public function checkAccessToken(){
		if(!isset(self::$params['consumer_key']))
			throw new OAuthConsumerKeyException();
		if(!isset(self::$params['oauth_token']))
			throw new OAuthTokenException();
		
		$result = $this->config->item('store_oauth')->checkAccessToken(
					self::$params['consumer_key'], 
					self::$params['oauth_token']);
		if($result)
			return true;
		else 
			throw new OAuthInvalidTokenException();
	}
	
	public function checkConsumerKey(){
		if(!isset(self::$params['consumer_key']))
			throw new OAuthConsumerKeyException();
		
		$result = $this->config->item('store_oauth')->checkConsumerKey(
					self::$params['consumer_key']);
		if($result)
			return true;
		else 
			throw new OAuthInvalidConsumerException();
	}
    
    
    
    protected function _parseOut_json($data){
    	$msg = (self::$msg_response=='')? 'text_successful_process': self::$msg_response;
    	$key_ini = '{';
    	$info = '';
    	if($data!=''){
    		//$key_ini = '{';
    		$info = ','.$data;
    	}
    	$info .= '}';
    	
    	$res = (self::$response_error)? $data: $key_ini.'"status": {"code": 200, "message": "'.lang($msg).'"}'.$info;
    	return $res;
    }
    
    protected function _parseOutError_json($e){
    	return '{"status": {"code": '.$e->getCode().',"message": "'.$e->getMessage().'"}}';
    }
    
	protected function _format_json($data = array()){
    	return json_encode($data);
    }
    
    protected function _formatin_json($data = array(), $inarray=true){
    	return json_decode($data, $inarray);
    }
    
    protected function _trim_format_json($data=''){
    	$start = strpos($data, '{')+1;
    	$end = strripos($data, '}');
    	return substr($data, $start, ($end-$start));
    }
}