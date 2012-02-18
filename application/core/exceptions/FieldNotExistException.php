<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class FieldNotExistException extends MY_Exception{

	function __construct($tableName, $fieldName){
		parent::__construct("exp_field_not_found", 6, "Campo <$tableName.$fieldName>");
	}
}
?>