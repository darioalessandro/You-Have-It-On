<?php
class Category_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
	}
	
	/**
	 * Agrega una categoriay la relacion con sus atributos
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function saveCategory(&$params){
		$code_name = isset($params['code_name'])? 
				($params['code_name']!=''? $params['code_name']: ''): '';
		$name = isset($params['name'])? 
				($params['name']!=''? $params['name']: ''): '';
		
		//categoria padre
		$insert = true;
		$parent = array('', '');
		if(isset($params['category_parent'])){
			if($params['category_parent'] > 0){
				$parent[0] = ', category_parent';
				$parent[1] = ', '.$params['category_parent'];
				$res_chk = $this->db->query("SELECT id FROM category WHERE id = ".$params['category_parent']);
				if($res_chk->num_rows() < 1)
					$insert = false;
			}
		}

		if($insert){
			//se inserta la categoria
			$this->db->query("INSERT INTO category (code_name, name, is_public, created_by_user_id, lang".$parent[0].") 
				VALUES ('".$code_name."', '".$name."', '1', '".$params['user_id']."', '".$params['lang']."'".$parent[1].")");
			$res_cat = $this->db->query("SELECT id FROM category ORDER BY id DESC LIMIT 1");
			$data_cat = $res_cat->row();
			
			if(isset($params['cat_attr'])){
				if(is_array($params['cat_attr'])){
					foreach($params['cat_attr'] as $attr){
						$attr = intval($attr);
						$res_chk = $this->db->query("SELECT id FROM attrs WHERE id = ".$attr);
						if($res_chk->num_rows() > 0)
							$this->db->query("INSERT INTO cat_attr (cat_id, attr_id) 
								VALUES ('".$data_cat->id."', '".$attr."')");
					}
				}
			}
		}else
			return false;
		
		return true;
	}
	
	
	
	
	/**
	 * Obtiene una lista de categorias, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function getCategories(&$params){
		$sql = $this->getQuery($params);
		$query = Sys::pagination("
			SELECT id, code_name, name 
			FROM category".$sql, $params, true);
		
		$cats = array('categories' => array(), 'total_rows' => $query["total_rows"]);
		
		$params['filter_recursive'] = isset($params['filter_recursive'])?
			($params['filter_recursive']!=''? intval($params['filter_recursive']): 0): 0;
		
		$res_cat= $this->db->query($query["query"]);
		if($res_cat->num_rows() > 0){
			Sys::loadLanguage(Sys::$idioma_load, 'categories');
			Sys::loadLanguage(Sys::$idioma_load, 'attrs');
			
			foreach($res_cat->result_array() as $row){
				if($row['name'] != '')
					unset($row['code_name']);
				elseif($row['code_name'] != ''){
					$row['name'] = lang($row['code_name']);
					unset($row['code_name']);
				}
				
				//obtenemos los atributos de cada categoria
				$row['attributes'] = $this->getAttrCat($row['id']);
				
				$row['categories'] = array();
				if($params['filter_recursive'] == 1) //se ejecuta la recursividad para obtener las sub categorias
					$row['categories'] = $this->recursivo($row['id']);
				
				$cats['categories'][] = $row;
			}	
		}
		//si existe filter_category_parent regresamos los datos de la categoria padre
		if(isset($params['filter_category_parent'])){
			if($params['filter_category_parent']!=0){
				$res_cat2 = $this->db->query("SELECT id, code_name, name FROM category WHERE id = ".$params['filter_category_parent']);
				$data_cat2 = $res_cat2->row_array();
				$cats["id"] = $data_cat2["id"];
				$cats["name"] = $data_cat2['name'] != ''? $data_cat2['name']: lang($data_cat2['code_name']);
			}
		}
		
		return $cats;
	}
	
	
	public function recursivo($id){
		$response = array();
		
		$res = $this->db->query("SELECT id, code_name, name FROM category WHERE category_parent = ".$id);
		foreach($res->result_array() as $row){
			if($row['name'] != '')
				unset($row['code_name']);
			elseif($row['code_name'] != ''){
				$row['name'] = lang($row['code_name']);
				unset($row['code_name']);
			}
			
			//obtenemos los atributos de cada categoria
			$row['attributes'] = $this->getAttrCat($row['id']);
				
			$row['categories'] = $this->recursivo($row['id']);
			
			$response[] = $row;
		}
		
		return $response;
	}
	
	/**
	 * Regresa un array con los atributos de una categoria en particular
	 * @param int $id. id de la categoria
	 */
	private function getAttrCat($id){
		$response = array();
		$res_attr = $this->db->query("SELECT a.id, a.code_name, a.name 
									FROM category AS c INNER JOIN cat_attr AS ca ON c.id = ca.cat_id 
										INNER JOIN attrs AS a ON a.id = attr_id 
									WHERE c.id = ".$id);
		foreach($res_attr->result_array() as $row){
			if($row['name'] != '')
				unset($row['code_name']);
			elseif($row['code_name'] != ''){
				$row['name'] = lang($row['code_name']);
				unset($row['code_name']);
			}
			$response[] = $row;
		}
		return $response;
	}
	
	
	/**
	 * Genera los filtros de la consulta sql para realizar la busqueda en los atributos
	 * @param array $params.Contiene los parametros que se enviaron por post y get
	 */
	private function getQuery(&$params){
		$sql = isset($params['filter_category_id'])? 
					($params['filter_category_id']!=''? " WHERE id = '".$params['filter_category_id']."'": '') 
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
		$sql .= isset($params['filter_category_parent'])? 
					($params['filter_category_parent']!=0? 
						($sql!=''? ' AND ':' WHERE ')."category_parent = '".$params['filter_category_parent']."'"
					: '') 
				: '';
		return $sql;
	}
}
?>