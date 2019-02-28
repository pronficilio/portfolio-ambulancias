<?php
session_start();
include("../../admin/classes/Achat.class.php");
$chat = new Achat("ajax/iniciaChat.php");
$chat->creaMensaje($_POST['idChat'], $_POST['mensaje'], 1);