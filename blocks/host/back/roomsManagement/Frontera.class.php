<?php
include_once("core/manager/Configurador.class.php");
include_once("core/auth/Sesion.class.php");
include_once("plugin/filter/generadorFiltros.class.php");

class FronteraroomsManagement{

	var $ruta;
	var $sql;
	var $funcion;
	var $lenguaje;
	var $formulario;
	var $enlace;
	var $miConfigurador;
	var $companies;
	
	function __construct()
	{
	
		$this->miConfigurador=Configurador::singleton();
		$this->miSesion=Sesion::singleton();
		$conexion=$this->miSesion->getValorSesion('dbms');
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);		
		$this->masterResource=$this->miConfigurador->fabricaConexiones->getRecursoDB("master");		
		$this->commerce=$this->miSesion->getValorSesion('commerce');
		$this->enlace=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."?".$this->miConfigurador->getVariableConfiguracion("enlace");
		 
	}

	public function setRuta($unaRuta){
		$this->ruta=$unaRuta;
	}

	public function setLenguaje($lenguaje){
		$this->lenguaje=$lenguaje;
	}

	public function setFormulario($formulario){
		$this->formulario=$formulario;
	}

	function setSql($a)
	{
		$this->sql=$a;

	}

	function setFuncion($funcion){
		$this->funcion=$funcion;

	}

	function html(){
		
		include_once("core/builder/FormularioHtml.class.php");
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
		$this->miFormulario=new formularioHtml();
		$option=isset($_REQUEST['option'])?$_REQUEST['option']:"listRooms";

		switch($option){
			case "listRooms":
				$this->showListRooms();
				break;
			case "listTypeRooms":
				$this->showListTypeRooms();
				break;				
			case "new":
				$this->showNew();
				break;
			case "edit":
				$this->showEdit($_REQUEST['optionValue']);
				break;
			case "view":
				$this->showEdit($_REQUEST['optionValue']);
				break;

		}
	}

	function showListRooms($currency="COP"){
	
		$variable["commerce"]=$commerce=$this->commerce;
		$variable["currency"]=$currency;  //Pendiente por parametrizar

		$cadena_sql=$this->sql->cadena_sql("roomListbyCommerce",$variable);
		$roomList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("priceList");
		$priceList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("typeListRoom",$variable);
		$typeListRoom=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$priceList=$this->orderArrayKeyBy($priceList,"IDRESERVABLE","SEASON");
		
		$formSaraData="jxajax=main"; 
		$formSaraData.="&action=roomsManagement";
		$formSaraData.="&bloque=roomsManagement";
		$formSaraData.="&idcommerce=".$commerce;
	  $formSaraData.="&bloqueGrupo=host/back";
		
		$formSaraDataEdit=$formSaraData."&optionProcess=processEdit";
		$formSaraDataEdit=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataEdit,$this->enlace);

		$formSaraDataNew.="pagina=roomsManagement";
		$formSaraDataNew.="&action=roomsManagement";
		$formSaraDataNew.="&saramodule=host";
		$formSaraDataNew.="&bloque=roomsManagement";
		$formSaraDataNew.="&idcommerce=".$commerce;
	  $formSaraDataNew.="&bloqueGrupo=host/back";
		$formSaraDataNew.="&optionProcess=processNew";
		$formSaraDataNew=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataNew,$this->enlace);
		
		$formSaraDataTypeRooms.="pagina=roomsManagement";
		$formSaraDataTypeRooms.="&bloque=roomsManagement";
		$formSaraDataTypeRooms.="&idcommerce=".$commerce;
    $formSaraDataTypeRooms.="&bloqueGrupo=host/back";
    $formSaraDataTypeRooms.="&saramodule=host";
		$formSaraDataTypeRooms.="&option=listTypeRooms";
		$formSaraDataTypeRooms.="&tema=admin";
		$formSaraDataTypeRooms=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataTypeRooms,$this->enlace);
		
		$formSaraDataDelete.="action=roomsManagement";
		$formSaraDataDelete.="&bloque=roomsManagement";
		$formSaraDataDelete.="&idcommerce=".$commerce;
	  $formSaraDataDelete.="&bloqueGrupo=host/back";
		$formSaraDataDelete.="&optionProcess=processDelete";
		$formSaraDataDelete=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataDelete,$this->enlace);
				
		include_once($this->ruta."/html/listRooms.php");
	}	
	
	
	function showListTypeRooms($currency="COP"){
	
		$variable["commerce"]=$commerce=$this->commerce;
		$variable["currency"]=$currency;//pendiente por parametrizar

		$cadena_sql=$this->sql->cadena_sql("commercebyID",$this->commerce);
		$dataCommerce=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("roomTypeListbyCommerce",$variable);
		$roomList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("priceList");
		$priceList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("typeListRoom",$variable);
		$typeListRoom=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$priceList=$this->orderArrayKeyBy($priceList,"IDTYPEROOM","SEASON","GUEST");
		
		$formSaraData="jxajax=main";
		$formSaraData.="&action=roomsManagement";
		$formSaraData.="&bloque=roomsManagement";
		$formSaraData.="&idcommerce=".$commerce;
	  $formSaraData.="&bloqueGrupo=host/back";
		
		$formSaraDataEdit=$formSaraData."&optionProcess=processEditTypeRoom";
		$formSaraDataEdit=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataEdit,$this->enlace);
				
		$formSaraDataCapacity=$formSaraData."&optionProcess=processUpdateCapacity";
		$formSaraDataCapacity=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataCapacity,$this->enlace);
		
		$formSaraDataNew="action=roomsManagement";
		$formSaraDataNew.="&bloque=roomsManagement";
		$formSaraDataNew.="&idcommerce=".$commerce;
	  $formSaraDataNew.="&bloqueGrupo=host/back";
		$formSaraDataNew.="&optionProcess=processNewTypeRoom";
		$formSaraDataNew=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataNew,$this->enlace);
		
		$formSaraDataRooms="pagina=roomsManagement";
		$formSaraDataRooms.="&bloque=roomsManagement";
		$formSaraDataRooms.="&idcommerce=".$commerce;
	  $formSaraDataRooms.="&saramodule=host";
	  $formSaraDataRooms.="&bloqueGrupo=host/back";
		$formSaraDataRooms.="&option=listRooms";
		$formSaraDataRooms.="&tema=admin";
		$formSaraDataRooms=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataRooms,$this->enlace);
		
		$formSaraDataDelete="action=roomsManagement";
		$formSaraDataDelete.="&bloque=roomsManagement";
		$formSaraDataDelete.="&idcommerce=".$commerce;
	  $formSaraDataDelete.="&bloqueGrupo=host/back";
		$formSaraDataDelete.="&optionProcess=processDeleteTypeRoom";
		$formSaraDataDelete=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataDelete,$this->enlace);
				
		include_once($this->ruta."/html/listTypeRooms.php");
	}
	

	function showNew(){

		$cadena_sql=$this->sql->cadena_sql("companyList");
		$companyList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

		$cadena_sql=$this->sql->cadena_sql("roleList");
		$roleList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

		$formSaraData="bloque=userManagement";
		$formSaraData.="&bloqueGrupo=admin";
		$formSaraData.="&action=userManagement";
		$formSaraData.="&option=processNew";
		$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar($formSaraData);

		include_once($this->ruta."/html/new.php");
	}


	function orderArrayKeyBy($array,$key,$second_key,$third_key=""){

		$newArray=array();

		if($third_key<>""){
			foreach($array as $name=>$value){
				$newArray[$value[$key]][$value[$second_key]][$value[$third_key]]=$array[$name];
			}
		}else{
			foreach($array as $name=>$value){
				$newArray[$value[$key]][$value[$second_key]]=$array[$name];
			}
		}	
		return $newArray;
	}

}