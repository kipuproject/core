<?php
if(!isset($GLOBALS["autorizado"])){
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("core/crypto/Encriptador.class.php");
include_once("core/auth/Sesion.class.php");

class FuncionclientManagement{

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
	
	function processNew($variable){
		include_once($this->ruta."/funcion/processNew.php");
	}

	function processEdit($id){
		include_once($this->ruta."/funcion/processEdit.php");
	}

	function enviarCorreo($email,$clave){
		include_once($this->ruta."/funcion/enviarCorreo.php");
	}
	
	function processDelete($id){
		include_once($this->ruta."/funcion/processDelete.php");
	}

	function redireccionar($option, $valor=""){
		include_once($this->ruta."/funcion/redireccionar.php");
	}

	function action(){
    
    $excluir="";
		$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);

    $option=isset($_REQUEST['option'])?$_REQUEST['option']:"list";

		switch($option){
			case "processNew":
				$this->processNew($_REQUEST);
				if(!$this->status){
					$mensaje=implode("<br/>",$this->mensaje['error']);	
					$this->redireccionar("falloRegistro",array($mensaje,$this->data));
				}else{
					$mensaje=implode("<br/>",$this->mensaje['exito']);
					$this->redireccionar("exitoRegistro",array($mensaje));
				}
			break;
			case "processEdit":
				$this->processEdit($_REQUEST['optionValue']);
				$this->redireccionar("exitoRegistro",array($mensaje)); 
			break;
			case "processDelete":
				$this->processDelete($_REQUEST['optionValue']);
				$this->redireccionar("exitoRegistro",array($mensaje));
			break;
		}
	}

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		$this->miInspectorHTML=InspectorHTML::singleton();
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");		
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
?>