<?php
require("../../admin/classes/Cliente.class.php");
$cli = new Cliente("ajax/cliente/autocomplete.php");
$dat = $cli->filtro($_GET['q']);
$res = $dat;
echo json_encode($res);