<?php
class Website_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function deleteWeb(&$params){
		$res_count = $this->db->query("SELECT id, url 
			FROM websites WHERE id = ".$params["website_id"]);
		if($res_count->num_rows() > 0){
			$this->db->query("DELETE FROM websites WHERE id = ".$params["website_id"]);
			return true;
		}else 
			return false;
	}
}
?>