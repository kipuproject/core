<div class="titulob">
<div class="tituloimg">
<h1>EDITAR CLIENTE</h1>
</div>
</div>
<div id="main_user">
  <div class="row-fluid">
      <div class="span12">
        <div class="box box-bordered">
          <div class="box-content nopadding">
            <form action="index.php" method="POST" class='form-horizontal form-bordered'>
              <div class="control-group">
                <label for="textfield" class="control-label">Nombre</label>
                <div class="controls">
                  <div class="input-prepend">
                    <input name="name" type="text" value="<?=$dataByID['NAME']?>" placeholder="">
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label for="textfield" class="control-label">Apellido</label>
                <div class="controls">
                  <div class="input-prepend">
                    <input name="lastname" type="text" value="<?=$dataByID['LASTNAME']?>" placeholder="">
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label for="textfield" class="control-label">Identificacion</label>
                <div class="controls">
                  <div class="input-prepend">
                    <input name="dni" type="text" value="<?=$dataByID['DNI']?>" placeholder="">
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label for="textfield" class="control-label">Nacionalidad</label>
                <div class="controls">
                  <div class="input-prepend">
                    <input name="country" type="text" value="<?=$dataByID['COUNTRY']?>" placeholder="">
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label for="textfield" class="control-label">Correo</label>
                <div class="controls">
                  <div class="input-prepend">
                    <input name="email" type="text" value="<?=$dataByID['EMAIL']?>" placeholder="">
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label for="textfield" class="control-label">Tel&eacute;fono</label>
                <div class="controls">
                  <div class="input-prepend">
                    <input name="phone" type="text" value="<?=$dataByID['PHONE']?>" placeholder="">
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label for="textfield" class="control-label">Estado</label>
                <div class="controls">
                  <select  id="my-select" name="status">
                    <option value="1" <?=($dataByID['STATUS']=="1")?"selected":""?> >ACTIVO</option>
                    <option value="0" <?=($dataByID['STATUS']=="0")?"selected":""?> >INACTIVO</option>
                  </select>
                  <span class="help-block">
                  </span>
                </div>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <button type="button" class="btn">Cancel</button>
              </div>
              <input type='hidden' name='formSaraData' value="<?=$formSaraData?>">
            </form>
          </div>
        </div>
      </div>
    </div>
</div>