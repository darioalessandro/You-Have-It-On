<?php
class home extends CI_Controller{
	private $token = '';
	
	function __construct(){
		parent::__construct();
		
		$zone = Sys::getLocalTimezone();
		if($zone!=false){
			date_default_timezone_set($zone["region"]);
			$this->db->query("SET time_zone = '".$zone["offset"]."';");
		}
	}
	
	public function _remap($method){
		$this->token = Sys::getTokenVal();
		if($this->token == false){
			if($method != 'index' || (isset($_GET['lmlid']) && $method == 'index')){
				setcookie('lml_goto', Sys::getCurrentUrl(), time()+1200, '/');
				header("Location: ".Sys::$url_base."services/facebook/sigin");
				exit;
			}
			$this->phome();
		}else{
			if(isset($_GET['import']))
				$this->importFBFriends();
			else if(isset($_COOKIE["lml_goto"])){ //redireccionar a alguna url despues de logearse
				$url = $_COOKIE["lml_goto"];
				setcookie('lml_goto', '', time()-1200, '/');
				header("Location: ".$url);
			}else if($method=="index"){
		    	$this->verMuro();
			}else
		    	$this->{$method}();
		}
	}

	
	function phome(){
		$this->load->library('user_agent');
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$this->load->model('user_model');
		
		$params = array(
			"consumer_secret"=>			Sys::$consumer_secret,
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token
		);
		
		$params["title"] = Sys::$title_web;
		
		/*if($this->agent->is_mobile()){
			echo $this->load->view("ui_controls/phome_movil", $params, true);
		}else{*/
			echo $this->load->view("ui_controls/header_home", $params, true);
			echo $this->load->view("ui_controls/phome", $params, true);
			echo $this->load->view("ui_controls/footer_home", array(), true);
		//}
	}
	
	
	/**
	 * Importar los contactos de facebook al sistema
	 */
	function importFBFriends(){
		$this->load->library('user_agent');
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$this->load->model('user_model');
		
		$params = array(
			"consumer_secret"=>			Sys::$consumer_secret,
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token
		);
		
		$params["title"] = lang("sync_contacts_facebook")." - ".Sys::$title_web;
		
		echo $this->load->view("ui_controls/header_home", $params, true);
		echo $this->load->view("ui_controls/pag_import_contactsfb", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	
	function verMuro(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$this->load->model('user_model');
		$this->load->model("postyle_model");
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token,
			"filter_both"=>				"1",
			"pos_menu"=>				14
		);
		
		//datos del usuario de token
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
		
		//si el usuario es el mismo del token regresa al home
		if($params["token_user_id"] == $_GET["lmlid"])
			redirect(Sys::$url_base.'?lang='.Sys::getLang());
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['token_user_id']);
			
		$params["title"] = Sys::$title_web;
		
		//$view_error = "view_errors";
		//$params["title"] = lang("title_pag_error").Sys::$title_web;
		//$params["message"] = lang("message_pag_error1", "Item");
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/home", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	private function getNumNotifi($user_id){
		$this->load->model("notification_model");
		$params = array('token_user_id' => $user_id);
		$num = $this->notification_model->getNumNotification($params);
		if($num['num_notification'] == 0)
			$resul = array('num_notifica' => $num['num_notification'], 'class' => '');
		else
			$resul = array('num_notifica' => $num['num_notification'], 'class' => 'notifi_alert');
		return $resul;
	}
	
	function my_postyles(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
    
		$this->load->model('user_model');
		$this->load->model("postyle_model");
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token,
			"pos_menu"=>				43
		);
		
