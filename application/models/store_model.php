<?php
class Store_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
		$this->load->model('comments_model');
	}
	
	/**
	 * Agrega un store location y/o store.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	public function saveStore($params, $images){
		$params['description'] = isset($params['description'])? 
				($params['description']!=''? $params['description']: ''): '';
		$store = $this->insertStore($params);
		$location_id = $this->insertLocation($params);
		
		$field_brand = '';
		$value_brand = ''; //se guarda el brand si biene
		if(isset($params['brand_id'])){
			$params['brand_id'] = intval($params['brand_id']);
			$res_bran = $this->db->query("SELECT id FROM brands WHERE id = ".$params['brand_id']);
			if($res_bran->num_rows() > 0){
				$field_brand = ', brand_id';
				$value_brand = ', '.$params['brand_id'];
			}
		}
		
		//se inserta el store_location
		$this->db->query("INSERT INTO store_locations (store_id, name, description, location_id, is_principal, created_by_user_id".$field_brand.") 
			VALUES ('".$store['id']."', '".$params['name']."', '".$params['description']."', 
					'".$location_id."', '".$store['principal']."', '".$params['user_id']."'".$value_brand.")");
		$res_sl = $this->db->query("SELECT id FROM store_locations ORDER BY id DESC LIMIT 1");
		$data_sl = $res_sl->row();
		
		//vector de respuesta
		$response = array(
			"store_location_id" => $data_sl->id,
			"images" => array() 
		);
		//si hay imagenes se guardan en la bd
		if(count($images)>0){
			$primary = 1;
			foreach($images as $imgs){
				if(!isset($imgs['code'])){
					$img = $imgs[0];
					
					$id_img = $this->image_model->insertImage($img['file']);
					$this->db->query("INSERT INTO store_locations_imgs (image_id, is_primary, size, store_location_id) 
						VALUES (".$id_img.", '".$primary."', '".$img['file']['size']."', ".$data_sl->id.");");
					
					$primary = 0;
				}else
					$response['images'][] = array(
						'code' => $imgs['code'],
						'message' => $imgs['message'],
						'file' => $imgs['file']
					);
			}
		}else{ //si no agregamos el mapa de google donde esta la tienda
			try{
				$band = true;
				$info_img = @getimagesize('http://maps.google.com/maps/api/staticmap?center='.$params['lat'].','.$params['lon'].'&zoom=18&size=500x500&maptype=roadmap&markers=color:red|color:red|label:A|'.$params['lat'].','.$params['lon'].'&sensor=false');
				
				if(!is_array($info_img))
					$band = false;
				
				$img_name = md5(microtime().rand(0, 9999));
				$carga_url = UploadFile::pathTemp().$img_name.'.'.UploadFile::getImgType($info_img["mime"]);
				if(!copy('http://maps.google.com/maps/api/staticmap?center='.$params['lat'].','.$params['lon'].'&zoom=18&size=500x500&maptype=roadmap&markers=color:red|color:red|label:A|'.$params['lat'].','.$params['lon'].'&sensor=false', 
				$carga_url)){
					$band = false;
				}
				
				if($band){
					$this->load->model('image_model');
					$this->load->library('my_upload');
					
					$conf_url = array(
						'resize' => array('b','m','s')
					);
					$imagess = $this->my_upload->upload($carga_url, $conf_url);
					foreach($imagess as $imgss){ 	//se insertan las imagenes a la tienda
						$id_img = $this->image_model->insertImage($imgss[0]['file']);
						$this->db->query("INSERT INTO store_locations_imgs (store_location_id, image_id, is_primary, size) 
							VALUES (".$data_sl->id.", ".$id_img.", '1', '".$imgss[0]['file']['size']."');");
					}
				}
			}catch(Exception $e){}
		}
		
		//si hay horarios se guardan en la bd
		if(isset($params['day_id'])){ 
			if(count($params['day_id'])>0){
				foreach($params['day_id'] as $key => $item){
					if($item != '' && $params['open_hour'][$key] != ''){
						$params['close_hour'][$key] = ($params['close_hour'][$key]!='')? $params['close_hour'][$key]: '00:00:00';
						$this->db->query("INSERT INTO days_range_hours (day_id, open_hour, close_hour, store_location_id) 
							VALUES (".$item.", '".$params['open_hour'][$key]."', '".$params['close_hour'][$key]."', ".$data_sl->id.");");
					}
				}
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
					
					$this->db->query("INSERT INTO store_location_websites (store_location_id, website_id) 
							VALUES (".$data_sl->id.", ".$data_website->id.");");
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
					
					$this->db->query("INSERT INTO store_location_phone_numbers (store_location_id, phone_number_id) 
							VALUES (".$data_sl->id.", ".$data_phone->id.");");
				}
			}
		}
		
		return $response;
	}
	
	/**
	 * Comprueba si el store_id existe y si no se crea (se inserta en la bd un store)
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @return integer $store_id
	 */
	public function insertStore(&$params){
		if(isset($params['store_id'])){
			if($params['store_id']!=''){
				$res = $this->db->query("SELECT id FROM store WHERE id = ".$params['store_id']);
				if($res->num_rows() > 0) //ya existe
					return array('id' => $params['store_id'], 'principal' => 0);
			}
		}
		
		//No existe entonces se inserta
		$this->db->query("INSERT INTO store (name, description) 
			VALUES ('".$params['name']."', '".$params['description']."')");
		$res = $this->db->query("SELECT id FROM store ORDER BY id DESC LIMIT 1");
		$data = $res->row();
		return array('id' => $data->id, 'principal' => 1);
	}
	
	/**
	 * Inserta una localizacion en la bd con los datos que llegan, los unicos requeridos es
	 * la latitud y longitud.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @return integer $location_id
	 */
	public function insertLocation(&$params){
		if(isset($params['lat']) && isset($params['lon'])){
			if($params['lat']!='' && $params['lon']!=''){
				$sql = array('field' => '', 'value' => '');
				if(isset($params['location_street_name'])){
					if($params['location_street_name']!=''){
						$sql['field'] = "street_name";
						$sql['value'] = "'".$params['location_street_name']."'";
					}
				}
				if(isset($params['location_description'])){
					if($params['location_description']!=''){
						$sql['field'] .= ($sql['field']!=''? ', ': '')."description";
						$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$params['location_description']."'";
					}
				}
				if(isset($params['location_city'])){
					if($params['location_city']!=''){
						$sql['field'] .= ($sql['field']!=''? ', ': '')."city";
						$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$params['location_city']."'";
					}
				}
				if(isset($params['location_postal_code'])){
					if($params['location_postal_code']!=''){
						$sql['field'] .= ($sql['field']!=''? ', ': '')."postal_code";
						$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$params['location_postal_code']."'";
					}
				}
				if(isset($params['location_state'])){
					if($params['location_state']!=''){
						$res = $this->db->query("SELECT id, country_id FROM regions WHERE LOWER(region) = LOWER('".$params['location_state']."')");
						if($res->num_rows()>0){
							$data = $res->row();
							$sql['field'] .= ($sql['field']!=''? ', ': '')."state";
							$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$data->id."'";
							
							$sql['field'] .= ($sql['field']!=''? ', ': '')."contry";
							$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$data->country_id."'";
							$enter_con = true;
						}
					}
				}
				if(isset($params['location_country']) && !isset($enter_con)){
					if($params['location_country']!=''){
						$res = $this->db->query("SELECT id FROM country WHERE LOWER(country) = LOWER('".$params['location_country']."')");
						if($res->num_rows()>0){
							$data = $res->row();
							$sql['field'] .= ($sql['field']!=''? ', ': '')."contry";
							$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$data->id."'";
						}
					}
				}
				$sql['field'] .= ($sql['field']!=''? ', ': '')."lat";
				$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$params['lat']."'";
				
				$sql['field'] .= ($sql['field']!=''? ', ': '')."lon";
				$sql['value'] .= ($sql['value']!=''? ', ': '')."'".$params['lon']."'";
				
				//insertamos la localizacion con los datos que lleguen
				if($sql['field'] != ''){
					$this->db->query("INSERT INTO locations (".$sql['field'].") 
						VALUES (".$sql['value'].")");
					$res = $this->db->query("SELECT id FROM locations ORDER BY id DESC LIMIT 1");
					$data = $res->row();
					return $data->id;
				}
			}
		}
		return null;
	}
	
	
	/**
	 * Actualiza los datos de una tienda.
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param array $images. Contiene los archivos que se subieron al servidor
	 */
	public function updateStore($params, $images){
		$res_store = $this->db->query("SELECT id, store_id, name, description, location_id, is_principal, created_by_user_id, verified 
			FROM store_locations WHERE id = ".$params['store_id']);
		if($res_store->num_rows() > 0){
			$data_store = $res_store->row();
			//Verifica si el usuario puede modificar la tienda
			if($data_store->verified==0 && $data_store->created_by_user_id == $params['user_id']){
				$this->changeStore($params, $data_store);
				$this->updateLocation($params, $data_store->location_id);
				
				//se actualiza el store_location
				$sql = $this->getQueryUpdateStore($params);
				if($sql != '')
					$this->db->query("UPDATE store_locations SET ".$sql." WHERE id = ".$data_store->id);
				
				//vector de respuesta
				$response = array(
					"store_location_id" => $data_store->id,
					"images" => array() 
				);
				//si hay imagenes se guardan en la bd
				if(count($images)>0){
					foreach($images as $imgs){
						if(!isset($imgs['code'])){
							$img = $imgs[0];
							
							$id_img = $this->image_model->insertImage($img['file']);
							$this->db->query("INSERT INTO store_locations_imgs (image_id, is_primary, size, store_location_id) 
								VALUES (".$id_img.", '0', '".$img['file']['size']."', ".$data_store->id.");");
						}else
							$response['images'][] = array(
								'code' => $imgs['code'],
								'message' => $imgs['message'],
								'file' => $imgs['file']
							);
					}
				}
				
				//si hay horarios se guardan en la bd
				if(isset($params['day_id'])){ 
					if(count($params['day_id'])>0){
						//eliminamos los ranfgos que no vengan en el vector
						$res_hrs = $this->db->query("SELECT * FROM days_range_hours 
							WHERE store_location_id = ".$data_store->id
						);
						foreach($res_hrs->result_array() as $row){
							$bad = true;
							foreach($params['days_range_id'] as $key => $item){
								if($row['id'] == $item)
									$bad = false;
							}
							if($bad)
								$this->db->query("DELETE FROM days_range_hours WHERE id = ".$row['id']);
						}
						
						//actualizamos o insertamos los rangos faltantes
						foreach($params['days_range_id'] as $key => $item){
							$params['days_range_id'][$key] = intval($item);
							$params['close_hour'][$key] = ($params['close_hour'][$key]!='')? $params['close_hour'][$key]: '00:00:00';
							
							if($params['days_range_id'][$key] > 0){
								$this->db->query("UPDATE days_range_hours SET day_id = ".$params['day_id'][$key].", 
										open_hour = '".$params['open_hour'][$key]."', 
										close_hour = '".$params['close_hour'][$key]."', store_location_id = ".$data_store->id."
									WHERE id = ".$params['days_range_id'][$key]);
							}else{
								$this->db->query("INSERT INTO days_range_hours (day_id, open_hour, close_hour, store_location_id) 
										VALUES (".$params['day_id'][$key].", '".$params['open_hour'][$key]."', 
											'".$params['close_hour'][$key]."', ".$data_store->id.");");
							}
						}
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
							
							$this->db->query("INSERT INTO store_location_websites (store_location_id, website_id) 
									VALUES (".$data_store->id.", ".$data_website->id.");");
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
							
							$this->db->query("INSERT INTO store_location_phone_numbers (store_location_id, phone_number_id) 
									VALUES (".$data_store->id.", ".$data_phone->id.");");
						}
					}
				}
				
				return $response;
			}
			return -1; //el usuario no tiene permisos para editar la tienda
		}
		return -2; //no se encontro la tienda
	}
	
	/**
	 * Actualiza los store
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @return integer $store_id
	 */
	public function changeStore(&$params, &$data_store){
		$params['principal'] = isset($params['principal'])? intval($params['principal']): 0;
		
		$sql = '';
		if($params['principal'] == 1){
			if($params['principal'] != $data_store->is_principal){
				$res = $this->db->query("SELECT id, is_principal FROM store_locations WHERE store_id = ".$data_store->store_id);
				foreach($res->result_array() as $row){
					if($row['is_principal'] == 1){
						$this->db->query("UPDATE store_locations SET is_principal = 0 WHERE id = ".$row['id']);
						break;
					}
				}
				$this->db->query("UPDATE store_locations SET is_principal = 1 WHERE id = ".$data_store->id);
				if(isset($params['name'])){
					if($params['name']!='')
						$sql = "name = '".$params['name']."'";
					else 
						$sql = "name = '".$data_store->name."'";
				}else
					$sql = "name = '".$data_store->name."'";
				if(isset($params['description'])){
					if($params['description']!='')
						$sql .= ($sql!=''? ', ': '')."description = '".$params['description']."'";
					else 
						$sql .= ($sql!=''? ', ': '')."description = '".$data_store->description."'";
				}else 
					$sql .= ($sql!=''? ', ': '')."description = '".$data_store->description."'";
			}else
				$sql = $this->getQueryUpdateStore($params);
		}
		
		if($sql != '')
			$this->db->query("UPDATE store SET ".$sql." 
							WHERE id = ".$data_store->store_id);
		return true;
	}
	
	private function getQueryUpdateStore(&$params){
		$sql = '';
		if(isset($params['name'])){
			if($params['name']!='')
				$sql = "name = '".$params['name']."'";
		}
		if(isset($params['description'])){
			if($params['description']!='')
				$sql .= ($sql!=''? ', ': '')."description = '".$params['description']."'";
		}
		if(isset($params['brand_id'])){
			if($params['brand_id']!='')
				$sql .= ($sql!=''? ', ': '')."brand_id = '".$params['brand_id']."'";
		}
		return $sql;
	}
	
	/**
	 * Actualiza una localizacion en la bd con los datos que llegan, tiene que ser una localizacion
	 * que exista
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 * @param int $id. id de la localizacion que se actualizara
	 */
	public function updateLocation(&$params, $id){
		$sql = array('field' => '', 'value' => '');
		if(isset($params['location_street_name'])){
			if($params['location_street_name']!='')
				$sql['field'] = "street_name = '".$params['location_street_name']."'";
		}
		if(isset($params['location_description'])){
			if($params['location_description']!='')
				$sql['field'] .= ($sql['field']!=''? ', ': '')."description = '".$params['location_description']."'";
		}
		if(isset($params['location_city'])){
			if($params['location_city']!='')
				$sql['field'] .= ($sql['field']!=''? ', ': '')."city = '".$params['location_city']."'";
		}
		if(isset($params['location_postal_code'])){
			if($params['location_postal_code']!='')
				$sql['field'] .= ($sql['field']!=''? ', ': '')."postal_code = '".$params['location_postal_code']."'";
		}
		if(isset($params['location_state'])){
			if($params['location_state']!=''){
				$res = $this->db->query("SELECT id, country_id FROM regions WHERE LOWER(region) = LOWER('".$params['location_state']."')");
				if($res->num_rows()>0){
					$data = $res->row();
					$sql['field'] .= ($sql['field']!=''? ', ': '')."state = '".$data->id."'";
					
					$sql['field'] .= ($sql['field']!=''? ', ': '')."contry = '".$data->country_id."'";
					$enter_con = true;
				}
			}
		}
		if(isset($params['location_country']) && !isset($enter_con)){
			if($params['location_country']!=''){
				$res = $this->db->query("SELECT id FROM country WHERE LOWER(country) = LOWER('".$params['location_country']."')");
				if($res->num_rows()>0){
					$data = $res->row();
					$sql['field'] .= ($sql['field']!=''? ', ': '')."contry = '".$data->id."'";
				}
			}
		}
		if(isset($params['lat'])){
			if($params['lat']!='')
				$sql['field'] .= ($sql['field']!=''? ', ': '')."lat = '".$params['lat']."'";
		}
		if(isset($params['lon'])){
			if($params['lon']!='')
				$sql['field'] .= ($sql['field']!=''? ', ': '')."lon = '".$params['lon']."'";
		}
		
		//actualizamos la localizacion con los datos que lleguen
		if($sql['field'] != '')
			$this->db->query("UPDATE locations SET ".$sql['field']." WHERE id = ".$id);
		return true;
	}
	
	
	
	
	/**
	 * Obtiene una lista de stores, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function getStores($params, $like=true){
		$sql = $this->getQuery($params);
		$sql_like = array('', '');
		if($like)
			$sql_like = array(", IFNULL(uli.`like`, 0) AS `like`",
			"LEFT JOIN (SELECT `like`, store_id FROM users_like_store WHERE user_id = ".$params['token_user_id'].") AS uli ON sl.id = uli.store_id");
		$query = Sys::pagination("
			SELECT sl.id, sl.name, sl.is_principal, 
				sl.description, l.street_name, l.city, r.code_name AS state, l.postal_code, l.description AS formatted_address, 
				c.iso3 AS country, l.lat, l.lon".$sql_like[0].", sl.brand_id AS brand
			FROM store AS s INNER JOIN store_locations AS sl ON s.id = sl.store_id 
				INNER JOIN locations AS l ON l.id = sl.location_id 
				LEFT JOIN regions AS r ON r.id = l.state 
				LEFT JOIN country AS c ON c.id = l.contry
				".$sql_like[1]." ".$sql, $params);
		
		$stores = array('stores' => array());
		
		$res_store = $this->db->query($query);
		if($res_store->num_rows() > 0){
			Sys::loadLanguage(Sys::$idioma_load, 'days');
			Sys::loadLanguage(Sys::$idioma_load, 'country');
			Sys::loadLanguage(Sys::$idioma_load, 'regions');
			
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
			
			foreach($res_store->result_array() as $row){
				//filtros de geo
				$accept_row = true;
				if($filter_geo){
					$point->setLongLat($row['lon'], $row['lat']);
					$point->convertLLtoTM();
					$punto = $point->getUTM();
					$accept_row = $point->inCircle($punto['northing'], $punto['easting'], $params['filter_radio'], 
													$punto_c['northing'], $punto_c['easting']);
				}
				
				//si es true indica que la tienda si cumple con los filtros de geolocalizacion
				if($accept_row){
					$row['country'] = lang($row['country']);
					$row['state'] = lang($row['state']);
					
					//si no hay direccion del store en la bd se obtiene de google y se guarda en la bd
					$direc = $row['formatted_address'].$row['street_name'].$row['city'].$row['state'].$row['postal_code'].$row['country'];
					if($direc==''){
						$dir_google = (array)json_decode(file_get_contents("http://maps.google.com/maps/api/geocode/json?latlng=".$row['lat'].",".$row['lon']."&sensor=false"));
						
						if(count($dir_google['results']) > 0){
							$dir_google['results'][0] = (array)$dir_google['results'][0];
							$parame = array();
							foreach($dir_google['results'][0]['address_components'] as $iitem){
								switch($iitem->types[0]){
									case 'route': 
										$parame['location_street_name'] = $iitem->long_name; break;
									case 'neighborhood': 
										$parame['location_street_name'] .= ', '.$iitem->long_name; break;
									case 'locality': 
										$parame['location_city'] = $iitem->long_name; break;
									case 'administrative_area_level_1': 
										$parame['location_state'] = $iitem->long_name; break;
									case 'country': 
										$parame['location_country'] = $iitem->long_name; break;
								}
							}
							if(count($parame) < 3)
								$parame['location_description'] = $dir_google['results'][0]['formatted_address'];
							$this->updateLocation($parame, $row['id']);
							
							$row['street_name'] = $dir_google['results'][0]['formatted_address'];
						}
					}
					
					//se obtienen las imagenes de la tienda
					$res_imgs = $this->db->query("SELECT bi.id, i.file_name, i.file_type, i.file_size, bi.is_primary 
						FROM store_locations_imgs AS bi INNER JOIN images AS i ON i.id = bi.image_id 
						WHERE bi.store_location_id = ".$row['id']." AND bi.enable = 1");
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
					
					//se obtienen la marca del store - si tiene
					$row["brand"] = intval($row["brand"]);
					if($row["brand"]>0){
						$res_brand = $this->db->query("SELECT id, name 
							FROM brands WHERE id = ".$row["brand"]);
						$row['brand'] = $res_brand->row_array();
					}else
						$row["brand"] = array();
					
					//se obtienen los horarios
					$res_hrs = $this->db->query("SELECT d.id, d.code_name AS day, dr.open_hour, dr.close_hour 
						FROM days AS d INNER JOIN days_range_hours AS dr ON d.id = dr.day_id 
						WHERE store_location_id = ".$row['id']);
					$row['schedules'] = array();
					
					foreach($res_hrs->result_array() as $hrs){
						$hrs['day'] = lang($hrs['day']);
						$row['schedules'][] = $hrs;
					}
					
					//se obtienen numeros de telefono
					$res_phone = $this->db->query("SELECT pn.id, pn.phone, pn.type 
						FROM store_location_phone_numbers AS slpn INNER JOIN phone_numbers AS pn ON pn.id = slpn.phone_number_id 
						WHERE slpn.store_location_id = ".$row['id']);
					$row['phone_numbers'] = array();
					
					foreach($res_phone->result_array() as $itm){
						$row['phone_numbers'][] = $itm;
					}
					
					//se obtienen las paginas webs
					$res_webs = $this->db->query("SELECT w.id, w.url 
						FROM store_location_websites AS slw INNER JOIN websites AS w ON w.id = slw.website_id 
						WHERE slw.store_location_id = ".$row['id']);
					$row['websites'] = array();
					
					foreach($res_webs->result_array() as $itm){
						$row['websites'][] = $itm;
					}
					
					//obtenemos los comentarios del brand
					$params['return_comments'] = isset($params['return_comments'])? $params['return_comments']: '0';
					if($params['return_comments'] == '1' && isset($params['filter_store_location_id'])){
						$this->comments_model->table = 'store_location_comments';
						$this->comments_model->field = 'store_location_id';
						$row['comments'] = $this->comments_model->getComments($params, 'filter_store_location_id');
					}
					
					$stores['stores'][] = $row;
				}
			}
		}
		return $stores;
	}
	
	
	
	/**
	 * Elimina una imagen de un store, se desactiva no se elimina fisicamente
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function removeImage($params){
		$res_img = $this->db->query("SELECT sli.id, sli.image_id, sl.created_by_user_id, sl.verified 
			FROM store_locations AS sl INNER JOIN store_locations_imgs AS sli ON sl.id = sli.store_location_id
			WHERE sli.id = ".$params['image_id']);
		if($res_img->num_rows() > 0){
			$data_img = $res_img->row();
			//Verifica si el usuario puede modificar el usuario
			if($data_img->verified==0 && $data_img->created_by_user_id == $params['user_id']){
				//$this->image_model->removeImage($data_img->image_id);
				$res_img = $this->db->query("UPDATE store_locations_imgs SET enable=".$params['enable']." WHERE id = ".$params['image_id']);
				return $params['enable'];
			}
			return -1;
		}
		return false;
	}
	
	
	/**
	 * SERVICIOS DE COMMENT stores
	 * Agrega un comentario a los store location
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function comment(&$params){
		$res_post = $this->db->query("SELECT id FROM store_locations WHERE id = ".$params['store_id']);
		if($res_post->num_rows() > 0){
			$id_comment = $this->comments_model->insertComment($params);
			$this->db->query("INSERT INTO store_location_comments (store_location_id, comment_id) 
				VALUES ('".$params['store_id']."', '".$id_comment."');");
			
			return true;
		}
		return false;
	}
	
	/**
	 * habilita o deshabilita un comentario de store location
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function disable_comment(&$params){
		$res_post = $this->db->query("SELECT mac.id, b.created_by_user_id, c.users_id AS comment_user_id 
			FROM store_location_comments AS mac INNER JOIN comments AS c ON c.id = mac.comment_id 
				INNER JOIN store_locations AS b ON b.id = mac.store_location_id 
			WHERE mac.id = ".$params['comment_id']);
		if($res_post->num_rows() > 0){
			$data_post = $res_post->row();
			//Verifica si el usuario puede realizar esta accion
			if($data_post->created_by_user_id == $params['user_id'] || $data_post->comment_user_id == $params['user_id']){
				$this->db->query("UPDATE store_location_comments SET enable=".$params['enable']." WHERE id = ".$params['comment_id']);
				return $params['enable'];
			}
			return -1;
		}
		return false;
	}
	
	/**
	 * Obtiene la lista de comentarios de un store location
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function get_comments(&$params){
		$this->comments_model->table = 'store_location_comments';
		$this->comments_model->field = 'store_location_id';
		$comments = array("comments" => $this->comments_model->getComments($params, 'store_id'));
		return $comments;
	}
	
	
	
	
	/**
	 * Genera los filtros de la consulta sql para realizar la busqueda en las tiendas
	 * @param array $params.Contiene los parametros que se enviaron por post y get
	 */
	private function getQuery($params){
		$sql = isset($params['filter_store_location_id'])? 
					($params['filter_store_location_id']!=''? " WHERE sl.id = '".$params['filter_store_location_id']."'": '') 
				: '';
		$sql .= isset($params['filter_name'])? 
					($params['filter_name']!=''? 
						($sql!=''? ' AND ':' WHERE ')."LOWER(sl.name) LIKE LOWER('".$params['filter_name']."%')"
					: '') 
				: '';
		$sql .= isset($params['filter_search_text'])? 
					($params['filter_search_text']!=''? 
						($sql!=''? ' AND ':' WHERE ')."(LOWER(sl.name) LIKE LOWER('%".$params['filter_search_text']."%') OR 
							LOWER(sl.description) LIKE LOWER('%".$params['filter_search_text']."%'))"
					: '') 
				: '';
		return $sql;
	}
	
	
	
	/**
	 * Metodo que carga imagenes de google map a las tiendas
	 * Enter description here ...
	 */
	public function cargaMapa(){
		$res = $this->db->query("SELECT l.id, lat, lon, sli.image_id, i.file_name 
			FROM store_locations AS sl INNER JOIN locations AS l ON l.id = sl.location_id
			INNER JOIN store_locations_imgs sli ON sl.id = sli.store_location_id 
			INNER JOIN images AS i ON i.id = sli.image_id");
		foreach($res->result_array() as $row){
			try{
				$band = true;
				$info_img = @getimagesize('http://maps.google.com/maps/api/staticmap?center='.$row['lat'].','.$row['lon'].'&zoom=18&size=500x500&maptype=roadmap&markers=color:red|color:red|label:A|'.$row['lat'].','.$row['lon'].'&sensor=false');
				
				if(!is_array($info_img))
					$band = false;
				
				$img_name = md5(microtime().rand(0, 9999));
				$carga_url = UploadFile::pathTemp().$img_name.'.'.UploadFile::getImgType($info_img["mime"]);
				if(!copy('http://maps.google.com/maps/api/staticmap?center='.$row['lat'].','.$row['lon'].'&zoom=18&size=500x500&maptype=roadmap&markers=color:red|color:red|label:A|'.$row['lat'].','.$row['lon'].'&sensor=false', 
				$carga_url)){
					$band = false;
				}
				
				unlink(UploadFile::pathBig().$row['file_name']);
				unlink(UploadFile::pathMedium().$row['file_name']);
				unlink(UploadFile::pathSmall().$row['file_name']);
				unlink(UploadFile::pathSmallSquare().$row['file_name']);
				
				if($band){
					$this->load->model('image_model');
					$this->load->library('my_upload');
					
					$conf_url = array(
						'resize' => array('b','m','s')
					);
					$imagess = $this->my_upload->upload($carga_url, $conf_url);
					foreach($imagess as $imgss){ 	//se insertan las imagenes a la tienda
						$params = $imgss[0]['file'];
						$params['file_name'] = isset($params['file_name'])? $params['file_name']: '';
						$params['file_type'] = isset($params['file_type'])? $params['file_type']: '';
						$params['file_size'] = isset($params['file_size'])? $params['file_size']: '';
						
						$this->db->query("UPDATE images SET file_name='".$params['file_name']."', 
							file_type='".$params['file_type']."', file_size='".$params['file_size']."' 
						WHERE id = ".$row['image_id']);
						
						/*$id_img = $this->image_model->insertImage($imgss[0]['file']);
						$this->db->query("INSERT INTO store_locations_imgs (store_location_id, image_id, is_primary, size) 
							VALUES (".$row['id'].", ".$id_img.", '1', '".$imgss[0]['file']['size']."');");*/
					}
				}
			}catch(Exception $e){}
		}
	}
}
?>