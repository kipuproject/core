<?php
if(!isset($GLOBALS["autorizado"])){
	include("index.php");
	exit;
}else{

	$this->mensaje="";
	
	$cadena_sql=$this->sql->cadena_sql("updateDataTypeRoom",$variable);
	$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
	
  //Si la capacidad es compartida solo se necesitan los valores para 1 invitado
  if($variable['capacity-type']=='C'){
    $variable['capacity']=1;  
  }
  
  
	//busco los q tienen precios de invitados mayor a la capacidad actual
	//y los elimino
	
	$cadena_sql=$this->sql->cadena_sql("deletePricesOverCapacity",$variable);
	$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");

	//busco si existe la capacidad si no la inserto con 0 pesos
	// El invitado corresponde al valor de niños
	$guest=0;
	

	for($guest;$guest<=$variable['capacity'];$guest++){

		$variable['guest']=$guest;
		$cadena_sql=$this->sql->cadena_sql("priceListbyGuest",$variable);
		$priceListbyGuest=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
		
		if(!is_array($priceListbyGuest)){
		
			for($i=1;$i<=4;$i++){
				$variable['season']=$i;
				$cadena_sql=$this->sql->cadena_sql("createPrices",$variable);
				$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");		
			}
		}
	}
	$this->mensaje.="Capacidad Actualizada";
				
	return $this->status=FALSE;

}
?>