<?php
require("../../admin/classes/Archivo.class.php");
$arch = new Archivo("producto/agregarProducto.php");

$arch->enlazaArchivo($_POST['idProd'], $_POST['idArch']);
?>