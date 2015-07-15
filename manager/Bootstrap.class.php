<?php
require_once("core/manager/Configurador.class.php");
require_once("core/auth/Sesion.class.php");
require_once("core/connection/FabricaDbConexion.class.php");
require_once("core/crypto/Encriptador.class.php");
require_once("core/builder/Mensaje.class.php");

class Bootstrap{

	var $sesionUsuario;
	
  /**
	 *
	 * Objeto.
	 * Encargado de inicializar las variables globales. Su atributo $configuracion contiene los valores necesarios
	 * para gestionar la aplicacion.
	 * @var Configurador
	 */
	var $miConfigurador;


	/**
	 *
	 * Objeto, con funciones miembro generales que encapsulan funcionalidades
	 * básicas.
	 * @var FuncionGeneral
	 */
	private $miFuncion;

	/**
	 *
	 * Objeto. Gestiona conexiones a bases de datos.
	 * @var FabricaDBConexion
	 */
	private $manejadorDB;

	/**
	 * Objeto de la clase Encriptador se encarga de codificar/decodificar cadenas de texto.
	 * @var Encriptador
	 */
	private $cripto;


	/**
	 *
	 * Objeto. Actua como controlador del modulo de instalación del framework/aplicativo
	 * @var Instalador
	 */
	var $miInstalador;

	/**
	 *
	 * Objeto. Instancia de la pagina que se está visitando
	 * @var Pagina
	 */
	var $miPagina;

	/**
	 *
	 * Arreglo.Ruta de acceso a los archivos, se utilizan porque aún no se ha rescatado las
	 * variables de configuración.
	 *
	 * @var string
	 */
	var $misVariables;

	/**
	 * Objeto que se encarga de mostrar los mensajes de error fatales.
	 * @var Mensaje
	 */
	var $cuadroMensaje;

	/**
	 * Contructor
	 * @param none
	 * @return integer
	 * */

	function __construct(){

		$this->cuadroMensaje=Mensaje::singleton();
		$this->conectorDB = FabricaDbConexion::singleton();
		$this->cripto = Encriptador::singleton();
		
		/**
		 * Importante conservar el orden de creación de los siguientes objetos porque tienen
		 * referencias cruzadas.
		 */
		$this->miConfigurador=Configurador::singleton();
		$this->miConfigurador->setConectorDB($this->conectorDB);
		
		/**
		 * El objeto del a clase Sesion es el último que se debe crear.
		 */
		$this->sesionUsuario=Sesion::singleton();

	}

	/**
	 *
	 * Iniciar la aplicación.
	 */

	public function iniciar(){

		// Poblar el atributo miConfigurador->configuracion

		$this->miConfigurador->variable();

		if(!$this->miConfigurador->getVariableConfiguracion("instalado"))
		{
			$this->instalarAplicativo();

		}else{
			$this->ingresar();
		}
	}

	/**
	 *
	 * Asigna los valores a las variables que indican las rutas predeterminadas.
	 * @param strting array $variables
	 */

	function setMisVariables($variables){
		$this->misVariables=$variables;
		$this->miConfigurador->setRutas($variables);
	}

	/**
	 *
	 * Ingresar al aplicativo.
	 * @param Ninguno
	 * @return int
	 */
	private function ingresar() {

		/**
		 * @global boolean $GLOBALS['autorizado']
		 * @name $autorizado
		 */
		$GLOBALS["autorizado"]=TRUE;
		$pagina=$this->determinarPagina();
		$this->miConfigurador->setVariableConfiguracion("pagina",$pagina);

		/**
		 * Verificar que se tenga una sesión válida
		*/

		require_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/core/auth/Autenticador.class.php");
		$this->autenticador=Autenticador::singleton();
		$this->autenticador->especificarPagina($pagina);
    				
		if($this->autenticador->iniciarAutenticacion()){
			/**
			 * Procesa la página solicitada por el usuario
			 */
			require_once($this->miConfigurador->getVariableConfiguracion("raizDocumento")."/core/builder/Pagina.class.php");
	
			$this->miPagina=new Pagina();

			if($this->miPagina->inicializarPagina($pagina)){ 
				return true;
			}else{
				$this->mostrarMensajeError($this->miPagina->getError());
				return false;
			}			
		}else{
			$this->mostrarMensajeError($this->autenticador->getError());
			return false;
		}		
		
	}
	
	private function mostrarMensajeError($mensaje){
		$this->miConfigurador->setVariableConfiguracion("error", true);
		$this->cuadroMensaje->mostrarMensaje($mensaje, "error");		
	}

	private function determinarPagina(){
		/**
		 * Determinar la página que se desea cargar
		 */

		if(isset($_REQUEST[$this->miConfigurador->getVariableConfiguracion("enlace")])) {
			$this->miConfigurador->fabricaConexiones->crypto->decodificar_url($_REQUEST[$this->miConfigurador->getVariableConfiguracion("enlace")]);

			if(isset($_REQUEST["redireccionar"])) {
				$this->redireccionar();
				return false;
			}
			if(isset($_REQUEST["pagina"])) {
				return $_REQUEST["pagina"];
			}else {
				if(!isset($_REQUEST["action"])){
					return "";
				}else {
					return "index";
				}
			}
		}else {
			return "index";
		}
	}

	/**
	 *
	 * Instalar el aplicativo.
	 */

	private function instalarAplicativo() {
		require_once("install/Instalador.class.php");
		$this->miInstalador=new Instalador();
		if(isset($_REQUEST["instalador"])){
			$this->miInstalador->procesarInstalacion();
		}else{
			$this->miInstalador->mostrarFormularioDatosConexion();
		}
		return 0;
	}
	/**
	 * Redireccionar a otra página
	 * @return number
	 */

	function redireccionar(){
		$variable="";
		foreach($_REQUEST as $clave=> $val) {
			if($clave !="redireccion") {
				$variable.="&".$clave."=".$val;
			}
		}
		$this->miConfigurador->cripto->decodificar_url($_REQUEST["redireccion"]);

		foreach($_REQUEST as $clave=> $val) {
			$variable.="&".$clave."=".$val;
		}
		$variable=$this->miConfigurador->cripto->codificar_url($variable,$this->miConfigurador->configuracion);
		$indice=$this->miConfigurador->configuracion["host"].$this->miConfigurador->configuracion["site"]."/index.php?";
		echo "<script>location.replace('".$indice.$variable."')</script>";
		return 0;

	}
};