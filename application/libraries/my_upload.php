<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once BASEPATH.'libraries/Upload.php';

class my_upload extends CI_Upload{
	public $files = array();
	public $errors = array();
	
	private $path_small = '';
	private $path_small_s = '';
	private $path_medium = '';
	private $path_big = '';
	
	function __construct($confi=array()){
		parent::__construct();
		
		$this->path_big = UploadFile::pathBig();
		$this->path_medium = UploadFile::pathMedium();
		$this->path_small = UploadFile::pathSmall();
		$this->path_small_s = UploadFile::pathSmallSquare();
		
		$this->ini($confi);
		Sys::loadLanguage();
	}
	
	public function ini($confi=array()){
		if(count($confi) > 0){
			foreach($confi as $item){
				if(isset($item['input_file'])){
					if(isset($item['size']) && isset($item['dimensions']) && is_array($item['format'])
						&& is_array($item['resize']) && isset($item['key'])){
						$this->files[] = $item;
					}else{
						$this->errors[] = array(
							'code' => 301,
							'message' => lang('txt_upload_eparams'),
							'file' => $_FILES[$item['input_file']]['name'][$item['key']]
						);
					}
				}
			}
		}
	}
	
	public function upload($carga_url='', $conf_url=null){
		if($carga_url != '' && $conf_url != null){
			$this->upload_by_url($carga_url, $conf_url);
		}else{
			foreach($this->files as $key => $file){
				$config = $this->configFile($file);
				$this->initialize($config);
				
				if(!$this->do_upload($file['input_file'], $file['key'])){
					$this->errors[] = array(
								'code' => 301,
								'message' => $this->display_errors(),
								'file' => $_FILES[$file['input_file']]['name'][$file['key']]
							);
				}else{
					Sys::loadFile('image_lib', 'library');
					$image_lib = new CI_Image_lib();
					
					$data = $this->data();
					if($data['is_image']){
						
						if(array_search('s', $file['resize'])!==false)
							$file['resize'][] = 'sc';
							
						sort($file['resize']);
						foreach($file['resize'] as $item){
							$image_lib->clear();
							$data['resize'] = $item;
							$conf_img = $this->configImg($data);
							
							if($item == 'sc'){
								$this->cropImg($conf_img['new_image'], $conf_img['source_image']);
							}else{
								$image_lib->initialize($conf_img);
								$image_lib->resize();
							}
							$this->errors[$key][] = array(
									'code' => 200,
									'message' => lang('text_successful_process'),
									'file' => $this->getInfoImg($data, $conf_img)
								);
						}
					}
				}
			}
		}
		
		return $this->errors;
	}
	
	
	private function upload_by_url($carga_url, $file){
		Sys::loadFile('image_lib', 'library');
		$this->errors = array();
		
		$image_lib = new CI_Image_lib();
		
		$name = explode("/", $carga_url);
		$file_name = $name[(count($name)-1)];
		$exten = explode(".", $file_name);
		$exten = $exten[(count($exten)-1)];
		$file_name_code = md5($file_name.date("Y-m-d H:i:s:U")).".".$exten;
		$data = array(
			"full_path" => UploadFile::pathTemp().$file_name,
			"file_name" => $file_name_code,
			"file_type" => $exten,
			"image_type" => $exten,
			"is_image" => "1"
		);
		if(array_search('s', $file['resize'])!==false)
			$file['resize'][] = 'sc';
			
		sort($file['resize']);
		foreach($file['resize'] as $item){
			$image_lib->clear();
			$data['resize'] = $item;
			$conf_img = $this->configImg($data);
			
			if($item == 'sc'){
				$this->cropImg($conf_img['new_image'], $conf_img['source_image']);
			}else{
				$image_lib->initialize($conf_img);
				$image_lib->resize();
			}
			$this->errors[0][] = array(
					'code' => 200,
					'message' => lang('text_successful_process'),
					'file' => $this->getInfoImg($data, $conf_img)
				);
		}
	}
	
	private function getInfoImg($data, $conf_img){
		list($imagewidth, $imageheight, $imageType) = getimagesize($conf_img['new_image']);
		
		preg_match('#/(b|m|s|temp)(/s|/n|)/#si', $conf_img['new_image'], $matches);
		$matches[0] = $matches[1].str_replace("/", "", $matches[2]);
		
		return array(
			"file_name" => $data['file_name'],
			'file_mime' => $data['file_type'],
			'file_type' => $data['image_type'],
			'file_path' => $conf_img['new_image'],
			'file_width' => $imagewidth,
			'file_height' => $imageheight,
			'file_size' => ceil(filesize($conf_img['new_image'])/1024),
			'size' => $matches[0],
			'is_image' => $data['is_image']
		);
	}
	
