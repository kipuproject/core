<?php
if(!isset($GLOBALS["autorizado"])){
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

class FuncioninternalBooking{

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
	var $mensaje;

	function verificarCampos(){
		include_once($this->ruta."/funcion/verificarCampos.php");
		if($this->error==true){
			return false;
		}else{
			return true;
		}
	}

	function procesarReservaItemReservable($variable){
		include_once($this->ruta."/funcion/procesarReservaIR.php");
	}
	
	function buscarTipoReserva(){
		include_once($this->ruta."/funcion/buscarTipoReserva.php");
	}

	function consultarDisponibilidadItemReservable(){
		include_once($this->ruta."/funcion/consultarDisponibilidadIR.php");
	}

	function redireccionar($opcion, $valor=""){
		include_once($this->ruta."/funcion/redireccionar.php");
	}

	function action(){
		$this->data=array();
		//Evitar que se ingrese codigo HTML y PHP en los campos de texto
		//Campos que se quieren excluir de la limpieza de código. Formato: nombreCampo1|nombreCampo2|nombreCampo3
		$excluir="";
		$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);
		$_REQUEST=$this->miInspectorHTML->limpiarSQL($_REQUEST);


		$option=isset($_REQUEST['option'])?$_REQUEST['option']:"";
		
		switch($option){
		  case 'consultarDisponibilidadIR':
		    $this->consultarDisponibilidadItemReservable();
			$this->redireccionar("mostrarDisponibilidadIR",array($this->mensaje,$this->data));
		  break;
		  case 'consultarDisponibilidadNP':
		    $this->consultarDisponibilidadNumeroPersonas();
			$this->redireccionar("mostrarDisponibilidadNP",array($this->mensaje,$this->data));
		  break;
		  
		  case 'procesarReserva':

			if(!isset($_REQUEST['jxajax'])){

				$this->rutaURL=$this->miConfigurador->getVariableConfiguracion("host");
				$this->rutaURL.=$this->miConfigurador->getVariableConfiguracion("site");

				$this->procesarReservaItemReservable();
				if($this->status=="false"){
					echo "<script>location.replace('".$this->rutaURL."/index.php?mensaje=".$this->mensaje."')</script>";
				}elseif($this->status=="true"){
					echo "<script>location.replace('".$this->rutaURL."/index.php?mensaje=".$this->mensaje."')</script>";
				}
				
			}else{
				$this->procesarReservaItemReservable($_REQUEST);
				$responce=new stdClass();
				$responce->mensaje = $this->mensaje;
				$responce->status = $this->status;
				$responce->control = $this->txtControl;
				$responce->content = isset($this->content)?$this->content:"";
				$responce->idbooking = isset($this->idbooking)?$this->idbooking:"";
				$responce->value = isset($this->value)?$this->value:"";
				echo json_encode($responce);
			}
			
		 break;
		 case 'buscarTipoReserva':
			$this->buscarTipoReserva();
			$this->redireccionar("mostrarTiposReserva",array($this->mensaje,$this->data));
		  break;			 
		}
		return false;
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
	
	/**
	* Cada key posee un valor unico $newArray[$value[$key]]
	* Cada key puede conener multiples registros $newArray[$value[$key]][]
	*/
	public function orderArrayKeyBy($array,$key,$second_key=""){
		$newArray=array();
		if($second_key<>""){
			foreach($array as $name=>$value){
				$newArray[$value[$key]][$value[$second_key]]=$array[$name];
			}
		}else{
			foreach($array as $name=>$value){
				$newArray[$value[$key]]=$array[$name];
			}
		}
		return $newArray;
	}

}