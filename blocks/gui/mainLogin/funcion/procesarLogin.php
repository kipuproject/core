<?php
if(!isset($GLOBALS["autorizado"])){
	include("index.php");
	exit;
}else{
		
	//1. Verificar que el usuario esté registrado en el sistema
	$token=strrev(($this->miConfigurador->getVariableConfiguracion("enlace")));

	if(isset($_REQUEST[$token."usuario"]) && isset($_REQUEST[$token."clave"])){

		$variable["usuario"]=$_REQUEST[$token."usuario"];
    $variable["clave"]=md5($_REQUEST[$token."clave"]);
	
    $cadena_sql=$this->sql->cadena_sql("buscarUsuarioAplicativo",$variable);
    $registro=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
   
    if(is_array($registro)){
  		//Tener en cuenta que la clausla SQL deberá generar un campo llamado CLAVE y uno llamado USUARIOID
			//if($this->miConfigurador->fabricaConexiones->crypto->decodificar($registro[0]["CLAVE"])==$variable["clave"]){
			if($registro[0]["CLAVE"]==$variable["clave"]){
		
				//1. Crear una sesión de trabajo
				
				$this->miSesion->tema=$registro[0]["TEMA"];
				$this->miSesion->rol=$registro[0]["ROL"];
				$this->miSesion->idioma=$registro[0]["IDIOMA"];
				$registro[0]["SESION"]=$this->miSesion->crearSesion($registro[0]["USUARIOID"]);
				$registro[0]["OPCION"]="account";

				//Redirigir a la página principal del usuario 				
				$this->funcion->redireccionar("indexUsuario",$registro[0]);
				return true;				
			}			
		}	
		// Redirigir a la página de inicio con mensaje de error en usuario/clave	
    
    $this->funcion->redireccionar("index",$variable["usuario"]);
	}
}