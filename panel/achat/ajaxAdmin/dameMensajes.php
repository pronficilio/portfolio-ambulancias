<?php
include("../../admin/classes/Achat.class.php");
$chat = new Achat("ajax/dameMensajes.php");
$datos = $chat->dameMensajes($_POST['idChat'], false);
foreach($datos as $val){
    echo "<div class='mensaje'>";
    echo "<p class='";
    if($val['quien'] == "1"){
        echo "yo";
    }else{
        echo "el";
    }
    echo "'>";
    echo $val['texto'];
    echo "</p>";
    echo "</div>";
}