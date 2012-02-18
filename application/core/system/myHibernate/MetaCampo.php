<?php
class MetaCampo{
	const DEFAULT_NULL = "NULL";
	const DEFAULT_CURRENT_TIMESTAMP = "CURRENT_TIMESTAMP";
	private $nombre = null;
	private $valor = null;
	private $tipo = TipoDato::Cadena;
	private $default = null;
	private $maxLargo = null;
	private $esLlavePrimaria = false;
	private $esLlaveForanea = false;
	private $esAutoIncrementable = false;
	private $esUnico = false;
	private $usaDefault = false;
	private $esNullable = false;
	public $validaciones = null;


	public function getNombre(){
		return $this->nombre;
	}


	public function setNombre($nombre){
		$this->nombre = $nombre;
	}


	public function getValor(){
		return $this->valor;
	}
	
	
	public function setValor($valor){
		if ($valor != null){
			if (($this->getTipo() == TipoDato::Cadena or $this->getTipo() == TipoDato::Numero) and $this->getMaxLargo() != null and strlen($valor) > $this->getMaxLargo()){
				$valor = substr($valor, 0, $this->getMaxLargo());
			}
			
			if (!is_a($valor, "Fecha")){
				switch ($this->getTipo()){
					case TipoDato::Anio:
						$valor = new Fecha($valor, TipoFecha::Year);
						break;
					case TipoDato::Fecha:
						$valor = new Fecha($valor, TipoFecha::Date);
						break;
					case TipoDato::FechaHora:
						$valor = new Fecha($valor, TipoFecha::DateTime);
						break;
					case TipoDato::Hora:
						$valor = new Fecha($valor, TipoFecha::Time);
						break;
				}
			}
		}
		
		$this->valor = $valor;
	}


	public function getTipo(){
		return $this->tipo;
	}


	public function setTipo($tipo){
		$this->tipo = $tipo;
	}
	
	
	public function getMaxLargo(){
		return $this->maxLargo;
	}


	public function setMaxLargo($maxLargo){
		$this->maxLargo = $maxLargo;
		
		//actualizamos el valor del campo dependiendo del maximo largo permitido
		if ($this->getValor() != null){
			$this->setValor($this->getValor());
		}
	}
	
	
	public function getUsaDefault(){
		return $this->usaDefault;
	}


	public function setUsaDefault($usaDefault){
		$this->usaDefault = $usaDefault;
	}

	
	public function getDefault(){
		return $this->default;
	}


	public function setDefault($default){
		$this->setUsaDefault(true);
		$this->default = $default;
	}


	public function esLlavePrimaria(){
		return $this->esLlavePrimaria;
	}


	public function setEsLlavePrimaria($esLlavePrimaria){
		$this->esLlavePrimaria = $esLlavePrimaria;
	}


	public function esLlaveForanea(){
		return $this->esLlaveForanea;
	}


	public function setEsLlaveForanea($esLlaveForanea){
		$this->esLlaveForanea = $esLlaveForanea;
	}
	
	
	public function esAutoIncrementable(){
		return $this->esAutoIncrementable;
	}


	public function setEsAutoIncrementable($esAutoIncrementable){
		$this->esAutoIncrementable = $esAutoIncrementable;
	}
	
	
	public function esUnico(){
		return $this->esUnico;
	}


	public function setEsUnico($esUnico){
		$this->esUnico = $esUnico;
	}
	
	
	public function esNullable(){
		return $this->esNullable;
	}


	public function setEsNullable($esNullable){
		$this->esNullable = $esNullable;
	}

	
	public function MetaCampo($nombre = null, $valor = null){
		$this->setNombre($nombre);
		$this->setValor($valor);
	}
}
?>