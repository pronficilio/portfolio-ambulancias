<?php
session_start();
require("../../admin/classes/Agenda.class.php");
require("../../admin/classes/Acceso.class.php");
$acc = new Acceso("ajax/agenda/getAgenda.json.php");
$ag = new Agenda("ajax/agenda/getAgenda.json.php");

$aux = $ag->getAgendaById($_POST['id']);
if(!empty($aux)){
    $aux['tipo'] = ucfirst($aux['tipo']);
    $aux['hp'] = $acc->dameNombre($aux['hechoPor']);
    $aux['mio'] = $_SESSION['_idUser'] == $aux['idUsuarios'];
}

header('Content-Type: application/json; charset=utf-8'); 

echo json_encode($aux);
?>