		//datos del usuario de token
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
		
		
		//si el usuario es el mismo del token regresa al home
		if($params["token_user_id"] == $_GET["lmlid"])
			redirect(Sys::$url_base."my_postyles?lang=".Sys::getLang());
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['token_user_id']);	
		
		$params["title"] = Sys::$title_web;
		
		//$view_error = "view_errors";
		//$params["title"] = lang("title_pag_error").Sys::$title_web;
		//$params["message"] = lang("message_pag_error1", "Item");
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/home", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function my_friends(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$this->load->model('user_model');
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token,
			"pos_menu"=>				95
		);
		
		//datos del usuario de token
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
		
		//si el usuario es el mismo del token regresa al home
		if($params["token_user_id"] == $_GET["lmlid"])
			redirect(Sys::$url_base."my_friends?lang=".Sys::getLang());
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['token_user_id']);	
		
		$params["title"] = lang("my")." ".lang("friends")." - ".Sys::$title_web;
		
		//$view_error = "view_errors";
		//$params["title"] = lang("title_pag_error").Sys::$title_web;
		//$params["message"] = lang("message_pag_error1", "Item");
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/myFriends", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function my_items(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$this->load->model('user_model');
		$this->load->model("item_model");
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token,
			"pos_menu"=>				71
		);
		
		//datos del usuario de token
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
		
		//si el usuario es el mismo del token regresa al home
		if($params["token_user_id"] == $_GET["lmlid"])
			redirect(Sys::$url_base."my_items?lang=".Sys::getLang());
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['token_user_id']);	
		
		$params["title"] = lang("my")." Items - ".Sys::$title_web;
		
		//$view_error = "view_errors";
		//$params["title"] = lang("title_pag_error").Sys::$title_web;
		//$params["message"] = lang("message_pag_error1", "Item");
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/myItems", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function item(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token,
			"result_items_per_page"=>	'4',
			"result_page"=>				'1',
			"filter_item_id"=>			$_GET["lmlid"]
		);
		if($_GET["lmlid"] > 0){
			$this->load->model('item_model');
			//$itemlistcontro = new ItemListControl();
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken($params);
			$data_usr = $res_usr->row_array();
			$params['token_user_id'] = $data_usr['id'];
			
			$data = $this->item_model->getItems($params);
			if(isset($data["total_rows"])){
				if($data["total_rows"] == 0)
					$data = null;
			}else
				$data = null;
			//var_dump($data);
		}
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['token_user_id']);	
		
		$view_error = "view_errors";
		if($data != null){
			$params["title"] = lang("title_item_details", $data["items"][0]["label"]).Sys::$title_web;
			$params["data"] = $data;
			$view_error = "pag_item_details";
		}else{ 
			$params["title"] = lang("title_pag_error").Sys::$title_web;
			$params["message"] = lang("message_pag_error2", "Item");
		}
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/".$view_error, $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function notifications(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token,
			"result_items_per_page"=>	'15',
			"result_page"=>				'1',
			"output_format" =>			'1',
			"pagination" =>				'y'
		);
		
		$this->load->model("notification_model");
		$this->load->model('user_model');
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['user_id'] = $params['token_user_id'] = $data_usr['id'];
		
		$this->notification_model->updateSee($params);
		
		$data = $this->notification_model->getNotifications($params);
		$params["data"] = $data;
		
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['user_id']);	
		
		$params["title"] = lang("title_pnotifications").Sys::$title_web;
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/pag_notifications", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function postyle(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token,
			"result_items_per_page"=>	'4',
			"result_page"=>				'1',
			"filter_postyle_id"=>		$_GET["lmlid"]
		);
		if($_GET["lmlid"] > 0){
			$this->load->model("postyle_model");
			$this->load->model('user_model');
			$res_usr = $this->user_model->getIdUserToken($params);
			$data_usr = $res_usr->row_array();
			$params['flt_no'] = 'no';
			$params['user_id'] = $data_usr['id'];
			
			$data = $this->postyle_model->getPostyles($params);
			if(isset($data["total_rows"])){
				if($data["total_rows"] == 0)
					$data = null;
			}else
				$data = null;
			//var_dump($data);
		}
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['user_id']);	
		
		$view_error = "view_errors";
		if($data != null){
			$params["title"] = lang("title_postyle_details", $data["postyles"][0]["user_name"]).Sys::$title_web;
			$params["data"] = $data;
			$view_error = "postyle_detail";
		}else{ 
			$params["title"] = lang("title_pag_error").Sys::$title_web;
			$params["message"] = lang("message_pag_error2", "Postyle");
		}
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/".$view_error, $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	/** New Postyle **/
	function new_postyle(){
		$this->checkAccessToken();
		
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		$this->load->model('user_model');
			
		$params = array(
			"lang"=>					Sys::getLang(),
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				$this->token
		);
		
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
		
		//obtenemos el numero de notificaciones
		$params['notifi'] = $this->getNumNotifi($params['token_user_id']);	
		
		$params["title"] = lang("title_new_postyle").Sys::$title_web;
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/pag_new_postyle", $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function load_image(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$data = array("error" => "");
		
		if(isset($_POST["upload"])){
			$config = $this->configImg();
			if(count($config) == 1){
				//$config['upload_path'] = APPPATH.'application/files/temp/';
				ini_set("max_execution_time", "300");
				ini_set("max_input_time", "300");
				ini_set('memory_limit', '128M');
				$this->load->library('my_upload', $config);
				
				$data = $this->my_upload->upload('', null);
		
				if(isset($data[0]["code"]))
					$data = array('error' => $data[0]["message"]);
				else{
					$data = array('error' => $data[0][0]["file"]);
					$data["error"]["url_file"] = $this->config->item('base_url').'application/files/temp/'.$data["error"]["file_name"];
				}
			}else
				$data = array('error' => lang("select_an_image"));
		}
		
		$this->load->view('ui_controls/uploadImgPostyle', $data);
	}
	
	private function configImg(){
   		$config = array();
   		if(isset($_FILES['images'])){
			foreach($_FILES['images']['name'] as $key => $file){
				if($file != ''){
					$config[] = array(
						'input_file' => 'images',
						'key' => $key,
						'size' => 5120,
						'dimensions' => '6272x6272',
						'min_dimensions' => '150x150',
						'format' => array('png','jpg'),
						'resize' => array('t')
					);
				}
			}
   		}
		return $config;
    }
	
	
	
	
	public function checkAccessToken(){
		$result = $this->config->item('store_oauth')->checkAccessToken(
					Sys::$consumer_key, 
					$this->token);
				
		if($result)
			return true;
		else 
			header("Location: ".$this->config->item('base_url')."services/user/logout?lang=".Sys::getLang());
	}
}
?>