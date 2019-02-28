var tablaCarritoA = "";
var tablaCarritoC = "";
var tablaCarritoContenido = "";
var identifica_carrito = -1;
var auto_cliente_carrito = "";

function actualizaListaCarritos(){
    if($("#carritos_tabla_listaA").length){
        if(tablaCarritoA == ""){
            tablaCarritoA = $("#carritos_tabla_listaA").DataTable({
                serverSide: true,
                ajax: "ajax/carrito/getCarritos.json.php?c=0",
                pageLength: 5,
                lengthMenu: [5, 10, 15, 20, 50],
                language: {
                    url: "locales/datatable.es.json"
                },
                fnDrawCallback: function(){
                    $("#carritos_tabla_listaA ._tool").tooltip();
                },
                ordering: false,
                searching: false,
                columns: [
                    {width: "5%"},
                    null,
                    null,
                    {width: "20%"}
                ]
            });
        }else{
            tablaCarritoA.ajax.reload();
        }
    }
}
function actualizaListaCarritosCerrados(){
    if($("#carritos_tabla_listaC").length){
        if(tablaCarritoC == ""){
            tablaCarritoC = $("#carritos_tabla_listaC").DataTable({
                serverSide: true,
                ajax: "ajax/carrito/getCarritos.json.php?c=1",
                pageLength: 5,
                lengthMenu: [5, 10, 15, 20, 50],
                language: {
                    url: "locales/datatable.es.json"
                },
                fnDrawCallback: function(){
                    $("#carritos_tabla_listaC ._tool").tooltip();
                },
                ordering: false,
                searching: false,
                columns: [
                    {width: "5%"},
                    null,
                    null,
                    null,
                    null,
                    {width: "20%"}
                ]
            });
        }else{
            tablaCarritoC.ajax.reload();
        }
    }
}

function actualizaListaContenido(){
    if($("#carritoContenido_tabla").length){
        if(tablaCarritoContenido == ""){
            tablaCarritoContenido = $("#carritoContenido_tabla").DataTable({
                serverSide: true,
                ajax: {
                    url: "ajax/carrito/getCarritoById.json.php?idCarrito="+identifica_carrito,
                    dataSrc: function (json){
                        console.warn(json);
                        if(json.entregado == 0){
                            $("#requiereFacturaCB22").removeAttr("disabled");
                            $("#datosFacturacion22 input[name='razonSocial']").removeAttr("disabled");
                            $("#datosFacturacion22 input[name='correoFactura']").removeAttr("disabled");
                            $("#modal_carrito_descuento").removeAttr("disabled");
                            $("#estado_carrito").removeClass("hidden");
                            if(json.cerrado == 0){
                                $("#modal_carrito_abierto").parent().addClass("active");
                                $("#modal_carrito_cerrado").parent().removeClass("active");
                                //$("#autocompletar_modal_carrito").slideDown("fast");
                            }else{
                                $("#modal_carrito_abierto").parent().removeClass("active");
                                $("#modal_carrito_cerrado").parent().addClass("active");
                                //$("#autocompletar_modal_carrito").slideUp("fast");
                            }
                        }else{
                            $("#modal_carrito_descuento").attr("disabled", true);
                            $("#requiereFacturaCB22").attr("disabled", true);
                            $("#datosFacturacion22 input[name='razonSocial']").attr("disabled", true);
                            $("#datosFacturacion22 input[name='correoFactura']").attr("disabled", true);
                            //$("#autocompletar_modal_carrito").slideUp("fast");
                            $("#estado_carrito").addClass("hidden");
                        }
                        $("#modal_carrito_cliente").html(json.nombre);
                        $("#modal_carrito_fecha").html(json.fechaCreacion);
                        $("#modal_carrito_subtotal").html(json.subtotal);
                        if(json.idDescuento == "" || json.idDescuento == "0")
                            $("#modal_carrito_descuento").val("0");
                        else
                            $("#modal_carrito_descuento").val(json.idDescuento);
                        $("#modal_carrito_total").html(json.total);
                        $("#modal_carrito_total").attr("totalNum", json.totalNum);
                        $("#montoACubrirCA").html(number_format(json.totalNum,2));
                        if(json.factura == 1){
                            $("#requiereFacturaCB22").prop("checked", true);
                            $("#datosFacturacion22").show();
                            $("#datosFacturacion22 input[name='razonSocial']").val(json.carritoRS);
                            $("#datosFacturacion22 input[name='correoFactura']").val(json.carritoE);
                        }else{
                            $("#requiereFacturaCB22").prop("checked", false);
                            $("#datosFacturacion22").hide();
                            $("#datosFacturacion22 input[name='razonSocial']").val(json.razonSocial);
                            $("#datosFacturacion22 input[name='correoFactura']").val("");
                        }
                        if(json.saldo==0){
                            $("#estado_carrito_credito").parent().parent().parent().hide();
                            $("#estado_carrito_credito").val(0);
                        }else{
                            $("#estado_carrito_credito").parent().parent().parent().show();
                            $("#estado_carrito_credito").val(json.saldo);
                        }
                        $(".montoACubrirCA_class").trigger("change");
                        return json.data;
                    }
                },
                pageLength: 5,
                lengthMenu: [5, 10, 15, 20, 50],
                language: {
                    url: "locales/datatable.es.json"
                },
                fnDrawCallback: function(){
                    $("#carritoContenido_tabla ._tool").tooltip();
                },
                ordering: false,
                searching: false,
                paging: false,
                info: false
            });
            $('#busca_producto').typeahead({
                hint: true,
                highlight: true
            },{
            display: 'nombre',
            limit: Infinity,
            source: _auto_producto
            });
            $('#busca_producto').bind('typeahead:select', function(ev, suggestion) {
                $("#modal_carrito_producto").val(suggestion.idProducto);
                $("#modal_carrito_agregar").removeAttr("disabled");
            });
            $('#busca_producto').bind('typeahead:open', function(ev, suggestion) {
                $("#modal_carrito_producto").val("");
                $("#modal_carrito_agregar").attr("disabled", "disabled");
                limpiaCampos("#modal_carrito_cantidad", 0);
            });
        }else{
            tablaCarritoContenido.ajax.url("ajax/carrito/getCarritoById.json.php?idCarrito="+identifica_carrito).load();
        }
    }
}

