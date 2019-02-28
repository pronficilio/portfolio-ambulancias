<?php
session_start();
include("../../admin/classes/Agenda.class.php");
$ag = new Agenda("ajax/agenda/agregaActividad.php");

$ag->agregarTarea($_POST['idC'], date("Y-m-d H:i:s", strtotime($_POST['fecha']." ".$_POST['hora'])), $_POST['nota'], $_POST['act'], $_SESSION['_idUser']);