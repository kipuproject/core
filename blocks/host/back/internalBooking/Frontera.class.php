<?php
include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("plugin/filter/generadorFiltros.class.php");
include_once("core/auth/Sesion.class.php");

class FronterainternalBooking{

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
		$this->rutaBloque.="/blocks/general/gestionReserva";
		$this->miInspectorHTML=InspectorHTML::singleton();
		$this->grupoFiltros=new generadorFiltros();
		$conexion=$this->miSesion->getValorSesion('dbms');
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);		
		$this->masterResource=$this->miConfigurador->fabricaConexiones->getRecursoDB("master");		
		$this->commerce=$this->miSesion->getValorSesion('commerce');
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

	function showInfoRooms($dataCommerce,$item){
		
		$folder=opendir($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/".$dataCommerce['FOLDER']."/".$item['MACHINE_NAME']);
		
		$images=array();
		
		while ($file = readdir($folder)){
			if (!is_dir($file)){
				$images[]=$file;
			}
		}
		
		include_once($this->ruta."/html/infoRoom.php");
	}
	
	function showFriendsRooms($num){
		include_once($this->ruta."/html/infoFriends.php");
	}
	
	function showDataCustomer($customer){
	
		$cadena_sql=$this->sql->cadena_sql("dataUserByIden",$customer);
		$dataUser=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");	
		
		include_once($this->ruta."/html/infoCustomer.php");
	}
	
	function nuevaReserva(){
		
		$idUser=$this->miSesion->getValorSesion("idUsuario");

		$variable["commerce"]=$this->commerce;

		$cadena_sql=$this->sql->cadena_sql("dataItems",$variable);
		$dataItems=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

		$formSaraData="jxajax=main";		
		$formSaraData.="&bloque=internalBooking";
		$formSaraData.="&bloqueGrupo=host/back";
		$formSaraData.="&action=gestionReserva";
		$formSaraData.="&option=procesarReservaIR";
		$formSaraData.="&commerce=".$this->commerce; 

		$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);
		
		$formSaraDataHtmlFriend="jxajax=main";		
		$formSaraDataHtmlFriend.="&pagina=internalBooking";
		$formSaraDataHtmlFriend.="&bloque=internalBooking";
		$formSaraDataHtmlFriend.="&bloqueGrupo=host/back";
		$formSaraDataHtmlFriend.="&option=showRoomFriends";
		$formSaraDataHtmlFriend.="&commerce=".$this->commerce; //id del comercio asociado a la empresa

		$formSaraDataHtmlFriend=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataHtmlFriend,$this->enlace);

		$formSaraDataHtmlCustomer="jxajax=main";		
		$formSaraDataHtmlCustomer.="&pagina=internalBooking";
		$formSaraDataHtmlCustomer.="&bloque=internalBooking";
		$formSaraDataHtmlCustomer.="&bloqueGrupo=host/back";
		$formSaraDataHtmlCustomer.="&option=showDataCustomer";
		
		$formSaraDataHtmlCustomer=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataHtmlCustomer,$this->enlace);

		$formSaraDataAction="bloque=internalBooking";
		$formSaraDataAction.="&action=internalBooking";
		$formSaraDataAction.="&bloqueGrupo=host/back";
		$formSaraDataAction.="&jxajax=main";
		$formSaraDataAction.="&option=procesarReserva";
		$formSaraDataAction.="&commerce=".$this->commerce;

		$formSaraDataAction=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataAction,$this->enlace);
		
		include_once($this->ruta."/script/action.php"); //este archivo contiene los procedmientos jquery
		include_once($this->ruta."/html/formularioReserva.php");
	}
	
	
	function setSql($a){
		$this->sql=$a;
	}

	function setFuncion($funcion){
		$this->funcion=$funcion;
	}

	function html(){
	
			$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);
			
			//var_dump($_REQUEST);
			if(isset($_REQUEST['extra2']) && $_REQUEST['extra2']=='payu-check-in'){
				$this->payuResponse($_REQUEST);
			}
				
      $opcion=isset($_REQUEST['option'])?$_REQUEST['option']:"new";
      
      switch($opcion){
        
        case 'new':
          $this->nuevaReserva();
        break;
        case 'showRoomDetails':
          $cadena_sql=$this->sql->cadena_sql("dataCommerce",$_REQUEST['commerce']);
          $dataCommerce=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
          $dataCommerce=$dataCommerce[0];
          
          $variable["commerce"]=$_REQUEST['commerce'];
          $variable["group"]=$_REQUEST['optionValue'];

          $cadena_sql=$this->sql->cadena_sql("dataGroupReservable",$variable);
          $group=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

          $this->showInfoRooms($dataCommerce,$group[0]);
        break; 
        case 'showRoomFriends':
          $this->showFriendsRooms(($_REQUEST['guestBooking'])+($_REQUEST['kids']));
        break; 
        case 'showDataCustomer':
          $this->showDataCustomer($_REQUEST['idCustomer']);
        break; 
        case 'showRoomAvailability':
          $variable["commerce"]=$_REQUEST['commerce'];
          $variable["group"]=$_REQUEST['optionValue'];
          $variable['guest']=$_REQUEST['guestBooking'];

          $cadena_sql=$this->sql->cadena_sql("dataRoomAvailability",$variable);
          $availability=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
          
          if(is_array($availability)){
            $i=0;
            while(isset($availability[$i][0])){
          
              echo '<option value="'.$availability[$i]['IDGROUP'].'" >'.$availability[$i]['NAME'].'</option>';
          
            $i++;
            }
          }else{
            echo "FALSE";
        
          }
        break; 
        default:
          $this->buscarTipoReserva();
        break;			 
      }
	}

	function payuResponse($variable){
		
		include_once("plugin/payulatam/payulatam.class.php");
		$payment= new payuLatam();
		echo "<br/><h1>".$payment->payuResponse($variable)."</h1></br>";
		//include_once($this->ruta."/html/payuResponse.php");
	}	


	function getUserId(){

		if($this->miSesion->getValorSesion('idUsuario')<>""){
			$id=$this->miSesion->getValorSesion('idUsuario');
		}else{
			$id=0; //0 ES POR DEFECTO EL USUARIO ANONIMO
		}
		
		return $id;
	}	

	function orderArrayKeyBy($array,$key){
		$newArray=array();
		foreach($array as $name=>$value){
			$newArray[$value[$key]]=$array[$name];
		}
		return $newArray;
	}

}