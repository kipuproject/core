<div class="titulob">
	<div class="tituloimg">
		<h1>ADMINISTRAR TEMPORADAS</h1>
	</div>
</div>
<br/>

<script>
	$(document).ready(function(){
	  $("#year").change(function(){
			location.replace("<?=$formSaraDataUrl?>"+"&year="+$("#year").val());
	  });
	});
</script>

<select id="year">
	<option <?=($year=="2014")?"selected":""?> value="2014" >2014</option>
	<option <?=($year=="2015")?"selected":""?> value="2015" >2015</option>
</select>
	
<br/>

<?php 

  # definimos los valores iniciales para nuestro calendario .
	# Obtenemos el dia de la semana del primer dia 
	# Devuelve 0 para domingo, 6 para sabado 
	# Obtenemos el ultimo dia del mes 

	$meses=array(1=>"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"); ?> <!DOCTYPE html> 

	
	<?php
		foreach($season as $key=>$value){ 
	?>
			<a onclick="assignSeason('<?=$value['IDSEASON']?>','<?=$value['COLOR']?>','<?=$formSaraData?>')" class="button-season" style="background:<?=$value['COLOR']?>" ><?=$value['NAME']?></a>
	<?php
		}
	?>
	
	<div style="clear:both"></div>
	
	<?php
	for($month=1;$month<=12;$month++): 
	
		$diaSemana=date("w",mktime(0,0,0,$month,1,$year))+7; 
		$ultimoDiaMes=date("d",(mktime(0,0,0,$month+1,1,$year)-1)); 

    ?>

    <table id="calendar" class="box-month"> 
      <caption><?php echo $meses[$month]." ".$year?></caption> 
      <tr> 
        <th>Lun</th>
        <th>Mar</th>
        <th>Mie</th>
        <th>Jue</th>
        <th>Vie</th>
        <th>Sab</th>
        <th>Dom</th> 
      </tr> 
      <tr bgcolor="silver"> 

        <?php 
        
        $last_cell=$diaSemana+$ultimoDiaMes; 

        // hacemos un bucle hasta 42, que es el máximo de valores que puede 
        // haber... 6 columnas de 7 dias 

        for($i=1;$i<=42;$i++){ 
        
          if($i==$diaSemana) { 
            // determinamos en que dia empieza 
            $day=1; 
          }

          if($i<$diaSemana || $i>=$last_cell){ 
            // celda vacia
            echo "<td class='grey'> </td>"; 
          }
          else{ 
            //mostramos el dia
            $idDay=mktime(0,0,0,$month,$day,$year);
            if(isset($allDays[$idDay])){
              echo "<td id='$idDay' class='clickableElement' style='background:".$season[$allDays[$idDay]['IDSEASON']]['COLOR']."' >$day</td>";
            }else{
              echo "<td id='$idDay' class='clickableElement grey'>$day</td>";
            }
            $day++; 	
          } 
          
          //cuando llega al final de la semana, iniciamos una columna nueva
          if($i%7==0){
            echo "</tr><tr>\n"; 
          } 
        } 
        ?> 
       </tr> 
     </table> 
	<?php
	endfor;
	?> 