<?php 
  $mensaje = '';
  if( base64_decode( $_GET['msn'] ) == 'Ok_1' ){
    $mensaje = '<div style="background: #088A85; color: #FFFFFF; padding: 5px; text-align: center;"> Su mensaje ha sido enviado. Nos pondremos en contacto con usted.<br> &iexcl;Gracias! </div>';
  }else if( base64_decode( $_GET['msn'] ) == 'Err_1' ){
    $mensaje = '<div style="background: #990000; color: #FFFFFF; padding: 5px; text-align: center;"> El captcha es incorrecto. Por favor vuelva a intentarlo </div>';
  }else if( base64_decode( $_GET['msn'] ) == 'Err_2' ){
    $mensaje = '<div style="background: #990000; color: #FFFFFF; padding: 5px; text-align: center;"> SU CORREO NO PUDO ENVIARSE. Por favor vuelva a intentarlo </div>';
  }else if( base64_decode( $_GET['msn'] ) == 'Err_3' ){
    $mensaje = '<div style="background: #990000; color: #FFFFFF; padding: 5px; text-align: center;"> SU CORREO NO PUDO ENVIARSE. Por favor llene todos los campos requeridos. Gracias </div>';        
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>MEDEVAC Ambulancias</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="author" content="Rodrigo Marín" />
  <meta name="description" content="Medevac ambulancias, ambulancias, asistencia medica, paramedico, translados, pacientes, urgencias, terapia intensiva movil" />

  <link rel="shortcut icon" href="images/medevac.ico">
  <link href="css/style.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="css/coin-slider.css" />
  <link rel="stylesheet" type="text/css" href="css/validationEngine.jquery.css" />

  <script type="text/javascript" src="js/cufon-yui.js"></script>
  <script type="text/javascript" src="js/cufon-quicksand.js"></script>
  <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
  <script type="text/javascript" src="js/coin-slider.min.js"></script>
  <script type="text/javascript" src="js/jquery.validationEngine.js"></script>
  <script type="text/javascript" src="js/jquery.validationEngine-es.js" charset="utf-8"></script>
  <script>
    jQuery(document).ready(function(){
      jQuery("#formulario_contacto").validationEngine();
      jQuery("#recaptcha_response_field").addClass("validate[required]");
    });
  </script>
</head>

<body>

  <div class="main">
    <div class="header">
      <div class="header_resize">
        <div class="logo"><a href="index.html"><img src="images/logo-medevac.jpg" /></a></div>
        <div class="menu_nav">
          <ul>
            <li><a href="index.html"><span>Inicio</span></a></li>
            <li><a href="la-empresa.html"><span>La empresa</span></a></li>
            <li><a href="servicios.html"><span>Servicios</span></a></li>
            <li><a href="clientes.html"><span>Clientes</span></a></li>            
            <li class="active"><a href="contacto.php"><span>Contacto</span></a></li>
          </ul>
        </div>

        <div class="clr"></div>

        <!-- SLIDE SHOW -->
        <div class="slider">
          <div id="coin-slider"> <a href="#"><img src="images/slide01.jpg" width="935" height="293" alt="" /> </a> <a href="#"><img src="images/slide02.jpg" width="935" height="293" alt="" /> </a> <a href="#"><img src="images/slide01.jpg" width="935" height="293" alt="" /> </a> </div>
          <div class="clr"></div>
        </div>
        <!-- /SLIDE SHOW -->

        <div class="clr"></div>

      </div>
    </div>

<div class="clr"></div>

<div class="content">
  <div class="content_resize">
    <div class="mainbar">
        <div class="article">
          <h2><span>Contacto</span></h2>
          <div class="clr"></div>
          <p>Todos los campos marcados con <span class="requerido">*</span> son requeridos</p>
          <?php echo $mensaje; ?>
        </div>
        <div class="article">
          <h2><span>Enviar</span> Mensaje </h2>
          <div class="clr"></div>
          <form action="fcontacto.php" method="post" id="formulario_contacto" name="formulario_contacto">
            <ol>
              <li>
                <label for="contacto_nombre">Nombre <span class="requerido">*</span></label>
                <input type="text" id="contacto_nombre" name="contacto_nombre" class="text validate[required]" maxlength="30" value="<?php echo $_POST["contacto_nombre"];?>" />
              </li>
              <li>
                <label for="contacto_correo">Correo electrónico <span class="requerido">*</span></label>
                <input type="text" id="contacto_correo" name="contacto_correo" class="text validate[required,custom[email]]" maxlength="30" value="<?php echo $_POST["contacto_correo"];?>"/>
              </li>
              <li>
                <label for="contacto_mensaje">Mensaje <span class="requerido">*</span></label>
                <textarea id="contacto_mensaje" name="contacto_mensaje" rows="8" cols="50" class="validate[required]" maxlength="500"><?php echo $_POST["contacto_mensaje"];?></textarea>
              </li>
              <li>
                <?php
                  require_once('correo/recaptchalib.php');
                  $publickey = "6Lco-vQSAAAAAIpu86gX4vI5bPBg0zdT1MWsTvgJ";
                  echo recaptcha_get_html($publickey);
                ?>
                <script type="text/javascript">
                  var RecaptchaOptions = {
                     lang : 'es',
                     theme : 'white'
                  };
                </script>
              </li>
              <li>
                <input type="hidden" name="MMDSI_control" id="MMDSI_control" value="enviar_contacto">
                <input type="image" name="imageField" id="imageField" src="images/submit.gif" class="send" />
                <div class="clr"></div>
              </li>
            </ol>
          </form>
        </div>
      </div>

<div class="sidebar">

      <div class="gadget">
        <h2 class="star">Acceso rápido</h2>

        <div class="clr"></div>

        <ul class="sb_menu">
          <li><a href="index.html"><span>&raquo; Inicio</span></a></li>
          <li><a href="la-empresa.html"><span>&raquo; La empresa</span></a></li>
          <li><a href="servicios.html"><span>&raquo; Servicios</span></a></li>
          <li><a href="clientes.html"><span>&raquo; Clientes</span></a></li>
          <li class="active"><a href="contacto.php"><span>&raquo; Contacto</span></a></li>
        </ul>
      </div>

      <div class="gadget">
        <!-- Aquí puede ir un banner -->
      </div>

    </div>
<!-- -->
<div class="clr"></div>

   
    
  </div>
</div>

<div class="fbg">
  <div class="fbg_resize">
    <div class="col c1">
      <h2><span>Gelería</span></h2>
      <a href="#"><img src="images/gal1.jpg" width="75" height="75" alt="" class="gal" /></a> <a href="#"><img src="images/gal2.jpg" width="75" height="75" alt="" class="gal" /></a> <a href="#"><img src="images/gal3.jpg" width="75" height="75" alt="" class="gal" /></a> <a href="#"><img src="images/gal4.jpg" width="75" height="75" alt="" class="gal" /></a> <a href="#"><img src="images/gal5.jpg" width="75" height="75" alt="" class="gal" /></a> <a href="#"><img src="images/gal6.jpg" width="75" height="75" alt="" class="gal" /></a> </div>
      <div class="col c2">
        <h2><span>Servicios</span> Recomendados</h2>
        <p align="justify">Siendo nuestra especialidad, el servicio de ambulancias para transportación de pacientes en la modalidad de urgencia o terapia intensiva, cuenta con el personal médico y paramédico mejor preparado, así como con las unidades más especializadas del Estado de Morelos, legalizadas ante la secretaría de Salud del Estado de acuerdo con la Norma Oficial Mexicana NOM-237-SSA1-2004, equipadas con  equipo electromédico para la atención integral, soporte cardiovascular y ventilatorio.</p>
      </div>
      <div class="col c3">
        <h2><span>Contacto</span></h2>
        <p>Nueva Grecia No.2, Fraccionamiento Rincón de Valle, Cuernavaca Morelos. C.P. 62240.</p>
        <p class="contact_info"> <span>Teléfono:</span>01 (777) 245 36 08<br />
          <span>Teléfono:</span> 01 (777) 261 68 39<br />
          <span>ID:</span> 52*14*2383<br />
          <span>Correo:</span> <a href="mailto: medevac.ambulancias@gmail.com">medevac.ambulancias@gmail.com</a> </p>
        </div>

        <div class="clr"></div>

      </div>
    </div>
    <div class="footer">
      <div class="footer_resize">
        <p class="lf">2014 &copy; Copyright MEDEVAC ambulancias.</p>
        <p class="rf">Diseñado por <a href="#">SYSTEMP</a> &amp; <a href="http://www.dsienlinea.com/" target="_blank">DSI en Línea</a></p>
        <div style="clear:both;"></div>
      </div>
    </div>
  </div>
</body>
</html>