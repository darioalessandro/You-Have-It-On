<?php
class Brand_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
		$this->load->model('comments_model');
	}
	
	/**
	 * Agrega un brand.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	public function saveBrand($params, $images){
		$params['country'] = isset($params['country'])? 
									($params['country']!=''? $params['country']: Sys::$country_brand) 
								: Sys::$country_brand;
		$res_country = $this->db->query("SELECT id FROM country WHERE iso2 = LOWER('".$params['country']."')");
		if($res_country->num_rows() > 0){
			$data_country = $res_country->row();
			
			$params['description'] = isset($params['description'])? $params['description']: '';
			$params['verified'] = isset($params['verified'])? intval($params['verified']): 0;
			
			//se intenta insertar un location si vienen datos
			$this->load->model("store_model");
			$location_id = $this->store_model->insertLocation($params);
			$fiels = '';
			$fi_val = '';
			if($location_id != null){
				$fiels = ', headquarters';
				$fi_val = ', '.$location_id;
			}
			
			$this->db->query("INSERT INTO brands (name, description, contry_id, created_by_user_id, verified".$fiels.") 
				VALUES ('".$params['name']."', '".$params['description']."', ".$data_country->id.", 
						".$params['user_id'].", ".$params['verified'].$fi_val.");"); 
			$res_brand = $this->db->query("SELECT id FROM brands ORDER BY id DESC LIMIT 1");
			$data_brand = $res_brand->row();
			
			$response = array(
				"brand_id" => $data_brand->id,
				"images" => array() 
			);
			
			if(count($images)>0){
				$primary = 1;
				foreach($images as $imgs){
					if(!isset($imgs['code'])){
						$img = $imgs[0];
						//foreach($imgs as $key => $img){
						
						$id_img = $this->image_model->insertImage($img['file']);
						$this->db->query("INSERT INTO brands_imgs (image_id, is_primary, size, brand_id) 
							VALUES (".$id_img.", '".$primary."', '".$img['file']['size']."', ".$data_brand->id.");");
						//}
						$primary = 0;
					}else
						$response['images'][] = array(
							'code' => $imgs['code'],
							'message' => $imgs['message'],
							'file' => $imgs['file']
						);
				}
			}
			
			//se insertan los sitios webs
			if(isset($params['website'])){
				foreach($params['website'] as $site){
					if($site != ''){
						$site = strrpos($site, "http://")===false? 'http://'.$site: $site;
						$this->db->query("INSERT INTO websites (url) VALUES ('".$site."');");
						$res_website = $this->db->query("SELECT id FROM websites ORDER BY id DESC LIMIT 1");
						$data_website = $res_website->row();
						
						$this->db->query("INSERT INTO brand_websites (brand_id, website_id) 
								VALUES (".$data_brand->id.", ".$data_website->id.");");
					}
				}
			}
			
			//se insertan los numeros de telefono
			if(isset($params['phone_number'])){
				foreach($params['phone_number'] as $phone){
					if($phone != ''){
						$phone = explode(":", $phone);
						$tipo = '';
						$pnumber = '';
						if(isset($phone[1])){
							$tipo 		= intval($phone[0]);
							$pnumber 	= $phone[1];
						}else{
							$tipo 		= '5';
							$pnumber 	= $phone[0];
						}
						
						$this->db->query("INSERT INTO phone_numbers (phone, type) VALUES ('".$pnumber."', ".$tipo.");");
						$res_phone = $this->db->query("SELECT id FROM phone_numbers ORDER BY id DESC LIMIT 1");
						$data_phone = $res_phone->row();
						
						$this->db->query("INSERT INTO brand_phone_numbers (brand_id, phone_number_id) 
								VALUES (".$data_brand->id.", ".$data_phone->id.");");
					}
				}
			}
			
			//se insertan las categorias
			if(isset($params['brand_categories'])){
				foreach($params['brand_categories'] as $catego){
					$catego = intval($catego);
					$res_catego = $this->db->query("SELECT id FROM category WHERE id = ".$catego);
					if($res_catego->num_rows() > 0){
						$this->db->query("INSERT INTO brand_categories (brand_id, category_id) 
								VALUES (".$data_brand->id.", ".$catego.");");
					}
				}
			}
			
			return $response;
		}
		return false;
	}
	
	/**
	 * Actualiza los datos de un brand.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	public function updateBrand($params, $images){
		$res_bra = $this->db->query("SELECT id, contry_id, created_by_user_id, verified, headquarters FROM brands WHERE id = ".$params['brand_id']);
		if($res_bra->num_rows() > 0){
			//validar el pais, si no se deja el que tenia antes
			$data_bra = $res_bra->row();
			//Verifica si el usuario puede modificar el usuario
			if($data_bra->verified==0 && $data_bra->created_by_user_id == $params['user_id']){
				$country_id = $data_bra->contry_id;	
				if(isset($params['country'])){
					if($params['country']!=''){
						$res_country = $this->db->query("SELECT id FROM country WHERE iso2 = LOWER('".$params['country']."')");
						if($res_country->num_rows() < 1)
							return -1;
						$data_country = $res_country->row();
						$country_id = $data_country->id;	
					}
				}
				
				//se actualiza los datos de location
				$this->load->model("store_model");
				$this->store_model->updateLocation($params, $data_bra->headquarters);
				
				
				$params['description'] = isset($params['description'])? 
											($params['description']!=''? "'".$params['description']."'": 'description')
										: 'description';
				//se actualiza la info del brand
				$this->db->query("UPDATE brands SET name='".$params['name']."', description=".$params['description'].", 
					contry_id=".$country_id." WHERE id = ".$params['brand_id']); 
				
				//si hay imagenes se insertan a la bd
				$response = array(
					"brand_id" => $params['brand_id'],
					"images" => array() 
				);
				if(count($images)>0){
					foreach($images as $imgs){
						if(!isset($imgs['code'])){
							$img = $imgs[0];
							//foreach($imgs as $key => $img){
							$id_img = $this->image_model->insertImage($img['file']);
							$this->db->query("INSERT INTO brands_imgs (image_id, is_primary, size, brand_id) 
								VALUES (".$id_img.", '0', '".$img['file']['size']."', ".$params['brand_id'].");");
							//}
						}else
							$response['images'][] = array(
								'code' => $imgs['code'],
								'message' => $imgs['message'],
								'file' => $imgs['file']
							);
					}
				}
				
				
				//se insertan los sitios webs
				if(isset($params['website'])){
					foreach($params['website'] as $site){
						if($site != ''){
							$site = strrpos($site, "http://")===false? 'http://'.$site: $site;
							$this->db->query("INSERT INTO websites (url) VALUES ('".$site."');");
							$res_website = $this->db->query("SELECT id FROM websites ORDER BY id DESC LIMIT 1");
							$data_website = $res_website->row();
							
							$this->db->query("INSERT INTO brand_websites (brand_id, website_id) 
									VALUES (".$data_bra->id.", ".$data_website->id.");");
						}
					}
				}
				
				//se insertan los numeros de telefono
				if(isset($params['phone_number'])){
					foreach($params['phone_number'] as $phone){
						if($phone != ''){
							$phone = explode(":", $phone);
							$tipo = '';
							$pnumber = '';
							if(isset($phone[1])){
								$tipo 		= intval($phone[0]);
								$pnumber 	= $phone[1];
							}else{
								$tipo 		= '5';
								$pnumber 	= $phone[0];
							}
							
							$this->db->query("INSERT INTO phone_numbers (phone, type) VALUES ('".$pnumber."', ".$tipo.");");
							$res_phone = $this->db->query("SELECT id FROM phone_numbers ORDER BY id DESC LIMIT 1");
							$data_phone = $res_phone->row();
							
							$this->db->query("INSERT INTO brand_phone_numbers (brand_id, phone_number_id) 
									VALUES (".$data_bra->id.", ".$data_phone->id.");");
						}
					}
				}
				
				//se insertan las categorias
				if(isset($params['brand_categories'])){
					foreach($params['brand_categories'] as $catego){
						$catego = intval($catego);
						$res_catego = $this->db->query("SELECT id FROM category WHERE id = ".$catego);
						if($res_catego->num_rows() > 0){
							$res_catego = $this->db->query("SELECT brand_id FROM brand_categories WHERE brand_id = ".$data_bra->id." AND category_id = ".$catego);
							if($res_catego->num_rows() == 0){
								$this->db->query("INSERT INTO brand_categories (brand_id, category_id) 
										VALUES (".$data_bra->id.", ".$catego.");");
							}
						}
					}
				}
			
				
				return $response;
			}
			return -1; //el usuario no tiene permisos para editar el brand
		}
		return -2; //no se encontro el brand
	}
	
	/**
	 * Obtiene una lista de brands, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function getBrands($params){
		$sql = $this->getQuery($params);
		$query = Sys::pagination("SELECT b.id, b.name, b.description, c.iso2 AS country, IFNULL(uli.`like`, 0) AS `like`, b.headquarters  
			FROM brands AS b INNER JOIN country AS c ON c.id = b.contry_id 
				LEFT JOIN (SELECT  `like`, brand_id FROM users_like_brand WHERE user_id = ".$params['token_user_id'].") 
				AS uli ON b.id = uli.brand_id".$sql, $params);
		
		Sys::loadLanguage(Sys::$idioma_load, 'categories');
		Sys::loadLanguage(Sys::$idioma_load, 'country');
			Sys::loadLanguage(Sys::$idioma_load, 'regions');
		
		$brands = array('brands' => array());
		
		$res_brand = $this->db->query($query);
		if($res_brand->num_rows() > 0){
			foreach($res_brand->result_array() as $row){
				//se obtienen las imagenes de la marca
				$res_imgs = $this->db->query("SELECT bi.id, i.file_name, i.file_type, i.file_size, bi.is_primary 
					FROM brands_imgs AS bi INNER JOIN images AS i ON i.id = bi.image_id 
					WHERE bi.brand_id = ".$row['id']." AND bi.enable = 1");
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
				
				//se obtiene la informacion del location - headquarters
				if(intval($row['headquarters']) > 0){
					$res_head = $this->db->query("SELECT l.id, l.street_name, l.city, r.code_name AS state, l.postal_code, 
							c.iso3 AS country, l.lat, l.lon 
						FROM locations AS l 
							LEFT JOIN regions AS r ON r.id = l.state 
							LEFT JOIN country AS c ON c.id = l.contry
						WHERE l.id = ".$row['headquarters']);
					$row['headquarters'] = array();
					
					foreach($res_head->result_array() as $itm){
						$itm['country'] = lang($itm['country']);
						$itm['state'] = lang($itm['state']);
						$row['headquarters'][] = $itm;
					}
				}else 
					$row['headquarters'] = array();
				
				//se obtienen numeros de telefono
				$res_phone = $this->db->query("SELECT pn.id, pn.phone, pn.type 
					FROM brand_phone_numbers AS slpn INNER JOIN phone_numbers AS pn ON pn.id = slpn.phone_number_id 
					WHERE slpn.brand_id = ".$row['id']);
				$row['phone_numbers'] = array();
				
				foreach($res_phone->result_array() as $itm){
					$row['phone_numbers'][] = $itm;
				}
				
				//se obtienen las paginas webs
				$res_webs = $this->db->query("SELECT w.id, w.url 
					FROM brand_websites AS slw INNER JOIN websites AS w ON w.id = slw.website_id 
					WHERE slw.brand_id = ".$row['id']);
				$row['websites'] = array();
				
				foreach($res_webs->result_array() as $itm){
					$row['websites'][] = $itm;
				}
				
				//se obtienen las categorias
				$res_webs = $this->db->query("SELECT c.id, c.code_name, c.name 
					FROM brand_categories AS slw INNER JOIN category AS c ON c.id = slw.category_id 
					WHERE slw.brand_id = ".$row['id']);
				$row['categorys'] = array();
				
				foreach($res_webs->result_array() as $itm){
					if($itm['name'] != '')
						unset($itm['code_name']);
					elseif($itm['code_name'] != ''){
						$itm['name'] = lang($itm['code_name']);
						unset($itm['code_name']);
					}
					$row['categorys'][] = $itm;
				}
				
				//obtenemos los comentarios del brand
				$params['return_comments'] = isset($params['return_comments'])? $params['return_comments']: '0';
				if($params['return_comments'] == '1' && isset($params['filter_brand_id'])){
					$this->comments_model->table = 'brands_comments';
					$this->comments_model->field = 'brands_id';
					$row['comments'] = $this->comments_model->getComments($params, 'filter_brand_id');
				}
				
				$brands['brands'][] = $row;
			}
		}
		return $brands;
	}
	
	/**
	 * Elimina una imagen de un brand, se desactiva no se elimina fisicamente
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function removeImage($params){
		$res_img = $this->db->query("SELECT bi.id, bi.image_id, b.created_by_user_id, b.verified 
			FROM brands AS b INNER JOIN brands_imgs AS bi ON b.id = bi.brand_id 
			WHERE bi.id = ".$params['image_id']);
		if($res_img->num_rows() > 0){
			$data_img = $res_img->row();
			//Verifica si el usuario puede modificar el usuario
			if($data_img->verified==0 && $data_img->created_by_user_id == $params['user_id']){
				//$this->image_model->removeImage($data_img->image_id);
				$res_img = $this->db->query("UPDATE brands_imgs SET enable=".$params['enable']." WHERE id = ".$params['image_id']);
				return $params['enable'];
			}
			return -1;
		}
		return false;
	}
	
	
	/**
	 * SERVICIOS DE COMMENT brands
	 * Agrega un comentario a las brands
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function comment(&$params){
		$res_post = $this->db->query("SELECT id FROM brands WHERE id = ".$params['brands_id']);
		if($res_post->num_rows() > 0){
			$id_comment = $this->comments_model->insertComment($params);
			$this->db->query("INSERT INTO brands_comments (brands_id, comment_id) 
				VALUES ('".$params['brands_id']."', '".$id_comment."');");
			
			return true;
		}
		return false;
	}
	
	/**
	 * habilita o deshabilita un comentario de brands
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function disable_comment(&$params){
		$res_post = $this->db->query("SELECT mac.id, b.created_by_user_id, c.users_id AS comment_user_id 
			FROM brands_comments AS mac INNER JOIN comments AS c ON c.id = mac.comment_id INNER JOIN brands AS b ON b.id = mac.brands_id 
			WHERE mac.id = ".$params['comment_id']);
		if($res_post->num_rows() > 0){
			$data_post = $res_post->row();
			//Verifica si el usuario puede realizar esta accion
			if($data_post->created_by_user_id == $params['user_id'] || $data_post->comment_user_id == $params['user_id']){
				$this->db->query("UPDATE brands_comments SET enable=".$params['enable']." WHERE id = ".$params['comment_id']);
				return $params['enable'];
			}
			return -1;
		}
		return false;
	}
	
	/**
	 * Obtiene la lista de comentarios de un brand
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function get_comments(&$params){
		$this->comments_model->table = 'brands_comments';
		$this->comments_model->field = 'brands_id';
		$comments = array("comments" => $this->comments_model->getComments($params));
		return $comments;
	}
	
	
	
	/**
	 * Genera los filtros de la consulta sql para realizar la busqueda en los brands
	 * @param array $params.Contiene los parametros que se enviaron por post y get
	 */
	private function getQuery($params){
		$sql = isset($params['filter_country'])? 
					($params['filter_country']!=''? " WHERE LOWER(c.iso2) = LOWER('".$params['filter_country']."')": '') 
				: '';
		$sql .= isset($params['filter_brand_id'])? 
					($params['filter_brand_id']!=''? 
						($sql!=''? ' AND ':' WHERE ')."b.id = '".$params['filter_brand_id']."'"
					: '') 
				: '';
		$sql .= isset($params['filter_name'])? 
					($params['filter_name']!=''? 
						($sql!=''? ' AND ':' WHERE ')."LOWER(b.name) LIKE LOWER('".$params['filter_name']."%')"
					: '') 
				: '';
		$sql .= isset($params['filter_search_text'])? 
					($params['filter_search_text']!=''? 
						($sql!=''? ' AND ':' WHERE ')."(LOWER(b.name) LIKE LOWER('%".$params['filter_search_text']."%') OR 
							LOWER(b.description) LIKE LOWER('%".$params['filter_search_text']."%'))"
					: '') 
				: '';
		return $sql;
	}
}
?>