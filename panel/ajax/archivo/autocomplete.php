<?php
require("../../admin/classes/Archivo.class.php");
$arch = new Archivo("ajax/archivo/autocomplete.php");
$dat = $arch->busca($_GET['q']);
$res = $dat;/*array();
foreach($dat as $val){
    $actual['nombre'] = $val['nombre'];
    $actual['id'] = $val['idCliente'];
    $res[] = $actual;
}*/
echo json_encode($res);
?>