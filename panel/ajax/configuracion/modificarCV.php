<?php
include("../../admin/classes/permisos.php");
$_bd = new Permisos("ajax/configuracion/concretarVenta.php");

$_bd->modificaOpcion("concretarVenta", $_POST['msg']);