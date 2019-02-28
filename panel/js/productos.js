/*jslint browser: true*/
/*global $, jQuery, alert, console, mensaje, DataTable, camposVacios, FormData, limpiaCampos, ponMensaje, ajaxToHtml, ajaxFast, number_format, confirma */
/*productos.js
 +----------------------------------------------------------------------+
 | Software: Crm-shop                                                   |
 |  Version: 1.0                                                        |
 |      PHP: 5                                                          |
 +----------------------------------------------------------------------+
 | Copyright (c) Applett                                                |
 +----------------------------------------------------------------------+
 | Description: Primeras funciones del panel                            |
 +----------------------------------------------------------------------+
 | Modification: 2016-08-29                                             | 
 +----------------------------------------------------------------------+
 *
 * @package Fhasa
 * @copyright 2016 - 2017 Applett
 */
var tablaProductos = "";
var llamadorModalCreador;
var auto_archivo = false;

function actualizaListaProductos() {
    "use strict";
    if (tablaProductos === "") {
        tablaProductos = $("#productos_tabla").DataTable({
            serverSide: true,
            ajax: "ajax/producto/getProductos.json.php",
            pageLength: 10,
            lengthMenu: [10, 15, 20, 100, 1000],
            language: {
                url: "locales/datatable.es.json"
            },
            dom: 'B<"clearfix">lfrtip',
            buttons: [{
                extend: 'collection',
                text: 'Exportar',
                fade: true,
                buttons: [
                    {
                        extend: 'print', className: 'blue btn-outline', text:'Imprimir',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    }, {
                        extend: 'copy', className: 'red btn-outline',text:'Copiar',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    }, {
                        extend: 'excel', className: 'yellow btn-outline ',text:'Excel',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    }
                ]
            }, {
                extend: 'colvis', className: 'dark btn-outline', text: 'Columnas', columns: ':not(:last-child)'
            }],
            fnDrawCallback: function () {
                $("#productos_tabla ._tool").tooltip();
            },
            order: [ 1, 'asc' ],
            columns : [
                {orderable: false, width: "5%"},
                {orderable: true},
                {orderable: true},
                {orderable: true},
                {orderable: true},
                {orderable: true},
                {orderable: false}
            ]
        });
    } else {
        tablaProductos.ajax.reload();
    }
}

function agregaProducto() {
    "use strict";
    var error = false, formData;
    if (!camposVacios("#new_producto_nombre,#new_producto_categoria,#new_producto_precio,#new_producto_descripcion")) {
        if ($("#new_producto_inventario_max").val() !== "" && $("#new_producto_inventario_min").val() !== "") {
            if (parseInt($("#new_producto_inventario_max").val(), 10) < parseInt($("#new_producto_inventario_min").val(), 10)) {
                error = true;
                mensaje("La cantidad de artículos máximos y mínimos en inventario no cuadra", "alert-danger");
            }
        }
        if (!error) {
            formData = new FormData($("#form_producto")[0]);
            console.log(formData);
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                url: "ajax/producto/agregarProducto.php",
                data: formData,
                type: "post",
                success: function (data) {
                    if (data === "") {
                        limpiaCampos("#new_producto_nombre,#new_producto_categoria,#new_producto_precio,#new_producto_descripcion," +
                                     "#new_producto_inventario_max,#new_producto_inv,#new_producto_inventario_min,#Rnew_producto_imagen," +
                                     "#new_producto_pdf,#new_producto_iva", 1);
                        $("#new_producto_categoria").trigger("change");
                        $("#new_producto_disponible").val(1);
                        mensaje("Se ha agregado el producto correctamente", "alert-success");
                    } else {
                        mensaje(data, "alert-danger");
                    }
                }
            });
        }
    }
}

function limpiaModalEditarProducto() {
    "use strict";
    $("#editProdSubcat_full").collapse("hide");
    limpiaCampos("#editProdNombre,#editProdCategoria,#editProdPrecio,#editProdInventario,#editProdInventario_max,#editProdInventario_min,#editProdDescripcion,#editProdIVA", 1);
    $("#editProdDescripcion").html("");
    $(".editProdSubcategoria").eq(0).html("");
    $("#editProdArchivos").html("");
    $("#mensajesEditaProducto").html("");
}

