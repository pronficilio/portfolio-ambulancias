<?php 
  if( isset( $_POST['MMDSI_control'] ) && $_POST['MMDSI_control'] == 'enviar_contacto' ) {
    $privatekey = "6Lco-vQSAAAAACuGTdRXkPYZbucDFQMIt8tHE8hA";
    $resp = recaptcha_check_answer ($privatekey,
                  $_SERVER["REMOTE_ADDR"],
                  $_POST["recaptcha_challenge_field"],
                  $_POST["recaptcha_response_field"]);
    
    if (!$resp->is_valid) {
      $msn = base64_encode('Err_1'); 
      header("Location: contacto.php?msn=".$msn);
    } else {

      if(isset($_POST["contacto_nombre"]) && isset($_POST["contacto_correo"]) && isset($_POST["contacto_mensaje"]) ){

        $nombre_contacto = strip_tags( htmlspecialchars( $_POST['contacto_nombre'] ) );
        $correo_electronico_contacto = strip_tags( $_POST['contacto_correo'] );
        $mensaje = strip_tags( htmlspecialchars( $_POST['contacto_mensaje'] ) );

        $to = "dsienlinea.media@gmail.com";
        $subject = "Comentario desde página Web - www.medevacambulancias.mx";

        $contenido .= "Nombre: ".$nombre_contacto."\n";
        $contenido .= "Correo electrónico: ".$correo_electronico_contacto."\n\n";
        $contenido .= "Comentario: ".$mensaje."\n\n";
        $header = "From: contacto@medevacambulancias.mx\n";
        $header .= "Mime-Version: 1.0\n";
        $header .= "Content-Type: text/plain";

        if( mail($to, $subject, $contenido ,$header) ){
          $msn = base64_encode('Ok_1'); 
          header("Location: contacto.php?msn=".$msn);
        }
        else {
          $msn = base64_encode('Err_2'); 
          header("Location: contacto.php?msn=".$msn);
        }
      }
      else{
          $msn = base64_encode('Err_3'); 
          header("Location: contacto.php?msn=".$msn);
      }
    }
  }
?>