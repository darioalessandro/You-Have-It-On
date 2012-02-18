<?php
class Ocation_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
	}
	
	/**
	 * Agrega una propiedad que se usara en las categorias.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function saveOcation(&$params, $rtu_id=false){
		$code_name = isset($params['code_name'])? 
				($params['code_name']!=''? $params['code_name']: ''): '';
		$name = isset($params['name'])? 
				($params['name']!=''? $params['name']: ''): '';
		
		//se inserta la ocacion
		$this->db->query("INSERT INTO ocation (code_name, name, is_public, created_by_user_id, lang) 
			VALUES ('".$code_name."', '".$name."', '1', '".$params['user_id']."', '".$params['lang']."')");
		if($rtu_id){
			$res = $this->db->query("SELECT id FROM ocation ORDER BY id DESC LIMIT 1");
			$data_o = $res->row();
			return $data_o->id;
		}else
			return true;
	}
	
	
	/**
	 * Obtiene una lista de ocasiones, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function getOcation(&$params){
		$sql = $this->getQuery($params);
		
		$query = Sys::pagination("
			SELECT id, code_name, name 
			FROM ocation".$sql, $params);
		
		$ocations = array('ocations' => array());
		
		$res_ocation = $this->db->query($query);
		if($res_ocation->num_rows() > 0){
			Sys::loadLanguage(Sys::$idioma_load, 'ocations');
			
			foreach($res_ocation->result_array() as $row){
				if($row['name'] != '')
					unset($row['code_name']);
				elseif($row['code_name'] != ''){
					$row['name'] = lang($row['code_name']);
					unset($row['code_name']);
				}
				$ocations['ocations'][] = $row;
			}
		}
		return $ocations;
	}
	
	
	/**
	 * Genera los filtros de la consulta sql para realizar la busqueda en los atributos
	 * @param array $params.Contiene los parametros que se enviaron por post y get
	 */
	private function getQuery(&$params){
		$sql = isset($params['filter_ocation_id'])? 
					($params['filter_ocation_id']!=''? " WHERE id = '".$params['filter_ocation_id']."'": '') 
				: '';
		$sql .= isset($params['filter_name'])? 
					($params['filter_name']!=''? 
						($sql!=''? ' AND ':' WHERE ')."LOWER(name) LIKE LOWER('".$params['filter_name']."%')"
					: '') 
				: '';
		$sql .= isset($params['filter_lang'])? 
					($params['filter_lang']!=''? 
						($sql!=''? ' AND ':' WHERE ')."LOWER(lang) = LOWER('".$params['filter_lang']."')"
					: '') 
				: '';
		$sql .= isset($params['filter_type'])? 
					($params['filter_type']!=0? 
						($sql!=''? ' AND ':' WHERE ').($params['filter_type']==1? 'code_name': 'name')." <> ''"
					: '') 
				: '';
		return $sql;
	}
}
?>