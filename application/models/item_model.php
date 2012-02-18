<?php
class Item_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
		$this->load->model('comments_model');
		$this->load->model('user_permissions_model');
	}
	
	/**
	 * Agrega un item con imagens, valores de atributos.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	public function saveItem($params, $images){
		$sql = array('field' => '', 'value' => '');
		if(isset($params['brand_id'])){
			if($params['brand_id']!=''){
				$res = $this->db->query("SELECT id FROM brands WHERE id = ".$params['brand_id']);
				if($res->num_rows() > 0){
					$sql['field'] = ", brand_id";
					$sql['value'] = ", '".$params['brand_id']."'";
				}else 
					return -1; //no existe la marca
			}
		}elseif(isset($params['brand_name'])){
			if($params['brand_name']!=''){
				$this->load->model('brand_model');
				$params['name'] = $params['brand_name'];
				$data = $this->brand_model->saveBrand($params, array());
				
				if(isset($data["brand_id"])){
					$sql['field'] = ", brand_id";
					$sql['value'] = ", '".$data["brand_id"]."'";
				}else 
					return -1; //no existe la marca
			}
		}
		
		if(isset($params['bought_in'])){
			if($params['bought_in']!=''){
				$res = $this->db->query("SELECT id FROM store_locations WHERE id = ".$params['bought_in']);
				if($res->num_rows() > 0){
					$sql['field'] .= ", bought_in";
					$sql['value'] .= ", '".$params['bought_in']."'";
				}else
					return -2; //no existe el store_location
			}
		}elseif(isset($params["bought_label"]) && isset($params["bought_lat"]) && isset($params["bought_lon"])){ //se agrega el store
			if($params['bought_label']!='' && $params['bought_lat']!='' && $params['bought_lon']!=''){
				$this->load->model('store_model');
				$params['name'] = $params['bought_label'];
				$params['lat'] = $params['bought_lat'];
				$params['lon'] = $params['bought_lon'];
				$data = $this->store_model->saveStore($params, array());
				
				if(isset($data["store_location_id"])){
					$sql['field'] .= ", bought_in";
					$sql['value'] .= ", '".$data["store_location_id"]."'";
				}else 
					return -2; //no existe el store_location
			}
		}
		
		if(isset($params['price'])){ //si biene el precio se agrega
			if($params['price']!=''){
				$sql['field'] .= ", price";
				$sql['value'] .= ", '".$params['price']."'";
			}
		}
		
		//se inserta el item
		$this->db->query("INSERT INTO items (label".$sql['field'].", date_added) 
			VALUES ('".$params['label']."'".$sql['value'].", '".date("Y-m-d H:i:s")."')");
		$res_item = $this->db->query("SELECT id FROM items ORDER BY id DESC LIMIT 1");
		$data_item = $res_item->row();
		//insertamos la relacion del item y el usuario que lo creo
		$this->db->query("INSERT INTO items_users (item_id, user_id) 
			VALUES (".$data_item->id.", '".$params['user_id']."')");
		
		//vector de respuesta
		$response = array(
			"item_id" => $data_item->id,
			"images" => array() 
		);
		//si hay imagenes se guardan en la bd
		$primary = 1;
		if(count($images)>0){
			foreach($images as $imgs){
				if(!isset($imgs['code'])){
					$img = $imgs[0];
					
					$id_img = $this->image_model->insertImage($img['file']);
					$this->db->query("INSERT INTO items_imgs (image_id, is_primary, size, item_id) 
						VALUES (".$id_img.", '".$primary."', '".$img['file']['size']."', ".$data_item->id.");");
					
					$primary = 0;
				}else
					$response['images'][] = array(
						'code' => $imgs['code'],
						'message' => $imgs['message'],
						'file' => $imgs['file']
					);
			}
		}
		//si hay atributos y valores los agregamos
		if(isset($params['cat_attr_id'])){
			if(count($params['cat_attr_id'])>0){
				foreach($params['cat_attr_id'] as $key => $item){
					$res = $this->db->query("SELECT id FROM cat_attr WHERE id = ".$item);
					if($res->num_rows() > 0){
						$this->db->query("INSERT INTO item_cat_attr_val (value, item_id, cat_attr_id) 
							VALUES ('".$params['value'][$key]."', ".$data_item->id.", '".$item."');");
					}
				}
			}
		}
		
		if($primary == 0){
			//verificamos si tiene permisos para que se publique en otros lados
			$this->user_permissions_model->publish($params, array(
				array(
					"method" => "conf_publish_facebook_item",
					"action" => "item",
					"obj_id" => $data_item->id,
					"opc"    => "add"
				),
				array(
					"method_conf" 	=> "conf_publish_twitter",
					"method" 		=> "conf_publish_twitter_item",
					"action" 		=> "item",
					"obj_id" 		=> $data_item->id
				)
			));
		}
		
		return $response;
	}
	
	
	
	/**
	 * Actualiza los datos de un item.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	public function updateItem($params, $images){
		$res_item = $this->db->query("SELECT i.id, i.label, i.brand_id, i.bought_in, i.verified, iu.user_id AS created_by_user_id 
			FROM items AS i INNER JOIN items_users AS iu ON i.id = iu.item_id 
			WHERE i.id = ".$params['item_id']);
		if($res_item->num_rows() > 0){
			$data_item = $res_item->row();
			//Verifica si el usuario puede modificar el item
			if($data_item->verified==0 && $data_item->created_by_user_id == $params['user_id']){				
				$sql = '';
				if(isset($params['brand_id'])){
					if($params['brand_id']!=''){
						$res = $this->db->query("SELECT id FROM brands WHERE id = ".$params['brand_id']);
						if($res->num_rows() > 0){
							$sql = ", brand_id = '".$params['brand_id']."'";
						}else 
							return -3; //no existe la marca
					}
				}
				if(isset($params['bought_in'])){
					if($params['bought_in']!=''){
						$res = $this->db->query("SELECT id FROM store_locations WHERE id = ".$params['bought_in']);
						if($res->num_rows() > 0){
							$sql .= ", bought_in = '".$params['bought_in']."'";
						}else
							return -4; //no existe el store_location
					}
				}
				if(isset($params['price'])){ //si biene el precio se agrega
					if($params['price']!=''){
						$sql .= ", price = '".$params['price']."'";
					}
				}
				
				//se actualiza el item
				$this->db->query("UPDATE items SET label = '".$params['label']."'".$sql." WHERE id = ".$data_item->id);
				
				//vector de respuesta
				$response = array(
					"item_id" => $data_item->id,
					"images" => array() 
				);
				//si hay imagenes se guardan en la bd
				if(count($images)>0){
					foreach($images as $imgs){
						if(!isset($imgs['code'])){
							$img = $imgs[0];
							
							$id_img = $this->image_model->insertImage($img['file']);
							$this->db->query("INSERT INTO items_imgs (image_id, is_primary, size, item_id) 
								VALUES (".$id_img.", '0', '".$img['file']['size']."', ".$data_item->id.");");
						}else
							$response['images'][] = array(
								'code' => $imgs['code'],
								'message' => $imgs['message'],
								'file' => $imgs['file']
							);
					}
				}
				//si hay atributos y valores los agregamos 
				if(count($params['cat_attr_id'])>0){
					//eliminamos los atributos y valores que no vengan en el vector
					$res_cav = $this->db->query("SELECT * FROM item_cat_attr_val 
						WHERE item_id = ".$data_item->id
					);
					foreach($res_cav->result_array() as $row){
						$bad = true;
						foreach($params['cat_attr_id'] as $key => $item){
							if($row['cat_attr_id'] == $item && $row['value'] == $params['value'][$key])
								$bad = false;
						}
						if($bad)
							$this->db->query("DELETE FROM item_cat_attr_val WHERE id = ".$row['id']);
					}
					
					//insertamos los valores de atributos
					foreach($params['cat_attr_id'] as $key => $item){
						$res = $this->db->query("SELECT * FROM item_cat_attr_val 
							WHERE item_id = ".$data_item->id." AND value = '".$params['value'][$key]."' 
								AND cat_attr_id = ".$item);
						if($res->num_rows() < 1)
							$this->db->query("INSERT INTO item_cat_attr_val (value, item_id, cat_attr_id) 
								VALUES ('".$params['value'][$key]."', ".$data_item->id.", '".$item."');");
					}
				}
				
				return $response;
			}
			return -1; //el usuario no tiene permisos para editar
		}
		return -2; //no se encontro el item
	}
	
	
	/**
	 * Obtiene una lista de items, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function getItems(&$params){
		Sys::loadLanguage(Sys::$idioma_load, 'country');
		Sys::loadLanguage(Sys::$idioma_load, 'regions');
		Sys::loadLanguage(Sys::$idioma_load, 'attrs');
		
		return $this->getItem($params);
	}
	/**
	 * Metodo complementario de getItems. En realidad es el metodo que hace todo el trabajo
	 * para obtener los items deacuerdo a los filtros.
	 * @param unknown_type $params
	 */
	private function getItem($params){
		//filtro postyle id
		$sql_postyle = isset($params['filter_postyle_id'])? 
					($params['filter_postyle_id']!=''? 
						" INNER JOIN postyle_items AS pi ON i.id = pi.item_id"
					: '') 
				: '';
		//obtienen las condiciones de la consulta
		$sql = $this->getQuery($params);
		//si get_light_result=1 regresa poquitos campos
		$get_light_result = '0';
		if(isset($params['get_light_result']))
			if($params['get_light_result']=='1')
				$get_light_result = '1';
				
		$query = Sys::pagination("
			SELECT i.id, i.label, IFNULL(i.price, 0) AS price, i.brand_id, i.bought_in, iu.user_id, IFNULL(uli.`like`, 0) AS `like`, 
     			IF(iu.user_id=".$params['token_user_id'].",1,0) AS is_mine 
			FROM items AS i INNER JOIN items_users AS iu ON i.id = iu.item_id 
     			LEFT JOIN (SELECT `like`, item_id FROM users_like_items WHERE user_id = ".$params['token_user_id'].") AS uli 
     			ON i.id = uli.item_id".$sql_postyle.$sql, $params, true);
		
		$items = array('items' => array(), "category" => array(), "total_rows" => $query["total_rows"]);
		
		$res_item = $this->db->query($query["query"]);
		if($res_item->num_rows() > 0){
			//si los filtros de geolocalizacion estan, se implementa ese filtro
			$filter_geo = false;
			if(isset($params['filter_lon'])){
				if($params['filter_lon'] != ''){
					$point = new gPoint();
					$point->setLongLat($params['filter_lon'], $params['filter_lat']);
					$point->convertLLtoTM();
					$punto_c = $point->getUTM();
					$filter_geo = true;
				}
			}
			//terminamos de filtrar los resultados
			foreach($res_item->result_array() as $row){
				$accept_row = true;
				
				//validamos si el usuario quien creeo el item es el logeado o si es un amigo del logeado
				/*$res_usr = $this->db->query("SELECT Count(*) AS num 
							FROM items_users AS iu INNER JOIN friends AS f ON iu.user_id = f.friend_id 
							WHERE f.user_id = ".$params['token_user_id']." AND iu.item_id = ".$row['id']." AND f.status = 1");
				$data_usr = $res_usr->row();
				$res_usr->free_result();
				
				if($data_usr->num > 0 || $params['token_user_id'] == $row['user_id']){*/
				if($get_light_result == '0'){
					//obtenemos los datos de la marca del item (si es que tiene)
					$row['brand_id'] = intval($row['brand_id']);
					$res_brand = $this->db->query("
						SELECT id, name, description 
						FROM brands 
						WHERE id = ".$row['brand_id']);
					$data_brand = $res_brand->row_array();
					
					//obtenemos los datos del store_location (si es que tiene)
					$row['bought_in'] = intval($row['bought_in']);
					$res_sl = $this->db->query("
						SELECT sl.id, sl.name, l.street_name, l.city, r.code_name AS state, l.postal_code, c.iso3 AS country, 
								l.lat, l.lon 
						FROM store_locations AS sl INNER JOIN locations AS l ON l.id = sl.location_id 
								LEFT JOIN regions AS r ON r.id = l.state LEFT JOIN country AS c ON c.id = l.contry 
						WHERE sl.id = ".$row['bought_in']);
					$data_sl = $res_sl->row_array();
					
					//filtros de geo
					if($filter_geo){
						if($res_sl->num_rows() > 0){
							$point->setLongLat($data_sl['lon'], $data_sl['lat']);
							$point->convertLLtoTM();
							$punto = $point->getUTM();
							$accept_row = $point->inCircle($punto['northing'], $punto['easting'], $params['filter_radio'], 
															$punto_c['northing'], $punto_c['easting']);
						}else
							$accept_row = false;
					}
					
					//si es true indica que la tienda si cumple con los filtros de geolocalizacion
					if($accept_row){
						//asignamos la marca al response si tiene
						$row['brand'] = array();
						if($res_brand->num_rows() > 0){
							$row['brand'] = $data_brand;
						}
						
						//asignamos el store_location al response
						$row['store'] = array();
						if($res_sl->num_rows() > 0){
							$data_sl['country'] = lang($data_sl['country']);
							$data_sl['state'] = lang($data_sl['state']);
							$row['store'] = $data_sl;
						}
						
						//se obtienen las imagenes del item
						$res_imgs = $this->db->query("SELECT bi.id, i.file_name, i.file_type, i.file_size, bi.is_primary 
							FROM items_imgs AS bi INNER JOIN images AS i ON i.id = bi.image_id 
							WHERE bi.item_id = ".$row['id']." AND bi.enable = 1");
						$row['images'] = array();
						foreach($res_imgs->result_array() as $imgs){
							$imgs['sizes'] = array(
								array(
									'url' => UploadFile::urlBig().$imgs['file_name'],
									'size' => 'B'
								),
								array(
									'url' => UploadFile::urlMedium().$imgs['file_name'],
									'size' => 'M'
								),
								array(
									'url' => UploadFile::urlSmall().$imgs['file_name'],
									'size' => 'SN'
								),
								array(
									'url' => UploadFile::urlSmallSquare().$imgs['file_name'],
									'size' => 'SS'
								)
							);
							$row['images'][] = $imgs;
						}
						
						//se obtienen los atributos de la marca
						$res_attr = $this->db->query("
								SELECT a.code_name, a.name, iav.value 
								FROM item_cat_attr_val AS iav INNER JOIN cat_attr AS ca ON ca.id = iav.cat_attr_id 
									INNER JOIN attrs AS a ON a.id = ca.attr_id 
								WHERE iav.item_id = ".$row['id']);
						$row['attributes'] = array();
						foreach($res_attr->result_array() as $attr){
							if($attr['name'] != '')
								unset($attr['code_name']);
							elseif($attr['code_name'] != ''){
								$attr['name'] = lang($attr['code_name']);
								unset($attr['code_name']);
							}
							$row['attributes'][] = $attr;
						}
						
						//obtenemos los comentarios del item
						$params['return_comments'] = isset($params['return_comments'])? $params['return_comments']: '0';
						if($params['return_comments'] == '1' && isset($params['filter_item_id'])){
							$this->comments_model->table = 'items_comments';
							$this->comments_model->field = 'item_id';
							$row['comments'] = $this->comments_model->getComments($params, 'filter_item_id');
						}
						
						//quitamos las columnas que no ocupamos
						unset($row['brand_id']);
						unset($row['bought_in']);
						
						
						$items['items'][] = $row;
						
						$res_imgs->free_result();
						$res_attr->free_result();
					}
					
					$res_brand->free_result();
					$res_sl->free_result();
				}else{
					unset($row['brand_id'], $row['bought_in'], $row['user_id'], $row['like'], $row['is_mine']);
					$items['items'][] = $row;
				}
				//}
			}
			
			
			/*//si existe el filtro de categoria el metodo se hace recursivo para obtener los items
			//de las categorias hijo
			if(isset($params['filter_category_id'])){
				if($params['filter_category_id'] != ''){
					$res = $this->db->query("SELECT id, code_name, name FROM category 
								WHERE category_parent = ".$params['filter_category_id']);
					foreach($res->result_array() as $rowcat){
						$params['filter_category_id'] = $rowcat['id'];
						$recur_cat = $this->getItem($params);
						
						foreach($recur_cat['items'] as $citem){
							if(count(Sys::buscarArray($items['items'], "id", $citem["id"])) == 0)
								$items['items'][] = $citem;
						}
					}
				}	
			}*/
		}
		
		//obtenemos las categorias hijas si el parametro filter_category esta
		if(isset($params['filter_category_id'])){
			if($params['filter_category_id'] != ''){
				//info de la categoria padre
				$res_cat = $this->db->query("SELECT id, code_name, name FROM category 
							WHERE id = ".$params['filter_category_id']);
				$data_cat = $res_cat->row_array();
				$data_cat["name"] = ($data_cat["code_name"]=='' || is_null($data_cat["code_name"]))?
					$data_cat["name"]: $data_cat["code_name"];
				$items["category"]["id"] = $data_cat["id"];
				$items["category"]["name"] = $data_cat["name"];
				
				$res_cat->free_result();
				
				//info de las categorias hijas
				$res = $this->db->query("SELECT id, code_name, name FROM category 
							WHERE category_parent = ".$params['filter_category_id']);
				foreach($res->result_array() as $rowcat){
					$rowcat["name"] = ($rowcat["code_name"]=='' || is_null($rowcat["code_name"]))?
						$rowcat["name"]: $rowcat["code_name"];
					unset($rowcat["code_name"]);
					$items["category"]["childs_categorys"][] = $rowcat;
				}
			}
		}
		
		return $items;
	}
	
	
	
	/**
	 * Elimina una imagen de un item, se desactiva no se elimina fisicamente
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function removeImage($params){
		$res_img = $this->db->query("SELECT ii.id, ii.image_id, iu.user_id, i.verified 
			FROM items AS i INNER JOIN items_imgs AS ii ON i.id = ii.item_id 
				INNER JOIN items_users AS iu ON i.id = iu.item_id 
			WHERE ii.id = ".$params['image_id']);
		if($res_img->num_rows() > 0){
			$data_img = $res_img->row();
			//Verifica si el usuario puede modificar el item
			if($data_img->verified==0 && $data_img->user_id == $params['user_id']){
				$res_img = $this->db->query("UPDATE items_imgs SET enable=".$params['enable']." WHERE id = ".$params['image_id']);
				return $params['enable'];
			}
			return -1;
		}
		return false;
	}
	
	
	/**
	 * SERVICIOS DE COMMENT Accesorios
	 * Agrega un comentario a los items
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function comment(&$params){
		$res_post = $this->db->query("SELECT id FROM items WHERE id = ".$params['item_id']);
		if($res_post->num_rows() > 0){
			$id_comment = $this->comments_model->insertComment($params);
			$this->db->query("INSERT INTO items_comments (item_id, comment_id) 
				VALUES ('".$params['item_id']."', '".$id_comment."');");
			
			//verificamos si tiene permisos para que se publique en otros lados
			$this->user_permissions_model->publish($params, array(
				array(
					"method" => "conf_publish_facebook_item",
					"action" => "comment",
					"obj_id" => array(
									'table' => 'items_comments',
									'table2' => 'items',
									'field' => 'item_id',
									'key_param' => '',
									'item_id' => $params['item_id'],
									'comment_id' => $id_comment
								),
					"opc"    => "add"
				)/*,
				array(
					"method_conf" 	=> "conf_publish_twitter",
					"method" 		=> "conf_publish_twitter_item",
					"action" 		=> "comment",
					"obj_id" 		=> array(
											'table' => 'items_comments',
											'table2' => 'items',
											'field' => 'item_id',
											'key_param' => '',
											'item_id' => $params['item_id'],
											'comment_id' => $id_comment
										)
				)*/
			));
			
			return true;
		}
		return false;
	}
	
	/**
	 * habilita o deshabilita un comentario de accesorios de un usuario
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function disable_comment(&$params){
		$res_post = $this->db->query("SELECT cc.id, c.users_id AS comment_user_id 
			FROM items_comments AS cc INNER JOIN comments AS c ON c.id = cc.comment_id 
			WHERE cc.id = ".$params['comment_id']);
		if($res_post->num_rows() > 0){
			$data_post = $res_post->row();
			//Verifica si el usuario puede realizar esta accion
			if($data_post->comment_user_id == $params['user_id']){
				$this->db->query("UPDATE items_comments SET enable=".$params['enable']." WHERE id = ".$params['comment_id']);
				
				if($params['enable'] == '0'){
					//verificamos si tiene permisos para que se publique en otros lados
					$this->user_permissions_model->publish($params, array(
						array(
							"method" => "conf_publish_facebook_item",
							"action" => "comment",
							"obj_id" => array(
											'table' => 'items_comments',
											'comment_id' => $params['comment_id']
										),
							"opc"    => "delete"
						)
					));
				}
				
				return $params['enable'];
			}
			return -1;
		}
		return false;
	}
	
	/**
	 * Obtiene la lista de comentarios de los accesorios de un usuario
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function get_comments(&$params){
		$this->comments_model->table = 'items_comments';
		$this->comments_model->field = 'item_id';
		$comments = $this->comments_model->getComments($params);
		return $comments;
	}
	
	
	
	
	/**
	 * Genera los filtros de la consulta sql para realizar la busqueda en los items
	 * @param array $params.Contiene los parametros que se enviaron por post y get
	 */
	private function getQuery(&$params){
		$sql = '';
		$cats = false;
		if(isset($params['filter_category_id'])){
			if($params['filter_category_id'] != ''){
				$sql = " INNER JOIN item_cat_attr_val AS iav ON i.id = iav.item_id 
     					INNER JOIN cat_attr AS ca ON ca.id = iav.cat_attr_id
					WHERE ca.cat_id = ".$params['filter_category_id'];
				$cats = true;
			}	
		}
		
		$sql .= isset($params['filter_item_id'])? 
					($params['filter_item_id']!=''? 
						($sql!=''? ' AND ':' WHERE ')."i.id = '".$params['filter_item_id']."'"
					: '') 
				: '';
		$sql .= isset($params['filter_label'])? 
					($params['filter_label']!=''? 
						($sql!=''? ' AND ':' WHERE ')."LOWER(i.label) LIKE LOWER('".$params['filter_label']."%')"
					: '') 
				: '';
		$sql .= isset($params['filter_store_location_id'])? 
					($params['filter_store_location_id']!=''? 
						($sql!=''? ' AND ':' WHERE ')."i.bought_in = '".$params['filter_store_location_id']."'"
					: '') 
				: '';
		$sql .= isset($params['filter_brand_id'])? 
					($params['filter_brand_id']!=''? 
						($sql!=''? ' AND ':' WHERE ')."i.brand_id = '".$params['filter_brand_id']."'"
					: '') 
				: '';
		$sql .= isset($params['filter_user_id'])? 
					($params['filter_user_id']!=''? 
						($sql!=''? ' AND ':' WHERE ')."iu.user_id = '".$params['filter_user_id']."'"
					: '') 
				: '';
		$sql .= isset($params['filter_postyle_id'])? 
					($params['filter_postyle_id']!=''? 
						($sql!=''? ' AND ':' WHERE ')."pi.postyle_id = '".$params['filter_postyle_id']."'"
					: '') 
				: '';
		
		//validar si el items es del usuario o de un amigo
		$only_my_items = isset($params['only_my_items'])? $params['only_my_items']: '0';
		if($only_my_items == '0')
			$sql .= ($sql!=''? ' AND ':' WHERE ')."(SELECT item_valid( ".$params["token_user_id"].", iu.user_id, i.id ))=1";
			
		if($cats)
			$sql .= " GROUP BY i.id";
			
		return $sql;
	}
}
?>