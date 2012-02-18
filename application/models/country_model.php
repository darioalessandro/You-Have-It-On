<?php
class Country_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function getCountries($params){
		$res_count = $this->db->query("SELECT id, country, iso2, iso3, lat, lon 
			FROM country WHERE iso3 <> ''");
		if($res_count->num_rows() > 0){
			Sys::loadLanguage(Sys::$idioma_load, 'country');
			
			$data = array('countries' => array());
			foreach($res_count->result_array() as $row){
				$data['countries'][] = array(
								'name' => lang($row['iso3']),
								'code' => $row['iso2'],
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