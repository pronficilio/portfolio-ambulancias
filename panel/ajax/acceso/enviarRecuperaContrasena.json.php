<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$ac = new Acceso("enviarRecuperaContrasena.json.php");
header('Content-Type: application/json; charset=utf-8');
$ac->emailRestablecerPass($_POST['id']);
$res['error'] = false;
echo json_encode($res);