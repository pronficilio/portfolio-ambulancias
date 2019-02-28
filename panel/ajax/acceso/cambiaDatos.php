<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$usu = new Acceso("ajax/cambiaDatos.php");
$posibleId = $usu->existeUsuario($_POST['username']);
if($posibleId==0 || $posibleId==$_SESSION['_idUser']){
    $res = $usu->modificaUsuario($_SESSION['_idUser'], $_POST['username'], $_POST['nombre'], $_POST['email']);
    if(!$res)
        echo "Error: No se ha podido modificar el nombre del usuario";
    else
        $_SESSION['_user'] = $_POST['username'];
    if(!empty($_POST['nueva_contra_1'])){
        if(!$usu->modificaContra($_SESSION['_idUser'], $_POST['nueva_contra_1'])){
            if(!$res)
                echo "<br>";
            echo "Error: No se ha podido modificar la contraseña del usuario";
        }
    }
}else{
    echo "Error: Usuario en uso, elige otro";
}