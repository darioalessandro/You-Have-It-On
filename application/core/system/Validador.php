<?php
class Validador{
	/**
	 * Verifica si un texto dado, es correo o no
	 * @return boolean.
	 */
	public static function esCorreo($correo = ""){
		if (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$", trim($correo))){
	       return true;
	    }else{
	       return false;
	    } 
	}
	
	
	/**
	 * Verifica si una cadena esta vacia o no. Una serie de espacios en blanco se considera como cadena vacia
	 * @return boolean
	 */
	public static function estaVacio($q = ""){
		for ($i = 0; $i < strlen($q); $i++){
			if ($q{$i} != " "){ 
				return false;
			}
		}
		return true;
	}
}
?>