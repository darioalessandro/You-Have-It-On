<?php
class Image_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	public function insertImage($params){
		$params['label'] = isset($params['label'])? $params['label']: '';
		$params['file_name'] = isset($params['file_name'])? $params['file_name']: '';
		$params['file_type'] = isset($params['file_type'])? $params['file_type']: '';
		$params['file_size'] = isset($params['file_size'])? $params['file_size']: '';
		
		$this->db->query("INSERT INTO images (label, file_name, file_type, file_size) 
				VALUES ('".$params['label']."', '".$params['file_name']."', '".$params['file_type']."', '".$params['file_size']."');"); 
		$res_img = $this->db->query("SELECT id FROM images ORDER BY id DESC LIMIT 1");
		if($res_img->num_rows() > 0){
			$data_img = $res_img->row();
			return $data_img->id;
		}
		return false;
	}
	
	/**
	 * Elimina fisicamente y de la bd una imagen
	 * @param int $id_image
	 */
	public function removeImage($id_image){
		$res_img = $this->db->query("SELECT id, file_name FROM images WHERE id = ".$id_image);
		if($res_img->num_rows() > 0){
			$data_img = $res_img->row();
			
			$this->deleteFile(UploadFile::pathBig().$data_img->file_name);
			$this->deleteFile(UploadFile::pathMedium().$data_img->file_name);
			$this->deleteFile(UploadFile::pathSmall().$data_img->file_name);
			$this->deleteFile(UploadFile::pathSmallSquare().$data_img->file_name);
			
			$this->db->query("DELETE FROM images WHERE id = ".$id_image);
		}
	}
	
	
	
	private function deleteFile($path){
		try{
			unlink($path);
		}catch(Exception $e){
		}
	}
}
?>