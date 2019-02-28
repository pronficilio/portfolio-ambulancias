<?php
include("../../admin/classes/Achat.class.php");
$chat = new Achat("ajax/verificaChat.php");
$datos = $chat->dameChats();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($datos);