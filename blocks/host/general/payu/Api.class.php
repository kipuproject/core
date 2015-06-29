<?php
if(!isset($GLOBALS["autorizado"])){
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("plugin/mail/class.phpmailer.php");
include_once("plugin/mail/class.smtp.php");
include_once("core/builder/Acceso.class.php");

class ApiPayu{

	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $error;
	var $miRecursoDB;
	var $crypto;
	var $mensaje;
	var $status="";

	function __construct(){
		$this->miConfigurador=Configurador::singleton();
		$this->miInspectorHTML=InspectorHTML::singleton();
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
    $this->rutaURL=$this->miConfigurador->getVariableConfiguracion("host");
		$this->rutaURL.=$this->miConfigurador->getVariableConfiguracion("site");
	  $this->Access=Acceso::singleton();
	  $conexion="master";
	  $this->master_resource=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
	}

	public function setRuta($unaRuta){
		$this->ruta=$unaRuta;
	}

	public function setSql($a){
		$this->sql=$a;
	}

	public function setFuncion($funcion){
		$this->funcion=$funcion;
	}

	public function setLenguaje($lenguaje){
		$this->lenguaje=$lenguaje;
	}

	public function setFormulario($formulario){
		$this->formulario=$formulario;
	}
	
	public function process(){
	
		if(!isset($_REQUEST['key'])){
			echo "error";
			exit;
		}else{ 
			$cadena_sql=$this->sql->cadena_sql("api_key",$_REQUEST['key']);
			$commerce=$this->master_resource->ejecutarAcceso($cadena_sql,"busqueda");
			$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($commerce[0]['DBMS']);
			$this->commerce=$commerce[0]['IDCOMMERCE'];   
			$this->commerce_folder=$commerce[0]['FOLDER']; 
		}
		
		$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);
		$_REQUEST=$this->miInspectorHTML->limpiarSQL($_REQUEST);
		
		unset($_REQUEST['aplicativo']);
		unset($_REQUEST['PHPSESSID']);
		
		foreach($_REQUEST as $key=>$value){
			$_REQUEST[urldecode($key)]=urldecode($value);
		}
		
		if(isset($_REQUEST['method'])){
			
			switch($_REQUEST['method']){
				case 'payugo': 
					$result=$this->payuGO($_REQUEST['value']);
				break;
			}
			
			$json=json_encode($result);
			if(isset($_GET['callback'])){
				echo "{$_GET['callback']}($json)";
			}else{
				echo $json;
			}
 		}else{ 
				echo "no data";
		}
	}
	

  /**
  * Funcion que establece los datos que serán enviados a payu
  * @param idPayment identificador del registro de pago 
  */		
  private function payuGO($idPayment){
    
    $response= new stdClass();
    
    $string_sql=$this->sql->cadena_sql("searchTransaction",$idPayment);
    $result=$this->miRecursoDB->ejecutarAcceso($string_sql,"busqueda");
    $result=$result[0];

    $data = array(
        'merchantId' => $result['MERCHANTID'],
        'accountId' => $result['ACCOUNTID'],
        'description' => $result['DESCRIPTION'],
        'referenceCode' => $result['IDPAYMENT'],
        'amount' => $result['VALUE'],
        'extra1' => $result['IDCOMMERCE'], //Identificador del plugin origen
        'extra2' => 'payu-check-in',
        'tax' => '0',
        'taxReturnBase' => "0", //$taxes['base_price'],
        'shipmentValue' => "0",
        'currency' => $result['CURRENCY'],
        'lng' => "es",
        'signature' => $this->getFirm($result),
        'sourceURL' => $this->rutaURL,
        'responseURL' => $result['RESPONSEURL'],
        'test'=>"0",
        'buyerEmail' => $result['EMAILCUSTOMER'],
    );
    foreach ($data as $name => $value) {
      $response->$name=$value;
    }
    return $response;
  }
    
  
  private function getFirm($settings) {
    $params = array(
      $settings['APIKEY'],
      $settings['MERCHANTID'],
      $settings['IDPAYMENT'],
      $settings['VALUE'],
      $settings['CURRENCY']
    );
    return md5(implode('~',$params));
  }
    
}