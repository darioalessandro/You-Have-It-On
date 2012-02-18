<?php defined('BASEPATH') OR exit('No direct script access allowed');

class my_twitter{
	private $CI;
	private $app_id;
	private $app_secret;
	
	private $url_postyle = "";
	private $url_item = "";
	
	function my_twitter(){
		$this->CI =& get_instance();
		$this->CI->load->library('user_agent');
		$this->CI->load->database();
		
		$this->url_postyle = $this->CI->config->item('base_url').'postyle';
		$this->url_item = $this->CI->config->item('base_url').'item';
		
		$this->app_id = Sys::$twitter_app_id;
		$this->app_secret = Sys::$twitter_app_secret;
	}
	
	
	private function publicar($access_token, $msg){
		if(count($access_token) == 2){
			$tmhOAuth = new tmhoauth(array(
			  'consumer_key'    => $this->app_id,
			  'consumer_secret' => $this->app_secret,
			  'user_token'      => $access_token[0],
			  'user_secret'     => $access_token[1],
			  'use_ssl'			=> false
			));
			
			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), array(
			  'status' => $msg
			));
		}
		/*if($code == 200) {
		  $tmhOAuth->pr(json_decode($tmhOAuth->response['response']));
		} else {
		  $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
		}*/
	}
	
	/**
	 * Publica un postyle en twitter
	 * @param entero $postyle_id
	 */
	public function postyle($postyle_id=0, $user_id=0, $action='add'){
		$res_usr = $this->CI->db->query("SELECT token_twitter FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = explode("&=&", $data_usr->token_twitter);
			
			//obtenemos los datos del postyle
			$res = $this->CI->db->query("SELECT id, description 
				FROM postyles WHERE id = ".$postyle_id);
			
			if($res->num_rows() > 0){
				$data = $res->row();
				$goo = new goo();
				$url = $goo->shorten($this->url_postyle.'/'.$data->id);
				
				$quedan = (139-strlen($url))-4;
				$data->description = substr($data->description, 0, $quedan).'... '.$url;
				
				$this->publicar($access_token, $data->description);
			}
		}
	}
	
	
	public function comment($comment=0, $user_id=0, $action='add'){
		$res_usr = $this->CI->db->query("SELECT token_twitter FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = explode("&=&", $data_usr->token_twitter);
			
			//obtenemos los datos del comentario
			$res = $this->CI->db->query("SELECT co.id AS comment_id, c.comment, co.facebook_id 
			FROM comments AS c INNER JOIN ".$comment['table']." AS co ON c.id = co.comment_id 
			WHERE co.enable = 1 AND c.id = ".$comment['comment_id']." 
				AND co.".$comment['field']." = ".$comment[($comment['key_param']!=''? $comment['key_param']: $comment['field'])]);
			
			if($res->num_rows() > 0){
				$data = $res->row();
				
				$url = '';
				switch($comment['field']){
					case 'item_id': $url = $this->url_item; break;
					case 'postyle_id': $url = $this->url_postyle; break;
				}
				
				if($url != ''){
					$goo = new goo();
					$url = $goo->shorten($url.'/'.$comment[($comment['key_param']!=''? $comment['key_param']: $comment['field'])]);
				}
				
				$quedan = (139-strlen($url))-4;
				$data->comment = substr($data->comment, 0, $quedan).'... '.$url;
				
				$this->publicar($access_token, $data->comment);
			}
		}
	}
	
	
	/**
	 * Publica un item en twitter
	 * @param entero $item_id
	 */
	public function item($item_id=0, $user_id=0, $action='add'){
		$res_usr = $this->CI->db->query("SELECT token_twitter FROM users WHERE id = ".$user_id);
		if($res_usr->num_rows() > 0){
			$data_usr = $res_usr->row();
			$access_token = explode("&=&", $data_usr->token_twitter);
			
			//obtenemos los datos del item
			$res = $this->CI->db->query("SELECT i.id, i.label, b.name AS brand, sl.name AS store 
				FROM items AS i LEFT JOIN brands AS b ON b.id = i.brand_id 
					LEFT JOIN store_locations AS sl ON sl.id = i.bought_in 
				WHERE i.id = ".$item_id." LIMIT 1");
			
			if($res->num_rows() > 0){
				$data = $res->row();
				$goo = new goo();
				$url = $goo->shorten($this->url_item.'/'.$data->id);
				
				Sys::loadLanguage(Sys::$idioma_load);
				$message = $data->label;
				if($data->brand != '' && $data->brand != NULL)
					$message .= ", ".lang('txt_brand').": ".$data->brand;
				if($data->brand != '' && $data->brand != NULL)
					$message .= ", ".lang('txt_store').": ".$data->store;
						
				$quedan = (139-strlen($url))-4;
				$message = substr($message, 0, $quedan).'... '.$url;
				
				$this->publicar($access_token, $message);
			}
		}
	}
	
	
	public function like($comment=0, $user_id=0, $action='add'){
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
					$graph_url = 'https://graph.facebook.com/'.$data->facebook_id.'/likes?method=delete&'.$data_usr->token_facebook;	
					$res = file_get_contents($graph_url);
				}
			}
		}
	}
	
}