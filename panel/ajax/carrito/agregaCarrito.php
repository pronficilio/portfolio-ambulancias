<?php
require("../../admin/classes/Carrito.class.php");
require("../../admin/classes/Producto.class.php");
$carr = new Carrito("ajax/carrito/agregaCarrito.php");
$pro = new Producto("ajax/carrito/agregaCarrito.php");
if($carr->carritoAbierto($_POST['idCarr'])){
	$producto = $pro->getProducto($_POST['idProd']);
	if($producto['enInventario'] < $_POST['cant']){
		echo "Faltan (".($_POST['cant']-$producto['enInventario']).") de ".$producto['nombre'];
	}
    if(!$carr->agregaCarrito($_POST['idCarr'], $_POST['idProd'], $_POST['cant']))
        echo "Error: no se pudo agregar al carrito. Verifica los datos";
}else{
    echo "Error: el carrito esta cerrado, no se pueden agregar más productos";
}