<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Helper Lang
 *
 * Permite simplificar el acceso al sistema de traduccion.
 * Recibe un conjunto de parametros, en donde el parametro 0 es el code_name del sistema de tadruccion,
 * los demas parametros son remplazados en el string. 
 *
 * @access	public
 * @param 0 -> es el code_name del sistema de traduccion
 * @return 1..n -> parametos a remplazar en orden.
 * @author gama
 */	
if(!function_exists('lang')){
	function lang(){
		$retorno = '';
		$num_args = func_num_args();
		if($num_args > 0){
			$nombre = func_get_arg(0);
			
			$CI =& get_instance();
			$texto = $CI->lang->line($nombre);
			
			if($texto!==''){
				$vals = func_get_args();
				
				$patrones = array();
				$valores = array();
				
				//Generamos los arrays para la busqueda y el remplazo
				for($a = 1; $a < $num_args; $a++){
					$valores[$a-1] = $vals[$a];
					$patrones[$a-1] = "{".($a-1)."}";
				}
				
				$retorno = str_replace($patrones, $valores, $texto);
			}
		}
		return $retorno;
	}
}
