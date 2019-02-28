<?php
session_start();
if(!empty($_SESSION['_idUser'])){
    if($_POST['url'] == "index")
        echo "window.location = 'panel.html';";
}else{
    if($_POST['url'] != "index")
        echo "window.location = 'index.html';";
}
?>