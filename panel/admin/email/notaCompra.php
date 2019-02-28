<html style="width: 100%;position:absolute;">
    <body style="width: 100%;margin: 0px;">
        <div style="background: #cecece;max-width:800px;padding-top:0px;margin: auto;height: auto;">
            <div style="background:white;width:780px;margin:0 auto;padding:5px 5px;">
                <table style="width:100%;">
                    <tr>
                        <td style="text-align:center;background:black;"><img src="http://<?=$GLOBALS['g_sitio'].$GLOBALS['g_nueva'].$GLOBALS['g_logo'];?>" style="width:100%;max-width: 500px;"></td>
                    </tr>
                </table>
                <h1 style="color:#4d4d4d">Nota de compra</h1>
                <address>
                    <?=htmlentities($GLOBALS['g_rfc_razon']);?><br>
                    <?=htmlentities($GLOBALS['g_rfc_calle']);?><br>
                    Col. <?=htmlentities($GLOBALS['g_rfc_colonia']);?><br>
                    <?=htmlentities($GLOBALS['g_rfc_municipio_estado']);?><br>
                    México, C.P. <?=htmlentities($GLOBALS['g_rfc_cp']);?><br>
                    Tel. 55796640
                </address>
                <?php if(!empty($_SESSION['email']['cliente'])): ?>
                <br>
                Cliente: <?=htmlentities($_SESSION['email']['cliente']);?>
                <br>
                <?php endif; ?>
                
                <br>
                <table style="width: 100%;">
                    <tr><td>Descripci&oacute;n</td><td style="text-align:right;">Subtotal</td></tr>
                </table>
                <hr>
                <?php $tot = 0; foreach($_SESSION['email']['productos'] as $val): ?>
                <p style="margin-bottom:0px;"><?=htmlentities($val['producto']);?></p>
                <p style="text-align:right;margin-top:0px;border-bottom:1px dotted black;">
                    <?=number_format($val['precioUnitario'], 2);?> x <?=$val['cnt'];?>.00 PZA = <?=number_format($val['sub'], 2);?>
                </p>
                <?php $tot += $val['sub']; endforeach; ?>
                <table style="width:100%;">
                    <tr>
                        <td style="text-align:right;">Importe: $</td>
                        <td style="text-align:right;"><?=number_format($tot, 2);?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Descuento: </td>
                        <td style="text-align:right;"><?=$_SESSION['email']['descuento'];?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">IVA: $</td>
                        <td style="text-align:right;"><?=number_format($_SESSION['email']['iva'], 2);?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Total: $</td>
                        <td style="text-align:right;"><?=number_format($_SESSION['email']['total'], 2);?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Falta pagar: $</td>
                        <td style="text-align:right;"><?=number_format($_SESSION['email']['cuenta'], 2);?></td>
                    </tr>
                </table>
            </div>
            <div style="margin-top:40px;padding-bottom:20px;text-align:center;font-size:85%">
                <?=$GLOBALS['g_principal_title'];?>
                <address><?=$GLOBALS['g_direccion'];?></address>
                <address><?=$GLOBALS['g_footerEmail'];?></address>
            </div>
            <div style="display: block;page-break-before: always;"></div>
        </div>
    </body>
</html>