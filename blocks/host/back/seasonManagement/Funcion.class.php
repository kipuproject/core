<?php
if(!isset($GLOBALS["autorizado"])){
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("core/builder/Mensaje.class.php");
include_once("core/crypto/Encriptador.class.php");
include_once("core/auth/Sesion.class.php");

class FuncionseasonManagement{

	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $error;
	var $miRecursoDB;
	var $crypto;
	var $mensaje;
	var $status;
	

	function processSeason($variable){
		include_once($this->ruta."/funcion/processSeason.php");
	}

	function redireccionar($option, $valor=""){
		include_once($this->ruta."/funcion/redireccionar.php");
	}
	

	function action(){
			
		$excluir="";
		$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);

		$option=isset($_REQUEST['option'])?$_REQUEST['option']:"";

		switch($option){
			case "updateSeason":
				$this->processSeason($_REQUEST);
				echo implode("\n",$this->mensaje);
			break;
		}
	}

	function __construct(){
  
		$this->miConfigurador=Configurador::singleton();
		$this->miInspectorHTML=InspectorHTML::singleton();
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");		
		$this->miMensaje=Mensaje::singleton();
		$this->miSesion=Sesion::singleton();
		$conexion=$this->miSesion->getValorSesion('dbms');
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);		
		$this->masterResource=$this->miConfigurador->fabricaConexiones->getRecursoDB("master");		
		$this->commerce=$this->miSesion->getValorSesion('commerce');
	}

	public function setRuta($unaRuta){
		$this->ruta=$unaRuta;
	}

	function setSql($a){
		$this->sql=$a;
	}

	function setFuncion($funcion){
		$this->funcion=$funcion;
	}

	public function setLenguaje($lenguaje){
		$this->lenguaje=$lenguaje;
	}

	public function setFormulario($formulario){
		$this->formulario=$formulario;
	}

}