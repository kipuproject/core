<?php
if(!isset($_REQUEST['kipu-development'])){
  ini_set ('display_errors','0');
}

require_once("core/manager/Configurador.class.php");
require_once("core/builder/builderSql.class.php");
include_once("core/auth/Sesion.class.php");

class ArmadorPagina{

	var $miConfigurador;
	var $generadorClausulas;
	var $host;
	var $sitio;
	var $raizDocumentos;
	var $bloques;
	var $seccionesDeclaradas;

	function __construct(){

		$this->miConfigurador=Configurador::singleton();
		$this->generadorClausulas=BuilderSql::singleton();
		$this->host=$this->miConfigurador->getVariableConfiguracion("host");
		$this->sitio=$this->miConfigurador->getVariableConfiguracion("site");
		$this->raizDocumentos=$this->miConfigurador->getVariableConfiguracion("raizDocumento");
		$this->miSesion=Sesion::singleton();
		$this->enlace=$this->host.$this->sitio."?".$this->miConfigurador->getVariableConfiguracion("enlace");
		$conexion="master";
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

	}

	function armarHTML($registroBloques){

		$this->bloques=$registroBloques;

		if($this->miConfigurador->getVariableConfiguracion("cache")) {

			//De forma predeterminada las paginas del aplicativo no tienen cache
			// header("Cache-Control: cache");
			// header("Expires: Sat, 20 Jun 1974 10:00:00 GMT");
		}
		header("Cache-Control: public");

		$this->raizDocumento=$this->miConfigurador->getVariableConfiguracion("raizDocumento");

		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> ';
		echo "\n<html lang='es'>\n";
		$this->encabezadoPagina();
		$this->cuerpoPagina();
		echo "</html>\n";
	}

	private function encabezadoPagina(){

		$enlace=$this->miConfigurador->getVariableConfiguracion("enlace");
	//	$_REQUEST[$enlace]=str_replace(" ","+",$_REQUEST[$enlace]);
	//	$cadena_sql="SELECT titulo,key_words FROM {$this->miConfigurador->getVariableConfiguracion("prefijo")}url_amigable WHERE url_original='".$_REQUEST[$enlace]."' ";
	//	$seo=$this->miRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");


		$htmlPagina="<head>\n";

	//	if(is_array($seo)){
	//		$htmlPagina.="<title>".$seo[0][0]."</title>\n";
	//		$htmlPagina.="<meta name='keywords' content='".$seo[0][1]."'>\n";
	//	}else{
			$htmlPagina.="<title>".$this->miConfigurador->getVariableConfiguracion("nombreAplicativo")."</title>\n";
	//	}

		$htmlPagina.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' >\n";

		$htmlPagina.="<link rel='shortcut icon' href='".$this->host.$this->sitio."/"."favicon.ico' >\n";
		echo $htmlPagina;


		// Enlazar los estilos definidos en cada bloque
		foreach($this->bloques as $unBloque){
			$this->incluirEstilosBloque($unBloque);
		}

		//Funciones javascript globales del aplicativo
		include_once("plugin/scripts/Script.php");

		// Insertar las funciones js definidas en cada bloque
		foreach($this->bloques as $unBloque){
			$this->incluirFuncionesBloque($unBloque);
		}

		// Para las páginas que requieren jquery
		if(isset($_REQUEST["jquery"])){

			$this->incluirFuncionReady($unBloque);
		}

		echo "</head>\n";
	}

