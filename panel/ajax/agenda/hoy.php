<?php
session_start();
include("../../admin/classes/Agenda.class.php");
$ag = new Agenda("ajax/agenda/hoy.php");
/**
las actividades de hoy se van a mostrar asi:
FULL: actividades de todo el dia, estas siempre se muestran hasta que este en hecho.
H: Se muestra una hora antes del evento.
**/
$datos = $ag->getTareasDia(date("Y-m-d"), $_SESSION['_idUser']);
/**
unset($_SESSION['_ag_full']);
unset($_SESSION['_ag_h']);
/** **/
$res = array();
if(!empty($datos)){
    foreach($datos as $val){
        $val['full'] = false;
        $val['hoy'] = false;
        $val['fecha'] = strtotime($val['fecha']);
        if(date("H:i", $val['fecha']) == "00:00")
            $val['full'] = true;
        if(date("Y-m-d") == date("Y-m-d", $val['fecha'])){
            $val['hoy'] = true;
            //echo "............\n".date("Y-m-d H:i")." con ".date("Y-m-d H:i", $val['fecha'] - 3600)."\n";
            if(date("Y-m-d H:i") < date("Y-m-d H:i", $val['fecha'] - 3600)){
                continue; // no falta 1 hora para la actividad
            }
        }
        if(empty($_SESSION[$_GET['pag'].'_ag_h'][$val['idAgenda']])){
            $res[] = $val;
            $_SESSION[$_GET['pag'].'_ag_h'][$val['idAgenda']] = true;
        }
    }
}
header('Content-Type: application/json; charset=utf-8'); 
echo json_encode($res);