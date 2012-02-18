<?php
class User_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function saveInfoUser($params){
		$id = intval($this->existUser($params, 'sys', false));
		if($id==0){
			$this->db->query("INSERT INTO users (name, sex, email, password) VALUES ('".$params['name']."', '".$params['sex']."', 
				'".$params['email']."', '".$params['password']."');");
			$id = intval($this->getIdUser("WHERE email = '".$params['email']."'"));
			
			return $id;
		}else
			return false;
	}
	
	public function getInfoUser($params=array()){
		if(isset($params["user_id"])){
			$params["user_id"] = intval($params["user_id"]);
			$res = $this->getIdUserToken($params, $params["user_id"]);
		}else
			$res = $this->getIdUserToken($params);
		
		if($res->num_rows() > 0){
			//informacion del usuario
			$data = $res->row_array();
			$data = array("user" => $data);
			
			if(isset($data["user"]['birthday'])){
				$fe = new Fecha($data["user"]['birthday'], TipoFecha::DateTime);
				if($fe->esValida())
					$data["user"]['birthday'] = $fe->getTimeStamp();
			}

			Sys::loadLanguage(Sys::$idioma_load, 'country');
			Sys::loadLanguage(Sys::$idioma_load, 'regions');
			//obtenemos el pais y estado del usuario
			$res_loca = $this->db->query("SELECT c.iso3, c.iso2, r.code_name AS code_region, r.code 
			FROM users AS u INNER JOIN regions AS r ON r.id = u.state_id 
				INNER JOIN country AS c ON c.id = r.country_id 
			WHERE u.id = ".$data["user"]['id']." LIMIT 6");
			$data_loca = $res_loca->row();
			if(isset($data["user"]['birthday'])){ //indica que no es amigo y no se regresa ese dato
				$data["user"]['state'] = array(
									'name' => lang($data_loca->code_region),
									'code' => $data_loca->code
								 );
			}
			$data["user"]['country'] = array(
								'name' => lang($data_loca->iso3),
								'code' => $data_loca->iso2
							 );
			
			//obtenemos las imagenes
			$res_imgs = $this->db->query("SELECT i.file_name, i.file_type, ui.is_primary, i.label
				FROM users_imgs AS ui INNER JOIN images AS i ON i.id = ui.image_id
				WHERE ui.user_id = ".$data["user"]['id']);
			$images = array();
			foreach ($res_imgs->result_array() as $row){
				$row['sizes'] = array(
					array(
						'url' => UploadFile::urlBig().$row['file_name'],
						'size' => 'B'
					),
					array(
						'url' => UploadFile::urlMedium().$row['file_name'],
						'size' => 'M'
					),
					array(
						'url' => UploadFile::urlSmall().$row['file_name'],
						'size' => 'SN'
					),
					array(
						'url' => UploadFile::urlSmallSquare().$row['file_name'],
						'size' => 'SS'
					)
				);
			   $images[] = $row;
			}
			$data["user"]['images'] = $images;
			
			return $data;
		}
		return false;
	}
	
	public function updateInfoUser($params, $id){
		$sql = '';
		$sql .= (isset($params['name']))? "name = '".$params['name']."'": '';
		$sql .= (isset($params['last_name']))? ($sql==''?'':', ')."last_name = '".$params['last_name']."'": '';
		$sql .= (isset($params['sex']))? ($sql==''?'':', ')."sex = '".$params['sex']."'": '';
		$sql .= (isset($params['email']))? ($sql==''?'':', ')."email = '".$params['email']."'": '';
		$sql .= (isset($params['password']))? ($sql==''?'':', ')."password = '".$params['password']."'": '';
		if (isset($params['birthday'])){
			$dd = new Fecha($params['birthday'], TipoFecha::TimeStamp);
			$sql .= ($sql==''?'':', ')."birthday = '".$dd->toSqlTimeStamp()."'";
		}
		if(isset($params['state'])){
			$res_state = $this->db->query("SELECT id FROM regions WHERE id = ".$params['state']." LIMIT 1");
			if($res_state->num_rows() > 0)
				$sql .= ($sql==''?'':', ')."state_id = '".$params['state']."'";
			else
				return false;
		}
		
		//actualizamos los datos
		if ($sql!=='')
			$this->db->query("UPDATE users SET ".$sql."
				WHERE id = ".$id);
		
		return true;
	}
	
	public function getFriendsUser($params=array()){
		$res = $this->getIdUserToken($params);
		
		if($res->num_rows() > 0){
			//informacion del usuario (token)
			$data = $res->row_array();
			
			$user_exist = intval($this->getIdUser("WHERE id = ".$params['user_id']));
			if($user_exist < 1)
				return false;
			
			$fields = ($data['id']==$params['user_id'])? 
						"id, name, last_name, Concat(users.name, ' ', IFNULL(users.last_name, '')) AS full_name, email, birthday, sex": 
						"id, name, last_name, Concat(users.name, ' ', IFNULL(users.last_name, '')) AS full_name, sex";
			
			//idiomas
			Sys::loadLanguage(Sys::$idioma_load, 'country');
			Sys::loadLanguage(Sys::$idioma_load, 'regions');
			
			//filtro nombre
			$sql_name = '';
			if(isset($params['filter_name']))
				$sql_name = " AND Concat(users.name, ' ', IFNULL(users.last_name, '')) LIKE '".$params['filter_name']."%'";

			//obtenemos los amigos
			$status = "1";
			if(isset($params["pending"]))
				if($params["pending"] == 1)
					$status = "0";
			
			$sqlstatus_usr = ' AND users.status = 1';
			if(isset($params["all"])){
				if($params["all"] == '1')
					$sqlstatus_usr = "";
			}
				
			$query = Sys::pagination("SELECT ".$fields." 
				FROM users
					INNER JOIN friends ON id = friend_id 
				WHERE friends.status = ".$status.$sqlstatus_usr.$sql_name." AND user_id = ".$params['user_id'], $params, true);
			$res_fields = $this->db->query($query["query"]);
			
			$friends = array('friends' => array(), "total_rows" => $query["total_rows"]);
			foreach($res_fields->result_array() as $row){
				if(isset($row['birthday'])){
					$fe = new Fecha($row['birthday'], TipoFecha::DateTime);
					if($fe->esValida())
						$row['birthday'] = $fe->getTimeStamp();
				}
				
				//obtenemos el pais y estado del usuario
				$res_loca = $this->db->query("SELECT c.iso3, c.iso2, r.code_name AS code_region, r.code 
				FROM users AS u INNER JOIN regions AS r ON r.id = u.state_id 
					INNER JOIN country AS c ON c.id = r.country_id 
				WHERE u.id = ".$row['id']);
				$data_loca = $res_loca->row();
				$row['state'] = array(
									'name' => lang($data_loca->code_region),
									'code' => $data_loca->code
								 );
				$row['country'] = array(
								'name' => lang($data_loca->iso3),
								'code' => $data_loca->iso2
							 );
				
				
				//vemos si es amigo o no del que hace la solicitud
				$ismyfriend = array("token_user_id" => $data['id'], "user_id" => $row['id']);
				$row['is_my_friend'] = ($this->isMyFriend($ismyfriend)? '1': '0');
				
				
				//id del ultimo postyle del usuario
				$this->load->model("postyle_model");
				$params['result_items_per_page'] = '1';
				$params['result_page'] = '1';
				$params['filter_user_id'] = $row['id'];
				$params['filter_order'] = 'desc';
				$data_postyle = $this->postyle_model->getPostyles($params);
				$row['last_postyle'] = isset($data_postyle["postyles"][0])? $data_postyle["postyles"][0]["id"]: '';

				
				//obtenemos las imagenes
				$res_imgs = $this->db->query("SELECT i.file_name, i.file_type, ui.is_primary, i.label, ui.size
					FROM users_imgs AS ui INNER JOIN images AS i ON i.id = ui.image_id
					WHERE ui.user_id = ".$row['id']." LIMIT 1");
				$images = array();
				foreach ($res_imgs->result_array() as $row_img){
					$name = $row_img["file_name"];
					$row_img["file_name"] = UploadFile::urlBig().$name;
					$row_img["size"] = 'b';
					$images[] = $row_img;
					$row_img["file_name"] = UploadFile::urlMedium().$name;
					$row_img["size"] = 'm';
					$images[] = $row_img;
					$row_img["file_name"] = UploadFile::urlSmall().$name;
					$row_img["size"] = 'sn';
					$images[] = $row_img;
					$row_img["file_name"] = UploadFile::urlSmallSquare().$name;
					$row_img["size"] = 'ss';
					$images[] = $row_img;
				}
				$row['images'] = $images;
				
				$friends['friends'][] = $row;
			}
			
			return $friends;
		}
		return false;
	}
	
	public function inviteFriend($params){
		/*$res = $this->getIdUserToken($params);
		
		if($res->num_rows() > 0){
			//informacion del usuario (token)
			$data = $res->row_array();*/
			
			//existe el user_id?
			$user_exist = intval($this->getIdUser("WHERE id = ".$params['user_id']));
			if($user_exist < 1)
				return -1; //el usuario no existe
			//existe el friend_id?
			$user_exist = intval($this->getIdUser("WHERE id = ".$params['friend_id']));
			if($user_exist < 1)
				return -2; //el amigo no existe
				
			$res_rela = $this->db->query("SELECT users.id, users.name, friends.status 
				FROM users INNER JOIN friends ON id = friend_id 
				WHERE user_id = ".$params['user_id']." AND friend_id = ".$params['friend_id']."");
			if($res_rela->num_rows() > 0){
				$data_rela = $res_rela->row_array();
				if($data_rela['status']==1)
					return -3; //ya es amigo de esa persona
				else 
					return -4; //ya hay una invitacion que esta pendiente
			}else{ //enviamos la solicitud
				$this->db->query("INSERT INTO friends (user_id, friend_id) VALUES (".$params['user_id'].", ".$params['friend_id'].")");
				//$this->db->query("INSERT INTO friends (user_id, friend_id) VALUES (".$params['friend_id'].", ".$params['user_id'].")");
				
				//Enviar notificaciones via email
				$param = array(
					'token_user_id' => $params['friend_id'],
					'uuser_id' => $params['user_id'],
					'action' => '8',
				);
				$this->load->model('notification_model');
				$this->notification_model->sendNotifications($param);
					
				return 1;
			}
		//}
	}
	
	public function joinFriendFb($params){
		if(is_array($params["fb_friends"])){
			foreach($params["fb_friends"] as $key => $item){
				$item->name = Sys::limpiarTexto($item->name);
				//buscamos los usuarios con el id de facebook
				$res_user = $this->db->query("SELECT id, name 
					FROM users WHERE fb_user_id = ".$item->id." LIMIT 1");
				if($res_user->num_rows() == 1){
					$data_user = $res_user->row();
					
					//comprobamos que no exista la amistad entre los dos usaurios 
					$res_rela = $this->db->query("SELECT user_id, friend_id, status FROM friends 
						WHERE user_id = ".$params['user_id']." AND friend_id = ".$data_user->id."");
					if($res_rela->num_rows() == 0){
						//Registramos la amistad de facebook a la braba
						$this->db->query("INSERT INTO friends (user_id, friend_id, status) VALUES (".$params['user_id'].", ".$data_user->id.", 1)");
						$this->db->query("INSERT INTO friends (user_id, friend_id, status) VALUES (".$data_user->id.", ".$params['user_id'].", 1)");
					}
				}else if($res_user->num_rows() == 0){
					//se agregan los usuarios desactivados
					$this->db->query("INSERT INTO users (name, fb_user_id, status) VALUES ('".$item->name."', ".$item->id.", 0)");
					$res = $this->db->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
					$data_usr = $res->row();
					//Registramos la amistad de facebook a la braba
					$this->db->query("INSERT INTO friends (user_id, friend_id, status) VALUES (".$params['user_id'].", ".$data_usr->id.", 1)");
					$this->db->query("INSERT INTO friends (user_id, friend_id, status) VALUES (".$data_usr->id.", ".$params['user_id'].", 1)");
				}
				
			}
		}
		return true;
	}
	
	public function responseFriend($params){
		$user_exist = intval($this->getIdUser("WHERE id = ".$params['user_id']));
		if($user_exist < 1)
			return -1; //el usuario no existe
		//existe el friend_id?
		$user_exist = intval($this->getIdUser("WHERE id = ".$params['friend_id']));
		if($user_exist < 1)
			return -2; //el amigo no existe

		if($params['action_response'] == 'accept'){
			$res_rela = $this->db->query("SELECT users.id, users.name, friends.status 
				FROM users INNER JOIN friends ON id = friend_id 
				WHERE user_id = ".$params['friend_id']." AND friend_id = ".$params['user_id']."");
			if($res_rela->num_rows() > 0){
				$data_rela = $res_rela->row_array();
				if($data_rela['status']==0){
					$this->db->query("UPDATE friends SET status = 1 
						WHERE user_id = ".$params['friend_id']." AND friend_id = ".$params['user_id']);
					$this->db->query("INSERT INTO friends (user_id, friend_id, status) 
						VALUES (".$params['user_id'].", ".$params['friend_id'].", 1)");
					
					//Enviar notificaciones via email
					$param = array(
						'token_user_id' => $params['user_id'],
						'action' => '6',
					);
					$this->load->model('notification_model');
					$this->notification_model->sendNotifications($param);
					$param['token_user_id'] = $params['friend_id'];
					$param['action'] = '9';
					$this->notification_model->sendNotifications($param);
				}
				return 2; //se acepto la invitacion
			}else
				return -5; //no existe ninguna invitacion
		}else{ //no se asepto la invitacion
			$this->db->query("DELETE FROM friends 
						WHERE (user_id = ".$params['user_id']." AND friend_id = ".$params['friend_id'].") OR 
							(user_id = ".$params['friend_id']." AND friend_id = ".$params['user_id'].")");
			
			//Enviar notificaciones via email
			$param = array(
				'token_user_id' => $params['user_id'],
				'action' => '7',
			);
			$this->load->model('notification_model');
			$this->notification_model->sendNotifications($param);
			
			return 3; //se denego la invitacion
		}
	}
	
	public function getIdUserToken($params=array(), $user_id=null){
		if($user_id != null){
			$res_user_token = $this->getIdUserToken($params);
			$data = $res_user_token->row();
			$param = array(
				"token_user_id" => $data->id,
				"user_id" => $user_id
			);
			if($this->isMyFriend($param) || $user_id == $data->id)
				$fields = "id, name, last_name, email, birthday, sex";
			else
				$fields = "id, name, last_name, sex";
			
			return $this->db->query("SELECT ".$fields." 
				FROM users
				WHERE id = ".$user_id);
		}else{
			return $this->db->query("SELECT id, name, last_name, email, birthday, sex 
				FROM users INNER JOIN oauth_consumer_registry ON id = ocr_usa_id_ref
					INNER JOIN oauth_consumer_token ON ocr_id = oct_ocr_id_ref 
				WHERE ocr_consumer_key = '".$params['consumer_key']."' AND oct_token = '".$params['oauth_token']."'");
		}
	}
	
	
	public function configUser(&$params){
		foreach($params['config_id'] as $key => $id_conf){
			$res = $this->db->query("SELECT * FROM users_config 
				WHERE user_id = ".$params['user_id']." AND config_id = ".$id_conf);
			if($res->num_rows() > 0){
				if($params['unique'][$key]=='1'){
					$this->db->query("UPDATE users_config SET value = '".$params['values'][$key]."' 
						WHERE user_id = ".$params['user_id']." AND config_id = ".$id_conf);
				}else{
					$this->db->query("INSERT INTO users_config (user_id, config_id, value) 
						VALUES (".$params['user_id'].", ".$id_conf.", '".$params['values'][$key]."')");
				}
			}else{
				$this->db->query("INSERT INTO users_config (user_id, config_id, value) 
					VALUES (".$params['user_id'].", ".$id_conf.", '".$params['values'][$key]."')");
			}
			$res->free_result();
		}
		return true;
	}
	
	public function getConfigUser(&$params){
		Sys::loadLanguage(Sys::$idioma_load, 'config');
		
		$res = $this->db->query("SELECT c.id, c.code_name AS name, uc.value 
			FROM config AS c INNER JOIN users_config AS uc ON c.id = uc.config_id 
			WHERE uc.user_id = ".$params['user_id']);
		$conf = array("config" => array());
		foreach($res->result_array() as $item){
			$item['name'] = lang($item['name']);
			$conf['config'][] = $item;
		}
		return $conf;
	}
	
	public function login(&$params, $face_goo=null){
		if($face_goo == null)
			$sqll = "u.email = '".$params['email']."' AND u.password = '".$params['password']."'";
		else
			$sqll = "u.id = '".$params['user_id']."'";
			
		$res = $this->db->query("SELECT u.id, oct.oct_token, oct.oct_id 
			FROM users AS u INNER JOIN oauth_consumer_registry AS ocr ON u.id = ocr.ocr_usa_id_ref 
				INNER JOIN oauth_consumer_token AS oct ON (u.id = oct.oct_usa_id_ref 
				AND ocr.ocr_id = oct.oct_ocr_id_ref) 
			WHERE ".$sqll." 
				AND ocr.ocr_consumer_key = '".$params['consumer_key']."'");
		if($res->num_rows() > 0){
			$data = $res->row();
			$key_token = $this->config->item('store_oauth')->renewToken($data->oct_id); //renueva el token del usuario
			
			return array("user_id" => $data->id, "token" => $key_token);
		}else
			return false;
	}
	
	public function removeFriend(&$params){
		$res = $this->db->query("DELETE FROM friends 
			WHERE (user_id = ".$params["user_id"]." AND friend_id = ".$params["friend_id"].") OR 
				(user_id = ".$params["friend_id"]." AND friend_id = ".$params["user_id"].")");
	}
	
	public function isMyFriend(&$params, $req=false){
		$sql = $req==true? '': ' AND status = 1';
		$res = $this->db->query("SELECT Count(*) AS num, status FROM friends 
			WHERE user_id = ".$params["token_user_id"]." AND friend_id = ".$params["user_id"].$sql);
		$data = $res->row();
		if($data->num > 0){
			if($req)
				return $data;
			else
				return true;
		}else
			return false;
	}
	
	
	
	
	/**
	 * Metodos que se ocupan para el OAuth
	 */
	
	public function save($params=array(), $type){
		if($type=='google'){
			$params['contact/country/home'] = (isset($params['contact/country/home']))?
					sys::limpiarTexto($params['contact/country/home']):
					Sys::$country_iso2;
			
			$res_region = $this->db->query("SELECT re.id, re.region 
				FROM country AS co INNER JOIN regions AS re ON co.id = re.country_id 
				WHERE co.iso2 = '".$params['contact/country/home']."' ORDER BY re.region LIMIT 1");
			$data_region = $res_region->row();
			
			$sql = "('".$params['namePerson/first']."', '".$params['namePerson/last']."', '".$params['contact/email']."', 
				'', '".$params['token']."', token_facebook, ".$data_region->id.", fb_user_id)";
		}else{
			$default = true;
			if(isset($params['hometown']->name)){
				$params['hometown']->name = explode(', ', $params['hometown']->name);
				$region = Sys::eliminaAcentos(
							(count($params['hometown']->name)==2? 
								$params['hometown']->name[1]:
								$params['hometown']->name[0])
						  );
				$res_region = $this->db->query("SELECT r.id 
					FROM regions AS r INNER JOIN cities AS c ON r.id = c.region_id
					WHERE LOWER(r.region) = LOWER('".$region."') 
						OR LOWER(c.city) = LOWER('".$region."') LIMIT 1");
				if($res_region->num_rows()>0)
					$default = false;
			}
			if($default){
				$res_region = $this->db->query("SELECT re.id, re.region 
					FROM country AS co INNER JOIN regions AS re ON co.id = re.country_id 
					WHERE co.iso2 = '".Sys::$country_iso2."' ORDER BY re.region LIMIT 1");
			}
			$data_region = $res_region->row();
			
			$last_name = (isset($params['middle_name'])? $params['middle_name'].' ': '').$params['last_name'];
			$sql = "('".$params['first_name']."', '".$last_name."', '".$params['email']."', 
				'', token_google, '".$params['token']."', ".$data_region->id.", ".$params["id"].")";
		}
		
		if(!isset($params['usr_status'])){
			$this->db->query("INSERT INTO users (name, last_name, email, password, token_google, token_facebook, state_id, fb_user_id) 
				VALUES ".$sql);
			$res_usr = $this->db->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
			$data_usr = $res_usr->row();
			$dausr_id = $data_usr->id;
		}else if(isset($params['user_id']) && isset($params['usr_status'])){
			$this->db->query("UPDATE users SET name='".$params['first_name']."', last_name='".$last_name."', email='".$params['email']."', 
				password='', token_google=token_google, token_facebook='".$params['token']."', state_id=".$data_region->id.", 
				fb_user_id=".$params["id"].", status=1 WHERE id = ".$params['user_id']);
			$dausr_id = $params['user_id'];
		}
		
		//Insertamos una imagen al usuaior
		try{
			if($type=='facebook'){
				$info_img = getimagesize('http://graph.facebook.com/'.$params["id"].'/picture?type=large');
				
				$img_name = md5(microtime().rand(0, 9999));
				$carga_url = UploadFile::pathTemp().$img_name.'.'.UploadFile::getImgType($info_img["mime"]);
				if(!copy('http://graph.facebook.com/'.$params["id"].'/picture?type=large', $carga_url)){
				    $carga_url = Sys::$url_base."application/images/anonimous.jpg";
				}
			}else
				$carga_url = Sys::$url_base."application/images/anonimous.jpg";
			
			$this->load->model('image_model');
			$this->load->library('my_upload');
			
			$conf_url = array(
				'resize' => array('b','m','s')
			);
			$imagess = $this->my_upload->upload($carga_url, $conf_url);
			foreach($imagess as $imgss){ 	//se insertan las imagenes al usuario
				$id_img = $this->image_model->insertImage($imgss[0]['file']);
				$this->db->query("INSERT INTO users_imgs (user_id, image_id, is_primary, size) 
					VALUES (".$dausr_id.", ".$id_img.", '1', '".$imgss[0]['file']['size']."');");
			}
		}catch(Exception $e){}
		
		if($type=='facebook'){
			/*//Obtenemos los amigos de facebook y buscamos en el sistema si hay amigos de facebook 
			//agregamos la amistad a la braba
			$this->load->library('my_facebook');
			$friends = $this->my_facebook->getListFrieds($params['token']);
			$param = array(
				'fb_friends' => $friends,
				'user_id' => $data_usr->id
			);
			$this->joinFriendFb($param);*/
			
			//Agregamos los permisos a la braba para publicar
			$this->db->query("INSERT INTO users_config (user_id, config_id, value) 
				VALUES (".$dausr_id.", 3, '1'), (".$dausr_id.", 4, '1'), (".$dausr_id.", 5, '1'), (".$dausr_id.", 6, '1');");
		}
		
		return true;
	}
	
	public function update($params=array(), $type){
		$sql = ($type=='google')? 
			"token_google='".$params['token']."' WHERE email='".$params['contact/email']."'": 
			"token_facebook='".$params['token']."' WHERE email='".$params['email']."'";
		
		$this->db->query("UPDATE users SET ".$sql);
		return true;
	}
	
	public function existUser($params, $type, $update=true, $returnTy=false){
		$sql = ($type=='google')? 
				"WHERE email = '".$params['contact/email']."'": 
				"WHERE fb_user_id = '".$params["id"]."'";
		  
		$res = $this->db->query("SELECT Count(*) AS num, id AS user_id, status FROM users ".$sql);
	 	foreach($res->result() as $row){
	 		if($row->num > 0){
	 			$params['usr_status'] = $row->status; //0:pendiente, 1:activo
	 			$params['user_id'] = $row->user_id;
	 			
	 			if($update && $row->status==1)
	 				$this->update($params, $type);
	 			else if($row->status==0)
	 				$this->save($params, $type);
	 			
	 			if($returnTy)
	 				return array("user_id" => $this->getIdUser($sql), 'action' => ($row->status==1?'update': 'save'));
	 			else
	 				return $this->getIdUser($sql);
	 		}else{
	 			$params['user_id'] = $row->user_id;
	 			
	 			if($update)
	 				$this->save($params, $type);
	 				
	 			if($returnTy)
	 				return array("user_id" => $this->getIdUser($sql), 'action' => 'save');
	 			else
	 				return $this->getIdUser($sql);
	 		}	
	 	}
	}
	
	public function joinTwitter($user_id, $access_token){
		if(isset($access_token['oauth_token']) && isset($access_token['oauth_token_secret'])){
			$token = $access_token['oauth_token'].'&=&'.$access_token['oauth_token_secret'];
			$this->db->query("UPDATE users SET token_twitter = '".$token."' WHERE id = ".$user_id);
			return true;
		}else
			return false;
	}
	
	public function loginUser($paramas){
		$res = $this->db->query("SELECT id FROM users WHERE email = '".$paramas['email']."' AND password = '".md5($paramas['password'])."'");
		if($res->num_rows() > 0){
			$row = $res->row();
			return $row->id;
		}
		return false;
	}
	
	public function getIdUser($sql){
		$res = $this->db->query("SELECT id FROM users ".$sql);
		if($res->num_rows() > 0){
			$row = $res->row();
			return $row->id;
		}
		return false;
	}
	
	public function getUser($usr_id){
		$res = $this->db->query("SELECT * FROM users WHERE id = ".$usr_id);
		if($res->num_rows() > 0){
			$row = $res->row();
			return $row;
		}
		return false;
	}
}
?>