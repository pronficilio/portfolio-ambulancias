<?php
include("../../admin/classes/permisos.php");
$_bd = new Permisos("ajax/configuracion/modificarND.php");

$_bd->modificaOpcion("noDisponible", $_POST['msg']);