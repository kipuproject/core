<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlgestionReserva extends sql {
	
	
	var $miConfigurador;
	
	
	function __construct(){
		$this->miConfigurador=Configurador::singleton();
	}
	

	function cadena_sql($tipo,$variable="") {
		 
		/**
		 * 1. Revisar las variables para evitar SQL Injection
		 *
		 */
		
		$prefijo=$this->miConfigurador->getVariableConfiguracion("prefijo");
		$idSesion=$this->miConfigurador->getVariableConfiguracion("id_sesion");
		 
		switch($tipo) {
			 
			/**
			 * Clausulas específicas
			 */
			 
			case "api_key":
				$cadena_sql="SELECT ";
				$cadena_sql.="dbms DBMS, ";
				$cadena_sql.="id_tipoReserva IDCOMMERCE, ";
				$cadena_sql.="files_folder FOLDER ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="WHERE estado=1 ";
				$cadena_sql.="AND api_key='".$variable."'";
				break;
								
			case "buscarReservablesOcupados":
				$cadena_sql="SELECT  ";
				$cadena_sql.="count(id_reservable) ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="ON (".$prefijo."reserva_reservable.id_reserva = ".$prefijo."reserva.id_reserva) ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="( ";
				$cadena_sql.=$prefijo."reserva.fecha_inicio BETWEEN '".$variable["timeStampStart"]."' AND '".$variable["timeStampEnd"]."' ";
				$cadena_sql.=" OR ";				
				$cadena_sql.=$prefijo."reserva.fecha_fin BETWEEN '".$variable["timeStampStart"]."' AND '".$variable["timeStampEnd"]."' ";
				$cadena_sql.=" OR ";
				$cadena_sql.="	( ";
				$cadena_sql.=$prefijo."reserva.fecha_inicio < '".$variable["timeStampStart"]."' ";
				$cadena_sql.="	AND ";
				$cadena_sql.=$prefijo."reserva.fecha_fin > '".$variable["timeStampEnd"]."' ";
				$cadena_sql.="	) ";
				$cadena_sql.=") ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.estado_reserva NOT IN (3,4) "; //la reserva no contenga los estados FINALIZADO Y CANCELADO
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.establecimiento='".$variable["company"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.tipo_reserva='".$variable["commerce"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva_reservable.id_reservable_grupo='".$variable["groupRoom"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.estado='1' ";
				$cadena_sql.="GROUP BY id_reservable ";
				break;		
				
			case "getFieldsAdditional":
				$cadena_sql="SELECT  ";
				$cadena_sql.="id_field IDFIELD, ";
				$cadena_sql.="name NAMEFIELD ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_fields  ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=" id_commerce='".$variable["commerce"]."' ";
				
				break;

			case "buscarReservable":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."reservable.id_reservable ID_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.nombre NOMBRE_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.descripcion DESCRIPCION_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.grupo GRUPO_RESERVABLE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."reservable.id_reservable = '".$variable."' ";
			
				break;
			case "buscarServiciosAdicionales":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."adicional.id_adicional ID_ADICIONAL, ";
				$cadena_sql.=$prefijo."adicional.nombre NOMBRE_ADICIONAL, ";
				$cadena_sql.=$prefijo."adicional.descripcion DESCRIPCION_ADICIONAL, ";
				$cadena_sql.=$prefijo."adicional.valor_cargo VALOR_CARGO, ";
				$cadena_sql.=$prefijo."adicional.moneda_cargo MONEDA_CARGO ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."adicional ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."adicional.tipo_reserva = '".$variable["tipo_reserva"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."adicional.establecimiento >= '".$variable["establecimiento"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."adicional.estado<>0 ";
				break;

			case "buscarInformacionReservables":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."reservable.id_reservable ID_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.nombre NOMBRE_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.descripcion DESCRIPCION_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.grupo GRUPO_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.capacidad CAPACIDAD, ";
				$cadena_sql.=$prefijo."reservable.valor VALOR, ";
				$cadena_sql.=$prefijo."reservable.moneda MONEDA, ";
				$cadena_sql.=$prefijo."reservable.imagen IMAGEN ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."reservable.tipo_reserva = '".$variable["tipo_reserva"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reservable.id_reservable IN ( ".$variable["cadena_reservables"]." ) ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reservable.establecimiento >= '".$variable["establecimiento"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reservable.estado<>0 ";
				break;
				
			case "insertBooking":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="( ";
				$cadena_sql.="`fecha_inicio`, ";
				$cadena_sql.="`fecha_fin`, ";
				$cadena_sql.="`tipo_reserva`, ";
				$cadena_sql.="`establecimiento`, ";
				$cadena_sql.="`cliente`, ";
				$cadena_sql.="`valor_total`, ";
				$cadena_sql.="`fecha_registro`, ";
				$cadena_sql.="`usuario_registro`, ";
				$cadena_sql.="`sesion_temp`, ";
				$cadena_sql.="`tiempo_expira_temp`, ";
				$cadena_sql.="`estado_reserva`, ";
				$cadena_sql.="`estado_pago` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['timeStampStart']."', ";
				$cadena_sql.="'".$variable['timeStampEnd']."', ";
				$cadena_sql.="'".$variable['commerce']."', ";
				$cadena_sql.="'".$variable['company']."', ";
				$cadena_sql.="'".$variable['user']."', ";
				$cadena_sql.="'0', ";
				$cadena_sql.="'".time()."', ";
				$cadena_sql.="'".$variable['user']."', ";
				$cadena_sql.="'".$variable['session']."', ";
				$cadena_sql.="'".((time())+1800)."', "; //POR DEFECTO CADA RESERVA SE GUARDARA 30 MINUTOS SI NO SE FINALIZA CORRECTAMENTE
				$cadena_sql.="'1', ";
				$cadena_sql.="'0' ";
				$cadena_sql.=")";
				break;
			
			case "insertBookingItems":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_reserva`, ";
				$cadena_sql.="`id_reservable_grupo` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['id_reserva']."', ";
				$cadena_sql.="'".$variable['groupRoom']."' ";
				$cadena_sql.=")";
				break;		
				
			case "insertReservables":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_reserva`, ";
				$cadena_sql.="`adults`, ";
				$cadena_sql.="`children`, ";
				$cadena_sql.="`infants`, ";
				$cadena_sql.="`id_reservable_grupo`, "; 
				$cadena_sql.="`id_reservable` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['id_reserva']."', ";
				$cadena_sql.="'".$variable['adults']."', ";
				$cadena_sql.="'".$variable['children']."', ";
				$cadena_sql.="'".$variable['infants']."', "; 
				$cadena_sql.="'".$variable['groupRoom']."', ";
				$cadena_sql.="'".$variable['idRoom']."' ";
				$cadena_sql.=")";
				break;
				
			case "insertServices":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva_servicio ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_reserva`, ";
				$cadena_sql.="`id_servicio`, ";
				$cadena_sql.="`cantidad` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['id_reserva']."', ";
				$cadena_sql.="'".$variable['id_servicio']."', ";
				$cadena_sql.="'".$variable['cantidad']."' ";
				$cadena_sql.=")";
				break;
				
			case "dataBookingItems":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable_grupo IDGROUP, ";
				$cadena_sql.="adults ADULTS, ";
				$cadena_sql.="children CHILDREN, ";
				$cadena_sql.="infants INFANTS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reserva ='".$variable."' ";
				break;
				
			case "insertFriend":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva_guest ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_reserva`, ";
				$cadena_sql.="`id_usuario` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable[0]."', ";
				$cadena_sql.="'".$variable[1]."' ";
				$cadena_sql.=")";
				break;	
				
			case "dataOtherFields":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_field IDFIELD, ";
				$cadena_sql.="value VALUE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_values ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reserva ='".$variable."' ";
        
				break;
				
			case "insertOtherFields":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva_values ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_field`, ";
				$cadena_sql.="`id_reserva`, ";
				$cadena_sql.="`value` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['idfield']."', ";
				$cadena_sql.="'".$variable['idbooking']."', ";
				$cadena_sql.="'".$variable['value']."' "; 
				$cadena_sql.=")";
				break;		
				
			case "createFriend":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."guest ";
				$cadena_sql.="( ";
				$cadena_sql.="`identificacion`, ";
				$cadena_sql.="`nombre`, ";
				$cadena_sql.="`pais_origen`, ";
				$cadena_sql.="`estado` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['Id']."', ";
				$cadena_sql.="'".$variable['name']."', ";
				$cadena_sql.="'".$variable['country']."', ";
				$cadena_sql.="'1' ";
				$cadena_sql.=")";
				break;	
				
			case "buscarTipoReserva":
				$cadena_sql="SELECT DISTINCT ";
				$cadena_sql.=$prefijo."commerce.id_tipoReserva ID_TIPORESERVA ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador ";
				$cadena_sql.="ON (".$prefijo."commerce.id_tipoReserva = ".$prefijo."commerce_filtrador.id_tipoReserva) ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."commerce.estado=1 ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador.estado=1 ";
				if($variable['filtros']<>""){
				  $cadena_sql.="AND ";
				  $cadena_sql.=$prefijo."tipo_reserva_filtrador.id_filtroOpcion IN ({$variable['filtros']}) ";
				}
				break;

			

			case "dataGroupReservable":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable_grupo IDGROUP, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="nombre_maquina MACHINE_NAME, ";
				$cadena_sql.="capacidad CAPACITY, ";
				$cadena_sql.="tipo_capacidad CAPACITYTYPE, "; 
				$cadena_sql.="descripcion DESCRIPTION ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable_grupo ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_tipoReserva ='".$variable["commerce"]."' ";
				if($variable["group"]<>""){
				$cadena_sql.="AND ";
				$cadena_sql.="id_reservable_grupo ='".$variable["group"]."' ";				
				}
				$cadena_sql.="AND ";
				$cadena_sql.="estado = 1 ";
				
			break;

			case "dataRoomAvailability":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable_grupo IDGROUP, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="nombre_maquina MACHINE_NAME, ";
				$cadena_sql.="capacidad CAPACITY, ";
				$cadena_sql.="descripcion DESCRIPTION ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable_grupo ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_tipoReserva ='".$variable["commerce"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="capacidad >= '".$variable["guest"]."' ";				
				$cadena_sql.="AND ";
				$cadena_sql.="estado = 1 ";
				$cadena_sql.="ORDER BY ";
				$cadena_sql.="capacidad ASC ";
				
			break;

			case "dataUserByID":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_usuario USUARIOID, ";
				$cadena_sql.="nombre NOMBRE, ";
				$cadena_sql.="apellido APELLIDO, ";
				$cadena_sql.="telefono TELEFONO, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="fecha_nacimiento BIRTHDAY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`id_usuario` ='".$variable."' ";
			break;
			
			case "dataUserByEmail":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_usuario USERID, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="apellido LASTNAME, ";
				$cadena_sql.="telefono PHONE, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="fecha_nacimiento BIRTHDAY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`correo` ='".$variable."' ";
			break;
			
			case "dataUserById": 
				$cadena_sql="SELECT ";
				$cadena_sql.="id_usuario USERID, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="apellido LASTNAME, ";
				$cadena_sql.="telefono PHONE, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="fecha_nacimiento BIRTHDAY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="WHERE "; 
				$cadena_sql.="`id_usuario` ='".$variable."' ";
			break;
			
			case "dataUserByIden":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_usuario USUARIOID, ";
				$cadena_sql.="nombre NOMBRE, ";
				$cadena_sql.="apellido APELLIDO, ";
				$cadena_sql.="telefono TELEFONO, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="fecha_nacimiento BIRTHDAY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`identificacion` ='".$variable."' ";
			break;
			
			case "dataGuestByIden":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_guest USUARIOID, ";
				$cadena_sql.="nombre NOMBRE, ";
				$cadena_sql.="apellido APELLIDO, ";
				$cadena_sql.="telefono TELEFONO, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="fecha_nacimiento BIRTHDAY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."guest ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`identificacion` ='".$variable."' ";
			break;
			
			case "priceList":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable_grupo IDTYPEROOM, ";
				$cadena_sql.="id_temporada SEASON, ";
				$cadena_sql.="guest GUEST, ";
				$cadena_sql.="COP COP, ";
				$cadena_sql.="USD USD ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable_valor ";
				$cadena_sql.="WHERE estado='1' ";
				$cadena_sql.="AND ";
				$cadena_sql.="`id_reservable_grupo` ='".$variable['idgroup']."' ";
				$cadena_sql.="AND( ";
				$cadena_sql.="`guest` ='".$variable['guest']."' ";
				$cadena_sql.="OR ";
				$cadena_sql.="`guest` = '0' "; //para incluir niños
				$cadena_sql.=") ";
				
				break;
				
			case "activeBooking": 
				$cadena_sql="SELECT ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="r.tipo_reserva IDCOMMERCE, ";
				$cadena_sql.="r.fecha_inicio STARTBOOKING, ";
				$cadena_sql.="r.fecha_fin ENDBOOKING, ";
				$cadena_sql.="DATEDIFF(FROM_UNIXTIME( `fecha_fin` ),FROM_UNIXTIME( `fecha_inicio` )) NUMDAYS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva r "; 
				$cadena_sql.="WHERE ";
				$cadena_sql.="r.sesion_temp ='".$variable['session']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="r.estado_reserva ='1' ";				
			break;
			
			case "bookingByID": 
				$cadena_sql="SELECT ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="r.tipo_reserva IDCOMMERCE, "; 
				$cadena_sql.="FROM_UNIXTIME(r.fecha_inicio) CHECKIN, ";
				$cadena_sql.="r.fecha_inicio STARTBOOKING, ";
				$cadena_sql.="r.fecha_fin ENDBOOKING, ";
				$cadena_sql.="FROM_UNIXTIME(r.fecha_fin) CHECKOUT, ";
				$cadena_sql.="DATEDIFF(FROM_UNIXTIME( `fecha_fin` ),FROM_UNIXTIME( `fecha_inicio` )) NUMDAYS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva r "; 
				$cadena_sql.="WHERE ";
				$cadena_sql.="r.id_reserva ='".$variable."' ";

				
			break;
			
			case "lockedBooking":
				$cadena_sql="SELECT ";
				$cadena_sql.="date ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_locked ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=" date ='".$variable['date']."' ";
				$cadena_sql.=" AND ";
				$cadena_sql.=" id_commerce ='".$variable['commerce']."' ";
				
			break;
			
			case "searchDay":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_season IDSEASON ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."season_calendar ";
				$cadena_sql.="WHERE estado='1' ";
				$cadena_sql.=" AND id_commerce = ".$variable['commerce'];
				$cadena_sql.=" AND time = '".$variable['day']."'";
				//$cadena_sql.=" AND DATE_FORMAT(FROM_UNIXTIME(time),'%m%d') = DATE_FORMAT(FROM_UNIXTIME('".$variable['day']."'),'%m%d')";
			break; 	  
				
			
			case "dataBookingByID":
				$cadena_sql="SELECT ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="DATE_FORMAT(FROM_UNIXTIME(r.fecha_inicio),'%m/%d/%Y') CHECKIN, ";
				$cadena_sql.="DATE_FORMAT(FROM_UNIXTIME((r.fecha_fin)+2),'%m/%d/%Y') CHECKOUT, ";
				$cadena_sql.="r.fecha_inicio CHECKIN_UNIXTIME, ";
				$cadena_sql.="r.fecha_fin CHECKOUT_UNIXTIME, ";	
				$cadena_sql.="r.observacion_cliente OBSERVATION_CLIENT, ";
				$cadena_sql.="'0' INFANTS, ";
				$cadena_sql.="r.cliente CLIENT, ";
				$cadena_sql.="r.tipo_reserva COMMERCE, ";
				$cadena_sql.="r.valor_total VALUE ";
				$cadena_sql.="FROM "; 
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="r.id_reserva ='".$variable."' ";
			break;
			
			case "dataBookingBySession":
				$cadena_sql="SELECT ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="DATE_FORMAT(FROM_UNIXTIME(r.fecha_inicio),'%m/%d/%Y') CHECKIN, ";
				$cadena_sql.="DATE_FORMAT(FROM_UNIXTIME((r.fecha_fin)+2),'%m/%d/%Y') CHECKOUT, ";
				$cadena_sql.="r.fecha_inicio CHECKIN_UNIXTIME, ";  
				$cadena_sql.="r.fecha_fin CHECKOUT_UNIXTIME, ";	
				$cadena_sql.="'0' INFANTS, ";
				$cadena_sql.="r.cliente CLIENT, ";
				$cadena_sql.="r.tipo_reserva COMMERCE, ";
				$cadena_sql.="r.observacion_cliente OBSERVATION_CLIENT, ";
				$cadena_sql.="r.valor_total VALUE ";
				$cadena_sql.="FROM "; 
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="r.sesion_temp ='".$variable."' ";
			break;		
			
			case "dataRoomBookingbyID":
				$cadena_sql="SELECT ";
				$cadena_sql.="rg.nombre NAME ";
				$cadena_sql.="FROM "; 
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."reserva_reservable rr ";
				$cadena_sql.="ON ";
				$cadena_sql.="rr.id_reserva = r.id_reserva ";				
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."reservable_grupo rg ";
				$cadena_sql.="ON ";
				$cadena_sql.="rr.id_reservable_grupo = rg.id_reservable_grupo ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="r.id_reserva ='".$variable."' ";
			break;
			
			case "deleteUnconfirmedBookingUser":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`cliente` ='".$variable."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="sesion_temp<>'' ";
				$cadena_sql.="AND ";
				$cadena_sql.="estado_reserva=1 ";
			break;	

			case "deleteUnconfirmedSession":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`sesion_temp` ='".$variable."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="`estado_reserva` ='1' "; 
			break;	

			case "deleteUnconfirmedBookingAll":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="sesion_temp<>'' ";
				$cadena_sql.="AND ";
				$cadena_sql.="`tiempo_expira_temp` < ".time()." ";
				$cadena_sql.="AND ";
				$cadena_sql.="`estado_reserva` ='1' ";

			break;	

			case "countRoomsByGroup":
				$cadena_sql="SELECT ";
				$cadena_sql.="count(id_reservable) ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reservableGrupo ='".$variable['groupRoom']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="tipo_reserva = '".$variable['commerce']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="estado = '1' ";
			break;
			
			case "updateDataUser":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."user "; 
				$cadena_sql.="SET ";
				if($variable['country']<>""){
					$cadena_sql.="pais_origen ='".$variable['country']."', ";
				}
				if($variable['name']<>""){
					$cadena_sql.="nombre ='".$variable['name']."', ";
				}
				if($variable['lastname']<>""){
					$cadena_sql.="apellido ='".$variable['lastname']."', ";
				}
				if($variable['dni']<>""){
					$cadena_sql.="identificacion ='".$variable['dni']."', ";
				}
				if($variable['phone']<>""){
					$cadena_sql.="telefono='".$variable['phone']."', ";
				}
				$cadena_sql.="estado='1' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="correo ='".$variable['email']."' ";
			break;	
			
			case "insertUser":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="( ";
				$cadena_sql.="`nombre`, ";
				$cadena_sql.="`apellido`, ";
				$cadena_sql.="`correo`, ";
				$cadena_sql.="`telefono`, ";
				$cadena_sql.="`usuario`, ";
				$cadena_sql.="`clave`, ";
				$cadena_sql.="`identificacion`, ";
				$cadena_sql.="`pais_origen`, ";
				$cadena_sql.="`estilo`, ";
				$cadena_sql.="`idioma`, ";
				$cadena_sql.="`estado` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['name']."', ";
				$cadena_sql.="'".$variable['lastname']."', ";
				$cadena_sql.="'".$variable['email']."', ";
				$cadena_sql.="'".$variable['phone']."', ";
				$cadena_sql.="'', ";
				$cadena_sql.="'', ";
				$cadena_sql.="'".$variable['dni']."', ";
				$cadena_sql.="'".$variable['country']."', ";
				$cadena_sql.="'default', ";
				$cadena_sql.="'es_es', ";
				$cadena_sql.="'1' ";
				$cadena_sql.=")";
				break;
        
      case "insertRole":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."user_role ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_usuario`, ";
				$cadena_sql.="`id_subsistema`, ";
				$cadena_sql.="`estado` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['user']."', ";
				$cadena_sql.="'3', ";
				$cadena_sql.="'1' ";
				$cadena_sql.=")";
				break;  
							
			case "getAllSession":
				$cadena_sql = "SELECT valor "; 
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."session ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="sesionId = '".$variable."' "; 
				break;
			
			case "setBookingSession":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."session ";
				$cadena_sql.="( ";
				$cadena_sql.="`sesionId`, ";
				$cadena_sql.="`variable`, ";
				$cadena_sql.="`valor`, ";
				$cadena_sql.="`expiracion` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable."', ";
				$cadena_sql.="'idUsuario', ";
				$cadena_sql.="'".time()."', "; 
				$cadena_sql.="'".(time()+86400)."' ";
				$cadena_sql.=")";
				break;	
				
			case "updateValueBookingbyID":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET "; 
				$cadena_sql.="valor_total ='".$variable['value']."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reserva ='".$variable['idbooking']."' ";
			break;		
			
			
			case "confirmBookingbyID":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="sesion_temp ='', ";
				$cadena_sql.="tiempo_expira_temp=fecha_fin, ";
				$cadena_sql.="estado_reserva=2, ";
				$cadena_sql.="cliente ='".$variable['customer']."', ";
				$cadena_sql.="valor_total ='".$variable['valueBooking']."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reserva ='".$variable['idbooking']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="estado_reserva = '1' ";
			break;	
			
			case "confirmBooking":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="sesion_temp ='', ";
				$cadena_sql.="tiempo_expira_temp=fecha_fin, ";
				if(isset($variable['user'])){
					$cadena_sql.="cliente ='".$variable['user']."', ";
				}	
				if(isset($variable['observation'])){
					$cadena_sql.="observacion_cliente='".$variable['observation']."', ";
				}	
				if(isset($variable['value'])){
					$cadena_sql.="valor_total ='".$variable['value']."', ";
				}
				$cadena_sql.="estado_reserva=2 ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="sesion_temp ='".$variable['session']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="estado_reserva = '1' ";
			break;	
			
			case "updateUserBooking":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="cliente ='".$variable['user']."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="sesion_temp ='".$variable['session']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="estado_reserva = '1' ";
			break;	

			case "valFiltersCommerceID":
				$cadena_sql="SELECT ";
				$cadena_sql.="fo.nombre NOMBRE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."filtro_opcion fo ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."commerce_filtrador trf ";
				$cadena_sql.="ON (fo.id_filtroOpcion = trf.id_filtroOpcion) ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="trf.id_tipoReserva ='".$variable['commerce']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="fo.id_filtroComponente = '".$variable['component']."' ";
			break;

			
			case "iniciarTransaccion":
				$cadena_sql="START TRANSACTION";
				break;

			case "finalizarTransaccion":
				$cadena_sql="COMMIT";
				break;

			case "cancelarTransaccion":
				$cadena_sql="ROLLBACK";
				break;

  
			case "eliminarTemp":

				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tempFormulario ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_sesion = '".$variable."' ";
				break;
				
			case "dataAllCommerce":  
				$cadena_sql="SELECT  ";
				$cadena_sql.=" GROUP_CONCAT(".$prefijo."commerce.id_tipoReserva) ID, ";
				$cadena_sql.=$prefijo."commerce.nombre NAME, ";
				$cadena_sql.=$prefijo."commerce.url URL, ";
				$cadena_sql.=$prefijo."commerce.facebook FACEBOOK, ";
				$cadena_sql.=$prefijo."commerce.correo EMAIL, ";
				$cadena_sql.=$prefijo."commerce.descripcion DESCRIPTION, ";
				$cadena_sql.=$prefijo."commerce.id_establecimiento BRANCH, ";
				$cadena_sql.=$prefijo."commerce.capacidad CAPACITY, ";
				$cadena_sql.=$prefijo."commerce.files_folder FILESFOLDER, ";
				$cadena_sql.=$prefijo."commerce.imagen IMAGE, ";
				$cadena_sql.=$prefijo."commerce.hora_inicio CHECKIN, ";
				$cadena_sql.=$prefijo."commerce.hora_cierre CHECKOUT, ";				
				$cadena_sql.=$prefijo."commerce.direccion ADDRESS, ";
				$cadena_sql.=$prefijo."commerce.latitud LATITUDE, ";
				$cadena_sql.=$prefijo."commerce.longitud LONGITUDE, ";
				$cadena_sql.=$prefijo."commerce.telefono PHONE ";
				$cadena_sql.="FROM "; 
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."commerce.estado=1 ";
				if($variable["all"]==""){
					$cadena_sql.="AND ";
					$cadena_sql.=$prefijo."commerce.id_tipoReserva IN ( ".$variable["commerces"]." ) ";
				}
				if($variable["commerceType"]<>""){
					$cadena_sql.="AND ";
					$cadena_sql.=$prefijo."commerce.id_claTipoReserva='".$variable["commerceType"]."' ";
				}
				if($variable["plan"]<>""){
					$cadena_sql.="AND ";
					$cadena_sql.=$prefijo."commerce.id_plan='".$variable["plan"]."' ";
				}
				$cadena_sql.="GROUP BY `id_establecimiento` ";
				$cadena_sql.="ORDER BY ".$prefijo."commerce.id_plan DESC, ".$prefijo."commerce.nombre ASC";
				break;	
				
			case "apiCommerceByID":  
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."commerce.api_key APIKEY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."commerce.id_tipoReserva ='".$variable."' ";
				break;			
				
			case "dataCommerceByID":  
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."commerce.nombre NAME, ";
				$cadena_sql.=$prefijo."commerce.descripcion DESCRIPTION, ";
				$cadena_sql.=$prefijo."commerce.telefono PHONE, ";
				$cadena_sql.=$prefijo."commerce.longitud LONGITUDE, ";
				$cadena_sql.=$prefijo."commerce.latitud LATITUDE, ";
				$cadena_sql.=$prefijo."commerce.facebook FACEBOOK, ";
				$cadena_sql.=$prefijo."commerce.datos_cuenta BANKACCOUNT, ";
				$cadena_sql.=$prefijo."commerce.files_folder FOLDER, ";
				$cadena_sql.=$prefijo."commerce.imagen LOGO, ";
				$cadena_sql.=$prefijo."commerce.correo EMAIL ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."commerce.id_tipoReserva ='".$variable."' ";
				break;	
        
       case "getAdditionalFields":
				$cadena_sql="SELECT  ";
				$cadena_sql.="id_field IDFIELD, ";
				$cadena_sql.="name NAME ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_fields  ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=" id_commerce='".$variable["commerce"]."' ";
        $cadena_sql.="ORDER BY id_field ASC "; 
				break; 

		}
		//echo "<br/>".$tipo."=".$cadena_sql;
		return $cadena_sql;

	}
}



?>
