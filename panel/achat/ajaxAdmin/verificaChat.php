<?php
include("../../admin/classes/Achat.class.php");
$chat = new Achat("ajax/verificaChat.php");
$chat->cierraChats();