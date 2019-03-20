<?php
session_start();
require("../../admin/classes/Cliente.class.php");
include("../../admin/classes/Acceso.class.php");
$acc = new Acceso("ajax/producto/getProductos.json.php");
$arrPerm = $acc->dameMisPermisos($_SESSION['_idUser']);

$cli = new Cliente("ajax/cliente/getClientes.json.php");
$aux = $cli->getClienteJson($_GET);

header('Content-Type: application/json; charset=utf-8'); 

$contador = 1;
$total = array();
foreach($aux['data'] as $fila){
    $telefonos = $cli->getTelefonos($fila['idCliente']);
    $compras = $cli->comprasConcretadas($fila['idCliente']);
    $arr = array();
    $arr[] = $contador++;
    $N = $fila['nombre'].
        "<p class='text-muted'>".
        "<span class='_tool' data-toggle='tooltip' title='Fecha de alta'><span class='glyphicon glyphicon-calendar'></span> <span>".date("d-m-Y", strtotime($fila['tiempo']))."</span></span><br>";
    if($compras > 0)
        $N .= "<span class='_tool' data-toggle='tooltip' title='Compras concretadas'><span class='glyphicon glyphicon-check'></span> <span>".$compras."</span></span><br>";
    
    foreach($telefonos as $val)
        $N .= "<span class='_tool' data-toggle='tooltip' title='".$val['label']."'><span class='glyphicon glyphicon-earphone'></span> <span>".$val['numero']."</span></span> &nbsp; ";
    
    $N .= "</p>";
    $arr[] = $N;
    $arr[] = $fila['email'];
    $arr[] = $fila['categoria'];
    $arr[] = $fila['tipoTienda'];
    $arr[] = "<span class='text-muted'><i>".$fila['notasCliente']."</i></span>";
    $N = "<div class='text-center'><div class='btn-group' role='group' data-idCliente='".$fila['idCliente']."'>";
    if(in_array(".editaCliente", $arrPerm)){
        $N .= "<button type='button' class='btn btn-default editaCliente _tool' data-toggle='tooltip' title='Haz clic para editar los datos del cliente'>";
        $N .= "<span class='glyphicon glyphicon-pencil'></span>";
        $N .= "</button>";
    }
    if(count($telefonos) > 0)
        $lla = "title='Hacer llamada' onclick='creaLlamada(".$fila['idCliente'].");'";
    else
        $lla = "title='Agrega un teléfono para hacer una llamada' disabled";
    if(in_array(".creaLlamada", $arrPerm)){
        $N .= "<button type='button' class='btn btn-default _tool' data-toggle='tooltip' ".$lla.">";
        $N .= "<span class='glyphicon glyphicon-earphone'></span>";
        $N .= "</button>";
    }
    if(in_array(".eliminaCliente", $arrPerm)){
        $N .= "<button type='button' class='btn btn-default eliminaCliente _tool' data-toggle='tooltip' title='Haz clic para eliminar al cliente'>";
        $N .= "<span class='glyphicon glyphicon-trash'></span>";
        $N .= "</button>";
    }
    $N .= "</div></div>";
    $arr[] = $N;
    $total[] = $arr;
}
$aux['data'] = $total;
echo json_encode($aux);
