
var tablaProveedores = "";

function actualizaListaProveedores() {
    "use strict";
    if (tablaProveedores === "") {
        tablaProveedores = $("#proveedores_tabla").DataTable({
            serverSide: true,
            ajax: "ajax/proveedores/dameProveedores.json.php",
            pageLength: 10,
            lengthMenu: [10, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function () {
                $("#proveedores_tabla ._tool").tooltip();
            },
            order: [ 1, 'asc' ],
            columns : [
                {orderable: false, width: "5%"},
                {orderable: true},
                {orderable: true},
                {orderable: true},
                {orderable: false}
            ]
        });
    } else {
        tablaProveedores.ajax.reload();
    }
}

function eliminaProveedor(id){
    $.post("ajax/proveedores/eliminaProveedor.json.php", {idProv: id}, function(res){
        if(!res.error){
            actualizaListaProveedores();
            toastr.success("Proveedor eliminado");
        }else{
            toastr.error(res.msg);
        }
    });
}
function asignaProveedor(idCat, idProv, nombre){
    $.post("ajax/proveedores/asignaProveedor.json.php", {idProv: idProv, idCat: idCat}, function(res){
        if(!res.error){
            if($("#categorias_asignadas [data-id='"+idCat+"']").length == 0)
                $("#categorias_asignadas").append("<button class='btn btn-default eliminarCategoria margintop20' data-id='"+idCat+"'> "+nombre+"</button> ");
            toastr.success("Proveedor asignado");
        }else{
            toastr.error(res.msg);
        }
    });
}
$(function(){
    $(document).on('shown.bs.tab', "#subtab_catalogo a[href='#catalogo_proveedores']", function(e){
        actualizaListaProveedores();
        ajaxToHtml("categoria/getCategoriasSelect.php", "#select_categoria_proveedores", true, "");
    });
    $(document).on("submit", "#form_agrega_proveedor", function(e){
        e.preventDefault();
        $.post("ajax/proveedores/agregarProveedor.json.php", $(this).serialize(), function(res){
            if(!res.error){
                toastr.success("Proveedor agregado");
                $("#form_agrega_proveedor")[0].reset();
                actualizaListaProveedores();
            }else{
                toastr.error(res.msg);
            }
        });
    });
    $(document).on("submit", "#form_edita_proveedor", function(e){
        e.preventDefault();
        $.post("ajax/proveedores/editarProveedor.json.php", $(this).serialize(), function(res){
            if(!res.error){
                toastr.success("Proveedor editado");
                actualizaListaProveedores();
                $("#modal_editar_proveedor").modal("hide");
            }else{
                toastr.error(res.msg);
            }
        });
    });
    $(document).on("click", ".enviarAccesoProveedor", function(){
        $.post("ajax/proveedores/enviarAcceso.json.php", {idProv: $(this).attr("data-id") }, function(res){
            if(!res.error){
                toastr.success("Se ha enviado al proveedor su acceso por mail");
            }else{
                toastr.error(res.msg);
            }
        });
    });
    $(document).on("click", ".editarProveedor", function(){
        $.post("ajax/proveedores/dameProveedorById.json.php", {idProv: $(this).attr("data-id")}, function(res){
            if(!res.error){//nombre, telefono, email, idProveedor
                $("#modal_editar_proveedor input[name='nombre']").val(res.data.nombre);
                $("#modal_editar_proveedor input[name='email']").val(res.data.email);
                $("#modal_editar_proveedor input[name='tel']").val(res.data.telefono);
                $("#modal_editar_proveedor input[name='id']").val(res.data.idProveedor);
                $("#modal_editar_proveedor").modal("show");
            }else{
                toastr.error(res.msg);
            }
        });
    });
    $(document).on("click", ".enlazarProveedor", function(){
        $(".nombre-proveedor").html("");
        $("#categorias_asignadas").html("");
        $.post("ajax/proveedores/dameProveedorById.json.php", {idProv: $(this).attr("data-id")}, function(res){
            $(".nombre-proveedor").html(res.data.nombre);
        });
        $.post("ajax/proveedores/dameCategorias.json.php", {idProv: $(this).attr("data-id")}, function(res){
            if(res.data.length){
                res.data.forEach(function(e, i){
                    $("#categorias_asignadas").append("<button class='btn btn-default eliminarCategoria margintop20' data-id='"+e.idCategoria+"'> "+e.nombre+"</button> ");
                });
            }
        });
        $("#modal_categoria_proveedor input[name='id']").val($(this).attr("data-id"));
        $("#select_categoria_proveedores").val("");
        $("#modal_categoria_proveedor").modal("show");
    });
    $(document).on("click", ".eliminaAccesoProveedor", function(){
        confirma("Eliminar proveedor", "Se eliminará el proveedor y sus categorias relacionadas se quedarán sin proveedor asignado<br>¿Deseas continuar?",
                 "eliminaProveedor(" + $(this).attr("data-id") + ");");
    });
    $(document).on("click", "#asignaProveedorCategoria", function(){
        if($("#select_categoria_proveedores").val() != ""){
            asignaProveedor($("#select_categoria_proveedores").val(), $("#modal_categoria_proveedor input[name='id']").val(), $("#select_categoria_proveedores option:selected").text());
        }else{
            toastr.error("Selecciona una categoria para asignarla al proveedor");
        }
    });
    $(document).on("click", ".eliminarCategoria", function(){
        var elemento = $(this);
        $.post("ajax/proveedores/eliminaCategoria.json.php", {
            idProv: $("#modal_categoria_proveedor input[name='id']").val(),
            idCat: $(this).attr("data-id")
        }, function(res){
            if(!res.error){
                toastr.success("Categoria desenlazada");
                elemento.remove();
            }else{
                toastr.error(res.msg);
            }
        });
    });
});