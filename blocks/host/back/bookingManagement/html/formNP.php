<form action="index.php" method="POST" class='form-horizontal form-bordered'>
  <div class="control-group" style="width:20%; float:left">
    <div for="textfield" class="labelSup">/</div>
    <?php foreach($grid['LABELS'] as $idlabelroom=>$label): ?>
        <div  id="room_<?=$idlabelroom?>" class="labelSup "> 
          <?=$label?>
        </div>
    <?php endforeach; ?>
  </div>
  <?php 
  for($i=1;$i<=$numDaysMonth;$i++):
  ?>
  <div class="control-group" style="width:<?=$widthCell?>%; float:left"> <? /* width:<?=$widthCell?>% */?>
    <div for="textfield" class="labelSup" ><?=$i?></div>
    <?php 
      foreach($grid['BOOKING'][$i] as $value): 
        $numBookings=count($bookings[$value]);
        $totalAdults=0;
        $totalChildren=0;
        $Infants=0;
        $bbc=0; //se recorren todas las reservas q pertenecen a la misma celda
        while(isset($bookings[$value][$bbc]['IDCELL'])){
          $totalAdults=$bookings[$value][$bbc]['NUMGUEST']+$totalAdults;	
          $totalChildren=$bookings[$value][$bbc]['NUMKIDS']+$totalChildren;
          $bbc++;
        }
        if($numBookings>0){
          $infoCell=$bookings[$value][0]['INFOCELL'];
        }else{
          $infoCell="-";
        }
        $classColor="";
        if($numBookings>0){
          $classColor="grey";
        }
        if($bookings[$value][0]['STATEBOOKING']==5){
          $classColor="black";
        }elseif($bookings[$value][0]['STATEBOOKING']==6){
          $classColor="yellow";
        }elseif($bookings[$value][0]['STATEBOOKING']==2){
          $classColor="green";
        }
      ?>
        <div style="width:95%; border:1px solid #FFFFFF" >
          <div id='<?=$value?>-<?=$bookings[$value][0]['DATESTART']?>' title="Adultos:<?=$totalAdults?> Niños:<?=$totalChildren?>" class="clickableElement <?=$classColor?>"  id="">
            <?php
              if($bookings[$value][0]['STATEBOOKING']<>5){
                echo $numBookings;
              }else{
                echo "B";
              }
            ?>
          </div>	
        </div>	
    <?php 
      endforeach;
    ?>
  </div>
  <?php endfor; ?>
</form>