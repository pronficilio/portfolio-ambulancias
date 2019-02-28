<?php
include("../../admin/classes/permisos.php");
$_bd = new Permisos("ajax/configuracion/concretarVenta.php");

echo $_bd->opcion("compraMinima");