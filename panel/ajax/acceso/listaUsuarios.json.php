<?php
session_start();
require("../../admin/classes/Acceso.class.php");
$ac = new Acceso("ajax/acceso/listaUsuarios.json.php");
//$_SESSION['h'] = $_GET;
$aux = $ac->getUsuariosJson($_GET);
$perm = $ac->damePermisosTodos();

header('Content-Type: application/json; charset=utf-8');
$res['draw'] = $aux['draw'];
$res['recordsTotal'] = $aux['recordsTotal'];
$res['recordsFiltered'] = $aux['recordsFiltered'];
$res['data'] = array();
foreach($aux['data'] as $fila){
    $act = array();
    $act[] = $fila['idUsuarios'];
    $act[] = ucwords($fila['name']).
             "<p class='text-left'><label class='label label-info'><i class='glyphicon glyphicon-user'></i> ".$fila['nombre']."</label></p>".
             "<p class='text-left'><label class='label label-primary'>".$fila['email']."</label></p>";
    $select = "<select class='form-control cambiarPermisos' data-id='".$fila['idUsuarios']."'>";
    foreach($perm as $val){
        $select .= "<option value='".$val['idPermiso']."' ".($fila['permisos']==$val['idPermiso']?"selected":"").">".$val['nombrePermiso']."</option>";
    }
    $act[] = $select;
    $act[] = "<div class='text-center'><div class='btn-group' role='group' data-us='".$fila['idUsuarios']."'>".
             "<button type='button' class='btn btn-default reenviarContrasena _tool' data-toggle='tooltip' title='Reenviar contraseña'>".
             "<span class='glyphicon glyphicon-envelope'></span></button>".
             "<button type='button' class='btn btn-default eliminaUsuario _tool' data-toggle='tooltip' title='Eliminar usuario'>".
             "<span class='glyphicon glyphicon-trash'></span></button></div></div>";
    $res['data'][] = $act;
}
echo json_encode($res);