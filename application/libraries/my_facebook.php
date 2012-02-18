<?php defined('BASEPATH') OR exit('No direct script access allowed');

class my_facebook{
	private $CI;
	private $status = true;
	private $app_id;
	private $app_secret;
	private $redirect_url = "";
	private $permissions = 'offline_access,user_photos,publish_stream,user_hometown,user_about_me,email,read_friendlists';
	
	function my_facebook(){
		$this->CI =& get_instance();
		$this->redirect_url = $this->CI->config->item('base_url').'services/facebook/sigin';
		$this->CI->load->library('user_agent');
		$this->CI->load->database();
		
		$this->app_id = Sys::$facebook_app_id;
		$this->app_secret = Sys::$facebook_app_secret;
	}
	
	
	/**
	 * SERVICIOS QUE PERMITEN PUBLICAR LAS COSAS EN FACEBOOK
	 * Se autentifica en facebook
	 * @param string $redirect_url
	 */
	protected function authFacebook($redirect_url){
		if(empty(self::$params["code"])) {
	        $dialog_url = ($this->agent->is_mobile())?
	        	"http://m.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($redirect_url)."&scope=".$this->permissions:
	    		"http://www.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($redirect_url)."&scope=".$this->permissions;
			header("Location: ".$dialog_url);
			exit;
	    }
	    return true;
	}
	
	/**
	 * Obtiene un accessToken de facebook
	 * @param string $redirect_url
	 */
	protected function getToken($redirect_url, $trim=false){
		$token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$this->app_id."&redirect_uri=".
			 				$redirect_url."&client_secret=".$this->app_secret."&code=".self::$params['code'];
        $access_token = file_get_contents($token_url);
        
        if($trim)
        	$access_token = explode("&", str_replace("access_token=", "", $access_token));
        
		return $access_token;
	}
	
