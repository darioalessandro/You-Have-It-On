<?php
/*
 * Clase abstracta para definir los tipos de formatos de salida formateada de fechas
 */
abstract class FormatoFecha{
	const DiaCorto = "ffDC"; //Lun
	const DiaLargo = "ffDL"; //Lunes
	const DiaNumero = "ffDN"; //02
	const MesCorto = "ffMC"; //Mar
	const MesLargo = "ffML"; //Marzo
	const MesNumero = "ffMN"; //03
	const AnioCorto = "ffAC"; //09
	const AnioLargo = "ffAL"; //2009
	const Hora = "ffHH"; //14
	const Hora12 = "ff12"; //02
	const Minuto = "ffMM"; //28
	const Segundo = "ffSS"; //13
	const AmPmMinusculas = "ffPm"; //pm
	const AmPmMayusculas = "ffPM"; //AM
}
?>