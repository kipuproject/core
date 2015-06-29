<?php
class Autenticador{

	private static $instancia;
	var $pagina;
	var $sesionUsuario;
	var $tipoError;
	var $configurador;

	private function __construct(){
    $this->configurador=Configurador::singleton();
		require_once($this->configurador->getVariableConfiguracion("raizDocumento")."/core/auth/Sesion.class.php");
		$this->sesionUsuario=Sesion::singleton();
	}

	public static function singleton(){
		if (!isset(self::$instancia)) {
			$className = __CLASS__;
			self::$instancia = new $className;
		}
		return self::$instancia;
	}

	function iniciarAutenticacion(){

		$resultado=$this->verificarExistenciaPagina();
		if($resultado){
			$resultado=$this->cargarSesionUsuario();

			if($resultado){
				// Verificar que el usuario está autorizado para el nivel de acceso de la página
				$resultado=$this->verificarAutorizacionUsuario();
				if($resultado){
					return true;
				}
				$this->tipoError="usuarioNoAutorizado";
				return false;
			}
			$this->tipoError="sesionNoExiste";
			return false;

		}

		$this->tipoError="paginaNoExiste";
		return false;

	}

	function especificarPagina($pagina){
		$this->pagina["nombre"]=$pagina;
	}

	private function verificarExistenciaPagina(){

    $clausulaSQL=$this->sesionUsuario->miSql->getCadenaSql("seleccionarPagina",$this->pagina["nombre"]);

		if($clausulaSQL){

			$registro=$this->configurador->conexionDB->ejecutarAcceso($clausulaSQL,"busqueda");
			$totalRegistros=$this->configurador->conexionDB->getConteo();

			if($totalRegistros>0) {
				$this->pagina["nivel"]=$registro[0][0];
				return true;
			}
		}
		$this->tipoError="paginaNoExiste";
		return false;
	}
	function getError(){
		return $this->tipoError;
	}

	/**
	 * Método.
	 * @return boolean
	 */
	function cargarSesionUsuario(){

		$this->sesionUsuario->setSesionNivel($this->pagina["nivel"]);
		
		$verificar=$this->sesionUsuario->verificarSesion();
		
		if($verificar==false)
		{
			$this->tipoError="sesionNoExiste";
			return false;
		}
		return true;
	}

	function verificarAutorizacionUsuario(){

		if($this->sesionUsuario->getSesionNivel()==$this->pagina["nivel"]){
			return true;
		}

		return false;
	}
}