	public function createAlbum($acction, $user_id, $access_token){
		$graph_url = 'https://graph.facebook.com/me/albums';
		
		Sys::loadLanguage(Sys::$idioma_load);
		
		switch($acction){
			case 'fb_album_postyle_id':
				$name = lang('txt_album_postyle_name');
				$message = lang('txt_album_postyle_message');
			break;
			case 'fb_album_item_id':
				$name = lang('txt_album_item_name');
				$message = lang('txt_album_item_message');
			break;
		}
		
		// ARRAY CON Los datos del album
		$data_album = array(
			'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
			'name'			=> $name,
			'message' 		=> $message
		);
		
		$res = $this->curlExec($graph_url, $data_album);
		if(isset($res->id)){
			$this->CI->db->query("UPDATE users SET ".$acction." = '".$res->id."' 
									WHERE id = ".$user_id);
			return $res->id;
		}
		return null;
	}
	
	
	/**
	 * Obtiene la lista de amigos de facebook del usuario logeado
	 * @param unknown_type $token_fb
	 */
	public function getListFrieds($user_id=0){
		$res_usr = $this->CI->db->query("SELECT IFNULL(token_facebook, '') AS token_facebook FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			if($data_usr->token_facebook != ''){
				$graph_url = "https://graph.facebook.com/me/friends?".$data_usr->token_facebook;
				$friends = (array)json_decode(file_get_contents($graph_url));
				return $friends["data"];
			}
		}
		return false;
	}
	
	
	/**
	 * Publica un postyle en facebook
	 * @param entero $postyle_id
	 */
	public function postyle($postyle_id=0, $user_id=0, $action='add'){
		$res_usr = $this->CI->db->query("SELECT token_facebook, fb_album_postyle_id FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = str_replace('access_token=', '', $data_usr->token_facebook);
			
			//obtenemos los datos del postyle
			$res = $this->CI->db->query("SELECT p.id, p.description, Concat(u.name, ' ', u.last_name) AS user_name, i.file_name, 
					IFNULL(o.name, '') AS name, p.facebook_id 
				FROM users AS u INNER JOIN postyles AS p ON u.id = p.user_id INNER JOIN postyle_imgs AS pi ON p.id = pi.postyle_id 
					INNER JOIN images AS i ON i.id = pi.image_id LEFT JOIN ocation AS o ON o.id = p.ocation_id 
				WHERE p.id = ".$postyle_id);
			
			if($res->num_rows() > 0){
				$data = $res->row();
				
				if($action == 'add'){
					$graph_url = 'https://graph.facebook.com/me/feed';
					// ARRAY CON LA INFORMACION DEL postyle QUE SE ENVIARA A FB
					$urlImagen = UploadFile::urlBig().$data->file_name;
					$data_foto = array(
						'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
						'message' 		=> '',
						'picture' 		=> $urlImagen,		// IMAGEN DEL postyle
						'link' 			=> Sys::lml_get_url("postyle", $data->id),
						'name' 			=> $data->name,
						'caption' 		=> '',
						'description' 	=> $data->description
					);
					
					$res = $this->curlExec($graph_url, $data_foto);
					if(isset($res->id))
						$this->CI->db->query("UPDATE postyles SET facebook_id = '".$res->id."' WHERE id = ".$postyle_id);
					
					
					//checamos si en el postyle se taggearon amigos y si si se les manda la publicacion
					$res = $this->CI->db->query("SELECT u.fb_user_id 
					FROM postyle_tags_users AS ptu INNER JOIN users AS u ON u.id = ptu.user_id 
					WHERE postyle_id = ".$postyle_id);
					if($res->num_rows() > 0){
						Sys::loadLanguage(null, 'notification');
						foreach($res->result() as $data_frien){
							$graph_url = 'https://graph.facebook.com/'.$data_frien->fb_user_id.'/feed';
							// ARRAY CON LA INFORMACION Para publicar que A Sido Etiquetado en el Postyle
							$data_foto = array(
								'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
								'message' 		=> $data->user_name.' '.lang('ntf_tag_in_postyle'),
								'picture' 		=> $urlImagen,		// IMAGEN DEL postyle
								'link' 			=> Sys::lml_get_url("postyle", $data->id),
								'name' 			=> $data->name,
								'caption' 		=> '',
								'description' 	=> $data->description
							);
							$res = $this->curlExec($graph_url, $data_foto);
						}
					}
					
					/*//si no tiene creado el album lo crea
					if($data_usr->fb_album_postyle_id == NULL){
						$data_usr->fb_album_postyle_id = $this->createAlbum("fb_album_postyle_id", $user_id, $access_token);
					}
					
					$graph_url = 'https://graph.facebook.com/'.$data_usr->fb_album_postyle_id.'/photos';
					
					//obtenemos las tags
					$res = $this->CI->db->query("SELECT id, description, x, y FROM postyle_tags WHERE postyle_id = ".$postyle_id);
					$tags = '';
					foreach($res->result() as $item){
						$tags .= ',{"tag_uid":"","tag_text":"'.$item->description.'","x":'.$item->x.',"y":'.$item->y.'}';
					}
					$tags = '['.substr($tags, 1, strlen($tags)).']';
					
					//Preparamos la imagen para enviarla a fb
					$urlImagen = UploadFile::pathBig().$data->file_name; //UploadFile::urlBig().$data->file_name;
					$imagenTmp = tempnam('.', 'xhttp-tmp-'); // CREA UNA RUTA TEMPORAL PARA LA IMAGEN
					file_put_contents($imagenTmp, file_get_contents($urlImagen)); //crea la imagen temporal
					$img = '@'.$imagenTmp;
					
					// ARRAY CON LA INFORMACION DEL postyle QUE SE ENVIARA A FB
					$data_foto = array(
						'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
						'message' 		=> $data->description,
						'source' 		=> $img, 					// IMAGEN DEL postyle
						'tags' 			=> $tags
					);
					
					$res = $this->curlExec($graph_url, $data_foto);
					if(isset($res->id))
						$this->CI->db->query("UPDATE postyles SET facebook_id = '".$res->id."' WHERE id = ".$postyle_id);
					
					unlink(str_replace('@', '', $img)); //eliminamos la imagen temporal*/
					
				}elseif($action == 'delete'){
					if($data->facebook_id){
						try{
							$graph_url = 'https://graph.facebook.com/'.$data->facebook_id.'?method=delete&'.$data_usr->token_facebook;
							$res = @file_get_contents($graph_url);
						}catch(Exception $e){}
					}
				}
			}
		}
	}
	
	public function comment($comment=0, $user_id=0, $action='add'){
		$res_usr = $this->CI->db->query("SELECT * FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = str_replace('access_token=', '', $data_usr->token_facebook);
			
			if($action == 'delete'){
				$res = $this->CI->db->query("SELECT facebook_id FROM ".$comment['table']." WHERE id = ".$comment['comment_id']);
				if($res->num_rows() > 0){
					$data = $res->row();
					if($data->facebook_id){
						try{
							$graph_url = 'https://graph.facebook.com/'.$data->facebook_id.'?method=delete&'.$data_usr->token_facebook;
							$res = @file_get_contents($graph_url);
						}catch(Exception $e){}
					}
				}
			}else{
				//obtenemos los datos del comentario
				$res = $this->CI->db->query("SELECT co.id AS comment_id, c.comment, co.facebook_id 
				FROM comments AS c INNER JOIN ".$comment['table']." AS co ON c.id = co.comment_id 
				WHERE co.enable = 1 AND c.id = ".$comment['comment_id']." 
					AND co.".$comment['field']." = ".$comment[($comment['key_param']!=''? $comment['key_param']: $comment['field'])]);
				
				if($res->num_rows() > 0){
					$data = $res->row();
					
					//obtenemos los datos del objeto
					$res_post = $this->CI->db->query("SELECT facebook_id FROM ".$comment['table2']." 
							WHERE id = ".$comment[($comment['key_param']!=''? $comment['key_param']: $comment['field'])]);
					if($res_post->num_rows() > 0){
						$data_post = $res_post->row();
						if($action == 'add'){
							$graph_url = 'https://graph.facebook.com/'.$data_post->facebook_id.'/comments';
							
							// ARRAY CON Los datos del comentario
							$data_comment = array(
								'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
								'message' 		=> $data->comment
							);
							
							$res = $this->curlExec($graph_url, $data_comment);
							if(isset($res->id))
								$this->CI->db->query("UPDATE ".$comment['table']." SET facebook_id = '".$res->id."' 
														WHERE comment_id = ".$comment['comment_id']);
						}
					}
				}
			}
		}
	}
	
	
	public function like($comment=0, $user_id=0, $action='add'){
		if($comment['table'] == 'brands' || $comment['table'] == 'store')
			return true;
		
		$res_usr = $this->CI->db->query("SELECT * FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = str_replace('access_token=', '', $data_usr->token_facebook);
			
			//obtenemos los datos del obj del like
			$res = $this->CI->db->query("SELECT facebook_id  FROM ".$comment['table']." 
			WHERE id = ".$comment[($comment['key_param']!=''? $comment['key_param']: $comment['field'])]);
			
			if($res->num_rows() > 0){
				$data = $res->row();
				
				if($action == 'add'){
					$graph_url = 'https://graph.facebook.com/'.$data->facebook_id.'/likes';
					$data_like = array(
						'access_token' 	=> $access_token
					);
					$res = $this->curlExec($graph_url, $data_like);
				}elseif($action == 'delete'){
					if($data->facebook_id){
						try{
							$graph_url = 'https://graph.facebook.com/'.$data->facebook_id.'/likes?method=delete&'.$data_usr->token_facebook;	
							$res = @file_get_contents($graph_url);
						}catch(Exception $e){}
					}
				}
			}
		}
	}
	
	/**
	 * Publica un item en facebook
	 * @param entero $item_id
	 */
	public function item($item_id=0, $user_id=0, $action='add'){
		$res_usr = $this->CI->db->query("SELECT token_facebook, fb_album_item_id FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = str_replace('access_token=', '', $data_usr->token_facebook);
			
			//obtenemos los datos del item
			$res = $this->CI->db->query("SELECT i.id, i.label, b.name AS brand, sl.name AS store, img.file_name, i.facebook_id 
				FROM items AS i INNER JOIN items_imgs as ii ON i.id = ii.item_id 
					INNER JOIN images AS img ON img.id = ii.image_id LEFT JOIN brands AS b ON b.id = i.brand_id 
					LEFT JOIN store_locations AS sl ON sl.id = i.bought_in 
				WHERE i.id = ".$item_id." LIMIT 1");
			
			if($res->num_rows() > 0){
				$data = $res->row();
				
				if($action == 'add'){
					//si no tiene creado el album lo crea
					if($data_usr->fb_album_item_id == NULL){
						$data_usr->fb_album_item_id = $this->createAlbum("fb_album_item_id", $user_id, $access_token);
					}
					
					$graph_url = 'https://graph.facebook.com/'.$data_usr->fb_album_item_id.'/photos';
					
					//Preparamos la imagen para enviarla a fb
					$urlImagen = UploadFile::pathBig().$data->file_name; //UploadFile::urlBig().$data->file_name;
					$imagenTmp = tempnam('.', 'xhttp-tmp-'); // CREA UNA RUTA TEMPORAL PARA LA IMAGEN
					file_put_contents($imagenTmp, file_get_contents($urlImagen)); //crea la imagen temporal
					$img = '@'.$imagenTmp;
					
					Sys::loadLanguage(Sys::$idioma_load);
					$message = $data->label;
					if($data->brand != '' && $data->brand != NULL)
						$message .= ", ".lang('txt_brand').": ".$data->brand;
					if($data->brand != '' && $data->brand != NULL)
						$message .= ", ".lang('txt_store').": ".$data->store;
					
					// ARRAY CON LA INFORMACION DEL postyle QUE SE ENVIARA A FB
					$data_foto = array(
						'access_token' 	=> $access_token, 		// ACCESS TOKEN PARA PERMISOS
						'message' 		=> $message,
						'source' 		=> $img
					);
					
					$res = $this->curlExec($graph_url, $data_foto);
					if(isset($res->id))
						$this->CI->db->query("UPDATE items SET facebook_id = '".$res->id."' WHERE id = ".$item_id);
					
					unlink(str_replace('@', '', $img)); //eliminamos la imagen temporal
					
				}elseif($action == 'delete'){
					if($data->facebook_id){
						try{
							$graph_url = 'https://graph.facebook.com/'.$data->facebook_id.'?method=delete&'.$data_usr->token_facebook;
							$res = @file_get_contents($graph_url);
						}catch(Exception $e){}
					}
				}
			}
		}
	}
	
	
	private function curlExec($graph_url, $data){
		$handle = curl_init($graph_url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		
		$respuesta = json_decode(curl_exec($handle)); // OBTIENE EL RESULTADO DEL ENVIO DE DATOS DEL EVENTO
		
		curl_close($handle);
		
		return $respuesta;
	}
}