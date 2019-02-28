<?php
session_start();
require_once("../admin/clases/PHPExcel.php");
require_once("../admin/clases/PHPExcel/IOFactory.php");
$direccion=$_FILES['archivo']['tmp_name'];
$inputFileName = $direccion;
//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
$objeto = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
if(!empty($objeto) && is_array($objeto)){
	if(count($objeto) > 1){
		$_SESSION['archivoE'] = $objeto;
	}else{
		echo "El archivo no tiene informacion";
	}
}else{
	echo "El archivo esta vacio";
}


/*$abecedario=["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
$limY=sizeof($objeto);
$limX=sizeof($objeto[1]);
$datos=[];
$j=0;
foreach ($objeto[1] as $fila) {
	$datos[$fila]=[];//nueva posicion para la columna
	for($i=2;$i<=$limY;$i++){ //Desde 2 hasta fin
		$datos[$fila][]=$objeto[$i][$abecedario[$j]];
	}
	$j++;
}
$_SESSION["archivo"]=$datos;
$tam=sizeof($_SESSION["archivo"]["Fecha DDMMAA"]);
if($tam>0&&$tam==sizeof($_SESSION["archivo"]["Guía CIE"])&&$tam==sizeof($_SESSION["archivo"]["Referencia"])&&$tam==sizeof($_SESSION["archivo"]["Concepto"])&&$tam==sizeof($_SESSION["archivo"]["Importe"])){
}else{
	echo "Error: Hacen falta campos o no coindiden con el formato.";
}*/
//echo "<pre>";
//	print_r($datos);
//echo "</pre>";
//echo $_SESSION["archivo"];
?>