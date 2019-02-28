<?php
include("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/quitaCarrito.php");

$datos = $carr->dameCarritoContenido($_POST['id']); // idp, can, precuni, idcarrito
if(!empty($datos)){
	$carr->agregaAInventario($datos['idProducto'], $datos['cantidad']);
	$CR = $carr->dameCarritoRapido($datos['idCarrito']); // idc, idd, fac
	$costo = $datos['precioUnitario']*$datos['cantidad'];
	$descuento = $carr->dameValorDescuento($CR['idDescuento']);
	if(!empty($CR['factura'])){
		$costo *= 1.16;
	}
	if(!empty($descuento)){
		$costo = $costo*(1-($descuento/100));
	}
	$carr->agregaSaldo($CR['idCliente'], $costo);
	$carr->eliminaCarritoContenido($_POST['id']);
}