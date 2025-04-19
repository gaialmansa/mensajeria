/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */


var zfx;
if (!zfx) zfx = {};
zfx.Zaf = {};
zfx.Zaf.data = {};


/**
 * Elegir una pestaña
 * @param tab Se puede pasar el id de la pestaña (corresponde a data-tab="id") o el objeto jquery de su botón
 */
zfx.Zaf.selectTab = function (tab) {
    let obj;
    if (typeof tab == 'string') {
        obj = $(".zjTabButton[data-tab='" + tab + "']");
    } else {
        obj = tab;
        tab = obj.attr('data-tab');
    }
    let desk = obj.closest('.zjDesktop');
    desk.find('.zjTabPanel').removeClass('zjTabSelected');
    desk.find('.zjTabButton').removeClass('zjTabSelected');
    $('#_tab_' + tab).addClass('zjTabSelected');
    obj.addClass('zjTabSelected');

    // Si hay algún mapa, que es incapaz de dibujarse a sí mismo estando oculto,
    // lo refrescamos
    $('#_tab_' + tab + " .zjMap").each(function () {
        // Refrescamos el mapa
        var idMap = $(this).attr("id");
        zfx.Map[idMap].map.invalidateSize();
        zfx.Map[idMap].reloadLayers();
    });
}

// Si se devuelve un único item en el fast-inserter se preelige su ID.
zfx.Zaf.setupFITarget = function (target) {
    let items = target.find('.zafFastInserterResult');
    if (items.length == 1) {
        target.closest('.zafFastInserter').find('input[name="id"]').val(items[0].getAttribute('data-adm-fi-id'));
    }
}


// Gestión de interdepencias entre campos
zfx.Zaf.formInterDeps = function (formID, config) {
    var formSelector = '#' + formID;
    for (var key in config) {
        var type = config[key]['type'];
        switch (type) {
            case 'select-one-from-group': {
                $(document).on('change', formSelector + ' ' + 'select[name="' + key + '"]', config[key], function (e) {
                    let valor;
                    if (e.data.extra == 1) {
                        valor = $(this).find("option:selected").attr('data-extra');
                    } else {
                        valor = $(this).val();
                    }
                    if (valor == undefined) return;
                    var slavekey;
                    // Si el select tiene el valor nulo, hacemos lo que marque "onnull"
                    if (valor == '') {
                        if (e.data.onnull == 'show-all') {
                            for (slavekey in e.data.slaves) {
                                zfx.Zaf.formShowElement(formSelector, e.data.slaves[slavekey]);
                                if (e.data.req == 1) zfx.Zaf.formReqElement(formSelector, e.data.slaves[slavekey]);
                            }
                        } else if (e.data.onnull == 'hide-all') {
                            for (slavekey in e.data.slaves) {
                                zfx.Zaf.formHideElement(formSelector, e.data.slaves[slavekey]);
                                if (e.data.req == 1) zfx.Zaf.formNoReqElement(formSelector, e.data.slaves[slavekey]);
                            }
                        }
                    }
                    // Si el select no tiene un valor nulo, mostramos el que marque su valor y ocultamos el resto
                    else {
                        for (slavekey in e.data.slaves) {
                            if (slavekey == valor) {
                                zfx.Zaf.formShowElement(formSelector, e.data.slaves[slavekey]);
                                if (e.data.req == 1) zfx.Zaf.formReqElement(formSelector, e.data.slaves[slavekey]);
                            } else {
                                zfx.Zaf.formHideElement(formSelector, e.data.slaves[slavekey]);
                                if (e.data.req == 1) zfx.Zaf.formNoReqElement(formSelector, e.data.slaves[slavekey]);
                            }
                        }
                    }
                })
                $(formSelector + ' ' + 'select[name="' + key + '"]').trigger('change');
                break;
            }
            case 'select-subset-from-group': {
                $(document).on('change', formSelector + ' ' + 'select[name="' + key + '"]', config[key], function (e) {
                    let valor;
                    if (e.data.extra == 1) {
                        valor = $(this).find("option:selected").attr('data-extra');
                    } else {
                        valor = $(this).val();
                    }
                    if (valor == undefined) return;
                    let slavekey;
                    // Si el select tiene el valor nulo (es cadena vacía), hacemos lo que marque "onnull"
                    if (valor == '') {
                        if (e.data.onnull == 'show-all') {
                            for (slavekey in e.data.slaves) {
                                for (let element of e.data.slaves[slavekey]) {
                                    zfx.Zaf.formShowElement(formSelector, element);
                                    if (e.data.req == 1) zfx.Zaf.formReqElement(formSelector, element);
                                }
                            }
                        } else if (e.data.onnull == 'hide-all') {
                            for (slavekey in e.data.slaves) {
                                for (let element of e.data.slaves[slavekey]) {
                                    zfx.Zaf.formHideElement(formSelector, element);
                                    if (e.data.req == 1) zfx.Zaf.formNoReqElement(formSelector, element);
                                }
                            }
                        }
                    }
                    // Si el select no tiene un valor nulo, mostramos el que marque su valor y ocultamos el resto
                    else {
                        for (slavekey in e.data.slaves) {
                            if (slavekey != valor) {
                                for (let element of e.data.slaves[slavekey]) {
                                    zfx.Zaf.formHideElement(formSelector, element);
                                    if (e.data.req == 1) zfx.Zaf.formNoReqElement(formSelector, element);
                                }
                            }
                        }
                        for (slavekey in e.data.slaves) {
                            if (slavekey == valor) {
                                for (let element of e.data.slaves[slavekey]) {
                                    zfx.Zaf.formShowElement(formSelector, element);
                                    if (e.data.req == 1) zfx.Zaf.formReqElement(formSelector, element);
                                }
                            }
                        }
                    }
                });
                $(formSelector + ' ' + 'select[name="' + key + '"]').trigger('change');
                break;
            }
            case 'change-set-js': {
                $(document).on('input change', formSelector + ' ' + '[name="' + key + '"]', config[key], function (e) {
                    // El valor que se le pasará a las funciones
                    let valor;
                    if (e.data.extra == 1) {
                        valor = $(this).find("option:selected").attr('data-extra');
                    } else {
                        valor = $(this).val();
                    }
                    if (valor == undefined) return;
                    // Si tenemos otros valores, vamos a construir un objeto con ello.
                    let p = {};
                    if (e.data.othervalues) {
                        let ovkey;
                        for (ovkey in e.data.othervalues) {
                            p[ovkey] = $('[name="' + e.data.othervalues[ovkey] + '"]').val();
                        }
                    } else p = undefined;
                    // Ahora ejecutamos los slaves
                    let slavekey;
                    for (slavekey in e.data.slaves) {
                        let nombrefuncion = e.data.slaves[slavekey];
                        if (typeof window[nombrefuncion] === "function") {
                            let result = window[nombrefuncion](valor, p, $('[name="' + slavekey + '"]'));
                            if (result != null) {
                                $('[name="' + slavekey + '"]').val(result);
                            }
                        }
                    }
                });
                $(formSelector + ' ' + '[name="' + key + '"]').trigger('change');
                break;
            }
            case 'any-change-set-js': {
                let selector = '';
                let uniqselector = '';
                let ovk;
                for (ovk in config[key].othervalues) {
                    selector += '[name="' + config[key].othervalues[ovk] + '"],';
                    uniqselector = '[name="' + config[key].othervalues[ovk] + '"],'; // será el último
                }
                selector = selector.slice(0, -1);
                uniqselector = uniqselector.slice(0, -1);
                $(document).on('input change', selector, config[key], function (e) {
                    // Construir un objeto con los valores
                    let p = {};
                    if (e.data.othervalues) {
                        let ovkey;
                        for (ovkey in e.data.othervalues) {
                            p[ovkey] = $('[name="' + e.data.othervalues[ovkey] + '"]').val();
                        }
                    } else p = undefined;
                    // Ahora ejecutamos los slaves
                    let slavekey;
                    for (slavekey in e.data.slaves) {
                        let nombrefuncion = e.data.slaves[slavekey];
                        if (typeof window[nombrefuncion] === "function") {
                            let result = window[nombrefuncion](p, $('[name="' + slavekey + '"]'));
                            if (result != null) {
                                $('[name="' + slavekey + '"]').val(result);
                            }
                        }
                    }
                });
                $(uniqselector).trigger('change');
                break;
            }
            case 'select-makes-required': {
                $(document).on('change', formSelector + ' ' + 'select[name="' + key + '"]', config[key], function (e) {
                    var valor = $(this).val();
                    var slavekey;
                    // Si el select tiene el valor nulo, asumiremos que su valor será lo que marque "onnull"
                    if (valor == '') {
                        valor = e.data.onnull;
                    }
                    // Dependiendo del valor que adopte, haremos que los esclavos sean requeridos o no.
                    for (slavekey in e.data.slaves) {
                        if (slavekey == valor) {
                            zfx.Zaf.formReqElement(formSelector, e.data.slaves[slavekey]);
                        } else {
                            zfx.Zaf.formNoReqElement(formSelector, e.data.slaves[slavekey]);
                        }
                    }
                    checkForms($(formSelector).parent());
                })
                $(formSelector + ' ' + 'select[name="' + key + '"]').trigger('change');
                break;
            }
        }
    }
}

