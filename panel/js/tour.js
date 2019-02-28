function iniciaTour(arr){
    var intro = introJs();
    intro.setOptions({
        steps: arr
    });
    intro.start();
}
function tourAgregarPermiso(){
    var intro = introJs();
    intro.setOptions({
        steps: [{
            intro: "En esta sección podrás agregar permisos de lo que ven los diferentes tipos de usuarios<br><small>Haz clic en <i>Sig</i> para iniciar el recorrido</small>"
        }, {
            element: "#form_agregar_permiso input[type='text']",
            intro: "Escribe aquí el nombre del permiso",
            position: 'bottom'
        }, {
            element: "#tree_permisos",
            intro: "Selecciona las pestañas y subpestañas a las que tendrá el permiso",
            position: 'top'
        }, {
            element: "#form_agregar_permiso button[type='submit']",
            intro: "Guarda el permiso haciendo clic en este botón",
            position: 'top'
        }]
    });
    intro.start();
}
function tourListaPermiso(){
    var intro = introJs();
    var pasos = new Array();
    pasos.push({
        intro: "En esta sección podrás modificar lo que ven los diferentes tipos de usuarios<br><small>Haz clic en <i>Sig</i> para iniciar el recorrido</small>"
    });
    if($(".verPermisos").length){
        pasos.push({
            intro: "Haz clic aquí para ver o editar el permiso",
            element: ".verPermisos",
            position: 'left'
        });
    }else{
        pasos.push({
            intro: "Haz clic en el ícono <i class='glyphicon glyphicon-eye-open'></i> para ver o editar el permiso"
        });
    }
    if($(".eliminaPermiso").length){
        pasos.push({
            intro: "Haz clic aquí para eliminar un permiso. Esto hará que los usuarios con este permiso pierdan todos sus privilegios",
            element: ".eliminaPermiso",
            position: 'left'
        });
    }else{
        pasos.push({
            intro: "Haz clic en el ícono <i class='glyphicon glyphicon-trash'></i> para eliminar un permiso. Esto hará que los usuarios con ese permiso pierdan todos sus privilegios"
        });
    }
    intro.setOptions({
        steps: pasos
    });
    intro.start();
}
function tourCostoEnvio(){
    var intro = introJs();
    intro.setOptions({
        steps: [{ 
            intro: "Escribe aquí el costo de envío.",
            element: "#costoEnvio"
        }, {
            intro: "Guarda los cambios haciendo clic en este botón",
            element: "#btnCostoEnvio"
        }]
    });
    intro.start();
}
function tourCompraMinima(){
    var intro = introJs();
    intro.setOptions({
        steps: [{ 
            intro: "Escribe aquí la mínima cantidad que tus clientes deben comprar para que el costo de envio sea gratuito.",
            element: "#compraMinima"
        }, {
            intro: "Guarda los cambios haciendo clic en este botón",
            element: "#btnCompraMinima"
        }]
    });
    intro.start();
}
function tourAgregarUsuario(){
    var intro = introJs();
    intro.setOptions({
        steps: [{ 
            intro: "Escribe aquí el nombre completo del usuario a agregar",
            element: "#form_lista_usuario input[name='nombre']",
            position: 'right'
        }, {
            intro: "Escribe aquí un nombre de usuario para su inicio de sesión",
            element: "#form_lista_usuario input[name='name']",
            position: 'right'
        }, {
            intro: "Escribe el email del usuario, se usará para enviarle una contraseña para su inicio de sesión",
            element: "#form_lista_usuario input[name='email']",
            position: 'right'
        }, {
            intro: "Selecciona el permiso que tendrá el usuario",
            element: "#form_lista_usuario select",
            position: 'right'
        }, {
            intro: "Finaliza la creación haciendo clic aquí",
            element: "#form_lista_usuario button[type='submit']",
            position: 'right'
        }]
    });
    intro.start();
}
function tourListaUsuarios(){
    var intro = introJs();
    intro.setOptions({
        steps: [{ 
            intro: "Aquí verás la lista de todos los usuarios dentro del sistema",
            element: "#configuracion_usuarios_lista",
            position: 'left'
        }, {
            intro: "Cambia el permiso de tus usuarios aquí.",
            element: ".cambiarPermisos",
            position: 'left'
        }, {
            intro: "Haz clic en este ícono para ejecutar el restablecimiento de contraseña por mail.",
            element: ".reenviarContrasena",
            position: 'left'
        }, {
            intro: "Haz clic en este ícono para eliminar el usuario",
            element: ".eliminaUsuario",
            position: 'left'
        }]
    });
    intro.start();
}
function tourCalendario(){
    var pasos = new Array();
    pasos.push({
        intro: "El calendario de actividades mostrará las actividades agendadas y las ventas realizadas"
    }, {
        intro: "Usa estos botones para navegar en el calendario",
        element: ".fc-left",
        position: 'right'
    }, {
        intro: "Cambia la vista del calendario aquí",
        element: ".fc-right",
        position: 'left'
    });
    if($(".fc-event-container").length){
        pasos.push({
            intro: "Haz clic en un evento para ver sus detalles",
            element: '.fc-day',
            position: 'top'
        });
    }
    iniciaTour(pasos);
}
function tourAgregarProveedor(){
    var pasos = new Array();
    pasos.push({
        intro: "Los proveedores son usuarios con acceso a las ventas de determinadas categorias"
    }, {
        intro: "Escribe los datos principales de tu proveedor en estos campos",
        element: '#tour_form_agrega_proveedor',
        position: 'top'
    }, {
        intro: "Los proveedores que agregues no serán avisados que están dentro del sistema y les podrás enviar tu acceso desde la lista de proveedores",
        element: "#proveedores_tabla",
        position: 'top'
    });
    iniciaTour(pasos);
}
function tourListaProveedores(){
    var pasos = new Array();
    pasos.push({
        intro: "Los proveedores dados de alta están en esta lista",
        element: "#proveedores_tabla",
        position: 'top'
    });
    if($(".editarProveedor").length){
        pasos.push({
            intro: "Edita los datos personales del proveedor",
            element: ".editarProveedor",
            position: 'top'
        });
    }
    if($(".enviarAccesoProveedor").length){
        pasos.push({
            intro: "Envia el acceso al proveedor. En caso de que el proveedor tenga un acceso anterior, éste quedará inhabilitado y sólo funcionará el nuevo acceso.",
            element: ".enviarAccesoProveedor",
            position: 'top'
        });
    }
    if($(".enlazarProveedor").length){
        pasos.push({
            intro: "Enlaza una categoria (y todos los productos en ella) con éste proveedor. Una categoría puede tener un solo proveedor.",
            element: ".enlazarProveedor",
            position: 'left'
        });
    }
    if($(".eliminaAccesoProveedor").length){
        pasos.push({
            intro: "Elimina el proveedor. Las categorías que tenga enlazadas quedarán sin proveedor.",
            element: ".eliminaAccesoProveedor",
            position: 'left'
        });
    }
    iniciaTour(pasos);
}

