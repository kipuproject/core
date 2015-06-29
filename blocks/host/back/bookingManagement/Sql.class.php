<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlbookingManagement extends sql {
	
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

		 
		switch($tipo) {
			 
			/**
			 * Clausulas específicas
			 */
			 
			case "searchRooms":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable IDROOM, ";
				$cadena_sql.="nombre NAME ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable ";
				$cadena_sql.="WHERE estado = 1 ";
				$cadena_sql.="AND tipo_reserva = '".$variable."'";
				$cadena_sql.="ORDER BY id_reservableGrupo,nombre ";  
				break;
				
			case "searchBusyRooms":
				$cadena_sql="SELECT  ";
				$cadena_sql.="id_reservable ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva  ";
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
				$cadena_sql.=$prefijo."reserva.tipo_reserva='".$variable["commerce"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva_reservable.id_reservable_grupo='".$variable["groupRoom"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.estado = 1 ";
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
				
			case "getFieldsAdditional":
				$cadena_sql="SELECT  ";
				$cadena_sql.="id_field IDFIELD, ";
				$cadena_sql.="name NAMEFIELD ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_fields  ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=" id_commerce='".$variable["commerce"]."' ";
				break;
				
			case "commerceByUser":
				$cadena_sql="SELECT ";
				$cadena_sql.="tr.id_tipoReserva IDCOMMERCE, ";
				$cadena_sql.="tr.nombre NAME ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce tr ";
				break;

				
			case "commerceByCompany":
				$cadena_sql="SELECT ";
				$cadena_sql.="tr.id_tipoReserva IDCOMMERCE, ";
				$cadena_sql.="tr.nombre NAME ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce tr ";
				$cadena_sql.="WHERE tr.id_establecimiento IN (".$variable.")";
				break;
				
				
			case "bookingsByNP":
				$cadena_sql="SELECT ";
				$cadena_sql.="CONCAT(r.fecha_inicio,'-',rr.id_reservable) IDCELL, ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="rr.id_reservable IDRESERVABLE, ";
				$cadena_sql.="fecha_inicio DATESTART, ";
				$cadena_sql.="fecha_fin	DATEEND, ";
				$cadena_sql.="cliente CUSTOMER, ";
				$cadena_sql.="numero_personas NUMGUEST, ";
				$cadena_sql.="numero_kids NUMKIDS, ";
				$cadena_sql.="estado_reserva STATEBOOKING, ";
				$cadena_sql.="estado_pago STATEPAYMENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."reserva_reservable rr ";
				$cadena_sql.="ON ";
				$cadena_sql.="r.id_reserva=rr.id_reserva  ";
				$cadena_sql.="WHERE fecha_inicio>=".(($variable['firstDay'])*1-860000)." "; //-10 dias
				$cadena_sql.="AND fecha_fin<=".(($variable['LastDay'])*1+2764800)." "; //+ 32 dias 
				$cadena_sql.="AND tipo_reserva=".$variable['commerce']." ";
				$cadena_sql.="AND estado_reserva NOT IN (1,3,4) ";
				$cadena_sql.="AND r.estado = 1 ";
				break;

			case "allBookingsByCompany":
				$cadena_sql="SELECT ";
				$cadena_sql.="CONCAT(r.fecha_inicio,'-',rr.id_reservable) IDCELL, ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="rr.id_reservable IDRESERVABLE, ";
				$cadena_sql.="rv.nombre NAMERESERVABLE, ";
				$cadena_sql.="er.nombre STATUS, ";
				$cadena_sql.="DATE_FORMAT(FROM_UNIXTIME( `fecha_registro` ),'%m/%d/%Y %H:%i') DATEREGISTER, ";
				$cadena_sql.="DATE_FORMAT(FROM_UNIXTIME( `fecha_inicio` ),'%m/%d/%Y') DATESTART, ";
				$cadena_sql.="DATE_FORMAT(FROM_UNIXTIME( `fecha_fin` ),'%m/%d/%Y') DATEEND, ";
				$cadena_sql.="cliente CUSTOMER, ";
				$cadena_sql.="medio SOURCE, ";
				$cadena_sql.="valor_total PAYMENT, ";
				$cadena_sql.="numero_personas NUMGUEST, ";
				$cadena_sql.="estado_reserva STATEBOOKING, ";
				$cadena_sql.="estado_pago STATEPAYMENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="INNER JOIN {$prefijo}reserva_reservable rr ON rr.id_reserva = r.id_reserva  ";
				$cadena_sql.="INNER JOIN {$prefijo}reservable rv ON rr.id_reservable = rv.id_reservable  ";
				$cadena_sql.="INNER JOIN {$prefijo}estado_reserva er ON er.id_estadoReserva = r.estado_reserva  ";
				$cadena_sql.="AND r.commerce IN (".$variable.") ";
				$cadena_sql.="AND r.estado_reserva NOT IN (5) ";
				$cadena_sql.=" AND r.estado = 1 ";
				
				break;
				
				
			case "typeBookingCommerce":
				$cadena_sql="SELECT ";
				$cadena_sql.="tr.metodo_reserva TYPE, ";
				$cadena_sql.="tr.intervalo_reserva INTERVALO, ";
				$cadena_sql.="tr.hora_inicio STARTBOOKING, ";
				$cadena_sql.="tr.hora_cierre ENDBOOKING ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce tr ";
				$cadena_sql.="WHERE tr.id_tipoReserva=".$variable;
				break;
				
			case "companyByUser":
				$cadena_sql="SELECT ";
				$cadena_sql.="u.id_usuario IDUSER, ";
				$cadena_sql.="e.id_establecimiento IDCOMPANY, ";
				$cadena_sql.="e.id_parent IDPARENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user u ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."usuario_establecimiento ue ";
				$cadena_sql.="ON ";
				$cadena_sql.="u.id_usuario = ue.id_usuario ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."establecimiento e ";
				$cadena_sql.="ON ";
				$cadena_sql.="ue.id_establecimiento = e.id_establecimiento ";
				$cadena_sql.="WHERE u.id_usuario=".$variable;
				break;

			case "detailPayment":
				$cadena_sql="SELECT ";
				$cadena_sql.="value VALUE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."payu_payment ";
				$cadena_sql.="WHERE system_reference=".$variable;
				$cadena_sql.=" AND status='1'";
				break;
				
			case "companyList":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_establecimiento IDCOMPANY, ";
				$cadena_sql.="id_parent IDPARENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."establecimiento ";
				$cadena_sql.="WHERE id_parent=".$variable;
				break;
				
			case "updateDataCompany":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."establecimiento ";
				$cadena_sql.="SET ";
				$cadena_sql.="nombre='".$variable['nombre']."',";
				$cadena_sql.="descripcion='".$variable['descripcion']."',";
				$cadena_sql.="contacto='".$variable['contacto']."',";
				$cadena_sql.="url='".$variable['url']."',";
				$cadena_sql.="email='".$variable['email']."',";
				$cadena_sql.="telefonos='".$variable['telefono']."',";
				$cadena_sql.="direccion='".$variable['direccion']."' ";
				$cadena_sql.="WHERE id_establecimiento=".$variable['optionValue'];

				break;

			case "updateDataCommerceBasic":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="SET ";
				$cadena_sql.="nombre='".$variable['nombre']."',";
				$cadena_sql.="descripcion='".$variable['descripcion']."',";
				$cadena_sql.="capacidad='".$variable['capacidad']."',";
				$cadena_sql.="direccion='".$variable['direccion']."' ";
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;

			case "updateDataCommerceTime":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="SET ";
				$cadena_sql.="intervalo_reserva='".$variable['intervalo']."' ";
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;

			case "assignRoom":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="SET ";
				$cadena_sql.="id_reservable='".$variable['room']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['booking'];
				break;
				
			case "assignTypeRoom":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="SET ";
				$cadena_sql.="id_reservable_grupo='".$variable['typeroom']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['booking'];
				break;
			
			case "updateValue":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="valor_total='".$variable['value']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['booking'];
				break;
				
			case "updatePaymentValue":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="valor_pagado='".$variable['payment']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['booking'];
				break;
				
			case "updateObservation":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET "; 
				$cadena_sql.="observacion='".$variable['observation']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['booking'];
				break;
				
			case "inactiveBooking":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="estado='0' ";
				$cadena_sql.="WHERE id_reserva=".$variable['IDBOOKING'];
				break;
			 
			case "activeBooking":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="estado='1' ";
				$cadena_sql.="WHERE id_reserva=".$variable['IDBOOKING'];
				break;
				
			case "updateGuest":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva_guest ";
				$cadena_sql.="SET ";
				$cadena_sql.="id_reserva='".$variable['newBooking']."' ";
				$cadena_sql.="WHERE id_reserva='".$variable['oldBooking']."' ";
				break;	
				
			case "updatePayment":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva_payu_payment ";
				$cadena_sql.="SET ";
				$cadena_sql.="system_reference='".$variable['newBooking']."' ";
				$cadena_sql.="WHERE system_reference='".$variable['oldBooking']."' ";
				break;
				
				
			case "assignStatus":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="estado_reserva='".$variable['status']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['booking'];
				break;
				
			case "assignStatusPayment":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="estado_pago='".$variable['paymentstatus']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['booking'];
				break;

      case "getOnlineValue":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_payu_reference IDPAYMENT, ";
				$cadena_sql.="value VALUE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."payu_payment ";
				$cadena_sql.="WHERE system_reference=".$variable['booking'];
				$cadena_sql.=" AND id_commerce=".$variable['commerce'];
				break;	
				
			case "assignOnlineValue":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."payu_payment ";
				$cadena_sql.="SET ";
				$cadena_sql.="value='".$variable['onlinepayment']."' ";
				$cadena_sql.="WHERE system_reference=".$variable['booking'];
				break;
				
			case "insertOnlineValue":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."payu_payment(id_user,id_commerce,system_reference,value,currency,description,status,answer)  ";
				$cadena_sql.="VALUES( ";
				$cadena_sql.="'".$variable['user']."',";
				$cadena_sql.="'".$variable['commerce']."',";
				$cadena_sql.="'".$variable['booking']."',";
				$cadena_sql.="'".$variable['onlinepayment']."',";
				$cadena_sql.="'".$variable['currency']."',";
				$cadena_sql.="'".$variable['description']."',";
				$cadena_sql.="'1',";
				$cadena_sql.="'".$variable['answer']."') ";
				break;		
				
			case "insertLog":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."logger ";
				$cadena_sql.="VALUES( ";
				$cadena_sql.="'".$variable['user']."',";
				$cadena_sql.="'".$variable['event']."',";
				$cadena_sql.="'".time()."') ";
				break;
			case "deleteDataCommerceFeatures":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce_filtrador  ";
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;

			case "insertDataCommerceFeatures":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."commerce_filtrador  ";
				$cadena_sql.="VALUES( ";
				$cadena_sql.="'".$variable['optionValFeature']."',";
				$cadena_sql.="'".$variable['optionValue']."',";
				$cadena_sql.="'1'";
				$cadena_sql.=") ";
				break;


			case "companyListbyID":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_establecimiento IDCOMPANY, ";
				$cadena_sql.="id_parent IDPARENT, ";
				$cadena_sql.="nombre NOMBRE, ";
				$cadena_sql.="descripcion DESCRIPCION, ";
				$cadena_sql.="contacto CONTACTO, ";
				$cadena_sql.="url URL, ";
				$cadena_sql.="email EMAIL, ";
				$cadena_sql.="telefonos TELEFONOS, ";
				$cadena_sql.="direccion DIRECCION ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."establecimiento ";
				$cadena_sql.="WHERE id_establecimiento IN (".$variable.") ";
				$cadena_sql.="AND estado<>0";
				break;


			case "commerceListbyCompany":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_tipoReserva IDCOMMERCE, ";
				$cadena_sql.="id_establecimiento IDCOMPANY, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="tr.id_claTipoReserva IDTYPE, ";
				$cadena_sql.="(SELECT nombre FROM {$prefijo}clasificacion_tipo_reseva ctr WHERE ctr.id_claTipoReserva=tr.id_claTipoReserva) NAMETYPE, ";
				$cadena_sql.="capacidad CAPACITY, ";
				$cadena_sql.="metodo_reserva METHOD, ";
				$cadena_sql.="descripcion DESCRIPTION, ";
				$cadena_sql.="intervalo_reserva INTERVALO, ";
				$cadena_sql.="telefono PHONES, ";
				//$cadena_sql.="url URL, ";
				//$cadena_sql.="email EMAIL, ";
				//$cadena_sql.="telefonos TELEFONOS, ";
				$cadena_sql.="direccion ADDRESS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce tr ";
				$cadena_sql.="WHERE id_establecimiento IN (".$variable.") ";
				$cadena_sql.="AND estado<>0";
				break;

			case "companyListAll":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_establecimiento IDCOMPANY, ";
				$cadena_sql.="id_parent IDPARENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."establecimiento ";
				break;
				
			case "typeRooms":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable_grupo IDTYPEROOM, ";
				$cadena_sql.="nombre NAME ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable_grupo ";
				$cadena_sql.="WHERE estado=1 ";
				break;
			
			
			case "commerceFilterList":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_filtroOpcion IDOPTION ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce_filtrador ";
				$cadena_sql.="WHERE id_tipoReserva IN (".$variable.") ";
				$cadena_sql.="AND estado<>0";
				break;
				
			case "detailBooking":
				$cadena_sql="SELECT ";
				$cadena_sql.="FROM_UNIXTIME(r.fecha_inicio) FECHA_INICIO, ";
				$cadena_sql.="FROM_UNIXTIME((r.fecha_fin)+2) FECHA_FIN, ";
				$cadena_sql.="r.fecha_inicio FECHA_INICIO_UNIX, ";
				$cadena_sql.="r.fecha_fin FECHA_FIN_UNIX, ";
				$cadena_sql.="r.observacion OBSERVATION, ";
				$cadena_sql.="r.observacion_cliente OBSERVATION_CUSTOMER, ";
				$cadena_sql.="r.valor_pagado VALUEPAYMENT, ";
				$cadena_sql.="r.numero_personas NUMGUEST, ";
				$cadena_sql.="r.numero_kids NUMKIDS, ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="r.estado_reserva STATUS, ";
				$cadena_sql.="r.valor_total VALUEBOOKING, ";
				$cadena_sql.="r.medio MEDIO, ";
				$cadena_sql.="r.estado_pago PAYMENT, ";
				$cadena_sql.="rr.id_reservable_grupo ROOMTYPE, ";
				$cadena_sql.="rr.id_reservable ROOM, ";
				$cadena_sql.="r.cliente CLIENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."reserva_reservable rr ";
				$cadena_sql.="ON ";
				$cadena_sql.="r.id_reserva=rr.id_reserva  ";
				$cadena_sql.="WHERE 1=1 "; 
				$cadena_sql.="AND ";
				if($variable['IDCELL']<>""){
					$cadena_sql.="CONCAT(r.fecha_inicio,'-',rr.id_reservable,'-',r.fecha_inicio) ='".$variable['IDCELL']."' ";
				}elseif($variable['IDBOOKING']<>""){
					$cadena_sql.="r.id_reserva='".$variable['IDBOOKING']."' ";
				}
				$cadena_sql.="AND ";
				$cadena_sql.="r.tipo_reserva ='".$variable['COMMERCE']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="r.estado_reserva NOT IN ('3','4','5') "; //no canceladas,finalizadas,bloqueadas
				$cadena_sql.=" AND r.estado = 1 ";
			break;
			
			case "allUsers":
				$cadena_sql="SELECT ";
				$cadena_sql.="CONCAT(u.nombre,' ',u.apellido)  NAMECLIENT, ";
				$cadena_sql.="u.id_usuario  IDUUSER, ";
				$cadena_sql.="u.correo  EMAILCLIENT, ";
				$cadena_sql.="u.identificacion  DNI, ";
				$cadena_sql.="u.pais_origen  COUNTRY, ";
				$cadena_sql.="u.telefono  PHONECLIENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user u ";
				
			break;
			
			
			case "detailBookingGuest":
				$cadena_sql="SELECT ";
				$cadena_sql.="u.id_guest IDUSER, ";
				$cadena_sql.="CONCAT(u.nombre,' ',u.apellido)  NAMECLIENT, ";
				$cadena_sql.="u.correo  EMAILCLIENT, ";
				$cadena_sql.="u.identificacion  DNI, ";
				$cadena_sql.="u.pais_origen  COUNTRY, ";
				$cadena_sql.="u.telefono  PHONECLIENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_guest rg ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."guest u ";
				$cadena_sql.="ON ";
				$cadena_sql.="rg.id_usuario = u.id_guest ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="rg.id_reserva ='".$variable['IDBOOKING']."' ";
			break;
			
			case "insertBookingItems":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_reserva`, ";
				$cadena_sql.="`id_reservable_grupo`, ";
				$cadena_sql.="`id_reservable` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['id_reserva']."', ";
				$cadena_sql.="(SELECT id_reservableGrupo FROM {$prefijo}reservable WHERE id_reservable='".$variable['id_reservable']."'), ";
				$cadena_sql.="'".$variable['id_reservable']."' ";
				$cadena_sql.=")";
				break;
				
			case "blockBooking":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="( ";
				$cadena_sql.="`fecha_inicio`, ";
				$cadena_sql.="`fecha_fin`, ";
				$cadena_sql.="`commerce`, ";
				$cadena_sql.="`establecimiento`, ";
				$cadena_sql.="`cliente`, ";
				$cadena_sql.="`valor_total`, ";
				$cadena_sql.="`fecha_registro`, ";
				$cadena_sql.="`numero_personas`, ";
				$cadena_sql.="`usuario_registro`, ";
				$cadena_sql.="`sesion_temp`, ";
				$cadena_sql.="`tiempo_expira_temp`, ";
				$cadena_sql.="`estado_reserva`, ";
				$cadena_sql.="`estado_pago` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['timeStampIni']."', ";
				$cadena_sql.="'".$variable['timeStampFin']."', ";
				$cadena_sql.="'".$variable['commerce']."', ";
				$cadena_sql.="(SELECT id_establecimiento FROM {$prefijo}commerce WHERE id_tipoReserva=".$variable['commerce']."), "; //pendiente
				$cadena_sql.="'".$variable['user']."', ";
				$cadena_sql.="'0', ";
				$cadena_sql.="'".time()."', ";
			    $cadena_sql.="'999999999', ";
				$cadena_sql.="'".$variable['user']."', ";
				$cadena_sql.="' ', ";
				$cadena_sql.="'0', "; //POR DEFECTO CADA RESERVA SE GUARDARA 15 MINUTOS SI NO SE FINALIZA CORRECTAMENTE
				$cadena_sql.="'5', ";
				$cadena_sql.="'0' ";
				$cadena_sql.=")";
				break;
				
			case "unblockBooking":
				$cadena_sql="DELETE FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="fecha_inicio='".$variable['timeStampIni']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="tipo_reserva='".$variable['commerce']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="estado_reserva='5' "; 

				break;	

			case "serviceListbyCommerce":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_servicio IDSERVICE, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="TRIM(descripcion) DESCRIPTION, ";
				$cadena_sql.="online ONLINE, ";
				$cadena_sql.="dinamico DYNAMIC, ";
				$cadena_sql.="id_reservable_grupo IDTYPEROOM, ";
				$cadena_sql.="pago_unico ONPAYMENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."servicio ";
				$cadena_sql.="WHERE id_comercio=".$variable['commerce'];
				$cadena_sql.=" AND estado='1'";
				$cadena_sql.=" ORDER BY nombre";
				break;
				
			case "searchService":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_servicio FROM ";
				$cadena_sql.=$prefijo."reserva_servicio ";
				$cadena_sql.="WHERE id_reserva=".$variable['idbooking'];
				$cadena_sql.=" AND id_servicio=".$variable['is'];
				break;		
				
			case "serviceListbyBooking":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_servicio ID,  ";
				$cadena_sql.="cantidad CANT,  ";
				$cadena_sql.="valor VALUE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_servicio ";
				$cadena_sql.="WHERE id_reserva=".$variable['idbooking'];
				break;
				
			case "insertServices":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva_servicio ";
				$cadena_sql.="( ";
				$cadena_sql.="`id_reserva`, ";
				$cadena_sql.="`id_servicio`, ";
				$cadena_sql.="`cantidad`, ";
				$cadena_sql.="`valor` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['idbooking']."', ";
				$cadena_sql.="'".$variable['is']."', ";
				$cadena_sql.="'".$variable['cs']."', ";
				$cadena_sql.="'".$variable['vs']."' ";
				$cadena_sql.=")";
				break;		
				
			case "updateServices":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva_servicio ";
				$cadena_sql.="SET ";
				$cadena_sql.="cantidad='".$variable['cs']."', ";
				$cadena_sql.="valor='".$variable['vs']."' ";
				$cadena_sql.="WHERE id_reserva=".$variable['idbooking'];
				$cadena_sql.=" AND id_servicio=".$variable['is'];
			break;
			
				
			case "deleteService":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_servicio ";
				$cadena_sql.="WHERE id_reserva=".$variable['idbooking'];
				$cadena_sql.=" AND id_servicio=".$variable['is'];
				break;
				
				
		}

		//echo "<br/><br/>$tipo=".$cadena_sql;

		return $cadena_sql;

	}
}
?>
