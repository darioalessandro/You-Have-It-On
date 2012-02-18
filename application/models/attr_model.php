<?php
class Attr_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
	}
	
	/**
	 * Agrega una propiedad que se usara en las categorias.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function saveAttr(&$params){
		$code_name = isset($params['code_name'])? 
				($params['code_name']!=''? $params['code_name']: ''): '';
		$name = isset($params['name'])? 
				($params['name']!=''? $params['name']: ''): '';
		
		//se inserta el attr
		$this->db->query("INSERT INTO attrs (code_name, name, is_public, created_by_user_id, lang) 
			VALUES ('".$code_name."', '".$name."', '1', '".$params['user_id']."', '".$params['lang']."')");
		
		
		return true;
	}
	
	
	
	
	/**
	 * Obtiene una lista de atributos, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function getAttrs(&$params){
		$sql = $this->getQuery($params);
		$scat = isset($params['filter_category_id'])? 
					(is_numeric($params['filter_category_id'])? " INNER JOIN cat_attr AS ca ON a.id = ca.attr_id": '')
				: '';
		
		$query = Sys::pagination("
			SELECT a.id, a.code_name, a.name 
			FROM attrs AS a".$scat.$sql, $params);
		
		$attrs = array('attrs' => array());
		
		$res_attr = $this->db->query($query);
		if($res_attr->num_rows() > 0){
			Sys::loadLanguage(Sys::$idioma_load, 'attrs');
			
			foreach($res_attr->result_array() as $row){
				if($row['name'] != '')
					unset($row['code_name']);
				elseif($row['code_name'] != ''){
					$row['name'] = lang($row['code_name']);
					unset($row['code_name']);
				}
				$attrs['attrs'][] = $row;
			}
		}
		return $attrs;
	}
	
	
	/**
	 * Genera los filtros de la consulta sql para realizar la busqueda en los atributos
	 * @param array $params.Contiene los parametros que se enviaron por post y get
	 */
	private function getQuery(&$params){
		$sql = isset($params['filter_attr_id'])? 
					($params['filter_attr_id']!=''? " WHERE a.id = '".$params['filter_attr_id']."'": '') 
				: '';
		$sql .= isset($params['filter_name'])? 
					($params['filter_name']!=''? 
						($sql!=''? ' AND ':' WHERE ')."LOWER(a.name) LIKE LOWER('".$params['filter_name']."%')"
					: '') 
				: '';
		$sql .= isset($params['filter_lang'])? 
					($params['filter_lang']!=''? 
						($sql!=''? ' AND ':' WHERE ')."LOWER(a.lang) = LOWER('".$params['filter_lang']."')"
					: '') 
				: '';
		$sql .= isset($params['filter_type'])? 
					($params['filter_type']!=0? 
						($sql!=''? ' AND ':' WHERE ').($params['filter_type']==1? 'a.code_name': 'a.name')." <> ''"
					: '') 
				: '';
		$sql .= isset($params['filter_category_id'])? 
					($params['filter_category_id']!=''? 
						($sql!=''? ' AND ':' WHERE ')."ca.cat_id = '".$params['filter_category_id']."'"
					: '') 
				: '';
		return $sql;
	}
}
?>