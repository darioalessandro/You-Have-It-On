<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exception extends Exception{
	/**
	 * Crea una exepción con un mensaje y un código. Se puede especificar un mensaje opcional que puede o no reemplazar
	 * al mensaje principal.
	 * @param string $message Mensaje a mostrar (este solo debe ser el code_name de un mensaje a ser traducido)
	 * @param int $code codigo que identifica la excepción
	 * @param string $optionalMessage Mensaje opcional. Si $replaceMessage es true, $optionalMessage reemplazará a $message.
	 * 			de lo contrario, se agregará al final de $message.
	 * @param boolean $replaceMessage
	 */
	function MY_Exception($message, $code, $optionalMessage='', $replaceMessage = false){
		parent::__construct();
		
		$ci =& get_instance();
		$ci->load->helper("language");
		
		$this->message = 
			$replaceMessage? 
				$optionalMessage : 
				(lang($message).(
					($optionalMessage!=null and strlen(trim($optionalMessage))>0)?
						(". ".$optionalMessage) : ""
				)
			);
			
		$this->code = $code;
	}
	
	
	function toArray(){
		return array("status" => 
			array(
				"code" => $this->getCode(),
				"message" => $this->getMessage()
			)
		);
	}
	
	
	function __toString(){
		return json_encode($this->toArray());
	}
}
?>