<?php
abstract class EstadoTabla{
	/*
	 * La clase cTabla se acaba de crear y no tiene datos
	 */
	const nuevo = 0;
	/*
	 * se acaba de actualizar la tabla con los datos de la BD
	 */
	const actualizado = 1;
	/*
	 * los datos que se habian traido de la BD se han modificado
	 */
	const modificado = 2;
}
?>