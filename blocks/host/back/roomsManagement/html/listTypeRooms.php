<?php
$folder=urlencode($dataCommerce[0]['FILEFOLDER']);
?>
<script>
<?php
$i=0;
while(isset($roomList[$i][0])){
?>
	$(document).ready(function(){
		$(".fotografias<?=$roomList[$i]['IDTYPEROOM']?>").colorbox({href:'http://hoteles.kipu.co/plugin/uploadify/index.php?folder_upload=<?=$folder?>/<?=$roomList[$i]['IDTYPEROOM']?>', width:"75%", height:"90%"});
	}); 
<?
$i++;
}?> 
</script>		  

	
<div class="titulob">
  <div class="tituloimg">
  <h1>TIPOS DE HABITACION</h1>
  </div>
</div>

<div id="main_user">	
	<div class="row-fluid">
		<div class="span12">
			<div class="box box-color box-bordered">
				<div class="box-title">
					<h3>
						<ul class="main-nav menu_calendar">
							<li class="btn-calendar">
								<a href="<?=$formSaraDataNew?>"><span>NUEVO TIPO</span></a>
							</li>
							<li class="btn-calendar">
								<a href="<?=$formSaraDataRooms?>"><span>HABITACIONES</span></a>
							</li>
						</ul>	
					</h3>
				</div>
				<div class="box-content nopadding">
							<?php
							$i=0;
							while(isset($roomList[$i][0])):
							?>
							<br/>
							<div class="habitaciones2">
							<div class="cuatrocol20">
                Nombre
							</div>
							<div class="cuatrocol20">
                Descripción
							</div>
							<div class="cuatrocol30">
							
							</div>
							<div class="cuatrocol30" style="text-align: right;">
                <a class="btn" style="cursor:pointer" onclick="if(confirm('Estas seguro de eliminar este tipo de Habitacion? La operacion no se puede deshacer')){ deleteRoom('<?=$formSaraDataDelete?>&idtyperoom=<?=$roomList[$i]['IDTYPEROOM']?>') }"  class="btn" rel="tooltip" title="Borrar"><i class="icon-remove"></i></a>
							</div>
							<div class="clear"></div>
								</div>
							
							<div class="habitaciones">
										<form id="form-room" >
											<div class="cuatrocol_20">
												<div class="dat_1">
                          (<?=$roomList[$i]['IDTYPEROOM']?>) Nombre Habitación</div>
												<br/>
                        <input name="name"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" value="<?=$roomList[$i]['NAME']?>"  type="text" placeholder=""><br/>
												<div class="dat_1">
                          Capacidad (# Personas) 
                          <?=($roomList[$i]['CAPACITYTYPE']=="C")?"<br/>Capacidad Compartida":""?>
                        </div>
                        <br/>
                        <input name="capacity-type" value="<?php echo $roomList[$i]['CAPACITYTYPE']; ?>" type="hidden" />
                        <input name="capacity"  onchange="updateCapacity('<?=$formSaraDataCapacity?>',$(this.form))" value="<?=$roomList[$i]['CAPACITY']?>"  type="text" placeholder="">
											</div>
											<div class="cuatrocol_20">
												<textarea style="width: 95%; height: 175px;"onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="description"><?=trim($roomList[$i]['DESCRIPTION'])?></textarea>
												<a class="fotografias<?=$roomList[$i]['IDTYPEROOM']?> btn btn-primary">Fotografias</a>   
											</div>
											<div class="cuatrocol_30">
												<table>
													<tr> 
														<th style="background: rgb(164, 169, 173);">Valor<br/>Temporada Baja</th>
														<th style="background:rgb(45, 179, 3);">Valor<br/>Temporada Alta</th>
													</tr>
													<? 
													//Si la capacidad esta determinada como C de Compartida
													//El Valor de la reserva se determina Valor Individual x No Invitados
													//Es decir solo se muestra el valor para una persona
													$imgGuest='<img src="http://www.hoteles.kipu.co/theme/admin/web/images/guest.png" style="height:20px"/>';
													
													if($roomList[$i]['CAPACITYTYPE']=="C"){
														$roomList[$i]['CAPACITY']=1;
													}
														
													$g=1;
													for($g;$g<=($roomList[$i]['CAPACITY']);$g++){	
													
															$img=1;
															$imgPrint=array();
															 
															for($img;$img<=($roomList[$i]['CAPACITY']);$img++){
																
																$boy=1;
																
																for($boy;$boy<=$img;$boy++){
																
																	$imgPrint[$img][]=$imgGuest;

																}
															}

													
													?>
													<tr>
														<td><?=implode("",$imgPrint[$g])?><input style="width:90px" name="currency-<?=$currency?>-1-<?=$g?>"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['1'][$g][$currency]?>"  type="text" placeholder=""></td>
														<td><?=implode("",$imgPrint[$g])?><input style="width:90px" name="currency-<?=$currency?>-2-<?=$g?>"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['2'][$g][$currency]?>"  type="text" placeholder=""></td>
													</tr>	
													
													<?}?>
													
													<tr> 
														<td style="color:darkblue" >Niño <input style="width:90px" name="currency-<?=$currency?>-1-0"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['1']['0'][$currency]?>"  type="text" placeholder=""></td>
														<td style="color:darkblue">Niño <input style="width:90px" name="currency-<?=$currency?>-2-0"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['2']['0'][$currency]?>"  type="text" placeholder=""></td>
													</tr>
													
												</table>
												
											</div>									
											<div class="cuatrocol_30">
												<table>
													<tr>
													<th style="background: rgb(204, 164, 5);">Valor<br/>Promocional 1</th>
														<th style="background:rgb(25, 179, 185);">Valor<br/>Promocional 2</th>
													</tr>
													<? 
													$imgGuest='<img src="http://www.agenda.acaracademia.com/theme/admin/web/images/guest.png" style="height:20px"/>';
													$g=1;
													for($g;$g<=($roomList[$i]['CAPACITY']);$g++){	
															$img=1;
															$imgPrint=array();
															for($img;$img<=($roomList[$i]['CAPACITY']);$img++){
																$boy=1;
																for($boy;$boy<=$img;$boy++){
																	$imgPrint[$img][]=$imgGuest;
																}
															}
													?>
													<tr>
														<td style="color:darkblue"><?=implode("",$imgPrint[$g])?><input style="width:90px" name="currency-<?=$currency?>-3-<?=$g?>"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['3'][$g][$currency]?>"  type="text" placeholder=""></td>
														
														<td style="color:darkblue"><?=implode("",$imgPrint[$g])?><input style="width:90px" name="currency-<?=$currency?>-4-<?=$g?>"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['4'][$g][$currency]?>"  type="text" placeholder=""></td>
													
													</tr>	

													<?}?>
													<tr>
														<td style="color:darkblue">Niño <input style="width:90px" name="currency-<?=$currency?>-3-0"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['3']['0'][$currency]?>"  type="text" placeholder=""></td>
														<td style="color:darkblue">Niño <input style="width:90px" name="currency-<?=$currency?>-4-0"  onchange="updateTypeRoom('<?=$formSaraDataEdit?>',$(this.form))" name="name"  value="<?=$priceList[$roomList[$i]['IDTYPEROOM']]['4']['0'][$currency]?>"  type="text" placeholder=""></td>
													</tr>
												</table>
											</div>
											<input name="idtyperoom"  value="<?=$roomList[$i]['IDTYPEROOM']?>"  type="hidden" placeholder="">
										</form>
								</div>		
							<?php
							$i++;
							endwhile;
							?>
				</div>
			</div>
		</div>
	</div>
</div>