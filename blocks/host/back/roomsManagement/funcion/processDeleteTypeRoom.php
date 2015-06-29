<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("index.php");
	exit;
}else{

	$cadena_sql=$this->sql->cadena_sql("deleteTypeRoom",$variable);
	$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

	$formSaraData="pagina=roomsManagement";
	$formSaraData.="&option=listTypeRooms";
	$formSaraData.="&saramodule=host";
	$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);
	
	echo "<script>location.replace('".$formSaraData."')</script>";

}
?>