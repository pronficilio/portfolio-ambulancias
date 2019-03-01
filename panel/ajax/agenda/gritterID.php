<?php
session_start();
$_SESSION[$_POST['pag'].'_ag_g'][$_POST['id']] = $_POST['idA'];