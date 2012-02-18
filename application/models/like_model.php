<?php
class Like_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('user_permissions_model');
	}
	
	public function num_likes(&$params){			
		$res_rela = $this->db->query("SELECT nums_likes('".$params["table"]."', ".$params["id"].", 1) as num_likes");
		$data = $res_rela->row();
		return array("nums_likes" => $data->num_likes);
	}
	
	public function neutro(&$params){
		if(($err = $this->valExist($params)) < 1)
			return $err;
			
		$res_rela = $this->db->query("SELECT *  
			FROM ".$params['table']." 
			WHERE user_id = ".$params['user_id']." 
				AND ".$params['field']." = ".$params[$params['field']]."");
		if($res_rela->num_rows() > 0){
			$this->db->query("UPDATE ".$params['table']." SET `like` = 0 
				WHERE user_id = ".$params['user_id']." AND ".$params['field']." = ".$params[$params['field']]."");
			
			//verificamos si tiene permisos para que se publique en otros lados
			$this->user_permissions_model->publish($params, array(
				array(
					"method" => "conf_publish_facebook_like",
					"action" => "like",
					"obj_id" => array(
									'table' => $params['table_compare'],
									'field' => $params['field'],
									'key_param' => '',
									$params['field'] => $params[$params['field']]
								),
					"opc"    => "delete"
				)
			));
			
			return 3; //se puso en neutro
		}else
			return -4; //no existe el like
	}
	
	public function like($params){
		if(($err = $this->valExist($params)) < 1)
			return $err;

		$res = 0;
		$res_rela = $this->db->query("SELECT *  
			FROM ".$params['table']." 
			WHERE user_id = ".$params['user_id']." 
				AND ".$params['field']." = ".$params[$params['field']]."");
		if($res_rela->num_rows() > 0){
			$data_rela = $res_rela->row_array();
			if($data_rela['like']==1)
				return -3; //ya tiene like 
			else{ 
				$this->db->query("UPDATE ".$params['table']." SET `like` = 1 
					WHERE user_id = ".$params['user_id']." AND ".$params['field']." = ".$params[$params['field']]."");
				
				$res = 1; //se puso en like
			}
		}else{ //se inserta el registro y se pone like
			$this->db->query("INSERT INTO ".$params['table']." (user_id, ".$params['field'].", `like`) 
				VALUES (".$params['user_id'].", ".$params[$params['field']].", 1)");
			$res = 1;
		}
		
		if($res == 1){
			//verificamos si tiene permisos para que se publique en otros lados
			$this->user_permissions_model->publish($params, array(
				array(
					"method" => "conf_publish_facebook_like",
					"action" => "like",
					"obj_id" => array(
									'table' => $params['table_compare'],
									'field' => $params['field'],
									'key_param' => '',
									$params['field'] => $params[$params['field']]
								),
					"opc"    => "add"
				)
			));
			
			if($params['table_compare'] == 'postyles' || $params['table_compare'] == 'items'){
				$action = '2';
				if($params['table_compare'] == 'items')
					$action = '4';
				//Enviar notificaciones via email
				$param = array(
					'token_user_id' => $params['user_id'],
					'action' => $action,
				);
				$this->load->model('notification_model');
				$this->notification_model->sendNotifications($param);
			}
		}
		return $res;
	}
	
	public function unlike($params){
		if(($err = $this->valExist($params)) < 1)
			return $err;
			
		$res_rela = $this->db->query("SELECT *  
			FROM ".$params['table']." 
			WHERE user_id = ".$params['user_id']." 
				AND ".$params['field']." = ".$params[$params['field']]."");
		if($res_rela->num_rows() > 0){
			$this->db->query("UPDATE ".$params['table']." SET `like` = -1 
				WHERE user_id = ".$params['user_id']." AND ".$params['field']." = ".$params[$params['field']]."");
			
			//verificamos si tiene permisos para que se publique en otros lados
			$this->user_permissions_model->publish($params, array(
				array(
					"method" => "conf_publish_facebook_like",
					"action" => "like",
					"obj_id" => array(
									'table' => $params['table_compare'],
									'field' => $params['field'],
									'key_param' => '',
									$params['field'] => $params[$params['field']]
								),
					"opc"    => "delete"
				)
			));
		}else{ //se inserta el registro y se pone en unlike
			$this->db->query("INSERT INTO ".$params['table']." (user_id, ".$params['field'].", `like`) 
				VALUES (".$params['user_id'].", ".$params[$params['field']].", -1)");
		}
		
		if($params['table_compare'] == 'postyles' || $params['table_compare'] == 'items'){
			$action = '3';
			if($params['table_compare'] == 'items')
				$action = '5';
			//Enviar notificaciones via email
			$param = array(
				'token_user_id' => $params['user_id'],
				'action' => $action,
			);
			$this->load->model('notification_model');
			$this->notification_model->sendNotifications($param);
		}
		
		return 2; //se puso en unlike
	}
	
	public function like_unlike($params){
		$res = $this->db->query("SELECT `like` FROM ".$params['table']." 
			WHERE ".$params['field']." = ".$params['field_val']." AND user_id = ".$params['user_id']);
		if($res->num_rows() > 0){
			$data = $res->row();
			return $data->like;
		}else
			return 0;
	}
	
	private function valExist($params){
		//existe el user_id?
		$res = $this->db->query("SELECT id FROM users WHERE id = ".$params['user_id']);
		if($res->num_rows() < 1)
			return -1; //el usuario no existe
		
		//existe el id del que se quiere hacer like?
		$res = $this->db->query("SELECT id FROM ".$params['table_compare']." WHERE id = ".$params[$params['field']]);
		if($res->num_rows() < 1)
			return -2; //el store no existe
		
		return 1;
	}
}
?>