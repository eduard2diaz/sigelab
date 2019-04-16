//CONFIGURACION DE LOS CAMPOS DEL FORMULARIO DE USUARIO
var configurarFormularioUsuario = function () {
    $('select#usuario_idrol').select2({
        dropdownParent: $("#basicmodal"),
        //allowClear: true
    });
    $('select#usuario_facultad').select2({
        dropdownParent: $("#basicmodal"),
        //allowClear: true
    });
    $('input#usuario_activo').bootstrapSwitch();
    Ladda.bind('.mt-ladda-btn');
}

//VALIDACION DE LOS CAMPOS DE EDICION DE USUARIOS
function validarEditUser() {
    $("div#basicmodal form#usuario_edit").validate({
        rules: {
            'usuario[nombre]': {required: true},
            'usuario[apellido]': {required: true},
            'usuario[facultad]': {required: true},
            'usuario[correo]': {required: true, email: true},
            'usuario[idrol][]': {required: true},
            'usuario[password][second]': {
                equalTo: "#usuario_password_first"
            }
        }
    });
}

//CONFIGURACION DE LOS CAMPOS DEL FORMULARIO DE MENSAJES
var configurarFormularioMensaje = function () {
    $('select#mensaje_iddestinatario').select2({
        dropdownParent: $("#basicmodal"),
        ajax: {
            url: Routing.generate('usuario_ajax.' + _locale),
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });
}

function validarMensaje() {
    $("div#basicmodal form#message_new").validate({
        rules: {
            'mensaje[iddestinatario][]': {required: true},
            'mensaje[descripcion]': {required: true},
        }
    });
}

//dentro de este tipo de funciones se pueden definir variables y otras funciones
var authenticated = function () {
    //INTERFAZ DE VISUALIZACION DE DETALLES DEL USUARIO
    var usuarioProfile = function () {
        $('body').on('click', 'a.ajax_show', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    base.blockUI({message: 'Cargando'});
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        $('div#basicmodal').modal('show');
                    }
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                    base.unblockUI();
                }
            });
        });
    }
    //EVENTO DE ESCUCHA DE EDICION DE USUARIO
    var edicionCurrentUser = function () {
        $('div#basicmodal').on('click', 'a.editar_usuario', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    base.blockUI({message: 'Cargando'});
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        configurarFormularioUsuario();
                        $('div#basicmodal').modal('show');
                        validarEditUser();
                    }
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                    base.unblockUI();
                }
            });
        });
    }
    //PROCESAMIENTO DEL FORMULARIO DE EDICION DE USUARIOS
    var edicionCurrentUserAction = function () {
        $('div#basicmodal').on('submit', 'form#usuario_edit', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(), //para enviar el formulario hay que serializarlo
                beforeSend: function () {
                    //  base.blockUI({message: 'Cargando'});
                },
                complete: function () {
                    l.stop();
                    //     base.unblockUI();
                },
                success: function (data) {
                    if (data['error']) {
                        padre.html(data['form']);
                        configurarFormularioUsuario();
                    } else {
                        if (data['mensaje'])
                            base.enviarMensaje(null, null, data['mensaje'])
                        if ($('table#table_usuario')) {
                            var pagina = table.page();
                            obj.parents('tr').children('td:nth-child(2)').html(data['nombre']);
                            obj.parents('tr').children('td:nth-child(3)').html(data['apellido']);
                            obj.parents('tr').children('td:nth-child(4)').html(data['usuario']);
                            obj.parents('tr').children('td:nth-child(5)').html("<span class='badge badge-" + data['badge_color'] + "'>" + data['badge_texto'] + "</span>");
                        }
                        $('div#basicmodal').modal('hide');
                    }
                },
                error: function () {
                    base.Error();
                }
            });
        });
    }

    //CARGADO DE NUEVOS MENSAJES A TRAVES DE AJAX
    var cargarMensajes = function () {
        var link = Routing.generate('mensaje_recent.' + _locale);
        $.ajax({
            type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
            url: link,
            beforeSend: function (data) {
                // base.blockUI({message: 'Cargando'});
            },
            success: function (data) {
                if(data['contador']>0)
                    $('span#mensaje_contador').append("<span class='m-nav__link-badge m-badge m-badge--danger'>"+data['contador']+"</span>");
                $('div#mensaje_content').html(data['html']);
            },
            error: function () {
                base.Error();
            },
            complete: function () {
            }
        });
    }

    //ESCUCHA DE EVENTO DE ENVIO DE MENSAJES
    var enviarMensaje = function () {
        $('body').on('click', 'a.enviarmensaje', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message: window.loadingMessage});
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        configurarFormularioMensaje();
                        $('div#basicmodal').modal('show');
                        validarMensaje();
                    }
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                    mApp.unblock("body");
                }
            });
        });
    }
    //PROCESAMIENTO DEL FORMULARIO DE ENVIO DE MENSAJES
    var enviarMensajeAction = function () {
        $('div#basicmodal').on('submit', 'form#message_new', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(), //para enviar el formulario hay que serializarlo
                beforeSend: function () {
                    //  base.blockUI({message: 'Cargando'});
                },
                complete: function () {
                    l.stop();
                    //     base.unblockUI();
                },
                success: function (data) {
                    if (data['error']) {
                        padre.html(data['form']);
                        configurarFormularioNotificacion();
                    } else {
                       /* if (data['mensaje'])
                            base.enviarMensaje(null, null, data['mensaje'])*/
                        $('div#basicmodal').modal('hide');
                    }
                },
                error: function () {
                    base.Error();
                }
            });
        });
    }

    var mensajeShow = function () {
        $('body').on('click', 'a.mensaje_show', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message: window.loadingMessage});
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        $('div#basicmodal').modal('show');
                    }
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                    mApp.unblock("body");
                }
            });
        });
    }

    return {
        init: function () {
            $().ready(function () {
                usuarioProfile();
                edicionCurrentUser();
                edicionCurrentUserAction();
                cargarMensajes();
                enviarMensaje();
                enviarMensajeAction();
                mensajeShow();
            });
        },
    };
}();