function editaProducto() {
    "use strict";
    var error = false, formData;
    if (!camposVacios("#editProdNombre,#editProdCategoria,#editProdPrecio,#editProdDescripcion")) {
        if ($("#editProdInventario_max").val() !== "" && $("#editProdInventario_min").val() !== "") {
            if (parseInt($("#editProdInventario_max").val(), 10) < parseInt($("#editProdInventario_min").val(), 10)) {
                error = true;
                ponMensaje("La cantidad de artículos máximos y mínimos en inventario no cuadra", "alert-danger", "#mensajesEditaProducto", "#modalEditaProducto");
            }
        }
        if (!error) {
            formData = new FormData($("#form_edita_producto")[0]);
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                url: "ajax/producto/modificarProducto.php",
                data: formData,
                type: "post",
                success: function (data) {
                    if (data === "") {
                        limpiaModalEditarProducto();
                        $("#modalEditaProducto").modal("hide");
                        mensaje("Se ha editado el producto correctamente", "alert-success");
                        actualizaListaProductos();
                    } else {
                        ponMensaje(data, "alert-danger", "#mensajesEditaProducto", "#modalEditaProducto");
                    }
                }
            });
        }
    }
}

function limpiaModalAgregarImagenes() {
    "use strict";
    limpiaCampos("#new_producto_imagen", 1);
    $("#imagenesActuales").html("");
    $("#mensajesEditaImagenes").html("");
}

function agregaImagenes() {
    "use strict";
    var error = false, formData;
    if (!camposVacios("#new_producto_imagen")) {
        formData = new FormData($("#form_edita_imagenes")[0]);
        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            url: "ajax/producto/agregarImagenes.php",
            data: formData,
            type: "post",
            beforeSend: function () {
                $("#agrega_imagen_progress").children("div").css("width", "0%");
                $("#agrega_imagen_progress").children("div").html("Agregando imágenes... [0%]");
                $("#agrega_imagen_progress").removeClass("hidden");
            },
            success: function (data) {
                if (data === "") {
                    limpiaModalAgregarImagenes();
                    $("#modalEditaImagenes").modal("hide");
                    mensaje("Se han agregado las imágenes correctamente", "alert-success");
                    actualizaListaProductos();
                } else {
                    ponMensaje(data, "alert-danger", "#mensajesEditaImagenes", "#modalEditaImagenes");
                }
                $("#agrega_imagen_progress").addClass("hidden");
            }
        });
    }
}

function limpiaModalAgregarArchivos() {
    "use strict";
    limpiaCampos("#new_producto_archivo", 1);
    $("#archivosActuales").html("");
    $("#mensajesEditaArchivos").html("");
    $("#newArchivo_idArchivo").val("");
    $("#busca_archivo").val("");
    $("#boton_enlaza").attr("disabled", "disabled");
}

function agregaArchivos() {
    "use strict";
    var error = false, formData;
    if (!camposVacios("#new_producto_archivo")) {
        formData = new FormData($("#form_edita_archivos")[0]);
        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            url: "ajax/archivo/agregarArchivos.php",
            data: formData,
            type: "post",
            beforeSend: function () {
                $("#agrega_archivo_progress").children("div").css("width", "0%");
                $("#agrega_archivo_progress").children("div").html("Agregando archivos... [0%]");
                $("#agrega_archivo_progress").removeClass("hidden");
            },
            success: function (data) {
                if (data === "") {
                    limpiaModalAgregarArchivos();
                    $("#modalEditaArchivos").modal("hide");
                    mensaje("Se han agregado los archivos correctamente", "alert-success");
                    actualizaListaProductos();
                } else {
                    ponMensaje(data, "alert-danger", "#mensajesEditaArchivos", "#modalEditaArchivos");
                }
                $("#agrega_archivo_progress").addClass("hidden");
            }
        });
    }
}

function finalizaPreparacionModal(data) {
    "use strict";
    if (data.linaje.length !== 0) {
        $("#editProdSubcat_full").addClass("in");
    } else {
        $(".editProdSubcategoria").eq(0).html("");
    }
    $(".editProdSubcats").last().val(data.idCategoria);
    $(".editProdSubcats").last().trigger("change");
    $("#editProdPrecio").val(data.precio);
    $("#editProdInventario").val(data.enInventario);
    $("#editProdInventario_max").val(data.maximo);
    $("#editProdInventario_min").val(data.minimo);
    $("#editProdDescripcion").val(data.descripcion);
    $("#editProdIVA").val(data.iva);
    $("#editProdDisponible").val(data.disponible);
}

