<?php
require_once("core/manager/Configurador.class.php");
require_once("aes.class.php");
require_once("aesctr.class.php");

class Encriptador{

	private static $instance;

	//Constructor
	function __construct(){

	}

	public static function singleton()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}


	function codificar_url($cadena,$enlace=""){
		$cifrado = MCRYPT_RIJNDAEL_256;
	  $modo = MCRYPT_MODE_ECB;
	  $clave = "4@t0rK4renP4l4c10s";
		$cadena = base64_encode(mcrypt_encrypt($cifrado,$clave,$cadena,$modo,mcrypt_create_iv(mcrypt_get_iv_size($cifrado,$modo), MCRYPT_RAND)));

		$this->miConfigurador = Configurador::singleton();

		$cadena=$enlace."=".$cadena;

		return $cadena;
	}

	/**
	 *
	 * Método para decodificar la cadena GET para obtener las variables de la petición
	 * @param $cadena
	 * @return boolean
	 */

	function decodificar_url($cadena){
	  /*reemplaza valores + / */
		//$cadena=AesCtr::decrypt(str_pad(strtr($cadena, '-_', '+/'), strlen($cadena) % 4, '=', STR_PAD_RIGHT),"",256);

		$cadena = str_replace(" ","+",$cadena);
		$cifrado = MCRYPT_RIJNDAEL_256;
	  $modo = MCRYPT_MODE_ECB;
	  $clave = "4@t0rK4renP4l4c10s";
		$cadena = trim(mcrypt_decrypt($cifrado,$clave,base64_decode($cadena),$modo,mcrypt_create_iv(mcrypt_get_iv_size($cifrado, $modo), MCRYPT_RAND)));

		parse_str($cadena,$matriz);

		foreach($matriz as $clave=>$valor){
			$_REQUEST[$clave] = $valor;
		}

		return true;
	}

	function codificar($cadena){
    $cifrado = MCRYPT_RIJNDAEL_256;
    $modo = MCRYPT_MODE_ECB;
    $clave = "4@t0rK4renP4l4c10s";
    $cadena=base64_encode(mcrypt_encrypt($cifrado,$clave,$cadena,$modo,mcrypt_create_iv(mcrypt_get_iv_size($cifrado,$modo), MCRYPT_RAND)));
		return $cadena;
  }

	function decodificar($cadena){
    /*reemplaza valores + / */
		//$cadena=AesCtr::decrypt(str_pad(strtr($cadena, '-_', '+/'), strlen($cadena) % 4, '=', STR_PAD_RIGHT),"",256);

    $cifrado = MCRYPT_RIJNDAEL_256;
    $modo = MCRYPT_MODE_ECB;
    $clave = "4@t0rK4renP4l4c10s";
    $cadena = trim(mcrypt_decrypt($cifrado,$clave,base64_decode($cadena),$modo,mcrypt_create_iv(mcrypt_get_iv_size($cifrado, $modo), MCRYPT_RAND)));

		return $cadena;

	}

}//Fin de la clase

?>
