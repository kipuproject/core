<?php
 
if(!isset($GLOBALS["autorizado"])){
	include("index.php");
	exit;
}else{ 

	if(!$this->miRecursoDB){
		$this->mensaje.="Error de Conexion";
		return $this->status="false";
	}
	
	$this->idUser = $variable['user']=$this->miSesion->getValorSesion("idUsuario");
	$cadena_sql=$this->sql->cadena_sql("dataCommerce",$this->commerce); 
	$commerce=$this->masterResource->ejecutarAcceso($cadena_sql,"busqueda"); 
	
	//** INICIO PRIMER PASO **/
	if($variable['optionprocess']=='prebooking' || $variable['prebooking']==""){ 
		$dataRoomBooking=explode("-",$variable['dataRoomBooking']);
		$variable['groupRoom']=$dataRoomBooking[0];
		$variable['idRoom']=$dataRoomBooking[1];
		$param['api']="hbooking";  
		$param['method']="validate"; 
		$param['key']=$commerce[0]['APIKEY'];
		$param['session']=$this->miSesion->getSesionId();
		$param['guestBooking']=$variable['guestBooking'];
		$param['checkin']=$variable['checkin'];
		$param['checkout']=$variable['checkout'];
		$param['commerce']=$commerce[0]['ID_TIPORESERVA'];;
		$param['groupRoom']=$dataRoomBooking[0];
		$param['room']=$dataRoomBooking[1];
		$param['kids']=$variable['kids'];
		$site=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."?";
		$url=$site.http_build_query($param,'','&');
		$data=file_get_contents($url); 

		$data=json_decode($data);
		if($data->status=="true"){
			$variable['prebooking']=$this->idbooking=$data->idbooking;
      $param2['api']="hbooking"; 
			$param2['method']="value"; 
			$param2['key']=$commerce[0]['APIKEY'];
			$param2['id']=$variable['prebooking'];
			$url=$site.http_build_query($param2,'','&');
			$data=file_get_contents($url);
			$data=json_decode($data);
			$this->value=$data->value;
			
		}else{
			$this->mensaje.=implode("\n-",$data->message);
			return $this->status="false";
		}
	}	
	//** FIN PRIMER PASO **/
	 

	//** INICIO SEGUNDO PASO **/
	if($variable['optionprocess']=='book'){
	
		if($variable['emailCustomer']==""){
			$this->mensaje.="El email del responsable de la reserva es obligatorio";
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			return $this->status="false";
		}
		if($variable['nameCustomer']==""){ 
			$this->mensaje.="El nombre del responsable de la reserva es obligatorio";
			$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			return $this->status="false";
		}
		
		$this->mensaje.=$cadena_sql=$this->sql->cadena_sql("dataUserByEmail",$variable['emailCustomer']);
		$dataUser=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		$dataUser=$dataUser[0];
		
		
		if(is_array($dataUser)){
			$variable['cliente']=$dataUser['USUARIOID'];
			//verificar datos y actualizarlos
		//	$cadena_sql=$this->sql->cadena_sql("updateDataUser",$variable);
		//	$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

		}else{
			//crear usuario
			include_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/blocks/admin/userManagement/Funcion.class.php");

			$userManagement= new FuncionuserManagement();
			$userManagement->ruta=$this->miConfigurador->getVariableConfiguracion("raizDocumento")."/blocks/admin/userManagement/";
			
			include_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/blocks/admin/userManagement/Sql.class.php");
			$userManagement->setSql(new SqluserManagement);
			
			$varUser['email']=$variable['emailCustomer'];
			$varUser['nombre']=$variable['nameCustomer'];
			$varUser['apellido']="";
			$varUser['identificacion']=$variable['idCustomer'];
			$varUser['password']="A".$variable['idCustomer'];
			$varUser['passwordc']="A".$variable['idCustomer'];
			
			$userManagement->conf_valid_email="false";
			$userManagement->processNew($varUser);
			
			if($userManagement->status==FALSE){
				$this->mensaje.=implode("\n",$userManagement->mensaje['error']);
				return $this->status="false";
			}
			
		//	$cadena_sql=$this->sql->cadena_sql("updateDataUser",$variable);
		//	$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			
			$cadena_sql=$this->sql->cadena_sql("dataUserByEmail",$variable['emailCustomer']);
			$dataUser=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
			$dataUser=$dataUser[0];
		
		}
		
		$friends=array();
		foreach($variable as $key=>$value){
			$friend=explode("_",$key);
			if($friend[0]=="friend"){
				$friends[$friend[2]][$friend[1]]=$value;
			}
		
		}

		
		foreach($friends as $key=>$value){
		
				
			if($value['Id']<>""){
				$cadena_sql=$this->sql->cadena_sql("dataGuestByIden",$value['Id']);
				$dataUserFriend=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
			}	 
			 
			if(is_array($dataUserFriend)){ 
			
				$cadena_sql=$this->sql->cadena_sql("insertFriend",array($variable['prebooking'],$dataUserFriend[0]['USUARIOID']));
				$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
			 
			}elseif($value['name']<>"" ){
			
				$cadena_sql=$this->sql->cadena_sql("createFriend",$value);
				$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
				
				$cadena_sql=$this->sql->cadena_sql("insertFriend",array($variable['prebooking'],$this->miRecursoDB->ultimo_insertado()));
				$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

			}
			
		}
	//** FIN SEGUNDO PASO **/
	
	
	//**INICIO TERCER PASO**//	
		$variable['customer']=$dataUser['USUARIOID']; 
		$variable['valueBooking']=$variable['valueBooking'];
		$variable['idbooking']=$variable['prebooking'];
		$cadena_sql=$this->sql->cadena_sql("confirmBookingbyID",$variable);
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
		
		$cadena_sql=$this->sql->cadena_sql("updateMedioBooking",$variable); 
		$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
		 
		$this->mensaje="La reserva se realizo con exito!";
		return $this->status="true";
	
	//**FIN TERCER PASO**//	

	}
	
	return $this->status="true";

	
}

 
?>