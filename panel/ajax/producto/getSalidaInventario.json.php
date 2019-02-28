<?php
session_start();
require("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/producto/getProductos.json.php");
$aux = $prod->getSalidaProductoJson($_GET, $_GET['fechaIni'], $_GET['fechaFin']);
header('Content-Type: application/json; charset=utf-8'); 
echo json_encode($aux);