<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlclientManagement extends sql {
	
	
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
			 
			case "dataByID":
				$cadena_sql="SELECT ";
				$cadena_sql.="a.id_usuario ID, ";
				$cadena_sql.="a.nombre NAME, ";
				$cadena_sql.="a.apellido LASTNAME, ";
				$cadena_sql.="a.identificacion DNI, ";
				$cadena_sql.="a.pais_origen COUNTRY, ";
				$cadena_sql.="a.correo EMAIL, ";
				$cadena_sql.="a.telefono PHONE, ";
				$cadena_sql.="a.estado STATUS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user a ";
				$cadena_sql.="WHERE a.estado IN ('0','1') ";
				$cadena_sql.="AND a.id_usuario='".$variable."'";
				break;
				
			case "dataList":
				$cadena_sql="SELECT ";
				$cadena_sql.="a.id_usuario ID, ";
				$cadena_sql.="a.nombre NAME, ";
				$cadena_sql.="a.apellido LASTNAME, ";
				$cadena_sql.="a.identificacion DNI, ";
				$cadena_sql.="a.pais_origen COUNTRY, ";
				$cadena_sql.="a.correo EMAIL, ";
				$cadena_sql.="a.telefono PHONE, ";
				$cadena_sql.="a.estado STATUS ";
				$cadena_sql.="FROM "; 
				$cadena_sql.=$prefijo."user a ";
				$cadena_sql.="INNER JOIN ".$prefijo."user_role r  ";
        $cadena_sql.="ON a.id_usuario=r.id_usuario  ";
        $cadena_sql.="WHERE r.id_subsistema=3  ";
				break;

			case "DeleteData":
				$cadena_sql="DELETE FROM ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_usuario='".$variable."' ";
				break;
		
			case "updateData":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="SET ";
				$cadena_sql.="`nombre` = '".$variable["name"]."', ";
				$cadena_sql.="`apellido` = '".$variable["lastname"]."', ";
				$cadena_sql.="`identificacion` = '".$variable["dni"]."', ";
				$cadena_sql.="`pais_origen` = '".$variable["country"]."', ";
				$cadena_sql.="`correo` = '".$variable["email"]."', ";
				$cadena_sql.="`telefono` = '".$variable["phone"]."', ";
				$cadena_sql.="`estado` = '".$variable["status"]."'  ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`id_usuario` =".$_REQUEST["optionValue"]." ";
				break;
							
			case "insertData":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="( ";
 				$cadena_sql.="`nombre`, ";
				$cadena_sql.="`apellido`, ";
				$cadena_sql.="`identificacion`, ";
				$cadena_sql.="`pais_origen`, ";
				$cadena_sql.="`correo`, ";
				$cadena_sql.="`telefono`, ";
				$cadena_sql.="`estado` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="'".$variable["name"]."', ";
				$cadena_sql.="'".$variable["lastname"]."', ";
				$cadena_sql.="'".$variable["dni"]."', ";
				$cadena_sql.="'".$variable["country"]."', ";
				$cadena_sql.="'".$variable["email"]."', ";
				$cadena_sql.="'".$variable["phone"]."', ";
				$cadena_sql.="'1' ";
				$cadena_sql.=")";
				break;
			
		}
		//echo "<br/><br/>".$cadena_sql;
		return $cadena_sql;
	}
}
?>