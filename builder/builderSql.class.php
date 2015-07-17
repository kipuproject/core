<?php
include_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/core/connection/Sql.class.php");

class BuilderSql extends Sql{

	var $cadena_sql;
	var $miConfigurador;

	private static $instance;

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		return 0;
	}

	public static function singleton()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	function cadenaSql($indice,$parametro=""){
		$this->clausula($indice, $parametro);
		if(isset($this->cadena_sql[$indice])){
			return $this->cadena_sql[$indice];
		}
		return false;
	}

	private function clausula($indice,$parametro){

		$prefijo=$this->miConfigurador->getVariableConfiguracion("prefijo");

		switch ($indice){
			case "apiparams":
				$cadena_sql="SELECT  ";
				$cadena_sql.="param PARAM ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."api ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="name = '".$parametro."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="status='1'";
				break;

			case "getAccess":
				$cadena_sql="SELECT  ";
				$cadena_sql.="tp.identificador, ";
				$cadena_sql.="p.valor ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tipo_permiso tp ";
				$cadena_sql.="INNER JOIN ";
				$cadena_sql.=$prefijo."permisos p ";
				$cadena_sql.="ON tp.id_permiso=p.id_permiso ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="tp.identificador IN (".$parametro['permission'].") ";
				$cadena_sql.="AND ";
				$cadena_sql.="p.rol='".$parametro['rol']."'";

				break;

			case "usuario":
				$cadena_sql="SELECT  ";
				$cadena_sql.="usuario, ";
				$cadena_sql.="nombre NOMBRE, ";
				$cadena_sql.="apellido APELLIDO, ";
				$cadena_sql.="estilo ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."user ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_usuario='".$parametro."'";
				break;

			case "page":
				$cadena_sql="SELECT  ";
				$cadena_sql.=$prefijo."block_page.*,";
				$cadena_sql.=$prefijo."bloque.nombre, ";
				$cadena_sql.=$prefijo."page.parametro ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."page, ";
				$cadena_sql.=$prefijo."block_page, ";
				$cadena_sql.=$prefijo."bloque ";
				$cadena_sql.="WHERE ";
				$cadena_sql.=$prefijo."page.nombre='".$parametro."' ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."block_page.id_bloque=".$prefijo."bloque.id_bloque ";
				$cadena_sql.="AND ";
				$cadena_sql.=$prefijo."block_page.id_pagina=".$prefijo."page.id_pagina";
				break;

			case "bloquesPagina":
				$cadena_sql="SELECT  ";
				$cadena_sql.="bp.*,";
				$cadena_sql.="b.nombre ,";
				$cadena_sql.="p.parametro, ";
				$cadena_sql.="b.grupo ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."page p, ";
				$cadena_sql.=$prefijo."block_page bp, ";
				$cadena_sql.=$prefijo."block b ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="p.nombre='".$parametro['page']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="p.modulo='".$parametro['module']."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="bp.id_bloque=b.id_bloque ";
				$cadena_sql.="AND ";
				$cadena_sql.="bp.id_pagina=p.id_pagina ";
				$cadena_sql.="ORDER BY bp.seccion,bp.seccion ";
				break;
		}
		//echo "<br/><br/>{$indice}->".$cadena_sql;// exit;
		if(isset($cadena_sql)){
			$this->cadena_sql[$indice]=$cadena_sql;
		}
	}
}
?>
