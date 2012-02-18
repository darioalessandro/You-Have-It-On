<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Angel López
 * Clase desarrollada para el manejo basico de fechas entre php y mysql y otras bases de datos.
 */
class Fecha{
	/*
	 * Propiedades
	 */

	private $dia = 0;
	private $mes = 0;
	private $anio = 0;
	private $hora = 0;
	private $minuto = 0;
	private $segundo = 0;
	private $amPm = null;
	private $timeStamp = 0;
	private $idioma = Idiomas::espaniol;
	private $diaSemana = null;
	private $diaAnio = null;
	protected $nombresFechas = array(
		"dias" => array(
			Idiomas::espaniol => array(
				"cortos"=> array("Lun","Mar","Mie","Jue","Vie","Sab","Dom"),
				"largos"=> array("Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo")
			),
			Idiomas::frances => array(
				"cortos"=> array("Lun","Mar","Mer","Jeu","Ven","Sam","Dim"),
				"largos"=> array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche")
			),
			Idiomas::ingles => array(
				"cortos"=> array("Mon","Tue","Wed","Thu","Fri","Sat","Sun"),
				"largos"=> array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday")
			)
		),
		"meses" => array(
			Idiomas::espaniol => array(
					"cortos"=> array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"),
					"largos"=> array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre")
			),
			Idiomas::frances => array(
					"cortos"=> array("Jan","Fév","Mar","Avr","Mai","Jui","Jui","Aoú»","Sep","Oct","Nov","Déc"),
					"largos"=> array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aoút","Septembre","Octobre","Novembre","Décembre")
			),
			Idiomas::ingles => array(
					"cortos"=> array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"),
					"largos"=> array("January","February","March","April","May","June","July","August","September","October","November","December")
			)
		)
	);
	

	public function getDia(){
		return $this->dia;
	}


	public function setDia($dia){
		$this->dia = $dia;
	}

	public function getMes(){
		return $this->mes;
	}


	public function setMes($mes){
		$this->mes = $mes;
	}


	public function getAnio(){
		return $this->anio;
	}


	public function setAnio($anio){
		$this->anio = $anio;
	}


	/**
	 * @return hora del dia. (0 - 23)
	 */
	public function getHora(){
		return $this->hora;
	}


	public function setHora($hora){
		$this->hora = $hora;
	}


	public function getMinuto(){
		return $this->minuto;
	}


	public function setMinuto($minuto){
		$this->minuto = $minuto;
	}


	public function getSegundo(){
		return $this->segundo;
	}


	public function setSegundo($segundo){
		$this->segundo = $segundo;
	}


	public function getAmPm(){
		return $this->amPm;
	}


	public function setAmPm($amPm){
		$this->amPm = $amPm;
	}


	public function getTimeStamp(){
		return $this->timeStamp;
	}


	public function setTimeStamp($timeStamp){
		$this->timeStamp = $timeStamp;
	}


	public function getIdioma(){
		return $this->idioma;
	}


	public function setIdioma($idioma){
		$this->idioma = $idioma;
	}


	/**
	 * Regresa el numero del dia de la semana (lunes -> 1 ... domingo -> 7)
	 * @return Numero del dia de la semana
	 */
	public function getDiaSemana(){
		return $this->diaSemana;
	}


	public function setDiaSemana($diaSemana){
		$this->diaSemana = $diaSemana;
	}


	/**
	 * Regresa el numero del dia del año (0 - 365)
	 * @return Numero del dia del aÃ±o
	 */
	public function getDiaAnio(){
		return $this->diaAnio;
	}


	public function setDiaAnio($diaAnio){
		$this->diaAnio = $diaAnio;
	}


	/*
	 * Contructor
	 */

