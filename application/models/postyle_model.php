<?php
class Postyle_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
		$this->load->model('comments_model');
		$this->load->model('user_permissions_model');
	}
	
	/**
	 * Agrega un postyle con imagenes y etiquetas.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	public function savePostyle($params, $images){
		$params['ocation_id'] = isset($params['ocation_id'])? 
				($params['ocation_id']!=''? $params['ocation_id']: 'NULL'): 'NULL';
		//si biene el ocation_label se inserta el ocation
		if(isset($params["ocation_label"])){
			if($params["ocation_label"]!=''){
				$this->load->model('ocation_model');
				$params["name"] = $params["ocation_label"];
				$params['ocation_id'] = $this->ocation_model->saveOcation($params, true);
			}
		}
		
		$params['location_lat'] = isset($params['location_lat'])? 
				($params['location_lat']!=''? $params['location_lat']: ''): '';
		$params['location_lon'] = isset($params['location_lon'])? 
				($params['location_lon']!=''? $params['location_lon']: ''): '';
		$params['description'] = isset($params['description'])? 
				($params['description']!=''? $params['description']: ''): '';
		
		if($params['ocation_id'] != 'NULL'){
			$res_val = $this->db->query("SELECT id FROM ocation WHERE id = ".$params['ocation_id']);
			if($res_val->num_rows() < 1)
				return -2;
		}
		
		//se inserta el postyle
		$this->db->query("INSERT INTO postyles (user_id, description, ranking_id, ocation_id, lat, lon) 
			VALUES ('".$params['user_id']."', '".$params['description']."', NULL, 
					".$params['ocation_id'].", '".$params['location_lat']."', '".$params['location_lon']."')");
		$res_post = $this->db->query("SELECT id FROM postyles ORDER BY id DESC LIMIT 1");
		$data_post = $res_post->row();
		
		//vector de respuesta
		$response = array(
			"postyle_id" => $data_post->id,
			"images" => array() 
		);
		
		//se guarda la imagen en la bd
		$img = null;
		foreach($images as $imgs){
			if(!isset($imgs['code'])){
				$img = $imgs[0];
				
				$id_img = $this->image_model->insertImage($img['file']);
				$this->db->query("INSERT INTO postyle_imgs (image_id, size, postyle_id) 
					VALUES (".$id_img.", '".$img['file']['size']."', ".$data_post->id.");");
			}else
				$response['images'][] = array(
					'code' => $imgs['code'],
					'message' => $imgs['message'],
					'file' => $imgs['file']
				);
		}
		
		//se guardan las tags de items
		if(isset($params['items_id'])){
			if(count($params['items_id'])>0){
				foreach($params['items_id'] as $key => $item){
					$res_val = $this->db->query("SELECT id FROM items WHERE id = ".$item);
					if($res_val->num_rows() > 0){
						$this->db->query("INSERT INTO postyle_items (postyle_id, item_id) 
							VALUES (".$data_post->id.", ".$item.");");
						$this->db->query("INSERT INTO postyle_tags (description, width, height, x, y, postyle_id, item_id) 
							VALUES ('".$params['tag_description'][$key]."', '".$params['tag_width'][$key]."', 
									'".$params['tag_height'][$key]."', '".$params['tag_x'][$key]."', '".$params['tag_y'][$key]."', 
									".$data_post->id.", ".$item.");");
						
						
						try{//Agregamos las imagenes de los tags a los items
							if($img!=null){
								$conff = array(
									"x" => ceil($params['tag_x'][$key]*$img['file']['file_width']/100),
									"y" => ceil($params['tag_y'][$key]*$img['file']['file_height']/100),
									"width" => ceil($params['tag_width'][$key]*$img['file']['file_width']/100),
									"height" => ceil($params['tag_height'][$key]*$img['file']['file_height']/100),
								);
								$this->load->library('my_upload');
								
								$source_image = UploadFile::pathBig().$img['file']['file_name'];
								$new_image = UploadFile::pathTemp().(md5(rand(1, 999).$img['file']['file_name']).
									".".$this->my_upload->getFormat($img['file']['file_name']));
								
								$path_file = $this->my_upload->cropImg($new_image, $source_image, $conff["width"], $conff["height"], $conff);
								$carga_url = Sys::$url_base.$path_file;
								
								$conf_url = array(
									'resize' => array('b','m','s')
								);
								$imagess = $this->my_upload->upload($carga_url, $conf_url);
								foreach($imagess as $imgss){ 	//se insertan las imagenes en los items
									$id_img = $this->image_model->insertImage($imgss[0]['file']);
									$this->db->query("INSERT INTO items_imgs (image_id, is_primary, size, item_id) 
										VALUES (".$id_img.", '0', '".$imgss[0]['file']['size']."', ".$item.");");
								}
							}
						}catch(Exception $e){}
					}
				}
			}
		}
		
		//se guardan las tags de usuarios
		if(isset($params['users_id'])){
			if(count($params['users_id'])>0){
				foreach($params['users_id'] as $key => $item){
					$res_val = $this->db->query("SELECT id FROM users WHERE id = ".$item);
					if($res_val->num_rows() > 0){
						$this->db->query("INSERT INTO postyle_tags_users (description, width, height, x, y, postyle_id, user_id) 
							VALUES ('".$params['tag_usrdescription'][$key]."', '".$params['tag_usrwidth'][$key]."', 
									'".$params['tag_usrheight'][$key]."', '".$params['tag_usrx'][$key]."', '".$params['tag_usry'][$key]."', 
									".$data_post->id.", ".$item.");");
					}
				}
			}
		}
		
		//verificamos si tiene permisos para que se publique en otros lados
		$this->user_permissions_model->publish($params, array(
			array(
				"method" => "conf_publish_facebook_postyle",
				"action" => "postyle",
				"obj_id" => $data_post->id,
				"opc"    => "add"
			),
			array(
				"method_conf" 	=> "conf_publish_twitter",
				"method" 		=> "conf_publish_twitter_postyle",
				"action" 		=> "postyle",
				"obj_id" 		=> $data_post->id
			)
		));
		//Enviar notificaciones via email
		$param = array(
			'token_user_id' => $params['user_id'],
			'action' => '0',
		);
		$this->load->model('notification_model');
		$this->notification_model->sendNotifications($param);
		
		return $response;
	}
	
	
	/**
	 * Actualiza los datos de una postyle.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	/*public function updatePostyle($params, $images){
		$res_postyle = $this->db->query("SELECT id, user_id, description, ranking_id, ocation_id 
			FROM postyles WHERE id = ".$params['postyle_id']." AND enable = 1");
		if($res_postyle->num_rows() > 0){
			$data_post = $res_postyle->row();
			//Verifica si el usuario puede modificar el postyle
			if($data_post->user_id == $params['user_id']){
				//se actualiza el postyle
				$sql = $this->getQueryUpdatePost($params);
				if($sql != '')
					$this->db->query("UPDATE postyles SET ".$sql." WHERE id = ".$data_post->id);
				
				//vector de respuesta
				$response = array(
					"postyle_id" => $data_post->id,
					"images" => array() 
				);
				//se guarda la imagen en la bd. Si llega una imagen la anterior se remplazara por esta
				foreach($images as $imgs){
					if(!isset($imgs['code'])){
						$img = $imgs[0];
						
						$this->db->query("DELETE FROM postyle_imgs WHERE postyle_id = ".$data_post->id);
						$id_img = $this->image_model->insertImage($img['file']);
						$this->db->query("INSERT INTO postyle_imgs (image_id, size, postyle_id) 
							VALUES (".$id_img.", '".$img['file']['size']."', ".$data_post->id.");");
					}else
						$response['images'][] = array(
							'code' => $imgs['code'],
							'message' => $imgs['message'],
							'file' => $imgs['file']
						);
				}
				
				//se guardan las tags 
				if(count($params['items_id'])>0){
					//eliminamos las tags que no vengan en el vector
					$res_cav = $this->db->query("SELECT * FROM postyle_tags 
						WHERE postyle_id = ".$data_post->id
					);
					foreach($res_cav->result_array() as $row){
						$bad = true;
						foreach($params['items_id'] as $key => $item){
							if($row['item_id'] == $item && $row['description'] == $params['tag_description'][$key] && 
									$row['width'] == $params['tag_width'][$key] && $row['height'] == $params['tag_height'][$key] && 
									$row['x'] == $params['tag_x'][$key] && $row['y'] == $params['tag_y'][$key])
								$bad = false;
						}
						if($bad){
							$this->db->query("DELETE FROM postyle_items 
								WHERE postyle_id = ".$row['postyle_id']." AND item_id = ".$row['item_id']);
							$this->db->query("DELETE FROM postyle_tags WHERE id = ".$row['id']);
						}
					}
					
					//insertamos los tags
					foreach($params['items_id'] as $key => $item){
						$res = $this->db->query("SELECT * FROM postyle_tags 
							WHERE postyle_id = ".$data_post->id." AND item_id = ".$item);
						if($res->num_rows() < 1){
							$res_val = $this->db->query("SELECT id FROM items WHERE id = ".$item);
							if($res_val->num_rows() > 0){
								$this->db->query("INSERT INTO postyle_items (postyle_id, item_id) 
									VALUES (".$data_post->id.", ".$item.");");
								$this->db->query("INSERT INTO postyle_tags (description, width, height, x, y, postyle_id, item_id) 
									VALUES ('".$params['tag_description'][$key]."', '".$params['tag_width'][$key]."', 
											'".$params['tag_height'][$key]."', '".$params['tag_x'][$key]."', '".$params['tag_y'][$key]."', 
											".$data_post->id.", ".$item.");");
							}
							$res_val->free_result();
						}
						$res->free_result();
					}
				}
				
				//verificamos si tiene permisos para que se publique en otros lados
				$this->user_permissions_model->publish($params, array(
					array(
						"method" => "conf_publish_facebook_postyle",
						"action" => "postyle",
						"obj_id" => $data_post->id,
						"opc"    => "update"
					)
				));
				
				return $response;
			}
			return -1; //el usuario no tiene permisos para editar la tienda
		}
		return -2; //no se encontro la tienda
	}*/
	
	private function getQueryUpdatePost(&$params){
		$sql = '';
		if(isset($params['description'])){
			if($params['description']!='')
				$sql .= ($sql!=''? ', ': '')."description = '".$params['description']."'";
		}
		if(isset($params['ocation_id'])){
			if($params['ocation_id']!=''){
				$res_val = $this->db->query("SELECT id FROM ocation WHERE id = ".$params['ocation_id']);
				if($res_val->num_rows() > 0)
					$sql .= ($sql!=''? ', ': '')."ocation_id = '".$params['ocation_id']."'";
			}
		}
		if(isset($params['location_lat'])){
			if($params['location_lat']!='')
				$sql .= ($sql!=''? ', ': '')."lat = '".$params['location_lat']."'";
		}
		if(isset($params['location_lon'])){
			if($params['location_lon']!='')
				$sql .= ($sql!=''? ', ': '')."lon = '".$params['location_lon']."'";
		}
		return $sql;
	}
	
	
	/**
	 * Obtiene una lista de postyles, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function getPostyles($params){
		$sql = $this->getQuery($params);
		$query = Sys::pagination("
			SELECT p.id, u.id AS user_id, u.name AS user_name, u.last_name AS user_last_name, p.date_added,  
				p.description, r.code_name AS rank, o.code_name AS ocation_code, 
				o.name AS ocation_name, p.lat, p.lon, is_my_friend(".$params["user_id"].", 0, 'postyle', p.id) AS can_be_deleted 
			FROM postyles AS p INNER JOIN users AS u ON u.id = p.user_id LEFT JOIN rankings AS r ON r.id = p.ranking_id 
				LEFT JOIN ocation AS o ON o.id = p.ocation_id
			WHERE p.enable = 1".$sql, $params, true);
		
		$post = array('postyles' => array(), "total_rows" => $query["total_rows"]);
		
		$res_post = $this->db->query($query["query"]);
		if($res_post->num_rows() > 0){
			Sys::loadLanguage(Sys::$idioma_load, 'ocations');
			Sys::loadLanguage(Sys::$idioma_load, 'rankings');
			$this->load->model('like_model');
			
			foreach($res_post->result_array() as $row){
				$row["i_like"] = $this->like_model->like_unlike(array(
					"table"		=> "users_like_postyle",
					"field"		=> "postyle_id",
					"field_val"	=> $row["id"],
					"user_id"	=> $params["user_id"]
				));
				//validamos cuando llegue el filtro de items, que los postyles tengan almenos uno de los items
				$badd = true;
				if(isset($params['filter_items'])){
					if(is_array($params['filter_items'])){
						$aux = false;
						foreach($params['filter_items'] as $item){
							$aux = true;
							$res_item = $this->db->query("SELECT Count(*) AS num 
									FROM postyle_tags 
									WHERE item_id = ".$item." AND postyle_id = ".$row['id']);
							$data_item = $res_item->row();
							$res_item->free_result();
							if($data_item->num > 0){
								$aux = false;
								break;
							}
						}
						if($aux)
							$badd = false;
					}
				}
				
				/*//validamos si el usuario quien creeo el postyle es el logeado o si es un amigo del logeado
				$res_usr = $this->db->query("SELECT Count(*) AS num 
						FROM postyles AS p INNER JOIN friends AS f ON p.user_id = f.friend_id 
						WHERE f.user_id = ".$params['user_id']." AND p.id = ".$row['id']);
				$data_usr = $res_usr->row();
				$res_usr->free_result();
				if(($data_usr->num > 0 || $params['user_id'] == $row['user_id']) && $badd==true){*/
					$row['rank'] = lang($row['rank']);
					if($row['ocation_name'] != '')
						unset($row['ocation_code']);
					elseif($row['ocation_code'] != ''){
						$row['ocation_name'] = lang($row['ocation_code']);
						unset($row['ocation_code']);
					}else
						$row['ocation_name'] = '---';
					
					//se obtienen la imagen del usuario
					$res_imgs = $this->db->query("SELECT bi.id, i.file_name, i.file_type, i.file_size 
						FROM users_imgs AS bi INNER JOIN images AS i ON i.id = bi.image_id 
						WHERE bi.user_id = ".$row['user_id']." AND bi.is_primary = 1");
					$row['user_images'] = array();
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
						$row['user_images'][] = $imgs;
					}
					
					//se obtienen la imagen del postyle
					$res_imgs = $this->db->query("SELECT bi.id, i.file_name, i.file_type, i.file_size 
						FROM postyle_imgs AS bi INNER JOIN images AS i ON i.id = bi.image_id 
						WHERE bi.postyle_id = ".$row['id']." AND bi.enable = 1");
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
					
					//se obtienen los tags de items para postyle
					$res_tags = $this->db->query("SELECT i.label AS item_label, i.id AS item_id, pt.description, pt.width, pt.height, 
						pt.x, pt.y, b.id AS brand_id, b.name AS brand_name, nums_likes('items', i.id, 1) AS nums_likes, 
						IFNULL(uli.`like`, 0) AS `like`, (pt.width * pt.height) AS area 
					FROM postyle_tags AS pt INNER JOIN items AS i ON i.id = pt.item_id LEFT JOIN brands AS b ON b.id = i.brand_id 
						LEFT JOIN (SELECT `like`, item_id FROM users_like_items WHERE user_id = ".$params['user_id'].") AS uli 
						ON i.id = uli.item_id 
					WHERE pt.postyle_id = ".$row['id']);
					$row['tags'] = array();
					
					foreach($res_tags->result_array() as $tag){
						$row['tags'][] = $tag;
					}
					
					//se obtienen los tags de usuarios para postyle
					$res_tags = $this->db->query("SELECT Concat(i.name, ' ', IFNULL(i.last_name, '')) AS user_name, i.status AS user_status, 
						i.id AS user_id, pt.description, pt.width, pt.height, pt.x, pt.y, (pt.width * pt.height) AS area
					FROM postyle_tags_users AS pt INNER JOIN users AS i ON i.id = pt.user_id  
					WHERE pt.postyle_id = ".$row['id']);
					
					foreach($res_tags->result_array() as $tag){
						$row['tags'][] = $tag;
					}
					$row['tags'] = Sys::ordenarMultiArray($row['tags'], 'area', 'DESC');
					
					//obtenemos los comentarios del postyle
					$params['return_comments'] = isset($params['return_comments'])? $params['return_comments']: '0';
					if($params['return_comments'] == '1' && isset($params['filter_postyle_id'])){
						$this->comments_model->table = 'postyle_comments';
						$this->comments_model->field = 'postyle_id';
						$row['comments'] = $this->comments_model->getComments($params, 'filter_postyle_id');
					}
					
					$post['postyles'][] = $row;
				/*}else 
					$post['total_rows']--;*/
			}
		}
		return $post;
	}
	
	/**
	 * Genera los filtros de la consulta sql para realizar la busqueda en las tiendas
	 * @param array $params.Contiene los parametros que se enviaron por post y get
	 */
	private function getQuery(&$params){
		$sql = '';
		$sql .= isset($params['filter_postyle_id'])? 
					($params['filter_postyle_id']!=''? 
						" AND p.id = ".$params['filter_postyle_id']
					: '') 
				: '';
		/*$sql .= isset($params['filter_user_id'])? 
					($params['filter_user_id']!=''? 
						" AND p.user_id = ".$params['filter_user_id']
					: " AND p.user_id = ".$params['user_id']) 
				: " AND p.user_id = ".$params['user_id'];*/
		$sql .= isset($params['filter_ranking_id'])? 
					($params['filter_ranking_id']!=''? 
						" AND r.id = ".$params['filter_ranking_id']
					: '') 
				: '';
		$sql .= isset($params['filter_ocation_id'])? 
					($params['filter_ocation_id']!=''? 
						" AND o.id = ".$params['filter_ocation_id']
					: '') 
				: '';
		$sql .= isset($params['filter_location_lat'])? 
					($params['filter_location_lat']!=''? 
						" AND p.lat = ".$params['filter_location_lat']
					: '') 
				: '';
		$sql .= isset($params['filter_location_lon'])? 
					($params['filter_location_lon']!=''? 
						" AND p.lon = ".$params['filter_location_lon']
					: '') 
				: '';
		
				
		if(isset($params['filter_user_id'])){ 
			if($params['filter_user_id']!=''){ 
				$sql .= " AND p.user_id ".$this->getQueryFriend($params, $params['filter_user_id']);
			}else{
				$sql .= " AND p.user_id ".$this->getQueryFriend($params, $params['user_id']);
			}
		}elseif(!isset($params["flt_no"])){
			$sql .= " AND p.user_id ".$this->getQueryFriend($params, $params['user_id']);
		}
		
		
		$order = isset($params['filter_order'])? $params['filter_order']: '';
		$sql .= " ORDER BY date_added ".(mb_strtolower($order, 'UTF-8')=='desc'? 'DESC': 'ASC');

		return $sql;
	}
	
	private function getQueryFriend(&$params, $id){
		$users_id = '';
		if(isset($params["filter_both"])){
			if($params["filter_both"] == 1){
				$res_usrf = $this->db->query("SELECT user_id, friend_id 
					FROM friends WHERE user_id = ".$id." AND status = 1");
				foreach($res_usrf->result_array() as $row){
					$users_id .= ','.$row["friend_id"];
				}
				$users_id = " IN (".$id.$users_id.")";
			}else
				$users_id = " = ".$id;
		}else
			$users_id = " = ".$id;
		return $users_id;
	}
	
	
	
	/**
	 * habilita o deshabilita un postyle
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function disable(&$params){
		$res_post = $this->db->query("SELECT * FROM postyles WHERE id = ".$params['postyle_id']);
		if($res_post->num_rows() > 0){
			$data_post = $res_post->row();
			//Verifica si el usuario puede realizar esta accion
			if($data_post->user_id == $params['user_id']){
				$this->db->query("UPDATE postyles SET enable=".$params['enable']." WHERE id = ".$params['postyle_id']);
				
				if($params['enable'] == '0'){
					//verificamos si tiene permisos para que se publique en otros lados
					$this->user_permissions_model->publish($params, array(
						array(
							"method" => "conf_publish_facebook_postyle",
							"action" => "postyle",
							"obj_id" => $data_post->id,
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
	 * Agrega o actualiza un ranking de un postyle
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function rank(&$params){
		$res_val = $this->db->query("SELECT id FROM postyles WHERE id = ".$params['postyle_id']);
		if($res_val->num_rows() < 1)
			return -1;
		$res_val = $this->db->query("SELECT id FROM rankings WHERE id = ".$params['ranking_id']);
		if($res_val->num_rows() < 1)
			return -2;
		
		$res_post = $this->db->query("SELECT * FROM postyle_qualify 
			WHERE user_id = ".$params['user_id']." AND postyle_id = ".$params['postyle_id']);
		if($res_post->num_rows() > 0){
			$this->db->query("UPDATE postyle_qualify SET ranking_id=".$params['ranking_id']." 
				WHERE user_id = ".$params['user_id']." AND postyle_id = ".$params['postyle_id']);
		}else{
			$this->db->query("INSERT INTO postyle_qualify (user_id, postyle_id, ranking_id) 
				VALUES (".$params['user_id'].", ".$params['postyle_id'].", ".$params['ranking_id'].")");
		}
		$this->rank_triggers($params['postyle_id']);
		
		return 0;
	}
	
	/**
	 * Metodo que simula los triggers para actualizar el rank
	 * @param unknown_type $postyle_id
	 */
	private function rank_triggers($postyle_id){
		$res = $this->db->query("SELECT user_id, ranking_id FROM postyle_qualify WHERE postyle_id = ".$postyle_id);
		$suma = 0;
		foreach($res->result_array() as $row){
			$res_rank = $this->db->query("SELECT value FROM rankings WHERE id=".$row['ranking_id']);
			$data_rank = $res_rank->row();
			$suma += $data_rank->value;
			$res_rank->free_result();
		}
		
		$id_rank = round($suma/$res->num_rows());
		$this->db->query("UPDATE postyles SET ranking_id = ".$id_rank." WHERE id = ".$postyle_id);
		return $id_rank;
	}
	
	
	/**
	 * SERVICIOS DE COMMENT POSTYLE
	 * Agrega un comentario a un postyle
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function comment(&$params){
		$res_post = $this->db->query("SELECT * FROM postyles WHERE id = ".$params['postyle_id']);
		if($res_post->num_rows() > 0){
			$id_comment = $this->comments_model->insertComment($params);
			$this->db->query("INSERT INTO postyle_comments (postyle_id, comment_id) 
				VALUES ('".$params['postyle_id']."', '".$id_comment."');");
			
			//verificamos si tiene permisos para que se publique en otros lados
			$this->user_permissions_model->publish($params, array(
				array(
					"method" => "conf_publish_facebook_postyle",
					"action" => "comment",
					"obj_id" => array(
									'table' => 'postyle_comments',
									'table2' => 'postyles',
									'field' => 'postyle_id',
									'key_param' => '',
									'postyle_id' => $params['postyle_id'],
									'comment_id' => $id_comment
								),
					"opc"    => "add"
				)/*,
				array(
					"method_conf" 	=> "conf_publish_twitter",
					"method" 		=> "conf_publish_twitter_postyle",
					"action" 		=> "comment",
					"obj_id" 		=> array(
											'table' => 'postyle_comments',
											'table2' => 'postyles',
											'field' => 'postyle_id',
											'key_param' => '',
											'postyle_id' => $params['postyle_id'],
											'comment_id' => $id_comment
										)
				)*/
			));
			//Enviar notificaciones via email
			$param = array(
				'token_user_id' => $params['user_id'],
				'action' => '1',
			);
			$this->load->model('notification_model');
			$this->notification_model->sendNotifications($param);
			
			return true;
		}
		return false;
	}
	
	/**
	 * habilita o deshabilita un comentario de un postyle
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function disable_comment(&$params){
		$res_post = $this->db->query("SELECT pc.id, p.user_id AS postyle_user_id, c.users_id AS comment_user_id 
			FROM postyles AS p INNER JOIN postyle_comments AS pc ON p.id = pc.postyle_id 
				INNER JOIN comments AS c ON c.id = pc.comment_id 
			WHERE pc.id = ".$params['comment_id']." AND p.enable = 1");
		if($res_post->num_rows() > 0){
			$data_post = $res_post->row();
			//Verifica si el usuario puede realizar esta accion
			if($data_post->postyle_user_id == $params['user_id'] || $data_post->comment_user_id == $params['user_id']){
				$this->db->query("UPDATE postyle_comments SET enable=".$params['enable']." WHERE id = ".$params['comment_id']);
				
				if($params['enable'] == '0'){
					//verificamos si tiene permisos para que se publique en otros lados
					$this->user_permissions_model->publish($params, array(
						array(
							"method" => "conf_publish_facebook_postyle",
							"action" => "comment",
							"obj_id" => array(
											'table' => 'postyle_comments',
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
	 * Obtiene la lista de comentarios de un postyle
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function get_comments(&$params){
		$this->comments_model->table = 'postyle_comments';
		$this->comments_model->field = 'postyle_id';
		$comments = $this->comments_model->getComments($params);
		return $comments;
	}
}
?>