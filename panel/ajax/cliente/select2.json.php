<?php
require("../../admin/classes/Cliente.class.php");
header('Content-Type: application/json; charset=utf-8'); 
$cli = new Cliente("ajax/cliente/autocomplete.php");
$dat = $cli->select2cliente(filter_var($_POST['search'], FILTER_SANITIZE_STRING),
	filter_var($_POST['page'], FILTER_SANITIZE_STRING));
$res = $dat;
echo json_encode($res);