<?php
session_start();
include("../../admin/classes/Agenda.class.php");
$ag = new Agenda("ajax/agenda/hecho.php");
$ag->setEstado($_POST['id'], $_POST['estado'], $_SESSION['_idUser']);