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

var tablaGrid = "";

function actualizaListaGrid() {
    "use strict";
    if (tablaGrid === "") {
        tablaGrid = $("#grid_tabla").DataTable({
            serverSide: true,
            ajax: "ajax/grid/dameGrid.json.php",
            pageLength: 10,
            lengthMenu: [10, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function () {
                $("#grid_tabla ._tool").tooltip();
                $("#grid_tabla ._moment").each(function (i, e) {
                    $(e).html(moment($(e).attr("time"), 'YYYY-MM-DD HH:mm:ss').format('D[/]MMM[/]YYYY'));
                });
            },
            order: [ 0, 'asc' ],
            columns : [
                {width: "8%"},
                {orderable: false},
                {},
                {},
                {},
                {width: "15%", orderable: false}
            ]
        });
    } else {
        tablaGrid.ajax.reload();
    }
}

function preparaModalAgregarGrid() {
    "use strict";
    limpiaCampos("#modal_agregar_cabecera,#modal_agregar_texto,#modal_agregar_url,#modal_agregar_img", 1);
    $("#modal_agregar_grid").modal("show");
}

function preparaModalModificarGrid(id) {
    "use strict";
    limpiaCampos("#modal_modificar_cabecera,#modal_modificar_texto," +
                 "#modal_modificar_url,#modal_modificar_img,#modal_modificar_grid_id", 1);
    $.post("ajax/grid/dameGridById.json.php", "id=" + id, function (res) {
        console.log(res);
        $("#modal_modificar_grid_id").val(id);
        $("#modal_modificar_cabecera").val(res.titulo);
        $("#modal_modificar_texto").val(res.texto);
        $("#modal_modificar_url").val(res.url);
        $("#modal_modificar_grid").modal("show");
    }, "json");
}

function agregaGrid() {
    "use strict";
    if (!camposVacios("#modal_agregar_cabecera,#modal_agregar_img")) {
        var formData = new FormData($("#modal_agrega_grid_form")[0]);
        $.ajax({
            contentType: false,
            processData: false,
            url: "ajax/grid/agregaGrid.php",
            data: formData,
            type: "post",
            success: function (data) {
                $("#modal_agregar_grid").modal("hide");
                if (data !== "") {
                    mensaje(data, "alert-danger");
                } else {
                    mensaje("Se ha agregado correctamente el grid", "alert-success");
                    actualizaListaGrid();
                }
            }
        });
    }
}

function eliminaGrid(id) {
    "use strict";
    $.post("ajax/grid/eliminaGrid.php", "id=" + id, function (res) {
        if (res === "") {
            actualizaListaGrid();
        } else {
            mensaje(res, "alert-success");
        }
    });
}

function modificarGrid() {
    "use strict";
    if (!camposVacios("#modal_modificar_cabecera")) {
        var formData = new FormData($("#modal_modificar_grid_form")[0]);
        $.ajax({
            contentType: false,
            processData: false,
            url: "ajax/grid/modificaGrid.php",
            data: formData,
            type: "post",
            success: function (data) {
                $("#modal_modificar_grid").modal("hide");
                if (data !== "") {
                    mensaje(data, "alert-danger");
                } else {
                    mensaje("Se ha modificado correctamente el grid", "alert-success");
                }
            }
        });
    }
}

$(function () {
    "use strict";
    $("#tabs a[href='#grid']").on('shown.bs.tab', function (e) {
        if ($("#grid").html() === "") {
            $.get("tabs/grid.html", {"_": $.now()}, function (data) {
                $("#grid").html(data);
                actualizaListaGrid();
            });
        } else {
            actualizaListaGrid();
        }
    });
    $(document).on("click", ".modificaGrid", function () {
        console.log("me cago en...");
        console.warn($(this).parent().attr("data-idGridPrincipal"));
        preparaModalModificarGrid($(this).parent().attr("data-idGridPrincipal"));
    });
    $(document).on("click", ".eliminaGrid", function () {
        confirma("Eliminar grid", "¿Estas seguro que deseas continuar?",
                 "eliminaGrid(" + $(this).parent().attr("data-idGridPrincipal") + ");");
    });
});