var nuevaVenta, cuantosTablaNuevaVenta = 0, totalVenta = 0, tablaSalidaInv="", tablaCarritoCPV="", tablaCarritoDPV="", tablaCarritoAbiertoPV="", tablaCarritoContenido_d="", pv_descuento=1, ultimo_dev_id=0, saldoFavor=0;
function vaciarCarritoPV(){
    nuevaVenta.clear().draw();
    totalVenta = 0;
    cuantosTablaNuevaVenta = 0;
    $("#total-pagar").html(number_format(totalVenta, 2));
    $("#total-productos").html("0");
}
var elementoGlobal = "";
function imprimeTicket(id, cual){
    if(elementoGlobal != "#cuenta"){
        $.post("ajax/carrito/dameTicket.php", {idCarrito: id}, function(html){
            var printWindow = window.open('', '', ',width=300');
            printWindow.document.write(html);
            $(printWindow.document).ready(function(){
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 300);
            });
        });
    }else{
        $.post("ajax/carrito/dameTicket.php", {idCarrito: id, cual: 1}, function(html){
            var printWindow = window.open('', '', ',width=800');
            printWindow.document.write(html);
            $(printWindow.document).ready(function(){
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 300);
            });
        });
    }
}
function verTicket(id){
    $.post("ajax/carrito/dameTicket.php", {idCarrito: id}, function(html){
        $("#contenido_modal_ticket").html(html);
        $("#modal_ticket").modal("show");
    });
}
function actualizaListaContenido_d(identifica_carrito_d){
    ultimo_dev_id = identifica_carrito_d;
    if($("#carritoContenido_tabla_d").length){
        if(tablaCarritoContenido_d == ""){
            tablaCarritoContenido_d = $("#carritoContenido_tabla_d").DataTable({
                serverSide: true,
                ajax: {
                    url: "ajax/carrito/getCarritoById.json.php?d=1&idCarrito="+identifica_carrito_d,
                    dataSrc: function (json){
                        $("#modal_carrito_cliente_d").html(json.nombre);
                        $("#modal_carrito_cliente2_d").html(json.nombre);
                        $("#modal_carrito_saldo_d").html("$"+number_format(json.saldo, 2));
                        if(json.efectivo!=0){
                            $("#modal_carrito_efectivo_d").html("$"+number_format(json.efectivo, 2));
                            $("#modal_carrito_efectivo_d").parent().parent().show();
                        }else{
                            $("#modal_carrito_efectivo_d").parent().parent().hide();
                            $("#modal_carrito_efectivo_d").html("$"+number_format(0, 2));
                        }
                        if(json.tarjeta!=0){
                            $("#modal_carrito_tarjeta_d").html("$"+number_format(json.tarjeta, 2));
                            $("#modal_carrito_tarjeta_d").parent().parent().show();
                        }else{
                            $("#modal_carrito_tarjeta_d").parent().parent().hide();
                            $("#modal_carrito_tarjeta_d").html("$"+number_format(0, 2));
                        }
                        if(json.cheque!=0){
                            $("#modal_carrito_cheque_d").html("$"+number_format(json.cheque, 2));
                            $("#modal_carrito_cheque_d").parent().parent().show();
                        }else{
                            $("#modal_carrito_cheque_d").parent().parent().hide();
                            $("#modal_carrito_cheque_d").html("$"+number_format(0, 2));
                        }
                        if(json.credito!=0){
                            $("#modal_carrito_credito_d").html("$"+number_format(json.credito, 2));
                            $("#modal_carrito_credito_d").parent().parent().show();
                        }else{
                            $("#modal_carrito_credito_d").parent().parent().hide();
                            $("#modal_carrito_credito_d").html("$"+number_format(0, 2));
                        }
                        $("#modal_carrito_fecha_d").html(json.fechaCreacion);
                        $("#modal_carrito_subtotal_d").html(json.subtotal + (json.factura=="1"?" +IVA":""));
                        if(json.descuento == "0")
                            $("#modal_carrito_descuento_d").html("");
                        else
                            $("#modal_carrito_descuento_d").html(json.descuento+"%");
                        $("#modal_carrito_total_d").html(json.total);
                        
                        return json.data;
                    }
                },
                pageLength: 5,
                lengthMenu: [5, 10, 15, 20, 50],
                language: {
                    url: "locales/datatable.es.json"
                },
                fnDrawCallback: function(){
                    $("#carritoContenido_tabla_d ._tool").tooltip();
                },
                ordering: false,
                searching: false,
                paging: false,
                info: false
            });
        }else{
            tablaCarritoContenido_d.ajax.url("ajax/carrito/getCarritoById.json.php?d=1&idCarrito="+identifica_carrito_d).load();
        }
    }
}
function devolucion(id){
    $("#modalProductosCarrito_devolucion").modal("show");
    actualizaListaContenido_d(id);
}
function actualizaListaCarritosAbiertosPV(){
    if($("#carritos_tabla_listaCarrAbi").length){
        if(tablaCarritoAbiertoPV == ""){
            tablaCarritoAbiertoPV = $("#carritos_tabla_listaCarrAbi").DataTable({
                serverSide: true,
                ajax: {
                    url: "ajax/carrito/getCarritos.json.php",
                    data: {
                        c: "0",
                        full: "1",
                        cliente: function(){ return $("#cliente_ca_vd").val(); },
                        fechaIni: function(){ return $("#fechaIni_ca_vd").val(); },
                        fechaFin: function(){ return $("#fechaFin_ca_vd").val(); }
                    }
                },
                paging: false,
                language: {
                    url: "locales/datatable.es.json"
                },
                dom: 'B<"clear">lfrtip',
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
                fnDrawCallback: function(full){
                    $("#carritos_tabla_listaCarrAbi ._tool").tooltip();
                },
                ordering: false,
                searching: false,
                columns: [
                    {width: "5%", data: "idCarrito"},
                    {data: "nombre"},
                    {data: "fechaCreacion", render: function(data, type, full){ return moment(data).format("LL"); }},
                    {data: "productos"},
                    {data: "vendedor"},
                    {data: "factura", render: function(data, type, full){
                        if(data == "1")
                            return "<small class='_tool' title='RFC'><strong>RFC:</strong> "+full.carritoRS+"</small><br>"+
                                   "<small class='_tool' title='Teléfono'><span class='glyphicon glyphicon-earphone'></span> "+full.carritoE+"</small>";
                        return "No";
                    }},
                    {data: "efectivo", visible: false},
                    {data: "tarjeta", visible: false},
                    {data: "cheque", visible: false},
                    {data: "cuenta", visible: false},
                    {data:"totaltotal", render: function(data){ return "$"+number_format(data, 2); }},
                    {width: "25%", data: "idCarrito", render: function(data, type, full){
                        var msg="<div class='text-center'><div class='btn-group' role='group' data-idCarrito='"+data+"'>"+
                                   "<button type='button' class='btn btn-default _tool ver_lista_modal_carrito' "+
                                        "title='Ver productos en el carrito' data-toggle='tooltip'}'>"+
                                        "<span class='glyphicon glyphicon-shopping-cart'></span>"+
                                   "</button>";
                        if(full.idCliente==0){
                            msg += "<button type='button' class='btn btn-default _tool' title='Asignar cliente' "+
                                        "data-toggle='tooltip' onclick='asignarCliente("+data+")'>"+
                                        "<span class='glyphicon glyphicon-user'></span>"+
                                   "</button>";
                        }
                        msg += "<button type='button' class='btn btn-default _tool borrarCarrito' title='Borrar carrito' data-toggle='tooltip'>"+
                                    "<span class='glyphicon glyphicon-trash'></span>"+
                                "</button>";
                        msg += "</div></div>";
                        return msg;
                }}
                ]
            });
        }else{
            tablaCarritoAbiertoPV.ajax.reload();
        }
    }
}
function actualizaListaCarritosCerradosDPV(){
    if($("#carritos_tabla_listaDPV").length){
        if(tablaCarritoDPV == ""){
            tablaCarritoDPV = $("#carritos_tabla_listaDPV").DataTable({
                serverSide: true,
                ajax: {
                    url: "ajax/carrito/getCarritos.json.php",
                    data: {
                        c: "1",
                        full: "1",
                        cliente: function(){ return $("#cliente_vd").val(); },
                        fechaIni: function(){ return $("#fechaIni_vd").val(); },
                        fechaFin: function(){ return $("#fechaFin_vd").val(); }
                    }
                },
                paging: false,
                language: {
                    url: "locales/datatable.es.json"
                },
                dom: 'B<"clear">lfrtip',
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
                fnDrawCallback: function(full){
                    $("#carritos_tabla_listaDPV ._tool").tooltip();
                },
                ordering: false,
                searching: false,
                columns: [
                    {width: "5%", data: "idCarrito"},
                    {data: "nombre"},
                    {data: "fechaEntrega", render: function(data, type, full){ return moment(data).format("LL"); }},
                    {data: "productos"},
                    {data: "vendedor"},
                    {data: "factura", render: function(data, type, full){
                        if(data == "1")
                            return "<small class='_tool' title='RFC'><strong>RFC:</strong> "+full.carritoRS+"</small><br>"+
                                   "<small class='_tool' title='Teléfono'><span class='glyphicon glyphicon-earphone'></span> "+full.carritoE+"</small>";
                        return "No";
                    }},
                    {data: "efectivo", visible: false},
                    {data: "tarjeta", visible: false},
                    {data: "cheque", visible: false},
                    {data: "cuenta", visible: false},
                    {data:"totaltotal", render: function(data){ return "$"+number_format(data, 2); }},
                    {width: "25%", data: "idCarrito", render: function(data, type, full){
                        var msg="<div class='text-center'><div class='btn-group' role='group' data-idCarrito='"+data+"'>"+
                                   "<button type='button' class='btn btn-default _tool ver_lista_modal_carrito' "+
                                        "title='Ver productos en el carrito' data-toggle='tooltip'}'>"+
                                        "<span class='glyphicon glyphicon-shopping-cart'></span>"+
                                   "</button>";
                        if(full.idCliente!=0){
                            msg += "<button type='button' class='btn btn-default _tool' title='Devolución' "+
                                        "data-toggle='tooltip' onclick='devolucion("+data+")'>"+
                                        "<span class='glyphicon glyphicon-wrench'></span>"+
                                   "</button>";
                        }else{
                            msg += "<button type='button' class='btn btn-default _tool' title='Asignar cliente' "+
                                        "data-toggle='tooltip' onclick='asignarCliente("+data+")'>"+
                                        "<span class='glyphicon glyphicon-user'></span>"+
                                   "</button>";
                        }
                        msg += "</div></div>";
                        return msg;
                }}
                ]
            });
        }else{
            tablaCarritoDPV.ajax.reload();
        }
    }
}
function actualizaListaSalidaInventario(){
    if($("#tabla_salida_inventario").length){
        if(tablaSalidaInv == ""){
            tablaSalidaInv = $("#tabla_salida_inventario").DataTable({
                serverSide: true,
                ajax: {
                    url: "ajax/producto/getSalidaInventario.json.php",
                    data: {
                        fechaIni: function(){ return $("#fechaIni_s_vd").val(); },
                        fechaFin: function(){ return $("#fechaFin_s_vd").val(); }
                    }
                },
                paging: false,
                language: {
                    url: "locales/datatable.es.json"
                },
                dom: 'B<"clear">lfrtip',
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
                fnDrawCallback: function(full){
                    $("#tabla_salida_inventario ._tool").tooltip();
                },
                ordering: false,
                searching: false,
                columns: [
                    {data: "nombre"},
                    {data: "descripcion"},
                    {data: "cantidad"},
                    {data: "nota"},
                    {data: "tiempo", render: function(data, type, full){ return moment(data).format("LL"); }}
                ]
            });
        }else{
            tablaSalidaInv.ajax.reload();
        }
    }
}
function asignarCliente(idCarrito){
    $("#asignausuario input[name='id']").val(idCarrito);
    $("#modalProductosCarrito_asignauser").modal("show");
}
function actualizaListaCarritosCerradosPV(){
    if($("#carritos_tabla_listaCPV").length){
        if(tablaCarritoCPV == ""){
            tablaCarritoCPV = $("#carritos_tabla_listaCPV").DataTable({
                serverSide: true,
                ajax: {
                    url: "ajax/carrito/getCarritos.json.php",
                    data: {
                        c: "1",
                        full: "1",
                        cliente: function(){ return $("#cliente_vcv").val(); },
                        fechaIni: function(){ return $("#fechaIni_vcv").val(); },
                        fechaFin: function(){ return $("#fechaFin_vcv").val(); }
                    }
                },
                paging: false,
                language: {
                    url: "locales/datatable.es.json"
                },
                dom: 'B<"clear">lfrtip',
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
                fnDrawCallback: function(full){
                    $("#carritos_tabla_listaCPV ._tool").tooltip();
                    $("#productosVendidos_pv").html("");
                    full.json.productosVendidos.forEach(function(e, i){
                        $("#productosVendidos_pv").append("<div class='col-md-3'>"+e+"</div>");
                    });
                    $("#pv_totalConsulta").html(full.json.total);
                    $("#pv_totalConsultaIVA").html(full.json.totalIVA);
                    $("#pv_totalConsultaE").html(full.json.totalE);
                    $("#pv_totalConsultaT").html(full.json.totalT);
                    $("#pv_totalConsultaC").html(full.json.totalC);
                    $("#pv_totalConsultaAC").html(full.json.totalAC);
                    $("#pv_totalConsultaU").html(full.json.totalU);
                },
                ordering: false,
                searching: false,
                columns: [
                    {width: "5%", data: "idCarrito"},
                    {data: "nombre"},
                    {data: "fechaEntrega", render: function(data, type, full){ return moment(data).format("LL"); }},
                    {data: "productos"},
                    {data: "vendedor"},
                    {data: "factura", render: function(data, type, full){
                        if(data == "1")
                            return "<small class='_tool' title='RFC'><strong>RFC:</strong> "+full.carritoRS+"</small><br>"+
                                   "<small class='_tool' title='Teléfono'><span class='glyphicon glyphicon-earphone'></span> "+full.carritoE+"</small>";
                        return "No";
                    }},
                    {data: "efectivo", visible: false},
                    {data: "tarjeta", visible: false},
                    {data: "cheque", visible: false},
                    {data: "cuenta", visible: false},
                    {data:"totaltotal", render: function(data){ return "$"+number_format(data, 2); }},
                    {width: "25%", data: "idCarrito", render: function(data, type, full){
                        return "<div class='text-center'><div class='btn-group' role='group' data-idCarrito='"+data+"'>"+
                                   "<button type='button' class='btn btn-default _tool ver_lista_modal_carrito' "+
                                        "title='Ver productos en el carrito' data-toggle='tooltip'}'>"+
                                        "<span class='glyphicon glyphicon-shopping-cart'></span>"+
                                   "</button>"+
                                   "<button type='button' class='btn btn-default _tool' title='Imprimir ticket' "+
                                        "data-toggle='tooltip' onclick='imprimeTicket("+data+")'>"+
                                        "<span class='glyphicon glyphicon-print'></span>"+
                                   "</button>"+
                                   "<button type='button' class='btn btn-default _tool' title='Ver ticket' "+
                                        "data-toggle='tooltip' onclick='verTicket("+data+")'>"+
                                        "<span class='glyphicon glyphicon-eye-open'></span>"+
                                   "</button>"+
                                   "<button type='button' class='btn btn-default _tool borrarCarrito' "+
                                        "title='Borrar carrito' data-toggle='tooltip'>"+
                                        "<span class='glyphicon glyphicon-trash'></span>"+
                                   "</button>"+
                                "</div></div>";
                }}
                ]
            });
        }else{
            tablaCarritoCPV.ajax.reload();
        }
    }
}
function finalizarVenta(){
    console.log($.post("ajax/barcode/finalizaPuntoVenta.json.php", $("#punto_venta_form").serialize(), function(res){
        if(!res.error){
            vaciarCarritoPV();
            $("#modal_formaPago").modal("hide");
            if($("#requiereFacturaCB").prop("checked")){
                $("#requiereFacturaCB").prop("checked", false);
                $("#datosFacturacion").slideUp();
            }
            pv_descuento = 1;
            $("#punto_venta_form")[0].reset();
            $("#punto_venta_form select").val("").trigger("change");
            toastr.success("Venta finalizada");
            $("#imprimir_ultimo_ticket").trigger("click");
        }else{
            toastr.error(res.msg, "Error al finalizar la venta");
        }
    }));
}
function guardarCarrito(){
    console.log($.post("ajax/barcode/finalizaPuntoVenta.json.php", $("#punto_venta_form").serialize()+"&soloGuarda=1", function(res){
        if(!res.error){
            vaciarCarritoPV();
            if($("#requiereFacturaCB").prop("checked")){
                $("#requiereFacturaCB").prop("checked", false);
                $("#datosFacturacion").slideUp();
            }
            pv_descuento = 1;
            $("#punto_venta_form")[0].reset();
            $("#punto_venta_form select").val("").trigger("change");
            toastr.success("Carrito guardado");
        }else{
            toastr.error(res.msg, "Error al guardar la venta");
        }
    }));
}
function ejecutaBarcode(e){
    $.post("ajax/barcode/barcode.json.php", {barcode: e}, function(res){
        console.log(res);
        var error = true, msg = "sin definir";
        if($("#tabs .active a").attr("href") == "#punto-venta" || $("#tabs .active a").attr("href") == "#cuenta"){
            if($("#subtab_punto-venta .active a").attr("href") == "#venta-punto"){
                if(res.existe){
                    error = false;
                    if(res.producto.enInventario <= 0){
                        $("#contenido_modal_faltainventario").html("El producto "+res.producto.nombre+" no tiene elementos en inventario");
                        $("#modal_faltainventario").modal("show");
                    }else{
                        if($("#tabla-nueva-venta input[name='prod["+res.id+"]']").length){
                            $("#tabla-nueva-venta input[name='prod["+res.id+"]']").val(parseInt($("#tabla-nueva-venta input[name='prod["+res.id+"]']").val())+1).trigger("change");
                        }else{
                            var precio = "$"+number_format(res.producto.precio, 2);
                            if(res.producto.precioOutlet != null){
                                precio = "<u>"+precio+"</u><br>$"+number_format(res.producto.precioOutlet, 2);
                            }
                            totalVenta += res.precioReal;
                            nuevaVenta.row.add([
                                ++cuantosTablaNuevaVenta,
                                res.producto.nombre,
                                "<p class='text-muted'>"+res.producto.linaje+"</p>",
                                "<input type='number' min='0' class='form-control prodcant' data-id='"+res.id+"' data-real='"+res.precioReal+"' name='prod["+res.id+"]' value='1'>",
                                 precio+(res.producto.iva=="0"?"":" +IVA"),
                                "$<span class='prod-"+res.id+"'>"+number_format(res.precioReal, 2)+"<span>"]).draw().node();
                            nuevaVenta.order([0, 'desc']).draw();
                            $("#total-pagar").html(number_format(totalVenta*pv_descuento, 2));
                            var totalProd = 0;
                            $(".prodcant").each(function(i, e){
                                if($(e).val() > 0)
                                    totalProd += parseInt($(e).val());
                            });
                            $("#total-productos").html(totalProd);
                        }
                    }
                }else{
                    msg = "Producto no registrado";
                }
            }
            if($("#subtab_punto-venta .active a").attr("href") == "#venta-salida"){
                if(res.existe){
                    error = false;
                    if(res.producto.enInventario <= 0){
                        $("#contenido_modal_faltainventario").html("El producto "+res.producto.nombre+" no tiene elementos en inventario");
                        $("#modal_faltainventario").modal("show");
                    }else{
                        $("#form_salidainventario input[name='id']").val(res.id);
                        $("#mps_nombrepr").html(res.producto.nombre);
                        $("#codigobarrasps").val(e);
                        $("#modalProductoSalida").modal("show");
                    }
                }else{
                    msg = "Producto no registrado";
                }
            }
            if($("#subtab_punto-venta .active a").attr("href") == "#venta-carritosabiertos"){
                if(identifica_carrito > 0){
                    $.post("ajax/carrito/agregaCarrito.php", {
                        idCarr: identifica_carrito,
                        idProd: res.id,
                        cant: 1
                    }, function(data){
                        if(data == ""){
                            toastr.success("Se ha agregado el producto al carrito");
                            actualizaListaContenido();
                        }else{
                            toastr.error(data, "Error");
                        }
                    });
                    error = false;
                }
            }
            if($("#subtab_punto-venta .active a").attr("href") == "#venta-agregar-barras"){
                if($("#barra-contenedor-producto .alert").attr("data-id") != undefined){ /// se pretende agregar un producto
                    if(res.existe){ /// ya existe un producto con ese codigo de barras asignado
                        if(res.id == $("#barra-contenedor-producto .alert").attr("data-id")){ /// se verifica si se trata de una actualizacion
                            error = false;
                        }else{
                            msg = "Este código de barras ya está asignado a otro producto.";
                        }
                    }else{
                        error = false;
                    }
                    if(!error){
                        $.post("ajax/barcode/asignaBarcode.json.php", {
                            id: $("#barra-contenedor-producto .alert").attr("data-id"),
                            barcode: e
                        }, function(res){
                            console.log(res);
                            if(!res.error){
                                if(res.msg != ""){
                                    toastr.info(res.msg);
                                }else{
                                    toastr.success("Asignación realizada");
                                }
                            }else{
                                toastr.error(res.msg);
                            }
                        });
                    }
                }else{
                    msg = "Primero escribe el nombre del producto";
                }
            }
            if($("#subtab_punto-venta .active a").attr("href") == "#venta-eliminar-barras"){
                console.log("Eliminar codigo de barras");
                if(res.existe){
                    error = false;
                    $("#barra-contenedor-producto-eliminar").attr("data-id", res.id);
                    $("#barra-contenedor-producto-eliminar .alert").slideDown('fast');
                    $("#barra-contenedor-producto-eliminar .nombre-producto").html(res.producto.nombre);
                    $("#barra-contenedor-producto-eliminar .descripcion-producto").html(res.producto.descripcion);
                    $("#barra-contenedor-producto-eliminar .precio-producto").html(number_format(res.producto.precio, 2)+(res.producto.iva=="0"?"":" +IVA"));
                    $("#barra-contenedor-producto-eliminar .en-inventario").html(res.producto.enInventario);
                }else{
                    msg = "El código de barras no pertenece a ningun producto";
                    $("#barra-contenedor-producto-eliminar .alert").slideUp('fast');
                    $("#barra-contenedor-producto-eliminar").removeAttr("data-id");
                    $("#barra-contenedor-producto-eliminar .nombre-producto").html("Nombre del producto");
                    $("#barra-contenedor-producto-eliminar .descripcion-producto").html("Descripción");
                    $("#barra-contenedor-producto-eliminar .precio-producto").html("0.00");
                    $("#barra-contenedor-producto-eliminar .en-inventario").html("0");
                }
            }
            if(error){
                toastr.error(msg);
            }
        }else{
            console.log($("#tabs .active a").attr("href"));
            if(res.existe){
                $.gritter.add({
                    title: res.producto.nombre,
                    text: res.producto.descripcion+"<hr><strong>$"+number_format(res.precioReal, 2)+"</strong>",
                    time: 3000
                });
            }else{
                $.gritter.add({
                    title: "Código de barras no encontrado",
                    time: 2500
                });
            }
        }
    });
}
function cobranza(){
    var totalTemp = (totalVenta*pv_descuento)-saldoFavor;
    if(saldoFavor > 0){
        $("#tieneCredito").removeClass("hidden");
        $("#tieneCredito input[name='credito']").val(saldoFavor);
    }else{
        $("#tieneCredito").addClass("hidden");
    }
    if(saldoFavor > (totalVenta*pv_descuento)){
        totalTemp = 0;
        $("#tieneCredito input[name='credito']").val((totalVenta*pv_descuento));
    }else{
        $(".cobranza").each(function(i, e){
            totalTemp -= $(e).val();
        });
    }
    $("#modalfp_total").html(number_format(totalTemp, 2));
    if(totalTemp <= 0){
        $("#modal_formaPago .btn-primary").removeAttr("disabled");
    }else{
        $("#modal_formaPago .btn-primary").attr("disabled", "disabled");
    }
}
function formatRepo_pv(repo, a, b) {
    if (repo.loading) return "Buscando...";
    console.log(repo, a, b);
    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class=''>" +
        "<div class='select2-result-repository__title'>" + repo.text;
    if(repo.text == repo.id)
        markup += "<small style=''><br><i>Agregar cliente</i></small>";
    markup += "</div></div></div>";
    /*if(!$.isArray(repo.dias)){
        repo.dias = $.parseJSON(repo.dias);
    }
    if(repo.dias.length){
        markup += "<div class='select2-result-repository__description'>Días hábiles: ";
        repo.dias.forEach(function(e, i){
            if(i != 0)
                markup += ", ";
            markup += dias[e];
        });
        markup += "</div>";
    }
    markup += "<div class='select2-result-repository_statistics'>";
    markup += "<div class='select2-result-repository__stargazers'>" + (repo.costoMensajeria != 0?"Costo: $"+number_format(repo.costoMensajeria, 2): "Sin costo") + "</div>";
    
    markup += "</div>" +
              "</div></div>";*/

    return markup;
}


