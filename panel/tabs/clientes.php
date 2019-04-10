<?php
include("../../admin/classes/usuarios.class.php");
$usu = new Usuarios("registro.php");
$muni = $usu->dameMunicipios();
?>
<div class="container">
    <div class="page-header">
        <h1>Clientes</h1>
    </div>
    <div>
        <ul class="nav nav-tabs" role="tablist" id="subtab_cliente">
            <li role="presentation">
                <a href="#clientes_agrega" role="tab" data-toggle="tab">Agrega cliente</a>
            </li>
            <li role="presentation" class="active">
                <a href="#clientes_lista" role="tab" data-toggle="tab">Lista de clientes</a>
            </li>
            <li role="presentation">
                <a href="#clientes_registrados" role="tab" data-toggle="tab">Lista de registrados</a>
            </li>
            <li role="presentation">
                <a class="hidden" href="#clientes_detalles" role="tab" data-toggle="tab"></a>
            </li>
        </ul>
        <div class="tab-content margintop20">
            <div role="tabpanel" class="tab-pane" id="clientes_agrega">
                <div class="alert alert-info">
                    Agrega aquí a tus clientes. 
                </div>
                <div class="row container">
                    <form class="form-horizontal" id="form_cliente_nuevo">
                        <div class="col-md-10 col-md-offset-1"><big><strong>Datos generales</strong></big><br><br></div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label" for="new_nombre">* Nombre:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nombre" id="new_nombre" placeholder="Nombre completo">
                                <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                                <span class="help-block">Este campo es obligatorio</span>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label" for="new_email">Email:</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" id="new_email" placeholder="usuario@dominio.com">
                                <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                                <span class="help-block">Email inválido</span>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label" for="new_acdescuento">Descuento:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="new_acdescuento" name="descuento"></select>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label">Categoría:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="categoria" placeholder="Categoría">
                            </div>
                            <label class="col-sm-2 control-label">Tipo de tienda:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="tipoTienda">
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label">Número de tiendas:</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="noTienda">
                            </div>
                            <label class="col-sm-2 control-label">Expo:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="expo">
                            </div>
                        </div>
                        <div id="clientes_new_telefonos">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Teléfono:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="label[]" id="new_labeltel1" placeholder="Etiqueta teléfono (ejemplo: Oficina)">
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></div>
                                        <input type="text" class="form-control telefono" name="telefono[]" id="new_tel1" placeholder="Teléfono">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="new_notas">Notas:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="notas" id="new_notas" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1"><hr><big><strong>Datos de envío</strong></big><br><br></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nombre de quien recibe</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="envioNombre">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="new_calle">Calle y número:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="new_calle" name="calle">
                            </div>
                            <label class="col-sm-2 control-label" for="new_entrecalle">Entre calles:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="new_entrecalle" name="entrecalle">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="new_colonia">Colonia:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="new_colonia" name="colonia">
                            </div>
                            <label class="col-sm-2 control-label" for="new_ciudad">Ciudad:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="new_ciudad" name="ciudad">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="new_cp">Código postal:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="new_cp" name="cp">
                            </div>
                            <label class="col-sm-2 control-label" for="new_estado">Estado:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="new_estado" name="estado">
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1"><hr><big><strong>Datos de facturación</strong></big><br><br></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tipo de Documento:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="tipoDoc">
                            </div>
                            <label class="col-sm-2 control-label">RFC:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="razSoc">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Cuenta Bancaria:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="cb">
                            </div>
                            <label class="col-sm-2 control-label">Razón Social:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="rfc">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Correo Electrónico:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="correoFac">
                            </div>
                            <label class="col-sm-2 control-label">Forma de pago:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="formaPago">
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1"><hr><big><strong>Seguimiento</strong></big><br><br></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Última Fecha de Contacto:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="fechaContacto">
                            </div>
                            <label class="col-sm-2 control-label">Tareas:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="tareas">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <input type="submit" class="btn btn-primary" value="Agrega cliente">
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <div class="alert alert-info">
                    En esta sección puedes importar varios clientes. Descarga el esqueleto, llenalo de valores reales y súbelo.
                </div>
                <form enctype="multipart/form-data" id="form_importar_xls">
                    <div class="row">
                        <div class="col-md-4 lead">
                            Importa .xls<br>
                            <small><small><a href="xls/esqueleto_clientes.xls" download>Descarga esqueleto</a></small></small>
                        </div>
                        <div class="col-md-4"><input type="file" class="form-control" name="importa_cli[]"></div>
                        <div class="col-md-4"><input type="submit" class="form-control btn btn-primary primary"></div>
                    </div>
                </form>
            </div>
            <div role="tabpanel" class="tab-pane active" id="clientes_lista">
                <h3>Lista de clientes
                    <small onclick="tourClientes();" role="button"><i class="glyphicon glyphicon-question-sign"></i></small>
                </h3>
                <table id="clientes_tabla" class="table table-striped table-hover has-feedback" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Categoría</th>
                            <th>Tipo de tienda</th>
                            <th>Notas</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="clientes_registrados">
                <h3>Lista de registrados
                </h3>
                <table id="registrados_tabla" class="table table-striped table-hover has-feedback" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Edad</th>
                            <th>Email</th>
                            <th>Escuela</th>
                            <th>Grado</th>
                            <th>Tutor</th>
                            <th>Emailtutor</th>
                            <th>Subsistema</th>
                            <th>Municipio</th>
                            <th>Enterado</th>
                            <th>Año</th>
                            <th>Puntaje</th>
                            <th>Hora</th>
                            <th>Salon</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade" id="modalEditaCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title">
                    Datos completos del cliente<br>
                    <small id="tituloModalNombreCliente">Fulano</small>
                </h2>
            </div>
            <div class="modal-body project-info">
                <div id="mensajesEditaCliente"></div>
                <div class="row">
                    <form class="form-horizontal" id="form_cliente_edit">
                        <input type="hidden" name="idCliente" id="cliente_edit_id">
                        <div class="col-md-10 col-md-offset-1"><big><strong>Datos generales</strong></big><br><br></div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label" for="editmodal_nombre">* Nombre:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nombre" id="editmodal_nombre" placeholder="Nombre completo">
                                <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                                <span class="help-block">Este campo es obligatorio</span>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label" for="editmodal_email">* Email:</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" id="editmodal_email" placeholder="usuario@dominio.com">
                                <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                                <span class="help-block">Email inválido</span>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label" for="edit_acdescuento">Descuento:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="edit_acdescuento" name="descuento"></select>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label">Categoría:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="categoria" placeholder="Categoría">
                            </div>
                            <label class="col-sm-2 control-label">Tipo de tienda:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="tipoTienda">
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label">Número de tiendas:</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="noTienda">
                            </div>
                            <label class="col-sm-2 control-label">Expo:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="expo">
                            </div>
                        </div>
                        <div id="clientes_editmodal_telefonos">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="edit_labeltel1">Teléfono:</label>
                                <div class="col-sm-4">
                                    <input type="hidden" name="idTel[]" id="edit_idTelFirst" value="">
                                    <input type="text" class="form-control" name="label[]" id="edit_labeltel1" placeholder="Etiqueta teléfono">
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></div>
                                        <input type="text" class="form-control telefonoEdit" padre="#clientes_editmodal_telefonos" name="telefono[]" id="editmodal_tel1" placeholder="Teléfono">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="editmodal_notas">Notas:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="notas" id="editmodal_notas" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1"><hr><big><strong>Datos de envío</strong></big><br><br></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nombre de quien recibe</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="envioNombre">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="editmodal_calle">Calle y número:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="editmodal_calle" name="calle">
                            </div>
                            <label class="col-sm-2 control-label" for="editmodal_entrecalle">Entre calles:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="editmodal_entrecalle" name="entrecalle">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="editmodal_colonia">Colonia:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="editmodal_colonia" name="colonia">
                            </div>
                            <label class="col-sm-2 control-label" for="editmodal_ciudad">Ciudad:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="editmodal_ciudad" name="ciudad">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="editmodal_cp">Código postal:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="editmodal_cp" name="cp">
                            </div>
                            <label class="col-sm-2 control-label" for="editmodal_estado">Estado:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="editmodal_estado" name="estado">
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1"><hr><big><strong>Datos de facturación</strong></big><br><br></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tipo de Documento:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="tipoDoc">
                            </div>
                            <label class="col-sm-2 control-label">RFC:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="razSoc">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Cuenta Bancaria:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="cb">
                            </div>
                            <label class="col-sm-2 control-label">Razón Social:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="rfc">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Correo Electrónico:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="correoFac">
                            </div>
                            <label class="col-sm-2 control-label">Forma de pago:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="formaPago">
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1"><hr><big><strong>Seguimiento</strong></big><br><br></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Última Fecha de Contacto:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="fechaContacto">
                            </div>
                            <label class="col-sm-2 control-label">Tareas:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="tareas">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <input type="submit" class="btn btn-primary" value="Guardar cambios">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditaCregistrado" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title">
                    Editar registrado
                </h2>
            </div>
            <div class="modal-body project-info">
                <div id="mensajesEditaCregistrado"></div>
                <div class="row">
                    <style>
                        
                        #form_cregistrado_edit .form-group{ margin: 0px; }
                    </style>
                    <form class="form-horizontal" id="form_cregistrado_edit">
                        <input type="hidden" name="id" id="cregistrado_edit_id">
                        <div class="row">
                            <div class="form-group col-sm-6 wow fadeInRight">
                                <label for="nombre" class="control-label">* Nombre(s):</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre(s)" maxlength="100" required>
                            </div>
                            <div class="form-group col-sm-6 wow fadeInRight" data-wow-delay="100ms">
                                <label for="apellido" class="control-label">* Apellidos:</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" maxlength="100" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6 wow fadeInRight" data-wow-delay="150ms">
                                <label for="email" class="control-label">* Email:</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" maxlength="150" autocomplete="off" required>
                            </div>
                            <div class="form-group col-sm-6 wow fadeInRight" data-wow-delay="200ms">
                                <label for="edad" class="control-label">* Edad:</label>
                                <input type="number" class="form-control" id="edad" name="edad" min="3" max="20" placeholder="Edad" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6 wow fadeInRight" data-wow-delay="250ms">
                                <label for="grado" class="control-label">* Grado escolar:</label>
                                <select id="grado" class="form-control" name="grado" required>                
                                    <option value="">Selecciona una opción</option>
                                    <optgroup label="Primaria">
                                        <option value="1ro Primaria">Primer año</option>
                                        <option value="2do Primaria">Segundo año</option>
                                        <option value="3ro Primaria">Tercer año</option>
                                        <option value="4to Primaria">Cuarto año</option>
                                        <option value="5to Primaria">Quinto año</option>
                                        <option value="6to Primaria">Sexto año</option>
                                    </optgroup>
                                    <optgroup label="Secundaria">
                                        <option value="1ro Secundaria">Primer año o equivalente</option>
                                        <option value="2do Secundaria">Segundo año o equivalente</option>
                                        <option value="3ro Secundaria">Tercer año o equivalente</option>
                                    </optgroup>
                                    <optgroup label="Preparatoria">
                                        <option value="1ro Preparatoria">Primer año o equivalente</option>
                                        <option value="2do Preparatoria">Segundo año o equivalente</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group col-sm-6 wow fadeInRight" data-wow-delay="300ms">
                                <label for="municipio" class="control-label">* Municipio:</label>
                                <select id="municipio" class="form-control select2generico" name="municipio" required>
                                    <option value="">Selecciona el municipio de tu escuela</option>
                                    <?php
                                    foreach($muni as $val){
                                        ?>
                                    <option value="<?=$val['id'];?>"><?=$val['nombre'];?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6  fadeInRight">
                                <label for="subsis" class="control-label">* Subsistema de tu escuela:</label>
                                <select id="subsis" class="form-control select2generico" name="subsis" required>
                                    <option value="priPart">Primaria particular</option>
                                    <option value="priPubl">Primaria pública</option>
                                    <option value="secPart">Secundaria particular</option>
                                    <option value="secPubl">Secundaria pública</option>
                                    <option value="DGETI">Dirección General de Educación Tecnológica Industrial (DGETI)</option>
                                    <option value="DGETA">Dirección General de Educación Tecnológica Agropecuaria (DGETA)</option>
                                    <option value="CONALEP">Colegio Nacional de Educación Profesional Técnica (CONALEP)</option>
                                    <option value="CECyTE">Colegio de Estudios Científicos y Tecnologicos (CECyTE)</option>
                                    <option value="COBAEM">Colegio de Bachilleres (COBAEM)</option>
                                    <option value="UAEM">Universidad Autónoma del Estado de Morelos</option>
                                    <option value="Particular">Escuela particular</option>
                                    <option value="Otro">Otro o desconocido</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6  fadeInRight" data-wow-delay="50ms">
                                <label for="escuela" class="control-label">* Nombre de la escuela (o número de plantel):</label>
                                <input type="text" id="escuela" class="form-control" name="escuela" placeholder="Escuela" maxlength="150" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6  fadeInRight" data-wow-delay="100ms">
                                <label for="tutor" class="control-label">Nombre de tu maestro o tutor (opcional):</label>
                                <input type="text" class="form-control" id="tutor" name="tutor" placeholder="Opcional" maxlength="100">
                            </div>
                            <div class="form-group col-sm-6  fadeInRight" data-wow-delay="150ms">
                                <label for="emailtutor" class="control-label">Email de tu maestro o tutor (opcional):</label>
                                <input type="text" class="form-control" id="emailtutor" name="emailtutor" maxlength="100" placeholder="Opcional">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-6 col-xs-offset-6  fadeInRight" data-wow-delay="300ms">
                                <input type="submit" class="btn btn-success form-control" value="Guarda cambios">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_historial" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Conversación - <span id="nombreHistorial"></span></h4>
            </div>
            <div class="modal-body has-feedback">
                <div class="" id="mensajesHistorial"></div>
                <br>
            </div>
        </div>
    </div>
</div>