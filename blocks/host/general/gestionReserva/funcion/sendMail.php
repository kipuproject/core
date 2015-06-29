<?php	   
		$fecha = date("d-M-Y  h:i:s A");
	
		ob_start();
		include_once($this->ruta."/html/confirmation-mail.php");
		$body=ob_get_contents();
		ob_end_clean();
		 
		$email= $response->responsible['EMAIL'];
	//$email.= ','.$this->ruta=$this->miConfigurador->getVariableConfiguracion("correoAdministrador").' ';

		$asunto = "Confirmacion Reserva ".$response->commerce['NAME']." \n\n";
		$cabeceras="From: reservas@kipu.co\r\n";
	//$cabeceras.="Bcc: karenpalacios@kreent.com,carolinaherrera@kreent.com,info@kreent.com\r\n";
		$cabeceras.="Content-type: text/html\r\n";
		$ok=mail($email,$asunto,$body,$cabeceras);

		if(!$ok){
			$result=print_r(error_get_last(),TRUE); 
		}else{
			$result="Gracias por reservar con nosotros";  

		}
?>