<?php
require_once("core/manager/Configurador.class.php");

class ProcesadorPagina{

	var $miConfigurador;
	var $raizDocumentos;
	var $unBloque;

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		$this->raizDocumentos=$this->miConfigurador->getVariableConfiguracion("raizDocumento");
	}

	function procesarPagina(){
		/**
		 * Siempre debe existir una variable bloque que identifica el bloque que va a procesar los datos recibidos por REQUEST
		 * esta variable, y la variable bloqueGrupo se definen en el formulario y va codificada dentro de la variable formsaradata
		 */
		$unBloque["nombre"]=$_REQUEST["action"];
		$unBloque["id_bloque"]=$_REQUEST["bloque"];

		if(isset($_REQUEST["bloqueGrupo"]) && $_REQUEST["bloqueGrupo"]!="") {
			$unBloque["grupo"]=$_REQUEST["bloqueGrupo"];
			include_once($this->raizDocumentos."/blocks/".$_REQUEST["bloqueGrupo"]."/".$unBloque["nombre"]."/bloque.php");
		}else{
			$_REQUEST["bloqueGrupo"]="";
			include_once($this->raizDocumentos."/blocks/".$unBloque["nombre"]."/bloque.php");
		}
		return true;
	}
}