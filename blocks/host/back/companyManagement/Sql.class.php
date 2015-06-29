<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlcompanyManagement extends sql {
	
	
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

			case "insertCompany":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."establecimiento ";
				$cadena_sql.="( ";
				$cadena_sql.="`nombre`, ";
				$cadena_sql.="`descripcion`, ";
				$cadena_sql.="`contacto`, ";
				$cadena_sql.="`url`, ";
				$cadena_sql.="`email`, ";
				$cadena_sql.="`telefonos`, ";
				$cadena_sql.="`id_parent`, ";
				$cadena_sql.="`direccion` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['nameCompany']."', ";
				$cadena_sql.="'".$variable['description']."', ";
				$cadena_sql.="'".$variable['manager']."', ";
				$cadena_sql.="' ', ";
				$cadena_sql.="'".$variable['email']."',";
				$cadena_sql.="'".$variable['phone']."',";
				$cadena_sql.="'999', ";
				$cadena_sql.="'".$variable['address']."'";
				$cadena_sql.=")";
				break;

			case "insertCommerce":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="( ";
				$cadena_sql.="`nombre`, ";
				$cadena_sql.="`descripcion`, ";
				$cadena_sql.="`id_establecimiento`, ";
				$cadena_sql.="`id_plan`, ";
				$cadena_sql.="`id_claTipoReserva`, ";
				$cadena_sql.="`files_folder` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable['nameCommerce']."', ";
				$cadena_sql.="'".$variable['description']."', ";
				$cadena_sql.="'".$variable['idCompany']."', ";
				$cadena_sql.="'".$variable['plan']."', ";
				$cadena_sql.="'".$variable['typeCommerce']."', ";
				$cadena_sql.="'".$variable['files_folder']."' ";
				$cadena_sql.=")";
				break;
				
			case "companyByUser":
				$cadena_sql="SELECT ";
				$cadena_sql.="u.id_usuario IDUSER, ";
				$cadena_sql.="e.id_establecimiento IDCOMPANY, ";
				$cadena_sql.="e.id_parent IDPARENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."usuario u ";
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

			case "companyList":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_establecimiento IDCOMPANY, ";
				$cadena_sql.="id_parent IDPARENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."establecimiento ";
				$cadena_sql.="WHERE id_parent=".$variable;
				break;
				
			case "categoryListCommerce":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_claTipoReserva IDCATCOMMERCE, ";
				$cadena_sql.="nombre NAME ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."clasificacion_tipo_reseva ";
				$cadena_sql.="WHERE estado='1'";
				break;

				
			case "updateDataCompany":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."establecimiento ";
				$cadena_sql.="SET ";
				$cadena_sql.="nombre='".$variable['nombre']."',";
				$cadena_sql.="descripcion='".$variable['descripcion']."',";
				$cadena_sql.="contacto='".$variable['contacto']."',";
				$cadena_sql.="url='".$variable['url']."',";
				//$cadena_sql.="email='".$variable['email']."',";
				$cadena_sql.="telefonos='".$variable['telefono']."',";
				$cadena_sql.="direccion='".$variable['direccion']."' ";
				$cadena_sql.="WHERE id_establecimiento=".$variable['optionValue'];

				break;

			case "updateDataCommerceBasic":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="SET ";
				$cadena_sql.="id_claTipoReserva='".$variable['commercetype']."',";
				$cadena_sql.="nombre='".$variable['nombre']."',";
				$cadena_sql.="descripcion='".$variable['descripcion']."',";
				$cadena_sql.="capacidad='".$variable['capacidad']."',";
				$cadena_sql.="correo='".$variable['email']."',";
				$cadena_sql.="url='".$variable['url']."',";
				$cadena_sql.="facebook='".$variable['facebook']."',";
				$cadena_sql.="direccion='".$variable['direccion']."', ";
				$cadena_sql.="id_plan='".$variable['plan']."', ";
				$cadena_sql.="telefono='".$variable['telefono']."', ";
				$cadena_sql.="imagen='".$variable['fileImage']."', ";
				//$cadena_sql.="menu='".$variable['urlmenu']."', ";
				$cadena_sql.="estado='".$variable['commercestatus']."', ";
				$cadena_sql.="latitud='".$variable['latitude']."', ";
				$cadena_sql.="longitud='".$variable['longitude']."' ";
				
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;
			
			
			case "updateDataCommerceMenu":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="SET ";
				$cadena_sql.="menu='".$variable['menu']."' ";
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;
				
			case "updateDataCommerceLogo":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="SET ";
				$cadena_sql.="imagen='".$variable['logo']."' ";
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;
				
				
			case "updateDataCommerceTime":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."commerce ";
				$cadena_sql.="SET ";
				$cadena_sql.="politicas_pago='".$variable['ppago']."', ";
				$cadena_sql.="hora_inicio='".$variable['horapertura']."', ";
				$cadena_sql.="hora_cierre='".$variable['horacierre']."', ";
				$cadena_sql.="politicas_especiales='".$variable['pespeciales']."' ";
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;

			case "deleteDataCommerceFeatures":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador  ";
				$cadena_sql.="WHERE id_tipoReserva=".$variable['optionValue'];
				break;

			case "deleteDataCompany":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."establecimiento  ";
				$cadena_sql.="WHERE id_establecimiento=".$variable['optionValue'];
				break;

			case "deleteDataCommmerce":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce  ";
				$cadena_sql.="WHERE id_establecimiento=".$variable['optionValue'];
				break; 
				
			case "insertDataCommerceFeatures":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador  ";
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
				$cadena_sql.="hora_inicio STARTTIME, ";
				$cadena_sql.="hora_cierre ENDTIME, ";
				$cadena_sql.="descripcion DESCRIPTION, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="url URL, ";
				$cadena_sql.="facebook FACEBOOK, ";
				$cadena_sql.="intervalo_reserva INTERVALO, ";
				$cadena_sql.="id_plan PLAN, ";
				$cadena_sql.="horario HOURLABEL, ";
				$cadena_sql.="telefono PHONES, ";
				$cadena_sql.="imagen IMAGE, ";
				
				$cadena_sql.="files_folder FILEFOLDER, ";
				$cadena_sql.="menu MENU, ";
				$cadena_sql.="latitud LATITUDE, ";
				$cadena_sql.="longitud LONGITUDE, ";
				
				//$cadena_sql.="telefonos TELEFONOS, ";
				$cadena_sql.="direccion ADDRESS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce tr ";
				$cadena_sql.="WHERE id_establecimiento IN (".$variable.") ";
				$cadena_sql.="AND estado<>0";
				break;
				
			case "commercebyID":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_tipoReserva IDCOMMERCE, ";
				$cadena_sql.="id_establecimiento IDCOMPANY, ";
				$cadena_sql.="nombre NAME, ";
				$cadena_sql.="tr.id_claTipoReserva IDTYPE, ";
				$cadena_sql.="capacidad CAPACITY, ";
				$cadena_sql.="metodo_reserva METHOD, ";
				$cadena_sql.="hora_inicio STARTTIME, ";
				$cadena_sql.="hora_cierre ENDTIME, ";
				$cadena_sql.="politicas_pago PPAGO, ";
				$cadena_sql.="politicas_especiales PESPECIALES, ";
				$cadena_sql.="descripcion DESCRIPTION, ";
				$cadena_sql.="correo EMAIL, ";
				$cadena_sql.="url URL, ";
				$cadena_sql.="facebook FACEBOOK, ";
				$cadena_sql.="intervalo_reserva INTERVALO, ";
				$cadena_sql.="id_plan PLAN, ";
				$cadena_sql.="horario HOURLABEL, ";
				$cadena_sql.="telefono PHONES, ";
				$cadena_sql.="imagen IMAGE, ";
				
				$cadena_sql.="files_folder FILEFOLDER, ";
				$cadena_sql.="api_key APIKEY, ";
				$cadena_sql.="menu MENU, ";
				$cadena_sql.="latitud LATITUDE, ";
				$cadena_sql.="longitud LONGITUDE, ";
				$cadena_sql.="estado STATUS, ";
				
				//$cadena_sql.="telefonos TELEFONOS, ";
				$cadena_sql.="direccion ADDRESS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce tr ";
				$cadena_sql.="WHERE id_tipoReserva = '".$variable."' ";
				break;

			case "companyListAll":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_establecimiento IDCOMPANY, ";
				$cadena_sql.="id_parent IDPARENT ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."establecimiento ";
				break;

			case "commerceFilterList":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_filtroOpcion IDOPTION ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tipo_reserva_filtrador ";
				$cadena_sql.="WHERE id_tipoReserva IN (".$variable.") ";
				$cadena_sql.="AND estado<>0";
				break;

		}

		//echo "<br/><br/>$tipo=".$cadena_sql;

		return $cadena_sql;

	}
}
?>