// Para su uso desde zfx.Zaf.formInterDeps
zfx.Zaf.formHideElement = function (formSelector, name) {
    $(formSelector + ' ' + '[data-zaf-field-name="' + name + '"]').hide();
}

// Para su uso desde zfx.Zaf.formInterDeps
zfx.Zaf.formShowElement = function (formSelector, name) {
    $(formSelector + ' ' + '[data-zaf-field-name="' + name + '"]').show();
}

// Para su uso desde zfx.Zaf.formInterDeps
zfx.Zaf.formReqElement = function (formSelector, name) {
    let elem = $(formSelector + ' ' + '[name="' + name + '"]');
    elem.addClass('zjFvReq');
    let label = elem.parents('._element').find('._label');
    label.addClass('zjRequiredLabel');
    label.html(label.html() + '*');
    checkReq(elem);
}

// Para su uso desde zfx.Zaf.formInterDeps
zfx.Zaf.formNoReqElement = function (formSelector, name) {
    let elem = $(formSelector + ' ' + '[name="' + name + '"]');
    let label = elem.parents('._element').find('._label');
    console.log(label);
    let oldlabel = label.html();
    if (typeof(oldlabel) != 'undefined') {
        label.removeClass('zjRequiredLabel');
        label.html(oldlabel.replace(/\*+$/, ''));
    }
    elem.removeClass('zjFvReq').removeClass('zjReqKO');
    checkReq(elem);
}

// ------------------------------------------------------------------------
// Basic setup of a loaded container/document
// ------------------------------------------------------------------------
function _setup(target) {
    target.find('.zjDatePicker').datepicker();
    target.find('.zjDateTimePicker').datetimepicker();
    target.find('.zjTimePicker').timepicker();
    try {
        let x = target.find('.zjExtSelect');
        x.each(function (i, el) {
            // // Calculo la longitud máxima de la cadena. Ya no se va usar, pero antes con chosen, si.
            // let maxLength = Math.max.apply(null, $(el).find("option").map(function () {
            //     return $(this).text().length;
            // }));
            $(el).select2({language: "es"});
        });
    } catch (e) {
        console.log(e);
    }
    target.find('[data-magnify=gallery]').magnify({
        keyboard: true,
        zIndex: 999999
    });
    target.find('.zjDraggable').draggable({
        axis: "x",
        containment: "parent",
        cursor: "crosshair",
        opacity: 0.35,
        revert: "invalid",
        zIndex: 100
    }).css('position','sticky');
    target.find('.zjDroppable').droppable({
        classes: {
            "ui-droppable-hover": "zjDroppableHover"
        },
        tolerance: "pointer"
    }).css('position', 'sticky');
    target.find('.zjSketch').each(function () {
        var readonly = $(this).attr('data-readonly');
        var namevec = $(this).attr('data-namevec');
        var pad = new Sketchpad(this, {
            line: {
                color: '#000000',
                size: 3
            },
            width: $(this).attr('data-width'),
            height: $(this).attr('data-height')
        });
        if (readonly === 'true') pad.setReadOnly(true);
        // Los datos del dibujo (si existen)
        var form = $(this).closest('form');
        var vec = form.find('input:hidden[name="' + namevec + '"]');
        if (vec.length == 1) {
            try {
                var sketchdata = JSON.parse(vec.val());
                pad.loadJSON(sketchdata);
            } catch (e) {
                pad.clear();
            }
        }
        // Lo almaceno porque luego lo necesitaré
        zfx.Zaf.data['sketch-' + $(this).attr('name')] = pad;
    });

    _validate(target);
    target.find('select.zjFilterBox_select, select.zjFilterRange_select').each(function () {
        _jsh($(this), 'input.zjFilterBox_input, input.zjFilterRange_input');
    });

    target.find('select.zjDepMaster').each(function () {
        $(this).trigger('change');
    });

    // Si en el target hay un cuadro FastInserter, siempre recibe foco
    target.find('input.zjFastInserterSearchBox').first().focus();
}


