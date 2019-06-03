//dentro de este tipo de funciones se pueden definir variables y otras funciones
var base = function () {
    var internacionalizar = function () {
        var validator_message;
        switch (_locale){
            case 'es':
                validator_message={
                    "required": "Este campo es obligatorio.",
                    "remote": "Por favor arregla este campo.",
                    "email": "Por favor, introduce una dirección de correo electrónico válida.",
                    "url": "Por favor introduzca un URL válido.",
                    "date": "Por favor introduzca una fecha válida.",
                    "dateISO": "Por favor introduzca una fecha válida (ISO).",
                    "number": "por favor ingrese un número válido.",
                    "digits": "Por favor ingrese solo dígitos.",
                    "creditcard": "Por favor, introduzca un número de tarjeta de crédito válida.",
                    "equalTo": "Ingresa el mismo valor.",
                    "accept": "Por favor ingrese un valor con una extensión válida.",
                    "checkTime": "Por favor comprueba la hora.",
                    "compareDate": "La fecha de inicio debe ser inferior a la de fin.",

                };
                break;
            case 'fr':
                validator_message={
                    "required": "Ce champ est obligatoire.",
                    "remote": "Please fix this field.",
                    "email": "S'il vous plaît, mettez une adresse email valide.",
                    "url": "Veuillez entrer une URL valide.",
                    "date": "veuillez entrer une date valide.",
                    "dateISO": "Veuillez entrer une date valide (ISO).",
                    "number": "S'il vous plait, entrez un nombre valide.",
                    "digits": "Merci de n'entrer que des chiffres.",
                    "creditcard": "Veuillez entrer un numéro de carte de crédit valide.",
                    "equalTo": "Entrez la même valeur.",
                    "accept": "Veuillez entrer une valeur avec une extension valide.",
                    "checkTime": "Vérifier I´heure.",
                    "compareDate": "Date d´initie débit etre inferieur á la date de fin",
                };
                break;
                default:
                    validator_message={
                        "required": "This field is required.",
                        "remote": "Veuillez corriger ce champ.",
                        "email": "Please enter a valid email address.",
                        "url": "Please enter a valid URL.",
                        "date": "Please enter a valid date.",
                        "dateISO": "Please enter a valid date (ISO).",
                        "number": "Please enter a valid number.",
                        "digits": "Please enter only digits.",
                        "creditcard": "Please enter a valid credit card number.",
                        "equalTo": "Ingresa el mismo valor.",
                        "accept": "Please enter a value with a valid extension.",
                        "checkTime": "Please check the time.",
                        "compareDate": "Start date must be lower than End date.",
                    };
                break;
        }
        $.extend(jQuery.validator.messages,validator_message)
    }

    return {
        init:function(){
            internacionalizar();
        },
        Error: function () {
            bootbox.confirm({
                title: window.errormessaje_title,
                message: "<h5><i class='fa fa-danger fa-2x color-red'></i>"+errormessaje_title+"</h5>",
                buttons: {
                    confirm: {
                        label: 'Refrescar',
                        className: 'btn blue'
                    },
                },
                callback: function (result) {
                    if (result == true)
                        document.refresh();
                }
            });
          //  createjs.Sound.play("sound");
        },
    };
}();