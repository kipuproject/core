<?php
if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlPayu extends sql {
	
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
								

			
			case "iniciarTransaccion":
				$cadena_sql="START TRANSACTION";
				break;

			case "finalizarTransaccion":
				$cadena_sql="COMMIT";
				break;

			case "cancelarTransaccion":
				$cadena_sql="ROLLBACK";
				break;

			case "searchTransaction":
				$cadena_sql="SELECT  ";
				$cadena_sql.="pc.merchant_id MERCHANTID, ";
				$cadena_sql.="pc.account_id ACCOUNTID, ";
				$cadena_sql.="pp.system_reference SYSTEMREFERENCE, ";
				$cadena_sql.="pp.description DESCRIPTION, ";
				$cadena_sql.="pp.id_payu_reference IDPAYMENT, ";
				$cadena_sql.="pp.value VALUE, ";
				$cadena_sql.="pp.id_commerce IDCOMMERCE, ";
				$cadena_sql.="pp.currency CURRENCY, ";
				$cadena_sql.="pc.confirmationURL CONFIRMATIONURL, ";
				$cadena_sql.="pc.responseURL RESPONSEURL, ";
				$cadena_sql.="pc.api_key APIKEY ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."payu_payment pp ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."payu_config pc ";
				$cadena_sql.="ON (pp.id_commerce = pc.id_commerce) ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="pp.id_payu_reference='".$variable."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="pp.status=0 ";
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
   
		}
		//echo "<br/>".$tipo."=".$cadena_sql;
		return $cadena_sql;

	}
}
