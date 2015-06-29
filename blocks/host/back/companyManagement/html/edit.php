<div id="main_user">
	<div class="page-header">
		<div id="allCommerce"></div>
 
		<!--Inicio Comercio-->
		<?PHP
		$i=0;
		while(isset($commerce[$i]['IDCOMMERCE'])):
		$time=(time())+$i;
		?>
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#c2-<?=$time?>">
					<?=$commerce[$i]['NAME']?> 
				</a>
			</div>
			<div id="c2-<?=$time?>" class="accordion-body collapse in">
				<div class="accordion-inner">
					<div class="box-content nopadding">
						<ul class="tabs tabs-inline tabs-top">
							<li class='active'>
								<a href="#first11-<?=$time?>" data-toggle='tab'>Datos del Comer‭cio</a>
							</li>
							<li>
								<a href="#thirds33-<?=$time?>" data-toggle='tab'>Información Adicional</a>
							</li>
							
						</ul>
						<div class="tab-content padding tab-content-inline tab-content-bottom">
							<div class="tab-pane active" id="first11-<?=$time?>">
								<form id="commerceDataBasic-<?=$time?>" action="index.php" method="POST"  enctype="multipart/form-data" class='form-horizontal form-bordered'>
								<div class="control-group">
									<label for="textfield" class="control-label">api_key</label>
									<div class="controls"> 
										<?=$commerce[$i]['APIKEY']?>
									</div>
								</div>
								
								<div class="control-group">
									<label for="textfield" class="control-label">Estado</label>
									<div class="controls">
										<div class="input-prepend">
											<select name="commercestatus"  id="commercestatus" >
											<?php 	
												$statusList=array(array('IDSTATUS'=>'1','NAMESTATUS'=>'ACTIVO'),array('IDSTATUS'=>'0','NAMESTATUS'=>'INACTIVO'));
												foreach($statusList as $valueList){
													$selected=($commerce[$i]['STATUS']==$valueList['IDSTATUS'])?"selected":"";
													echo '<option value="'.$valueList['IDSTATUS'].'" type="text" '.$selected.' >'.$valueList['NAMESTATUS']."</option>";
												}
											?>
											</select>
										</div>
										<!--span class="helprocessEditprocessEditp-block">
											-
										</span-->
									</div>
								</div>
								<div class="control-group">
									<label for="textfield" class="control-label">Nombre</label>
									<div class="controls">
										<div class="input-prepend">
											<input name="nombre" value="<?=$commerce[$i]['NAME']?>" type="text" placeholder="">
										</div>
										
									</div>
								</div>
								<div class="control-group">
									<label for="textfield" class="control-label">Descripcion</label>
									<div class="controls">
										<div class="input-prepend">
											<input name="descripcion"  value="<?=$commerce[$i]['DESCRIPTION']?>"  type="text" placeholder="">
										</div>
										
									</div>
								</div>
								<div class="control-group">
									<label for="textfield" class="control-label">Email</label>
									<div class="controls">
										<div class="input-prepend">
											<input name="email"  value="<?=$commerce[$i]['EMAIL']?>"  type="text" placeholder="">
										</div>
										
									</div>
								</div>
								<div class="control-group">
									<label for="textfield" class="control-label">URL</label>
									<div class="controls">
										<div class="input-prepend">
											<input name="url"  value="<?=$commerce[$i]['URL']?>"  type="text" placeholder="">
										</div>
										
									</div>
								</div>
								<div class="control-group">
									<label for="textfield" class="control-label">Facebook</label>
									<div class="controls">
										<div class="input-prepend">
											<input name="facebook"  value="<?=$commerce[$i]['FACEBOOK']?>"  type="text" placeholder="">
										</div>
										
									</div>
								</div>
								
								<div class="control-group">
									<label for="textfield" class="control-label">Direccion</label>
									<div class="controls">
										<div class="input-prepend">
											<input name="direccion"  value="<?=$commerce[$i]['ADDRESS']?>"  type="text" placeholder="">
										</div>
									</div>
								</div>
								<div class="control-group">
									<label for="textfield" class="control-label">Telefono</label>
									<div class="controls">
										<div class="input-prepend">
											<input name="telefono"  value="<?=$commerce[$i]['PHONES']?>"  type="text" placeholder="">
										</div>
									</div>
								</div>
																
								<?=$imgs->formUploadFile(array(0=>$commerce[$i]['IMAGE']),"fileImage-".$commerce[$i]['IDCOMMERCE'],'Imagen Logo <a onclick="$(\'#commerceDataBasic-'.$time.'\').submit()" class="btn btn-primary">Actualizar Logo</a>',$commerce[$i]['FILEFOLDER']."/",TRUE)?> 
     
								<div class="control-group">
									<label for="textfield" class="control-label">Geolocalizaci&oacute;n</label>
									
									<div class="controls">
										Latitud: <input name="latitude"  value="<?=$commerce[$i]['LATITUDE']?>"  type="text" placeholder="">
										Longitud: <input name="longitude"  value="<?=$commerce[$i]['LONGITUDE']?>"  type="text" placeholder="">
									</div> 
								</div>
								  
								<?=$imgs->formUploadFile(array(0=>$commerce[$i]['MENU']),"filemenu-".$commerce[$i]['IDCOMMERCE'],'Archivo Menu <a onclick="$(\'#commerceDataBasic-'.$time.'\').submit()" class="btn btn-primary">Actualizar Menu</a><br/> (Max 2MB)',$commerce[$i]['FILEFOLDER']."/menu",FALSE)?>
   
									
								<div class="control-group">
									<label for="textfield" class="control-label">Ruta Principal Archivos</label>
									<div class="controls">
										<?=$commerce[$i]['FILEFOLDER']?>
									</div>
								</div>
								
								<?=$imgs->formUploadFile($commerce[$i]['FILEFOLDER']."/promociones","imgpromociones-".$commerce[$i]['IDCOMMERCE'],'Imagenes Promociones <a onclick="$(\'#commerceDataBasic-'.$time.'\').submit()" class="btn btn-primary">Actualizar Imagenes</a>',$commerce[$i]['FILEFOLDER']."/promociones")?>

															
								<?=$imgs->formUploadFile($commerce[$i]['FILEFOLDER']."/galeria","imggaleria-".$commerce[$i]['IDCOMMERCE'],'Imagenes Galeria <a onclick="$(\'#commerceDataBasic-'.$time.'\').submit()" class="btn btn-primary">Actualizar Imagenes</a>',$commerce[$i]['FILEFOLDER']."/galeria")?>
								
								
								
								
									<input type='hidden' name='formSaraData' value="<?=$formSaraDataCommerceAction?>">
									<input type='hidden' name='optionValue' value="<?=$commerce[$i]['IDCOMMERCE']?>">
									<input type='hidden' name='optionTab' value="basic">
									<div class="form-actions">
										<a onclick="updateForm('<?=$formSaraDataCommerce?>','#commerceDataBasic-<?=$time?>')" class="btn btn-primary">Actualizar</a>
									</div>
								</form>	
							</div>

							<!--TAB 2 -->
							
							<!--tab 3 -->

							<div class="tab-pane" id="thirds33-<?=$time?>">
								<form id="commerceDataTime-<?=$time?>" class="form-horizontal form-bordered" method="POST" action="index.php">
									<div class="control-group">
											<label for="textfield" class="control-label">Check In:	</label>
											<div class="controls">
												<div class="input-prepend">
													<input name="horapertura"  id="horapertura"  value="<?=$commerce[$i]['STARTTIME']?>"  type="text" placeholder="">
												</div>
												<span class="help-block">
													
												</span>
											</div>
									</div>
									
									<div class="control-group">
										<label for="textfield" class="control-label">Check Out:	</label>
										<div class="controls">
												<div class="input-prepend">
													<input name="horacierre"  id="horacierre"  value="<?=$commerce[$i]['ENDTIME']?>"  type="text" placeholder="">
												</div>
												<span class="help-block">
													
												</span>
										</div>
									</div>
									<div class="control-group">
										<label for="textfield" class="control-label">Politicas de Pago:	</label>
										<div class="controls">
												<textarea name="ppago" class=""><?=$commerce[$i]['PPAGO']?></textarea>
												<span class="help-block"></span> 
										</div>
									</div>
									<div class="control-group">
										<label for="textfield" class="control-label">Politicas Especiales:	</label>
										<div class="controls">
												<textarea name="pespeciales" class=""><?=$commerce[$i]['PESPECIALES']?></textarea>
												<span class="help-block"></span>
										</div>
									</div>
									
									<input type='hidden' name='optionValue' value="<?=$commerce[$i]['IDCOMMERCE']?>">
									<input type='hidden' name='optionTab' value="time">
									<div class="form-actions">
										<a onclick="updateForm('<?=$formSaraDataCommerce?>','#commerceDataTime-<?=$time?>')" class="btn btn-primary">Actualizar</a>
									</div>
								</form>
							</div>
							
							
						</div>
					</div>
				</div>
			</div>
		</div>
		<?PHP
		$i++;
		endwhile;
		?>
		<!--Fin Comercio-->
	</div>
					
</div>