	public function Fecha($tiempo = null, $tipoFecha = TipoFecha::Now){
		switch ($tipoFecha){
			case TipoFecha::Now:
				$this->desglozarFecha(strtotime("now"));
				break;
			case TipoFecha::DateTime:
				if (isset($tiempo{0}) and strlen($tiempo) < 20){
					if (strlen($tiempo) == 19){
						//YYYY-MM-DDTHH:MM:SS
						$this->desglozarFecha(mktime(substr($tiempo,11,2),substr($tiempo,14,2),substr($tiempo,17,2),substr($tiempo,5,2),substr($tiempo,8,2),substr($tiempo,0,4)));
					}elseif (strlen($tiempo) == 17){
						//YY-MM-DD HH:MM:SS
						$this->desglozarFecha(mktime(substr($tiempo,9,2),substr($tiempo,12,2),substr($tiempo,15,2),substr($tiempo,3,2),substr($tiempo,6,2),substr($tiempo,0,2)));
					}elseif (strlen($tiempo) == 14){
						//YYYYMMDDHHMMSS
						$this->desglozarFecha(mktime(substr($tiempo,8,2),substr($tiempo,10,2),substr($tiempo,12,2),substr($tiempo,4,2),substr($tiempo,6,2),substr($tiempo,0,4)));
					}elseif (strlen($tiempo) == 12){
						//YYMMDDHHMMSS
						$this->desglozarFecha(mktime(substr($tiempo,6,2),substr($tiempo,8,2),substr($tiempo,10,2),substr($tiempo,2,2),substr($tiempo,4,2),substr($tiempo,0,2)));
					}else{
						throw new DateDatetimeInvaldException($tiempo);
					}
				}else{
					throw new DateDatetimeInvaldException($tiempo);
				}

				break;
			case TipoFecha::Date:
				if (isset($tiempo{0}) and strlen($tiempo) < 11){
					if (strlen($tiempo) == 10){
						//YYYY-MM-DD
						$this->desglozarFecha(mktime(0,0,0,substr($tiempo,5,2),substr($tiempo,8,2),substr($tiempo,0,4)));
					}elseif (strlen($tiempo) == 8 and !is_numeric(substr($tiempo,2,1))){
						//YY-MM-DD
						$this->desglozarFecha(mktime(0,0,0,substr($tiempo,3,2),substr($tiempo,6,2),substr($tiempo,0,2)));
					}elseif (strlen($tiempo) == 8 and is_numeric(substr($tiempo,2,1))){
						//YYYYMMDD
						$this->desglozarFecha(mktime(0,0,0,substr($tiempo,4,2),substr($tiempo,6,2),substr($tiempo,0,4)));
					}elseif (strlen($tiempo) == 6){
						//YYMMDD
						$this->desglozarFecha(mktime(0,0,0,substr($tiempo,2,2),substr($tiempo,4,2),substr($tiempo,0,2)));
					}else{
						throw new DateDateInvaldException($tiempo);
					}
				}else{
					throw new DateDateInvaldException($tiempo);
				}

				break;
			case TipoFecha::TimeStamp:
				if ($tiempo > -1){
					$this->desglozarFecha($tiempo);
				}else{
					throw new DateDateInvaldException($tiempo);
				}

				break;
			case TipoFecha::Time:
				if (isset($tiempo{0}) and strlen($tiempo) < 9){
					if (isset($tiempo{0}) and strlen($tiempo) == 8){
						//HH:MM:SS
						$this->desglozarFecha(mktime(substr($tiempo,0,2),substr($tiempo,3,2),substr($tiempo,6,2)));
					}elseif (isset($tiempo{0}) and strlen($tiempo) == 6){
						//HHMMSS
						$this->desglozarFecha(mktime(substr($tiempo,0,2),substr($tiempo,2,2),substr($tiempo,4,2)));
					}elseif (isset($tiempo{0}) and strlen($tiempo) == 5){
						//HH:MM
						$this->desglozarFecha(mktime(substr($tiempo,0,2),substr($tiempo,3,2),0));
					}elseif (isset($tiempo{0}) and strlen($tiempo) == 4){
						//MMSS
						$this->desglozarFecha(mktime(0,substr($tiempo,0,2),substr($tiempo,2,2)));
					}elseif (isset($tiempo{0}) and strlen($tiempo) == 2){
						//SS
						$this->desglozarFecha(mktime(0,0,$tiempo));
					}else{
						throw new DateTimeInvaldException($tiempo);
					}
				}else{
					throw new DateTimeInvaldException($tiempo);
				}

				break;
			case TipoFecha::Year:
				if (isset($tiempo{0}) and strlen($tiempo) < 5){
					$this->desglozarFecha(mktime(0,0,0,0,0,$tiempo));
				}else{
					throw new DateYearInvaldException($tiempo);
				}

				break;
		}

		if (!$this->esValida()){
			throw new DateInvaldException($tiempo);
		}
	}


