	<div class="main">
		<form name="loginForm" title="Formulario" method="post" enctype="multipart/form-data" id="loginForm">
    		<h1>Sistema de Reservas <br/> <?php echo $this->miConfigurador->getVariableConfiguracion("nombreAplicativo"); ?> </h1>
  			<div class="inset">
	  			<p>
            <label for="email">Usuario </label>
            <input type="text" tabindex="1" data-validate="validate(required, minlength(1))" maxlength="100" size="" id="atadusuario" name="atadusuario" class="cuadroTexto ">
          </p>
  				<p>
				    <label for="password">Clave </label> 
				    <input type="password" tabindex="2" data-validate="validate(required)" maxlength="100" size="" value="" id="atadclave" name="atadclave" class="cuadroTexto ">
  				</p>
 			 </div>
			<input type="hidden" value="<?=$valorCodificado?>" id="formSaraData" name="formSaraData">
			  <p class="p-container">
        <input type="submit" value="Login">
			  </p>
		</form>
	</div>  
			<!-----start-copyright---->
	<div class="copy-right">
		<p><a target="_blank" href="http://kipu.co">kipu</a> by <a target="_blank" href="http://kreent.com">kreent</a></p> 
	</div>