function preparaSubcatModalEditar(data, i) {
    "use strict";
    if (data.linaje.length === i) {
        finalizaPreparacionModal(data);
        return;
    }
    $.post("ajax/categoria/getSubcategoriaSelect.php", "id=" + data.linaje[i], function (res) {
        $(".editProdSubcats:last").val(data.linaje[i]);
        $(".editProdSubcategoria:last").html("<select class='form-control editProdSubcats' name='editProdCategoria[]'>" +
                                             res + "</select><br><div class='editProdSubcategoria'></div>");
        preparaSubcatModalEditar(data, i + 1);
    });
}
           
function preparaModalEditarProducto(idProducto) {
    "use strict";
    ajaxToHtml("categoria/getCategoriasSelect.php", "#editProdCategoria", true, "");
    $.post("ajax/producto/getProductoById.json.php", "idProducto=" + idProducto, function (data) {
        $("#editIdProducto").val(data.idProducto);
        $("#editProdNombre").val(data.nombre);
        preparaSubcatModalEditar(data, 0);
    }, "json");
}

function finalizarModalInv() {
    "use strict";
    $.post("ajax/producto/actualizaInventario.php", "idP=" + $("#modalInvProd").val() + "&cnt=" + $("#modalInvProdCant").val(), function (data) {
        $("#modalEditaInventario").modal("hide");
        if (data === "") {
            mensaje("Se ha actualizado el inventario correctamente", "alert-success");
            actualizaListaProductos();
        } else {
            mensaje(data, "alert-danger");
        }
    });
}

function preparaModalEditarInventario(idProducto) {
    "use strict";
    $.post("ajax/producto/getProductoById.json.php", "idProducto=" + idProducto, function (data) {
        $("#tituloModalNombreProductoInventario").html(data.nombre);
        $("#modalInvProd").val(data.idProducto);
        $("#modalInvProdCant").val(data.enInventario);
        $("#modalInvProdMax").html(data.maximo);
        $("#modalInvProdMin").html(data.minimo);
    }, "json");
}

function preparaModalAgregarImagenes(idProducto) {
    "use strict";
    $.post("ajax/producto/getProductoById.json.php", "idProducto=" + idProducto, function (data) {
        $("#editImagenIdProducto").val(data.idProducto);
        $("#tituloModalNombreProducto").html(data.nombre);
        data.imagen.forEach(function (e, i) {
            var ojito = "glyphicon glyphicon-eye-open", clase = "";
            if (parseInt(e.activo, 10) === 0) {
                ojito = "glyphicon glyphicon-eye-close";
                clase = "bg-gris";
            }
            $("#imagenesActuales").append("<div class='col-sm-2 col-xs-3'>" +
                                          "<div class='img-thumbnail text-center " + clase + "'>" +
                                          "<img src='prod_img/less/" + e.url + "' class='img img-responsive'>" +
                                          "<p class='btn activadorImagen' data-activo='" + e.activo + "' data-idImg='" + e.idImagen + "'>" +
                                          "<span class='" + ojito + "'></span>" +
                                          "</p>" +
                                          "</div>" +
                                          "</div>");
        });
    }, "json");
}

function preparaModalAgregarArchivos(idProducto) {
    "use strict";
    $.post("ajax/producto/getProductoById.json.php", "idProducto=" + idProducto, function (data) {
        $("#editArchivoIdProducto").val(data.idProducto);
        $("#tituloModalArchivosNombreProducto").html(data.nombre);
        data.archivo.forEach(function (e, i) {
            $("#archivosActuales").append("<li class='collapse in' data-idArch='" + e.idArchivo + "'>" +
                                          "<p><span class='badge badgeclose'>&times;</span> " +
                                          "<a href='" + e.url + "' download>" + e.nombre + "</a></p>" +
                                          "</li>");
        });
    }, "json");
    if (auto_archivo === false) {
        auto_archivo = true;
        $('#busca_archivo').typeahead({
            hint: true,
            highlight: true
        }, {
            display: 'nombre',
            limit: Infinity,
            source: _auto_archivo
        });
        $('#busca_archivo').bind('typeahead:select', function (ev, suggestion) {
            $("#newArchivo_idArchivo").val(suggestion.idArchivo);
            $("#boton_enlaza").removeAttr("disabled");
        });
        $('#busca_archivo').bind('typeahead:open', function (ev, suggestion) {
            $("#newArchivo_idArchivo").val("");
            $("#boton_enlaza").attr("disabled", "disabled");
        });
    }
}

