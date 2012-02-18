<?php
class Accessories_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('comments_model');
	}
	
	/**
	 * SERVICIOS DE COMMENT Accesorios
	 * Agrega un comentario a los accesorios de una persona
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function comment(&$params){
		$res_post = $this->db->query("SELECT id FROM users WHERE id = ".$params['accessories_user_id']);
		if($res_post->num_rows() > 0){
			$id_comment = $this->comments_model->insertComment($params);
			$this->db->query("INSERT INTO my_accessories_comments (user_id, comment_id) 
				VALUES ('".$params['accessories_user_id']."', '".$id_comment."');");
			
			return true;
		}
		return false;
	}
	
	/**
	 * habilita o deshabilita un comentario de accesorios de un usuario
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function disable_comment(&$params){
		$res_post = $this->db->query("SELECT mac.id, mac.user_id AS accessories_user_id, c.users_id AS comment_user_id 
			FROM my_accessories_comments AS mac INNER JOIN comments AS c ON c.id = mac.comment_id 
			WHERE mac.id = ".$params['comment_id']);
		if($res_post->num_rows() > 0){
			$data_post = $res_post->row();
			//Verifica si el usuario puede realizar esta accion
			if($data_post->accessories_user_id == $params['user_id'] || $data_post->comment_user_id == $params['user_id']){
				$this->db->query("UPDATE my_accessories_comments SET enable=".$params['enable']." WHERE id = ".$params['comment_id']);
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
		$this->comments_model->table = 'my_accessories_comments';
		$this->comments_model->field = 'user_id';
		$comments = array("comments" => $this->comments_model->getComments($params));
		return $comments;
	}
}
?>