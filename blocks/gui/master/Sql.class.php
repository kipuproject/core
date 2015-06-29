<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class Sqlmaster extends sql {
	
	
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
			 
			case "userList":
				$cadena_sql="SELECT ";
				$cadena_sql.="u.id_usuario ID, ";
				$cadena_sql.="CONCAT(u.nombre,' ',apellido) NOMBRE,";
				$cadena_sql.="GROUP_CONCAT( DISTINCT (eu.id_establecimiento))  EMPRESA, ";				 
				$cadena_sql.="correo CORREO, ";
				$cadena_sql.="GROUP_CONCAT( DISTINCT (s.nombre) ) ROL, ";				 				 
				$cadena_sql.="u.estado ESTADO ";				 
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user u ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."user_role us ";
				$cadena_sql.="ON ";
				$cadena_sql.="u.id_usuario = us.id_usuario ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."role s ";
				$cadena_sql.="ON ";
				$cadena_sql.="us.id_subsistema = s.id_subsistema ";
				$cadena_sql.="LEFT JOIN ";
				$cadena_sql.=$prefijo."user_commerce eu ";
				$cadena_sql.="ON ";
				$cadena_sql.="u.id_usuario = eu.id_usuario ";
				$cadena_sql.="GROUP BY u.id_usuario ";
				break;

			case "userByID":
				$cadena_sql="SELECT ";
				$cadena_sql.="u.id_usuario ID, ";
				$cadena_sql.="u.nombre NOMBRE,";
				$cadena_sql.="u.apellido APELLIDO,";
				$cadena_sql.="u.correo CORREO, ";
				$cadena_sql.="u.estado ESTADO ";				 
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user u ";
				$cadena_sql.="WHERE u.id_usuario=".$variable;
				break;
				
			case "userListByID":
				$cadena_sql="SELECT ";
				$cadena_sql.="u.id_usuario ID, ";
				$cadena_sql.="u.nombre NOMBRE,";
				$cadena_sql.="u.apellido APELLIDO,";
				$cadena_sql.="GROUP_CONCAT( DISTINCT (eu.id_establecimiento))  EMPRESA, ";				 
				$cadena_sql.="correo CORREO, ";
				$cadena_sql.="GROUP_CONCAT( DISTINCT (s.nombre) ) ROL, ";				 				 
				$cadena_sql.="u.estado ESTADO ";				 
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user u ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."user_role us ";
				$cadena_sql.="ON ";
				$cadena_sql.="u.id_usuario = us.id_usuario ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."subsistema s ";
				$cadena_sql.="ON ";
				$cadena_sql.="us.id_subsistema = s.id_subsistema ";
				$cadena_sql.="LEFT JOIN ";
				$cadena_sql.=$prefijo."usuario_establecimiento eu ";
				$cadena_sql.="ON ";
				$cadena_sql.="u.id_usuario = eu.id_usuario ";
				$cadena_sql.="WHERE u.id_usuario=".$variable;
				$cadena_sql.=" GROUP BY u.id_usuario ";
				break;

			case "roleList":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_subsistema ID, ";
				$cadena_sql.="nombre ROL ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."role ";
				break;

			case "menuList":
				$cadena_sql="SELECT ";
				$cadena_sql.="m.nombre NOMBRE, ";
				$cadena_sql.="m.padre PADRE, ";
				$cadena_sql.="m.rol ROL, ";
				$cadena_sql.="m.tema TEMA, ";
				$cadena_sql.="m.lenguaje IDIOMA, ";
				$cadena_sql.="m.parametro PARAMETRO, ";
				$cadena_sql.="m.icono ICONO, ";
				$cadena_sql.="(SELECT p.nombre FROM {$prefijo}pagina p WHERE p.id_pagina=m.id_pagina ) PAGINA, ";
				$cadena_sql.="m.id_menu IDMENU ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."menu m ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."role s ";
				$cadena_sql.="ON ";
				$cadena_sql.="m.rol = s.id_subsistema ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="m.rol ='".$variable."' ";
				break;
				
			case "commerceByUser":
				$cadena_sql="SELECT ";
				$cadena_sql.="c.id_tipoReserva IDCOMMERCE, "; 
				$cadena_sql.="c.id_claTipoReserva TYPECOMMERCE, ";
				$cadena_sql.="c.nombre NAME, ";
				$cadena_sql.="c.nombre_sucursal NAMEBRANCH, ";
				$cadena_sql.="c.dbms DBMS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user_commerce uc ";
				$cadena_sql.="INNER JOIN ".$prefijo."commerce c ";
				$cadena_sql.="ON uc.id_commerce=c.id_tipoReserva ";
				//$cadena_sql.="WHERE c.estado='1' ";
				$cadena_sql.="AND uc.id_user ='".$variable."' ";
				$cadena_sql.="ORDER BY c.nombre ASC ";
				break;	
				
			case "commerceTypes":
				$cadena_sql="SELECT ";
				$cadena_sql.="cc.id_type IDTYPE, ";
				$cadena_sql.="cc.nombre NAME ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce_type cc ";
				$cadena_sql.="WHERE cc.estado='1' ";
				$cadena_sql.="ORDER BY cc.id_type ASC ";
				break;	 
			
			case "commerceList":
				$cadena_sql="SELECT ";
				$cadena_sql.="c.id_tipoReserva IDCOMMERCE, "; 
				$cadena_sql.="c.id_claTipoReserva TYPECOMMERCE, ";
				$cadena_sql.="c.nombre NAME, ";
				$cadena_sql.="c.dbms DBMS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce c ";
				$cadena_sql.="WHERE c.estado='1' ";
				$cadena_sql.="AND c.id_claTipoReserva ='".$variable."' ";
				$cadena_sql.="ORDER BY c.nombre ASC ";
				break;	 
				
			case "dataCommerce":
				$cadena_sql="SELECT ";
				$cadena_sql.="c.id_tipoReserva IDCOMMERCE, ";
				$cadena_sql.="c.id_claTipoReserva TYPECOMMERCE, ";
				$cadena_sql.="c.nombre NAME, ";
				$cadena_sql.="c.dbms DBMS ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."commerce c ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."user_commerce uc ";
				$cadena_sql.="ON "; 
				$cadena_sql.="c.id_tipoReserva = uc.id_commerce ";
				$cadena_sql.="WHERE c.estado='1' ";
				$cadena_sql.="AND uc.id_user='".$variable['user']."' ";
				$cadena_sql.="AND uc.id_commerce='".$variable['commerce']. "' ";
				$cadena_sql.="ORDER BY c.nombre ASC ";
				break;
		}

		return $cadena_sql;

	}
}
?>
