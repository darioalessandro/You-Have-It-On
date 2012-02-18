<?php
class Rank_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function getRanks($params){
		$res_count = $this->db->query("SELECT id, code_name 
			FROM rankings");
		Sys::loadLanguage(Sys::$idioma_load, 'rankings');
		
		$data = array('rankings' => array());
		foreach($res_count->result_array() as $row){
			$data['rankings'][] = array(
							'id' => $row['id'],
							'name' => lang($row['code_name'])
						 );
		}
		return $data;
	}
}
?>