function preparaPuntoVenta(elemento, url){
    $("#tabs a[href='"+elemento+"']").on('shown.bs.tab', function(e){
        elementoGlobal = elemento;
        if($(elemento).html() == ""){
            $.get("tabs/"+url, {"_": $.now()}, function(data){
                $(elemento).html(data);
                $("#datosFacturacion").slideUp(0);
                nuevaVenta = $("#tabla-nueva-venta").DataTable({
                    language: {
                        url: "locales/datatable.es.json"
                    },
                    info: false,
                    searching: false,
                    paging: false
                });
                $("#barra-contenedor-producto .alert").slideUp(0);
                $("#barra-contenedor-producto-eliminar .alert").slideUp(0);
                $('#barra-agrega-producto').typeahead({
                    hint: true,
                    highlight: true
                }, {
                    display: 'nombre',
                    limit: Infinity,
                    source: _auto_producto
                });
                $(document).on("change", ".prodcant", function(){
                    if($(this).val() == 0){
                        var quita = parseInt($(this).parents('tr').children("td:first-child").html());
                        console.log("Antes: "+totalVenta);
                        console.log("quitando "+ parseFloat($(".prod-"+$(this).attr("data-id")).html().replace(",", "")));
                        totalVenta -= parseFloat($(".prod-"+$(this).attr("data-id")).html().replace(",", ""));
                        console.log("despues: "+totalVenta);
                        nuevaVenta.row(quita-1).remove().draw();
                        cuantosTablaNuevaVenta--;
                        $("#total-pagar").html(number_format(totalVenta*pv_descuento, 2));
                    }else{
                        if($(this).val() < 1){
                            toastr.error("No se permiten valores negativos aqui.");
                            $(this).val(1).trigger("change");
                        }else{
                            var precio = parseFloat($(this).attr("data-real"));
                            precio *= $(this).val();
                            totalVenta -= parseFloat($(".prod-"+$(this).attr("data-id")).html().replace(",", ""));
                            $(".prod-"+$(this).attr("data-id")).html(number_format(precio, 2));
                            totalVenta += precio;
                            $("#total-pagar").html(number_format(totalVenta*pv_descuento, 2));
                        }
                    }
                    var totalProd = 0;
                    $(".prodcant").each(function(i, e){
                        if($(e).val() > 0)
                            totalProd += parseInt($(e).val());
                    });
                    $("#total-productos").html(totalProd);
                });
                $(document).on("click", "#eliminarBarcode", function(){
                    if($(this).parent().parent().attr("data-id") != undefined){
                        $.post("ajax/barcode/eliminaBarcode.json.php", {
                            id: $(this).parent().parent().attr("data-id")
                        }, function(res){
                            console.log(res);
                            if(!res.error){
                                if(res.msg == ""){
                                    toastr.success("Código de barras eliminado");
                                    $("#barra-contenedor-producto-eliminar .alert").slideUp('fast');
                                }else{
                                    toastr.info(res.msg);
                                }
                            }else{
                                toastr.error(res.msg);
                            }
                        });
                    }else{
                        toastr.error("Escanea un producto para poder eliminar su código de barras");
                    }
                });
                $('#barra-agrega-producto').bind('typeahead:select', function(ev, suggestion) {
                    $.post("ajax/producto/getProductoById.json.php", { idProducto:suggestion.idProducto}, function(res){
                        $("#barra-contenedor-producto .nombre-producto").html(res.nombre);
                        $("#barra-contenedor-producto .descripcion-producto").html(res.descripcion);
                        $("#barra-contenedor-producto .precio-producto").html(number_format(res.precio, 2)+(res.iva=="0"?"":" +IVA"));
                        $("#barra-contenedor-producto .en-inventario").html(res.enInventario);
                        $("#barra-contenedor-producto .alert").attr("data-id", res.idProducto).slideDown('fast').trigger("focus");

                    });
                });
                $('#barra-agrega-producto').bind('typeahead:open', function(ev, suggestion) {
                    $("#barra-contenedor-producto .alert").removeAttr("data-id").slideUp('fast');
                    $("#barra-contenedor-producto .nombre-producto").html("Nombre del producto");
                    $("#barra-contenedor-producto .descripcion-producto").html("Descripción");
                    $("#barra-contenedor-producto .precio-producto").html("0.00");
                    $("#barra-contenedor-producto .en-inventario").html("0");
                });
                $("#modal_formaPago .cobranza").change(cobranza);
                $("#modal_formaPago .cobranza").keyup(cobranza);
                $("#modal_formaPago .cubrirTodo").click(function(){
                    var totalTemp = (totalVenta*pv_descuento)-saldoFavor;
                    $(".cobranza").each(function(i, e){
                        totalTemp -= $(e).val();
                    });
                    $(".cobranza").eq($("#modal_formaPago .cubrirTodo").index($(this))).val(number_format(totalTemp, 2, ".", "")).trigger("change");
                });
                $("#modal_formaPago .reiniciar").click(function(){
                    $("#modal_formaPago .cobranza").val("");
                    $("#modal_formaPago input[name='cualrecibo']").val(0);
                    $("#modalfp_cajaCambio").html("");
                    cobranza();
                });
                $("#modal_formaPago input[name='cualrecibo']").change(function(e){
                    $("#modalfp_cajaCambio").html("$"+number_format($("#modal_formaPago input[name='cualrecibo']").val()-$("#modal_formaPago input[name='efectivo']").val()), 2);
                });
                $("#punto_venta_form").keydown(function(event){
                    if(event.keyCode == 13) {
                        event.preventDefault();
                        return false;
                    }
                });
                $(document).on("submit", "#punto_venta_form", function(e){
                    e.preventDefault();
                    console.log("1");
                    if(cuantosTablaNuevaVenta){
                        console.log("2");
                        $.post("ajax/carrito/verificaInventario.json.php", $(this).serialize(), function(res){
                            console.log("3", res);
                            if(!res.error){
                                $("#modal_formaPago .reiniciar").trigger("click");
                                $("#modal_formaPago").modal("show");
                            }else{
                                $("#contenido_modal_faltainventario").html(res.msg);
                                $("#modal_faltainventario").modal("show");
                            }
                        });
                    }else{
                        toastr.info("El carrito debe tener por lo menos un articulo para pagarlo");
                    }
                });
                $(document).on("change", "#select_pv_descuento", function(e){
                    pv_descuento = 1;
                    if($(this).val()!=""&&$(this).val()!=null){
                        pv_descuento = 1-($("#select_pv_descuento option:selected").attr("data-valor") / 100);
                    }
                    if($("#requiereFacturaCB").prop("checked")){
                        pv_descuento *= 1.16;
                    }
                    $("#total-pagar").html(number_format(totalVenta*pv_descuento, 2));
                });
                $(document).on("submit", "#form_importar_cvs", function(e){
                    e.preventDefault();
                    var formData = new FormData($("#form_importar_cvs")[0]);
                    $.ajax({
                        cache: false,
                        contentType: false,
                        processData: false,
                        url: "ajax/producto/importaExcel.json.php",
                        data: formData,
                        type: "post",
                        success: function (data) {
                            if(!data.error){
                                toastr.success("Importación completada");
                                $("#form_importar_cvs")[0].reset();
                            }else{
                                toastr.error(data.msg);
                            }
                        }
                    });
                });
                $(document).on("click", "#imprimir_ultimo_ticket", function(){
                    $.post("ajax/barcode/dameUltimoCarrito.json.php", {_: $.now()}, function(res){
                        if(res != 0){
                            console.warn("Elemento: ", elemento);
                            if(elemento == "cuenta.html")
                                imprimeTicket(res, 1);
                            else
                                imprimeTicket(res, 0);
                        }else{
                            toastr.error("No hay un ticket anterior.");
                        }
                    });
                });
                $(document).on("click", "#consulta_salida_boton", function(){
                    console.log("...");
                    if($("#fechaIni_s_vd").val()==""){
                        $("#fechaIni_s_vd, #fechaFin_s_vd").val(moment().format("Y-MM-DD"));
                    }
                    if($("#fechaFin_s_vd").val()=="")
                        $("#fechaFin_s_vd").val($("#fechaIni_s_vd").val());
                    if($("#fechaFin_s_vd").val() < $("#fechaIni_s_vd").val()){
                        toastr.error("La fecha de inicio debe ser menor o igual a la de fin");
                    }else{
                        actualizaListaSalidaInventario();
                    }
                });
                $(document).on("click", "#consulta_devolucion_boton", function(){
                    console.log("...");
                    if($("#fechaIni_vd").val()==""){
                        $("#fechaIni_vd, #fechaFin_vd").val(moment().format("Y-MM-DD"));
                    }
                    if($("#fechaFin_vd").val()=="")
                        $("#fechaFin_vd").val($("#fechaIni_vd").val());
                    if($("#fechaFin_vd").val() < $("#fechaIni_vd").val()){
                        toastr.error("La fecha de inicio debe ser menor o igual a la de fin");
                    }else{
                        actualizaListaCarritosCerradosDPV();
                    }
                });

                $(document).on("click", "#consulta_carritosabiertos_boton", function(){
                    console.log("...");
                    if($("#fechaIni_ca_vd").val()==""){
                        $("#fechaIni_ca_vd, #fechaFin_ca_vd").val(moment().format("Y-MM-DD"));
                    }
                    if($("#fechaFin_ca_vd").val()=="")
                        $("#fechaFin_ca_vd").val($("#fechaIni_ca_vd").val());
                    if($("#fechaFin_ca_vd").val() < $("#fechaIni_ca_vd").val()){
                        toastr.error("La fecha de inicio debe ser menor o igual a la de fin");
                    }else{
                        actualizaListaCarritosAbiertosPV();
                    }
                });
                $(document).on("click", "#consulta_ventas_boton", function(){
                    if($("#fechaIni_vcv").val()==""){
                        $("#fechaIni_vcv, #fechaFin_vcv").val(moment().format("Y-MM-DD"));
                    }
                    if($("#fechaFin_vcv").val()=="")
                        $("#fechaFin_vcv").val($("#fechaIni_vcv").val());
                    if($("#fechaFin_vcv").val() < $("#fechaIni_vcv").val()){
                        toastr.error("La fecha de inicio debe ser menor o igual a la de fin");
                    }else{
                        actualizaListaCarritosCerradosPV();
                    }
                });
                $(".select2cliente").select2({
                    language: "es",
                    width: "100%",
                    allowClear: true,
                    templateResult: formatRepo_pv,
                    tags:true,
                    ajax: {
                        url: 'ajax/cliente/select2.json.php',
                        type: "post",
                        data: function (params) {
                            var query = {
                                _: $.now(),
                                search: params.term,
                                page: params.page || 1
                            };
                            return query;
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    placeholder: "Selecciona un cliente"
                });
                $("#punto_venta_form .select2cliente").change(function(){
                    saldoFavor = 0;
                    if($.isNumeric($(this).val()) && $(this).val() > 0){
                        var datos = $("#punto_venta_form .select2cliente").select2("data")[0];
                        console.warn(datos);
                        $("#punto_venta_form input[name='razonSocial']").val(datos.razonSocial);
                        $("#punto_venta_form input[name='correoFactura']").val("");
                        $("#punto_venta_form #select_pv_descuento").val(datos.idDescuento);
                        $("#punto_venta_form #select_pv_descuento").trigger("change");
                        $.post("ajax/cliente/getClienteById.json.php", {id: $(this).val()}, function(datos2){
                            if(datos2.saldo>0){
                                $("#tieneSaldoAFavor").removeClass("hidden");
                                $("#punto_venta_form input[name='credito2']").val(datos2.saldo);
                                saldoFavor = datos2.saldo;
                            }else{
                                $("#tieneSaldoAFavor").addClass("hidden");
                                $("#punto_venta_form input[name='credito2']").val(0);
                            }
                        });
                    }else{
                        $("#punto_venta_form input[name='razonSocial']").val("");
                        $("#punto_venta_form input[name='correoFactura']").val("");
                        $("#punto_venta_form input[name='credito2']").val(0);
                        $("#tieneSaldoAFavor").addClass("hidden");
                        $("#punto_venta_form #select_pv_descuento").val("");
                        $("#punto_venta_form #select_pv_descuento").trigger("change");
                    }
                });
                $("#requiereFacturaCB").change(function(){
                    if($("#requiereFacturaCB").prop("checked")){
                        $("#datosFacturacion").slideDown();
                        pv_descuento *= 1.16;
                    }else{
                        $("#datosFacturacion").slideUp();
                        pv_descuento /= 1.16;
                    }
                    $("#total-pagar").html(number_format(totalVenta*pv_descuento, 2));
                });
                $("#simulaBarcode").submit(function(e){
                    e.preventDefault();
                    ejecutaBarcode($("#barcodemanual").val());
                    $("#barcodemanual").val("");
                });
                $("#simulaBarcode3").submit(function(e){
                    e.preventDefault();
                    ejecutaBarcode($("#barcodemanual3").val());
                    $("#barcodemanual3").val("");
                });
                $("#form_salidainventario").submit(function(e){
                    e.preventDefault();
                    $.post("ajax/producto/salidaInventario.php", $(this).serialize(), function(){
                        $("#modalProductoSalida").modal("hide");
                        toastr.success("Salida de inventario completada");
                        actualizaListaSalidaInventario();
                    });
                });
                $("#asignausuario").submit(function(e){
                    e.preventDefault();
                    $.post("ajax/carrito/asignaCliente.php", $(this).serialize(), function(){
                        $("#modalProductosCarrito_asignauser").modal("hide");
                        toastr.success("Asignación completa");
                        actualizaListaCarritosCerradosDPV();
                        actualizaListaCarritosAbiertosPV();
                    });
                });
                revisaPermisos("#subtab_punto-venta a");
                ajaxToHtml("categoria/descuento/getDescuentoSelect.php", "#select_pv_descuento", true, "");
            });
        }                  
    });
    $(document).on("shown.bs.tab", elemento+" a[href='#venta-consulta-ventas']", function(){
        actualizaListaCarritosCerradosPV();
        actualizaListaCarritosCerradosDPV();
        actualizaListaCarritosAbiertosPV();
    });
}
$(function(){
    preparaPuntoVenta("#punto-venta", "punto-venta.html");
    preparaPuntoVenta("#cuenta", "cuenta.html");
    $(document).scannerDetection(function(e){
        console.log(e);
        ejecutaBarcode(e);
    });
    $(document).on("click", ".devolverProducto", function(){
        var id = $(this).parent().attr("data-idCarritoContenido"),
             n = $(this).parent().attr("quien"),
             c = $(this).parent().attr("cuanto");
        if(confirm("Se eliminará "+c+" unidad(es) de "+n+" del carrito. ¿Desea continuar?")){
            $.post("ajax/carrito/quitaCarrito.php", {id: id}, function(){
                devolucion(ultimo_dev_id);
            });
        }
    });
    $("#formAuxiliarCarritos").submit(function(e){
        e.preventDefault();
    });
});