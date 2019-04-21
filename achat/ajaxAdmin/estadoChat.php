<?php
include("../../panel/admin/classes/Achat.class.php");
$chat = new Achat("ajax/dameMensajes.php");
echo $chat->estadoChat($_POST['idChat']);