<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");


class SqlmainLogin extends sql {
	
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
			 
			case "dataUserByID":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_usuario USUARIOID, ";
				$cadena_sql.="nombre NOMBRE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_usuario ='".$variable."'";
				break;
				
			case "buscarUsuarioAplicativo":
				$cadena_sql="SELECT ";
				$cadena_sql.=$prefijo."user.id_usuario USUARIOID, ";
				$cadena_sql.=$prefijo."user.clave CLAVE, ";
				$cadena_sql.=$prefijo."user.usuario, ";
				$cadena_sql.=$prefijo."user_role.id_subsistema ROL, ";
				$cadena_sql.=$prefijo."user_role.estado, ";
				$cadena_sql.=$prefijo."user.estilo TEMA, ";
				$cadena_sql.=$prefijo."user.idioma IDIOMA, ";
				$cadena_sql.=$prefijo."page.nombre PAGINA, ";
				$cadena_sql.=$prefijo."page.modulo MODULE, ";
				$cadena_sql.=$prefijo."user.tipo ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user, ";
        $cadena_sql.=$prefijo."page, ";
				$cadena_sql.=$prefijo."role, ";
				$cadena_sql.=$prefijo."user_role ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."user.usuario='".$variable["usuario"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."user.clave='".$variable['clave']."'  ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."user_role.id_subsistema=".$prefijo."role.id_subsistema ";
        $cadena_sql.="AND ";
				$cadena_sql.=$prefijo."page.id_pagina=".$prefijo."role.id_pagina ";
				$cadena_sql.="AND "; 
				$cadena_sql.=$prefijo."user_role.estado=1 ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."user.id_usuario=".$prefijo."user_role.id_usuario ";	
				break;

			case "buscarIndexUsuario":
				$cadena_sql="SELECT ";
				$cadena_sql.=$prefijo."user.id_usuario USUARIOID, ";
				$cadena_sql.=$prefijo."user.usuario, ";
				$cadena_sql.=$prefijo."user_role.id_subsistema ROL, ";
				$cadena_sql.=$prefijo."user_role.estado, ";
				$cadena_sql.=$prefijo."user.estilo TEMA, ";
				$cadena_sql.=$prefijo."user.idioma IDIOMA, ";
				$cadena_sql.=$prefijo."role.pagina PAGINA, ";
				$cadena_sql.=$prefijo."user.tipo ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user, ";
				$cadena_sql.=$prefijo."role, ";
				$cadena_sql.=$prefijo."user_role ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."user.id_usuario='".$variable["usuario"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."user_role.id_subsistema=".$prefijo."role.id_subsistema ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."user_role.estado=1 ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."user.id_usuario=".$prefijo."user_role.id_usuario ";	
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

		}
		//echo "<br/>".$tipo."=".$cadena_sql;
		return $cadena_sql;

	}
}
?>
