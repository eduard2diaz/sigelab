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

    /*Inicio de gestion de publicaciones*/
    var cargarNotificaciones = function () {
        $.ajax({
            url: Routing.generate('notificacion_index.' + _locale, {'_format': 'json'}),
            type: "GET",
            success: function (data) {
                if (data['contador'] > 0)
                    $('span#notificacion_contador').append("<span class='m-nav__link-badge m-badge m-badge--danger'>" + data['contador'] + "</span>");
                $('div#notificacion_content').html(data['html']);

            },
            error: function () {
                //       base.Error();
            }
        });
    }
    var verNotificacion = function () {
        $('table#notificacion_table, body').on('click', 'a.notificacion_show', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: window.loadingMessage});
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
                    mApp.unblock("body")
                }
            });
        });
    }
    /*Fin de gestion de publicaciones*/

    /*Inicio de gestion de mensajes*/
    var cargarMensajes = function () {
        var link = Routing.generate('mensaje_recent.' + _locale);
        $.ajax({
            type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
            url: link,
            beforeSend: function (data) {
                // base.blockUI({message: 'Cargando'});
            },
            success: function (data) {
                if (data['contador'] > 0)
                    $('span#mensaje_contador').append("<span class='m-nav__link-badge m-badge m-badge--danger'>" + data['contador'] + "</span>");
                $('div#mensaje_content').html(data['html']);
            },
            error: function () {
              //  base.Error();
            },
            complete: function () {
            }
        });
    }
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
                        {overlayColor: "#000000", type: "loader", state: "success", message: window.loadingMessage});
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
                        configurarFormularioMensaje();
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);
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
                        {overlayColor: "#000000", type: "loader", state: "success", message: window.loadingMessage});
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
    /*Fin de gestion de mensajes*/

    //INTERFAZ DE VISUALIZACION DE DETALLES DEL USUARIO
    var usuarioProfile = function () {
        $('body').on('click', 'a.usuario_show', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: window.loadingMessage});
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
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: window.loadingMessage});
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
                    mApp.unblock("body");
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
                data: new FormData(this), //para enviar el formulario hay que serializarlo
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function () {
                },
                complete: function () {
                    l.stop();
                },
                success: function (data) {
                    if (data['error']) {
                        padre.html(data['form']);
                        configurarFormularioUsuario();
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);
                        if ($('table#table_usuario').length>0) {
                            var pagina = table.page();
                            obj.parents('tr').children('td:nth-child(2)').html(data['nombre']);
                            obj.parents('tr').children('td:nth-child(3)').html(data['apellido']);
                            obj.parents('tr').children('td:nth-child(4)').html(data['usuario']);
                            obj.parents('tr').children('td:nth-child(5)').html("<span class='m-badge m-badge--wide m--font-boldest m-badge--" + data['badge_color'] + "'>" + data['badge_texto'] + "</span>");
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

    var pcDetails = function () {
        $('body').on('click', 'a.m-list-search__result-item', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: window.loadingMessage});
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

    var gestionarFoto = function () {
        $('body').on('click', '#foto_perfil', function () {
            mApp.block("body", {overlayColor: "#000000", type: "loader", state: "success", message: "Explorando archivos ..."});
            $('#usuario_file').click();
            mApp.unblock("body");
            document.getElementById('usuario_file').addEventListener('change', previewfile, false);

            $('body').on('change','input#usuario_file',function(){
                var fileName = document.getElementById("usuario_file").files[0].name;
                var fileSize = document.getElementById("usuario_file").files[0].size;
                var maxSize=2*1024*1024;
                if(fileSize>maxSize){
                    toastr.error('El archivo seleccionado excede el tamaño permitido (2MB)');
                    $('input#usuario_file').val('');
                }else
                    $('span.custom-file-control').addClass("selected").html(fileName);
            })
        });
    }

    var reiniciarFoto = function () {
        $('body').on('click', 'a#reload_picture', function () {
            $('img#foto_perfil').attr('src', $('#foto_perfil').attr('data-image'));
            $('#usuario_file').val('');
            $('a#reload_picture').addClass('m--hidden-desktop');
        });
    }

    function previewfile(evt) {
        var files = evt.target.files; // FileList object

        // Obtenemos la imagen del campo "file".
        for (var i = 0, f; f = files[i]; i++) {
            //Solo admitimos imágenes.
            if (!f.type.match('image.*')) {
                continue;
            }
            var reader = new FileReader();
            reader.onload = (function (theFile) {
                return function (e) {
                    // Insertamos la imagen
                    $('#foto_perfil').attr('src', e.target.result);
                };
            })(f);
            reader.readAsDataURL(f);
            $('a#reload_picture').removeClass('m--hidden-desktop');
        }
    }

    return {
        init: function () {
            $().ready(function () {
                cargarNotificaciones();
                verNotificacion();
                cargarMensajes();
                enviarMensaje();
                enviarMensajeAction();
                mensajeShow();
                //Gestion de usuarios
                usuarioProfile();
                edicionCurrentUser();
                edicionCurrentUserAction();
                gestionarFoto();
                reiniciarFoto();
                //Detalles de la pc
                pcDetails();
            });
        },
    };
}();