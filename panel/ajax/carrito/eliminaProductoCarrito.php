<?php
require("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/eliminaProductoCarrito.php");
if($carr->eliminaCarritoContenido($_POST['id']) === false)
    echo "Error: no se pudo eliminar el producto del carrito. ¿El carrito está abierto?";