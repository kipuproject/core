<!doctype html>
 <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
	$(function() {
		$( "#datepickerin" ).datepicker({
		numberOfMonths: 2,
		altField: "#checkin",
		currentText: "Hoy",
		
		dateFormat:"dd/mm/yy",
		showButtonPanel: false
		});
		
		$( "#datepickerout" ).datepicker({
		numberOfMonths: 2,
		altField: "#checkout",
		currentText: "Hoy",
		
		dateFormat:"dd/mm/yy",
		showButtonPanel: false
		});
	});
</script>

<div class="titulob">
	<div class="tituloimg">
		<h1>RESERVA INTERNA</h1>
	</div>

</div>
<br/><br/>
<div class="booking-component">
<div class="row-fluid">
	<div class="span12">
		<div class="box box-color">
			<div class="box-content nopadding">
				<form action="index.php" method="POST" class='form-horizontal form-wizard' id="form-booking">
					<div class="step" id="firstStep">
							<div class="seccion3-step">
									<div class="control-group" style="float:left;">
										HABITACION:
										<select name="dataRoomBooking" id="dataRoomBooking">
										<?php 
											$i=0;
											while(isset($dataItems[$i][0])):
                    ?>
												<option value="<?=$dataItems[$i]['IDGROUP']?>-<?=$dataItems[$i]['IDRESERVABLE']?>" ><?=$dataItems[$i]['NAME']?></option>
										<?php 
												$i++;
											endwhile;
                     ?>
										</select>
									</div>
									<div class="control-group" style="float:left;">
										ORIGEN DE LA RESERVA:
										<select name="medioBooking" id="medioBooking">
											<option value="HOTEL" >HOTEL</option>
											<option value="BOOKING" >BOOKING</option>
											<option value="DESPEGAR" >DESPEGAR</option>
											<option value="TRIPADVISOR" >TRIPADVISOR</option>
											<option value="ATRAPAME" >ATRAPAME</option>
											<option value="EXPEDIA" >EXPEDIA</option>
											<option value="HOTELES.COM" >HOTELES.COM</option>
										</select>
									</div>
									<div class="control-group" style="float:left;">
										<div class="chechstyke" >CHECK IN: </div>
										<div id="datepickerin"></div>
										<input  type="hidden" required="true" name="checkin" id="checkin" value="" class="">
										<input  type="hidden" id="status" value="false" >
									</div>
									
									<div class="control-group" style="float:left;">
										CHECK OUT: <div id="datepickerout"></div>
										<input  type="hidden" required="true" name="checkout" id="checkout" value="" class="">
										<input  type="hidden" id="status" value="false" >

									</div>
									
									<?
									$kids=$this->miConfigurador->getVariableConfiguracion("kids");
									if($kids=="" || $kids=="FALSE"){
									?>
										<div class="control-group" style="float:left;">
											NUMERO DE HUESPEDES:
											<input  type="text" id="guestBooking" onchange="updateRoomFriends()" name="guestBooking" value="1" >
										</div>
										<input  type="hidden" id="kids" name="kids" value="0" >

									<?
									}else{
									?>
										<div class="control-group" style="float:left;">
										NUMERO DE ADULTOS:
										<input  type="text" id="guestBooking" onchange="updateRoomFriends()" name="guestBooking" value="1" >
										</div>
										<div class="control-group" style="float:left;">
										NUMERO DE NI&Ntilde;OS ( <?=$kids?> ):
										<input  type="text" id="kids" onchange="updateRoomFriends()" name="kids" value="" >
									</div>
									
									<?
									}
									?>
									
									
									<div class="control-group" style="float:left;">
										<div class="form-actions"> 
											VALOR DE LA RESERVA:
											<input type="text" id="valueBooking"  name="valueBooking" value="" >
											<input type="hidden" id="prebooking" name="prebooking" value="" >
											<a onclick="bookingValidate('prebooking');" >Calcular</a>
										</div>
									</div>
									
									<div class="clear"></div>
									<fieldset>
									
									<div class="control-group">
										<div class="control-group_text">RESPONSABLE DE LA RESERVA</div>
										</div>
										<div class="control-group">
											<label for="firstname" class="control-label">Nº Documento de Identidad &oacute; C&oacute;digo de Cliente</label>
											<div class="controls">
												<input  onchange="updateDataCustomer()"  size="50" type="text"  name="idCustomer" id="idCustomer" value="" class="">
											</div>
										</div>
										
										<div id="dataCustomer">
										
											<div class="control-group">
												<label for="firstname" class="control-label">Nombre y Apellido</label>
												<div class="controls" id="div-nameCustomer" >
													<input  size="50" type="text"  name="nameCustomer" id="" value="" class="">*
												</div>
											</div>
											<div class="control-group">
												<label for="firstname" class="control-label">Fecha de Nacimiento</label>
												<div class="controls" id="div-dateCustomer" >
													<input  size="50" type="date"  name="dateCustomer" id="" value="" class="">
												</div>
											</div>
											
											<div class="control-group" >
												<label for="anotherelem" class="control-label">Correo</label>
												<div class="controls" id="div-emailCustomer">
													<input onchange="updateDataCustomer()" size="50" type="text"  name="emailCustomer" id="" value="" class="">*
 	 											</div>
											</div>
											<div class="control-group" >
												<label for="additionalfield" class="control-label">Telefono</label>
												<div class="controls" id="div-phoneCustomer">
													<input  size="50" type="text"  name="phoneCustomer" id="" value="" class="">
												</div>
											</div>
											<div class="control-group" >
												<label for="anotherelem" class="control-label">Nacionalidad</label>
												<div class="controls" id="div-countryCustomer">
													<input  size="50" type="text" required="true" name="countryCustomer" id="" value="" class="">
												</div>
											</div>
										  
										</div>  
										
								
									</fieldset>
						
									<div class="control-group" style="float:left;" id="friend-div">
								
									</div>
									
									<input size="50" type="hidden" required="true" name="numGuest" id="numGuest" value="1" class="">
									
							</div>
							
							<div style="clear: both;"></div>

					</div>

					
					<div class="form-actions">
						<input  type="hidden" name="numRooms" value="1" >
						<a onclick="bookingValidate('book');" class="booking-button" >Reservar</a>
					</div>
					<br/>
					<br/>
				</form>
			</div>
		</div>
	</div>
</div>

</div>


