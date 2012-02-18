<?php
class adminqwy extends CI_Controller{
	private $token = '';
	
	function __construct(){
		parent::__construct();
		$this->load->library('pagination');
	}

	public function _remap($method){
		if($method=="index"){
	    	$this->verMenu();
		}else
	    	$this->{$method}();
	}
	
	function verMenu(){
		$this->load->helper(array('form', 'url'));
			
		$this->load->view("admin/menu");
	}

	function brands(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$params["title"] = lang("title_new_postyle").Sys::$title_web;
			
		echo $this->load->view("admin/brands");
	}
	
	function load_imageBrand(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$data = array("error" => "");
		
		if(isset($_POST["upload"])){
			//Insertamos una imagen al usuaior
			try{
				$data = array('error' => 'aa');
				foreach($_POST['imag'] as $img){
					if($img != ''){
						if(sys::urlValid($img)){
							$info_img = @getimagesize($img);
							
							$exte = UploadFile::getImgType($info_img["mime"]);
							if($exte != ''){
								$img_name = md5(microtime().rand(0, 9999));
								$carga_url = UploadFile::pathTemp().$img_name.'.'.$exte;
								if(!copy($img, $carga_url)){
								    $carga_url = '';
								    $data['error'] .= 'Error en la imagen: '.$img.'<br>';
								}
								
								if($carga_url != ''){
									$this->load->model('image_model');
									$this->load->library('my_upload');
									
									$conf_url = array(
										'resize' => array('b','m','s')
									);
									$imagess = $this->my_upload->upload($carga_url, $conf_url);
									foreach($imagess as $imgss){ 	//se insertan las imagenes al usuario
										$id_img = $this->image_model->insertImage($imgss[0]['file']);
										$this->db->query("INSERT INTO brands_imgs (brand_id, image_id, is_primary, size) 
											VALUES (".$_GET['id'].", ".$id_img.", '0', '".$imgss[0]['file']['size']."');");
									}
								}
							}else 
								$data['error'] .= 'Error en la imagen: '.$img.'<br>';
						}else 
							$data['error'] .= 'Error en la imagen: '.$img.'<br>';
					}
				}
			}catch(Exception $e){}
		}
		
		$this->load->view('admin/uploadImgbrands', $data);
	}
	
	
	
	function stores(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$params["title"] = lang("title_new_postyle").Sys::$title_web;
			
		echo $this->load->view("admin/stores");
	}
	
	function load_imagestores(){
		$this->load->helper(array('form', 'url'));
		Sys::loadLanguage(Sys::getLang(), "view");
		
		$data = array("error" => "");
		
		if(isset($_POST["upload"])){
			//Insertamos una imagen al usuaior
			try{
				$data = array('error' => 'aa');
				foreach($_POST['imag'] as $img){
					if($img != ''){
						if(sys::urlValid($img)){
							$info_img = @getimagesize($img);
							
							$exte = UploadFile::getImgType($info_img["mime"]);
							if($exte != ''){
								$img_name = md5(microtime().rand(0, 9999));
								$carga_url = UploadFile::pathTemp().$img_name.'.'.$exte;
								if(!copy($img, $carga_url)){
								    $carga_url = '';
								    $data['error'] .= 'Error en la imagen: '.$img.'<br>';
								}
								
								if($carga_url != ''){
									$this->load->model('image_model');
									$this->load->library('my_upload');
									
									$conf_url = array(
										'resize' => array('b','m','s')
									);
									$imagess = $this->my_upload->upload($carga_url, $conf_url);
									foreach($imagess as $imgss){ 	//se insertan las imagenes al usuario
										$id_img = $this->image_model->insertImage($imgss[0]['file']);
										$this->db->query("INSERT INTO store_locations_imgs (store_location_id, image_id, is_primary, size) 
											VALUES (".$_GET['id'].", ".$id_img.", '0', '".$imgss[0]['file']['size']."');");
									}
								}
							}else 
								$data['error'] .= 'Error en la imagen: '.$img.'<br>';
						}else 
							$data['error'] .= 'Error en la imagen: '.$img.'<br>';
					}
				}
			}catch(Exception $e){}
		}
		
		$this->load->view('admin/uploadImgstores', $data);
	}
	
	
}
?>