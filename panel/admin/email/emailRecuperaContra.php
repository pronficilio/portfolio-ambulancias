<?php session_start(); ?>
<html>
    <body>
        <div style="width:600px;background:#d9d7d7;padding-top:30px">
            <div style="background:white;width:500px;margin:0 auto;border:0.5px solid #e5e5e5;padding:30px 20px;box-shadow:0px 0.5px 1px #909090;">
                <table style="width:100%;">
                    <tr>
                        <td><img src="http://<?=$GLOBALS['g_sitio'].$GLOBALS['g_nueva'].$GLOBALS['g_logo'];?>"></td>
                        <td style="text-align:right;"><i><?=date("d / M / Y");?></i></td>
                    </tr>
                </table>
                <h1 style="color:#4d4d4d">Recuperaci&oacute;n de contrase&ntilde;a</h1>
                <p>Si tu no pediste recuperar tu contrase&ntilde;a, ignora este mensaje.<br></p>
                <p style="text-align:center;">
                    <br>
                    <a href="http://<?=$GLOBALS['g_sitio'].$GLOBALS['g_nueva']."/panel/".$_SESSION['email']['link'];?>" target="_blank" style="background: #5bd0be;padding:15px 13px;color:white;font-weight:bolder;word-spacing:5px;">
                        Haz clic aquí para recuperar tu contrase&ntilde;a
                    </a><br><br><br>
                    <i><small>O copia y pega el siguiente link: http://<?=$GLOBALS['g_sitio'].$GLOBALS['g_nueva']."/panel/".$_SESSION['email']['link'];?></small></i>
                </p>
            </div>
            <div style="margin-top:40px;padding-bottom:20px;text-align:center;font-size:85%">
                <?=$GLOBALS['g_principal_title'];?>
                <address><?=$GLOBALS['g_direccion'];?></address>
                <address><?=$GLOBALS['g_footerEmail'];?></address>
            </div>
        </div>
    </body>
</html>