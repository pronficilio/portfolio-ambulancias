<?php
include("../../panel/admin/classes/Achat.class.php");
$ac = new Achat("achat/ajax/achatDisponible.json.php");
header('Content-Type: application/json; charset=utf-8');
if($_GET['hola']){
	echo date("Y-m-d H:i:s")." ".$ac->chatDisponible();
}
echo json_encode($ac->chatDisponible()>0);