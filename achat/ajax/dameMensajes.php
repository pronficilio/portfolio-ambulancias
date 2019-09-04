<?php
session_start();
include("../../panel/admin/classes/Achat.class.php");
$chat = new Achat("achat/ajax/dameMensajes.php");
$mostrar = $chat->chatDisponible()>0;
if($chat->estadoChat($_SESSION['chatActivo']) == 0){
    unset($_SESSION['chatActivo']);
    unset($_SESSION['inicioChat']);
}
if(!empty($_SESSION['chatActivo']) && $mostrar){
    $datos = $chat->dameMensajes($_SESSION['chatActivo']);
    foreach($datos as $val){
        echo "<div class='achat-mensaje'>";
        echo "<p class='achat-";
        if($val['quien'] == "0"){
            echo "yo right-block";
        }else{
            echo "el left-block";
        }
        echo "'>";
        echo $val['texto'];
        echo "</p>";
        echo "</div>";
    }
}
?>