<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<table style="font-family:Arial,sans serif;border-collapse:collapse;width:100%!important;line-height:100%!important;background:white" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td style="border-collapse:collapse" valign="top"> 
        <table style="border-collapse:collapse;border-spacing:0;margin:50px auto 10px auto" align="center" border="0" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td background="https://ci3.googleusercontent.com/proxy/_FgvbWBm0BnJ8fsOPRGd4s8RLuVsaF9yjvI5jqAro2r7RgaIPW0-9mTbGfCDqLdmXuFSHrslQ28Ideb0wEygMAFq4aKWgQ=s0-d-e1-ft#http://www.kipu.co/media/email/left-corner3.png" width="16" height="16" style="background:url(https://ci3.googleusercontent.com/proxy/_FgvbWBm0BnJ8fsOPRGd4s8RLuVsaF9yjvI5jqAro2r7RgaIPW0-9mTbGfCDqLdmXuFSHrslQ28Ideb0wEygMAFq4aKWgQ=s0-d-e1-ft#http://www.kipu.co/media/email/left-corner3.png);background-repeat:no-repeat"></td>
              <td background="https://ci3.googleusercontent.com/proxy/IfcDQAb7rJwfwfYg8pQrDroJsXfeObDAcaGC6da5vsbhIls3xRK37ml6_d9AavL-YCd7_ND8w85QYPD7zJ9-fQOdNglv=s0-d-e1-ft#http://www.kipu.co/media/email/top-shadow2.png" style="background:url(https://ci3.googleusercontent.com/proxy/IfcDQAb7rJwfwfYg8pQrDroJsXfeObDAcaGC6da5vsbhIls3xRK37ml6_d9AavL-YCd7_ND8w85QYPD7zJ9-fQOdNglv=s0-d-e1-ft#http://www.kipu.co/media/email/top-shadow2.png);background-repeat:repeat-x" height="16" valign="top"></td>
              <td background="https://ci6.googleusercontent.com/proxy/FV3lvqMg6mXmh5lTHK0qIAXcvyl6dTopnl931t_BBkul5a7OTI978tmD9wdSgZWRr3HcaDQLwVptPpBgKsHJbKZeJJXoz34=s0-d-e1-ft#http://www.kipu.co/media/email/right-corner3.png" width="16" height="16" style="background:url(https://ci6.googleusercontent.com/proxy/FV3lvqMg6mXmh5lTHK0qIAXcvyl6dTopnl931t_BBkul5a7OTI978tmD9wdSgZWRr3HcaDQLwVptPpBgKsHJbKZeJJXoz34=s0-d-e1-ft#http://www.kipu.co/media/email/right-corner3.png);background-repeat:no-repeat"></td>
            </tr>
            <tr>
              <td background="https://ci6.googleusercontent.com/proxy/58JUzzcCDje02fY1xlCL-KJttXWvUNV4NsM4hBgTMtnUmvEx070HV8kwjr1En9XcYAhF6lkF8gqXc290tWTWRfeyD3AywQ=s0-d-e1-ft#http://www.kipu.co/media/email/left-shadow2.png" style="background:url(https://ci6.googleusercontent.com/proxy/58JUzzcCDje02fY1xlCL-KJttXWvUNV4NsM4hBgTMtnUmvEx070HV8kwjr1En9XcYAhF6lkF8gqXc290tWTWRfeyD3AywQ=s0-d-e1-ft#http://www.kipu.co/media/email/left-shadow2.png);border-collapse:collapse" valign="top" width="16">
              </td>
              <td style="border-collapse:collapse;border:1px solid #b7b7b7" valign="top">
                <table style="border-collapse:collapse" border="0" cellpadding="0" cellspacing="0" width="600">
                  <tbody>
                    <tr>
                       <td style="border-collapse:collapse" bgcolor="#ffffff" height="10"></td>
                    </tr>
                    <tr>
                        <td style="border-collapse:collapse" align="right" bgcolor="#ffffff" height="35" valign="middle">
                            <a href="" alt="" target="_blank">
                              <img src="<?=$this->rutaURL."/".$response->commerce['FOLDER']."/".$response->commerce['LOGO']?>"  style="padding-top:12px;padding-bottom:3px;padding-right:22px;outline:none;text-decoration:none" class="CToWUd"></a> 
                        </td>
                    </tr>
                    <tr>
                       <td style="border-collapse:collapse" bgcolor="#ffffff" height="5"></td>
                    </tr>
                    <tr>                  
                       <td background="http://www.campingvilladeleyva.com/files/imgs/correo.png" style="background:url(http://www.campingvilladeleyva.com/files/imgs/correo.png);background-repeat:repeat-x;background-color:#fff;border-collapse:collapse" height="99"><h1 style="font-family:Arial,sans-serif;font-size:24px;color:#ffffff;margin-left:0px;margin-top:0;margin-right:20px;margin-bottom:0;line-height:32px"><center>Confirmaci&oacute;n de Reserva <?=$response->commerce['NAME']?></center></h1></td>
                    </tr>
                  </tbody>
                </table>
                <table style="border-spacing:0!important;border-collapse:collapse!important;background:white!important" border="0" cellpadding="0" cellspacing="0" width="600">
                  <tbody>
                    <tr>
                      <td style="border-collapse:collapse;padding:15px 28px 5px 28px;line-height:20px;font-size:14px">                
                        <b>Responsable: <?=$response->responsible['NAME']." ".$response->responsible['LASTNAME']?> </b>.<br>
                        <b>Tel: </b> <?=$response->responsible['PHONE']?><br>
                        <b>E-mail: </b><?=$response->responsible['EMAIL']?><br>
                        <b>Observacion: </b><?=$response->booking['OBSERVATION_CLIENT']?><br>
                
                        <?php
                        /*if(is_array($data['ADDVALUES'])):
                            $av=0;
                            while(isset($data['ADDVALUES'][$av][0])){
                              
                              echo "<br><b>".$data['ADDFIELDS'][$data['ADDVALUES'][$av]['IDFIELD']]['NAMEFIELD']."</b>:".$data['ADDVALUES'][$av]['VALUE'];
                              $av++;
                            }
                        endif;*/
                        ?>
                        <p style="margin-top:15px;text-align:justify;color:gray;">
                          Por favor, al momento de tu llegada al hotel, diríjete a la recepción e identifícate. Menciona que tu reserva la hiciste a través del sitio web del hotel para una mejor atención. 
                        </p>
                        <table style="font-family:Arial,sans serif;border-collapse:collapse;width:100%!important;line-height:100%!important" border="0" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr>
                              <td style="border-collapse:collapse" valign="top"> 
                                <table style="border-collapse:collapse;border-spacing:0" align="center" border="0" cellpadding="0" cellspacing="0">
                                  <tbody>
                                    <tr style="background:#6f6e73;height:10px!important">
                                      <td style="width:225px;color:white;border-right:1px solid gray;font-size:12px;padding:0!important;margin:0!important;line-height:1px!important">
                                        <p style="margin:0px!important;padding:10px 0 10px 10px"><b>Tipo de Habitación</b></p>
                                      </td>
                                      <td style="width:90px;color:white;border-right:1px solid gray;font-size:10px;padding:0!important;margin:0!important">
                                        <center><p style="margin:0px!important;padding:10px 0 10px 0"><b>Check In</b></p></center>
                                      </td>
                                      <td style="width:70px;color:white;border-right:1px solid gray;font-size:10px;padding:0!important;margin:0!important">
                                        <center><p style="margin:0px!important;padding:10px 0 10px 0"><b>Check Out</b></p></center>
                                      </td>
                                      <td style="width:90px;color:white;border-right:1px solid gray;font-size:10px;padding:0!important;margin:0!important">
                                        <center><p style="margin:0px!important;padding:10px 0 10px 0"><b>No Noches</b></p></center>
                                      </td>
                                      <td style="width:60px;color:white;border-right:1px solid gray;font-size:10px;padding:0!important;margin:0!important">
                                        <center><p style="margin:0px!important;padding:10px 0 10px 0"><b>No Huespedes</b></p></center>
                                      </td>
                                    </tr>
                                    <tr style="border-left:1px solid #c3c3c3;border-right:1px solid #c3c3c3;border-bottom:1px solid #c3c3c3">
                                      <td style="width:225px;color:black;background-color:#f5f5f5;font-size:12px;border-right:1px solid #c3c3c3">
                                        <p style="margin:0px!important;padding:10px 0 10px 10px"><a> <?=$response->room['NAME']?></a></p>
                                      </td>
                                     <td style="width:90px;color:black;background-color:#f5f5f5;font-size:12px;border-right:1px solid #c3c3c3">
                                      <center><p style="margin:0px!important;padding:10px 0 10px 0;color:black"><b><?=date($response->booking['CHECKIN'])?></b></p></center>
                                     </td>
                                     <td style="width:70px;color:black;background-color:#f5f5f5;font-size:12px;border-right:1px solid #c3c3c3">
                                      <center><p style="margin:0px!important;padding:10px 0 10px 0;color:black"><b><?=date($response->booking['CHECKOUT'])?></b></p></center>
                                     </td>
                                     <td style="width:90px;color:black;background-color:#f5f5f5;font-size:12px;border-right:1px solid #c3c3c3">
                                      <center><p style="margin:0px!important;padding:10px 0 10px 0;color:#a9a9a9;"><?=round((($response->booking['CHECKOUT_UNIXTIME'])*1-($response->booking['CHECKIN_UNIXTIME'])*1)/86400)?></p></center>
                                     </td>
                                     <td style="border-right:1px solid #c3c3c3;font-size:12px;width:60px;background-color:#f5f5f5">
                                      <center><p style="margin:0px!important;padding:10px 0 10px 0">Adultos:<?=$response->booking['NUMGUEST']?><br/>Niños:<?=$response->booking['NUMKIDS']?></p></center>
                                     </td>
                                  </tr>
                                  </tbody>
                                </table>
                               </td>
                             </tr>
                           </tbody>
                        </table>
                        <p style="padding-top:10px;text-align:center;color:grey;">
                        <i>--Gracias por elegirnos, esperamos tu pronta llegada--</i>
                        </p>
                        <p style="font-size:14px;line-height:24px;margin:0;color:grey;">
                        Cualquier duda escríbenos al correo electrónico 
                        <a href="mailto:<?=$response->commerce['EMAIL']?>" target="_blank"> <?=$response->commerce['EMAIL']?></a>
                        </p>
                        <p style="font-size:14px;line-height:24px;margin:0;color:grey;">
                        Contáctanos en los siguientes números: <?=$response->commerce['PHONE']?>
                        <br>
                        <br>
                        </p>
                        <img href="<?=$response->commerce['FACEBOOK']?>" src="https://cdn0.iconfinder.com/data/icons/yooicons_set01_socialbookmarks/48/social_facebook_box_blue.png">
                        <img href="<?=$this->host?>" src="https://cdn0.iconfinder.com/data/icons/yooicons_set01_socialbookmarks/48/social_twitter_box_blue.png">
                        <p style="margin-top:15px;text-align:justify;color:gray;font-size:11px;border-top:solid 1px grey;">
                        Para pagos a través de consignación bancaria no olvides que tienes 1 día hábil para realizarla y así mantener tu reserva. Al momento de consignar enviános un correo con la verificación. 
                        <br>
                        Datos para la consignación: <?=$response->commerce['BANKACCOUNT']?>
                        </p>
                        <br> 
                          Nuestra Ubicación:
                          <img src="http://maps.googleapis.com/maps/api/staticmap?zoom=13&size=600x300&maptype=roadmap&markers=color:green%7Clabel:HM%7C<?=$response->commerce['LATITUDE']?>,<?=$response->commerce['LONGITUDE']?>&sensor=false" width="100%" height="250px" />
                      </td>
                    </tr>
                  </tbody>  
                </table>     
                <table background="https://ci6.googleusercontent.com/proxy/RbylHyffKvZH2_DnX9HlE1acmmlABGxLS7P8Fu3DN9DgzlS5xgVPaUu9w12j672BE49XEO0nkHgq5CgjBVUzkqxn=s0-d-e1-ft#http://www.kipu.co/media/email/footer1a.png" style="background:url(https://ci6.googleusercontent.com/proxy/RbylHyffKvZH2_DnX9HlE1acmmlABGxLS7P8Fu3DN9DgzlS5xgVPaUu9w12j672BE49XEO0nkHgq5CgjBVUzkqxn=s0-d-e1-ft#http://www.kipu.co/media/email/footer1a.png);background-repeat:no-repeat;border-spacing:0!important;border-collapse:collapse!important" border="0" cellpadding="0" cellspacing="0" width="600" height="106">
                  <tbody>  
                    <tr> 								
                    <td style="border-collapse:collapse" valign="top" width="100%" height="106">      
                    <p style="color:grey;font-size:14px;text-align:center;font-weight:bold;">
                    <br>
                    <img src="https://cdn0.iconfinder.com/data/icons/app_iconset_creative_nerds/32/star.png">
                    <img style="width: 21px;" src="https://cdn0.iconfinder.com/data/icons/app_iconset_creative_nerds/32/star.png">

                    <span style="text-align:center;"> <?=$this->miConfigurador->getVariableConfiguracion("nombreAplicativo")?></span>
                    <img  style="width: 21px;" src="https://cdn0.iconfinder.com/data/icons/app_iconset_creative_nerds/32/star.png">
                    <img src="https://cdn0.iconfinder.com/data/icons/app_iconset_creative_nerds/32/star.png">
                    </p>
                    <a href="http://www.kipu.co" alt="kipu.co" style="text-decoration:none!important" target="_blank">
                    <p style="color:#919192;font-size:10px;text-align:center;">
                    <br><span style="text-align:center;">Sistema de reservas desarrollado por <a href="www.kipu.co">Kreent</a></span>
                    </p>
                    </a>
                    </td>	 
                    <td valign="top" width="25" height="106">
                    </td> 
                    </tr>
                  </tbody>
                </table>
              </td>
              <td background="https://ci4.googleusercontent.com/proxy/eV0DE67ntf7rXPJbjwdzqSp2zQk7zl1vya6Rt47lnyx6JOZm522LHZMa3XmXnuIA2-B6fnDEC-fR3pT-FG1zSysXy9eYxhs=s0-d-e1-ft#http://www.kipu.co/media/email/right-shadow2.png" style="background:url(https://ci4.googleusercontent.com/proxy/eV0DE67ntf7rXPJbjwdzqSp2zQk7zl1vya6Rt47lnyx6JOZm522LHZMa3XmXnuIA2-B6fnDEC-fR3pT-FG1zSysXy9eYxhs=s0-d-e1-ft#http://www.kipu.co/media/email/right-shadow2.png);border-collapse:collapse" valign="top" width="16">
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
</body>
</html>