<?php
session_start();
include("../../panel/admin/classes/Achat.class.php");
$chat = new Achat("achat/ajax/iniciaChat.php");

if(empty($_SESSION['chatActivo'])){
    $_SESSION['chatActivo'] = $chat->creaChat($_POST['nombre'], $_POST['email'], $_POST['pregunta'], $_POST['telefono']);
    if(empty($_SESSION['chatActivo'])){
        echo "Error: el email que ingresaste no es válido";
    }else{
        $_SESSION['inicioChat'] = date("Y-m-d H:i");
        if($_POST['enviaMail'] == true){
	        require_once('../../php/PHPMailer/PHPMailerAutoload.php');
			$mail = new PHPMailer;
			$mail->SMTPAuth = true;
			$mail->Mailer = "smtp";
			$mail->Host = "dtcwin106.ferozo.com";
			$mail->Port = 465;
			$mail->Username = "no-reply@medevacambulancias.com";
			$mail->Password = "S*T611J8yH";
			$mail->setFrom("no-reply@medevacambulancias.com", "MEDEVACAMBULANCIAS");
			$mail->addReplyTo("no-reply@medevacambulancias.com", "MEDEVACAMBULANCIAS");
			$mail->addAddress("ambulancias.gruposide@gmail.com", "ADMIN");
			$mail->Subject = "Pregunta recibida por chat";
			$mail->msgHTML("Nombre: ".$_POST['nombre']."<br>Email: ".$_POST['email']."<br>Telefono: ".$_POST['telefono'].
				"<br>Pregunta: ".$_POST['pregunta']);
			$var = $mail->send();
		}
    }
}
