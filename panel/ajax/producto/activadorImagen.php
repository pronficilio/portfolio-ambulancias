<?php
require("../../admin/classes/Archivo.class.php");
$arch = new Archivo("producto/agregarProducto.php");
$arch->activadorImagen($_POST['id'], $_POST['estado']);