	public function desglozarFecha($timeStamp){
		try{
			//obtenemos los datos de este dateStamp
			$datos = getdate($timeStamp);

			$this->setDia($datos["mday"]);
			$this->setMes($datos["mon"]);
			$this->setAnio($datos["year"]);
			$this->setHora($datos["hours"]);
			$this->setMinuto($datos["minutes"]);
			$this->setSegundo($datos["seconds"]);
			$this->setDiaSemana(date("N", $timeStamp));
			$this->setDiaAnio($datos["yday"]);
			$this->setTimeStamp($timeStamp);
			$this->setAmPm((($this->getHora() < 12)?'am':'pm'));
		}catch(Exception $e){
			throw new DateException($e->getMessage());
		}
	}


	public function esValida(){
		$valida = false;
		if (checkdate($this->getMes(), $this->getDia(), $this->getAnio())){
			$valida = true;
		}

		return $valida;
	}


	public function __toString(){
		return $this->toFechaFormateada(
		FormatoFecha::DiaCorto.", ".FormatoFecha::DiaNumero." de ".FormatoFecha::MesCorto.
	 		" del ".FormatoFecha::AnioCorto.". ".FormatoFecha::Hora12.":".FormatoFecha::Minuto.
	 		":".FormatoFecha::Segundo." ".FormatoFecha::AmPmMinusculas
		);
	}



	/**
	 * Regresa la fecha formateada como DateTime para sql
	 * @return YYYY-MM-DDTHH:MM:SS
	 */
	public function toSqlDateTime(){
		return $this->getAnio()."-".Sys::dosDigitos($this->getMes())."-".Sys::dosDigitos($this->getDia())."T".Sys::dosDigitos($this->getHora()).":".Sys::dosDigitos($this->getMinuto()).":".Sys::dosDigitos($this->getSegundo());
	}


	/**
	 * Regresa la fecha formateada como TimeStamp para sql
	 * Función identica a toSqlDateTime()
	 * @return YYYY-MM-DDTHH:MM:SS
	 */
	public function toSqlTimeStamp(){
		return $this->toSqlDateTime();
	}


	/**
	 * Regresa la fecha formateada como Date para sql
	 * @return YYYY-MM-DD
	 */
	public function toSqlDate(){
		return $this->getAnio()."-".Sys::dosDigitos($this->getMes())."-".Sys::dosDigitos($this->getDia());
	}


	/**
	 * Regresa la fecha formateada como Time para sql
	 * @return HH:MM:SS
	 */
	public function toSqlTime(){
		return Sys::dosDigitos($this->getHora()).":".Sys::dosDigitos($this->getMinuto()).":".Sys::dosDigitos($this->getSegundo());
	}


	/**
	 *Regresa la fecha formateada con las especificaciones enviadas
	 *Ejemplo:
	 *@code
	 *	toFechaFormateada(
	 *		FormatoFecha::DiaLargo.", ".FormatoFecha::DiaNumero." de ".FormatoFecha::MesLargo.
	 *		" del ".FormatoFecha::AnioCorto.". ".FormatoFecha::Hora12.":".FormatoFecha::Minuto.
	 *		":".FormatoFecha::Segundo." ".FormatoFecha::AmPmMayusculas
	 *	)
	 *	//Regresa: Lunes 06 de Julio del 09. 02:12:00 PM
	 *@endcode
	 */
	public function toFechaFormateada($formato){
		$patrones = array("ffDC","ffDL","ffDN","ffMC","ffML","ffMN","ffAC","ffAL","ffHH","ff12","ffMM","ffSS","ffPm","ffPM");
		$reemplazo = array(
			$this->getFormatoDiaCorto(),
			$this->getFormatoDiaLargo(),
			Sys::dosDigitos($this->getDia()),
			$this->getFormatoMesCorto(),
			$this->getFormatoMesLargo(),
			Sys::dosDigitos($this->getMes()),
			substr($this->getAnio(),2,2),
			$this->getAnio(),
			Sys::dosDigitos($this->getHora()),
			$this->toHora12($this->getHora()),
			Sys::dosDigitos($this->getMinuto()),
			Sys::dosDigitos($this->getSegundo()),
			$this->getAmPm(),
			strtoupper($this->getAmPm())
		);

		return str_replace($patrones, $reemplazo, $formato);
	}


