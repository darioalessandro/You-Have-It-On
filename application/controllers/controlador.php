<?php
class Controlador extends CI_Controller{
	function prueba(){
			$this->load->database();
			$this->load->model("tables/User_model", "user");
			
			$this->user->campos(array(
				"name" => "juan",
				"email" => "juan@hotmail.com",
				"password" => "11j2j2h2kj"
			));
			
			$this->user->insert();
	}
}