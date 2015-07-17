<?php
class InspectorHTML{
	
	private static $instance;
	
	
	//Constructor
	function __construct(){
	
	}
	
	function isNumber($numero)
	{
		if(is_numeric($numero)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function singleton()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}
	
	function limpiarPHPHTML($arreglo, $excluir=""){
		
		if($excluir!=""){
			$variables=explode("|",$excluir);
		}else{
			$variables[0]="";
		}
		
		foreach ($arreglo as $clave => $valor){

			if(!in_array($clave,$variables)){
				if(!is_array($valor)){
					$arreglo[$clave]= strip_tags($valor);
				}else{
					foreach ($valor as $clavevalor => $valorvalor){
						$arreglo[$clave][$clavevalor]= strip_tags($valorvalor);
					}
				}
			}
		}		
		
		return $arreglo;
		
	}
	
	
	function limpiarSQL($arreglo, $excluir=""){
	
		if($excluir!=""){
			$variables=explode("|",$excluir);
		}else{
			$variables[0]="";
		}
	
		foreach ($arreglo as $clave => $valor)
		{
			if(!is_array($valor)){
				$arreglo[$clave]= strip_tags($valor);
			}else{
				foreach ($valor as $clavevalor => $valorvalor){
					$arreglo[$clave][$clavevalor]= strip_tags($valorvalor);
				}
			}
		}
	
		return $arreglo;
	
	}

	function isValidEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		else {
			return false;
		}
	}

	function minMaxRange($min, $max, $what)
	{
		if(strlen(trim($what)) < $min)
			return true;
		else if(strlen(trim($what)) > $max)
			return true;
		else
		return false;
	}

	function friendly_url($url) {
			// Tranformamos todo a minusculas
			$url = strtolower($url);
			//Rememplazamos caracteres especiales latinos
			$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
			$repl = array('a', 'e', 'i', 'o', 'u', 'n');
			$url = str_replace ($find, $repl, $url);
			// Añadimos los guiones
			$find = array(' ', '&', '\r\n', '\n', '+');
			$url = str_replace ($find, '-', $url);
			// Eliminamos y Reemplazamos demás caracteres especiales
			$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
			$repl = array('', '-', '');
			$url = preg_replace ($find, $repl, $url);

			return $url;

	}
	
	
}
