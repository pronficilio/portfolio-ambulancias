<?php
session_start();
require("../../admin/classes/Agenda.class.php");
$ag = new Agenda("ajax/agenda/getAgenda.json.php");

$aux = $ag->getAgendaJson($_GET);

header('Content-Type: application/json; charset=utf-8');
$total = array();
foreach($aux['data'] as $fila){ //idAgenda, c.nombre, a.idCliente, idUsuarios, hechoPor, fecha, fechaRegistro, notas, tipo, hecho
    $arr = array();
    $arr[] = $fila['idAgenda'];
    $arr[] = $fila['nombre'];
    $arr[] = ucfirst($fila['tipo']);
    $fecha = date("d-m-Y", strtotime($fila['fecha']));
    if(date("H", strtotime($fila['fecha'])) != "00"){
        $fecha .= "<br>".date("H:i", strtotime($fila['fecha']));
    }
    $fecha .= "<br><i class='small'><span data-livestamp='".strtotime($fila['fecha'])."'></span></i>";
    $arr[] = "<p class='text-center'>".$fecha."</p>";
    $arr[] = $fila['notas'];
    $epa = "<div class='text-center'><div class='btn-group' role='group' data-ag='".$fila['idAgenda']."'>";
    $epa .= "<button type='button' class='btn btn-default verDetallesAct _tool' data-toggle='tooltip' title='Ver/Editar detalles'>";
    $epa .= "<span class='glyphicon glyphicon-list-alt'></span>";
    $epa .= "</button>";
    if($fila['hecho'] == 0){
        $epa .= "<button type='button' class='btn btn-default tareaHecha _tool' ag='".$fila['idAgenda']."' data-toggle='tooltip' title='Clic para marcar como hecha la tarea'>";
        $epa .= "<span class='glyphicon glyphicon-unchecked'></span>";
        $epa .= "</button>";
    }
    $epa .= "<button type='button' class='btn btn-default eliminaActividad _tool' data-toggle='tooltip' title='Clic para eliminar la tarea'>";
    $epa .= "<span class='glyphicon glyphicon-trash'></span>";
    $epa .= "</button>";
    $epa .= "</div></div>";
    $arr[] = $epa;
    $total[] = $arr;
}
$aux['data'] = $total;
echo json_encode($aux);