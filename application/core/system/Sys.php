<?php
/**
 * @author angel
 * Clase con funciones estaticas que realizan tareas especificas
 * En pocas palabras, es una librería de funciones
 */
class Sys{
	/**
	 * @var Boolean: Indica si todo el sistema estar? funcionando en modo <Depuraci?n> o en modo <Liberado>
	 * Deshabilitarlo cuando el sistema se encuentre en optimas condiciones para ser liberado
	 * Al Deshabilitarlo se dejar?n de mostrar detalles de los errores como los querys sql entre otros.
	 */
	public static $depurando = true;
	
	/**
	 * Tiene la url base de la aplicacion, tiene la url que esta en el archivo config.php
	 * @var string
	 */
	public static $url_base = 'http://localhost/lml/';
	
	/**
	 * Tiene el nombre de la pagina web
	 * @var string
	 */
	public static $title_web = 'YouHaveItOn';
	
	/**
	 * Tiene el consumer_key de la aplicacion web
	 * @var string
	 */
	public static $consumer_key = 'd313f3276c787d9a38184f954f74fdd704e02491a'; //d313f3276c787d9a38184f954f74fdd704e02491a //89c1cd99a5ed1f0362668d15c15cb58104d7675fc
	/**
	 * Tiene el consumer_secret de la aplicacion web
	 * @var string
	 */
	public static $consumer_secret = '082050b0a9187199ec0ae3a53f1c9eff'; //082050b0a9187199ec0ae3a53f1c9eff //f406440e71422542b5c40ec417df1e4c
	
	
	
	/**
	 * Dias en los que la sesion caducara (cookies y token)
	 * @var int
	 */
	public static $destroy_session = 3;
	
	/**
	 * Especifica el idioma por default que toma el sistema para los textos si esque no se le especifica otro.
	 * @var string
	 */
	public static $idioma = Idiomas::ingles;
	
	/**
	 * Contiene el idioma que el usuario esta solicitando, esta a qui para que
	 * se mantenga en todos lados
	 * @var string
	 */
	public static $idioma_load = idiomas::ingles;
	
	/**
	 * Especifica el pais por default para asignarlo a los usuarios
	 * @var string
	 */
	public static $country_iso2 = 'MX';
	public static $country_iso3 = 'MEX';
	
	/**
	 * Especifica el pais por default las marcas
	 * @var string
	 */
	public static $country_brand = 'US';
	
	
	/**
	 * Configuracion para la aplicacion de facebook
	 */
	public static $facebook_app_id = "108613799232588"; //"108922552528265"; //"93290085793";
	public static $facebook_app_secret = "d78605ad9b784a7f2e5f42e41bbfe7b7"; //"b902ff4b046a133055809bef6fe9a33b"; //"04d4d10fa3e1bc592cd6f5c438e5de55";
	
	/**
	 * Configuracion para la aplicacion de twitter
	 */
	public static $twitter_app_id = "DlxKvv2uLfWEZHFbo7PlQ"; //"cPeqo1mZgkS25Igv1feA";
	public static $twitter_app_secret = "lKdkwsUdQnwjzTc3SSnREfhM70KC4ouxaZxSAjqJVA"; //"IFvlKi0ChHIWty8J6eC6O7V1UpPbH9ZT2CaEFbFtw";
	
	
	
	public static function getTokenVal(){
		if(isset($_COOKIE['lml_usr_id']) && isset($_COOKIE['lml_usr_token'])){
			return $_COOKIE['lml_usr_token'];
		}
		return false;
	}
	
	public static function getCurrentUrl(){
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		return $url;
	}
	
	public static function getLang(){
		$lang = isset($_REQUEST['lang'])? $_REQUEST['lang']: '';
		$lang = isset($_POST['lang'])? $_POST['lang']: $lang;
		$lang = isset($_GET['lang'])? $_GET['lang']: $lang;
		$lang = isset($_COOKIE['lml_lang'])? $_COOKIE['lml_lang']: $lang;
		
		if($lang != ''){
    		if(file_exists(APPPATH.'/language/'.$lang.'/')){
    			Sys::$idioma_load = $lang;
    			return $lang;
    		}else 
    			return Sys::$idioma;
    	}else 
    		return Sys::$idioma;
	}
	
