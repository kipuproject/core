<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlinternalBooking extends sql {
	
	
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
			 
			case "buscarCruceReserva":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."reservable.id_reservable ID_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.nombre NOMBRE_RESERVABLE, ";
				$cadena_sql.="FROM_UNIXTIME(".$prefijo."reserva.fecha_inicio) FECHA_INICIO, ";
				$cadena_sql.="FROM_UNIXTIME(".$prefijo."reserva.fecha_fin) FECHA_FIN ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="ON (".$prefijo."reserva_reservable.id_reserva = ".$prefijo."reserva.id_reserva) ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."reservable ";
				$cadena_sql.="ON (".$prefijo."reserva_reservable.id_reservable = ".$prefijo."reservable.id_reservable) ";				
				$cadena_sql.="WHERE ";
				
				$cadena_sql.="( ";
				$cadena_sql.=$prefijo."reserva.fecha_inicio BETWEEN '".$variable["timestampInicio"]."' AND '".$variable["timestampFin"]."' ";
				$cadena_sql.=" OR ";				
				$cadena_sql.=$prefijo."reserva.fecha_fin BETWEEN '".$variable["timestampInicio"]."' AND '".$variable["timestampFin"]."' ";
				$cadena_sql.=" OR ";
				$cadena_sql.="	( ";
				$cadena_sql.=$prefijo."reserva.fecha_inicio < '".$variable["timestampInicio"]."' ";
				$cadena_sql.="	AND ";
				$cadena_sql.=$prefijo."reserva.fecha_fin > '".$variable["timestampFin"]."' ";
				$cadena_sql.="	) ";
				$cadena_sql.=") ";
				
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva_reservable.id_reservable IN ( ".$variable["cadena_reservables"]." ) ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.estado_reserva NOT IN (3,4) "; //la reserva no contenga los estados FINALIZADO Y CANCELADO
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.establecimiento=".$variable["establecimiento"]." ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.tipo_reserva=".$variable["tipo_reserva"]." ";
				break;
				
			case "buscarCruceCapacidadMaximaReserva":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."reserva.numero_personas NUMERO_PERSONAS, ";
				$cadena_sql.="FROM_UNIXTIME(".$prefijo."reserva.fecha_inicio) FECHA_INICIO, ";
				$cadena_sql.="FROM_UNIXTIME(".$prefijo."reserva.fecha_fin) FECHA_FIN ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="( ";
				$cadena_sql.=$prefijo."reserva.fecha_inicio BETWEEN '".$variable["timeStampIni"]."' AND '".$variable["timeStampEnd"]."' ";
				$cadena_sql.=" OR ";				
				$cadena_sql.=$prefijo."reserva.fecha_fin BETWEEN '".$variable["timeStampIni"]."' AND '".$variable["timeStampEnd"]."' ";
				$cadena_sql.=" OR ";
				$cadena_sql.="	( ";
				$cadena_sql.=$prefijo."reserva.fecha_inicio < '".$variable["timeStampIni"]."' ";
				$cadena_sql.="	AND ";
				$cadena_sql.=$prefijo."reserva.fecha_fin > '".$variable["timeStampEnd"]."' ";
				$cadena_sql.="	) ";
				$cadena_sql.=") ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.estado_reserva NOT IN (3,4) "; //la reserva no contenga los estados FINALIZADO Y CANCELADO
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.establecimiento=".$variable["company"]." ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reserva.tipo_reserva=".$variable["commerce"]." ";
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
				$cadena_sql.=$prefijo."reserva.estado='1'";
				break;		
				
			case "buscarReservables":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."reservable.id_reservable ID_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.nombre NOMBRE_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.descripcion DESCRIPCION_RESERVABLE, ";
				$cadena_sql.=$prefijo."reservable.grupo GRUPO_RESERVABLE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."reservable.tipo_reserva = '".$variable["tipo_reserva"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reservable.establecimiento >= '".$variable["establecimiento"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."reservable.estado<>0 ";
				
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
				
			case "dataCommerce": 
				$cadena_sql="SELECT  ";
				$cadena_sql.="c.id_tipoReserva ID_TIPORESERVA, ";
				$cadena_sql.="c.api_key APIKEY, ";
				$cadena_sql.="c.nombre NOMBRE_TIPORESERVA, ";
				$cadena_sql.="c.descripcion DESCRIPCION_TIPORESERVA, ";
				$cadena_sql.="c.id_establecimiento ID_ESTABLECIMIENTO, ";
				$cadena_sql.="c.metodo_reserva METODO, ";
				$cadena_sql.="c.capacidad CAPACIDAD, ";
				$cadena_sql.="c.imagen IMAGEN, ";
				$cadena_sql.="c.hora_inicio STARTBOOKING, ";
				$cadena_sql.="c.hora_cierre ENDBOOKING, ";				
				$cadena_sql.="c.files_folder FOLDER, ";	
				$cadena_sql.="c.intervalo_reserva INTERVALO ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce c ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="c.id_tipoReserva='".$variable."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="c.estado<>0 ";
				break;
			
			case "buscarInformacionTiposReservaNombre":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."tipo_reserva.id_tipoReserva ID_TIPORESERVA, ";
				$cadena_sql.=$prefijo."tipo_reserva.nombre NOMBRE_TIPORESERVA, ";
				$cadena_sql.=$prefijo."tipo_reserva.descripcion DESCRIPCION_TIPORESERVA, ";
				$cadena_sql.=$prefijo."tipo_reserva.id_establecimiento ID_ESTABLECIMIENTO, ";
				$cadena_sql.=$prefijo."tipo_reserva.metodo_reserva METODO, ";
				$cadena_sql.=$prefijo."tipo_reserva.capacidad CAPACIDAD, ";
				$cadena_sql.=$prefijo."tipo_reserva.imagen IMAGEN ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tipo_reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="UPPER(".$prefijo."tipo_reserva.nombre) like '%".$variable["TipoReservaNombre"]."%'  ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."tipo_reserva.estado<>0 ";
				break;
				
				
			case "insertBooking":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="( ";
				$cadena_sql.="`medio`, ";
				$cadena_sql.="`fecha_inicio`, ";
				$cadena_sql.="`fecha_fin`, ";
				$cadena_sql.="`tipo_reserva`, ";
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
				$cadena_sql.="'".$variable['medioBooking']."', ";
				$cadena_sql.="'".$variable['timeStampStart']."', ";
				$cadena_sql.="'".$variable['timeStampEnd']."', ";
				$cadena_sql.="'".$variable['commerce']."', ";
				$cadena_sql.="'".$variable['company']."', ";
				$cadena_sql.="'".$variable['user']."', ";
				$cadena_sql.="'".$variable['valueBooking']."', ";
				$cadena_sql.="'".time()."', ";
				$cadena_sql.="'".(($variable['guestBooking'])*1+($variable['kids'])*1)."', ";
				$cadena_sql.="'".$variable['user']."', ";
				$cadena_sql.="'', ";
				$cadena_sql.="'".((time())+1800)."', "; //POR DEFECTO CADA RESERVA SE GUARDARA 30 MINUTOS SI NO SE FINALIZA CORRECTAMENTE
				$cadena_sql.="'2', ";
				$cadena_sql.="'0' ";
				$cadena_sql.=")";
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
				$cadena_sql.="'".$variable['groupRoom']."', ";
				$cadena_sql.="'".$variable['idRoom']."' ";
				$cadena_sql.=")";
				break;
				
			case "dataBookingItems":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable_grupo IDGROUP ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva_reservable ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reserva ='".$variable."' ";
				break;
				
			case "dataItems":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_reservable IDRESERVABLE, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="id_reservableGrupo IDGROUP ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reservable ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="estado ='1' ";
				$cadena_sql.="AND ";
				$cadena_sql.="tipo_reserva ='".$variable["commerce"]."' ";
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
				$cadena_sql.=$prefijo."tipo_reserva.id_tipoReserva ID_TIPORESERVA ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tipo_reserva ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador ";
				$cadena_sql.="ON (".$prefijo."tipo_reserva.id_tipoReserva = ".$prefijo."tipo_reserva_filtrador.id_tipoReserva) ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."tipo_reserva.estado=1 ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador.estado=1 ";
				if($variable['filtros']<>""){
				  $cadena_sql.="AND ";
				  $cadena_sql.=$prefijo."tipo_reserva_filtrador.id_filtroOpcion IN ({$variable['filtros']}) ";
				}
				break;

			case "dataUserByEmail":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_usuario USUARIOID, ";
				$cadena_sql.="nombre NOMBRE, ";
				$cadena_sql.="apellido APELLIDO, ";
				$cadena_sql.="telefono TELEFONO, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="fecha_nacimiento BIRTHDAY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."usuario ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`correo` ='".$variable."' ";
			break;

			
			case "dataUserByIden":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_usuario USUARIOID, ";
				$cadena_sql.="nombre NOMBRE, ";
				$cadena_sql.="apellido APELLIDO, ";
				$cadena_sql.="telefono TELEFONO, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="telefono PHONE, ";
				$cadena_sql.="pais_origen COUNTRY, ";
				$cadena_sql.="fecha_nacimiento BIRTHDAY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."usuario ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`identificacion` ='".$variable."' ";
			break;
			
			
			case "dataGroupReservable":
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
				$cadena_sql.=$prefijo."usuario ";
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
				$cadena_sql.=$prefijo."usuario ";
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
				$cadena_sql.="AND ";
				$cadena_sql.="`guest` ='".$variable['guest']."' ";
				break;
				
			case "activeBooking":
				$cadena_sql="SELECT ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="r.tipo_reserva IDCOMMERCE, ";
				$cadena_sql.="r.fecha_inicio STARTBOOKING, ";
				$cadena_sql.="r.fecha_fin ENDBOOKING ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="r.sesion_temp ='".$variable['session']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="r.estado_reserva ='1' ";
				
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
				break;	
				
			
			case "dataUserBooking":
				$cadena_sql="SELECT ";
				$cadena_sql.="r.id_reserva IDBOOKING, ";
				$cadena_sql.="FROM_UNIXTIME(r.fecha_inicio) FECHA_INICIO, ";
				$cadena_sql.="r.numero_personas NUMGUEST, ";
				$cadena_sql.="r.valor_total VALUE, ";
				$cadena_sql.="tr.id_tipoReserva IDCOMMERCE, ";
				$cadena_sql.="tr.nombre NAMECOMMERCE, ";
				$cadena_sql.="e.email EMAILCOMPANY, ";
				$cadena_sql.="e.telefonos PHONE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva r ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."tipo_reserva tr ";
				$cadena_sql.="ON ";
				$cadena_sql.="tr.id_tipoReserva = r.tipo_reserva ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."establecimiento e ";
				$cadena_sql.="ON ";
				$cadena_sql.="e.id_establecimiento = tr.id_establecimiento ";			
				$cadena_sql.="WHERE ";
				$cadena_sql.="r.sesion_temp ='".$variable['session']."' ";
			break;
			
			case "deleteUnconfirmedBookingUser":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`cliente` ='".$variable."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="sesion_temp<>'' ";
			break;	

			case "deleteUnconfirmedBookingAll":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="sesion_temp<>'' ";
				$cadena_sql.="AND ";
				$cadena_sql.="`tiempo_expira_temp` < ".time()." ";

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
				$cadena_sql.=$prefijo."usuario ";
				$cadena_sql.="SET ";
				$cadena_sql.="nombre ='".$variable['nameCustomer']."', ";
				$cadena_sql.="fecha_nacimiento ='".$variable['dateCustomer']."', ";
				$cadena_sql.="pais_origen ='".$variable['countryCustomer']."', ";
				$cadena_sql.="telefono='".$variable['phoneCustomer']."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="identificacion ='".$variable['idCustomer']."' ";

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
				$cadena_sql.="estado_reserva=2, ";
				$cadena_sql.="cliente ='".$variable['user']."', ";
				$cadena_sql.="valor_total ='".$variable['valueBooking']."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="sesion_temp ='".$variable['session']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="estado_reserva = '1' ";

			break;	
			
			case "updateUserBooking":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="cliente='".$variable['cliente']."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reserva ='".$variable['id_reserva']."' ";
			break;
			
			case "updateMedioBooking":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."reserva ";
				$cadena_sql.="SET ";
				$cadena_sql.="medio='".$variable['medioBooking']."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_reserva ='".$variable['idbooking']."' ";
			break;

			case "valFiltersCommerceID":
				$cadena_sql="SELECT ";
				$cadena_sql.="fo.nombre NOMBRE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."filtro_opcion fo ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador trf ";
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

		}
		//echo "<br/>".$tipo."=".$cadena_sql;
		return $cadena_sql;

	}
}



?>
