<?php
class Enable_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function enable($table, $id, $enable){
		$res = $this->db->query("SELECT id FROM ".$table." WHERE id = ".$id);
		if($res->num_rows() > 0){
			$this->db->query("UPDATE ".$table." SET enable=".$enable." WHERE id = ".$id);
			
			return $enable;
		}
		return false;
	}
	
}
?>