<?php
$indice=0;
$estilo[$indice++]="style.css";
$estilo[$indice++]="bootstrap.min.css";
$estilo[$indice++]="bootstrap-responsive.min.css";
$estilo[$indice++]="datatable/TableTools.css";
$estilo[$indice++]="chosen/chosen.css";


$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if($unBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$unBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"];
}

foreach ($estilo as $nombre){
	echo "<link rel='stylesheet' type='text/css' href='".$rutaBloque."/css/".$nombre."'>\n";

}
?>
