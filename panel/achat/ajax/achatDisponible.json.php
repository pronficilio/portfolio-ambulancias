<?php
include("../../admin/classes/Achat.class.php");
$ac = new Achat("achat/ajax/achatDisponible.json.php");
header('Content-Type: application/json; charset=utf-8'); 
echo json_encode($ac->chatDisponible()>0);