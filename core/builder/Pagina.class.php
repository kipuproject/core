<?php
ini_set ('display_errors','1');
/**
 * Pagina.class.php
 * Implementa el patrón Fachada para el paquete builder.
*/

require_once("core/manager/Configurador.class.php");
require_once("core/builder/builderSql.class.php");
require_once("core/builder/ArmadorPagina.class.php");
require_once("core/builder/ProcesadorPagina.class.php");
include_once("core/crypto/Encriptador.class.php");

class Pagina{

	var $miConfigurador;
	var $recursoDB;
	var $pagina;
	var $generadorClausulas;
	var $tipoError;
	var $armadorPagina;
	var $cripto;

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		$this->generadorClausulas=BuilderSql::singleton();
		$this->armadorPagina=new ArmadorPagina();
		$this->procesadorPagina=new ProcesadorPagina();
		$this->cripto=Encriptador::singleton();
		$this->raizDocumentos=$this->miConfigurador->getVariableConfiguracion("raizDocumento");
	}

	function inicializarPagina($pagina){

		$this->recursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB("configuracion");

		if($this->recursoDB){

			$this->especificar_pagina($pagina);

			//SANITIZE_STRING
			parse_str(implode("&",$_REQUEST),$matriz);
			foreach($matriz as $clave=>$valor){
				$_REQUEST[$clave]=filter_var($valor,FILTER_SANITIZE_FULL_SPECIAL_CHARS); 				
			}

			//La variable POST formSaraData contiene información codificada
			if(isset($_REQUEST["formSaraData"])){
				$cadena=$this->cripto->decodificar_url($_REQUEST["formSaraData"]);
			}

				if(isset($_REQUEST["api"])){
					$this->procesar_api($_REQUEST["api"]);	
				}
				
				if(!isset($_REQUEST["jxajax"])){			
					if(!isset($_REQUEST['action'])){
						return $this->mostrar_pagina();
					}
					else{
						return $this->procesar_pagina();
					}
				}else{
					
					$this->raizDocumentos=$this->miConfigurador->getVariableConfiguracion("raizDocumento");

					if($_REQUEST["bloqueGrupo"]==""){
						include_once($this->raizDocumentos."/blocks/".$_REQUEST["bloque"]."/bloque.php");					
					}else{
						include_once($this->raizDocumentos."/blocks/".$_REQUEST["bloqueGrupo"]."/".$_REQUEST["bloque"]."/bloque.php");
					}
					return true;

				}
		}
		return false;
	}

	function especificar_pagina($nombre){
		$this->pagina=$nombre;
	}

	function procesar_api($api){
	 
		$cadenaSql=$this->generadorClausulas->cadenaSql("apiparams",$api);
		$result=$this->recursoDB->ejecutarAcceso($cadenaSql,"busqueda");
	
    parse_str($result[0]['PARAM'],$matriz);

		foreach($matriz as $clave=>$valor){
			$_REQUEST[$clave]=$valor;			
		}
	}

	function mostrar_pagina(){
 
		//1. Buscar los bloques que constituyen la página
		//var_dump($_REQUEST);
		if(isset($_REQUEST['saramodule'])){
			$module=$_REQUEST['saramodule'];
		}elseif($this->miConfigurador->getVariableConfiguracion("module")<>""){
			$module=$this->miConfigurador->getVariableConfiguracion("module");
		}else{
			$module="master";
		}
		//echo $module; 
		$this->miConfigurador->setVariableConfiguracion("module",$module);
	
		$param['page']=$this->pagina; 
		$param['module']=$module; 
		$cadenaSql=$this->generadorClausulas->cadenaSql("bloquesPagina",$param);		

		if($cadenaSql){
			$registro=$this->recursoDB->ejecutarAcceso($cadenaSql,"busqueda");
			$totalRegistros=$this->recursoDB->getConteo();
			 
			if($totalRegistros>0){
				if(isset($registro[0]["parametro"]) && trim($registro[0]["parametro"])!=""){
					$parametros=explode("&",trim($registro[0]["parametro"]));
					foreach($parametros as $valor) {
						$elParametro=explode("=",$valor);
						$_REQUEST[$elParametro[0]]=$elParametro[1];					
					}
				}
				$this->armadorPagina->armarHTML($registro);				
				return true;
			}else{
				$this->tipoError="paginaSinBloques";
				return false;
			}
		}
	}
	
	function procesar_pagina(){
		$this->procesadorPagina->procesarPagina();
		return true;
	}
	
	function getError(){
		return $this->tipoError;
	}

}