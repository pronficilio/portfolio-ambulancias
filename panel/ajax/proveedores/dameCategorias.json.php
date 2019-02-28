<?php
session_start();
require("../../admin/classes/Proveedores.class.php");

$prov = new Proveedores("ajax/proveedores/dameProveedores.json.php");

$res['data'] = $prov->dameCategorias($_POST['idProv']);

header('Content-Type: application/json; charset=utf-8');

$res['error'] = false;
echo json_encode($res);