<?php
include("../../admin/classes/Agenda.class.php");
$ag = new Agenda("ajax/agenda/actualizaNota.php");
print_r($_POST);
if(!empty($_POST['fechaReag']))
	$fecha = date("Y-m-d H:i:s", strtotime($_POST['fechaReag']." ".$_POST['horaReag']));
else
	$fecha = "";
$ag->actualizaNota($_POST['id'], $_POST['nota'], $fecha);