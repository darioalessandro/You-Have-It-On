<?php
class Test_Js extends CI_Controller{
	function __construct(){
		parent::__construct();
	}
	
	function js_lml(){
		
		$this->load->helper(array('form', 'url'));
		$this->load->view('test_js_form');
	}
	
	
	function item(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Idiomas::espaniol, "view");
		
		$data = null;
		$_GET["lmlid"] = isset($_GET["lmlid"])? intval($_GET["lmlid"]): 0;
		
		$params = array(
			"lang"=>					'spa',
			"consumer_key"=>			Sys::$consumer_key,
			"oauth_token"=>				Sys::getTokenVal(),
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
		
		$view_error = "view_errors";
		if($data != null){
			$params["title"] = lang("title_item_details", $data["items"][0]["label"]).Sys::$title_web;
			$params["data"] = $data;
			$view_error = "pag_item_details";
		}else{ 
			$params["title"] = lang("title_pag_error").Sys::$title_web;
			$params["message"] = lang("message_pag_error1", "Item");
		}
			
		echo $this->load->view("ui_controls/header", $params, true);
		echo $this->load->view("ui_controls/".$view_error, $params, true);
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	
	function gallery(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Idiomas::espaniol, "view");
		
		echo $this->load->view("ui_controls/header", array(), true);
		
		echo $this->load->view("ui_controls/gallery", array(), true);
		
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	
	function tab(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Idiomas::espaniol, "view");
		
		echo $this->load->view("ui_controls/header", array(), true);
		
		$this->load->model("item_model");
		
		
		$tabcontrol = new TabControl();
		
		$tabs = array(
			array(
				"url" 		=> 'http://www.google.com/',
				"action" 	=> '',
				"label" 	=> 'Detalles',
				"icon" 		=> base_url()."application/images/more.png",
				"subitems" 	=> array()
			),
			array(
				"url" 		=> 'javascript:void(0);',
				"action" 	=> '',
				"label" 	=> 'Mapa',
				"subitems" 	=> array(
					array(
						"url" 		=> 'http://www.google.com/',
						"action" 	=> '',
						"label" 	=> 'Info1'
					),
					array(
						"url" 		=> 'javascript:void(0);',
						"action" 	=> '',
						"label" 	=> 'Info2'
					),
					array(
						"url" 		=> 'http://www.google.com/',
						"action" 	=> '',
						"label" 	=> 'Info3'
					)
				)
			),
			array(
				"url" 		=> 'javascript:void(0);',
				"action" 	=> '',
				"label" 	=> 'Sucursales',
				"subitems" 	=> array(
					array(
						"url" 		=> 'javascript:void(0);',
						"action" 	=> '',
						"label" 	=> 'Info4'
					),
					array(
						"url" 		=> 'javascript:void(0);',
						"action" 	=> '',
						"label" 	=> 'Info5'
					),
					array(
						"url" 		=> 'http://www.google.com/',
						"action" 	=> '',
						"label" 	=> 'Info6'
					)
				)
			)
		);
		$data = array(
			"width_control" => 800,
			"items" 		=> $tabs,
			"item_select"	=> 0
		);
		$tabcontrol->ini($data);
		$tabcontrol->printHtml(true);
		
		$data = array(
			"width_control" => 500,
			"items" 		=> $tabs,
			"item_select"	=> 1
		);
		$tabcontrol->ini($data);
		$tabcontrol->printHtml(true);
		
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	
	function load_image(){
		$this->load->helper(array('form', 'url'));
		
		$data = array("error" => "");
		
		if(isset($_POST["upload"])){
			$config['upload_path'] = './application/files/temp/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '700';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';
			
			include BASEPATH.'libraries/Upload.php';
			$upload = new CI_Upload($config);
	
			if(!$upload->do_upload("images", 0))
				$data = array('error' => $upload->display_errors());
			else{
				$data = array('error' => $upload->data());
				$data["error"]["url_file"] = $this->config->item('base_url').'application/files/temp/'.$data["error"]["file_name"];
			}
			
			var_dump($data);
		}
		
		$this->load->view('ui_controls/uploadImgPostyle', $data);
	}
	
	function items(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Idiomas::espaniol, "view");
				
		$this->load->model("item_model");
		
		
		$itemlistcontro = new ItemListControl();
		$params = array(
			"lang"=>					'spa',
			"consumer_key"=>			'89c1cd99a5ed1f0362668d15c15cb58104d7675fc',
			"oauth_token"=>				'a16029b067f18a0900871779e3a314fc04d76773c',
			"result_items_per_page"=>	'4',
			"result_page"=>				'1',
			"filter_category_id"=>		'4'
		);
		$this->load->model('user_model');
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
		
		$params["title"] = '';
		echo $this->load->view("ui_controls/header", $params, true);
		
		$data = $this->item_model->getItems($params);
		
		$data = array(
			"titulo" 	=> "Cat mart",
			"items" 	=> $data["items"],
			"categorys"	=> $data["category"],
			"total_items" 	=> $data["total_rows"],
			"params" 	=> $params,
			"view_actionbar" => true,
			"num_cols"		=> 1,
			"cat_per_page"	=> 1,
			"detailed_item"	=> true,
			"open_detail_win_click"	=> false
		);
		$itemlistcontro->ini($data);
		$itemlistcontro->printHtml(true);
		
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function friends(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Idiomas::espaniol, "view");
				
		$this->load->model("user_model");
		$friendcontro = new FriendListControl();
		$params = array(
			"lang"=>					'spa',
			"consumer_key"=>			'89c1cd99a5ed1f0362668d15c15cb58104d7675fc',
			"oauth_token"=>				'a16029b067f18a0900871779e3a314fc04d76773c',
			"result_items_per_page"=>	'2',
			"result_page"=>				'1',
			"user_id"=>					'2'
		);
		
		echo $this->load->view("ui_controls/header", $params, true);
		
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['token_user_id'] = $data_usr['id'];
		
		$data = $this->user_model->getFriendsUser($params);
		$data = array(
			"titulo" 		=> "Lista de amigos",
			"items" 		=> $data["friends"],
			"total_items" 	=> $data["total_rows"],
			"params" 		=> $params,
			"view_actionbar" => true,
			"num_cols"		=> 1
		);
		$friendcontro->ini($data);
		$friendcontro->printHtml(true);
		
		
		echo $this->load->view("ui_controls/footer", array(), true);
	}
	
	function controls(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Idiomas::espaniol, "view");
		
		$this->load->model("postyle_model");
		
		
		$postylecontro = new PostyleControl();
		$params = array(
			"lang"=>					'spa',
			"consumer_key"=>			'89c1cd99a5ed1f0362668d15c15cb58104d7675fc',
			"oauth_token"=>				'a16029b067f18a0900871779e3a314fc04d76773c',
			"result_items_per_page"=>	'20',
			"result_page"=>				'1',
			"filter_user_id"=>			'2'
		);
		$this->load->model('user_model');
		$res_usr = $this->user_model->getIdUserToken($params);
		$data_usr = $res_usr->row_array();
		$params['user_id'] = $data_usr['id'];
		
		echo $this->load->view("ui_controls/header", $params, true);
		
		$data = $this->postyle_model->getPostyles($params);
		$data = array(
			"titulo" 	=> "",
			"items" 	=> $data["postyles"],
			"params" 	=> $params
		);
		$postylecontro->ini($data);
		$postylecontro->printHtml(true);
		
		echo $this->load->view("ui_controls/footer", array(), true);
	}
}
?>