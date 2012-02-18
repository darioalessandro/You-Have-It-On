<?php
class Config_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function getConfig(&$params){
		$res_conf = $this->db->query("SELECT * FROM config");
		Sys::loadLanguage(Sys::$idioma_load, 'config');
		
		$data = array('config' => array());
		foreach($res_conf->result_array() as $row){
			$data['config'][] = array(
							'id' => $row['id'],
							'name' => lang($row['code_name'])
						 );
		}
		return $data;
	}
}
?>