// ------------------------------------------------------------------------
// Validation
// ------------------------------------------------------------------------
function _validate(target) {
    if (!target) target = $(document);
    target.find('form').submit(function (e) {
        if ($(this).find('.zjReqKO').length > 0) {
            e.preventDefault();
        }
    });
    target.find('.zjFvReq').each(function () {
        checkReq($(this));
    });
    target.find('.zjChkIBAN').each(function () {
        checkIBAN()($(this));
    });
    target.find('.zjChkNIF').each(function () {
        checkNIF()($(this));
    });
    target.find('.zjRelOR').each(function () {
        var groups = [];
        var group = zfx.StrTools.extractData($(this).attr('class'), 'zjGroupOR_', '(\\S+)');
        if (groups.indexOf(group) == -1) {
            groups.push(group);
            checkOr(group);
        }
    });
    checkForms(target);
}

function checkReq(jqObj) {
    // Necesitamos que sea requerido para hacer comprobaciones
    if (!jqObj.hasClass('zjFvReq')) return;
    // Si es un fichero o una imagen, lo olvidamos
    if (jqObj.hasClass('zjFvFile') || jqObj.hasClass('zjFvImage')) return;

    if (jqObj.hasClass('zjFvSketch')) {
        let s = jqObj.find(".zjSketch:has(canvas)");
        if (s.length == 1) {
            if (zfx.Zaf.data['sketch-' + $(s[0]).attr("name")]._strokes.length === 0) {
                jqObj.removeClass('zjReqOK');
                jqObj.addClass('zjReqKO');
            } else {
                jqObj.removeClass('zjReqKO');
                jqObj.addClass('zjReqOK');
            }
        }
    }
    // En cualquier otro caso, comprobamos el valor
    else {
        if (jqObj.val() === '') {
            jqObj.removeClass('zjReqOK');
            jqObj.addClass('zjReqKO');
        } else {
            jqObj.removeClass('zjReqKO');
            jqObj.addClass('zjReqOK');
        }
    }

}

// Handle IBAN format
function checkIBAN(jqObj) {
    if (jqObj.hasClass('zjChkIBAN')) {
        if (jqObj.val().length > 0) {
            if (!__validaIBAN(jqObj.val())) {
                jqObj.removeClass('zjReqOK');
                jqObj.addClass('zjReqKO');
            } else {
                jqObj.removeClass('zjReqKO');
                jqObj.addClass('zjReqOK');
            }
        }
    }
}

// Handle NIF letter
function checkNIF(jqObj) {
    if (jqObj.hasClass('zjChkNIF')) {
        if (jqObj.val().length > 0) {
            var newVal = __validaNIF(jqObj.val());
            if (newVal === '') {
                jqObj.removeClass('zjReqOK');
                jqObj.addClass('zjReqKO');
            } else {
                jqObj.removeClass('zjReqKO');
                jqObj.addClass('zjReqOK');
                jqObj.val(newVal);
            }
        }
    }
}

function checkOr(group) {
    var value;
    value = false;
    $('input.zjGroupOR_' + group + ',select.zjGroupOR_' + group).each(function (i, e) {
        if ($(e).val()) {
            value = true;
            return;
        }
    });
    if (value) {
        $('input.zjGroupOR_' + group + ',select.zjGroupOR_' + group).each(function (i, e) {
            $(e).removeClass('zjReqKO');
            $(e).addClass('zjReqOK');
        });
    } else {
        $('input.zjGroupOR_' + group + ',select.zjGroupOR_' + group).each(function (i, e) {
            $(e).removeClass('zjReqOK');
            $(e).addClass('zjReqKO');
        });
    }

}

function checkMirror(group) {
    var ref = '';
    var first = null;
    $('input.zjGroupMirror_' + group).each(function (i, e) {
        if (i == 0) {
            ref = $(e).val();
            first = $(e);
        } else {
            if ($(e).val() != ref) {
                first.removeClass('zjReqOK');
                first.addClass('zjReqKO');
                $(e).removeClass('zjReqOK');
                $(e).addClass('zjReqKO');
            } else {
                first.removeClass('zjReqKO');
                first.addClass('zjReqOK');
                $(e).removeClass('zjReqKO');
                $(e).addClass('zjReqOK');
            }
        }
    });
}

function checkForms(target) {
    if (!target) target = $(document);
    target.find('form').each(function (index, value) {
        if ($(value).find('input.zjReqKO, textarea.zjReqKO, div.zjReqKO, select.zjReqKO').length) {
            $(value).find('input[data-adm-action=submit], input[type=submit], button[data-adm-action=submit], button[type=submit]').prop('disabled', true);
        } else {
            $(value).find('input[data-adm-action=submit], input[type=submit], button[data-adm-action=submit], button[type=submit]').prop('disabled', false);
        }
    });
}

// ------------------------------------------------------------------------
// Auxiliary validation functions
// ------------------------------------------------------------------------

// Función que devuelve los números correspondientes a cada letra
function __getNumIBAN(letra) {
    var letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return letras.search(letra) + 10;
}

// Función que calcula el módulo sin hacer ninguna división
function __mod(dividendo, divisor) {
    var cDividendo = '';
    var cResto = '';

    for (var i in dividendo) {
        var cChar = dividendo[i];
        var cOperador = cResto + '' + cDividendo + '' + cChar;

        if (cOperador < parseInt(divisor)) {
            cDividendo += '' + cChar;
        } else {
            cResto = cOperador % divisor;
            if (cResto == 0) {
                cResto = '';
            }
            cDividendo = '';
        }
    }
    cResto += '' + cDividendo;
    if (cResto == '') {
        cResto = 0;
    }
    return cResto;
}

// Función que comprueba el IBAN
function __validaIBAN(IBAN) {
    IBAN = IBAN.toUpperCase();
    IBAN = IBAN.replace(/[^ ES0-9]/g, "");
    var num1, num2;
    var isbanaux;
    if (IBAN.length != 24) { // En España el IBAN son 24 caracteres
        return false;
    } else {
        num1 = __getNumIBAN(IBAN.substring(0, 1));
        num2 = __getNumIBAN(IBAN.substring(1, 2));
        isbanaux = IBAN.substr(4) + String(num1) + String(num2) + IBAN.substr(2, 2);
        resto = __mod(isbanaux, 97);
        return (resto == 1);
    }
}

