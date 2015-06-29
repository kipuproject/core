<?php
include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("core/auth/Sesion.class.php");

class FronteraPayu{

	var $ruta;
	var $sql;
	var $funcion;
	var $lenguaje;
	var $formulario;
	
	var $miConfigurador;
	
	function __construct(){
	
		$this->miConfigurador=Configurador::singleton();
		$this->miSesion=Sesion::singleton();
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
		$this->rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
		$this->rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");
		$this->rutaTema.=$this->rutaBloque."/theme/default";
		$this->rutaBloque.="/blocks/host/general/payu";
		$this->miInspectorHTML=InspectorHTML::singleton();
		$this->masterResource=$this->miConfigurador->fabricaConexiones->getRecursoDB("master");
		$this->rutaURL=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site");
		$this->enlace=$this->rutaURL."?".$this->miConfigurador->getVariableConfiguracion("enlace");
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

	function html(){
			$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);
			
			if(isset($_REQUEST['extra2']) && $_REQUEST['extra2']=='payu-check-in'){
				$this->payuResponse($_REQUEST);
			}
	}

	function payuResponse($variable){
		include_once("plugin/payulatam/payulatam.class.php");
		$payment= new payuLatam();
		echo "<br/><h1>".$payment->payuResponse($variable)."</h1></br>";
		//include_once($this->ruta."/html/payuResponse.php");
	}	

}