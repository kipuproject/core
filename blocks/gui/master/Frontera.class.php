<?php
include_once("core/manager/Configurador.class.php");
include_once("core/auth/Sesion.class.php");

class Fronteramaster{

	var $ruta;
	var $sql;
	var $funcion;
	var $lenguaje;
	var $formulario;
	var $enlace;
	var $miConfigurador;
	
	function __construct()
	{
		$this->miSesion=Sesion::singleton();
		$this->miConfigurador=Configurador::singleton();
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB("master");
		$this->enlace=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."?".$this->miConfigurador->getVariableConfiguracion("enlace");
		$this->id_usuario=$this->miSesion->getValorSesion('idUsuario');
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
		$this->idUser=$this->miSesion->getValorSesion('idUsuario');
		$this->showMenu();
		
	}
	
	
	function showMenu(){
			
		//1. Rescato unicamente los comercios permitidos del usuario
		//	 con su respectivo tipo 
		
			$cadena_sql=$this->sql->cadena_sql("userByID",$this->idUser);
			$user=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
			$cadena_sql=$this->sql->cadena_sql("commerceByUser",$this->idUser);
			$commerceList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
			$total=count($commerceList);
		
		//2. Traigo listado de tipos de comercio
		
			$cadena_sql=$this->sql->cadena_sql("commerceTypes",$this->idUser);
			$commerceTypes=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
			$commerceTypes=$this->orderArrayKeyBy($commerceTypes,'IDTYPE',TRUE);
		
		//3. Separo comercios dependiendo del tipo
		
			$commercesByType=$this->orderArrayKeyBy($commerceList,'TYPECOMMERCE');
			/*echo "<pre>";
			var_dump($commercesByType); 
			echo "</pre>";*/
			
		//4. Verifico si existe alguna peticion de tipo de comercio o si esta almacenado en sesion 
		
			$typeSession=$this->miSesion->getValorSesion('typecommerce');
			
			if(isset($_REQUEST['typecommerce'])){
				$currentType=$_REQUEST['typecommerce'];
			}elseif($typeSession<>""){  
				$currentType=$typeSession;
			}else{
				$currentType=$commerceList[0]['TYPECOMMERCE'];
			}
		
		
		//5.Filtro comercios de acuerdo al actual tipo
		// y los tipos de acuerdo a los tipos permitidos
		
			$commerceList=$commercesByType[$currentType];
			$typesList=array_keys($commercesByType);

		//6.Verifico si existe algun comercio almacenado en sesion 
		
			$commerce=$this->miSesion->getValorSesion('commerce');

		
			if(!isset($_REQUEST['commerce']) || $_REQUEST['commerce']==""){
				$_REQUEST['commerce']=$commerceList[0]['IDCOMMERCE'];
			}
		
		if($this->miSesion->getValorSesion('dbms')==""){
		
			$this->miSesion->guardarValorSesion('commerce',$_REQUEST['commerce']);  
			$this->miSesion->guardarValorSesion('typecommerce',$currentType);   
			$this->miSesion->guardarValorSesion('dbms',$commerceList[0]['DBMS']);
			 	
			//echo "<script>location.reload()</script>";	
			 
		}
	
		if(!isset($_REQUEST['saramodule'])){

			$formSaraData="pagina=".$this->miConfigurador->getVariableConfiguracion("pagina");
			$formSaraData.="&saramodule=".$commerceList[0]['DBMS']; 
			$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);
			echo "<script>location.replace('".$formSaraData."')</script>";
		}
		  
		$formSaraDataCommerce="bloque=master";  
		$formSaraDataCommerce.="&bloqueGrupo=gui";
		$formSaraDataCommerce.="&currentPage=".$this->miConfigurador->getVariableConfiguracion("pagina");
		$formSaraDataCommerce.="&currentModule=".$this->miConfigurador->getVariableConfiguracion("module");
		$formSaraDataCommerce.="&action=master";
		$formSaraDataCommerce=$this->miConfigurador->fabricaConexiones->crypto->codificar($formSaraDataCommerce);

		/*
		$menuList=$this->orderArrayKeyBy($menuList,"PADRE");

		$cadena_sql=$this->sql->cadena_sql("roleList");
		$roleList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		*/
		$linkEnd=$userName=$rutaTema="";
		
		if($total>1){
			include_once($this->ruta."/html/menu.php");
		}	
	}


	function makeURL($param,$page){

		$formSaraData="pagina=".$page;
		$formSaraData.="&";
		$formSaraData.=$param;
		$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);

		return $formSaraData;

	}

	//Unique True: Cuando la key solo le pertenece a un registro
	function orderArrayKeyBy($array,$key,$unique=FALSE){

		$newArray=array();

		foreach($array as $name=>$value){
			if($unique===TRUE){
				$newArray[$value[$key]]=$array[$name];
			}else{
				$newArray[$value[$key]][]=$array[$name];
			}
		}

		return $newArray;
	}
}
?>