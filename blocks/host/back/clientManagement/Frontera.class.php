<?php
include_once("core/manager/Configurador.class.php");
include_once("core/auth/Sesion.class.php");

class FronteraclientManagement{

	var $ruta;
	var $sql;
	var $funcion;
	var $lenguaje;
	var $formulario;
	var $enlace;
	var $miConfigurador;
	
	function __construct(){
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

	function setSql($a){
		$this->sql=$a;
	}

	function setFuncion($funcion){
		$this->funcion=$funcion;
	}

	function getUrlLinksbyId($id){

		$formSaraData="pagina=clientManagement";
 		$formSaraData.="&saramodule=host";
		$formSaraData.="&optionValue=".$id;
		$formSaraData.="&option=edit";
		$link['edit']=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);

		$formSaraData="bloque=clientManagement";
		$formSaraData.="&bloqueGrupo=host/back";
		$formSaraData.="&action=clientManagement";
		$formSaraData.="&option=processDelete";
		$formSaraData.="&optionValue=".$id;
		$link['delete']=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);
	
		return $link;
	}

	function html(){
		
		include_once("core/builder/FormularioHtml.class.php");
		
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
		$this->miFormulario=new formularioHtml();
		$option=isset($_REQUEST['option'])?$_REQUEST['option']:"list";
		
		switch($option){
			case "list":
				$this->showList();
				break;
			case "new":
				$this->showNew();
				break;
			case "edit":
				$this->showEdit($_REQUEST['optionValue']);
				break;
			case "view":
				$this->showView($_REQUEST['optionValue']);
				break;
		}
	}
	
	function showEdit($id){

		$cadena_sql=$this->sql->cadena_sql("dataByID",$id);
		$dataByID=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$dataByID=$dataByID[0];

		$formSaraData="bloque=clientManagement";
		$formSaraData.="&bloqueGrupo=host/back";
		$formSaraData.="&action=clientManagement"; 
		$formSaraData.="&option=processEdit";
		$formSaraData.="&saramodule=host"; 
		$formSaraData.="&optionValue=".$id;
		$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar($formSaraData);

		include_once($this->ruta."/html/edit.php");
	}

	function showView($id){

		$cadena_sql=$this->sql->cadena_sql("userListByID",$id);
		$userDataByID=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$userDataByID=$userDataByID[0];

		$cadena_sql=$this->sql->cadena_sql("companyList");
		$companyList=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");

		$cadena_sql=$this->sql->cadena_sql("roleList");
		$roleList=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");

		$link=$this->getUrlLinksbyId($id);

		include_once($this->ruta."/html/view.php");
	}

	function showList(){

		$formSaraDataNew="pagina=clientManagement";
		$formSaraDataNew.="&option=new";
		$formSaraDataNew.="&saramodule=host";
		$formSaraDataNew=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataNew,$this->enlace);
		
		$formSaraData="jxajax=main";
		$formSaraData.="&pagina=clientManagement";
		$formSaraData.="&action=clientManagement";
		$formSaraData.="&bloque=clientManagement";
	  $formSaraData.="&bloqueGrupo=host/back";
		$formSaraData.="&option=new";
		$formSaraData.="&saramodule=host";
	  $linkUserNew=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);

		$cadena_sql=$this->sql->cadena_sql("dataList"); 
		$dataList=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");

		include_once($this->ruta."/html/list.php");
	}

	function orderArrayKeyBy($array,$key){
		$newArray=array();
		foreach($array as $name=>$value){
			$newArray[$value[$key]]=$array[$name];
		}
		return $newArray;
	}

}