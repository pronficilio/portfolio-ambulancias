<html style="width: 100%;position:absolute;">
    <body style="width: 100%;margin: 0px;">
        <div style="max-width:300px;padding-top:0px;margin: auto;height: auto;">
            <div style="background:white;width:280px;margin:0 auto;padding:5px 5px;">
                <table style="width:100%;">
                    <tr>
                        <td><img src="http://<?=$GLOBALS['g_sitio'].$GLOBALS['g_nueva'].$GLOBALS['g_logo'];?>" style="width:100%;"></td>
                    </tr>
                </table>
                <h1 style="color:#4d4d4d">Ticket de compra</h1>
                <big>No. de ticket: <?=$_SESSION['email']['idCarrito'];?><br></big>
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
                <?php if(!empty($_SESSION['email']['carritoRS'])): ?>
                RFC: <?=htmlentities($_SESSION['email']['carritoRS']);?>
                <br>
                <?php endif; ?>
                <?php if(!empty($_SESSION['email']['carritoE'])): ?>
                Teléfono: <?=htmlentities($_SESSION['email']['carritoE']);?>
                <br>
                <?php endif; ?>
                
                <br>
                <table style="width: 100%;">
                    <tr><th>Descripci&oacute;n</th><th style="text-align:right;">Subtotal</th></tr>
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
                        <td style="text-align:right;">Descuento: $</td>
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
                    <tr><td colspan="2"><hr></td></tr>
                    <tr>
                        <td style="text-align:right;">Forma de pago</td>
                        <td style="text-align:right;"></td>
                    </tr>
                    <?php if(!empty($_SESSION['email']['efectivo'])): ?>
                    <tr>
                        <td style="text-align:right;">Efectivo: $</td>
                        <td style="text-align:right;"><?=number_format($_SESSION['email']['efectivo'], 2);?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(!empty($_SESSION['email']['tarjeta'])): ?>
                    <tr>
                        <td style="text-align:right;">Tarjeta: $</td>
                        <td style="text-align:right;"><?=number_format($_SESSION['email']['tarjeta'], 2);?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(!empty($_SESSION['email']['cheque'])): ?>
                    <tr>
                        <td style="text-align:right;">Cheque: $</td>
                        <td style="text-align:right;"><?=number_format($_SESSION['email']['cheque'], 2);?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(!empty($_SESSION['email']['credito'])): ?>
                    <tr>
                        <td style="text-align:right;">Crédito: $</td>
                        <td style="text-align:right;"><?=number_format($_SESSION['email']['credito'], 2);?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="text-align:center;" colspan="2"><hr>Número de artículos: <?=$_SESSION['email']['articulos'];?></td>
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