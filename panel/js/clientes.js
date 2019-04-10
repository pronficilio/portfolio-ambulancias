var tablaClientes = "", tablaRegistrados = "";

function actualizaListaClientes(){
    if(tablaClientes == ""){
        tablaClientes = $("#clientes_tabla").DataTable({
            serverSide: true,
            ajax: "ajax/cliente/getClientes.json.php",
            pageLength: 10,
            ordering: false,
            lengthMenu: [10, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#clientes_tabla ._tool").tooltip();
            },
            columns : [
                {width: "5%"},
                {},
                {},
                {},
                {},
                {},
                {width: "20%"}
            ]
        });
    }else{
        tablaClientes.ajax.reload();
    }
    
}

function actualizaListaRegistrados(){
    if(tablaRegistrados == ""){
        tablaRegistrados = $("#registrados_tabla").DataTable({
            serverSide: true,
            ajax: "../admin/ajax/dameRegistrados.json.php",
            pageLength: 10,
            lengthMenu: [10, 15, 20, 50, -1],
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
            fnDrawCallback: function(){
                $("#registrados_tabla ._tool").tooltip();
            },
            columns : [
                {width: "5%", data: "id"},
                {data: "nombre"},
                {data: "apellido"},
                {data: "edad"},
                {data: "email"},
                {data: "escuela"},
                {data: "grado"},
                {data: "tutor", visible:false},
                {data: "emailtutor"},
                {data: "subsistema", visible:false},
                {data: "municipioNombre", visible: false},
                {data: "enterado", visible: false},
                {data: "anio", visible: false},
                {data: "suma", visible: false},
                {data: "hora"},
                {data: "salon"},
                {data: "id", orderable: false, render: function(data){
                    return "<a class='btn btn-default btn-icon-only editReg'><i class='glyphicon glyphicon-pencil'></i></a>"+
                         "<a class='btn btn-default btn-icon-only trash'><i class='glyphicon glyphicon-trash'></i></a>";
                }}
            ]
        });
    }else{
        tablaRegistrados.ajax.reload();
    }
    
}

function agregaCliente(){
    if(!camposVacios("#new_nombre")){
        if($("#new_email").val() == "" || isEmail($("#new_email").val())){
            $("#new_email").parent().addClass("has-success");
            $("#new_email").parent().removeClass("has-error");
            
            $.post("ajax/cliente/nuevoCliente.php", $("#form_cliente_nuevo").serialize(), function(data){
                if(data != "")
                    mensaje(data, "alert-danger");
                else{
                    mensaje("Cliente agregado correctamente", "alert-success");
                    $("#form_cliente_nuevo")[0].reset();
                    limpiaCampos("#new_nombre,#new_email,#new_labeltel1,#new_tel1,#new_calle,#new_entrecalle,#new_colonia,#new_ciudad,#new_cp,#new_estado,#new_notas", 1);
                    $(".artificial").slideUp("slow");
                    $(".artificial").remove();
                }
            });
        }else{
            $("#new_email").parent().addClass("has-error");
            $("#new_email").parent().removeClass("has-success");
        }
    }
}

function editaCliente(){
    if(!camposVacios("#editmodal_nombre")){
        //if(isEmail($("#editmodal_email").val()) || $("#editmodal_email").val()==""){
            $.post("ajax/cliente/editaCliente.php", $("#form_cliente_edit").serialize(), function(data){
                console.log(data);
                if(data != "")
                    ponMensaje(data, "alert-danger", "#mensajesEditaCliente", "#modalEditaCliente");
                else{
                    actualizaListaClientes();
                    $("#modalEditaCliente").modal("hide");
                    mensaje("Cambios realizados", "alert-success");
                    limpiaCampos("#editmodal_nombre,#editmodal_email,#edit_labeltel1,#edit_idTelFirst,#editmodal_tel1,#editmodal_calle,#editmodal_entrecalle,#editmodal_colonia,#editmodal_ciudad,#editmodal_cp,#editmodal_estado,#editmodal_notas", 1);
                    $("#form_cliente_edit")[0].reset();
                    $(".artificial_edit").slideUp("slow");
                    $(".artificial_edit").remove();
                }
            });
        /*}else{
            $("#editmodal_nombre").parent().addClass("has-error");
            $("#editmodal_nombre").parent().removeClass("has-success");
        }*/
    }
}

