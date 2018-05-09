/*jslint browser: true*/
/*global $, jQuery, alert, console */
/* panel.js
 +----------------------------------------------------------------------+
 | Software: Fhasa                                                      |
 |  Version: 1.0                                                        |
 |      PHP: 5                                                          |
 +----------------------------------------------------------------------+
 |   Author: Isaí Landa Ortega <pronficilio@gmail.com>                  |
 | Copyright (c) Applett                                                |
 +----------------------------------------------------------------------+
 | Description: Funciones primordiales para el panel                    |
 +----------------------------------------------------------------------+
 | Modification: 2016-08-05                                             | 
 +----------------------------------------------------------------------+
 *
 * @package Fhasa
 * @author Isaí Landa
 * @copyright 2016 - 2017 Applett
 */
var last_msg = [];
var ini = 0;
var fin = 0;

function number_format(number, decimals, dec_point, thousands_sep) {
    "use strict";
    
    number = number.toString().replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return (Math.round(n * k) / k).toFixed(prec).toString();
        };
    s = (prec ? toFixedFix(n, prec) : Math.round(n)).split('.').toString();
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function scrollea(e, donde) {
    "use strict";
    $(donde).stop().animate({
        scrollTop: e.offset().top - 70
    }, 300);
}

function scrollAca(e) {
    "use strict";
    scrollea(e, 'html, body');
}

function isEmail(email) {
    "use strict";
    var regex = /^([a-zA-Z])+([a-zA-Z0-9_.+-])+\@(([a-zA-Z])+\.+?(com|co|in|org|net|edu|info|gob|mx|))\.?(com|co|in|org|net|edu|info|gob|mx)?$/;
    return regex.test(email);
}

function ponMensaje(mensaje, tipo, donde, dondeScroll) {
    "use strict";
    var mensajin = "<div class='alert " + tipo + " collapse'>";
    mensajin += "<span class='close'>&times;</span>";
    mensajin += mensaje;
    mensajin += "</div>";
    $(donde).append(mensajin);
    $(donde).children(".alert:last-child").slideDown("fast");
    last_msg[fin] = $(donde).children(".alert:last-child");
    fin += 1;
    scrollea($(donde).children(".alert:last-child"), dondeScroll);
    setTimeout(function () {
        last_msg[ini].slideUp(2000);
        ini += 1;
    }, 5000);
}

function mensaje(msg, tipo) {
    "use strict";
    ponMensaje(msg, tipo, "#contenedorAlertas", 'html, body');
}

function validaVacio(e) {
    "use strict";
    var err = false;
    if ($(e).val() === "" || $(e).val() === null) {
        $(e).parent().addClass("has-error");
        $(e).parent().removeClass("has-success");
        err = true;
    } else {
        $(e).parent().removeClass("has-error");
        $(e).parent().addClass("has-success");
    }
    return err;
}

function camposVacios(cadena) {
    "use strict";
    var datos = cadena.split(","),
        ultimo = "",
        error = false;
    datos.forEach(function (e, i) {
        if (validaVacio(e)) {
            error = true;
            ultimo = e;
        }
    });
    if (error) {
        scrollAca($(ultimo));
        $(ultimo).trigger("focus");
    }
    return error;
}

function limpiaFeedback(e) {
    "use strict";
    $(e).parent().removeClass("has-error");
    $(e).parent().removeClass("has-success");
}

function limpiaCampos(cadena, full) {
    "use strict";
    var datos = cadena.split(",");
    datos.forEach(function (e, i) {
        limpiaFeedback(e);
        if (full === 1) {
            $(e).val("");
        }
    });
}

function confirma(titulo, pregunta, accion) {
    "use strict";
    $("#modal_titulo").html("<span class='glyphicon glyphicon-info-sign'></span> " +
                            titulo + " <span class='glyphicon glyphicon-info-sign'></span>");
    $("#modal_texto").html(pregunta);
    $("#modal_accion").attr("onclick", accion);
    console.log(accion);
    $("#modal_confirma").modal("show");
}

function cargaTab(nombre) {
    "use strict";
    if ($("#" + nombre).html() === "") {
        $.ajax({
            cache: false,
            url: "tabs/" + nombre + ".html",
            success: function (data) {
                $("#" + nombre).html(data);
            }
        });
    }
}

$(function () {
    "use strict";
    $(document).on("click", ".close", function () {
        if ($(this).parent().hasClass("alert")) {
            $(this).parent().slideUp();
        }
    });
    $(document).on("click", "._tool", function () {
        $(this).tooltip("hide");
    });
});