	public static function getLocalTimezone($offset=null){
	    /*$iTime = time();
	    $arr = localtime($iTime);
	    $arr[5] += 1900; 
	    $arr[4]++;
	    $iTztime = gmmktime($arr[2], $arr[1], $arr[0], $arr[4], $arr[3], $arr[5], $arr[8]);
	    $offset = doubleval(($iTztime-$iTime)/(60*60));*/
		if($offset==null){
			if(isset($_COOKIE['lml_tzone'])){
				$offset = $_COOKIE['lml_tzone'];
			}else
				return false;
		}
		
	    $zonelist = 
	    array
	    (
	        'Kwajalein' => "-12:00",
	        'Pacific/Midway' => "-11:00",
	        'Pacific/Honolulu' => "-10:00",
	        'America/Anchorage' => "-09:00",
	        'America/Los_Angeles' => "-08:00",
	        'America/Denver' => "-07:00",
	        'America/Tegucigalpa' => "-06:00",
	        'America/New_York' => "-05:00",
	        'America/Caracas' => "-04:30",
	        'America/Halifax' => "-04:00",
	        'America/St_Johns' => "-03:30",
	        'America/Argentina/Buenos_Aires' => "-03:00",
	        'America/Sao_Paulo' => "-03:00",
	        'Atlantic/South_Georgia' => "-02:00",
	        'Atlantic/Azores' => "-01:00",
	        'Europe/Dublin' => "+00:00",
	        'Europe/Belgrade' => "+01:00",
	        'Europe/Minsk' => "+02:00",
	        'Asia/Kuwait' => "+03:00",
	        'Asia/Tehran' => "+03:30",
	        'Asia/Muscat' => "+04:00",
	        'Asia/Yekaterinburg' => "+05:00",
	        'Asia/Kolkata' => "+05:30",
	        'Asia/Katmandu' => "+05:45",
	        'Asia/Dhaka' => "+06:00",
	        'Asia/Rangoon' => "+06:30",
	        'Asia/Krasnoyarsk' => "+07:00",
	        'Asia/Brunei' => "+08:00",
	        'Asia/Seoul' => "+09:00",
	        'Australia/Darwin' => "+09:30",
	        'Australia/Canberra' => "+10:00",
	        'Asia/Magadan' => "+11:00",
	        'Pacific/Fiji' => "+12:00",
	        'Pacific/Tongatapu' => "+13:00"
	    );
	    $index = array_keys($zonelist, $offset);
	    if(sizeof($index)!=1)
	        return false;
	    return array("region" => $index[0], "offset" => $offset);
	}
	
	
	public static function lml_get_url($id, $val){
		switch($id){
			case 'postyle': return Sys::$url_base."postyle?lmlid=".$val; break;
			case 'more_friends': return Sys::$url_base."my_friends?lmlid=".$val; break;
			case 'brand': return Sys::$url_base."brands/".$val; break;
			case 'user': return Sys::$url_base."?lmlid=".$val; break;
			case 'store': return Sys::$url_base."stores/".$val; break;
			case 'item': return Sys::$url_base."item?lmlid=".$val; break;
		}
	}
	
	public static function loadFile($path, $type='file'){
		$ci =& get_instance();
		switch($type){
			case 'file': $ci->load->file($path); break;
			case 'library': $ci->load->library($path); break;
		}
	}
	
	
	/**
	 * Carga los textos del lenguaje especificado.
	 * Si no se especifica, se toma el lenguaje self::$idioma 
	 * @param <Idiomas> $lang Idioma a cargar
	 */
	public static function loadLanguage($lang = null, $file=''){
		if ($lang == null){
			$lang = self::$idioma_load;
		}
		
		$ci =& get_instance();
		if($file==''){
			$ci->lang->load('text', $lang);
	    	$ci->lang->load('exception', $lang);
		}else
			$ci->lang->load($file, $lang);
		$ci->load->helper('language');
    }
    
    
    /**
     * Quita los acentos de una cadena
     * @param string $cadena
     */
	public static function eliminaAcentos($cadena){
		$tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
		$replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
		return(strtr($cadena,$tofind,$replac));
	}
    
	/*
	 * @return
	 * 	regresa un string si $getArrayWithLines = false. De lo contrario regresa las lineas en 
	 * 	un array
	 */
	public static function leerArchivo($urlArchivo, $getArrayWithLines = false){
		$archivo = "";
			
		if (file_exists($urlArchivo)){
			if ($getArrayWithLines){
				$archivo = file($urlArchivo);
			}else{
				$archivo = file_get_contents($urlArchivo);
			}
		}

		return $archivo;
	}


