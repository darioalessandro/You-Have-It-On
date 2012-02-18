<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Lenguaje por default
 * 
 * Este paramatro de configuracion indica que lenguaje sera por default,
 * si al hacer referencia a los servicios no se espesifica un lenguaje
 * establese este. 
 * @author gama
 */
$config['lang'] = Idiomas::espaniol;

/**
 * Formato de salida por default
 * 
 * El parametro indica cual es el formato de salida de los datos por default
 * @author gama
 */
$config['format'] = OutputFormat::json;
?>
