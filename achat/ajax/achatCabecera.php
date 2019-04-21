<?php
include("../../panel/admin/classes/Achat.class.php");
$ac = new Achat("achat/ajax/achatDisponible.json.php");
echo $ac->cabecera();