<?php
// parametros (post): $id
require("../../../admin/classes/Categoria.class.php");
$categoria1 = new Categoria("ajax/categoria/descuento/eliminarDescuento.php");
$respuesta = $categoria1->eliminarDescuento($_POST['idDescuento']);
?>