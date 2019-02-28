<?php
include("../../admin/classes/Achat.class.php");
$ac = new Achat("achat/ajax/achatDisponible.json.php");
echo $ac->cabecera();