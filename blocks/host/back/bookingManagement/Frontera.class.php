<?php
include_once("core/manager/Configurador.class.php");
include_once("core/auth/Sesion.class.php");

class FronterabookingManagement{
 

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
		$this->masterResource=$this->miConfigurador->fabricaConexiones->getRecursoDB("master");		
		$this->commerce=$this->miSesion->getValorSesion('commerce');
		$this->idSesion=$this->miSesion->getValorSesion('idUsuario');
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

		$option=isset($_REQUEST['optionBooking'])?$_REQUEST['optionBooking']:"";
		
		switch($option){
			case "list":
				$this->showList();
				break;
			case "new":
				$this->showNew();
				break;
			case "reloadForm":
				$this->paintBookingForm($_REQUEST['month'],$_REQUEST['year'],$this->commerce);
				break;
			case "assignStatus":
				$this->assignStatus($_REQUEST['status'],$this->commerce,$_REQUEST['booking']);
				break;
				
			case "assignValue":
				$this->assignValue($_REQUEST['value'],$this->commerce,$_REQUEST['booking']);
				break;	
				
			case "assignPaymentValue":
				$this->assignPaymentValue($_REQUEST['value'],$this->commerce,$_REQUEST['booking']);
				break;
			
			case "assignOnlineValue":
				$this->assignOnlineValue($_REQUEST['onlinepayment'],$_REQUEST['commerce'],$_REQUEST['booking']);
				break;
        
			case "assignObservation":
				$this->assignObservation($_REQUEST['value'],$this->commerce,$_REQUEST['booking']);
				break;	
				
			case "assignStatusPayment":
				$this->assignStatusPayment($_REQUEST['paymentstatus'],$this->commerce,$_REQUEST['booking']);
				break;
			case "assignRoom":
				$this->assignRoom($_REQUEST['room'],$this->commerce,$_REQUEST['booking']);
				break;
			case "assignTypeRoom":
				$this->assignTypeRoom($_REQUEST['typeroom'],$this->commerce,$_REQUEST['booking']);
				break;
			case "assignDate":
				$this->assignDate($_REQUEST['chekininput'],$_REQUEST['chekoutinput'],$this->commerce,$_REQUEST['booking']);
				break;
				
			case "showDetails":
				$this->paintBookingDetail($_REQUEST['bookings'],$this->commerce);
				break;	
			case "blockBooking":
				$this->blockBookingDetail($_REQUEST['bookings'],$this->commerce);
				break;	
			case "unblockBooking":
				$this->unblockBookingDetail($_REQUEST['bookings'],$this->commerce);
				break;	
			case "bookinglist":
				$this->showViewBookingList();
				break;	
					
			case "voucher":
				$this->showVoucher($_REQUEST['idbooking']);
				break;	
				
			default:
				$this->showView();
				break;
		}
		
	}

	
	
	function paintBookingDetail($bookings,$commerce){

		//Solo se traen ls habitaciones q no se encuntran asignadas

		
		$cadena_sql=$this->sql->cadena_sql("typeRooms",$variable);
		$typeRooms=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$typeRooms=$this->orderArrayKeyBy($typeRooms,"IDTYPEROOM");	

				
		$bookings=explode(",",$bookings);
		include_once($this->ruta."/html/detail.php");
		
	}
	
	function blockBookingDetail($bookings,$commerce){
		$bookings=explode(",",$bookings);
		
		$variable['commerce']=1;
		$variable['user']=$this->idSesion;
		
		//insertar capacidad 999999999 //num maximo global
		//insertar estado 5
		
		foreach($bookings as $booking){
		
			$dataBooking=explode("-",$booking);
			
			$variable['timeStampIni']=(($dataBooking[0])*1);
			$variable['timeStampFin']=(($dataBooking[0])*1)+86399;
			
			$cadena_sql=$this->sql->cadena_sql("blockBooking",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
			//F. Inserto reservables correspondientes con la reserva
			if($result){
				
				$variable['id_reserva']=$this->miRecursoDB->ultimo_insertado();
				$variable['id_reservable']=$dataBooking[1];
				$cadena_sql=$this->sql->cadena_sql("insertBookingItems",$variable);
				$registro=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
					
			}
			
		}
		
		echo "Bloqueo exitoso";

		

	}

	function unblockBookingDetail($bookings,$commerce){
		$bookings=explode(",",$bookings);
		
		$variable['commerce']=1;
		$variable['user']=$this->idSesion;
		
		//insertar capacidad 999999999 //num maximo global
		//insertar estado 5
		
		foreach($bookings as $booking){
		
			$dataBooking=explode("-",$booking);
			$variable['timeStampIni']=$dataBooking[0];
			
			$cadena_sql=$this->sql->cadena_sql("unblockBooking",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
		}
		
		echo "Desbloqueo exitoso";

	}
	
	function assignDate($chekininput,$chekoutinput,$commerce,$booking){
		
		if (!ereg("(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](19|20)[0-9]{2}", $chekininput) || !ereg("(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](19|20)[0-9]{2}", $chekoutinput)) {
			echo "Ups! Formato de fecha incorrecto";
			return false;
		} 
		
		include_once("blocks/host/back/internalBooking/Funcion.class.php");
		
		
		$variable['IDBOOKING']=$booking;
		$variable['COMMERCE']=1;
		
		$cadena_sql=$this->sql->cadena_sql("detailBooking",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$result=$result[0];
		
		$variable['dataRoomBooking']=$result['ROOMTYPE']."-".$result['ROOM'];
		$variable['guestBooking']=$result['NUMGUEST'];
		$variable['checkin']=$chekininput;
		$variable['checkout']=$chekoutinput;
		$variable['no-users']="TRUE";
		$variable['numRooms']="1";
		$variable['medioBooking']=$result['MEDIO'];
		$variable['valueBooking']=$result['VALUEBOOKING'];
		$variable['numGuest']=$result['NUMGUEST'];
		$variable['commerce']=$result['IDCOMMERCE'];
		$variable['company']=$result['IDCOMMERCE'];
		$variable['cliente']=$result['CLIENT'];
		
		
		$booking=new FuncioninternalBooking();
		$booking->ruta="blocks/host/back/internalBooking/";
		
		include_once($booking->ruta."Sql.class.php");
		$booking->setSql(new SqlinternalBooking); 
			
		$cadena_sql=$this->sql->cadena_sql("inactiveBooking",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
		
		$booking->procesarReservaItemReservable($variable);
		
		if($booking->status=="false"){
			$cadena_sql=$this->sql->cadena_sql("activeBooking",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			echo $booking->mensaje;
			
		}elseif($booking->status=="true"){ 
			$variable['oldBooking']=$variable['IDBOOKING'];
			$variable['newBooking']=$booking->id_reserva;
			
			$cadena_sql=$this->sql->cadena_sql("updateGuest",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
			$cadena_sql=$this->sql->cadena_sql("updatePayment",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
			echo "Las fechas se actualizaron correctamente \n No olvides verificar el valor de la Reserva";
			
		}	
		
	}

	function assignValue($value,$commerce,$booking){

		
		$variable['value']=filter_var($value,FILTER_SANITIZE_NUMBER_INT);
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
				
		$cadena_sql=$this->sql->cadena_sql("updateValue",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		if($result){
			echo "El valor se actualizo correctamente";
		}else{
			echo "No se logro actualizar el valor";
		}
	}	
	
	function assignPaymentValue($value,$commerce,$booking){

		$variable['payment']=filter_var($value,FILTER_SANITIZE_NUMBER_INT);
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
				
		$cadena_sql=$this->sql->cadena_sql("updatePaymentValue",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		if($result){
			echo "El valor se actualizo correctamente";
		}else{
			echo "No se logro actualizar el valor"; 
		}
	}	
	
	function assignObservation($value,$commerce,$booking){

		$variable['observation']=filter_var($value,FILTER_SANITIZE_STRING);
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
				
		$cadena_sql=$this->sql->cadena_sql("updateObservation",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		if($result){
			echo "La observacion se actualizo correctamente";
		}else{
			echo "No se logro actualizar la observacion";
		}
	}
	
	function assignStatus($status,$commerce,$booking){

		$variable['status']=$status;
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
		$variable['user']=$this->idSesion; 

				
		$log['event']="update-status";
		$log['data']=$variable['status'];
		$variable['event']=json_encode($log);
		$cadena_sql=$this->sql->cadena_sql("insertLog",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
		$cadena_sql=$this->sql->cadena_sql("assignStatus",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		if($result){
			echo "El estado se actualizo correctamente";
		}else{
			echo "No se logro realizar la asignacion de estado";
		}
	}
	
	function assignStatusPayment($paymentstatus,$commerce,$booking){

		$variable['paymentstatus']=$paymentstatus;
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
				
		$cadena_sql=$this->sql->cadena_sql("assignStatusPayment",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		if($result){
			echo "El estado del pago se actualizo correctamente";
		}else{
			echo "No se logro realizar la asignacion del pago";
		}
	}
	
  function assignOnlineValue($paymentstatus,$commerce,$booking){

		$variable['user']=$this->idSesion; 
		$variable['onlinepayment']=filter_var($paymentstatus,FILTER_SANITIZE_NUMBER_INT);
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
		$variable['description']="RESERVA";
		$variable['currency']="COP";
		$variable['answer']="REGISTRO MANUAL :".date('l jS \of F Y h:i:s A');
				
		$cadena_sql=$this->sql->cadena_sql("getOnlineValue",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");	
		
		if(!is_array($result)){
			$cadena_sql=$this->sql->cadena_sql("insertOnlineValue",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
      $log['event']="insert-online-payment";
      $variable['event']=json_encode($log);
      $cadena_sql=$this->sql->cadena_sql("insertLog",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
      
		}else{
			$log['event']="update-online-payment";
			$log['data']=$result[0];
			$variable['event']=json_encode($log);
			$cadena_sql=$this->sql->cadena_sql("insertLog",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
			$cadena_sql=$this->sql->cadena_sql("assignOnlineValue",$variable);
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");		
		}
	
		
		if($result){
			echo "El pago se actualizo correctamente";
		}else{
			echo "No se logro realizar la asignacion del pago";
		}
	}
  
	function assignRoom($room,$commerce,$booking){

		$variable['room']=$room;
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
				
		$cadena_sql=$this->sql->cadena_sql("assignRoom",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		if($result){
			echo "La habitacion se asigno correctamente";
		}else{
			echo "No se logro realizar la asignacion";
		}
	}
	
	function assignTypeRoom($typeroom,$commerce,$booking){

		$variable['typeroom']=$typeroom;
		$variable['commerce']=$commerce;
		$variable['booking']=$booking;
				
		$cadena_sql=$this->sql->cadena_sql("assignTypeRoom",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		if($result){
			echo "El tipo de habitacion se actualizo correctamente. ";
		}else{
			echo "No se logro realizar la asignacion";
		}
	}
	
	function getGuestBooking($booking,$commerce){
	
		$variable['IDBOOKING']=$booking;
		$variable['COMMERCE']=$commerce;
		
		$cadena_sql=$this->sql->cadena_sql("detailBookingGuest",$variable);
		$clients=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		return $clients;
	}
	
	function getBookinsbyDate($booking,$commerce){
	
		$variable['IDCELL']=$booking;
		$variable['COMMERCE']=$this->commerce;
		
		$cadena_sql=$this->sql->cadena_sql("detailBooking",$variable);
		$clients=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
	
		return $clients;
	}
	
	function getPayuPayment($booking){

		$cadena_sql=$this->sql->cadena_sql("detailPayment",$booking);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		if(is_array($result)){
		
		}else{
			$result[0]['VALUE']=0;
		}
		return $result[0];
	}
	
	
	function getAvalaibleRooms($current,$start,$end,$commerce,$group){
	
		//consulto todas las habitaciones
		$cadena_sql=$this->sql->cadena_sql("searchRooms",$this->commerce);
		$Rooms=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$Rooms=$this->orderArrayKeyBy($Rooms,"IDROOM");
		
		//almaceno la habitacion actual
		$currentRoom=array();
		
		if($current<>"0"){
			$currentRoom[$current]=$Rooms[$current];
		}
		
		$variable["timeStampStart"]=$start;
		$variable["timeStampEnd"]=$end;
		$variable["commerce"]=$commerce;
		$variable["groupRoom"]=$group;
		
		//consulto todas la habitaciones ocupadas
		$cadena_sql=$this->sql->cadena_sql("searchBusyRooms",$variable);
		$busyRooms=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
	
		$busyRooms=$this->orderArrayKeyBy($busyRooms,"IDROOM");
		
		$avalaibleRooms=array_diff_key($Rooms,$busyRooms);
		$avalaibleRooms=array_merge($avalaibleRooms,$currentRoom);
		
		return $avalaibleRooms;
	}
	
  
	function getAdditionalData($booking){
			
			$variable["commerce"]=1;
			
			$cadena_sql=$this->sql->cadena_sql("getFieldsAdditional",$variable);
			$fieldsAdditional=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");			
			$fieldsAdditional=$this->orderArrayKeyBy($fieldsAdditional,'IDFIELD');
			$fieldsAdditional['100'][0]=array('NAMEFIELD'=>'Ciudad');
			//var_dump($fieldsAdditional);
			$cadena_sql=$this->sql->cadena_sql("dataOtherFields",$booking);
			$valuesAdditional=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
	
			$ad=0; 
			while(isset($valuesAdditional[$ad]['IDFIELD'])){ 
					$valuesAdditional[$ad]['NAMEFIELD']=$fieldsAdditional[$valuesAdditional[$ad]['IDFIELD']][0]['NAMEFIELD'];
				$ad++;
			}
			return $valuesAdditional;
	}
	
	
	function managementBooking($booking,$typeRooms){
	
		$cadena_sql=$this->sql->cadena_sql("allUsers","");
		$users=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
			
		$cadena_sql=$this->sql->cadena_sql("serviceListbyCommerce",$this->commerce);
		$serviceList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$serviceList=$this->orderArrayKeyBy($serviceList,"IDSERVICE");		
		
		$cadena_sql=$this->sql->cadena_sql("serviceListbyBooking",$booking['IDBOOKING']);
		$bookingServiceList=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$users=$this->orderArrayKeyBy($users,"IDUUSER");
		
		$formSaraDataURL.="pagina=bookingManagement";
		$formSaraDataURL.="&bloque=bookingManagement";
		$formSaraDataURL.="&action=bookingManagement";
		$formSaraDataURL.="&bloqueGrupo=host/back";
	 	$formSaraDataURL.="&idbooking=".$booking['IDBOOKING'];
	 	$formSaraDataURL.="&optionBooking=voucher";
		$formSaraDataURL=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataURL,$this->enlace);
			
		//$formSaraDataService.="pagina=bookingManagement";
		$formSaraDataService.="jxajax=main";
		$formSaraDataService.="&bloque=bookingManagement";
		$formSaraDataService.="&action=bookingManagement";
		$formSaraDataService.="&bloqueGrupo=host/back";
	 	$formSaraDataService.="&idbooking=".$booking['IDBOOKING'];
	 	$formSaraDataService.="&optionBooking=addService";
		$formSaraDataService=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataService,$this->enlace);
	
		$booking['URLVOUCHER']=$formSaraDataURL;
		$booking['DNI']=$users[$booking['CLIENT']][0]['DNI'];
		$booking['NAMECLIENT']=$users[$booking['CLIENT']][0]['NAMECLIENT'];
		$booking['COUNTRY']=$users[$booking['CLIENT']][0]['COUNTRY'];
		$booking['EMAILCLIENT']=$users[$booking['CLIENT']][0]['EMAILCLIENT'];
		$booking['PHONECLIENT']=$users[$booking['CLIENT']][0]['PHONECLIENT'];
		
		include($this->ruta."/html/managementBooking.php");
	}	
	
	
	function showView(){

		$formSaraDataURL="jxajax=main";
		$formSaraDataURL.="&pagina=bookingManagement";
		$formSaraDataURL.="&bloque=bookingManagement";
	 	$formSaraDataURL.="&bloqueGrupo=host/back";
		$formSaraDataURL=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataURL,$this->enlace);
	
		$formSaraDataBookingList="pagina=bookingManagement";
	 	$formSaraDataBookingList.="&optionBooking=bookinglist";
		$formSaraDataBookingList=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataBookingList,$this->enlace);
		
		$month=date("m"); //LEER CURRENT
		$year=date("Y");
		$numDaysMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		$widthCell=(80/($numDaysMonth));
		//
		include_once($this->ruta."/html/view.php");
	}
	
	function showViewBookingList(){
	
		$companiesByUser=$this->companyByUser();
		
		$cadena_sql=$this->sql->cadena_sql("allBookingsByCompany","1");
		$bookings=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		$cadena_sql=$this->sql->cadena_sql("allUsers","");
		$users=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$users=$this->orderArrayKeyBy($users,"IDUUSER");

		$formSaraDataURL="jxajax=main";
		$formSaraDataURL.="&pagina=bookingManagement";
		$formSaraDataURL.="&bloque=bookingManagement";
	  $formSaraDataURL.="&bloqueGrupo=host/back";
		$formSaraDataURL=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataURL,$this->enlace);
	
		$formSaraDataBookingList="pagina=bookingManagement";
	 	$formSaraDataBookingList.="&opcion=bookinglist";
		$formSaraDataBookingList=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraDataBookingList,$this->enlace);
	
		include_once($this->ruta."/html/viewList.php");
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
	
		$allCompaniesOrderByParent=$this->orderArrayKeyBy($allCompanies,"IDPARENT");
		//si el id tiene hijos asociados recorremos sus hijos
		if(array_key_exists($parent,$allCompaniesOrderByParent)){
			foreach($allCompaniesOrderByParent[$parent] as $key=>$value){
				$this->companyByParent($value['IDCOMPANY'],$allCompanies);
			}
		}
		else{
			//si el id no tiene hijos entonces lo agregamos a las empresas o establecimientos q pertenecen al usuario			
			$this->companies[]=$parent;
		}
	}
	
	function paintBookingForm($month,$year,$commerce){
	
		//calculo numero de dias del mes y tamaño del ancho de celda
		$numDaysMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		$widthCell=(80/($numDaysMonth));
		
		//calculo los intervalos de inicio y fin de mes
		$variable['firstDay']=mktime(0,0,0,$month,1,$year);
		$variable['LastDay']=mktime(23,59,59, $month,$numDaysMonth,$year);
		$variable['commerce']=$commerce;
		
		
		$cadena_sql=$this->sql->cadena_sql("typeBookingCommerce",$variable['commerce']);
		$type=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda");
			
		
		//si la reserva es por Numero de Personas se consulta la tabla reservas 
		
			$cadena_sql=$this->sql->cadena_sql("bookingsByNP",$variable);
			$bookings=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");		
			
			
			//recorremos las reservas para calcular la duracion de cada una
			if(is_array($bookings)){

			$b=0;
			while(isset($bookings[$b][0])){
				$duration=($bookings[$b]['DATEEND'])-($bookings[$b]['DATESTART']);
				$duration=$duration/86400;
				$c=1;
				for($c;$c<$duration;$c++){
				
					$position=count($bookings); //cuento el tamaño de las reservas para agregar al final las nuevas
					
					$bookings[$position]['IDCELL']=($bookings[$b]['DATESTART'])+($c*86400).'-'.$bookings[$b]['IDRESERVABLE'];
					$bookings[$position]['INFOCELL']=$bookings[$b]['IDBOOKING'];
					$bookings[$position]['DATESTART']=$bookings[$b]['DATESTART'];
					$bookings[$position]['NUMGUEST']=$bookings[$b]['NUMGUEST'];
					$bookings[$position]['NUMKIDS']=$bookings[$b]['NUMKIDS'];
				}
				
				$b++;
			}
			
			$bookings=$this->orderArrayKeyBy($bookings,'IDCELL');
      }

			//Se calculan el numero de intervalos
			
			$cadena_sql=$this->sql->cadena_sql("searchRooms",$variable['commerce']);
			$rooms=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
      $i=0;
			$labels=array();

			$grid['LABELS'][]="NO ASIG";
			$j=1;
			for($j;$j<=$numDaysMonth;$j++){
				$grid['BOOKING'][$j][]=mktime(0,0,0,$month,$j,$year)."-0";
			}
			
			while(isset($rooms[$i][0])){
				$time=time();
				$grid['LABELS'][$rooms[$i]['IDROOM']]=$rooms[$i]['NAME']; 
				$j=1;
				for($j;$j<=$numDaysMonth;$j++){
					$grid['BOOKING'][$j][]=mktime(0,0,0,$month,$j,$year)."-".$rooms[$i]['IDROOM'];
				}
				$i++;
			}
			
			include_once($this->ruta."/html/formNP.php");
	}



	function orderArrayKeyBy($array,$key){

		$newArray=array();

		foreach($array as $name=>$value){
			$newArray[$value[$key]][]=$array[$name];
		}
		/*echo "<pre>";
		var_dump($key);
		echo "</pre>";*/
		return $newArray;
	}



}
?>