function agregaProductoCarrito(){
    var datos = "idCarr="+identifica_carrito;
    datos += "&idProd="+$("#modal_carrito_producto").val();
    if(!camposVacios("#modal_carrito_cantidad")){
        datos += "&cant="+$("#modal_carrito_cantidad").val();
        $.post("ajax/carrito/agregaCarrito.php", datos, function(data){
            if(data == ""){
                toastr.success("Agregado correctamente");
                limpiaCampos("#modal_carrito_cantidad,#busca_producto", 1);
                actualizaListaContenido();
            }else{
                toastr.error(data, "Error al agregar producto");
            }
        });
    }
}

function editarListaArticulos(cantidad, idContenido){
    var datos = "id="+idContenido;
    if(!validaVacio(cantidad)){
        datos += "&cantidad="+cantidad.val();
        console.log($.post("ajax/carrito/actualizaCarrito.php", datos, function(data){
            if(data == ""){
                actualizaListaContenido();
                toastr.success("Editado correctamente");
            }
            else
                toastr.error(data, "Error al editar");
        }));
    }
}

function bloqueaEdicionListaCarrito(e){
    e.attr("disabled", "disabled");
    limpiaFeedback(e);
    invisibiliza(e, 7, "1,2,3,4");
}

function eliminaContenidoCarrito(id){
    $.post("ajax/carrito/eliminaProductoCarrito.php", "id="+id, function(res){
        if(res !== ""){
            toastr.error(res, "Error al eliminar");
        } else {
            toastr.success("El producto ha sido eliminado del carrito correctamente");
            actualizaListaContenido();
        }
    });
}

