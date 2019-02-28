<?php
session_start();
header('Content-Type: application/json; charset=utf-8'); 
include("../../admin/classes/Agenda.class.php");
$ag = new Agenda("ajax/agenda/actividades.json.php");
$datos = $ag->dameActividades($_GET['start'], $_GET['end']);

$res = array();
foreach($datos as $val){
    $check = "check";
    if($val['hecho'] == 0)
        $check = "unchecked";
    $act['title'] = "<span class='glyphicon glyphicon-".$check."'></span> ".ucfirst($val['tipo']);
    if(!empty($val['nombre']))
        $act['title'] .= " - ".$val['nombre'];
    $act['start'] = date("Y-m-d", strtotime($val['fecha']));
    if(date("H", strtotime($val['fecha'])) != "00"){
        $act['start'] .= "T".date("H:i:s", strtotime($val['fecha']))."-05:00";
    }
    $act['agenda'] = $val['idAgenda'];
    $res[] = $act;
    unset($act);
}
$datos = $ag->dameCarritosCerrados($_GET['start'], $_GET['end']);
foreach($datos as $val){
    $act['title'] = "<span class='glyphicon glyphicon-shopping-cart'></span> ".$val['nombre'];
    $act['start'] = date("Y-m-d", strtotime($val['fechaEntrega']))."T".date("H:i:s", strtotime($val['fechaEntrega']))."-05:00";
    $act['idCarrito'] = $val['idCarrito'];
    $res[] = $act;
}
echo json_encode($res);