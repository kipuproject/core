<?php

//Evitar un acceso directo a este archivo
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

//Todo bloque debe implementar la interfaz Bloque
include_once("core/builder/Bloque.interface.php");
include_once("core/manager/Configurador.class.php");
//Interfaz gráfica
include_once("Html.class.php");
//Funciones de procesamiento de datos
include_once("Funcion.class.php");
//Compilación de clausulas SQL utilizadas por el bloque
include_once("Sql.class.php");
//Mensajes
include_once("Lenguaje.class.php");
//api
include_once("Api.class.php");


//Para evitar redefiniciones de clases el nombre de la clase del archivo bloque debe corresponder al nombre del bloque
//precedida por la palabra Bloque
if(class_exists('BloquegestionReserva') === false){
	class BloquegestionReserva implements Bloque
	{

		var $nombreBloque;
		var $miFuncion;
		var $miSql;
		var $miConfigurador;

		public function __construct($esteBloque,$lenguaje="")
		{

			//El objeto de la clase Configurador debe ser único en toda la aplicación
			$this->miConfigurador=Configurador::singleton();


			$ruta=$this->miConfigurador->getVariableConfiguracion("raizDocumento");
			$rutaURL=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site");

			if($esteBloque["grupo"]==""){
				$ruta.="/blocks/".$esteBloque["nombre"]."/";
				$rutaURL.="/blocks/".$esteBloque["nombre"]."/";
			}else{
				$ruta.="/blocks/".$esteBloque["grupo"]."/".$esteBloque["nombre"]."/";
				$rutaURL.="/blocks/".$esteBloque["grupo"]."/".$esteBloque["nombre"]."/";
			}
				
			$this->miConfigurador->setVariableConfiguracion("rutaBloque",$ruta);
			$this->miConfigurador->setVariableConfiguracion("rutaUrlBloque",$rutaURL);

			$nombreClaseFuncion="Funcion".$esteBloque["nombre"];
			$this->miFuncion=new $nombreClaseFuncion();
			
			$nombreAPI="Api".$esteBloque["nombre"];
			$this->api=new $nombreAPI();

			$nombreClaseSQL="Sql".$esteBloque["nombre"];
			$this->miSql=new $nombreClaseSQL();

			$nombreClaseFrontera="Frontera".$esteBloque["nombre"];
			$this->miHTML=new $nombreClaseFrontera();

			$nombreClaseLenguaje="Lenguaje".$esteBloque["nombre"];
			$this->miLenguaje=new $nombreClaseLenguaje();

		}

		public function bloque(){
		
				$html=array();

				if(!isset($_REQUEST['action'])){
					$this->miHTML->setFuncion($this->miFuncion);
					$this->miHTML->setLenguaje($this->miLenguaje);
					$this->miHTML->setSql($this->miSql);
					$this->miHTML->html();
					
				}elseif($_REQUEST['api']){
					$this->api->setSql($this->miSql); 
					$resultado=$this->api->process();
					
				}else{
					$this->miFuncion->setSql($this->miSql);
					$resultado=$this->miFuncion->action();
				}

		}
		
	}
}


$unBloque["nombre"]="gestionReserva";
$unBloque["grupo"]="host/general";

$estaClase="Bloque".$unBloque["nombre"];

$this->miConfigurador->setVariableConfiguracion("esteBloque",$unBloque);

if(isset($lenguaje)){

	$esteBloque=new $estaClase($unBloque,$lenguaje);
	
}else{

	$esteBloque=new $estaClase($unBloque);
}

	$esteBloque->bloque();


?>
