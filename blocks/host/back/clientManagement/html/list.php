<div class="titulob">
  <div class="tituloimg">
  <h1>ADMINISTRAR CLIENTES</h1>
  </div>
</div>
<div id="main_user">
	<div class="row-fluid">
		<div class="span12">
			<div class="box box-color box-bordered">
				<div class="box-title">
          <div class="form-actions" style="background:transparent !important; border:none !important; margin:0px !important; ">
            <a style="cursor:pointer" href="<?=$formSaraDataNew?>" >CREAR</a>
          </div>
        </div>
				<div class="box-content nopadding">
					<table class="table table-hover table-nomargin table-bordered dataTable-columnfilter dataTable">
						<thead>
							<tr class='thefilter'>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
							<tr>
								<th>Nombre</th>
								<th>Identificacion</th>
								<th>Nacionalidad</th>
								<th>Correo</th>
								<th>Telefono</th>
								<th>Estado</th>
								<th>Opcion</th>
							</tr>
						</thead>
						<tbody>
					
							<?php
							$i=0;
							while(isset($dataList[$i][0])):
								$link=$this->getUrlLinksbyId($dataList[$i]['ID']);
							?>	
							<tr>
								<td><?php echo $dataList[$i]['NAME']; ?> <?php echo $dataList[$i]['LASTNAME']; ?></td>
								<td><?php echo $dataList[$i]['DNI']; ?></td>
								<td><?php echo $dataList[$i]['COUNTRY']; ?></td>
								<td><?php echo $dataList[$i]['EMAIL']; ?></td>
								<td><?php echo $dataList[$i]['PHONE']; ?></td>
								<td><?=($dataList[$i]['STATUS']=='1')?"Activo":"Inactivo"?></td>
								<td>
									<a style="cursor:pointer" href="<?php echo $link['edit']; ?>" class="btn" rel="tooltip" title="Edit"><i class="icon-search"></i></a>
								</td>
							</tr>
							<?php
							$i++;
							endwhile;
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>