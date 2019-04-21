<?php
session_start();
include("../../panel/admin/classes/Achat.class.php");
$chat = new Achat("achat/ajax/iniciaChat.php");
$chat->creaMensaje($_SESSION['chatActivo'], $_POST['mensaje'], 0);