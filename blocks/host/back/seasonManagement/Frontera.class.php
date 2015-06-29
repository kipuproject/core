<?php
include_once("core/manager/Configurador.class.php");
include_once("core/auth/Sesion.class.php");

class FronteraseasonManagement{

	var $ruta;
	var $sql;
	var $funcion;
	var $lenguaje;
	var $formulario;
	
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

	function frontera(){
		$this->html();
	}

	function setSql($a){
		$this->sql=$a;

	}

	function setFuncion($funcion){
		$this->funcion=$funcion;
	}

	function html(){
		
		include_once("core/builder/FormularioHtml.class.php");
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
		$this->miFormulario=new formularioHtml();
		$option=isset($_REQUEST['optionFormSearch'])?$_REQUEST['optionFormSearch']:"view";

		switch($option){
			case "view":
				$this->showView();
			break;
		}

	}

	function showView(){

		$variable['commerce']=$commerce=$this->commerce;
		
		$cadena_sql=$this->sql->cadena_sql("searchSeason",$variable);
		$season=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$season=$this->orderArrayKeyBy($season,"IDSEASON");
	
		$cadena_sql=$this->sql->cadena_sql("searchAllDays",$variable);
		$allDays=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$allDays=$this->orderArrayKeyBy($allDays,"TIMEDAY");
		
		$rutaTema=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."/theme/".$this->miConfigurador->getVariableConfiguracion("tema");

		$formSaraData="jxajax=main";
		$formSaraData.="&bloque=seasonManagement";
		$formSaraData.="&bloqueGrupo=host/back";
		$formSaraData.="&action=seasonManagement";
		$formSaraData.="&option=updateSeason";
		$formSaraData.="&commerce=".$commerce;
		$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);
 
		$formSaraDataUrl.="pagina=seasonManagement";
		$formSaraDataUrl.="&saramodule=host";
		$formSaraDataUrl.="&commerce=".$commerce; 
		$formSaraDataUrl=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataUrl,$this->enlace);

		$year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y"); 
		$diaActual=date("j"); 

		include_once($this->ruta."/html/view.php");
	}
	
	
	function orderArrayKeyBy($array,$key){
		$newArray=array();
		foreach($array as $name=>$value){
			$newArray[$value[$key]]=$array[$name];
		}
		return $newArray;
	}

}