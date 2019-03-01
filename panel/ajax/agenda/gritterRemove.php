<?php
session_start();
unset($_SESSION[$_POST['pag'].'_ag_h'][$_SESSION[$_POST['pag'].'_ag_g'][str_replace("gritter-item-", "", $_POST['id'])]]);
unset($_SESSION[$_POST['pag'].'_ag_g'][str_replace("gritter-item-", "", $_POST['id'])]);