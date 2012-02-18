<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Server extends Services{
	private $app_id = '93290085793';
	private $app_secret = "04d4d10fa3e1bc592cd6f5c438e5de55";
	private $redirect_url = "";
	
	function Server(){
		parent::__construct();
		$this->load->helper('url');
		$this->redirect_url = $this->config->item('base_url').'services/Server/pru';
	}
	
	function oauth(){
		$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=".$this->app_id.
	        			"&redirect_uri=".urlencode($this->redirect_url)."&scope=publish_stream";
		header("Location: ".$dialog_url);
	}
	
	function pru(){
		$fb = new Facebook(array(
		    'appId' => $this->app_id,
		    'secret' => $this->app_secret,
		    'cookie' => true,
		    ));
		$session = $fb->getSession();
		var_dump($_GET);
		var_dump($session);
	}
	
	private function foto(){
		$url_encode = urlencode( URLB . "index.php?opc=perfil&method=foto"); // CODIFICA URL
		$code = $_REQUEST['code'];
		
		if ( empty( $code ) ){
		header('Location:http://www.facebook.com/dialog/oauth?client_id=146020155462544&redirect_uri=' . $url_encode . '&scope=publish_stream');
		}
		elseif ( !isset( $_REQUEST['error_reason'] ) ) {
		$token_url = "https://graph.facebook.com/oauth/access_token?client_id=146020155462544&redirect_uri=" . $url_encode . "&client_secret=cc4a286e104ff0aa88660177ac1c1ba1&code=" . $code;
		$access_token = file_get_contents( $token_url );
		$graph_url = "https://graph.facebook.com/me/photos?" . $access_token;
		$post = file_get_contents ( $graph_url ) ;
		
		$access_token = explode( "&" , str_replace( "access_token=" , "" , $access_token ) );
		$url = 'https://graph.facebook.com/me/photos';
		
		$urlImagen = 'http://www.ecojoven.com/cuatro/04/gente.jpg';
		$imagenTmp = tempnam('.', 'xhttp-tmp-'); // CREA UNA RUTA TEMPORAL PARA LA IMAGEN
		file_put_contents( $imagenTmp , file_get_contents( $urlImagen ) );
		$img = '@' . $imagenTmp;
		var_dump($img);
		echo '</br></br>';
		
		$datosFoto = array( // ARRAY CON LA INFORMACION DEL EVENTO QUE SE ENVIARA A FB
		'access_token' => $access_token [0] , // ACCESS TOKEN PARA PERMISOS
		'message'=>'Mensaje de la foto',
		'source' => $img, // IMAGEN DEL EVENTO
		'tags' => "[{\"tag_uid\":\"\",\"tag_text\":\"gama\",\"x\":45.283,\"y\":38.5714}]"
		);
		//{"data":[{"id":"","name":"gama","x":45.283,"y":38.5714,"created_time":"2011-04-01T17:59:33+0000"}]
		$handle = curl_init( $url );
		curl_setopt( $handle , CURLOPT_RETURNTRANSFER , true );
		curl_setopt( $handle , CURLOPT_POST , true );
		curl_setopt( $handle , CURLOPT_HTTPHEADER , array('Expect:') );
		curl_setopt( $handle , CURLOPT_POSTFIELDS , $datosFoto );
		
		$respuesta = json_decode( curl_exec( $handle ) ); // OBTIENE EL RESULTADO DEL ENVIO DE DATOS DEL EVENTO
		echo '</br></br>';
		var_dump($respuesta);
		
		unlink( str_replace( '@' , '' , $img ) );
		curl_close( $handle );
		exit;
		
		}
		else{}
		
		//echo str_replace(',', ', ', $str);
	}
}