function preparaModalOutlet(idProducto, precio) {
    "use strict";
    ajaxToHtml("categoria/descuento/getDescuentoSelect.php", "#select_outlet_descuento", true, "");
    $("#editOutletPrecio").val(precio);
    $("#editOutletIdProducto").val(idProducto);
    $("#modalPrecioOutlet").val("$" + number_format(precio, 2));
    $("#mensajesEditaOutlet").html("");
    $("#new_producto_outlet").val("");
    $.post("ajax/producto/getProductoById.json.php", "idProducto=" + idProducto, function (data) {
        $("#tituloModalNombreProductoOutlet").html(data.nombre);
        if (data.precioOutlet !== null && parseInt(data.precioOutlet, 10) !== 0) {
            $("#new_producto_outlet").val("$" + number_format(data.precioOutlet, 2));
        }
    }, "json");
}

function preparaAgregarProducto() {
    "use strict";
    ajaxToHtml("categoria/getCategoriasSelect.php", "#new_producto_categoria", true, "");
}

function enlazaArchivo() {
    "use strict";
    $.post("ajax/archivo/enlazaArchivoProducto.php", "idProd=" + $("#editArchivoIdProducto").val() + "&idArch=" + $("#newArchivo_idArchivo").val(), function (data) {
        $("#archivosActuales").append("<li class='collapse in' data-idArch='" + $("#newArchivo_idArchivo").val() + "'>" +
                                      "<p><span class='badge badgeclose'>&times;</span> " +
                                      "<a href='" + $("#newArchivo_url").val() + "' download>" + $("#busca_archivo").val() + "</a></p>" +
                                      "</li>");
        $("#newArchivo_idArchivo").val("");
        $("#busca_archivo").val("");
        $("#boton_enlaza").attr("disabled", "disabled");
        actualizaListaProductos();
    });
}

function eliminaProducto(idProducto) {
    "use strict";
    $.post("ajax/producto/eliminaProducto.php", "idProducto=" + idProducto, function (data) {
        actualizaListaProductos();
        mensaje("El producto ha sido eliminado correctamente", "alert-success");
    });
}

function quitaEnlace(archivo) {
    "use strict";
    ajaxFast("archivo/quitaEnlace.php", true, "idProd=" + $("#editArchivoIdProducto").val() + "&idArch=" + archivo);
}

function modificaOutlet() {
    "use strict";
    $.post("ajax/producto/modificaPrecioOutlet.php", "idProducto=" + $("#editOutletIdProducto").val() + "&precio=" + $("#new_producto_outlet").val(), function (data) {
        ponMensaje("Se ha actualizado el precio de outlet", "alert-success", "#mensajesEditaOutlet", "#modalEditaOutlet");
    });
}

