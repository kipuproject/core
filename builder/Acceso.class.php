<?php
require_once("core/manager/Configurador.class.php");
require_once("core/builder/builderSql.class.php");


class Acceso {

	private static $instance;

    public static function singleton() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    private function __construct() {
      $this->miConfigurador = Configurador::singleton();
		  $this->generadorClausulas = BuilderSql::singleton();
		  $conexion = "master";
		  $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    }

    public function getPermissions($permission,$rol){
		  $cadena = $this->generadorClausulas->cadenaSql("getAccess",array('permission'=>$permission,'rol'=>$rol));
		  $result = $this->miRecursoDB->ejecutarAcceso($cadena,"busqueda");
		  $r=0;
  		while(isset($result[$r][0])){
  			$output[$result[$r][0]] = (boolean)$result[$r][1];
  		$r++;
		}
		return $output;
	}


}