function preparaModalEditaCliente(id){
    console.log(id),
    $.post("ajax/cliente/getClienteById.json.php", "id="+id, function(data){
        console.log(data);
        $("#tituloModalNombreCliente").html(data.nombre);
        $("#cliente_edit_id").val(data.idCliente);
        $("#editmodal_nombre").val(data.nombre);
        $("#editmodal_email").val(data.email);
        $("#edit_acdescuento").val(data.idDescuento);
        $(".artificial_edit").remove();
        if(data.tel != null){
            data.tel.forEach(function(e, i){
                if(i == 0){
                    $("#edit_idTelFirst").val(e.idTelefono);
                    $("#edit_labeltel1").val(e.label);
                    $("#editmodal_tel1").val(e.numero);
                }else{
                    real = i + 1;
                    $("#clientes_editmodal_telefonos").append('<div class="form-group artificial_edit">'+
                                                '<label class="col-sm-3 control-label" for="edit_labeltel'+real+'">Teléfono ('+real+'):</label>'+
                                                '<div class="col-sm-4">'+
                                                '<input type="text" class="form-control" name="label[]" id="edit_labeltel'+real+'" placeholder="Etiqueta teléfono" value="'+e.label+'">'+
                                                '<input type="hidden" name="idTel[]" value="'+e.idTelefono+'">'+
                                                '</div>'+
                                                '<div class="col-sm-4">'+
                                                '<div class="input-group">'+
                                                '<div class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></div>'+
                                                '<input type="text" class="form-control telefonoEdit" padre="#clientes_editmodal_telefonos" name="telefono[]" placeholder="Teléfono" value="'+e.numero+'">'+
                                                '</div>'+
                                                '</div>'+
                                                '</div>');
                }
            });
            $(".telefonoEdit").last().trigger("change");
        }else{
            $("#edit_idTelFirst").val("");
            $("#edit_labeltel1").val("");
            $("#editmodal_tel1").val("");
        }
        // calle, entre, col, ciudad, estado, cp
        $("#editmodal_calle").val(data.calle);
        $("#editmodal_entrecalle").val(data.entre);
        $("#editmodal_colonia").val(data.col);
        $("#editmodal_ciudad").val(data.ciudad);
        $("#editmodal_cp").val(data.cp);
        $("#editmodal_estado").val(data.estado);
        $("#editmodal_notas").val(data.notasCliente);
        $("#form_cliente_edit input[name='categoria']").val(data.categoria);
        $("#form_cliente_edit input[name='tipoTienda']").val(data.tipoTienda);
        $("#form_cliente_edit input[name='noTienda']").val(data.noTiendas);
        $("#form_cliente_edit input[name='expo']").val(data.expo);
        $("#form_cliente_edit input[name='envioNombre']").val(data.envioNombre);
        $("#form_cliente_edit input[name='tipoDoc']").val(data.tipoDocumento);
        $("#form_cliente_edit input[name='razSoc']").val(data.razonSocial);
        $("#form_cliente_edit input[name='rfc']").val(data.rfc);
        $("#form_cliente_edit input[name='cb']").val(data.cuentaBancaria);
        $("#form_cliente_edit input[name='formaPago']").val(data.formaPago);
        $("#form_cliente_edit input[name='correoFac']").val(data.correoFac);
        $("#form_cliente_edit input[name='fechaContacto']").val(data.fechaContacto);
        $("#form_cliente_edit input[name='tareas']").val(data.tareas);
        $("#modalEditaCliente").modal("show");
    }, "json");
}

