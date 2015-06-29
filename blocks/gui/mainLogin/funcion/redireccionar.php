<?php
if(!isset($GLOBALS["autorizado"])){
	include("index.php");
	exit;
}else{

	$miPaginaActual=$this->miConfigurador->getVariableConfiguracion("pagina");

	switch($opcion){

		case "indexUsuario":
			if($miPaginaActual=="index" or $miPaginaActual=="cpanel"){
				$variable="pagina=".$valor['PAGINA'];
			}else{
				$variable="pagina=".$miPaginaActual;
			}
			$variable.="&opcionLogin=".$valor['OPCION'];
			$variable.="&saramodule=".$valor['MODULE'];
			
		break;

		case "index":
			$variable="pagina=index";
			$variable.="&mensaje=".$valor;

		break;
	
		case "paginaPrincipal":
			$variable="pagina=index";
			$variable.="&usuario=".$valor;
			$variable.="&mensaje=fallo Registro";
		break;
	}

	foreach($_REQUEST as $clave=>$valor){
		unset($_REQUEST[$clave]);
	}

	$enlace=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."?".$this->miConfigurador->getVariableConfiguracion("enlace");
	$variable=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable,$enlace);

	echo "<script>location.replace('".$variable."')</script>";  

}