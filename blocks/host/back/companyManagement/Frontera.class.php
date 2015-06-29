<?
include_once("core/manager/Configurador.class.php");
include_once("core/auth/Sesion.class.php");
include_once("plugin/filter/generadorFiltros.class.php");
include_once("core/builder/WidgetHtml.class.php");

class FronteracompanyManagement{

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
		$this->enlace=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."?".$this->miConfigurador->getVariableConfiguracion("enlace");
		$this->miSesion=Sesion::singleton();
		$conexion=$this->miSesion->getValorSesion('dbms');
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);		
		$this->idSesion=$this->miSesion->getValorSesion('idUsuario');
		$this->commerce=$this->miSesion->getValorSesion('commerce');
		$this->masterResource=$this->miConfigurador->fabricaConexiones->getRecursoDB("master");		

		if($this->idSesion==""){
			echo "Sesion cerrada<br/>";
		}
		
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

	function getUrlLinksbyId($id){

		$formSaraData="pagina=companyManagement";
		$formSaraData.="&optionValue=".$id;

		$option="&option=edit";
		$link['edit']=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData.$option,$this->enlace);

	
		$option="&option=view";
		$link['view']=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData.$option,$this->enlace);
		

		$formSaraData="jxajax=main";
		$formSaraData.="&action=companyManagement";
		$formSaraData.="&bloque=companyManagement";
	   	$formSaraData.="&bloqueGrupo=people/back";
		$formSaraData.="&optionProcess=processDelete";
		$formSaraData.="&optionValue=".$id;
		$link['delete']=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);
		

		$formSaraData="pagina=companyManagement";
		$formSaraData.="&bloque=companyManagement";
		$formSaraData.="&tema=admin";
		$formSaraData.="&bloqueGrupo=people/back";
		$formSaraData.="&optionProcess=list";
		$link['postDelete']=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);
		
		return $link;
	}




	function html(){
		
		include_once("core/builder/FormularioHtml.class.php");
		
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");

		$this->miFormulario=new formularioHtml();

		$option=isset($_REQUEST['option'])?$_REQUEST['option']:"edit";

		switch($option){
			case "list":
				$this->showList();
				break;
			case "new":
				$this->showNew();
				break;
			case "edit":
				$this->showEdit($this->commerce);
				break;
			case "view":
				$this->showEdit($this->commerce);
				break;
			case "addCommerce":

				$reload=isset($_REQUEST['reload'])?$_REQUEST['reload']:"";
				$this->showNewCommerce($_REQUEST['idCompany'],$_REQUEST['commercetype'],$reload);
				
				
				break;
		}
		
	}
	

	/**
	* Consulta el listado de establecimientos asociados a cada usuario tambien denominado nivel de acceos 
	*/
	function companyByUser(){

		$cadena_sql=$this->sql->cadena_sql("companyByUser",$this->idSesion);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

		$cadena_sql=$this->sql->cadena_sql("companyListAll");
		$allCompanies=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");


		$this->companies=array();      
		

		$i=0;
		while(isset($result[$i]['IDCOMPANY'])){

			$this->companyByParent($result[$i]['IDCOMPANY'],$allCompanies);

			$i++;
		}

		return $this->companies;
	}


	function companyByParent($parent,$allCompanies) {
		
		/*$cadena_sql=$this->sql->cadena_sql("companyList",$parent);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");*/
		$allCompaniesOrderByParent=$this->orderArrayKeyBy($allCompanies,"IDPARENT");

		//si el id tiene hijos asociados recorremos sus hijos
		if(array_key_exists($parent,$allCompaniesOrderByParent)){

			foreach($allCompaniesOrderByParent[$parent] as $key=>$value){
				$this->companyByParent($value['IDCOMPANY'],$allCompanies);
			}
		
		}
		/*if(is_array($result)){

			$i=0;
			while(isset($result[$i]['IDCOMPANY'])){

				$this->companyByParent($result[$i]['IDCOMPANY'],$allCompanies);
			
				$i++;
			}
		
		}*/else{
		
			//si el id no tiene hijos entonces lo agregamos a las empresas o establecimientos q pertenecen al usuario			
			$this->companies[]=$parent;
	
		}


	}	
    
    
    
	function loadFiltersByCommerce($type,$idcommerce) {

		$filter=new generadorFiltros();
		$component1=$filter->filterComponentList('GTR_HOTELES');
		$component2=$filter->filterComponentList('GTR_UBICACION');
		$component=array_merge($component1,$component2);
		 
		$component=$this->orderArrayKeyBy($component,"NOMBRE_COMPONENTE");

		$cadena_sql=$this->sql->cadena_sql("commerceFilterList",$idcommerce);
		$commerce=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
 		$commerce=$this->orderArrayKeyBy($commerce,"IDOPTION");
 

		//se rescatan las opciones listado principal  si la opcion la encontramos las opciones asociadas al comercio
		//marcamos la opcion como true 

		foreach($component as $keyComponent=>$option){
			foreach($option as $keyOption=>$value){

				if (array_key_exists($value["ID_OPCION"],$commerce)){

				 	$component[$keyComponent][$keyOption]["CHECKED"]="true";	

				}else{

				 	$component[$keyComponent][$keyOption]["CHECKED"]="false";	

				}
			}
		}
	  return $component;

	} 


	function showEdit($id){

		$imgs=new WidgetHtml();
		
		$cadena_sql=$this->sql->cadena_sql("commercebyID",$id);
		$commerce=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");  
	     		
		 
		$formSaraDataAddCommerce="bloque=companyManagement";
		$formSaraDataAddCommerce.="&bloqueGrupo=host/back";
		$formSaraDataAddCommerce.="&pagina=companyManagement";
		$formSaraDataAddCommerce.="&jxajax=companyManagement";
		$formSaraDataAddCommerce.="&option=addCommerce";
		$formSaraDataAddCommerce=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataAddCommerce,$this->enlace);

		
		$formSaraDataCompany="bloque=companyManagement";
		$formSaraDataCompany.="&pagina=companyManagement";
		$formSaraDataCompany.="&bloqueGrupo=host/back";
		$formSaraDataCompany.="&jxajax=main";
		$formSaraDataCompany.="&saramodule=host"; 
		$formSaraDataCompany.="&action=companyManagement";
		$formSaraDataCompany.="&optionProcess=processEditCompany";
		$formSaraDataCompany=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataCompany,$this->enlace);
	       

		$formSaraDataCommerce="bloque=companyManagement";
		$formSaraDataCommerce.="&bloqueGrupo=host/back";
		$formSaraDataCommerce.="&jxajax=main";
		$formSaraDataCommerce.="&pagina=companyManagement";
		$formSaraDataCommerce.="&action=companyManagement";
		$formSaraDataCommerce.="&optionProcess=processEditCommerce";
		$formSaraDataCommerce=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataCommerce,$this->enlace);
	        
		$formSaraDataCommerceAction="bloque=companyManagement";
		$formSaraDataCommerceAction.="&pagina=companyManagement";
		$formSaraDataCommerceAction.="&bloqueGrupo=host/back";
		$formSaraDataCommerceAction.="&saramodule=host"; 
		$formSaraDataCommerceAction.="&action=companyManagement";
		$formSaraDataCommerceAction.="&optionProcess=processEditCommerce";
		$formSaraDataCommerceAction=$this->miConfigurador->fabricaConexiones->crypto->codificar($formSaraDataCommerceAction);
	        

		include_once($this->ruta."/html/edit.php");
	}


	function showView($id){

		$cadena_sql=$this->sql->cadena_sql("companyListbyID",$id);
		$company=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$company=$company[0];


		include_once($this->ruta."/html/view.php");
	}
	
	function showNewCommerce($idCompany,$typeCommerce,$reload){

		/*$cadena_sql=$this->sql->cadena_sql("companyListbyID",$id);
		$company=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$company=$company[0];*/

		$formSaraData="bloque=companyManagement";
		$formSaraData.="&bloqueGrupo=host/back";
		$formSaraData.="&jxajax=main";
		$formSaraData.="&action=companyManagement";
		$formSaraData.="&optionProcess=processNewCommerce";
		$formSaraData.="&idCompany=".$idCompany;
		$formSaraData.="&typeCommerce=".$typeCommerce;
		$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);

		$time=time();
		
		include_once($this->ruta."/html/newCommerce.php");

	}
	
	
	function showList(){

		$cadena_sql=$this->sql->cadena_sql("companyListbyID",implode(",",$this->companies));
		$companyList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

		include_once($this->ruta."/html/list.php");
	}

	function showNew(){

		$cadena_sql=$this->sql->cadena_sql("companyList");
		$companyList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

		$cadena_sql=$this->sql->cadena_sql("roleList");
		$roleList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("categoryListCommerce");
		$categoryListCommerce=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");		
		

		$formSaraDataCompany="bloque=companyManagement";
		$formSaraDataCompany.="&bloqueGrupo=people/back";
		$formSaraDataCompany.="&jxajax=main";
		$formSaraDataCompany.="&action=companyManagement";
		$formSaraDataCompany.="&optionProcess=processNewCompany";
		$formSaraDataCompany=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataCompany,$this->enlace);

		
		$formSaraDataAddCommerce="bloque=companyManagement";
		$formSaraDataAddCommerce.="&bloqueGrupo=people/back";
		$formSaraDataAddCommerce.="&jxajax=companyManagement";
		$formSaraDataAddCommerce.="&option=addCommerce";
		$formSaraDataAddCommerce=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataAddCommerce,$this->enlace);


		
		include_once($this->ruta."/html/new.php");
	}


	function orderArrayKeyBy($array,$key){

		$newArray=array();

		foreach($array as $name=>$value){
			$newArray[$value[$key]][]=$array[$name];
		}
		/*echo "<pre>";
		var_dump($newArray);
		echo "</pre>";*/
		return $newArray;
	}






}
?>
