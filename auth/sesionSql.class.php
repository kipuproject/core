<?php
require_once("core/connection/Sql.class.php");	
class sesionSql extends Sql{

	var $cadena_sql;
	var $miConfigurador;

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		return 0;
	}

	function getCadenaSql($indice,$parametro){
		$this->clausula($indice, $parametro);
		if(isset($this->cadena_sql[$indice])){
			return $this->cadena_sql[$indice];
		}
		return false;
	}

	private function clausula($indice,$parametro){

		switch ($indice){
				
			case "seleccionarPagina":
				$this->cadena_sql[$indice]="SELECT ";
				$this->cadena_sql[$indice].="rol ";
				$this->cadena_sql[$indice].="FROM ";
				$this->cadena_sql[$indice].=$this->miConfigurador->getVariableConfiguracion("prefijo")."page ";
				$this->cadena_sql[$indice].="WHERE ";
				$this->cadena_sql[$indice].="nombre='".$parametro."' ";
				$this->cadena_sql[$indice].="LIMIT 1";
				break;
				
			case "actualizarSesion":
				$this->cadena_sql[$indice]="UPDATE ";
				$this->cadena_sql[$indice].=$this->miConfigurador->getVariableConfiguracion("prefijo")."session ";
				$this->cadena_sql[$indice].="SET ";
				$this->cadena_sql[$indice].="expiracion=".(time()+$parametro["expiracion"])." ";
				$this->cadena_sql[$indice].="WHERE ";
				$this->cadena_sql[$indice].="sesionId='".$parametro["sesionId"]."' ";
				break;
				
			case "borrarVariableSesion":
				$this->cadena_sql[$indice]="DELETE ";
				$this->cadena_sql[$indice]="FROM ";
				$this->cadena_sql[$indice]=	$this->miConfigurador->getVariableConfiguracion("prefijo")."session ";
				$this->cadena_sql[$indice]="WHERE ";
				$this->cadena_sql[$indice]="sesionId='".$parametro["sesionId"]." ";
				$this->cadena_sql[$indice]="AND variable='".$parametro["dato"]."'";
				
				break;
				
			case "borrarSesionesExpiradas":
				$this->cadena_sql[$indice]="DELETE ";
				$this->cadena_sql[$indice].="FROM ";
				$this->cadena_sql[$indice].=$this->miConfigurador->getVariableConfiguracion("prefijo")."session ";
				$this->cadena_sql[$indice].="WHERE ";
				$this->cadena_sql[$indice].="expiracion<".time();
				break;	

			case "buscarValorSesion":
				$this->cadena_sql[$indice] = "SELECT * ";
				$this->cadena_sql[$indice].="FROM ";
				$this->cadena_sql[$indice].=$this->miConfigurador->getVariableConfiguracion("prefijo")."session ";
				$this->cadena_sql[$indice].="WHERE ";
				$this->cadena_sql[$indice].="sesionId = '".$parametro["sesionId"]."' "; 
				$this->cadena_sql[$indice].="AND variable='".$parametro["variable"]."'";
				break;
				
			case "actualizarValorSesion":
				$this->cadena_sql[$indice] = "UPDATE ";
				$this->cadena_sql[$indice].= $this->miConfigurador->getVariableConfiguracion("prefijo")."session ";
				$this->cadena_sql[$indice].= "SET ";
				$this->cadena_sql[$indice].="valor='".$parametro["valor"]."', ";
				$this->cadena_sql[$indice].="expiracion='".$parametro["expiracion"]."' ";
				$this->cadena_sql[$indice].="WHERE ";
				$this->cadena_sql[$indice].="sesionId='".$parametro["sesionId"]."' ";
				$this->cadena_sql[$indice].="AND variable='".$parametro["variable"]."'";
				break;
				
			case "insertarValorSesion":
				$this->cadena_sql[$indice] = "INSERT INTO ";
				$this->cadena_sql[$indice].=  $this->miConfigurador->getVariableConfiguracion("prefijo")."session ";
				$this->cadena_sql[$indice].= "( ";
				$this->cadena_sql[$indice].= " sesionId, ";
				$this->cadena_sql[$indice].= " variable, ";
				$this->cadena_sql[$indice].= " valor,";
				$this->cadena_sql[$indice].= " expiracion";
				$this->cadena_sql[$indice].= ") ";
				$this->cadena_sql[$indice].= "VALUES ";
				$this->cadena_sql[$indice].= "(";
				$this->cadena_sql[$indice].= "'".$parametro["sesionId"]."', ";
				$this->cadena_sql[$indice].= "'".$parametro["variable"]."', ";
				$this->cadena_sql[$indice].= "'".$parametro["valor"]."', ";
				$this->cadena_sql[$indice].= "'".$parametro["expiracion"]."' ";
				$this->cadena_sql[$indice].= ")";
				break;
		}
		//echo "<br/>".$indice."=".$this->cadena_sql[$indice];
	}
}"<br/>".$indice."=".$this->cadena_sql[$indice];
	}
}
