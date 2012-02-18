<?php
class notification_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function updateSee($params){
		$res = $this->db->query("UPDATE notifications_user SET status = 1 WHERE user_id = ".$params['token_user_id']);
		$num = $this->getNumNotification($params);
		return array('num_notification' => $num['num_notification']);
	}
	
	/**
	 * Obtiene las notificaciones del usuario que le pertenese el token
	 * @param $params
	 */
	public function getNumNotification($params){
		$res = $this->db->query("SELECT get_notifi_user(".$params['token_user_id'].") AS num");
		$data = $res->row();
		return $response = array('num_notification' => $data->num);
	}
	
	
	public function getNotifications(&$params){
		$params['output_format'] = isset($params['output_format'])? intval($params['output_format']): 0;
		Sys::loadLanguage(null, 'notification');
		$resultados = array();
		$total_results = 0;
		
		//notificaciones de amistad
		$params_user = $params;
		$params_user['result_items_per_page'] = 3;
		$query1 = Sys::pagination("SELECT u.id AS user_id, Concat(u.name, ' ', u.last_name) AS name, ui.id, i.file_name, 
				i.file_type, i.file_size, ui.is_primary 
			FROM users AS u INNER JOIN friends AS f ON u.id = f.user_id INNER JOIN users_imgs AS ui ON ui.user_id = f.user_id 
				INNER JOIN images AS i ON i.id = ui.image_id 
			WHERE f.friend_id = ".$params['token_user_id']." AND f.status = 0", $params_user);
		$res_amis = $this->db->query($query1);
		foreach($res_amis->result() as $row){
			if($params['output_format'] == 1){
				$resultados[] = '<div class="notifica_img">
					<img src="'.UploadFile::urlSmallSquare().$row->file_name.'" width="40" height="40"></div>
				<div class="notifica_info sinver">
					<a href="'.Sys::lml_get_url('user', $row->user_id).'" class="link_blue">'.$row->name.'</a> '.lang('ntf_wants_your_friend').'
					<div class="clear"></div>
					<span class="friend_deny" id="'.$row->user_id.'" me_id="'.$params['token_user_id'].'">'.lang('ntf_deny').'</span> 
					<span class="friend_acept" id="'.$row->user_id.'" me_id="'.$params['token_user_id'].'">'.lang('ntf_confirm').'</span>
					<div id="noti_msg" class="clear"></div>
				</div>';
			}else if($params['output_format'] == 2){
				$resultados[] = array(
					'subject_id' => $row->user_id,
					'subject_name' => $row->name,
					'texts' => array(lang('ntf_wants_your_friend')),
					'type' => 8,
					'images' => array(
						array(
							'url' => UploadFile::urlBig().$row->file_name, 'size' => 'B'),
						array(
							'url' => UploadFile::urlMedium().$row->file_name, 'size' => 'M'),
						array(
							'url' => UploadFile::urlSmall().$row->file_name, 'size' => 'SN'),
						array(
							'url' => UploadFile::urlSmallSquare().$row->file_name, 'size' => 'SS')
					)
				);
			}else{
				$resultados[] = $row->name.' '.lang('ntf_wants_your_friend');
			}
		}
		$params['result_items_per_page'] -= $res_amis->num_rows();
		$total_results += $res_amis->num_rows();
		
		//las demas notificaciones
		$query = Sys::pagination("SELECT nu.user_id, nh.id AS notifi_id, (
				SELECT Concat(name, ' ', last_name) FROM users WHERE id = nu.user_id
			) AS name_from, nh.subject, Concat(u.name, ' ', u.last_name) AS name_subject, nh.action, nh.details, nh.object, nu.status 
		FROM notifications_user AS nu INNER JOIN notifications_history AS nh 
			ON nh.id = nu.notification_history_id INNER JOIN users AS u ON u.id = nh.subject 
		WHERE nu.user_id = ".$params['token_user_id']." ORDER BY nh.id DESC", $params, true);
		$res = $this->db->query($query['query']);
		$total_results += $query['total_rows'];
		
		$sttatus = '';
		//0:texto plano, 1:formato html, 2:en pedasos
		foreach($res->result() as $row){
			$sttatus = $row->status=='1'? '': ' sinver';
			switch($row->action){
				case 0: //publicacion postyle
					$res_post = $this->db->query("SELECT p.id, p.description, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM postyles AS p INNER JOIN postyle_imgs AS ui ON ui.postyle_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." AND p.enable = 1 LIMIT 1");
					if($res_post->num_rows() > 0){
						$data_pos = $res_post->row();
						$descrip = $data_pos->description==''? lang('see'): substr($data_pos->description, 0, 30);
						
						if($params['output_format'] == 1){
							$resultados[] = '<div class="notifica_img">
								<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
							<div class="notifica_info'.$sttatus.'">
								<a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
								lang('ntf_create_publication').': <a href="'.Sys::lml_get_url('postyle', $data_pos->id).'" class="link_blue">'.$descrip.'</a>
							</div>';
						}else if($params['output_format'] == 2){
							$resultados[] = array(
								'subject_id' => $row->subject,
								'subject_name' => $row->name_subject,
								'texts' => array(lang('ntf_create_publication')),
								'object_id' => $data_pos->id,
								'object_name' => $descrip,
								'type' => $row->action,
								'images' => array(
									array(
										'url' => UploadFile::urlBig().$data_pos->file_name, 'size' => 'B'),
									array(
										'url' => UploadFile::urlMedium().$data_pos->file_name, 'size' => 'M'),
									array(
										'url' => UploadFile::urlSmall().$data_pos->file_name, 'size' => 'SN'),
									array(
										'url' => UploadFile::urlSmallSquare().$data_pos->file_name, 'size' => 'SS')
								));
						}else{
							$resultados[] = $row->name_subject.' '.lang('ntf_create_publication').': '.$descrip;
						}
					}
				break;
				case 1: //comentario postyle
					$res_post = $this->db->query("SELECT p.id, p.description, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM postyles AS p INNER JOIN postyle_imgs AS ui ON ui.postyle_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." AND p.enable = 1 LIMIT 1");
					if($res_post->num_rows() > 0){
						$data_pos = $res_post->row();
						$descrip = $data_pos->description==''? lang('see'): substr($data_pos->description, 0, 30);
						
						if($params['output_format'] == 1){
							$resultados[] = '<div class="notifica_img">
								<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
							<div class="notifica_info'.$sttatus.'">
								<a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
								lang('ntf_mentioned_publication').': <a href="'.Sys::lml_get_url('postyle', $data_pos->id).'" class="link_blue">'.$descrip.'</a>
							</div>';
						}else if($params['output_format'] == 2){
							$resultados[] = array(
								'subject_id' => $row->subject,
								'subject_name' => $row->name_subject,
								'texts' => array(lang('ntf_mentioned_publication')),
								'object_id' => $data_pos->id,
								'object_name' => $descrip,
								'type' => $row->action,
								'images' => array(
									array(
										'url' => UploadFile::urlBig().$data_pos->file_name, 'size' => 'B'),
									array(
										'url' => UploadFile::urlMedium().$data_pos->file_name, 'size' => 'M'),
									array(
										'url' => UploadFile::urlSmall().$data_pos->file_name, 'size' => 'SN'),
									array(
										'url' => UploadFile::urlSmallSquare().$data_pos->file_name, 'size' => 'SS')
								));
						}else{
							$resultados[] = $row->name_subject.' '.lang('ntf_mentioned_publication').': '.$descrip;
						}
					}
				break;
				case ($row->action == 2 || $row->action == 3): //like/dislike postyle
					$res_post = $this->db->query("SELECT p.id, p.description, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM postyles AS p INNER JOIN postyle_imgs AS ui ON ui.postyle_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." AND p.enable = 1 LIMIT 1");
					if($res_post->num_rows() > 0){
						$tip = ($row->action==2? 'ntf_likes': 'ntf_dislikes');
						$data_pos = $res_post->row();
						$descrip = $data_pos->description==''? lang('see'): substr($data_pos->description, 0, 30);
						
						if($params['output_format'] == 1){
							$resultados[] = '<div class="notifica_img">
								<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
							<div class="notifica_info'.$sttatus.'">
								'.lang('ntf_a').' <a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
								lang($tip).': <a href="'.Sys::lml_get_url('postyle', $data_pos->id).'" class="link_blue">'.$descrip.'</a>
							</div>';
						}else if($params['output_format'] == 2){
							$resultados[] = array(
								'subject_id' => $row->subject,
								'subject_name' => $row->name_subject,
								'texts' => array(lang('ntf_a'), lang($tip)),
								'object_id' => $data_pos->id,
								'object_name' => $descrip,
								'type' => $row->action,
								'images' => array(
									array(
										'url' => UploadFile::urlBig().$data_pos->file_name, 'size' => 'B'),
									array(
										'url' => UploadFile::urlMedium().$data_pos->file_name, 'size' => 'M'),
									array(
										'url' => UploadFile::urlSmall().$data_pos->file_name, 'size' => 'SN'),
									array(
										'url' => UploadFile::urlSmallSquare().$data_pos->file_name, 'size' => 'SS')
								));
						}else{
							$resultados[] = lang('ntf_a').' '.$row->name_subject.' '.lang($tip).': '.$descrip;
						}
					}
				break;
				case ($row->action == 4 || $row->action == 5): //like/dislike items
					$res_post = $this->db->query("SELECT p.id, p.label, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM items AS p INNER JOIN items_imgs AS ui ON ui.item_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." LIMIT 1");
					if($res_post->num_rows() > 0){
						$tip = $row->action==4? 'ntf_likes': 'ntf_dislikes';
						$data_pos = $res_post->row();
						$descrip = $data_pos->label==''? lang('see'): substr($data_pos->label, 0, 30);
						
						if($params['output_format'] == 1){
							$resultados[] = '<div class="notifica_img">
								<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
							<div class="notifica_info'.$sttatus.'">
								'.lang('ntf_a').' <a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
								lang($tip).': <a href="javascript:void(0);" id="'.$data_pos->id.'" me_id="'.
								$params['token_user_id'].'" obj_id="'.$data_pos->id.'" obj_type="item" class="notifiOpenPreview link_blue">'.$descrip.'</a>
							</div>';
						}else if($params['output_format'] == 2){
							$resultados[] = array(
								'subject_id' => $row->subject,
								'subject_name' => $row->name_subject,
								'texts' => array(lang('ntf_a'), lang($tip)),
								'object_id' => $data_pos->id,
								'object_name' => $descrip,
								'type' => $row->action,
								'images' => array(
									array(
										'url' => UploadFile::urlBig().$data_pos->file_name, 'size' => 'B'),
									array(
										'url' => UploadFile::urlMedium().$data_pos->file_name, 'size' => 'M'),
									array(
										'url' => UploadFile::urlSmall().$data_pos->file_name, 'size' => 'SN'),
									array(
										'url' => UploadFile::urlSmallSquare().$data_pos->file_name, 'size' => 'SS')
								));
						}else{
							$resultados[] = lang('ntf_a').' '.$row->name_subject.' '.lang($tip).': '.$descrip;
						}
					}
				break;
				case ($row->action == 6 || $row->action == 7 || $row->action == 9): //acept/deny friend
					$sql = $row->action==7? "ui.user_id = ".$row->subject: "ui.user_id = ".$row->object;
					$res_post = $this->db->query("SELECT u.id, Concat(u.name, ' ', u.last_name) AS name, i.file_name, i.file_type, i.file_size 
						FROM users AS u INNER JOIN users_imgs AS ui ON u.id = ui.user_id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE ".$sql." LIMIT 1");
					
					if($res_post->num_rows() > 0){
						$data_pos = $res_post->row();
						$tip = ($row->action==6 || $row->action==9)? ($row->action==9?'ntf_acept_friend': 'ntf_acept_friend_me'): 'ntf_deny_friend';
						
						if($params['output_format'] == 1){
							if($row->action==6){
								$resultados[] = '<div class="notifica_img">
									<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
								<div class="notifica_info'.$sttatus.'">
									'.lang($tip).' <a href="'.Sys::lml_get_url('user', $data_pos->id).'" class="link_blue">'.$data_pos->name.'</a>
								</div>';
							}else if($row->action==9){
								$resultados[] = '<div class="notifica_img">
									<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
								<div class="notifica_info'.$sttatus.'">
									<a href="'.Sys::lml_get_url('user', $data_pos->id).'" class="link_blue">'.$data_pos->name.'</a> '.lang($tip).'
								</div>';
							}else{
								$resultados[] = '<div class="notifica_img">
									<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
								<div class="notifica_info'.$sttatus.'">
									<a href="'.Sys::lml_get_url('user',$row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.lang($tip).'
								</div>';
							}
						}else if($params['output_format'] == 2){
							$resultados[] = array(
								'subject_id' => $row->subject,
								'subject_name' => $row->name_subject,
								'texts' => array(lang($tip)),
								'type' => $row->action,
								'images' => array(
									array(
										'url' => UploadFile::urlBig().$data_pos->file_name, 'size' => 'B'),
									array(
										'url' => UploadFile::urlMedium().$data_pos->file_name, 'size' => 'M'),
									array(
										'url' => UploadFile::urlSmall().$data_pos->file_name, 'size' => 'SN'),
									array(
										'url' => UploadFile::urlSmallSquare().$data_pos->file_name, 'size' => 'SS')
								));
						}else{
							$resultados[] = $row->name_subject.' '.lang($tip);
						}
					}
				break;
			}
		}
		$num = isset($params['pagination'])? array("num_notification" => $total_results): $this->getNumNotification($params);
		return array('notifications' => $resultados, 'num_notification' => $num['num_notification']);
	}
	
	
	
	
	public function sendNotifications($params){
		Sys::loadLanguage(null, 'notification');
		
		$send_mail = true;
		$html = $to = $subject = '';
		
		if($params['action'] == 8){
			//notificaciones de amistad
			$res_amis = $this->db->query("SELECT u.id AS user_id, Concat(u.name, ' ', u.last_name) AS name, 
					(SELECT email FROM users WHERE id = f.friend_id) AS email, ui.id, i.file_name, 
					i.file_type, i.file_size, ui.is_primary 
				FROM users AS u INNER JOIN friends AS f ON u.id = f.user_id INNER JOIN users_imgs AS ui ON ui.user_id = f.user_id 
					INNER JOIN images AS i ON i.id = ui.image_id 
				WHERE f.friend_id = ".$params['token_user_id']." AND f.user_id = ".$params['uuser_id']." AND f.status = 0");
			if($res_amis->num_rows() > 0){
				$row = $res_amis->row();
				
				$to .= ", ".$row->email;
				$subject = $row->name.' '.lang('ntf_wants_your_friend');
				
				$html = '<div class="notifica_img">
					<img src="'.UploadFile::urlSmallSquare().$row->file_name.'" width="40" height="40"></div>
				<div class="notifica_info sinver">
					<a href="'.Sys::lml_get_url('user', $row->user_id).'" class="link_blue">'.$row->name.'</a> '.lang('ntf_wants_your_friend').'
					<div class="clear"></div>
				</div>';
			}else
				$send_mail = false;
		}else{
			$sql_ex = '';
			if($params['action'] == 6 || $params['action'] == 9)
				$sql_ex = " AND nu.user_id = ".$params['token_user_id'];
			$res = $this->db->query("SELECT nu.user_id, nh.id AS notifi_id, (
					SELECT CONCAT( name,  ' ', last_name ) 
					FROM users
					WHERE id = nu.user_id
				) AS name_from, nh.subject, CONCAT( u.name,  ' ', u.last_name ) AS name_subject, nh.action, nh.details, nh.object, nu.status,
				(SELECT email FROM users WHERE id = nu.user_id) AS email
			FROM notifications_user AS nu
				INNER JOIN notifications_history AS nh ON nh.id = nu.notification_history_id
				INNER JOIN users AS u ON u.id = nh.subject
			WHERE nh.subject = ".$params['token_user_id'].$sql_ex." AND nu.sent_mail = 0 AND nh.action = ".$params['action']."
			ORDER BY nh.id DESC");
			
			switch($params['action']){
				case 0: //publicacion postyle
					if($res->num_rows() > 0){
						$row = array();
						foreach($res->result() as $row){
							$to .= ", ".$row->email;
							$this->db->query("UPDATE notifications_user SET sent_mail = 1 WHERE user_id = ".$row->user_id." AND notification_history_id = ".$row->notifi_id);
						}
						
						$res_post = $this->db->query("SELECT p.id, p.description, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM postyles AS p INNER JOIN postyle_imgs AS ui ON ui.postyle_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." AND p.enable = 1 LIMIT 1");
						if($res_post->num_rows() > 0){
							$data_pos = $res_post->row();
							$descrip = $data_pos->description==''? lang('see'): substr($data_pos->description, 0, 30);
						
							$subject = $row->name_subject.' '.lang('ntf_create_publication');
							$html = '<div class="notifica_img">
								<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
							<div class="notifica_info">
							<a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
								lang('ntf_create_publication').': <a href="'.Sys::lml_get_url('postyle', $data_pos->id).'" class="link_blue">'.$descrip.'</a>
							</div>';
						}else
							$send_mail = false;
					}else
						$send_mail = false;
				break;
				case 1: //comentario postyle
					if($res->num_rows() > 0){
						$row = array();
						foreach($res->result() as $row){
							$to .= ", ".$row->email;
							$this->db->query("UPDATE notifications_user SET sent_mail = 1 WHERE user_id = ".$row->user_id." AND notification_history_id = ".$row->notifi_id);
						}
						
						$res_post = $this->db->query("SELECT p.id, p.description, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM postyles AS p INNER JOIN postyle_imgs AS ui ON ui.postyle_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." AND p.enable = 1 LIMIT 1");
						if($res_post->num_rows() > 0){
							$data_pos = $res_post->row();
							$descrip = $data_pos->description==''? lang('see'): substr($data_pos->description, 0, 30);
						
							$subject = $row->name_subject.' '.lang('ntf_mentioned_publication');
							$html = '<div class="notifica_img">
								<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
							<div class="notifica_info">
								<a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
								lang('ntf_mentioned_publication').': <a href="'.Sys::lml_get_url('postyle', $data_pos->id).'" class="link_blue">'.$descrip.'</a>
							</div>';
						}else
							$send_mail = false;
					}else
						$send_mail = false;
				break;
				case ($params['action'] == 2 || $params['action'] == 3): //like/dislike postyle
					if($res->num_rows() > 0){
						$row = array();
						foreach($res->result() as $row){
							$to .= ", ".$row->email;
							$this->db->query("UPDATE notifications_user SET sent_mail = 1 WHERE user_id = ".$row->user_id." AND notification_history_id = ".$row->notifi_id);
						}
						
						$res_post = $this->db->query("SELECT p.id, p.description, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM postyles AS p INNER JOIN postyle_imgs AS ui ON ui.postyle_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." AND p.enable = 1 LIMIT 1");
						if($res_post->num_rows() > 0){
							$tip = ($params['action']==2? 'ntf_likes': 'ntf_dislikes');
							$data_pos = $res_post->row();
							$descrip = $data_pos->description==''? lang('see'): substr($data_pos->description, 0, 30);
						
							$subject = lang('ntf_a').' '.$row->name_subject.' '.lang($tip).': ';
							$html = '<div class="notifica_img">
								<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
							<div class="notifica_info">
								'.lang('ntf_a').' <a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
								lang($tip).': <a href="'.Sys::lml_get_url('postyle', $data_pos->id).'" class="link_blue">'.$descrip.'</a>
							</div>';
						}else
							$send_mail = false;
					}else
						$send_mail = false;
				break;
				case ($params['action'] == 4 || $params['action'] == 5): //like/dislike items
					if($res->num_rows() > 0){
						$row = array();
						foreach($res->result() as $row){
							$to .= ", ".$row->email;
							$this->db->query("UPDATE notifications_user SET sent_mail = 1 WHERE user_id = ".$row->user_id." AND notification_history_id = ".$row->notifi_id);
						}
						
						$res_post = $this->db->query("SELECT p.id, p.label, ui.id AS image_id, i.file_name, i.file_type, i.file_size 
						FROM items AS p INNER JOIN items_imgs AS ui ON ui.item_id = p.id INNER JOIN images AS i ON i.id = ui.image_id 
						WHERE p.id = ".$row->details." LIMIT 1");
						if($res_post->num_rows() > 0){
							$tip = $params['action']==4? 'ntf_likes': 'ntf_dislikes';
							$data_pos = $res_post->row();
							$descrip = $data_pos->label==''? lang('see'): substr($data_pos->label, 0, 30);
						
							$subject = lang('ntf_a').' '.$row->name_subject.' '.lang($tip).': ';
							$html = '<div class="notifica_img">
									<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
								<div class="notifica_info">
									'.lang('ntf_a').' <a href="'.Sys::lml_get_url('user', $row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.
									lang($tip).': '.$descrip.'
								</div>';
						}else
							$send_mail = false;
					}else
						$send_mail = false;
				break;
				case ($params['action'] == 6 || $params['action'] == 7 || $params['action'] == 9): //acept/deny friend
					if($res->num_rows() > 0){
						$row = array();
						foreach($res->result() as $row){
							$to .= ", ".$row->email;
							$this->db->query("UPDATE notifications_user SET sent_mail = 1 WHERE user_id = ".$row->user_id." AND notification_history_id = ".$row->notifi_id);
						}
						
						$sql = $params['action']==7? "ui.user_id = ".$row->subject: "ui.user_id = ".$row->object;
						$res_post = $this->db->query("SELECT u.id, Concat(u.name, ' ', u.last_name) AS name, i.file_name, i.file_type, i.file_size 
							FROM users AS u INNER JOIN users_imgs AS ui ON u.id = ui.user_id INNER JOIN images AS i ON i.id = ui.image_id 
							WHERE ".$sql." LIMIT 1");
						
						if($res_post->num_rows() > 0){
							$data_pos = $res_post->row();
							$tip = ($params['action']==6 || $params['action']==9)? 
								($params['action']==9?'ntf_acept_friend': 'ntf_acept_friend_me'): 'ntf_deny_friend';
						
							if($params['action']==6){
								$subject = lang($tip).' '.$data_pos->name;
								$html = '<div class="notifica_img">
									<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
								<div class="notifica_info">
									'.lang($tip).' <a href="'.Sys::lml_get_url('user', $data_pos->id).'" class="link_blue">'.$data_pos->name.'</a>
								</div>';
							}else if($params['action']==9){
								$subject = $data_pos->name.' '.lang($tip);
								$html = '<div class="notifica_img">
									<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
								<div class="notifica_info">
									<a href="'.Sys::lml_get_url('user', $data_pos->id).'" class="link_blue">'.$data_pos->name.'</a> '.lang($tip).'
								</div>';
							}else{
								$subject = $row->name_subject.' '.lang($tip);
								$html = '<div class="notifica_img">
									<img src="'.UploadFile::urlSmallSquare().$data_pos->file_name.'" width="40" height="40"></div>
								<div class="notifica_info">
									<a href="'.Sys::lml_get_url('user',$row->subject).'" class="link_blue">'.$row->name_subject.'</a> '.lang($tip).'
								</div>';
							}
						}else
							$send_mail = false;
					}else
						$send_mail = false;
				break;
			}
		}
		
		if($send_mail){
			$to = substr($to, 2);
			
			$message = '
			<html>
			<head>
			  <title>'.$subject.'</title>
			</head>
			<body>
			  <table>
			    <tr>
			      <td>'.$html.'</td>
			    </tr>
			  </table>
			</body>
			</html>
			';
			
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			
			// Additional headers
			$headers .= 'From: '.Sys::$title_web.' <notification@youhaveiton.com>' . "\r\n";
			$headers .= 'Bcc: '.$to. "\r\n";
			
			//echo $to."-", $subject."-", $message;
			try {
				@mail('', $subject, $message, $headers);
			}catch(Exception $e){}
		}
	}
	
	
}
?>