<?php
session_start();
require("../../admin/classes/Cliente.class.php");
require("../../admin/classes/Carrito.class.php");
require("../../admin/classes/Producto.class.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = false;
$res['msg'] = "";
$res['post'] = $_POST;
$carr = new Carrito("ajax/barcode/finalizaPuntoVenta.php");
$pro = new Producto("ajax/barcode/finalizaPuntoVenta.php");
$cli = new Cliente("ajax/barcode/finalizaPuntoVenta.php");
$idCarrito = 0;
$map = array();
if(!empty($_POST['idCarrito'])){
	$idCarrito = $_POST['idCarrito'];
	$datos = $carr->getCarritoById($idCarrito);
	foreach($datos['contenido'] as $c){
		$map[$c['idProducto']] += $c['cantidad'];
	}
}
if(!empty($_POST['prod']) && count($_POST['prod'])){
	foreach($_POST['prod'] as $idProducto=>$cantidad){
	    $map[$idProducto] += $cantidad;
	}
}
foreach($map as $idProducto=>$cantidad){
	$producto = $pro->getProducto($idProducto);
	if($producto['enInventario'] < $cantidad){
		$res['msg'] .= "Faltan (".($cantidad-$producto['enInventario']).") de ".$producto['nombre']."\n";
		$res['error'] = true;
	}	
}
if(!$res['error']){
	$idCliente = $_POST['idCliente'];
	if(!empty($_POST['idCliente']) && !is_numeric($_POST['idCliente']) && is_string($_POST['idCliente']))
		$idCliente = $cli->agregarCliente($_POST['idCliente']);

	if(empty($_POST['idCarrito']))
		$idCarrito = $carr->creaCarrito($idCliente, true);
	if(!empty($_POST['prod']) && count($_POST['prod'])){
		foreach($_POST['prod'] as $idProducto=>$cantidad){
		    $carr->agregaCarrito($idCarrito, $idProducto, $cantidad);
		}
	}
	if($_POST['descuento'])
		$carr->descuentazo($idCarrito, $_POST['descuento']);
	$carr->sinGastosEnvio($idCarrito);
	if($_POST['soloGuarda']=="1"){
		if(!empty($_POST['requiereFactura']))
			$carr->aplicaFactura($idCarrito, $_POST['razonSocial'], $_POST['correoFactura']);
	}else{
		if(empty($_POST['requiereFactura']))
			$carr->aplicaFactura($idCarrito, "", "", 0);
		$carr->finalizaCarrito($idCarrito, $_SESSION['_idUser'],
			$_POST['tarjeta'], $_POST['cheque'], $_POST['efectivo'], $_POST['cuenta'], $_POST['credito'],
			$_POST['requiereFactura'], $_POST['razonSocial'], $_POST['correoFactura']);
		if(!empty($_POST['credito']))
			$carr->quitaSaldo($idCliente, $_POST['credito']);
		$_SESSION['_lastCarrito'] = $idCarrito;
	}
}
echo json_encode($res);