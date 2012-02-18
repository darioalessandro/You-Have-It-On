<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class FieldNotValidException extends MY_Exception{

	function __construct($tableName, $fieldName, $validacion = "", $valorValidacion = ""){
		parent::__construct("exp_field_not_valid", 17, "Campo <$tableName.$fieldName> Validación: <$validacion = $valorValidacion>");
	}
}
?>