	/*
	 * Forza un archivo a ser descargado por el navegador
	 */
	public static function forzarDescargaArchivo($urlArchivo){
		if (file_exists($urlArchivo)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($urlArchivo));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($urlArchivo));
			ob_clean();
			flush();
			readfile($urlArchivo);
		}
	}


	public static function dosDigitos($numero){
		if (strlen((string)$numero) == 1){
			return ('0'.$numero);
		}else{
			return (string)$numero;
		}
	}


	/**
	 *Convierte el id numerico regresado por mysqli_result::fetch_field_direct::type a un tipo de dato de TipoDato
	 *@return Elemento de TipoDato. Ejemplo TipoDato::Numero
	 */
	public static function tipoDatoMysqlToTipoDato($tipoDatoMysql){
		switch($tipoDatoMysql){
			case (($tipoDatoMysql > -1 and $tipoDatoMysql < 6) || $tipoDatoMysql == 8 || $tipoDatoMysql == 9 || $tipoDatoMysql == 246):
				$tipoDatoMysql = TipoDato::Numero;
				break;
			case ($tipoDatoMysql == 6):
				$tipoDatoMysql = TipoDato::Null;
				break;
			case ($tipoDatoMysql == 7 || $tipoDatoMysql == 12):
				$tipoDatoMysql = TipoDato::FechaHora;
				break;
			case ($tipoDatoMysql == 10):
				$tipoDatoMysql = TipoDato::Fecha;
				break;
			case ($tipoDatoMysql == 11):
				$tipoDatoMysql = TipoDato::Hora;
				break;
			case ($tipoDatoMysql == 13):
				$tipoDatoMysql = TipoDato::Anio;
				break;
			case ($tipoDatoMysql == 16):
				$tipoDatoMysql = TipoDato::Bit;
				break;
			default:
				$tipoDatoMysql = TipoDato::Cadena;
				break;
		}

		return $tipoDatoMysql;
	}
	
	
	/**
	 *Convierte el el valor de information_schema.columns.data_type a un tipo de dato de TipoDato
	 *@return Elemento de TipoDato. Ejemplo TipoDato::Numero
	 */
	public static function tipoDatoSchemaMysqlToTipoDato($tipoDatoSchemaMysql){
		$tipos = array(
			"varchar" => 1,
			"char" => 1,
			"longtext" => 1,
			"text" => 1,
			"tinytext" => 1,
			"mediumtext" => 1,
			"int" => 0,
			"decimal" => 0,
			"bigint" => 0,
			"tinyint" => 0,
			"smallint" => 0,
			"mediumint" => 0,
			"float" => 0,
			"double" => 0,
			"bit" => 5,
			"datetime" => 3,
			"date" => 2,
			"timestamp" => 3,
			"time" => 4,
			"year" => 7,
			"binary" => 8,
			"varbinary" => 8
		);
		
		$tipo = 1; //cadena por default
		if (isset($tipos[$tipoDatoSchemaMysql])){
			$tipo = $tipos[$tipoDatoSchemaMysql];
		}
		
		return $tipo;
	}
	
	public static function pagination($query, &$params, $total_rows=false){
		$CI =& get_instance();
		$res = $CI->db->query($query);
		
		$num_pag = ceil($res->num_rows()/$params['result_items_per_page']);
		$params['result_page'] = ($params['result_page']>0? $params['result_page']: 1)-1;
		$pag = $params['result_page']*$params['result_items_per_page'];
		
		if($total_rows)
			return array("query" => $query." LIMIT ".$pag.", ".$params['result_items_per_page'], 
						"total_rows" => $res->num_rows());
		else
			return $query." LIMIT ".$pag.", ".$params['result_items_per_page'];
	}
	
	
	public static function buscarArray($array, $key, $value){
		$results = array();
	
		if(is_array($array)){
			foreach($array as $key_item => $item){
				if($item[$key] == $value){
					$results[$key_item] = $item;
					break;
				}
			}
		}
	
		return $results;
	}
	
	/**
	 * Ordena un array multidimencion por un campo indicado
	 * @m: array. El array a ordenar
	 * @ordenar: string. campo del array por el cual se ordenara
	 * @direccion: string. indica si se ordena ascendente o descendentemente 'ASC'
	 * @result: array. el array ordenado.
	 */
	public static function ordenarMultiArray($m,$ordenar,$direccion) {
		usort($m, create_function('$item1, $item2', 'return strtoupper($item1[\'' . $ordenar . '\']) ' . ($direccion === 'ASC' ? '>' : '<') . ' strtoupper($item2[\'' . $ordenar . '\']);'));
		return $m;
	}
	
	public static function urlValid($url){
		return ( !! preg_match("/^((ht|f)tp(s?):\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(\/?[a-zA-Z0-9._\/~#&=;%+?-]+[a-zA-Z0-9\/#=?]{1,1}))$/", $url) ? TRUE : FALSE);
	}


	/**
	 * Limpia una cadena
	 * @param $txt. Texto a ser limpiado
	 * @return String. Texto limpio
	 */
	public static function limpiarTexto($txt, $remove_q=true){
		if(is_array($txt)){
			foreach($txt as $key => $item){
				$txt[$key] = addslashes(self::quitComillas(strip_tags(stripslashes(trim($item)))));
			}
			return $txt;
		}else{
			return addslashes(self::quitComillas(strip_tags(stripslashes(trim($txt)))));
		}
	}
	
	
	/**
	 * @param $txt. Texto al que se le eliminarán las comillas
	 * @return String. Texto sin comillas
	 */
	public static function quitComillas($txt){
		return str_replace("'","‘", str_replace('"','?',$txt));
	}

	
	public static function redireccionar($url){
		header("Location:".$url);
	}
	
	
	public static function recortarTexto($texto, $largo){
		return ((strlen($texto) > $largo)?(substr($texto, 0, $largo)."..."):$texto);
	}
}
?>