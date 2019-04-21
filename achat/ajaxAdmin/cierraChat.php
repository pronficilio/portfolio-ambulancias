<?php
include("../../panel/admin/classes/Achat.class.php");
$chat = new Achat("ajax/verificaChat.php");
$chat->cierraChat($_POST['id']);