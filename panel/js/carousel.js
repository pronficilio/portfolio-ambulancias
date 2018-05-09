/*jslint browser: true*/
/*global $, jQuery, alert, console, limpiaCampos, moment, camposVacios, FormData, mensaje, confirma*/
/* grid.js
 +----------------------------------------------------------------------+
 | Software: Fhasa                                                      |
 |  Version: 1.0                                                        |
 |      PHP: 5                                                          |
 +----------------------------------------------------------------------+
 |   Author: Isaí Landa Ortega <pronficilio@gmail.com>                  |
 | Copyright (c) Applett                                                |
 +----------------------------------------------------------------------+
 | Description: Administrador del grid principal                        |
 +----------------------------------------------------------------------+
 | Modification: 2016-08-08                                             | 
 +----------------------------------------------------------------------+
 *
 * @package Fhasa
 * @author Isaí Landa
 * @copyright 2016 - 2017 Applett
 */

var tablaCarousel = "";

function actualizaCarousel() {
    "use strict";
    if (tablaCarousel === "") {
        tablaCarousel = $("#carousel_tabla").DataTable({
            serverSide: true,
            ajax: "ajax/carousel/dameCarousel.json.php",
            pageLength: 10,
            ordering: false,
            searching: false,
            lengthMenu: [10, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function () {
                $("#carousel_tabla ._tool").tooltip();
            },
            columns : [
                {width: "8%"},
                {},
                {width: "20%"}
            ]
        });
    } else {
        tablaCarousel.ajax.reload();
    }
}

function preparaModalAgregarCarousel() {
    "use strict";
    limpiaCampos("#modal_agregar_carousel_img", 1);
    $("#modal_agregar_carousel").modal("show");
}

function agregaCarousel() {
    "use strict";
    if (!camposVacios("#modal_agregar_carousel_img")) {
        var formData = new FormData($("#modal_agrega_carousel_form")[0]);
        $.ajax({
            contentType: false,
            processData: false,
            url: "ajax/carousel/agregaCarousel.php",
            data: formData,
            type: "post",
            success: function (data) {
                $("#modal_agregar_carousel").modal("hide");
                if (data !== "") {
                    mensaje(data, "alert-danger");
                } else {
                    mensaje("Se ha agregado correctamente en el carousel", "alert-success");
                    actualizaCarousel();
                }
            }
        });
    }
}

function eliminarCarousel(id) {
    "use strict";
    $.post("ajax/carousel/eliminaCarousel.php", "id=" + id, function (res) {
        if (res === "") {
            mensaje("Se ha eliminado el elemento del carousel", "alert-success")
            actualizaCarousel();
        } else {
            mensaje(res, "alert-danger");
        }
    });
}

$(function () {
    "use strict";
    $("#tabs a[href='#carousel']").on('shown.bs.tab', function (e) {
        if ($("#carousel").html() === "") {
            $.get("tabs/carousel.html", {"_": $.now()}, function (data) {
                $("#carousel").html(data);
                actualizaCarousel();
            });
        } else {
            actualizaCarousel();
        }
    });
    $(document).on("click", ".eliminaCarousel", function () {
        confirma("Eliminar imagen", "¿Estas seguro que deseas continuar?",
                 "eliminarCarousel(" + $(this).parent().attr("data-idPortada") + ");");
    });
});