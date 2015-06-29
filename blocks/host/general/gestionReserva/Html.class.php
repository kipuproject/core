<?
include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("plugin/filter/generadorFiltros.class.php");
include_once("core/auth/Sesion.class.php");

class FronteragestionReserva{

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
		$this->rutaBloque.="/blocks/host/general/gestionReserva";
		
		$this->miInspectorHTML=InspectorHTML::singleton();
		
		$this->grupoFiltros=new generadorFiltros();
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB("people");
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
	
	
	function nuevaReserva(){
		//var_dump($_REQUEST);     
		$cadena_sql=$this->sql->cadena_sql("apiCommerceByID",$_REQUEST['tipo_reserva']);
		$api=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$api=$api[0]; 
		include_once($this->ruta."/html/formularioReservaIR.php");
	}
	
	
	
	
	function buscarTipoReserva(){
		include_once($this->ruta."/html/formularioBusquedaTipoReserva.php");
	}

	function mostrarTiposReserva(){
		include_once($this->ruta."/html/mostrarTiposReserva.php");
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
				
				$opcion=isset($_REQUEST['option'])?$_REQUEST['option']:"";
				
				switch($opcion){
					
					case 'nuevaReserva_metodo_IR':
						$this->nuevaReserva();
					break;
					case 'nuevaReserva_metodo_NP':
						$this->nuevaReserva();
					break;
					case 'formularioBasico':
						$this->formularioBasico();
					break;
					case 'mostrarTiposReserva':
						$this->mostrarTiposReserva();
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
						$this->showFriendsRooms($_REQUEST['guestBooking']);
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

	function formularioBasico(){
	/*
		$miPaginaActual=$this->miConfigurador->getVariableConfiguracion("pagina");
		
		$variable["all"]=isset($variable["all"])?$variable["all"]:"";
		$variable["commerces"]=$_REQUEST['tipo_reserva'];
		
			
		$cadena_sql=$this->sql->cadena_sql("dataCommerce",$variable);
		$dataCommerce=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$dataCommerce=$dataCommerce[0];
		
		$cadena_sql=$this->sql->cadena_sql("dataCommercePremium","");
		$dataCommercePremium=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");

		
			if($dataCommerce['ISBRANCH']=='1'){
			
				$cadena_sql=$this->sql->cadena_sql("commerceListbyCompany",$dataCommerce['ID_ESTABLECIMIENTO']);
				$commerceListbyCompany=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
				$branch=array();
				$clbc=0;
				while($commerceListbyCompany[$clbc][0]){
					$branch[]=$commerceListbyCompany[$clbc]['IDCOMMERCE'];
				$clbc++;
				}
			}
			$numbranchs=count($branch);
			if($numbranchs>1){
					$dataBranch=$this->proccessBranch($branch);
			}
		*//*
		$variable["commerce"]=$_REQUEST['tipo_reserva'];
		
		$variable["component"]='1';
		$cadena_sql=$this->sql->cadena_sql("valFiltersCommerceID",$variable);
		$tipo=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$tipo=$this->orderArrayKeyBy($tipo,'NOMBRE');
		
		$variable["component"]='2';
		$cadena_sql=$this->sql->cadena_sql("valFiltersCommerceID",$variable);
		$comida=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$comida=$this->orderArrayKeyBy($comida,'NOMBRE');

		$variable["component"]='3';
		$cadena_sql=$this->sql->cadena_sql("valFiltersCommerceID",$variable);
		$sector=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$sector=$this->orderArrayKeyBy($sector,'NOMBRE');
		
		$variable["component"]='4';
		$cadena_sql=$this->sql->cadena_sql("valFiltersCommerceID",$variable);
		$precio=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$precio=$this->orderArrayKeyBy($precio,'NOMBRE');

		$variable["component"]='7';
		$cadena_sql=$this->sql->cadena_sql("valFiltersCommerceID",$variable);
		$servicios=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
		$servicios=$this->orderArrayKeyBy($servicios,'NOMBRE');
		*/
		
		include_once($this->ruta."/html/formularioBasico.php");
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
?>