	public function getFormatoDiaCorto(){
		return $this->nombresFechas["dias"][$this->getIdioma()]["cortos"][$this->getDiaSemana() - 1];
	}


	public function getFormatoDiaLargo(){
		return $this->nombresFechas["dias"][$this->getIdioma()]["largos"][$this->getDiaSemana() - 1];
	}


	public function getFormatoMesCorto(){
		return $this->nombresFechas["meses"][$this->getIdioma()]["cortos"][$this->getMes() - 1];
	}


	public function getFormatoMesLargo(){
		return $this->nombresFechas["meses"][$this->getIdioma()]["largos"][$this->getMes() - 1];
	}


	public function agregarDias($numeroDias, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo(),$this->getMes(),$this->getDia() + $numeroDias,$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia() + $numeroDias,$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function agregarMeses($numeroMeses, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo(),$this->getMes() + $numeroMeses,$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes() + $numeroMeses,$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function agregarAnios($numeroAnios, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo(),$this->getMes(),$this->getDia(),$this->getAnio() + $numeroAnios));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio() + $numeroAnios));
			return $nuevaFecha;
		}
	}


	public function agregarHoras($numeroHoras, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora() + $numeroHoras,$this->getMinuto(),$this->getSegundo(),$this->getMes(),$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora() + $numeroHoras,$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function agregarMinutos($numeroMinutos, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto() + $numeroMinutos,$this->getSegundo(),$this->getMes(),$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto() + $numeroMinutos,$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function agregarSegundos($numeroSegundos, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo() + $numeroSegundos,$this->getMes(),$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo() + $numeroSegundos,$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function restarDias($numeroDias, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo(),$this->getMes(),$this->getDia() - $numeroDias,$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia() - $numeroDias,$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function restarMeses($numeroMeses, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo(),$this->getMes() - $numeroMeses,$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes() - $numeroMeses,$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function restarAnios($numeroAnios, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo(),$this->getMes(),$this->getDia(),$this->getAnio() - $numeroAnios));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio() - $numeroAnios));
			return $nuevaFecha;
		}
	}


	public function restarHoras($numeroHoras, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora() - $numeroHoras,$this->getMinuto(),$this->getSegundo(),$this->getMes(),$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora() - $numeroHoras,$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function restarMinutos($numeroMinutos, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto() - $numeroMinutos,$this->getSegundo(),$this->getMes(),$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto() - $numeroMinutos,$nuevaFecha->getSegundo(),$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public function restarSegundos($numeroSegundos, $sobreescribir = true){
		if ($sobreescribir){
			$this->desglozarFecha(mktime($this->getHora(),$this->getMinuto(),$this->getSegundo() - $numeroSegundos,$this->getMes(),$this->getDia(),$this->getAnio()));
		}else{
			$nuevaFecha = clone $this;
			$nuevaFecha->desglozarFecha(mktime($nuevaFecha->getHora(),$nuevaFecha->getMinuto(),$nuevaFecha->getSegundo() - $numeroSegundos,$nuevaFecha->getMes(),$nuevaFecha->getDia(),$nuevaFecha->getAnio()));
			return $nuevaFecha;
		}
	}


	public static function FechaToFechaHora($fecha){
		if ((strlen($fecha) == 10) or (strlen($fecha) == 8 and !is_numeric(substr($fecha,2,1)))){
			//YYYY-MM-DD
			$fecha .= "T00:00:00";
		}elseif ((strlen($fecha) == 8 and is_numeric(substr($fecha,2,1))) or (strlen($fecha) == 6)){
			//YYYYMMDD
			$fecha .= "000000";
		}else{
			throw new DateDateInvaldException($fecha);
		}

		return $fecha;
	}


	public function toHora12($hora){
		if ($this->getAmPm() == "pm" and $hora != 12){
			$hora -= 12;
		}elseif ($this->getAmPm() == "am" and $hora == 0){
			$hora = 12;
		}

		return Sys::dosDigitos($hora);
	}
}
?>