function agregaProductoNuevoCarrito(){
    var precio = parseFloat($("#nuevo_carrito_precio").val());
    var cantidad = parseInt($("#nuevo_carrito_cantidad").val());
    var total = parseFloat($("#total_nuevo_carrito").html());
    if(!camposVacios("#nuevo_carrito_cantidad") && cantidad > 0){
        total += cantidad*precio;
        total = parseInt(total*100);
        total /= 100;
        $("#total_nuevo_carrito").html(total);
        if($("#nuevo_carrito_lista").html() == ""){
            $("#nuevo_carrito_lista").append("<tr>"+
                                             "<th class='col-sm-2 text-center'>Quitar</th>"+
                                             "<th class='col-sm-4 text-center'>Producto</th>"+
                                             "<th class='col-sm-2 text-center'>Cantidad</th>"+
                                             "<th class='col-sm-2 text-center'>Precio unitario</th>"+
                                             "<th class='col-sm-2 text-center'>Subtotal</th>"+
                                             "</tr>");
        }
        $("#nuevo_carrito_lista").append("<tr subtot='"+(cantidad*precio)+"'>"+
                                         "<td><span class='badge quitaListaNewCar' role='button'>&times;</span></td>"+
                                         "<td>"+$("#busca_producto_carrito").val() + "</td>"+
                                         "<td>"+cantidad+"</td>"+
                                         "<td>$"+number_format(precio, 2)+"</td>"+
                                         "<td>$"+number_format(cantidad*precio, 2)+"</td>"+
                                         "</tr>");
        $("#nuevo_carrito_lista_real").append("<li>"+
                                              "&prod[]="+$("#nuevo_carrito_producto").val()+
                                              "&cant[]="+cantidad+
                                              "</li>");
    }
}

function creaCarrito(){
    if($("#carrito_auto_cliente").val() != ""){
        var datos = "cliente="+$("#carrito_auto_cliente").val();
        datos += "&cerrado=";
        if($("#nuevo_carrito_abierto").parent().hasClass("active")){
            datos += "0";
        }else{
            datos += "1";
        }
        $("#nuevo_carrito_lista_real li").each(function(i, e){
            datos += $(e).html().replace(/&amp;/g, '&');
        });
        $.post("ajax/carrito/creaCarrito.php", datos, function(data){
            if(data == ""){
                mensaje("Se ha creado el carrito", "alert-success");
                $("#nuevo_carrito_lista_real").html("");
                limpiaCampos("#carrito_busca_cliente,#carrito_auto_cliente,#nuevo_carrito_cantidad,"+
                             "#nuevo_carrito_producto,#busca_producto_carrito,#nuevo_carrito_precio", 1);
                $("#nuevo_carrito_lista").html("");
                $("#nuevo_carrito_agregar").attr("disabled", "disabled");
                $("#total_nuevo_carrito").html(0);
            }else{
                mensaje(data, "alert-danger");
            }
        });
    }else{
        mensaje("Debes seleccionar un cliente para crear un carrito", "alert-danger");
    }
}

function borraCarrito(idCarrito){
    $.post("ajax/carrito/borraCarrito.php", "id="+idCarrito, function(){
        mensaje("Se ha eliminado el carrito", "alert-success");
        actualizaListaCarritos();
        actualizaListaCarritosCerrados();
        actualizaListaCarritosCerradosPV();

    });
}

function finalizaCarrito(idCarrito){
    $.post("ajax/carrito/finalizaCarrito.php", {
        idCarrito: idCarrito
    }, function(){
        actualizaListaCarritosCerrados();    
    });
}

