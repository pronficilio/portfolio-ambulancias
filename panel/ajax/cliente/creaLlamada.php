<?php
session_start();
require("../../admin/classes/Llamada.class.php");
$ll = new Llamada("ajax/cliente/creaLlamada.php");
$aux = $ll->agregarLlamada($_POST['idC'], $_POST['idT'], $_POST['fecha'], $_POST['comentario'], $_SESSION['_idUser']);
?>