function tourAgregarProducto(){
    var pasos = new Array();
    pasos.push({
        intro: "Agrega tus productos llenando este formulario. Los campos con asterisco <strong>*</strong> son obligatorios",
        element: '#form_producto',
        position: 'top'
    }, {
        intro: "La categoría funciona para que el proveedor de este producto esté enterado de cuando tienes pocos productos en tu inventario.",
        element: "#new_producto_categoria",
        position: 'top'
    }, {
        intro: "Esta opción permitirá a los clientes comprar este producto por internet. Si seleccionas <strong>No</strong>, solo lo podrán ver y se les mostrará un mensaje.<br>"+
               "Personaliza el mensaje en la pestaña <strong>Configuración - Mensajes - Productos no disponibles</strong>",
        element: "#new_producto_disponible",
        position: 'top'
    }, {
        intro: "Si agregas el IVA, se le aumentará el 16% al valor que pongas en <strong>Precio</strong>",
        element: '#new_producto_iva',
        position: 'top'
    }, {
        intro: "Si el producto posee información técnica, sube los archivos en formato pdf, doc o xls. Los clientes podrán descargar esta información",
        element: '#new_producto_pdf',
        position: 'top'
    }, {
        intro: "Sube imágenes del producto. Se recomienda entre 1 y 3 imágenes",
        element: '#Rnew_producto_imagen',
        position: 'top',
    }, {
        intro: "Toda la información del producto la podrás cambiar en la pestaña <strong>Lista de productos</strong>",
        element: '#subtab_productos a[href="#productos_lista"]',
        position: 'bottom'
    });
    iniciaTour(pasos);
}

