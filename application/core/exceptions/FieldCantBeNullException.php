<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class FieldCantBeNullException extends MY_Exception{

	function __construct($tableName, $fieldName){
		parent::__construct("esp_field_cant_be_null", 16, "Campo <$tableName.$fieldName>");
	}
}
?>