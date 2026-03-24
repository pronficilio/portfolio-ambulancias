<?php
$asunto = "---==:: Solicitud de factura ::==---";
if($_POST['tipo'] == '2'){
	$asunto = "---==:: Opinion ::==---";
}
$contenido = "";
foreach($_POST as $ind=>$val){
	if($ind != "tipo")
    	$contenido .= "<strong>".$ind."</strong>:<br> ".$val."<br><br>";
}
//echo $contenido;
if(!empty($contenido)){
	$contenido = "Datos recibidos:<br>".$contenido;
	require_once('PHPMailer/PHPMailerAutoload.php');
	$mail = new PHPMailer;
	$mail->SMTPAuth = true;
	$mail->SMTPDebug = 2;
	$mail->Mailer = "smtp";
	$mail->Host = "dtcwin106.ferozo.com";
	$mail->Port = 465;
	$mail->Username = "no-reply@medevacambulancias.com";
	$mail->addReplyTo("no-reply@medevacambulancias.com", "MEDEVACAMBULANCIAS");
	if($_POST['tipo'] == '2'){
		$mail->addAddress("direccion@gruposide.mx", "DIRECCION");
	}else{
		$mail->addAddress("facturacion@gruposide.mx", "FACTURACION");
	}
	$mail->Subject = $asunto;
	$mail->msgHTML($contenido);
	$var = $mail->send();
}