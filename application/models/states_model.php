<?php
class States_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function getStates($params){
		$res_stat = $this->db->query("SELECT r.id, r.code_name, r.code, r.lat, r.lon 
			FROM country AS c INNER JOIN regions AS r ON c.id = r.country_id 
			WHERE c.iso2 = LOWER('".$params['country']."')");
		if($res_stat->num_rows() > 0){
			Sys::loadLanguage(Sys::$idioma_load, 'regions');
			
			$data = array('states' => array());
			foreach($res_stat->result_array() as $row){
				$data['states'][] = array(
								'id' => $row['id'],
								'name' => lang($row['code_name']),
								'code' => $row['code'],
								'lat' => $row['lat'],
								'lon' => $row['lon']
							 );
			}
			return $data;
		}
		return false;
	}
}
?>