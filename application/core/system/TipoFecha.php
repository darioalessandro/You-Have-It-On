<?php
/*
 * Clase abstracta para definir los tipos con los que se puede inicializar la clase cFecha
 */
abstract class TipoFecha{
	/**
	 * @var YYYY-MM-DDTHH:MM:SS  |  YY-MM-DD HH:MM:SS  |  YYYYMMDDHHMMSS  |  YYMMDDHHMMSS
	 */
	const DateTime = 0;
	/**
	 * @var YYYY-MM-DD  |  YY-MM-DD  |  YYYYMMDD  |  YYMMDD
	 */
	const Date = 1;
	/**
	 * @var Segundos desde el 01-01-1970
	 */
	const TimeStamp = 2;
	/**
	 * @var Fecha y Hora actuales
	 */
	const Now = 3;
	/**
	 * @var  HH:MM:SS  |  HHMMSS  |  HH:MM  |  MMSS  |  SS
	 */
	const Time = 4;
	/**
	 * @var YYYY  |  YY
	 */
	const Year = 5;
}
?>