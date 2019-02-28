<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$usu = new Acceso("dameDatosPersonales.json..php");
header('Content-Type: application/json; charset=utf-8'); 
$res = $usu->dameDatosPersonales($_SESSION['_idUser']);
echo json_encode($res);