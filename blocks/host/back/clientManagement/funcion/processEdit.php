<?php
if(!isset($GLOBALS["autorizado"])){
	include("index.php");
	exit;
}else{
	$cadena_sql=$this->sql->cadena_sql("updateData",$_REQUEST);
	$result=$this->masterResource->ejecutarAcceso($cadena_sql,"");
	$this->mensaje="Datos Actualizados Correctamente";
	return $this->status=FALSE;
}
?>