// Genera la letra del NIF
function __letraNIF(dni) {
    var lockup = 'TRWAGMYFPDXBNJZSQVHLCKE';
    return lockup.charAt(dni % 23);
}

// Valida un NIF
function __validaNIF(nif) {
    nif = nif.toUpperCase();
    var partes = nif.match(/^([0-9]+)([TRWAGMYFPDXBNJZSQVHLCKE])/);
    if (partes) {
        if (partes[2] == __letraNIF(partes[1])) {
            return partes[1] + partes[2];
        } else {
            return '';
        }
    } else if (nif.match(/^[0-9]{8}$/)) {
        return nif + __letraNIF(nif);
    } else {
        return nif;
    }

}

// ------------------------------------------------------------------------
// Action framework
// ------------------------------------------------------------------------
function beforeLoad(target) {
    target.addClass('zjLoading');
}

function afterLoad(target) {
    target.removeClass('zjLoading');
    if (target.hasClass('zafFastInserterList')) {
        zfx.Zaf.setupFITarget(target);
    } else {
        _setup(target);
    }
}

function actionLoad(sourceURL, targetID, autoFocus, expand) {
    var target = getTarget(targetID, expand);
    beforeLoad(target);
    if (autoFocus === true) {
        target.load(sourceURL, function () {
            target.find('input[type=text]:enabled, textarea:enabled, select:enabled').first().focus();
            afterLoad(target);
        });
    } else {
        target.load(sourceURL, function () {
            $(':focus').blur();
            afterLoad(target);
        });
    }

}

function actionSubmitData(actionURL, data, targetID, expand, success) {
    if (targetID) {
        var target = getTarget(targetID, expand);
        beforeLoad(target);
        $.ajax(
            {
                url: actionURL,
                type: "POST",
                data: data,
                contentType: false,
                processData: false,
                success: function (data) {
                    target.html(data);
                    afterLoad(target);
                }
            });
    } else {
        $.ajax(
            {
                type: "POST",
                url: actionURL,
                data: data,
                contentType: false,
                processData: false,
                success: success
            });
    }
}

// Hay que joderse
function dataURItoBlob(dataURI, dataTYPE) {
    var binary = atob(dataURI.split(',')[1]), array = [];
    for (var i = 0; i < binary.length; i++) array.push(binary.charCodeAt(i));
    return new Blob([new Uint8Array(array)], {type: dataTYPE});
}

function actionSubmit(actionURL, formID, targetID, expand, success) {
    // Si hay un zjSketch con un canvas en su interior o varios, pasamos sus contenidos como JSON
    $(formID).find(".zjSketch:has(canvas)").each(function () {
        var name = $(this).attr("name");
        var namevec = $(this).attr("data-namevec");
        var hiddenElement = $('input:hidden[name="' + namevec + '"]');
        $('input:hidden[name="' + namevec + '"]').attr('value', JSON.stringify(zfx.Zaf.data['sketch-' + name].toJSON()));
    });

    // Obtenemos los datos del formulario tal cual
    var formData = new FormData($(formID)[0]);

    // Y nuevamente si hay un zjSketch con un canvas en su interior o varios, pasamos sus contenidos como PNG
    $(formID).find(".zjSketch:has(canvas)").each(function () {
        var lienzos = this.getElementsByTagName("canvas");
        var name = $(this).attr("name");
        var namevec = $(this).attr("data-namevec");
        for (i = 0; i <= lienzos.length - 1; i++) {
            formData.append(name, dataURItoBlob(lienzos[i].toDataURL("image/png")), i + ".png");
        }
    });


    actionSubmitData(actionURL, formData, targetID, expand, window[success]);
}

function actionSetval(targetID, encodedtext) {
    $(targetID).val(zfx.StrTools.safeDecode(encodedtext));
    $(targetID).trigger('input');
}

function actionClose(targetID) {
    if (!targetID) return;
    var target = $(targetID);
    if (target.length != 0) {
        target.remove();
    }
}

function actionHide(targetID) {
    if (!targetID) return;
    var target = $(targetID);
    if (target.length != 0) {
        target.css("visibility", "hidden");
    }
}


function showCustomAlert(mensaje, funciones = null, btnPredeterminado = '', obligatorio = false) {
    let btns = '';
    let btnX = '';
    let rndm = Math.floor(Math.random() * (9999 - 1000) + 1000);
    let id = "#customAlert" + rndm;
    if (!jQuery.isEmptyObject(funciones)) {
        for (let i = 0; i < funciones.length; i++) {
            let id = 'boton' + i;
            btns += '<a id="' + id + '" class="zModalConfirmBtn zML1">' + funciones[i].label + '</a>'
        }
    }
    if (btnPredeterminado !== '') {
        btns += '<a class="zModalCancelBtn zML1" href="javascript:void(0)" data-adm-action="close" data-adm-close-target="' + id + '">' +
            btnPredeterminado + '</a>';
    }
    if (!obligatorio) {
        btnX = '<a class="zbfIcon zjOverlayCloseButton" href="javascript:void(0)" data-adm-action="close" data-adm-close-target="' + id + '">' +
            '<span class="fas fa-window-close"></span>' +
            '</a>'
    }
    let dialogDiv = $('' + '<div class="zCustomModalOverlay" id="customAlert' + rndm + '">' +
        '<div class="zjModalOverlay">' +
        '<div class="zjOverlayHeader">' +
        btnX
        +
        '</div>' +
        '<div class="zjDesktop">' +
        '<div class="zbfBox">' +
        '<div>' + mensaje + '</div>' +
        '<div class="zTextEnd zMT2">' + btns + '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>');
    // count older divs
    let num = $('.zjModalOverlay').length + 1;
    zindex = num;
    if (num > 3) num = 3;
    dialogDiv.css('z-index', zindex);
    $('.zjMain').append(dialogDiv);
    dialogDiv.show();

    if (!obligatorio) {
        //Si haces click fuera del cuadro de dialogo se cierra el modal
        $("#customAlert").click(function (event) {
            if (!$(event.target).closest('.zjModalOverlay').length) {
                actionClose(id);
            }
        });
    }

    if (!jQuery.isEmptyObject(funciones)) {
        for (let i = 0; i < funciones.length; i++) {
            $("#boton" + i).on('click', function () {
                funciones[i].funcion();
                actionClose(id);
            });
        }
    }
}