	public function cropImg($thumb_image_name, $image, $newImageWidth=50, $newImageHeight=50, $conf=null){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		
		if($conf==null){
			if($imagewidth>=$imageheight){
		  		$des_width = ceil($imagewidth*$newImageWidth/$imageheight);
		  		$des_height = $newImageWidth;
		  		$des_x = ceil((($des_width-$des_height)/2)*$imagewidth/$des_width);
		  		$des_y = 0;
		  	}else{
		  		$des_width = $newImageHeight;
		  		$des_height = ceil($imageheight*$newImageHeight/$imagewidth);
		  		$des_x = 0;
		  		$des_y = ceil((($des_height-$des_width)/2)*$imageheight/$des_height);
		  	}
		}else{
			$des_width = $imagewidth = $conf["width"];
	  		$des_height = $imageheight = $conf["height"];
	  		$des_x = $conf["x"];
	  		$des_y = $conf["y"];
		}
	  	
	  	
		$newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}
		imagecopyresampled($newImage, $source, 0, 0, $des_x, $des_y, $des_width, $des_height, $imagewidth, $imageheight);
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$thumb_image_name); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumb_image_name);  
				break;
	    }
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}
		
	
	private function configImg($img, $copy=true){
		$conf_img['image_library'] = 'GD2';
		$conf_img['source_image']	= $img['full_path'];
		$conf_img['maintain_ratio'] = true;
		$conf_img['master_dim'] = 'width';
		
		switch($img['resize']){
			case 'b': 
				$conf_img['width'] = UploadFile::$width_big;
				$conf_img['height']	= UploadFile::$width_big;
				$conf_img['new_image'] = $this->path_big.$img['file_name'];
			break;
			case 'm': 
				$conf_img['width'] = UploadFile::$width_medium;
				$conf_img['height']	= UploadFile::$width_medium;
				$conf_img['new_image'] = $this->path_medium.$img['file_name']; 
			break;
			case 's': 
				$conf_img['width'] = UploadFile::$width_small;
				$conf_img['height']	= $conf_img['width'];
				$conf_img['new_image'] = $this->path_small.$img['file_name'];
			break;
			case 'sc': 
				$conf_img['width'] = UploadFile::$width_small;
				$conf_img['height']	= $conf_img['width'];
				$conf_img['new_image'] = $this->path_small_s.$img['file_name'];
			break;
			case 't': 
				$conf_img['width'] = UploadFile::$width_big;
				$conf_img['height']	= UploadFile::$width_big;
				$conf_img['new_image'] = UploadFile::pathTemp().$img['file_name'];
			break;
		}
		return $conf_img;
	}
	
	
	private function configFile($file){
		$config['upload_path'] = $this->getPath($file['resize']);
		$config['allowed_types'] = $this->getTypes($file['format']);
		$config['max_size']	= ($file['size']>0)? $file['size']: 0;
		$dimensions = $this->getDimension($file['dimensions']);
		$config['max_width']  = ($dimensions['width']>0)? $dimensions['width']: 0;
		$config['max_height']  = ($dimensions['height']>0)? $dimensions['height']: 0;
		$dimensions = $this->getDimension($file['min_dimensions']);
		$config['min_width']  = ($dimensions['width']>0)? $dimensions['width']: 0;
		$config['min_height']  = ($dimensions['height']>0)? $dimensions['height']: 0;
		$config['encrypt_name'] = true;
		return $config;
	}
	
	private function getPath($resize){
		if(array_search('b', $resize)!==false)
			return $this->path_big;
		if(array_search('m', $resize)!==false)
			return $this->path_medium;
		if(array_search('s', $resize)!==false)
			return $this->path_small;
		if(array_search('t', $resize)!==false)
			return UploadFile::pathTemp();
	}
	
	public function getTypes($format){
		$types = '';
		foreach($format as $item){
			$types .= $item.'|';
		}
		return substr($types, 0, strlen($types)-1);
	}
	
	public function getFormat($format){
		return substr($format, -3);
	}
	
	private function getDimension($dimension){
		$dim = explode('x', $dimension);
		return array(
			'width' => $dim[0],
			'height' => $dim[1]
		);
	}
}
?>