function tourListaProductos(){
    var pasos = new Array();
    pasos.push({
        intro: "Aquí encontrarás una tabla con todos tus productos.",
        element: "#productos_tabla",
        position: 'top'
    });
    if(tablaProductos.rows()[0].length){
        if($(".editaProducto").length){
            pasos.push({
                intro: "Edita la información del producto aquí",
                element: '.editaProducto',
                position: 'left'
            });
        }
        if($(".editaInventario").length){
            pasos.push({
                intro: "Actualiza la información de tu inventario",
                element: '.editaInventario',
                position: 'left'
            });
        }
        if($(".editaOutlet").length){
            pasos.push({
                intro: "El precio Outlet es un precio promocional temporal. Editalo o eliminalo aquí",
                element: '.editaOutlet',
                position: 'left'
            });
        }
        if($(".agregaImagenProducto").length){
            pasos.push({
                intro: "Agrega o elimina las imágenes de tu producto",
                element: '.agregaImagenProducto',
                position: 'left'
            });
        }
        if($(".agregaArchivoProducto").length){
            pasos.push({
                intro: "Agrega o elimina los archivos de tu producto",
                element: '.agregaArchivoProducto',
                position: 'left'
            });
        }
        if($(".eliminaProducto").length){
            pasos.push({
                intro: "Elimina el producto de la lista de productos.",
                element: '.eliminaProducto',
                position: 'left'
            });
        }
    }else{
        pasos.push({
            intro: "Regresa cuando tengas al menos un producto para ver más opciones!"
        });
    }
    iniciaTour(pasos);
}
function tourCarritosAbiertos(){
    var pasos = new Array();
    pasos.push({
        intro: "Los carritos abiertos son como los carritos de compra de un súper: pueden o no contener productos y pertenecen a un posible cliente."
    }, {
        intro: "Aquí estará la lista de los carritos abiertos",
        element: "#carritos_tabla_listaA",
        position: 'top'
    });
    if($("#carritos_tabla_listaA .ver_lista_modal_carrito").length){
        pasos.push({
            intro: "Edita el contenido del carrito. Aquí también debes cerrar los carritos cuando inicie el proceso de pago",
            element: "#carritos_tabla_listaA .ver_lista_modal_carrito",
            position: 'left'
        });
    }
    if($("#carritos_tabla_listaA .borrarCarrito").length){
        pasos.push({
            intro: "Elimina el carrito y su contenido",
            element: "#carritos_tabla_listaA .borrarCarrito",
            position: 'left'
        });
    }
    iniciaTour(pasos);
}
function tourCarritosCerrados(){
    var pasos = new Array();
    pasos.push({
        intro: "Los carritos cerrados son como los carritos de compra de un súper formados: están formados y listos para pagar."
    }, {
        intro: "Aquí estará la lista de los carritos cerrados",
        element: "#carritos_tabla_listaC",
        position: 'top'
    });
    if($("#carritos_tabla_listaC .ver_lista_modal_carrito").length){
        pasos.push({
            intro: "Edita el contenido del carrito. Aquí también debes cerrar los carritos cuando inicie el proceso de pago",
            element: "#carritos_tabla_listaC .ver_lista_modal_carrito",
            position: 'left'
        });
    }
    if($("#carritos_tabla_listaC .glyphicon-print").length){
        pasos.push({
            intro: "Imprime el ticket de compra",
            element: "#carritos_tabla_listaC button[onclick='"+$("#carritos_tabla_listaC .glyphicon-print").eq(0).parent().attr("onclick")+"']",
            position: 'left'
        });
    }
    if($("#carritos_tabla_listaC .finalizaCarrito").length){
        pasos.push({
            intro: "Finaliza los carritos cerrados cuando el cliente tenga sus productos",
            element: "#carritos_tabla_listaC .finalizaCarrito",
            position: 'left'
        });
    }
    iniciaTour(pasos);
}
function tourClientes(){
    var pasos = new Array();
    pasos.push({
        intro: "Esta es tu lista de clientes",
        element: "#clientes_tabla",
        position: 'top'
    });
    if($("#clientes_tabla .editaCliente").length){
        pasos.push({
            intro: "Edita el cliente.",
            element: "#clientes_tabla .editaCliente",
            position: 'left'
        });
    }
    if($("#clientes_tabla .creaLlamada").length){
        pasos.push({
            intro: "Registra una llamada con el cliente. Esta opción solo está habilitada para clientes con teléfono registrado. Puedes registrar teléfonos desde <strong>Edita cliente</strong>",
            element: "#clientes_tabla .creaLlamada",
            position: 'left'
        });
    }
    if($("#clientes_tabla .eliminaCliente").length){
        pasos.push({
            intro: "Elimina el cliente.",
            element: "#clientes_tabla .eliminaCliente",
            position: 'left'
        });
    }
    iniciaTour(pasos);
}