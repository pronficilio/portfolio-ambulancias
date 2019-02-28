<?php
require("../../admin/classes/Cliente.class.php");
require("../../admin/classes/Llamada.class.php");
$ll = new Llamada("ajax/cliente/getClientesById.json.php");
$cli = new Cliente("ajax/cliente/getClientesById.json.php");
$aux = $cli->getClientes($_POST['id']);

header('Content-Type: application/json; charset=utf-8'); 

$aux['tel'] = $cli->getTelefonos($_POST['id']);
$aux['fecha'] = date("Y-m-d");

if(!empty($aux['direccion'])){
    $res = explode("\n", $aux['direccion']);
    $aux['calle'] = $aux['entre'] = $aux['col'] = $aux['ciudad'] = $aux['estado'] = $aux['cp'];
    foreach($res as $val){
        if("Calle: " == substr($val, 0, 7)){
            $aux['calle'] = substr($val, 7);
        }
        if("Entre calles: " == substr($val, 0, 14)){
            $aux['entre'] = substr($val, 14);
        }
        if("Colonia: " == substr($val, 0, 9)){
            $aux['col'] = substr($val, 9);
        }
        if("Ciudad: " == substr($val, 0, 8)){
            $aux['ciudad'] = substr($val, 8);
        }
        if("Estado: " == substr($val, 0, 8)){
            $aux['estado'] = substr($val, 8);
        }
        if("C. P.: " == substr($val, 0, 7)){
            $aux['cp'] = substr($val, 7);
        }
    }
}

$aux['historial'] = $ll->getLlamadas($_POST['id']);
foreach($aux['historial'] as $ind=>$val){
    $aux['historial'][$ind]['fecha'] = date("d-m-Y", strtotime($val['fecha']));
    $aux['historial'][$ind]['telefono'] = "";
    if(!empty($val['idTelefono'])){
        $datos = $ll->getTelefono($val['idTelefono']);
        $aux['historial'][$ind]['telefono'] = $datos['numero'];
        $aux['historial'][$ind]['label'] = $datos['label'];
    }
}

echo json_encode($aux);