// ------------------------------------------------------------------------
// Option testers
// ------------------------------------------------------------------------
function optionAsk(object) {
    if (/ask/.test(object.attr("data-adm-options"))) {
        return confirm('Necesito confirmar esta acción.');
    } else {
        return true;
    }
}

function optionDisableAfter(object) {
    return /disable/.test(object.attr("data-adm-options"));
}

function optionExpand(object) {
    return /expand/.test(object.attr("data-adm-options"));
}

function optionFeedback(object) {
    return /feedback/.test(object.attr("data-adm-options"));
}

function optionAutoFocus(object) {
    return /autoFocus/.test(object.attr("data-adm-options"));
}

function optionBlank(object) {
    return /blank/.test(object.attr("data-adm-options"));
}

function optionRequired(object) {
    return /required/.test(object.attr("data-adm-options"));
}

function optionOnce(object) {
    return /once/.test(object.attr("data-adm-options"));
}

function _jsh(el, selector) {
    // JSH Handling
    if (el.hasClass('zjFilterBox_select') || el.hasClass('zjFilterRange_select')) {
        sel = el.find(":selected");
        if (sel.attr("data-jsh")) {
            rel = el.parent().parent().find(selector);
            switch (sel.attr("data-jsh")) {
                case 'dt': {
                    rel.datepicker();
                    break;
                }
                case 'tm': {
                    rel.timepicker();
                }
                case 'dttm': {
                    rel.datetimepicker();
                    break;
                }
                default: {
                    rel.datepicker("destroy");
                    break;
                }
            }
        }
    }
}

