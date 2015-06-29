<?php
if(!isset($GLOBALS["autorizado"])){
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("plugin/mail/class.phpmailer.php");
include_once("plugin/mail/class.smtp.php");
include_once("core/builder/Acceso.class.php");

class ApigestionReserva{

	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $error;
	var $miRecursoDB;
	var $crypto;
	var $mensaje;
	var $status="";

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		$this->miInspectorHTML=InspectorHTML::singleton();
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
    $this->rutaURL=$this->miConfigurador->getVariableConfiguracion("host");
		$this->rutaURL.=$this->miConfigurador->getVariableConfiguracion("site");
	  $this->Access=Acceso::singleton();
	  $conexion="master";
	  $this->master_resource=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
	}

	public function setRuta($unaRuta){
		$this->ruta=$unaRuta;
	}

	public function setSql($a){
		$this->sql=$a;
	}

	public function setFuncion($funcion){
		$this->funcion=$funcion;
	}

	public function setLenguaje($lenguaje){
		$this->lenguaje=$lenguaje;
	}

	public function setFormulario($formulario){
		$this->formulario=$formulario;
	}
	
	public function process(){
	
		if(!isset($_REQUEST['key'])){
			echo "error";
			exit;
		}else{ 
			$cadena_sql=$this->sql->cadena_sql("api_key",$_REQUEST['key']);
			$commerce=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
			$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($commerce[0]['DBMS']);
			$this->commerce=$commerce[0]['IDCOMMERCE'];   
			//$this->commerce=1;  //FATAL ERROR!!!!!!!!!!!!!!
			$this->commerce_folder=$commerce[0]['FOLDER']; 
		}
		
		$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);
		$_REQUEST=$this->miInspectorHTML->limpiarSQL($_REQUEST);
		
		unset($_REQUEST['aplicativo']);
		unset($_REQUEST['PHPSESSID']);
		
		foreach($_REQUEST as $key=>$value){
			$_REQUEST[urldecode($key)]=urldecode($value);
		}
		
		if(isset($_REQUEST['method'])){
			
			switch($_REQUEST['method']){
				case 'getSession': 
					$result=$this->createBDSession($_REQUEST);
				break;
				case 'rooms':
					$result=$this->getRooms($_REQUEST);
				break;
				case 'validate':
					$result=$this->validate($_REQUEST);
				break;
				case 'saveMainGuest':
					$result=$this->saveMainGuest($_REQUEST);
				break;				
				case 'value':
					$result=$this->calculateValue($_REQUEST['id']);
				break;				
				case 'listCommerces':
					$result=$this->getListCommerces($_REQUEST);
				break;				
				case 'saveGuest':
					$result=$this->saveGuest($_REQUEST);
				break;				
				case 'dataCurrentBooking':
					$result=$this->getDataBookingBySession($_REQUEST);
				break;			
				case 'dataCommerce':
					$result=$this->getDataCommerceByID($_REQUEST['id']);
				break;				
				case 'confirmBooking':
					$result=$this->confirmBooking($_REQUEST);
				break;					
				case 'additionalFields':
					$result=$this->getAdditionalFields($_REQUEST);
				break;				
				case 'sendEmail':
					$result=$this->sendEmail($_REQUEST['id']);
				break;
			}
			
			$json=json_encode($result);
			if(isset($_GET['callback'])){
				echo "{$_GET['callback']}($json)";
			}else{
				echo $json;
			}
 		}else{ 
				echo "no data";
		}
	}
	
	private function confirmBooking($variable){
		$response=$this->sessionValidate($variable);
		if($response->status_code==205){
			return $response;
		}
		if(isset($variable['observation'])){
			if(strlen($variable['observation'])>254){
				$response->message="Tu observación no debe tener mas 255 carácteres";
				$response->status_code=201;  
				$response->status="false";
				return $response;
			}
		}
		$cadena_sql=$this->sql->cadena_sql("dataBookingBySession",$variable['session']);
		$book=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("confirmBooking",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
		
		if($this->miRecursoDB->registros_afectados()>0){
			$response->status_code=200;  
			$response->status="true";
			$this->sendEmail($book[0]['IDBOOKING']);  
		}else{
			$response->message="Sesión expirada";
			$response->status_code=201;  
			$response->status="false";
		}
		return $response;
	
	}  
	
	private function sessionValidate($variable){
		
		$response=new stdClass();
		
		if(!isset($variable['session']) || $variable['session']==""){ 
			$response->message="Invalid Session ";
			$response->status="false"; 
			return $response;
		}
		$cadena_sql=$this->sql->cadena_sql("getAllSession",$variable['session']);
		$session=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		
		if(!is_array($session)){ 
			$response->message="No fue posible rescatar una sesión. Prueba recargando la página ";
			$response->status_code=205;  
			$response->status="false";
			return $response;
		}
		
		$response->status_code=200;  
		$response->status="true";
		return $response;
		
	}
	
	private function getDataBookingBySession($variable){
	
		//Inicio Validaciones Sesion
		$response=$this->sessionValidate($variable);
		if($response->status_code==205){
			return $response;
		}

		//booking
		$cadena_sql=$this->sql->cadena_sql("dataBookingBySession",$variable['session']);
		$data=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){ 
			$response->message="Error de Datos (1). Prueba recargando la página ";
			$response->status_code=201;   
			$response->status="false";
			return $response;
		}else{
			$idbooking=$data[0]['IDBOOKING'];		
			$idcommerce=$data[0]['COMMERCE'];		
			$mainguest=$data[0]['CLIENT'];	
			$response->booking=$data[0]; 
		}
		
    //reservables
    
    $cadena_sql=$this->sql->cadena_sql("dataBookingItems",$idbooking); 
		$reservables=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    
    $response->reservables=$reservables[0]; //provisionalmente solo retorna un reservable
    
    
		//mainguest
		$cadena_sql=$this->sql->cadena_sql("dataUserById",$mainguest);
		$data=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){  
			$response->message="Error de Datos (2). Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false";
			return $response;
		}else{
			$response->responsible=$data[0]; 		
		}
		
		//commerce
		$cadena_sql=$this->sql->cadena_sql("dataCommerceByID",$idcommerce);
		$data=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){  
			$response->message="Error de Datos (3). Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false"; 
			return $response;
		}else{
			$response->commerce=$data[0]; 		
		}	
		
		//room
		$cadena_sql=$this->sql->cadena_sql("dataRoomBookingbyID",$idbooking);
		$data=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){  
			$response->message="Error de Datos (4). Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false";
			return $response;
		}else{
			$response->room=$data[0]; 		
		}
	
		$response->status_code=200;  
		$response->status="true"; 
		
		return $response;				
	}
  
  private function saveOthersFields($variable){
    $otherfields =array();
    foreach($variable as $key=>$value){
      $tmp=explode("_",$key);
      if($tmp[0]=="fieldbooking"){
        $otherfields[$tmp[1]]=$value;
      }
    }
    $cadena_sql=$this->sql->cadena_sql("dataBookingBySession",$variable['session']);
    $dataBooking=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    $dataBooking=$dataBooking[0]; 
        
    foreach($otherfields as $key=>$value){
        $cadena_sql=$this->sql->cadena_sql("insertOtherFields",array("idbooking"=>$dataBooking['IDBOOKING'],"idfield"=>$key,"value"=>$value));
				$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
		}
  }
	
	private function saveOthersGuest($variable){
	
    $friends=array();
    foreach($variable as $key=>$value){
      $friend=explode("-",$key);
      $friends[$friend[0]][$friend[1]]=$value;
    }
    
    foreach($friends as $key=>$value){
    
        $cadena_sql=$this->sql->cadena_sql("dataBookingBySession",$variable['session']);
        $dataBooking=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
        $dataBooking=$dataBooking[0];
          
        if($value['dni']<>""){
          $cadena_sql=$this->sql->cadena_sql("dataGuestByIden",$value['dni']);
          $dataUserFriend=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
        }	
         
        if(is_array($dataUserFriend)){
        
          $cadena_sql=$this->sql->cadena_sql("insertFriend",array($dataBooking['IDBOOKING'],$dataUserFriend[0]['USUARIOID']));
          $result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
         
        }elseif($value['name']<>"" ){
        
          $cadena_sql=$this->sql->cadena_sql("createFriend",$value);
          $result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
          
          $cadena_sql=$this->sql->cadena_sql("insertFriend",array($dataBooking['IDBOOKING'],$this->miRecursoDB->ultimo_insertado()));
          $result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
        }
    }
	}
	
	private function saveMainGuest($variable){
	
		if($variable['email']==""){
			return "el email del usuario responsable es obligatorio";
		}
		 
		$cadena_sql=$this->sql->cadena_sql("dataUserByEmail",$variable['email']);
		$dataUser=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		$dataUser=$dataUser[0];
		
			//si existe actualizo el cliente de la reserva
		if(is_array($dataUser)){
		
			$variable['user']=$dataUser['USERID'];
			$cadena_sql=$this->sql->cadena_sql("updateUserBooking",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			 
			$cadena_sql=$this->sql->cadena_sql("updateDataUser",$variable);
			$result=$this->master_resource->ejecutarAcceso($cadena_sql,"");
			
		}else{
			//crear usuario con acceso al sistema
			$cadena_sql=$this->sql->cadena_sql("insertUser",$variable);
			$result=$this->master_resource->ejecutarAcceso($cadena_sql,"");
			
			if($result){
				$variable['user']=$this->master_resource->ultimo_insertado();
				$cadena_sql=$this->sql->cadena_sql("updateUserBooking",$variable);
        $result=$this->master_resource->ejecutarAcceso($cadena_sql,"");
        
        $cadena_sql=$this->sql->cadena_sql("insertRole",$variable); 
        $result=$this->master_resource->ejecutarAcceso($cadena_sql,"");
			}	
		}
 		return true;
	}
	
	private function createBDSession(){
		$response=new stdClass();
		$fecha=explode (" ",microtime());
		$sesionId=md5($this->fecha[1].substr($this->fecha[0],2).rand());
		$response->sesionId=$sesionId; 
		$cadena_sql=$this->sql->cadena_sql("setBookingSession",$sesionId);
		$session=$this->master_resource->ejecutarAcceso($cadena_sql,""); 
		if($session){
			$response->status_code=200; 
		}
		
		return $response;
	}
	
	private function sendEmail($idbooking){
		
		//booking
		$response=new stdClass();
		$this->mail=new phpmailer();
		   
		$cadena_sql=$this->sql->cadena_sql("dataBookingByID",$idbooking);
		$data=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){ 
			$response->message="Error de Datos. Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false";
			return $response;
		}else{
			$idbooking=$data[0]['IDBOOKING'];		
			$idcommerce=$data[0]['COMMERCE'];		
			$mainguest=$data[0]['CLIENT'];	
			$response->booking=$data[0]; 
		}
		
		//mainguest
		$cadena_sql=$this->sql->cadena_sql("dataUserById",$mainguest);
		$data=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){  
			$response->message="Error de Datos. Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false";
			return $response;
		}else{
			$response->responsible=$data[0]; 		
		}
		 
		//commerce
		$cadena_sql=$this->sql->cadena_sql("dataCommerceByID",$idcommerce);
		$data=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){  
			$response->message="Error de Datos. Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false";
			return $response;
		}else{
			$response->commerce=$data[0]; 		
		}
		
		//room
		$cadena_sql=$this->sql->cadena_sql("dataRoomBookingbyID",$idbooking);
		$data=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($data)){  
			$response->message="Error de Datos. Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false";
			return $response;
		}else{
			$response->room=$data[0]; 		
		}
	
		$response->status_code=200;  
		$response->status="true"; 
		
		//var_dump($response);
		
		include_once($this->ruta."/funcion/sendMail.php");

		//return $response;	
	}
	

	private function getDataCommerceByID($idcommerce){
		//commerce
		$response=new stdClass();
	
		$cadena_sql=$this->sql->cadena_sql("dataCommerceByID",$idcommerce);
		$data=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		
		if(!is_array($data)){  
			$response->message="Error de Datos. Prueba recargando la página ";
			$response->status_code=201;  
			$response->status="false";
			return $response;
		}else{
			$response->commerce=$data[0]; 		
		}
		
		return $response;
	}
	

	//groupRoom,commerce,checkin(YYYY/MM/DD),checkout(YYYY/MM/DD),numRooms 
	//user,session,adults,children,infants
	private function validate($variable){ 
			
			//Inicio Validaciones Sesion
			if(!isset($variable['session']) || $variable['session']==""){ 
				$output['message'][]="Invalid Session ";
				$output['status']="false"; return $output;
			}
			//temporal 
			$cadena_sql=$this->sql->cadena_sql("getAllSession",$variable['session']);
			$session=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
			
			if(!is_array($session)){ 
				$output['message'][]="No fue posible rescatar una sesión. Prueba recargando la página ";
				$output['status_code']=205;  
				$output['status']="false"; return $output;
			}
			
			//Inicio Validaciones Basicas
			if(!isset($variable['groupRoom']) || $variable['groupRoom']==""){
				$output['message'][]="Error: Reservable no especificado ";
				$output['status']="false"; return $output;
			}else{
				$variable["group"]=$variable["groupRoom"];
			}
			
			if($variable['adults']==""){
				$output['message'][]="Debes colocar el numero de adultos ";
				$output['status']="false"; return $output;
			}
			
			if($variable['checkin']=="" || $variable['checkout']==""){
				$output['message'][]="Las fechas de CheckIn y CheckOut son obligatorias ";
				$output['status']="false"; return $output;
			}			
			
			$variable['commerce']=$this->commerce;			
			
			if($variable['commerce']==""){
				$output['message'][]="Error de datos";
				$output['status']="false"; return $output;
			}
			
			//Fin Validaciones Basicas//

			//La reserva es generada como usuario anonimo siempre en la confirmacion se actualiza
			$variable['user']=$this->idUser=$session[0][0];     
			
			//Obtener informacion del tipo de reservable
			$cadena_sql=$this->sql->cadena_sql("dataGroupReservable",$variable);
			$dataGroupReservable=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

			
			if(isset($variable['numRooms']) && $variable['numRooms']<>""){
				$variable['numRooms']=$variable['numRooms'];			
			}else{
				$variable['numRooms']=1;
			}
			
			$variable['idRoom']=0;
			if(isset($variable['room']) && $variable['room']<>""){
				$variable['idRoom']=$variable['room'];			
			}
			
			$checkin=explode("/",$variable['checkin']);
		
			$anio=$checkin[2];
			$mes=$checkin[1];
			$dia=$checkin[0];
			
			//la uso para validar la reserva aumento un segundo
			$variable['timeStampStart']=(mktime(0,0,0,$mes,$dia,$anio)); //+1
			
			//la uso para insertar la reserva
			$timeStampStart=mktime(0,0,0,$mes,$dia,$anio);

			$checkout=explode("/",$variable['checkout']);
		
			$anio=$checkout[2];
			$mes=$checkout[1];
			$dia=$checkout[0];
			
			//tiempo q voy a utilizar para crear la reserva

			$variable['timeStampEnd']=(mktime(0,0,0,$mes,$dia,$anio))-1;
			$timeStampEnd=$variable['timeStampEnd'];

			//*validar que la fecha inicial sea mayor al tiempo actual
			/*if((($variable['timeStampStart'])*1)<=time()){
				$output['message'][]="No se pueden realizar reservas anteriores a la fecha actual";
				$output['status']="false"; return $output;
			}*/
		
			//*validar que la fecha final sea mayor que la inicial
			if((($timeStampEnd)*1)<=(($variable['timeStampStart'])*1)){
				$output['message'][]="la fecha final no puede ser menor a la fecha inicial";
				$output['status']="false"; return $output;
				
			}


			//A. Se eliminan todas las reservas que tenga el usuario actual si aun no han sido confirmadas
			// ( Si las reservas estan confirmadas no deben tener el indicador de sesion temporal)

			$cadena_sql=$this->sql->cadena_sql("deleteUnconfirmedBookingUser",$this->idUser);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
			$cadena_sql=$this->sql->cadena_sql("deleteUnconfirmedSession",$variable['session']);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
		
		
			//b. Se eliminan todas las reservas que no hayan sido confirmadas y que hallan expirado
			$cadena_sql=$this->sql->cadena_sql("deleteUnconfirmedBookingAll");
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

			//D. Se valida si la reserva presenta cruce con otra reserva /////////////
			
			
			//-------Inicio Validacion Cruce--------//
			
				//Evaluo el tipo de validacion si es compartida o individual
			
					if($dataGroupReservable[0]['CAPACITYTYPE']<>"C"){
			
						//*Busco el total de habitaciones q pertenecen al grupo seleccionado
						
						$cadena_sql=$this->sql->cadena_sql("countRoomsByGroup",$variable);
						$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
						$maxRooms=$result[0][0]; //total habitaciones	
						
						//* Consulto el numero de habitaciones reservadas para la fecha
						//* filtrando por grupo independientemete si tienen o no habitacion asignada	
						
						$cadena_sql=$this->sql->cadena_sql("buscarReservablesOcupados",$variable);
						$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
						
						if(is_array($result)){
							$busyRooms=count($result); //total habitaciones ocupadas
						}else{
							$busyRooms=0;
						} 

						//$output['message'][]=print_r($result,TRUE);
						//$output['message'][]="Habilitadas: {$maxRooms} Ocupadas: {$busyRooms} ";
						//$output['message'][]="\nDisponibles:".($maxRooms-$busyRooms);
						//$output['status']="false"; return $output;
						
						$avalaibleRooms=$maxRooms-$busyRooms;
						 
												
						if($avalaibleRooms<=0){
							$output['message'][]="\n No tenemos disponibilidad para esta fecha";
							$output['status']="false"; return $output;
							exit;
						}
						
						if($avalaibleRooms<$variable['numRooms']){
							$output['message'][]="\n Solo tenemos {$avalaibleRooms} habitacion(es) disponible(s) para esta fecha";
							$output['status']="false"; return $output;
							exit;
						}
						
					}else{
					
						if($dataGroupReservable[0]['CAPACITY']<=$variable['adults']){
							$output['message'][]="\n Capacidad Maxima";
							$output['status']="false"; return $output;
							exit; 
						}
						//evaluar capacidad maxima
					
					}
					
					
			//-------Fin Validacion Cruce--------//
			
			
			//E. Inserto reserva anonima
			
			$variable['timeStampStart']=$timeStampStart;
			$cadena_sql=$this->sql->cadena_sql("insertBooking",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

			if($result){
				//F. Inserto reservables correspondientes con la reserva
				$output['idbooking']=$variable['id_reserva']=$this->miRecursoDB->ultimo_insertado();
				
				for($i=0;$i<$variable['numRooms'];$i++){
					$variable['id_reservable']=$valor;
					$cadena_sql=$this->sql->cadena_sql("insertReservables",$variable);
					$registro=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
				}	
				
				//Calculo del Valor
				$value=$this->calculateValue($variable['id_reserva']);
				$variable['value']=$value['value'];
				$variable['idbooking']=$variable['id_reserva'];   
				$cadena_sql=$this->sql->cadena_sql("updateValueBookingbyID",$variable);
				$registro=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
				
				
				//E. Inserto Servicios Adicionales
				//Por ahora el unico servicio es el de niños
				/*$variable['id_servicio']=1; 
				$variable['cantidad']=$variable['kids']; 
				$cadena_sql=$this->sql->cadena_sql("insertServices",$variable);
				$registro=$this->master_resource->ejecutarAcceso($cadena_sql,"");*/
				
			}

			$output['status_code']=200; 
			$output['status']="true"; 
			return $output;
	
	}
	
	private function calculateValue($idbooking){
			
      $output=array();
      $variable["commerce"]=$this->commerce;
      
			//Rescato los datos de la reserva
      $cadena_sql=$this->sql->cadena_sql("bookingByID",$idbooking);
			$currentBooking=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
			$currentBooking=$currentBooking[0];	  	
			$numDaysBooking=$currentBooking['NUMDAYS']; 
      

 			
			//Con el id rescato los reservables de la reserva (los grupos)
			//por ahora la relacion es uno a uno en el futuro se espera varios reservbles por reserva
			$cadena_sql=$this->sql->cadena_sql("dataBookingItems",$currentBooking['IDBOOKING']);
			$itemsBooking=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
						
			$i=0;
			$valueBooking=0;
			
			while(isset($itemsBooking[$i][0])){
      
        $variable['guest']=$itemsBooking[$i]['ADULTS'];
        $variable['adults']=$itemsBooking[$i]['ADULTS'];
        $variable['children']=$itemsBooking[$i]['CHILDREN'];
        $variable['infants']=$itemsBooking[$i]['INFANTS'];
			
				//Obtener informacion del tipo de reservable
				$variable["group"]=$itemsBooking[$i]['IDGROUP']; 
				$cadena_sql=$this->sql->cadena_sql("dataGroupReservable",$variable);
				$dataGroupReservable=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

				//rescato los valores correspondientes al actual grupo.
				
				
				$variable['idgroup']=$itemsBooking[$i]['IDGROUP'];

				//Evaluo el tipo de validacion si es compartida o individual
				if($dataGroupReservable[0]['CAPACITYTYPE']=="C"){
					$variable['guest']=1;
				}
				
				$cadena_sql=$this->sql->cadena_sql("priceList",$variable);
				$priceList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
				
				$priceList=$this->orderArrayKeyBy($priceList,"SEASON","GUEST");
				
				//empezando con la fecha inicial voy buscando la temporada
				//a la q corresponde cada dia si no la encuentro asumo temporada baja (1)
				
				$d=0;
				
				for($d;$d<=$numDaysBooking;$d++){
					
					$variable['day']=($currentBooking['STARTBOOKING'])+($d*86400);
					$variable['commerce']=$currentBooking['IDCOMMERCE'];

					$cadena_sql=$this->sql->cadena_sql("searchDay",$variable);
					$seasonDay=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
					
					//si no existe una temporada se asume temporada baja 
					if(is_array($seasonDay)){
						$sd=$seasonDay[0]['IDSEASON'];
					}else{	
						$sd=1;	
					}
					//Evaluo el tipo de validacion si es compartida o individual
									 
					if($dataGroupReservable[0]['CAPACITYTYPE']<>"C"){ 
						//Individual: Depende del valor asignado para ese numero de invitados en ese dia especifico
						$valueBooking=$valueBooking+($priceList[$sd][$variable['guest']]['COP'])*1;
				
          }else{ 
						//Compartida: Depende del valor asignado individual multiplicado por el numero de invitados en ese dia especifico
            $valueBooking=$valueBooking+($priceList[$sd][$variable['guest']]['COP'])*1*($variable['adults'])*1; 
					} 
			
					//el invitado 0 corresponde al valor unico para niños
					$valueBooking=$valueBooking+($priceList[$sd]['0']['COP'])*1*($variable['children'])*1; 
					  
				} 
				$i++; 
        

			}

  		$output['message'] = "";
			$output['idbooking'] = $idbooking;
			$output['guest'] =  $variable['guest'];
			$output['children'] =  $variable['children'];
			$output['infants'] =  $variable['infants'];
			$output['checkin'] = $currentBooking['CHECKIN'];
			$output['days'] = $currentBooking['NUMDAYS'];
			$output['value'] = $valueBooking;
      
			
			return $output;
	}

	private function getRooms($variable){
		$response=new stdClass();
		$variable["commerce"]=$this->commerce;
		$cadena_sql=$this->sql->cadena_sql("dataRoomAvailability",$variable);
		$rooms=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$rooms=$this->orderArrayKeyBy($rooms,"IDGROUP");
		foreach($rooms as $key=>$value){
			unset($rooms[$key][0]);
			unset($rooms[$key][1]);
			unset($rooms[$key][2]);
			unset($rooms[$key][3]);
			unset($rooms[$key][4]);
			
			$folder=opendir($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/".$this->commerce_folder."/".$key);
			$images=array();
			
			while($file=readdir($folder)){
				if (!is_dir($file)){
					$rooms[$key]['IMAGES'][]=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."/".$this->commerce_folder."/".$key."/".$file;
				}
			}
		}
		
		$response->status="true";
		$response->rooms=$rooms; 
		$response->message="true"; 
		return $response;
	}
  
	private function getAdditionalFields($variable){
		$response=new stdClass();
		$variable["commerce"]=$this->commerce;
		$cadena_sql=$this->sql->cadena_sql("getAdditionalFields",$variable);
		$fields=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    
    if(is_array($fields)){
      $response->status="true";
      $response->fields=$fields; 
    }else{
      $response->status="false";
    }
		return $response;
	}
	
	private function getListCommerces($variable){
	
		$response=new stdClass();
		$variable['all']='all';
		$variable['commerceType']='2';
		
		
		if(isset($variable['plan'])){ 
			switch($variable['plan']){
				case 'premium':
					$variable['plan']='4';
				break;
				case 'basico': 
					$variable['plan']='1';
				break;
				
			}
		}else{
			$variable['plan']="";
		}
		
		$cadena_sql=$this->sql->cadena_sql("dataAllCommerce",$variable);
		$commerce=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		$response->status="true";
		$response->places=$commerce; 
		$response->message="true"; 
		return $response;
	}
		
	private function saveGuest($variable){
		
    
    /* revisar campos adicionales */
    
        $this->saveOthersFields($variable);
    
    /* fin revisar campos adicionales */
    
		//temporal 
		foreach($variable as $key=>$value){
			$tmp=explode("@",$key);
			if(count($tmp)>1){
				$variable[$tmp[0]][$tmp[1]]=$value;
				unset($variable[$key]);
			}
		}
 
		$cadena_sql=$this->sql->cadena_sql("getAllSession",$variable['session']);
		$session=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
		if(!is_array($session)){ 
			$response->message="No fue posible rescatar una sesión. Prueba recargando la página ";
			$response->status_code=205;  
			$response->status="false"; 
		}else{
			//var_dump($variable);  
			
			if(!isset($variable['mainguest'])){
				$response->message="Los datos del responsable son obligatorios"; 
				$response->status="false"; 
				return $response;
			}
			if(isset($variable['mainguest'])){
				$variable['mainguest']['session']=$variable['session'];
				$result=$this->saveMainGuest($variable['mainguest']);		
				if($result!==true){
					$response->message=$result;
					$response->status="false"; 
					return $response;
				}
			}		
			if(isset($variable['guest'])){
				$variable['guest']['session']=$variable['session'];
				$this->saveOthersGuest($variable['guest']);
				if($result!==true){
					$response->message=$result;
					$response->status="false";
					return $response;
				}
			}
		}
		$response->status_code=200;
		return $response;
	}
	
	private function orderArrayKeyBy($array,$key,$second_key=""){
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
		/*echo "<pre>";
		var_dump($newArray);
		echo "</pre>";*/
		return $newArray;
	}
	
}