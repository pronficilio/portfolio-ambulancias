<?php
session_start();
require("../../admin/classes/Carrito.class.php");
require("../../admin/classes/Producto.class.php");
$carr = new Carrito("ajax/carrito/agregaCarrito.php");
$pro = new Producto("ajax/carrito/agregaCarrito.php");
$map = array();
header('Content-Type: application/json; charset=utf-8');
$res['error'] = false;
$res['msg'] = "";
foreach($_POST['prod'] as $idProducto=>$cantidad){
	$map[$idProducto] += $cantidad;
}
foreach($map as $idProducto=>$cantidad){
	$producto = $pro->getProducto($idProducto);
	if($producto['enInventario'] < $cantidad){
		$res['msg'] .= "Faltan (".($cantidad-$producto['enInventario']).") de ".$producto['nombre']."<br>";
		$res['error'] = true;
	}	
}
echo json_encode($res);