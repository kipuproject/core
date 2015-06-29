<?php 
				
$avalaibleRooms=$this->getAvalaibleRooms($booking['ROOM'],strtotime($booking['FECHA_INICIO']),strtotime($booking['FECHA_FIN']),$booking['IDCOMMERCE'],$booking['ROOMTYPE']); 
$payuPayment=$this->getPayuPayment($booking['IDBOOKING']);
$additionaldata=$this->getAdditionalData($booking['IDBOOKING']);

?>
<style>
	.span6{
		width:48% !important;
	}
	.detailBookingBox img{
		width:25px;
	}
	.detailBookingBox h1{
		  text-align: right;
      padding-right: 5%;
	}
	.control-label{
		width: 0 !important;
	}
	.pdf{
		position: relative;
		top: 3px;
		left: 76%;
		}	
	</style>	
	
		
<div class="detailBookingBox">
   <h1>C&Oacute;DIGO DE RESERVA: <?php echo $booking['IDBOOKING']; ?></h1>
	<ul class="tabs tabs-inline ">
		<li class="active" ><a href="#general<?=$booking['IDBOOKING']?>" data-toggle='tab'>INFORMACION GENERAL</a></li>
		<li><a href="#financiera<?=$booking['IDBOOKING']?>" data-toggle='tab'>INFORMACION FINANCIERA</a></li>
		<li><a href="#huespedes<?=$booking['IDBOOKING']?>" data-toggle='tab'>INFORMACION HUESPEDES</a></li>
		<!--li>
			<a href="<?=$booking['URLVOUCHER']?>" target="_blank">
				<img title="Imprimir Voucher" src="http://www.hoteles.kipu.co/blocks/host/back/bookingManagement/html/voucher/imagenes/pdf.png" >
			</a>
		</li-->
	</ul> 
	
	<div class="tab-content padding tab-content-inline tab-content-bottom">
	
		<div class="tab-pane active" id="general<?=$booking['IDBOOKING']?>">
		
		<div class="box-content nopadding">
			<form id="form-15" class="form-horizontal form-column form-bordered" method="POST" action="#">
				<div class="span6">
					<div class="control-group" >
						<label class="control-label" for="textfield">ENTRADA:</label>
						<div class="controls">
							<div class="input-prepend">
								<input type="text"  onchange="assignDate($('#chekininput'),$('#chekoutinput'),'<?=$booking['IDBOOKING']?>')" disabled="true"  id="chekininput" value="<?=date("d/m/Y",strtotime($booking['FECHA_INICIO']))?>" /></span>
								<span class="add-on"><a onclick="$('#chekininput').prop('disabled', false);" ><img src="http://www.agenda.acaracademia.com/images/edit.png"></a>
							</div>
							<span class="help-block">Check In dd/mm/YYYY</span> 
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="textfield">HABITACION:</label>
						<div class="controls">
							<div class="input-prepend">
							
							
								<select onchange="assignRoom($(this),'<?=$booking['IDBOOKING']?>')"  id="assignRoomObj" >
									<option <?=($booking['ROOM']=="0")?"selected":""?> value="0">SIN ASIGNAR</option>
									<?PHP
									
										foreach($avalaibleRooms as $key=>$value){ ?>
											<option <?=($booking['ROOM']==$value[0]['IDROOM'])?"selected":""?>  value="<?=$value[0]['IDROOM']?>" ><?=$value[0]['NAME']?></option>
									<?PHP
										
										}
									?>
								</select>
							</div>
							<span class="help-block">
								TIPO DE HABITACION: 
								<SELECT  id="typeroominput" disabled="true"  onchange="assignTypeRoom($(this),'<?=$booking['IDBOOKING']?>')">
								<? foreach($typeRooms as $key=>$value){ ?> 
										<OPTION <?=($booking['ROOMTYPE']==$value[0]['IDTYPEROOM'])?"selected":""?> value="<?=$value[0]['IDTYPEROOM']?>" ><?=$value[0]['NAME']?></OPTION>
								
								<? } ?>
								</SELECT>
								<span class="add-on"><a onclick="$('#typeroominput').prop('disabled', false);" ><img src="http://www.agenda.acaracademia.com/images/edit.png"></a></span>
							</span>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="textfield">OBSERVACIONES:</label>
						<div class="controls">
							<div class="">
									HOTEL:
									<textarea onchange="assignObservation($(this),'<?=$booking['IDBOOKING']?>')" id="observationvalueinput"><?=$booking['OBSERVATION']?></textarea>
									<br/>
									CLIENTE: <?=$booking['OBSERVATION_CUSTOMER']?>
							</div>
							
							<span class="help-block"> 
									<?
										if(is_array($additionaldata)){
											$ad=0;
											while(isset($additionaldata[$ad][0])){
												?>
													[ <?=$additionaldata[$ad]['NAMEFIELD']?>: <?=$additionaldata[$ad]['VALUE']?> ] 
												<?
												$ad++;
											}
										}
									?>
							
							</span>
						</div>
					</div>
				</div>
				
				<div class="span6">
					<div class="control-group">
						<label class="control-label"  for="textfield">SALIDA:</label>
						<div class="controls">
							<div class="input-prepend">
									<input type="text"  onchange="assignDate($('#chekininput'),$('#chekoutinput'),'<?=$booking['IDBOOKING']?>'); $('#chekoutinput').prop('disabled',true);" disabled="true"  id="chekoutinput" value="<?=date("d/m/Y",strtotime($booking['FECHA_FIN']))?>" />
									<span class="add-on"><a onclick="$('#chekoutinput').prop('disabled', false);" ><img src="http://www.agenda.acaracademia.com/images/edit.png"></a></span>
							</div>
							<span class="help-block">Check Out dd/mm/YYYY</span>
						</div>
					</div>
					
					<div class="control-group"  >
						<label class="control-label" for="textfield">ESTADO RESERVA:</label>
						<div class="controls">
							<div class="input-prepend">
									<select name="assignStatusObj" id="assignStatusObj" onchange="assignStatus($(this),'<?=$booking['IDBOOKING']?>')" >
										<option <?=($booking['STATUS']=="6")?"selected":""?> value="6">PENDIENTE</option>
										<option <?=($booking['STATUS']=="2")?"selected":""?> value="2">CONFIRMADA</option>
										<option <?=($booking['STATUS']=="3")?"selected":""?> value="3">CANCELADA</option>
									</select>
							</div>
							<span class="help-block">estado actual</span>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"  for="textfield">ORIGEN:</label>
						
						<div class="controls">
							<div class="input-prepend">
									<span class="add-on" id="origen"><?=$booking['MEDIO']?></span>
							</div>
							<br/><br/>
							<span class="help-block">Procedencia de la Reserva</span>
						</div>
					</div>
				</div>

				
			</form>
		</div>
		
		</div>
		
		
		<div class="tab-pane" id="financiera<?=$booking['IDBOOKING']?>">
      
      <div class="box-title" style="background: #fff !important; margin-top: 3px;">
        <h3>HOSPEDAJE</h3>
      </div>  
    
			<div class="box-content nopadding">
				<form class="form-horizontal form-column form-bordered" method="POST" action="#">
					<div class="span6">
						<div class="control-group">
							<label class="control-label" for="textfield">VALOR RESERVA:</label>
							<div class="controls">
								<div class="input-prepend">
									<span class="add-on">$</span>
									<input type="text" style="width:70px"  onchange="assignValue($(this),'<?=$booking['IDBOOKING']?>')" disabled="true"  id="valueinput" value="<?=$booking['VALUEBOOKING']?>" />
									<span class="add-on"><a onclick="$('#valueinput').prop('disabled', false);" ><img src="http://www.agenda.acaracademia.com/images/edit.png"></a></span>
								</div>
                <span class="help-block">
								VALOR POR NOCHE:  <?=round(($booking['VALUEBOOKING'])/(round((($booking['FECHA_FIN_UNIX'])*1-($booking['FECHA_INICIO_UNIX'])*1)/86400)))?>
 								# Noches: <span id="nights" ><?=round((($booking['FECHA_FIN_UNIX'])*1-($booking['FECHA_INICIO_UNIX'])*1)/86400)?></span>
                </span>

              </div>
						</div>
						<div class="control-group">
							<label class="control-label" for="textfield">SALDO PENDIENTE:</label>
							<div class="controls">
								<div class="input-prepend">
									<span class="add-on">$</span>
									<input class="input-small" style="width:70px" type="text"  disabled="true" id="valuenightinput" value="<?=($booking['VALUEBOOKING'])-($payuPayment['VALUE'])-($booking['VALUEPAYMENT'])?>" />  
								</div>
							</div>
						</div>
					</div>
					
					<div class="span6">
						<div class="control-group">
							<label class="control-label" for="textfield">ESTADO PAGO:</label>
							<div class="controls">
								<div class="input-prepend">
										<select name="assignStatusPaymentObj" id="assignStatusPaymentObj" onchange="assignStatusPayment($(this),'<?=$booking['IDBOOKING']?>')" >
										
											<option <?=($booking['PAYMENT']=="1")?"selected":""?> value="1">PAGO REALIZADO</option>
											<option <?=($booking['PAYMENT']=="0")?"selected":""?> value="0">NO SE REPORTA PAGO</option>
											<option <?=($booking['PAYMENT']=="2")?"selected":""?> value="2">PAGO PARCIAL</option>
										</select>
								</div>
								
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" for="textfield">TOTAL ABONADO:</label>
							<div class="controls">
								<div class="input-prepend">
									<span class="add-on">$</span>
									<input type="text" style="width:70px" onchange="assignOnlineValue($(this),'<?=$booking['IDBOOKING']?>')" disabled="true" id="payupaymentinput" value="<?=$payuPayment['VALUE']?>" />
									<span class="add-on">PAGO EN LINEA</span>
									<span class="add-on"><?=$payuPayment['VALUE']*100/$booking['VALUEBOOKING']?>%</span>
									<span class="add-on"><a onclick="$('#payupaymentinput').prop('disabled', false);" ><img src="http://www.agenda.acaracademia.com/images/edit.png"></a></span>

									<br/>
									<span class="add-on">$</span>
									<input type="text" style="width:70px"  onchange="assignPaymentValue($(this),'<?=$booking['IDBOOKING']?>')" disabled="true"  id="paymentvalueinput" value="<?=$booking['VALUEPAYMENT']?>" />
									<span class="add-on">PAGO DIRECTO</span>
									<span class="add-on"><a onclick="$('#paymentvalueinput').prop('disabled', false);" ><img src="http://www.agenda.acaracademia.com/images/edit.png"></a></span>
								</div>
							</div>
						</div>
						
					</div>
					<div  style="display:block" class="span12">  
						<div class="control-group">
							<label class="control-label" for="textfield">SERVICIOS ADICIONALES: </label>
							<div class="controls">
								<div class=""> 
									<div class="additional-service" style="border: 1px solid #CCC; padding: 3px;">
										<select id="selector-service-<?=$booking['IDBOOKING']?>" name="service[]" onchange="" >
											<? foreach($serviceList as $key=>$value){ ?> 
												<OPTION value="<?=$value[0]['IDSERVICE']?>" ><?=$value[0]['NAME']?></OPTION>
											<? } ?>
										</select>
										<a onclick="newService('<?=$booking['IDBOOKING']?>');" class="red-button">Agregar</a> 
										Estos valores no se sumarán automáticamente al valor de la reserva.
									</div>
								</div>
								
							</div>
							 
							<div class="list-services-<?=$booking['IDBOOKING']?>">
								<?$bs=0;
								while(isset($bookingServiceList[$bs]['ID'])){
								?>
									<div  style="display:block" class="controls">
										<div class="" style="border: 1px solid #CCC; padding: 3px;">
											<span class="title-service"><?=$serviceList[$bookingServiceList[$bs]['ID']][0]['NAME']?>: </span>
											Cantidad: 
											<input style="width:50px" type="text" class="cs" id="textfield" value="<?=$bookingServiceList[$bs]['CANT']?>" class="spinner input-mini">
											Valor: <input type="text" class="vs" value="<?=$bookingServiceList[$bs]['VALUE']?>" >
											<input type='hidden' class='is' value="<?=$bookingServiceList[$bs]['ID']?>">
											<a onclick="saveService($(this),'<?=$formSaraDataService?>','<?=$booking['IDBOOKING']?>','update');" class="red-button update" >Actualizar</a>
											<a onclick="if (confirm('Estas seguro de eliminar este registro')){ saveService($(this),'<?=$formSaraDataService?>','<?=$booking['IDBOOKING']?>','delete'); }" class="red-button" >X</a>
										</div>
									</div>
								
								<?
								$bs++;
								}
								?>
						
								<div  style="display:none" class="template-service<?=$booking['IDBOOKING']?> controls">
									<div class="" style="border: 1px solid #CCC; padding: 3px;">
										<span class="title-service"></span>
										Cantidad: 
										<input style="width:50px" type="text" class="cs" id="textfield" value="1" class="spinner input-mini">
										Valor: <input type="text" class="vs">
										<input type='hidden' class='is'>
										<a onclick="saveService($(this),'<?=$formSaraDataService?>','<?=$booking['IDBOOKING']?>','add');" class="red-button save" >Guardar</a>
										<a style="display:none" onclick="saveService($(this),'<?=$formSaraDataService?>','<?=$booking['IDBOOKING']?>','update');" class="red-button update" >Actualizar</a>
										<a onclick="if (confirm('Estas seguro de eliminar este registro')){ saveService($(this),'<?=$formSaraDataService?>','<?=$booking['IDBOOKING']?>','delete'); }" class="red-button" >X</a>
									</div>
								</div>
							</div>
						</div>
						
					</div> 
				</form>
			</div>
			<div class="clear"></div>
		</div>
		<div class="tab-pane" id="huespedes<?=$booking['IDBOOKING']?>"> 
			<div class="box-title" style="background:#DDD !important; color:#000 !important; border:none !important; margin-top:5px;">
				<h3>INFORMACION HUESPEDES - ADULTOS: <?=$booking['NUMGUEST']?> NIÑOS: <?=$booking['NUMKIDS']?>
				</h3>
			</div>

			<?php $guestBooking=$this->getGuestBooking($booking['IDBOOKING'],$booking['IDCOMMERCE']); ?>		

				<table class="table table-hover table-nomargin table-bordered " >
					<tr >
						<td style="font-weight:bold"><b>#</b></td>
						<td style="font-weight:bold"><b>IDENTIFICACION</b></td>
						<td style="font-weight:bold"><b>NOMBRE</b></td>
						<td style="font-weight:bold"><b>NACIONALIDAD</b></td>
						<td style="font-weight:bold"><b>EMAIL</b></td>
						<td style="font-weight:bold"><b>TELEFONO</b></td>			

					</tr>
					<tr style="background:rgb(252, 252, 252);">
					
						<td>RESPONSABLE</td>
						<td><?=$booking['DNI']?></td>
						<td><?=$booking['NAMECLIENT']?></td>
						<td><?=$booking['COUNTRY']?></td>
						<td><?=$booking['EMAILCLIENT']?></td>
						<td><?=$booking['PHONECLIENT']?></td>			

					</tr>
			<?PHP
				$g=0;
				while(isset($guestBooking[$g][0])){ ?>
					<tr>
						<td>INVITADO</td> 
						<td><?=$guestBooking[$g]['DNI']?></td>
						<td><?=$guestBooking[$g]['NAMECLIENT']?></td>
						<td><?=$guestBooking[$g]['COUNTRY']?></td>
						<td><?=$guestBooking[$g]['EMAILCLIENT']?></td>
						<td><?=$guestBooking[$g]['PHONECLIENT']?></td>			

					</tr>
				
				
			<?PHP
				$g++;
				}
			?>
				</table>
		</div>

	</div>

	
</div>
<br/><br/>