$(function(){
    $("#tabs a[href='#carritos']").on('shown.bs.tab', function(e){
        if($("#carritos").html() == ""){
            $.get("tabs/carritos.html", {"_": $.now()}, function(data){
                $("#carritos").html(data);
                actualizaListaCarritos();
                revisaPermisos("#subtab_carritos a");
            });
        }else{
            actualizaListaCarritos();
        }                  
    });
    $(document).on('shown.bs.tab', "#subtab_carritos a[href='#carritos_listaA']", function(){
        actualizaListaCarritos();
    });
    $(document).on('shown.bs.tab', "#subtab_carritos a[href='#carritos_listaC']", function(){
        actualizaListaCarritosCerrados();
    });
    $(document).on('shown.bs.tab', "#subtab_carritos a[href='#carritos_crear']", function(){
        if(!auto_cliente_carrito){
            auto_cliente_carrito = true;
            $('#busca_producto_carrito').typeahead({
                hint: true,
                highlight: true
            }, {
                display: 'nombre',
                limit: Infinity,
                source: _auto_producto
            });
            $('#carrito_busca_cliente').typeahead({
                hint: true,
                highlight: true
            }, {
                display: 'nombre',
                limit: Infinity,
                source: _auto_cliente
            });
            $('#carrito_busca_cliente').bind('typeahead:select', function(ev, suggestion) {
                $("#carrito_auto_cliente").val(suggestion.idCliente);
            });
            $('#carrito_busca_cliente').bind('typeahead:open', function(ev, suggestion) {
                $("#carrito_auto_cliente").val("");
            });
            $('#busca_producto_carrito').bind('typeahead:select', function(ev, suggestion) {
                $("#nuevo_carrito_producto").val(suggestion.idProducto);
                $("#nuevo_carrito_precio").val(suggestion.precio);
                $("#nuevo_carrito_agregar").removeAttr("disabled");
            });
            $('#busca_producto_carrito').bind('typeahead:open', function(ev, suggestion) {
                $("#nuevo_carrito_producto").val("");
                $("#nuevo_carrito_precio").val("");
                $("#nuevo_carrito_agregar").attr("disabled", "disabled");
            });
        }
    });
    $("#requiereFacturaCB22").change(function(){
        if($("#requiereFacturaCB22").prop("checked")){
            $("#datosFacturacion22").slideDown();
            $("#modal_carrito_total").attr("totalNum", number_format(parseFloat($("#modal_carrito_total").attr("totalNum"))*1.16, 2, ".", ""));
            $("#modal_carrito_total").html("$"+number_format($("#modal_carrito_total").attr("totalNum"), 2));
            $(".montoACubrirCA_class").trigger("change");
        }else{
            $("#datosFacturacion22").slideUp();
            $("#modal_carrito_total").attr("totalNum", number_format(parseFloat($("#modal_carrito_total").attr("totalNum"))/1.16, 2, ".", ""));
            $("#modal_carrito_total").html("$"+number_format($("#modal_carrito_total").attr("totalNum"), 2));
            $(".montoACubrirCA_class").trigger("change");
        }
        $("#total-pagar").html(number_format(totalVenta*pv_descuento, 2));
    });
    $(document).on("click", ".ver_lista_modal_carrito", function(){
        identifica_carrito = $(this).parent().attr("data-idCarrito");
        actualizaListaContenido();
        $("#estado_carrito_efectivo").val(0);
        $("#estado_carrito_tarjeta").val(0);
        $("#estado_carrito_cheque").val(0);
        $('#modalProductosCarrito').modal('show');
    });
    $(document).on("click", ".borrarCarrito", function(){
        confirma("Eliminar carrito", "Al eliminar el carrito, se perderá el registro y los productos vendidos regresarán a inventario. ¿Desea continuar?",
                 "borraCarrito("+$(this).parent().attr("data-idCarrito")+");");
    });
    // deja todo listo para editar
    $(document).on("click", ".editaListaCarrito", function(){
        visibiliza($(this), "5", ".actualizaListaCarrito,.cancelaListaCarrito,.borraListaCarrito");
    });
    // edita
    $(document).on("click", ".actualizaListaCarrito", function(){
        var cant = $(this).parent().parent().parent().parent().children("td:nth-child(5)").children("div").children("input");
        editarListaArticulos(cant, $(this).parent().attr("data-idCarritoContenido"));
    });
    // cancela
    $(document).on("click", ".cancelaListaCarrito", function(){
        var c = $(this).parent().parent().parent().parent().children("td:nth-child(5)").children("div").children("input");
        c.val(c.parent().attr("data-original"));
        bloqueaEdicionListaCarrito(c);
    });
    $(document).on("click", ".borraListaCarrito", function(){
        eliminaContenidoCarrito($(this).parent().attr("data-idCarritoContenido"));
    });
    $(document).on("hide.bs.modal", "#modalProductosCarrito", function(){
        actualizaListaCarritos();
        actualizaListaCarritosCerrados();
    });
    $(document).on("click", "#butt label", function(){
        var who = $("#butt label").index(this);
        if($("#montoACubrirCA").html() == "0.00"){
            console.log($.post("ajax/barcode/finalizaPuntoVenta.json.php", {
                idCarrito: identifica_carrito,
                tarjeta: $("#estado_carrito_tarjeta").val(),
                cheque: $("#estado_carrito_cheque").val(),
                efectivo: $("#estado_carrito_efectivo").val(),
                credito: $("#estado_carrito_credito").val(),
                requiereFactura: ($("#requiereFacturaCB22").prop("checked")?1:0),
                razonSocial: $("#datosFacturacion22 input[name='razonSocial']").val(),
                correoFactura:$("#datosFacturacion22 input[name='correoFactura']").val()
            }, function(res){
                if(!res.error){
                    //$("#autocompletar_modal_carrito").slideUp("fast");
                    $("#modalProductosCarrito").modal("hide");
                    actualizaListaCarritosAbiertosPV();
                    toastr.success("Carrito cerrado correctamente");
                }else{
                    toastr.error(res.msg, "Error al cerrar carrito");
                    setTimeout(function(){
                        $("#modal_carrito_cerrado").parent().removeClass("active");
                        $("#modal_carrito_abierto").parent().addClass("active");       
                    }, 500);
                }
            }));
        }else{
            toastr.error("Falta "+$("#montoACubrirCA").html()+" por cubrir");
            setTimeout(function(){
                $("#modal_carrito_cerrado").parent().removeClass("active");
                $("#modal_carrito_abierto").parent().addClass("active");       
            }, 500);
        }
        /*$.post("ajax/carrito/cierraCarrito.php", "idCarr="+identifica_carrito+"&decision="+who, function(){///////////////////////dfakljgaidjfhajhsgfashjdfsd
            if(who == 0){
                $("#autocompletar_modal_carrito").slideDown("fast");
                actualizaListaContenido();
            }else
                $("#autocompletar_modal_carrito").slideUp("fast");
        });*/
    });
    $(document).on("click", ".quitaListaNewCar", function(){
        var who = $("#nuevo_carrito_lista tr").index($(this).parent().parent());
        var total = parseFloat($("#total_nuevo_carrito").html());
        total -= parseFloat($(this).parent().parent().attr("subtot"));
        total = parseInt(total*100);
        total /= 100;
        $("#total_nuevo_carrito").html(total);
        $(this).parent().parent().remove();
        $("#nuevo_carrito_lista_real li").eq(who-1).remove();
        if($("#nuevo_carrito_lista_real").html() == ""){
            $("#nuevo_carrito_lista").html("");
            $("#total_nuevo_carrito").html(0);
        }
    });
    $(document).on("click", "#fin_nuevo_carrito", function(){
        var total = parseFloat($("#total_nuevo_carrito").html());
        if(!validaVacio("#carrito_busca_cliente")){
            if(total == 0){
                confirma("Crear carrito (¿vacío?)", "El total de esta compra es 0.<br>¿Deseas continuar?",
                     "creaCarrito();");
            }else{
                creaCarrito();
            }
        }
    });
    $(document).on("click", ".finalizaCarrito", function(){
        confirma("Finalizar carrito",
                 "Esta acción bloquea cualquier modificación sobre este carrito y lo toma como una venta exitosa.<br>"+
                 "Esta acción no tiene vuelta atrás, ¿está seguro de continuar?",
                 "finalizaCarrito("+$(this).parent().attr("data-idCarrito")+")");
    });
    $(document).on("change", ".montoACubrirCA_class", function(){
        $("#montoACubrirCA").html(number_format(
            parseFloat($("#modal_carrito_total").attr("totalNum"))-
            (parseFloat($("#estado_carrito_tarjeta").val())+parseFloat($("#estado_carrito_cheque").val())+
                parseFloat($("#estado_carrito_efectivo").val())+parseFloat($("#estado_carrito_credito").val())), 2));
    });
    $("#modal_carrito_descuento").change(function(){
        console.log({id: identifica_carrito, idD: $(this).val()});
        $.post("ajax/carrito/asignaDescuento.php", {id: identifica_carrito, idD: $(this).val()}, function(data){
            if(data == ""){
                toastr.success("Descuento asignado");
                actualizaListaContenido();
            }else{
                toastr.error(data, "Error al asignar descuento");
            }
        });
    });
    $("#simulaBarcode2").submit(function(e){
        e.preventDefault();
        ejecutaBarcode($("#barcodemanual2").val());
        $("#barcodemanual2").val("");
    });
    ajaxToHtml("categoria/descuento/getDescuentoSelect.php", "#modal_carrito_descuento", true, "");
});