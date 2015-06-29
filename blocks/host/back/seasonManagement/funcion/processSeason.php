<?php
if(!isset($GLOBALS["autorizado"])){
	include("index.php");
	exit;
	
}else{
	$changes=0;

	$day=explode(",",$variable['days']);
	if(is_array($day)){
		foreach($day as $value){
			
			$variable['day']=$value;
			
			if($value<>""){
			
				$cadena_sql=$this->sql->cadena_sql("searchDay",$variable);
				$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
				
				if(is_array($result)){
					$cadena_sql=$this->sql->cadena_sql("updateDay",$variable);
					$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
				}else{	
					$cadena_sql=$this->sql->cadena_sql("insertDay",$variable);
					$result=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"");
				}
				
				$changes++;
			}
		}
	}
	
	if($changes>0){
		$this->mensaje[] = "- El calendario se actualizo correctamente";
	}else{
		$this->mensaje[] = "- No se realizaron cambios";
	}	
						
	return $this->status=FALSE;				
}
?>