$(function(){
    $("#tabs a[href='#clientes']").on('shown.bs.tab', function(e){
        if($("#clientes").html() == ""){
            $.get("tabs/clientes.php", {"_": $.now()}, function(data){
                $("#clientes").html(data);
                actualizaListaClientes();
                actualizaListaRegistrados();
                revisaPermisos("#subtab_cliente a");
                $(document).on("click", "#registrados_tabla .trash", function(){
                    var tr = $(this).closest('tr');
                    var row = tablaRegistrados.row( tr );
                    console.log(row.data());
                    var d = row.data();
                    if(confirm("vas a borrar? o fue dedazo?"))
                        $.post("../admin/ajax/eliminaRegistro.json.php", {llave: "erubey", id: d.id}, function(res){
                            actualizaListaRegistrados();
                        });
                });
                $(document).on("click", "#registrados_tabla .editReg", function(){
                    var tr = $(this).closest('tr');
                    var row = tablaRegistrados.row( tr );
                    console.log(row.data());
                    var d = row.data();
                    $("#form_cregistrado_edit #cregistrado_edit_id").val(d.id);
                    $("#form_cregistrado_edit #nombre").val(d.nombre);
                    $("#form_cregistrado_edit #apellido").val(d.apellido);
                    $("#form_cregistrado_edit #edad").val(d.edad);
                    $("#form_cregistrado_edit #email").val(d.email);
                    $("#form_cregistrado_edit #escuela").val(d.escuela);
                    $("#form_cregistrado_edit #grado").val(d.grado);
                    $("#form_cregistrado_edit #tutor").val(d.tutor);
                    $("#form_cregistrado_edit #emailtutor").val(d.emailtutor);
                    $("#form_cregistrado_edit #subsis").val(d.subsistema);
                    $("#form_cregistrado_edit #municipio").val(d.municipio);
                    $("#modalEditaCregistrado").modal("show");
                });
                $("#form_cregistrado_edit").submit(function(e){
                    e.preventDefault();
                    $.post("../admin/ajax/editaRegistro.php", $(this).serialize(), function(res){
                        toastr.success("Cambios realizados");
                        $("#modalEditaCregistrado").modal("hide");
                        actualizaListaRegistrados();
                    });
                });
                ajaxToHtml("categoria/descuento/getDescuentoSelect.php", "#edit_acdescuento", true, "");
                ajaxToHtml("categoria/descuento/getDescuentoSelect.php", "#new_acdescuento", true, "");
            });
        }          
    });
    $(document).on('shown.bs.tab', "#subtab_cliente a[href='#clientes_lista']", function(e){
        actualizaListaClientes();
    });
    $(document).on("change", ".telefono", function(){
        var who = $("#clientes_new_telefonos").children(".form-group").index($(this).parent().parent().parent());
        console.log("Telefonos: "+$("#clientes_new_telefonos").children(".form-group").length);
        if($("#clientes_new_telefonos").children(".form-group").length == who + 1){
            var real = who+2;
            $("#clientes_new_telefonos").append('<div class="form-group artificial">'+
                                                '<label class="col-sm-3 control-label">Teléfono ('+real+'):</label>'+
                                                '<div class="col-sm-4">'+
                                                '<input type="text" class="form-control" name="label[]" id="new_labeltel'+real+'" placeholder="Etiqueta teléfono">'+
                                                '</div>'+
                                                '<div class="col-sm-4">'+
                                                '<div class="input-group">'+
                                                '<div class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></div>'+
                                                '<input type="text" class="form-control telefono" name="telefono[]" id="new_tel'+real+'" placeholder="Teléfono">'+
                                                '</div>'+
                                                '</div>'+
                                                '</div>');
        }
    });
    $(document).on("change", ".telefonoEdit", function(){
        var who = $($(this).attr("padre")).children(".form-group").index($(this).parent().parent().parent());
        if($($(this).attr("padre")).children(".form-group").length == who + 1){ /// solo agrega si ocupas el ultimo input
            var real = who+2;
            $($(this).attr("padre")).append('<div class="form-group artificial_edit">'+
                                            '<label class="col-sm-3 control-label" for="edit_labeltel'+real+'">Teléfono ('+real+'):</label>'+
                                            '<div class="col-sm-4">'+
                                            '<input type="text" class="form-control" name="label[]" id="edit_labeltel'+real+'" placeholder="Etiqueta teléfono">'+
                                            '</div>'+
                                            '<div class="col-sm-4">'+
                                            '<div class="input-group">'+
                                            '<div class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></div>'+
                                            '<input type="text" class="form-control telefonoEdit" padre="'+$(this).attr("padre")+'" name="telefono[]" placeholder="Teléfono">'+
                                            '</div>'+
                                            '</div>'+
                                            '</div>');
        }
    });
    $(document).on("submit", "#form_cliente_nuevo", function(e){
        e.preventDefault();
        agregaCliente();
    });
    $(document).on("submit", "#form_cliente_edit", function(e){
        e.preventDefault();
        editaCliente();
    });
    $(document).on("click", ".editaCliente", function(data){
        preparaModalEditaCliente($(this).parent().attr("data-idCliente"));
    });
    $(document).on("submit", "#form_importar_xls", function(e){
        e.preventDefault();
        var formData = new FormData($("#form_importar_xls")[0]);
        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            url: "ajax/cliente/importaExcel.json.php",
            data: formData,
            type: "post",
            success: function (data) {
                console.log(data);
                if(!data.error){
                    toastr.success("Importación completada");
                    $("#form_importar_xls")[0].reset();
                }else{
                    toastr.error(data.msg);
                }
            }
        });
    });
});