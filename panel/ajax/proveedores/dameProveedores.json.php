<?php
session_start();
require("../../admin/classes/Proveedores.class.php");

$prov = new Proveedores("ajax/proveedores/dameProveedores.json.php");

$_SESSION['guarda'] = $_GET;
$aux = $prov->getProveedoresJson($_SESSION['guarda']);

header('Content-Type: application/json; charset=utf-8');
$datos = $aux['data'];
$aux['data'] = array();
$i = 1;
foreach($datos as $val){
    $aux['data'][] = array(
        0 => $val['idProveedor'],
        1 => $val['nombre']."<br><small><i>Agregado <span data-livestamp='".strtotime($val['tiempo'])."'></span></i></small>",
        2 => $val['email'],
        3 => $val['telefono'],
        4 => "<div class='text-center'><div class='btn-group' role='group'>".
                 "<button type='button' class='btn btn-default editarProveedor _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Editar el proveedor' data-id='".$val['idProveedor']."'>".
                     "<span class='glyphicon glyphicon-pencil'></span>".
                 "</button>".
                 "<button type='button' class='btn btn-default enviarAccesoProveedor _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Enviar por mail el acceso' data-id='".$val['idProveedor']."'>".
                     "<span class='glyphicon glyphicon-envelope'></span>".
                 "</button>".
                 "<button type='button' class='btn btn-default enlazarProveedor _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Enlazar proveedor con categoria' data-id='".$val['idProveedor']."'>".
                     "<span class='glyphicon glyphicon-link'></span>".
                 "</button>".         
                 "<button type='button' class='btn btn-default eliminaAccesoProveedor _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Eliminar proveedor' data-id='".$val['idProveedor']."'>".
                     "<span class='glyphicon glyphicon-trash'></span>".
                 "</button>".
             "</div></div>"
    );
}
echo json_encode($aux);