$(function () {
    "use strict";
    $("#tabs a[href='#productos']").on('shown.bs.tab', function(e){
        if($("#productos").html() == ""){
            $.get("tabs/productos.html", {"_": $.now()}, function(data){
                $("#productos").html(data);
                actualizaListaProductos();
                revisaPermisos("#subtab_productos a");
                $("#botonBorrarTodo").click(function(){
                    if(confirm("¿Estas seguro de querer borrar todos los productos? Esta acción no tiene retorno")){
                        $.post("ajax/producto/borrarTodo.json.php", function(res){
                            toastr.success("Datos borrados");
                            actualizaListaProductos();
                        });
                    }
                });
            });
        }else{
            actualizaListaProductos();
        }
    });
    $(document).on('shown.bs.tab', "#subtab_productos a[href='#productos_lista']", function (e) {
        actualizaListaProductos();
    });
    $(document).on('shown.bs.tab', "#subtab_productos a[href='#productos_agregar']", function (e) {
        ajaxToHtml("categoria/getCategoriasSelect.php", "#new_producto_categoria", true, "");
        preparaAgregarProducto();
    });
    $(document).on("change", ".new_subcats", function () {
        var who = $(".new_subcats").index(this);
        if ($(this).val() === "" || $(this).val() === undefined) {
            $(this).val("");
            $(".new_producto_subcat").eq(who).html("");
            if (who === 0) {
                $("#new_producto_subcat_full").collapse("hide");
            }
        } else {
            $.post("ajax/categoria/getSubcategoriaSelect.php", "id=" + $(this).val(), function (data) {
                if (data !== "") {
                    $(".new_producto_subcat").eq(who).html("<select class='form-control new_subcats' name='new_producto_subcategoria[]'></select><br><div class='new_producto_subcat'></div>");
                    $(".new_subcats:last").html(data);
                    if (who === 0) {
                        $("#new_producto_subcat_full").collapse("show");
                    }
                } else {
                    $(".new_producto_subcat").eq(who).html("");
                    if (who === 0) {
                        $("#new_producto_subcat_full").collapse("hide");
                    }
                }
            });
        }
    });
    $(document).on("change", ".editProdSubcats", function () {
        var who = $(".editProdSubcats").index(this);
        if ($(this).val() === "" || $(this).val() === undefined) {
            $(this).val("");
            $(".editProdSubcategoria").eq(who).html("");
            if (who === 0) {
                $("#editProdSubcat_full").removeClass("in");
            }
        } else {
            $.post("ajax/categoria/getSubcategoriaSelect.php", "id=" + $(this).val(), function (data) {
                if (data !== "") {
                    $(".editProdSubcategoria").eq(who).html("<select class='form-control editProdSubcats' name='editProdCategoria[]'></select><br><div class='editProdSubcategoria'></div>");
                    $(".editProdSubcats:last").html(data);
                    if (who === 0) {
                        $("#editProdSubcat_full").addClass("in");
                    }
                } else {
                    $(".editProdSubcategoria").eq(who).html("");
                    if (who === 0) {
                        $("#editProdSubcat_full").removeClass("in");
                    }
                }
            });
        }
    });
    $(document).on("click", ".editaProducto", function () {
        limpiaModalEditarProducto();
        preparaModalEditarProducto($(this).parent().attr("data-idProducto"));
        $("#modalEditaProducto").modal("show");
    });
    
    $(document).on("click", ".editaInventario", function () {
        preparaModalEditarInventario($(this).parent().attr("data-idProducto"));
        $("#modalEditaInventario").modal("show");
    });
    
    $(document).on("click", ".agregaImagenProducto", function () {
        limpiaModalAgregarImagenes();
        preparaModalAgregarImagenes($(this).parent().attr("data-idProducto"));
        $("#modalEditaImagenes").modal("show");
    });
    $(document).on("click", ".editaOutlet", function () {
        preparaModalOutlet($(this).parent().attr("data-idProducto"), $(this).parent().attr("data-precio"));
        $("#modalEditaOutlet").modal("show");
    });
    $(document).on("change", "#select_outlet_descuento", function () {
        if ($(this).val() !== "") {
            var numero = $("#editOutletPrecio").val() * ($("#select_outlet_descuento option:selected").attr("data-valor") / 100);
            $("#new_producto_outlet").val("$ " + number_format(numero, 2));
        }
    });
    $(document).on("change", "#new_producto_outlet", function () {
        $("#select_outlet_descuento").val("");
    });
    $(document).on("hide.bs.modal", "#modalEditaOutlet", function () {
        actualizaListaProductos();
    });
    $(document).on("click", ".agregaArchivoProducto", function () {
        limpiaModalAgregarArchivos();
        preparaModalAgregarArchivos($(this).parent().attr("data-idProducto"));
        $("#modalEditaArchivos").modal("show");
    });
    $(document).on("click", ".eliminaProducto", function () {
        confirma("Eliminar producto", "Se eliminará el producto y no aparecerá más en la lista de productos.<br>¿Deseas continuar?",
                 "eliminaProducto(" + $(this).parent().attr("data-idProducto") + ");");
    });
    
    $(document).on("click", ".activadorImagen", function () {
        if ($(this).attr("data-activo") == 1) {
            $(this).children("span").removeClass("glyphicon-eye-open");
            $(this).children("span").addClass("glyphicon-eye-close");
            $(this).parent().addClass("bg-gris");
            $(this).attr("data-activo", 0);
        } else {
            $(this).children("span").removeClass("glyphicon-eye-close");
            $(this).children("span").addClass("glyphicon-eye-open");
            $(this).parent().removeClass("bg-gris");
            $(this).attr("data-activo", 1);
        }
        $.post("ajax/producto/activadorImagen.php", "id=" + $(this).attr("data-idImg") + "&estado=" + $(this).attr("data-activo"), function(){
            actualizaListaProductos();    
        });
    });
    $(document).on("click", ".badgeclose", function () {
        $(this).parent().parent().collapse("hide");
        quitaEnlace($(this).parent().parent().attr("data-idArch"));
        actualizaListaProductos();
    });
});