<?php
abstract class UploadFile{
	public static $path_small = 'files/s/n/';
	public static $path_small_square = 'files/s/s/';
	public static $path_medium = 'files/m/';
	public static $path_big = 'files/b/';
	public static $path_temp = 'files/temp/';
	
	public static $width_small = 50;
	public static $width_medium = 180;
	public static $width_big = 700;
	
	public static function pathSmall(){
		return APPPATH.self::$path_small;
	}
	
	public static function pathSmallSquare(){
		return APPPATH.self::$path_small_square;
	}
	
	public static function pathMedium(){
		return APPPATH.self::$path_medium;
	}
	
	public static function pathBig(){
		return APPPATH.self::$path_big;
	}
	
	public static function pathTemp(){
		return APPPATH.self::$path_temp;
	}
	
	
	public static function urlSmall(){
		return Sys::$url_base.'application/'.self::$path_small;
	}
	
	public static function urlSmallSquare(){
		return Sys::$url_base.'application/'.self::$path_small_square;
	}
	
	public static function urlMedium(){
		return Sys::$url_base.'application/'.self::$path_medium;
	}
	
	public static function urlBig(){
		return Sys::$url_base.'application/'.self::$path_big;
	}
	
	
	public static function getImgType($mime){
		$exten = '';
		switch($mime){
			case 'image/jpeg': $exten = 'jpg'; break;
			case 'image/png': $exten = 'png'; break;
			case 'image/x-png': $exten = 'png'; break;
			case 'image/gif': $exten = 'gif'; break;
		}
		return $exten;
	}
}
?>