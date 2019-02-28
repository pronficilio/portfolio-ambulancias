<?php
include("../../admin/classes/permisos.php");
$_bd = new Permisos("ajax/configuracion/modificarCE.php");

$_bd->modificaOpcion("compraMinima", $_POST['cnt']);