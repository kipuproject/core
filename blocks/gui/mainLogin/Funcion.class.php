<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/auth/Sesion.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("core/builder/Mensaje.class.php");


//Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
//metodos mas utilizados en la aplicacion

//Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
//en camel case precedido por la palabra Funcion

class FuncionmainLogin
{

	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $miSesion;
	var $error;
	var $miRecursoDB;
	var $crypto;

	function verificarCampos(){
		include_once($this->ruta."/funcion/verificarCampos.php");
		if($this->error==true){
			return false;
		}else{
			return true;
		}
	}

	function login(){
		include_once($this->ruta."/funcion/procesarLogin.php");
	}

	function redireccionar($opcion, $valor=""){
		include_once($this->ruta."/funcion/redireccionar.php");
	}

	function action(){
    
		//$this->borrarSesionesExpiradas();  //funcion pendiente por hacer

		//Evitar que se ingrese codigo HTML y PHP en los campos de texto
		//Campos que se quieren excluir de la limpieza de código. Formato: nombreCampo1|nombreCampo2|nombreCampo3
		$excluir="";
		$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);

		if(!isset($_REQUEST["opcionLogin"]) 	|| (isset($_REQUEST["opcionLogin"]) &&($_REQUEST["opcionLogin"]=="login"))){
			//Realizar una validación específica para los campos de este formulario:
			$validacion=$this->verificarCampos();

			if($validacion==false){
				//Instanciar a la clase pagina con mensaje de correcion de datos
				echo "Datos Incorrectos";

			}else{
				//Validar las variables para evitar un tipo  insercion de SQL
				$_REQUEST=$this->miInspectorHTML->limpiarSQL($_REQUEST);
				if(!isset($_REQUEST['opcionLogin'])||$_REQUEST["opcionLogin"]=="login"){
					$this->login();
				}					

			}
		}elseif(isset($_REQUEST["opcionLogin"]) &&($_REQUEST["opcionLogin"]=="logout")){
		    $this->miSesion->terminarSesion($_SESSION['aplicativo']);
		    $valor['OPCION']="login";
		    $_REQUEST=array();
		    echo "<script>location.replace('".$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."')</script>";
		}
		return false;
	}

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		$this->miInspectorHTML=InspectorHTML::singleton();
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
		$this->miMensaje=Mensaje::singleton();
		$this->miSesion=Sesion::singleton();
		$this->enlace=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."?".$this->miConfigurador->getVariableConfiguracion("enlace");
		$conexion="master";
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
	}

	public function setRuta($unaRuta){
		$this->ruta=$unaRuta;
		//Incluir las funciones
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
	

	public function borrarSesionesExpiradas(){
	
		if(!$this->miConfigurador->getVariableConfiguracion("estaSesion")){
			$miSesion["sesionId"]=time();
			$this->miConfigurador->setVariableConfiguracion("estaSesion",$miSesion);
		}
		$estaSesion=$this->miConfigurador->getVariableConfiguracion("estaSesion");

		$conexion="master";
		$esteRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
		$resultado=$esteRecursoDB->ejecutarAcceso($cadena_sql,"acceso");
	}

}