<?php
class Comments_model extends CI_Model{
	public $table;
	public $field;
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function insertComment(&$params){
		$params['comment'] = isset($params['comment'])? $params['comment']: '';
		$params['user_id'] = isset($params['user_id'])? $params['user_id']: '';
		
		$this->db->query("INSERT INTO comments (comment, users_id) 
				VALUES ('".$params['comment']."', '".$params['user_id']."');"); 
		$res_com = $this->db->query("SELECT id FROM comments ORDER BY id DESC LIMIT 1");
		if($res_com->num_rows() > 0){
			$data_com = $res_com->row();
			return $data_com->id;
		}
		return false;
	}
	
	public function getComments(&$params, $key_param=''){
		$canBeDeleted = '';
		if(isset($params["token_user_id"])){
			$canBeDeleted = ", is_my_friend(".$params["token_user_id"].", u.id, '".$this->field."', ".
				$params[($key_param!=''? $key_param:$this->field)].") AS can_be_deleted";
		}
		$query = Sys::pagination("
			SELECT co.id AS comment_id, c.comment, u.id AS user_id, u.name, u.last_name, c.date_added".$canBeDeleted." 
			FROM comments AS c INNER JOIN ".$this->table." AS co ON c.id = co.comment_id 
				INNER JOIN users AS u ON u.id = c.users_id 
			WHERE co.enable = 1 AND co.".$this->field." = ".$params[($key_param!=''? $key_param:$this->field)]." 
				ORDER BY c.date_added ASC", $params, true);
		
		$res = $this->db->query($query["query"]);
		$comments = array();
		foreach($res->result_array() as $row){
			$res_imgs = $this->db->query("SELECT bi.id, i.file_name, i.file_type 
				FROM users_imgs AS bi INNER JOIN images AS i ON i.id = bi.image_id 
				WHERE bi.user_id = ".$row['user_id']);
			$imgs = $res_imgs->row_array();
			
			$row["comment"] = nl2br($row["comment"]);
			
			$row['images'] = array();
			if(count($imgs) > 0){
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
			$comments[] = $row;
		}
		
		if(isset($params["token_user_id"]))
			return array("comments" => $comments, "total_rows" => $query["total_rows"]);
		else
			return $comments;
	}
}
?>