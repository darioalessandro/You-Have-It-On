<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase con el proposito de facilitar los procesos realizados con la base de datos, como Insert, Update, Delete, Select, etc.
 * @author Angel López
 *
 */
abstract class Table_model extends CI_Model{
	/*
	 * Caampo que forza al sistema a jalar la estructura de cada tabla siempre, sin utilizar el caché de sesión
	 */
	private $actualizarTablas = true;
	protected $campos = array();
	/*
	 * Array que usaremos como buffer para lograr hacer transacciones logicas
	 */
	protected $camposBuff = array();
	/*
	 * Array con los nombres (String) de los campos que son llaves. 
	 */
	protected $camposId = array();
	private $nombreTabla = null;
	private $estado = EstadoTabla::nuevo;
	/*
	 * Contiene el ultimo error lanzado por esta tabla. <cMsj>
	 */
	public $error = null;
	/*
	 * Bandera para verificar si se encontraron datos o no
	 */
	protected $hayDatos = false;
	/*
	 * Ultimo Id insertado
	 */
	protected $ultimoIdInsertado = 0;
	
	
	/**
	 * Función que al ser implementada, regresará el nombre de la tabla que se está cargando en memoria
	 */
	abstract protected function getTableName();
	
	/**
	 * Función que configira los campos de la tabla con los detalles específicos que no se pueden obtener automaticamente
	 * Configurar los campos que tengan las siguientes propiedades:
	 * 	-esLLaveForanea = true
	 * 	-esAutoIncrementable = true
	 * 	-esUnico = true
	 * 	-defaultValue
	 * 	-esNullable = true
	 */
	abstract protected function configCampos();
	
	
	public function getUltimoIdInsertado(){
		return $this->ultimoIdInsertado;
	}
	
	
	public function getNombreTabla(){
		return $this->nombreTabla;
	}
	
	
	protected function setNombreTabla($nombreTabla){
		$this->nombreTabla = $nombreTabla;
	}
	
	
	public function getEstado(){
		return $this->estado;
	}
	
	
	protected function setEstado($estado){
		$this->estado = $estado;
	}
	
	
	/**
	 * Si se sobreescribe el __construct() en una subclase, es necesario mandar llamar parent::__construct(); primero
	 */
	public function	__construct(){
		parent::__construct();
		$this->setNombreTabla($this->getTableName());
		
		if (isset($_SESSION[$this->getNombreTabla()]) 
				and $_SESSION[$this->getNombreTabla()] != null 
				and !$this->actualizarTablas){
			$objTemp = unserialize($_SESSION[$this->getNombreTabla()]);
			/*
			 * copiamos la estructura de la clase guardada en sesion
			 * aqui podemos copiar demás variables que sean necesarias futuramente
			 */
			$this->campos = $objTemp->campos;
			$this->camposId = $objTemp->camposId;
			
			unset($objTemp);
		}else{
			//obtenemos estructura desde la BD
			$this->obtenerEstructura();
			
			$_SESSION[$this->getNombreTabla()] = serialize($this);
		}
		
		//configuramos los campos
		$this->configCampos();
	}
	
	
	/**
	 * @param $obtenerDatos. boolean. Indica si la clase al crearse debe obtener los datos de la BD o no.
	 * @param $valoresLlave. array/string. Valores a introducir en los campos llaves de esta tabla para poder
	 * facilmente meter y sacar datos de la BD.
	 * @example
	 * @code
	 *	//crea un cliente indefinido (es decir, sin datos que lo identifiquen)
	 * 	$this->load->model("Cliente_model");
	 * 	
	 * 	//crea un registro Oficios con datos de inicialización
	 * 	//Nota: aún no existe en la base de datos hasta que se guarde con save()
	 * 	$this->load->model("Oficios_model");
	 * 	$this->Oficios_model->init(array(
	 * 		"oficio"=>"programador", 
	 * 		"departamento"=>14
	 * 	));
	 * 
	 * 	//crea el cliente con la unica llave primaria igual a 148 y saca los datos de la BD
	 * 	$this->load->model("Cliente_model", "cliente");
	 *  $this->cliente->init(148, true);
	 * @endcode
	 */
	public function init($valoresLlave = null, $obtenerDatos = false){
		if ($valoresLlave){
			if (is_array($valoresLlave)){
				//hay varias llaves
				foreach($valoresLlave as $llave => $valor){
					$this->campo($llave, $valor);
				}
			}else{
				//solo hay una llave
				if (count($this->camposId) > 0){
					$this->campo($this->camposId[0], $valoresLlave);
				}
			}
		}
		
		if ($obtenerDatos){
			$this->select();
		}
	}
	
	
	protected function obtenerEstructura(){
		$fields = $this->db->field_data($this->getNombreTabla());

		foreach ($fields as $field){
			$this->campos[$field->name] = new MetaCampo($field->name);
			$this->campos[$field->name]->setTipo($field->type);
			$this->campos[$field->name]->setMaxLargo($field->max_length);
			
			if ($field->primary_key){
				$this->campos[$field->name]->setEsLlavePrimaria($field->primary_key);
				$this->camposId[] = $field->name;
			}
		}
	}
	
	
	/**
	 *Obtiene/asigna un valor a un campo de la tabla
	 *@return <Table_model/mixed> Si se esta obtieniendo un campo, regresa el valor. De lo contrario regresa una referencia a este objeto
	 *@example
	 *@code
	 *	$cliente->campo("nombre", "juan");
	 *	$nombre = $cliente->campo("nombre");
	 *@endcode
	 */
	public function campo($nombre, $valor = "_getCampo_"){
		if (array_key_exists($nombre, $this->campos)){
			if ($valor === "_getCampo_"){
				return $this->campos[$nombre]->getValor();
			}else{
				$this->campos[$nombre]->setValor($valor);
				$this->setEstado(EstadoTabla::modificado);
				
				return $this;
			}
		}else{
			throw new FieldNotExistException($this->getNombreTabla(), $nombre);
		}
	}
	
	
	/**
	 *Obtiene un objeto MetaCampo de la tabla
	 *@example
	 *@code
	 *	$tipoCampo = $cliente->metaCampo("nombre")->getTipo();
	 *@endcode
	 */
	public function metaCampo($nombre){
		if (array_key_exists($nombre, $this->campos)){
			return $this->campos[$nombre];
		}else{
			throw new FieldNotExistException($this->getNombreTabla(), $nombre);
		}
	}
	
	
	/**
	 * Obtiene/asigna algunos/todos los campos en un array asociativo
	 * Puede recibir un array especificando los campos que desea regresar/ingresar
	 * 	NOTA: si el array es asociativo, significa que se estan ingresando datos
	 * 		  si el array es de indice numerico, significa que se estan obteniendo los datos
	 * @param $nombreCampos. array de nombres de los campos que se desea obtener
	 *			Si $nombreCampos es un array asociativo key=>value, significa que se están insertando datos en los campos.
	 * @return <Table_model/mixed> Si se esta obtieniendo una serie de campos, regresa los valores en un array. 
	 * 		De lo contrario regresa una referencia a este objeto
	 * @example
	 * @code
	 * 	//Obtiene campos
	 * 	list($nombre, $edad, $sexo) = $cliente->campos(array("nombre", "edad", "sexo"));
	 * 	$todosLosCampos = $cliente->campos();			
	 * 	$nombre = $todosLosCampos["nombre"];
	 * 
	 * 	//inserta valores en los campos especificados
	 * 	$cliente->campos(array("nombre"=>"juan", "sexo"=>"H", "edad"=>23, "fecha"=>"1988-06-17T14:33:20"));
	 * @endcode  
	 */
	public function campos($nombreCampos = null){
		if (is_array($nombreCampos)){
			if (array_key_exists(0, $nombreCampos)){
				//array numerico, se estan obteniendo campos
				$camp = array();
				
				foreach($nombreCampos as $campo){
					if (array_key_exists($campo, $this->campos)){
						$camp[$campo] = $this->campos[$campo]->getValor();
					}else{
						throw new FieldNotExistException($this->getNombreTabla(), $campo);
					}
				}

				return $camp;
			}else{
				//array asociativo, se estan insertando datos a los campos
				foreach($nombreCampos as $llave => $valor){
					$this->campo($llave, $valor);
				}
				
				return $this;
			}
		}else{
			//todos los campos
			$camp = array();
			foreach($this->campos as $campo){
				$camp[$campo->getNombre()] = $campo->getValor();
			}
			
			return $camp;
		}
	}
	
	
	/**
	 * Obtiene los campos en un array asociativo
	 * Puede recibir un array especificando los campos que desea regresar
	 * @param $nombreCampos. array de nombres de los campos que se desea obtener
	 * @return <array>
	 * @example
	 * @code
	 * 	//Obtiene un array con todos los campos de la tabla
	 * 	$todosLosMetaCampos = $cliente->metaCampos();	
	 * 	$tipoDatoNombre = $todosLosCampos["nombre"]->getTipo();
	 *
	 * 	//Obtiene un array con todos los campos especidicados
	 * 	$todosLosMetaCampos = $cliente->metaCampos(array("nombre", "edad"));	
	 * 	$tipoDatoNombre = $todosLosCampos["nombre"]->getTipo(); 
	 * 	$tipoDatoEdad = $todosLosCampos["edad"]->getTipo();
	 * @endcode 
	 */
	public function metaCampos($nombreCampos = null){
		//array de objetos MetaCampo
		if ($nombreCampos != null && is_array($nombreCampos)){
			$metaCampos = array();
			
			foreach($nombreCampos as $campo){
				$metaCampos[] = $this->metaCampo($campo);
			}
			
			return $metaCampos;
		}else{
			return $this->campos;
		}
	}
	
	
	/**
	 * Regresa un array con el nombre de todos los campos de la tabla
	 * @return array
	 */
	public function nombreCampos(){
		$nomCampos = array();
		foreach($this->campos as $campo){
			$nomCampos[] = $campo->getNombre();
		}
		
		return $nomCampos;
	}
	
	
	/**
	 * Verifica si la clase contiene datos o no
	 * return Boolean. True si se encontraron datos, de lo contrario regresa False
	 */
	public function hayDatos(){
		return $this->hayDatos;
	}
	
	
	/**
	 *@param $condicion <opcional>. String. condicion Sql para seleccionar los datos
	 *@return $this. <Table_model>
	 *@example
	 *@code
	 *	$clase->select();
	 *	$clase->select("nombre = 'jose'");
	 *@endcode
	 */
	public function select($condicion = null){
		if ($condicion == null){
			//la condicion sql será basada en los campos llave
			$separador = "";
			foreach($this->camposId as $campoId){
				$condicion .= $separador.$this->campos[$campoId]->getNombre()." = ".$this->formatearValorSql($this->campos[$campoId]->getValor(), $this->campos[$campoId]->getTipo());
				$separador = " and ";
			}
		}
		
		//obtenemos datos;
		$datos = $this->db->query("SELECT * FROM ".$this->getNombreTabla()." WHERE ".$condicion);
		$this->hayDatos = false;
		
		//guardamos los datos en sus respectivos campos
		foreach($datos->result_array() as $row){
			$this->hayDatos = true;
			
			foreach ($row as $llave => $valor){
				$this->campo($llave, $valor);
			}
			
			//modificamos estado
			$this->setEstado(EstadoTabla::actualizado);
			break;
		}
		
		//regresamos referencia al mismo objeto
		return $this;
	}
	
	
	/**
	 *@param $nombreSubClaseTabla <subclass of Table_model>. Nombre de la clase de la cual se crearán los objetos
	 *@param $condicion <opcional>. String. condicion Sql para seleccionar los objetos
	 *@param $ordenarPor <opcional>. String. fcriterio de ordenamiento de los resultados
	 *@return array<Table_model>
	 *@example
	 *@code
	 *	$users = User_model::obtenerObjetos("User_model", "nombre LIKE '%angel%'", "nombre ASC");
	 *
	 *	foreach ($users as $user){
	 *		echo "Nombre: ".$user->campo("nombre");
	 *		echo "Edad: ".$user->campo("edad");
	 *	}
	 *@endcode
	 */
	public static function obtenerObjetos($nombreSubClaseTabla = "", $condicion = "1 = 1", $ordenarPor = null){
		//obtenemos datos;
		$objAux = new $nombreSubClaseTabla();
		$ci =& get_instance();
		$datos = $ci->db->query("SELECT * FROM ".$objAux->getNombreTabla()." WHERE ".$condicion.(($ordenarPor!=null)?(" ORDER BY ".$ordenarPor):""));
		
		unset($objAux);
		$objetos = array();
		
		//guardamos los datos en sus respectivos campos
		foreach($datos->result_array() as $row){
			$objetos[$a] = new $nombreSubClaseTabla($row);
		}
		
		//regresamos array de objetos cTabla
		return $objetos;
	}
		
	
	/**
	 *Le agrega comillas a los campos que lo requieran. Si es una fecha, $valor es de tipo Fecha
	 *@param $valor. Fecha/String/Numeric
	 *@param $tipo. TipoDato
	 */
	protected function formatearValorSql($valor = "", $tipo = TipoDato::Cadena){
		switch ($tipo){
			case TipoDato::Cadena:
				$valor = "'".sys::limpiarTexto($valor)."'";
				break;
			case TipoDato::Anio:
				$valor = "'".$valor->getAnio()."'";
				break;
			case TipoDato::Fecha:
				$valor = "'".$valor->toSqlDate()."'";
				break;
			case TipoDato::FechaHora:
				$valor = "'".$valor->toSqlDateTime()."'";
				break;
			case TipoDato::Hora:
				$valor = "'".$valor->toSqlTime()."'";
				break;
		}
		
		return $valor;
	}
	
	
	protected function getLastInsertedId(){
		$data = $this->db->query("SELECT LAST_INSERT_ID() as lastId")->result_array();
		
		if (isset($data["lastId"])){
			return $data["lastId"];
		}
		
		return false;
	}
	
	
	/**
	 * Inserta en la BD los campos dentro de $this->campos
	 * Para ejecutar este metodo, es necesario tener todos los campos con su respectivo valor a exepción
	 * de los que son Nulleables o autoincrementables
	 * @param $camposAInsertar. array de campos que se desean insertar.
	 * @return $this. <cTabla>
	 * @example:
	 * @code
	 * 	$cliente->insert();
	 * 	$cliente->insert(array("nombre", "apellido", "edad"));
	 * @endcode
	 */
	public function insert($camposAInsertar = null){
		$campos = "";
		$valores = "";
		$separador = "";
		$condicion = "";
		
		if ($camposAInsertar === null){
			$camposAInsertar = $this->campos;
		}else{
			$camposAInsertar = $this->campos(true, $camposAInsertar);
		}
		
		//creamos consulta sql de campos y valores
		foreach($camposAInsertar as $campo){
			$campos .= $separador.$campo->getNombre();
			
			if ($campo->esAutoIncrementable()){
				$valores .= $separador."0";
			}elseif ($campo->getValor() === null){
				if ($campo->esNullable()){
					$valores .= $separador."NULL";
				}else{
					throw new FieldCantBeNullException($this->getNombreTabla(), $campo->getNombre());
				}
			}else{
				$valores .= $separador.$this->formatearValorSql($campo->getValor(), $campo->getTipo());
			}
			
			$separador = ", ";
		}
		
		try{
			//insertamos datos
			$this->db->query("INSERT INTO ".$this->getNombreTabla()." ($campos) VALUES ($valores)");
			 
			if (($nuevoId = $this->getLastInsertedId()) !== false){
				$this->ultimoIdInsertado = $nuevoId;
				
				//buscamos si existe un campo auto_increment. Si si existe, lo actualizamos
				foreach($this->campos as $campo){
					if ($campo->esAutoIncrementable()){
						$campo->setValor($nuevoId);
						break;
					}
				}
			}
			
			//modificamos estado
			$this->setEstado(EstadoTabla::actualizado);
		}catch(Exception $e){
			throw new SqlQueryException($e->getMessage());
		}
		
		
		//regresamos referencia al mismo objeto
		return $this;
	}
	
	
	/**
	 *@param $campos <opcional>. array con los campos a modificar.
	 *@param $condicion <opcional>. String. condicion Sql para modificar los campos
	 *@return $this. <cTabla>
	 *@example
	 *@code
	 *	//modifica todos los campos del objeto. La condición se basará en el/los campos IDs detectados
	 *	$user->update();
	 *
	 *	//modifica los campos especificados. La condición se basará en el/los campos IDs detectados
	 *	$user->update(array("nombre", "edad", "sexo"));
	 *
	 *	//modifica los campos especificados en donde el registro cumpla con la condición especificada
	 *	$user->update(array("nombre", "edad", "sexo"), "nombre = 'jose'");
	 *
	 *	//modifica todos los campos en donde el registro cumpla con la condición especificada
	 *	$user->update("nombre = 'jose'");
	 *@endcode
	 */
	public function update($campos = "*", $condicion = null){
		//acomodamos los campos a modificar y creamos condicion sql
		if (is_string($campos) and $campos != "*"){
			/*	esta enviando una condicion y quiere guardar todos los campos
			 * 	$clase->update("nombre = 'jose'");
			 */
			$condicion = $campos;
			$campos = $this->nombreCampos();
		}elseif ($condicion == null){
			/*	la condicion sql será basada en los campos llave
			 * 	$clase->update();
			 * 	$clase->update(array("nombre", "edad", "sexo"));
			 */
			$separador = "";
			foreach($this->camposId as $campoId){
				$condicion .= $separador.$this->campos[$campoId]->getNombre()." = ".$this->formatearValorSql($this->campos[$campoId]->getValor(), $this->campos[$campoId]->getTipo());
				$separador = " and ";
			}
			
			if ($campos == "*"){
				//quiere modificar todos los campos
				$campos = $this->nombreCampos();
			}
		}
		
		$datos = "";
		$separador = "";
		
		//creamos consulta sql de campos y valores
		foreach($campos as $nombreCampo){
			$campo = $this->metaCampo($nombreCampo);
			$datos .= $separador.$campo->getNombre()." = ";
			
			if ($campo->getValor() === null){
				if ($campo->esNullable()){
					$datos .= "NULL";
				}else{
					throw new FieldCantBeNullException($this->getNombreTabla(), $campo->getNombre());
				}
			}else{
				$datos .= $this->formatearValorSql($campo->getValor(), $campo->getTipo());
			}
			
			$separador = ", ";
		}
		
		//modificamos datos
		$this->db->query("UPDATE ".$this->getNombreTabla()." SET $datos WHERE $condicion");
		
		//modificamos estado
		$this->setEstado(EstadoTabla::actualizado);
		
		//regresamos referencia al mismo objeto
		return $this;
	}
	
	
	/**
	 *@param $condicion <opcional>. String. condicion Sql para eliminar los campos
	 *@return $this. <Table_model>
	 *@example
	 *@code
	 *	//borra el registro que cumpla con los campos IDs especificados en el objeto
	 *	$user->delete();
	 *
	 *	//borra el registro que cumpla con la condición especificada
	 *	$user->delete("nombre = 'jose'");
	 *@endcode
	 */
	public function delete($condicion = null){
		if ($condicion == null){
			/*	la condicion sql será basada en los campos llave
			 * 	$clase->delete();
			 */
			$separador = "";
			foreach($this->camposId as $campoId){
				$condicion .= $separador.$this->campos[$campoId]->getNombre()." = ".$this->formatearValorSql($this->campos[$campoId]->getValor(), $this->campos[$campoId]->getTipo());
				$separador = " and ";
			}
		}
		
		//modificamos datos
		$this->db->query("DELETE FROM ".$this->getNombreTabla()." WHERE $condicion");
		
		//modificamos estado
		$this->setEstado(EstadoTabla::nuevo);
		
		//regresamos referencia al mismo objeto
		return $this;
	}
	
	
	/**
     *Agrega una o varias validaciónes a uno o varios campos de la tabla
     *@param $campo. String/Array de nombres de campos
     *@param $validacion. Array de validaciones
     *@return $this. <Table_model>
     *@example
     *@code
     *	$cliente->agregarValidacion(
     *		array("nombre", "apellido", "colonia"),
     *		array(
     *			"obligatorio" = true,
     *			"minLargo" = 4,
     *			"maxLargo" = 10
     *		)
     *	);
     *
     *	$cliente->agregarValidacion("correo",
     *		array(
     *			"obligatorio" = true,
     *			"esCorreo" = true
     *		)
     *	);
     *@endcode
	 */
	public function agregarValidacion($campo, $validacion){
		$campos = ((is_array($campo))?$campo:array($campo));
		
		foreach($campos as $camp){
			if (array_key_exists($camp, $this->campos)){
				foreach($validacion as $llave => $valor){
					$this->campos[$camp]->validaciones[$llave] = $valor;
				}
			}else{
				throw new FieldNotExistException($this->getNombreTabla(), $camp);
			}
		}
		
		//regresamos referencia al mismo objeto
		return $this;
	}
	
	
	/**
	 * verifica si la validación recibida se cumple o no en el campo recibido
	 * @param $valorCampo. Valor del campo a validar
	 * @param $validacion. Llave de la validacion a realizar. Ejemplo "esCorreo"
	 * @param $valor. Valor de la validacion. Ejemplo $validacion = "maxLargo". $valor = 14
	 * @return boolean. True si el campo es valido, de lo contrario regresa false
	 */
	protected function checarValidacion($valorCampo, $validacion, $valor = null){
		$valorCampo = trim($valorCampo);
		$validacion = strtolower($validacion);
		$valido = true;
		
		switch($validacion){
			case "minlargo":
				if (strlen($valorCampo) < $valor){
					$valido = false;
				}
				break;
			case "maxlargo":
				if (strlen($valorCampo) > $valor){
					$valido = false;
				}
				break;
			case "escorreo":
				if (!Validador::esCorreo($valorCampo)){
					$valido = false;
				}
				break;
			case "puedeservacio":
				if (!$valor and Validador::estaVacio($valorCampo)){
					$valido = false;
				}	
				break;
			case "obligatorio":
				if ($valor and Validador::estaVacio($valorCampo)){
					$valido = false;
				}	
				break;
		}
		
		return $valido;
	}
	
	
	/**
	 * Valida todos o una serie de campos.
	 * @param $campos. String/Array. serie de campos a validar. <Opcional>. 
	 * 	Si se deja vacio, se validan todos los campos de la tabla
	 * @throws MY_Exception
	 * @return $this. <Table_model>
	 * @example 
	 * @code
	 * 	$cliente->validarCampos();
	 * 	$cliente->validarCampos(array("edad", "sexo"));
	 * @endcode
	 */
	public function validarCampos($campos = null){
		if ($campos == null){
			//validamos todos los campos
			$campos = $this->nombreCampos();
		}else{
			//nos esta enviando un solo campo como string o una serie de campos dentro de un array
			$campos = ((is_array($campos))?$campos:array($campos));
		}
		
		//obtenemos objetos cCampo de estos nombres de campo
		$campos = $this->campos(true, $campos);
		
		//giramos atravez de los campos
		foreach($campos as $campo){
			//giramos atravez de las validaciones de este campo
			foreach($campo->validaciones as $validacion => $valor){
				if (!$this->checarValidacion($campo->getValor(), $validacion, $valor)){
					throw new FieldNotValidException($this->getNombreTabla(), $campo->getNombre(), $validacion, $valor);
				}
			}
		}
		
		//regresamos referencia al mismo objeto
		return $this;
	}
	
	
	/**
	 * Respalda los datos en los campos existentes
	 * @return $this. <Table_model>
	 * @example
	 * @code
	 * 	$cliente->respaldarCampos();
	 * 
	 * 	try{
	 * 		$cliente->campos(array(
	 * 			"nombre" => "jose", 
	 * 			"sexo" => "H", 
	 * 			"edad" => 43
	 * 		));
	 * 		//se validan los datos. Si se detecta un error, se lanza una excepcón y los datos 
	 * 		//no se insertan/modifican en la BD
	 * 		$cliente->validarCampos();
	 * 
	 * 		$cliente->liberarRespaldo();  
	 * 		$cliente->insert();
	 * 	}catch(MY_Exception $e){
	 * 		$cliente->restaurarCampos();
	 * 		echo $e;
	 * 	}
	 * @endcode
	 */
	public function respaldarCampos(){
		//respaldamos datos actuales
		$this->camposBuff = $this->campos();
		
		//regresamos referencia al mismo objeto
		return $this;
	}

	
	/**
	 * Libera los recursos usados por el buffer de respaldo de datos
	 * @return $this. <Table_model>
	 */
	public function liberarRespaldo(){
		$this->camposBuff = array();
		
		//regresamos referencia al mismo objeto
		return $this;
	}


	/**
	 * Pasa el respaldo de datos a los datos originales. Volviendo asi a una version anterior del objeto
	 * @return $this. <Table_model>
	 */
	public function restaurarCampos(){
		//restauramos los datos
		$this->campos($this->camposBuff);
		
		//regresamos referencia al mismo objeto
		return $this;
	}
}
?>