// ------------------------------------------------------------------------
// Document ready event handler
// ------------------------------------------------------------------------
$(document).ready(function () {

    _init();

    // Menu a la altura correcta
    $('.zjNav').bind('scroll', function (event) {
        localStorage.setItem("_scroll", $('.zjNav').scrollTop());
    });

    if (localStorage.getItem("_scroll") != null) {
        $('.zjNav').animate({scrollTop: localStorage.getItem("_scroll")}, 0, 'swing');
    }

    // Set event handlers

    // Check/uncheck all boxes in same column
    $('body').on('click', 'input[type=checkbox].zjCheckAll', function () {
        thNum = $(this).closest('th').index();
        if ($(this).is(':checked')) {
            $(this).closest("table").find("td:nth-child(" + (thNum + 1) + ") input[type=checkbox]").prop('checked', true);
        } else {
            $(this).closest("table").find("td:nth-child(" + (thNum + 1) + ") input[type=checkbox]").prop('checked', false);
        }
    });


    // Click on row means first action
    $(document).on('click', "tr.zjClickOnRow td.zjData", function (event) {
        var anchor = $(this).parent().find('.zjFvAction').first();
        if (anchor.attr('href') && anchor.attr('href') != 'javascript:void(0)') {
            zfx.FlowTools.get(anchor.attr('href'));
        } else {
            anchor.trigger('click');
        }
    });


    // Cloned elements (for example repeat-password boxes)
    $(document).on('change input', '.zjClone', function (event) {
        var id = $(this).attr('data-cloneid');
        var value = $(this).val();
        var element = this;
        $('.zjClone[data-cloneid="' + id + '"]').each(function () {
            if (this === element) return;
            $(this).val(value);
            checkReq($(this));
        });
    });


    // On change
    $(document).on('input', 'input.zjFvReq, input.zjChkIBAN, input.zjChkNIF, textarea.zjFvReq, select.zjFvReq', function () {
        checkIBAN($(this));
        checkNIF($(this));
        checkReq($(this));
        checkForms();
    });

    $(document).on('mouseup', '.zjFvSketch', function () {
        checkReq($(this));
        checkForms();
    });

    $(document).on('touchend', '.zjFvSketch', function () {
        checkReq($(this));
        checkForms();
    });


    // Mirrored fields
    $(document).on('input', 'input.zjMirror', function () {
        checkMirror(zfx.StrTools.extractData($(this).attr('class'), 'zjGroupMirror_', '(\\S+)'));
        checkForms();
    });


    // OR field group: only one non-empty validates all group
    $(document).on('input change', 'input.zjRelOR, select.zjRelOR', function () {
        checkOr(zfx.StrTools.extractData($(this).attr('class'), 'zjGroupOR_', '(\\S+)'));
        checkForms();
    });


    // ENTER or ESC in a form with zjFormSaveButton and zjFormCancelButton will activate each one
    $(document).on('keydown', 'form.zjNoSendKey', function (event) {
        if (event.which == 27) {
            var button = $(this).find('.zjFormCancelButton');
            if (button) {
                button.trigger('click');
            }
        } else if (event.which == 13) {
            var currElement = $(document.activeElement);
            if (currElement.is('textarea')) return;
            var button = $(this).find('.zjFormSaveButton');
            if (button) {
                button.trigger('click');
            }
        }

    });


    // Dropdown in filters will activate its checkbox.
    $(document).on('change click', "select.zjFilterList_select", function (event) {
        if ($(this)[0].options[0].selected === true) {
            $(this).closest('form').find('[type=checkbox]').prop('checked', false).prop('disabled', true);
        } else {
            $(this).closest('form').find('[type=checkbox]').prop('checked', true).prop('disabled', false);
        }
    });


    // Checkbox in filters will select blank.
    $(document).on('change', ".zjFilterList_check input[type=checkbox]", function (event) {
        var checked = $(this).prop('checked');
        if (!checked) {
            $("#zjFilterList_select_" + $(this).val())[0].options[0].selected = true;
            $(this).prop('disabled', true);
        }
    });


    // Form with zjNoSendKey class will not respond to SUBMIT commands
    $(document).on("submit", "form.zjNoSendKey", function (event) {
        event.preventDefault();
    });
    // ADM Actions load, submit, setval, close. For convenience we will process adm-action-close if specified.


    // Clicks on anchors and buttons
    $(document).on('click', "a[data-adm-action='load'], button[data-adm-action='load'], input[type='button'][data-adm-action='load'], td[data-adm-action='load']", function (event) {
        if (event.isTrigger) event.preventDefault();
        var el = $(this);
        if (optionAsk(el)) {
            if (el.attr("data-adm-disabled") !== "true") {
                actionLoad(el.attr("data-adm-load-source"), el.attr("data-adm-load-target"), optionAutoFocus(el), optionExpand(el));
            }
            if (optionOnce(el)) {
                el.attr("data-adm-disabled", "true");
            }
        }
        event.preventDefault();
    });


    $(document).on('click', "a[data-adm-action='submit'], button[data-adm-action='submit'], input[type='button'][data-adm-action='submit'], td[data-adm-action='submit']", function (event) {
        if (event.isTrigger) event.preventDefault();
        var el = $(this);
        if (optionAsk(el)) {
            if (optionDisableAfter(el)) el.prop('disabled', true);
            actionSubmit(el.attr("data-adm-submit-action"), el.attr("data-adm-submit-form"), el.attr("data-adm-submit-target"), optionExpand(el), el.attr('data-adm-success'));
            if (optionFeedback(el)) {
                $('body').effect('transfer', {to: el});
            }
            actionClose(el.attr("data-adm-submit-close"));
            actionHide(el.attr("data-adm-submit-hide"));
            // If a function name is specified in 'data-adm-after' a function with that name will be called with the element itself as parameter.
            if (el.attr('data-adm-submit-tab')) {
                zfx.Zaf.selectTab(el.attr('data-adm-submit-tab'));
            }
            if (el.attr('data-adm-after')) {
                window[el.attr('data-adm-after')](el);
            }
        }
        event.preventDefault();
    });


    $(document).on('click', "a[data-adm-action='setval'], button[data-adm-action='setval'], input[type='button'][data-adm-action='setval'], td[data-adm-action='setval']", function (event) {
        if (event.isTrigger) event.preventDefault();
        var el = $(this);
        if (optionAsk(el)) {
            var i = 1;
            while (true) {
                if (el.attr('data-adm-setval-target-' + i)) {
                    actionSetval(el.attr('data-adm-setval-target-' + i), el.attr('data-adm-setval-value-' + i));
                    i++;
                } else {
                    break;
                }
            }
            actionClose(el.attr("data-adm-setval-close"));
            actionHide(el.attr("data-adm-setval-hide"));
        }
        event.preventDefault();
    });


    $(document).on('click', "a[data-adm-action='close'], button[data-adm-action='close'], input[type='button'][data-adm-action='close']", function (event) {
        if (event.isTrigger) event.preventDefault();
        var el = $(this);
        if (optionAsk(el)) {
            actionClose(el.attr("data-adm-close-target"));
        }
        event.preventDefault();
    });
    // Submit only

    // Input boxes will respond to adm-actions, but it will be delayed a bit
    $(document).on('input', "input[type='text'][data-adm-action='submit']", $.debounce(250, function () {
        // In case of filterbox or filterrange we will need a selected value
        if ($(this).hasClass('zjFilterBox_input')) {
            var selectorVal = $(this).parent().parent().find('.zjFilterBox_select').val();
            var selectorTxt = $(this).parent().parent().find('.zjFilterBox_select option:selected').text();
            if (!selectorVal && !selectorTxt) return;
        } else if ($(this).hasClass('zjFilterRange_input')) {
            var selectorVal = $(this).parent().parent().find('.zjFilterRange_select').val();
            var selectorTxt = $(this).parent().parent().find('.zjFilterRange_select option:selected').text();
            if (!selectorVal && !selectorTxt) return;
        }
        $(this).closest('.zjDhContainer').addClass('zjDhFilterActive');
        actionSubmit($(this).attr("data-adm-submit-action"), $(this).attr("data-adm-submit-form"), $(this).attr("data-adm-submit-target"));
    }));

    // Select controls will respond to adm-actions on change.
    $(document).on('change', "select[data-adm-action='submit']", function (event) {
        var el = $(this);
        if (optionAsk(el)) {
            if (el.val()) {
                el.closest('.zjDhContainer').addClass('zjDhFilterActive');
            } else {
                el.closest('.zjDhContainer').removeClass('zjDhFilterActive');
            }
            actionSubmit(el.attr("data-adm-submit-action"), el.attr("data-adm-submit-form"), el.attr("data-adm-submit-target"), optionExpand(el));
            _jsh(el, 'input.zjFilterBox_input, input.zjFilterRange_input');
        }
        event.preventDefault();
    });
    // Checkboxes
    $(document).on('change', "input[type='checkbox'][data-adm-action='submit']", function (event) {
        var el = $(this);
        if (el.prop('checked')) {
            el.closest('.zjDhContainer').addClass('zjDhFilterActive');
        } else {
            el.closest('.zjDhContainer').removeClass('zjDhFilterActive');
        }
        if (optionAsk(el)) {
            actionSubmit(el.attr("data-adm-submit-action"), el.attr("data-adm-submit-form"), el.attr("data-adm-submit-target"), optionExpand(el));
        }
        // No preventDefault.
    });


    // ADM Action submit only for row selection using checkboxes
    $(document).on('click', "a[data-adm-action='selection'], button[data-adm-action='selection'], input[type='button'][data-adm-action='selection']", function (event) {
        if (event.isTrigger) event.preventDefault();
        var el = $(this);
        if (optionAsk(el)) {
            // ¿AJAX?
            var ajax;
            if (el.attr("data-adm-selection-callback")) ajax = false;
            else if (el.attr('href') !== '' && el.attr('href') !== 'javascript:void(0)') ajax = false;
            else ajax = true;
            // Retrieve data
            var dat;
            // If an additional form was specified, retrieve its data
            var formID = el.attr('data-adm-selection-form');
            if (formID !== '' && $(formID).length === 1) {
                if (ajax) dat = new FormData($(formID)[0]);
                else dat = $(formID).serialize();
            } else {
                if (ajax) dat = new FormData();
                else dat = {};
            }
            emptyList = true;
            $(el.attr("data-adm-selection-source") + ' input[type=checkbox].zjSelector').each(function (index, element) {
                if ($(element).prop('checked') === true) {
                    emptyList = false;
                    var id = $(element).attr('data-adm-selector-id');
                    if (ajax) dat.append(id + index, $(element).attr('value'));
                    else dat[id + index] = $(element).attr('value');
                }
            });
            if (el.attr("data-adm-selection-callback")) {
                fnName = el.attr("data-adm-selection-callback");
                eval(fnName)(dat, el);
            } else {
                if (!optionRequired(el) || !emptyList) {
                    if (ajax)
                        actionSubmitData(el.attr("data-adm-selection-action"), dat, el.attr("data-adm-selection-target"), optionExpand(el));
                    else
                        zfx.FlowTools.post(el.attr('href'), dat, optionBlank(el));
                    if (optionFeedback(el)) {
                        $('body').effect('transfer', {to: el});
                    }
                }
            }
        }
        event.preventDefault();
    });


    // Tabs
    $(document).on('click', '.zjTabButton', function (event) {
        zfx.Zaf.selectTab($(this));
    });

    // Context Menu
    $(document).on('click', '.zjContextMenuButton', function (event) {
        var el = $(this);
        var menuSelector = $(this).attr("data-adm-ctxt");
        $('.' + menuSelector).toggleClass('zjContextMenuHidden');
    });

    $(document).on('click', 'a.zjContextMenuItem', function (event) {
        $(this).parents('div.zjContextMenu').addClass('zjContextMenuHidden');
    });


    // Column drag and drop in lst panels
    $(document).on("drop", ".zjFieldHeader", function (event, ui) {
        panel = $(this).parents('.zjLstPanel');
        action = panel.attr('data-adm-cfg');
        target = "#" + panel.attr('id');
        src = ui.draggable.attr('data-adm-field');
        des = $(this).attr('data-adm-field');
        data = new FormData();
        data.append('typ', 'col');
        data.append('src', src);
        data.append('des', des);
        actionSubmitData(action, data, target);
    });

    // Disable after click
    $(document).on('submit', 'form.zjFormSubmitOnce', function (event) {
        if ($(this).hasClass('zjFormSubmitted')) {
            event.preventDefault();
            return;

        }
        $(this).addClass('zjFormSubmitted');
    });

    // Changes in DepMasters will update slaves (targets).
    $(document).on('change', 'select.zjDepMaster', function (event) {
        var selval = $(this).val();
        var target = $(this).attr("data-zaf-dep-target");
        var all = $(this).attr("data-zaf-dep-all");
        var disabletrigger = $(this).attr("data-zaf-dep-disabletrigger");
        var keepvalue = $(this).attr("data-zaf-dep-keepvalue");
        // Buscamos el atributo data-zaf-dep-othervalues y construimos
        var segments = "/";
        var othervaluesattr = $(this).attr("data-zaf-dep-othervalues");
        if (othervaluesattr != undefined) {
            var othervalues = othervaluesattr.split(",");
            if (othervalues.constructor == Array) {
                for (const e of othervalues) {
                    var otherval = $(e.trim()).val();
                    if (otherval != undefined) {
                        segments += zfx.StrTools.safeEncode(otherval) + "/";
                    }
                }
            }
        }
        if (selval != '') {
            var source = $(this).attr("data-zaf-dep-source") + selval + segments;
        } else {
            if (all != '') {
                var source = $(this).attr("data-zaf-dep-source") + all + segments;
            } else {
                return;
            }
        }
        var newValue = $(this).attr("data-zaf-dep-default");
        $.ajax(source, {
                success: function (data) {
                    let objeto = JSON.parse(data);
                    $(target).each(function () {
                        $(this).data('oldValue', $(this).val());
                        $(this).html('');
                    });
                    $.each(objeto, function (k, v) {
                        $(target).each(function () {
                            $(this).append(new Option(v, k));
                        });
                    });
                    if (newValue) {
                        $(target).each(function () {
                            $(this).val(newValue);
                        });
                    } else if (keepvalue) {
                        $(target).each(function () {
                            $(this).val($(this).data('oldValue'));
                        });
                    }
                    if ($(target).hasClass("zjExtSelect") || $(target).hasClass("zjExtSelectH")) {
                        $(target).select2({language: "es"});
                    }
                    if (disabletrigger != '1') {
                        $(target).trigger("change");
                    }
                }
            }
        );
    });

    // Sketch buttons
    $(document).on('click', '.zjSketchButton', function (event) {
        let name = $(this).attr('data-for');
        let func = $(this).attr('data-func');
        switch (func) {
            case 'undo': {
                zfx.Zaf.data['sketch-' + name].undo();
                break;
            }
            case 'redo': {
                zfx.Zaf.data['sketch-' + name].redo();
                break;
            }
            case 'thin': {
                zfx.Zaf.data['sketch-' + name].setLineSize(3);
                $(".zjSketchButton[data-func='thick'][data-for='" + name + "']").removeClass('_enabled');
                $(this).addClass('_enabled');
                break;
            }
            case 'thick': {
                zfx.Zaf.data['sketch-' + name].setLineSize(8);
                $(".zjSketchButton[data-func='thin'][data-for='" + name + "']").removeClass('_enabled');
                $(this).addClass('_enabled');
                break;
            }
            case 'black': {
                zfx.Zaf.data['sketch-' + name].setLineColor('#000000');
                $(".zjSketchButton._color[data-for='" + name + "']").removeClass('_enabled');
                $(this).addClass('_enabled');
                break;
            }
            case 'red': {
                zfx.Zaf.data['sketch-' + name].setLineColor('#ff0000');
                $(".zjSketchButton._color[data-for='" + name + "']").removeClass('_enabled');
                $(this).addClass('_enabled');
                break;
            }
            case 'green': {
                zfx.Zaf.data['sketch-' + name].setLineColor('#00a000');
                $(".zjSketchButton._color[data-for='" + name + "']").removeClass('_enabled');
                $(this).addClass('_enabled');
                break;
            }
            case 'blue': {
                zfx.Zaf.data['sketch-' + name].setLineColor('#0066ff');
                $(".zjSketchButton._color[data-for='" + name + "']").removeClass('_enabled');
                $(this).addClass('_enabled');
                break;
            }
            case 'delete': {
                zfx.Zaf.data['sketch-' + name].clear();
                break;
            }
        }
    });

    // FastInserter

    // El buscador del fastinserter, al escribir.
    $(document).on('input', ".zjFastInserterSearchBox", $.debounce(250, function () {
        let axurl = $(this).attr("data-adm-fi-axsearch");
        let divId = $(this).attr("data-adm-fi-div");
        if (axurl != '' && $(this).val() != '') {
            $(divId).show();
            actionSubmit($(this).attr("data-adm-fi-axsearch"), $(this).attr("data-adm-fi-form"), divId);
        } else {
            // Ocultamos y limpiamos el valor elegido
            $(divId).hide();
            $(this).closest('form').find('input[name="id"]').val('');
        }
    }));

    // Tecla cursor abajo en el buscador da foco al primer elemento
    $(document).on('keydown', ".zjFastInserterSearchBox", function (e) {
        if (e.which == 40) {
            e.preventDefault();
            let divId = $(this).attr("data-adm-fi-div");
            if ($(divId).is(":visible")) {
                let results = $(divId).find(".zafFastInserterResult");
                if (results.length > 0) {
                    results[0].focus();
                }
            }
        }
    });

    // Teclas en un elemento seleccionado
    $(document).on('keydown', ".zafFastInserterList", function (e) {
        switch (e.which) {
            case 38: // arriba, anterior coincidencia
            {
                e.preventDefault();
                $(this).find("a.zafFastInserterResult:focus").prev().focus();
                break;
            }
            case 40: // abajo, siguiente coincidencia
            {
                e.preventDefault();
                $(this).find("a.zafFastInserterResult:focus").next().focus();
                break;
            }
            case 37: // izq, vuelve al cuadro de búsqueda
            {
                e.preventDefault();
                let fi = $(this).closest('.zafFastInserter');
                fi.find('input[name="search"]').focus();
                break;
            }
            case 39: // dcha, lo deja seleccionado y pasa al parámetro (si hay) o al botón de enviar
            {
                e.preventDefault();
                let fi = $(this).closest('.zafFastInserter');
                // Lo dejo seleccionado
                fi.find('input[name="id"]').val($(this).find("a.zafFastInserterResult:focus").attr('data-adm-fi-id'));
                // Busco el parámetro o el botón de enviar
                let param = fi.find('.zafFastInserterParam').first();
                if (param.length) {
                    param.focus();
                } else {
                    fi.find('button').focus();
                }
                $(this).hide();
                break;
            }
        }
    });


    // Haciendo clic en el resultado del fastinserter lo elegimos y hacemos post directamente
    $(document).on('click', ".zafFastInserterResult", function () {
        let fi = $(this).closest('.zafFastInserter');
        fi.find('input[name="id"]').val(this.getAttribute('data-adm-fi-id'));
        fi.find('button').click();
    });


    // Edición inline.
    zfx.Zaf.saveInline = function (el) {
        let url = el.attr('data-zaf-inl');
        let name = el.attr('name');
        let data = {
            '_id': el.attr('data-zaf-pk')
        };
        if (el.attr('type') === 'checkbox') {
            data[name] = (el.is(':checked') ? '1' : '0');
        } else {
            data[name] = el.val();
        }
        el.removeClass("zjInlineSaved");
        $.post(url, data, function () {
            el.addClass("zjInlineSaved");
        });
    }

    $(document).on('input', "input[data-zaf-beh='inlineEdit'],select[data-zaf-beh='inlineEdit']", $.debounce(500, function () {
        zfx.Zaf.saveInline($(this));
    }));

    $(document).on('blur', "input[data-zaf-beh='inlineEdit']", function () {
        zfx.Zaf.saveInline($(this));
    });

    // Pulsar arriba o abajo en edición inline
    $(document).on('keydown', "input[type='text'][data-zaf-beh='inlineEdit']", function (e) {
        switch (e.which) {
            case 38: // arriba, anterior coincidencia
            {
                let name = $(this).attr("name");
                $(this).closest('tr.zjData').prev().find("input[data-zaf-beh='inlineEdit'][name='" + name + "']").first().focus();
                e.preventDefault();
                break;
            }
            case 40: // abajo, siguiente coincidencia
            {
                let name = $(this).attr("name");
                $(this).closest('tr.zjData').next().find("input[data-zaf-beh='inlineEdit'][name='" + name + "']").first().focus();
                e.preventDefault();
                break;
            }
        }
    });


    $(document).on('click', ".zjTreeControl a", function () {
        let subtree = $(this).closest(".zjTreeNode").children(".zjTreeSubtree").first();
        if (subtree.hasClass("zjTreeClosed")) {
            subtree.removeClass("zjTreeClosed");
            $(this).html("⏷");
        } else {
            subtree.addClass("zjTreeClosed");
            $(this).html("⏵");
        }
    });


    $(document).on('click', ".zjGenDCControl", function () {
        let target = $("#" + $(this).attr("data-gen-dc-target"));
        if (target.length > 0) {
            if (target.hasClass("zjGenDCClosed")) {
                target.removeClass("zjGenDCClosed");
                //$(this).html("⏷");
            } else {
                target.addClass("zjGenDCClosed");
                //$(this).html("⏵");
            }
        }
    });

    $(document).on('click', ".zjHideOnceShow", function () {
        let target = $("#" + $(this).attr("data-target"));
        if (target.length > 0) {
            if (target.hasClass("zjHideOnceHidden")) {
                target.removeClass("zjHideOnceHidden");
                $(this).remove();
            }
        }
    });



    // Si hacemos click en la barra o en la etiqueta, también desplegamos
    $(document).on("click", ".zjPanelDropHeader", function (e) {
        e.stopImmediatePropagation();
        // Solo si es en el fondo o en la etiqueta, no en un control interno
        if (e.target == this || e.target.classList.contains("zjPanelDropLabel")) {
            $(this).find(".zjPanelDropControl a").click();
        }
    });


    $(document).on('click', ".zjPanelDropControl a", function (e) {
        e.stopImmediatePropagation();
        let content = $(this).closest(".zjPanelDrop").children(".zjPanelDropContent").first();
        let img = $(this).find('img');
        if (content.hasClass("zjPanelDropClosed")) {
            content.removeClass("zjPanelDropClosed");
            if (img.length == 0) {
                $(this).html("⏷");
            }
            else {
                img.attr("src", img.attr("data-alt2"));
            }
            // content.find(".zjExtSelectH").chosen({
            //     inherit_select_classes: false, // No heredar las clases del select, ya que son Hidden.
            // });
            content.find(".zjExtSelectH").select2({
                language: "es"
            });
            let el = $(this);
            if (optionAsk(el)) {
                if (el.attr("data-adm-disabled") !== "true") {
                    actionLoad(el.attr("data-adm-load-source"), el.attr("data-adm-load-target"), optionAutoFocus(el), optionExpand(el));
                }
                if (optionOnce(el)) {
                    el.attr("data-adm-disabled", "true");
                }
            }
        } else {
            content.addClass("zjPanelDropClosed");
            if (img.length == 0) {
                $(this).html("⏵");
            }
            else {
                img.attr("src", img.attr("data-alt1"));
            }
        }
    });


    $(document).on('click', ".zjMenuDropControl", function (e) {
        e.stopImmediatePropagation();
        let content = $("#" + $(this).attr("data-zaf-for"));
        if (content.hasClass("zjMenuDropClosed")) {
            content.removeClass("zjMenuDropClosed");
            $(this).html($(this).html().slice(0, -2) + "🞁");
            $(this).parent().addClass("_headerOpened");
        } else {
            content.addClass("zjMenuDropClosed");
            $(this).html($(this).html().slice(0, -2) + "🞃");
            $(this).parent().removeClass("_headerOpened");
        }
    });

    // Finalmente
    _setup($(document));



}); // Fin de document.load()


