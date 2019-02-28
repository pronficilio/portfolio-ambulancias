<?php
include("../../admin/classes/Agenda.class.php");
$ag = new Agenda("ajax/agenda/eliminaAct.php");
$ag->eliminaAgenda($_POST['id']);