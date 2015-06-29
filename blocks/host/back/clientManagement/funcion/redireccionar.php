<?php
if(!isset($GLOBALS["autorizado"])){
	include("index.php");
	exit;
}else{

	$miPaginaActual=$this->miConfigurador->getVariableConfiguracion("pagina");
	switch($option)
	{

		case "confirmarNuevo":
			$variable="pagina=".$miPaginaActual;
      $variable.="&saramodule=host";
			$variable.="&opcion=confirmar";
			if($valor!=""){
				$variable.="&id_sesion=".$valor;
			}
			break;

		case "confirmacionEditar":
			$variable="pagina=clientManagement";
			$variable.="&opcion=confirmarEditar";
      $variable.="&saramodule=host";
			if($valor!=""){
				$variable.="&registro=".$valor;
			}
			break;

		case "exitoRegistro":
			$variable="pagina=clientManagement";
			$variable.="&tema=admin";
      $variable.="&saramodule=host";
			$variable.="&opcion=nuevo";
			$variable.="&mensaje=".$valor[0];
			break;

		case "falloRegistro":
			$variable="pagina=clientManagement";
			$variable.="&tema=admin";
      $variable.="&saramodule=host";
			$variable.="&option=new";
			$variable.="&mensaje=".$valor[0];
			foreach($valor[1] as $clave=>$contenido){
				$variable.="&".$clave."=".$contenido;
			}
			break;

		case "exitoEdicion":
			$variable="pagina=clientManagement";
			$variable.="&opcion=mostrar";
      $variable.="&saramodule=host";
			$variable.="&mensaje=exitoEdicion";
			break;

		case "falloEdicion":
			$variable="pagina=clientManagement";
			$variable.="&opcion=mostrar";
      $variable.="&saramodule=host";
			$variable.="&mensaje=falloRegistro";
			break;

		case "paginaPrincipal":
			$variable="pagina=index";
			break;


	}

	foreach($_REQUEST as $clave=>$valor)	{
		unset($_REQUEST[$clave]);
	}

	$enlace=$this->miConfigurador->getVariableConfiguracion("enlace");
	$variable=$this->miConfigurador->fabricaConexiones->crypto->codificar($variable);

	$_REQUEST[$enlace]=$variable;
	$_REQUEST["recargar"]=true;

}

?>