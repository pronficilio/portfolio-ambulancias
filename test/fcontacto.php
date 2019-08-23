<?php
 //Variables del formulario de contacto

@$nombre = addslashes($_POST['contacto_nombre']);
 @$email = addslashes($_POST['contacto_correo']);
 @$mensaje = addslashes($_POST['contacto_mensaje']);

//Mensaje de contacto
 $cabeceras = "From: MEDEVAC"
  . "Reply-To: $emailn";
 $asunto = "Mensaje desde la pagina Web";
 $email_to = "dan.roag@gmail.com";
 $contenido = "$nombre ha enviado un mensaje desde la web www.neoteo.comn"
 . "n"
 . "Nombre: $nombren"
 . "Email: $emailn"
 . "Sitio Web: $webn"
 . "Mensaje: $mensajen"
 . "n";
 //Enviamos y resultados del mensaje
 if (@mail($email_to, $asunto ,$contenido ,$cabeceras )) {

//Confirmación mensaje Ok
 die("Su mensaje se envió correctamente, en la brevedad nos pondremos en contacto con usted. Gracias Neoteo");
 }else{
 //Error en el envió
 die("Error: Su mensaje no pudo ser enviado, intente nuevamente");
 }
 ?>
