<?php defined('BASEPATH') OR exit('No direct script access allowed');

class goo{
	public $url_goo = "https://www.googleapis.com/urlshortener/v1";
	public $key_goo = "AIzaSyDKkv62RB1BWjsvMVfB5MTr3AeSwmvRM8A";
	
	function goo() {
    }
    
    function shorten($url) {
        $ch = curl_init($this->url_goo."/url?key=".$this->key_goo);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('longUrl' => $url)));
       
        $rpta = curl_exec($ch);
        $data = json_decode($rpta, true);
        curl_close($ch);
        
        if(isset($data["id"]))
        	return $data["id"];
        else
        	return '';
    }
    
    function expand($url) {
        $ch = curl_init($this->url_goo."/url?shortUrl=".$url."&key=".$this->key_goo);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       
        $rpta = curl_exec($ch);
        $data = json_decode($rpta, true);
        curl_close($ch);
       
        return $data["longUrl"];
    }
}