	private function cuerpoPagina() {

		echo "<body>\n";

		if(isset($_REQUEST["tema"]) && $_REQUEST["tema"]<>""){
			$this->miConfigurador->setVariableConfiguracion("tema",$_REQUEST["tema"]);

		}else{
			$tema=$this->miSesion->getValorSesion("tema");

			if($tema<>""){
				$this->miConfigurador->setVariableConfiguracion("tema",$tema);
			}else{
				$this->miConfigurador->setVariableConfiguracion("tema","default");
			}

		}
		$valor=$this->variablesTema();
		$mensaje=$valor["mensaje"];
		$nombreUsuario=$userName=$valor["nombreUsuario"];
		$linkFin=$linkEnd=$valor["linkFinSesion"];
		$this->miConfigurador->setVariableConfiguracion("linkEnd",$linkEnd);

		$rutaTema=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."/theme/".$this->miConfigurador->getVariableConfiguracion("tema");
		$rutaFiles=$this->miConfigurador->getVariableConfiguracion("host").$this->miConfigurador->getVariableConfiguracion("site")."/files";

		foreach($this->bloques as $unBloque){
			$salida[$unBloque["seccion"]]=$this->incluirBloque($unBloque);
		}
		include($this->raizDocumentos."/theme/".$this->miConfigurador->getVariableConfiguracion("tema")."/template.php");

		echo "</body>\n";
	}
	private function variablesTema(){

		//mensaje
		if(isset($_REQUEST["mensaje"]) && $_REQUEST["mensaje"]<>""){
			$valor['mensaje']=$_REQUEST['mensaje'];
		}else{
			$valor['mensaje']="";
		}
		//Link Terminar Sesion

		$formSaraData="action=barraLogin";
		$formSaraData.="&bloque=barraLogin";
		$formSaraData.="&bloqueGrupo=gui";
		$formSaraData.="&opcionLogin=logout";
		$formSaraData=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($formSaraData,$this->enlace);

		$valor['linkFinSesion']=$formSaraData;

		//Usuario Actual

		  if($this->miSesion->getValorSesion('idUsuario')<>""){
			  $usuario_registro=$this->miSesion->getValorSesion('idUsuario');
		  }else{
			  $usuario_registro=0; //0 ES POR DEFECTO EL USUARIO ANONIMO
		  }

		$cadena=$this->generadorClausulas->cadenaSql("usuario",$usuario_registro);
		$usuario=$this->miRecursoDB->ejecutarAcceso($cadena,"busqueda");

		if(is_array($usuario)){
			$valor['nombreUsuario']=$usuario[0]['NOMBRE'];
		}else{
			$valor['nombreUsuario']='';
		}
		return $valor;
	}

	private function incluirBloque($unBloque){
		foreach($unBloque as $clave=>$valor){
			$unBloque[$clave]=trim($valor);
		}
		if($unBloque["grupo"]==""){
			$archivo=$this->raizDocumentos."/blocks/".$unBloque["nombre"]."/bloque.php";
		}else{
			$archivo=$this->raizDocumentos."/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"]."/bloque.php";
		}

		return $archivo;
	}

	private function incluirEstilosBloque($unBloque){
		foreach($unBloque as $clave=>$valor){
			$unBloque[$clave]=trim($valor);
		}
		if($unBloque["grupo"]==""){
			$archivo=$this->raizDocumentos."/blocks/".$unBloque["nombre"]."/css/Estilo.php";
		}else{
			$archivo=$this->raizDocumentos."/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"]."/css/Estilo.php";
		}
		if(file_exists($archivo)){
			include_once($archivo);
		}
	}

	private function incluirFuncionesBloque($unBloque){

		foreach($unBloque as $clave=>$valor){
			$unBloque[$clave]=trim($valor);
		}

		if($unBloque["grupo"]==""){
			$archivo=$this->raizDocumentos."/blocks/".$unBloque["nombre"]."/script/Script.php";
		}else{
			$archivo=$this->raizDocumentos."/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"]."/script/Script.php";
		}

		if(file_exists($archivo)){

			include_once($archivo);
		}
	}


	function incluirFuncionReady($unBloque){

		echo "<script type='text/javascript'>\n";
		echo "$(document).ready(function(){\n";

		foreach($this->bloques as $unBloque){

			foreach($unBloque as $clave=>$valor){
				$unBloque[$clave]=trim($valor);
			}

			if($unBloque["grupo"]==""){
				$archivo=$this->raizDocumentos."/blocks/".$unBloque["nombre"]."/script/ready.js";
			}else{
				$archivo=$this->raizDocumentos."/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"]."/script/ready.js";
			}

			if(file_exists($archivo)){

				include($archivo);
				echo "\n";

			}
		}
		echo "});\n";
		echo "</script>\n";

	}
	private function armar_no_pagina($seccion,$cadena) {
		$this->la_cadena=$cadena.' AND '.$this->miConfigurador->configuracion["prefijo"].'bloque_pagina.seccion="'.$seccion.'" ORDER BY '.$this->miConfigurador->configuracion["prefijo"].'bloque_pagina.posicion ASC';
		$this->base->registro_db($this->la_cadena,0);
		$this->armar_registro=$this->base->getRegistroDb();
		$this->total=$this->base->obtener_conteo_db();
		if($this->total>0) {

			for($this->contador=0;$this->contador<$this->total;$this->contador++) {
				$this->id_bloque=$this->armar_registro[$this->contador][0];
				$this->incluir=$this->armar_registro[$this->contador][4];
				include($this->miConfigurador->configuracion["raiz_documento"].$this->miConfigurador->configuracion["bloques"]."/".$this->incluir."/bloque.php");
			}
		}
		return TRUE;
	}
}
