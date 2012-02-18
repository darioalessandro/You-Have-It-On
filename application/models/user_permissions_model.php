<?php
class user_permissions_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function publish(&$params, $publish){
		foreach($publish as $item){
			if(isset($item['method_conf']))
				$this->{$item['method_conf']}($params, $item);
			else
				$this->conf_publish_facebook($params, $item);
		}
	}
	
	public function conf_publish_facebook(&$params, $publish){
		$data = $this->checkPermission($publish['method'], $params);
		if($data == 1){
			$fb = new my_facebook();
			$fb->{$publish['action']}($publish['obj_id'], $params['user_id'], $publish['opc']);
		}
	}
	
	public function conf_publish_twitter(&$params, $publish){
		$data = $this->checkPermission($publish['method'], $params);
		if($data == 1){
			$tw = new my_twitter();
			$tw->{$publish['action']}($publish['obj_id'], $params['user_id']);
		}
	}
	
	/**
	 * Verifica si usuaior a asignado el permiso  
	 * @param unknown_type $code_name
	 */
	public function checkPermission($code_name, &$params){
		$res = $this->db->query("SELECT users_config.value 
			FROM config INNER JOIN users_config ON config.id = users_config.config_id 
			WHERE code_name = '".$code_name."' AND users_config.user_id = ".$params['user_id']);
		if($res->num_rows() > 0){
			$data = $res->row();
			return $data->value;
		}
		return false;
	}
	
	private function curlExec($url){
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Expect:'));
		
		$respuesta = json_decode(curl_exec($handle)); // OBTIENE EL RESULTADO
		
		curl_close($handle);
		
		return $respuesta;
	}
}
?>