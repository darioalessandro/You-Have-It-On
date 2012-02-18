<?php
class phone_number_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function deleteWeb(&$params){
		$res_count = $this->db->query("SELECT id 
			FROM phone_numbers WHERE id = ".$params["phone_number_id"]);
		if($res_count->num_rows() > 0){
			$this->db->query("DELETE FROM phone_numbers WHERE id = ".$params["phone_number_id"]);
			return true;
		}else 
			return false;
	}
}
?>