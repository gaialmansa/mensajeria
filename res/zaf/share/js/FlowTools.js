/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */


// -------------------------------

var zfx;
if (!zfx) zfx = {};
zfx.FlowTools = {};


// -------------------------------


zfx.FlowTools.post = function (action, postData, out) {
    this.sendForm('post', action, postData, out);
};

zfx.FlowTools.get = function (action, postData, out) {
    this.sendForm('get', action, postData, out);
};

zfx.FlowTools.sendForm = function (method, action, postData, out) {
    out = typeof out !== 'undefined' ? out : false;

    form = document.createElement("form");

    form.setAttribute('method', method);
    form.setAttribute('action', action);
    if (out) {
        form.setAttribute('target', '_blank');
    }

    if (method === 'get' && postData === null) {
        if (out) {
            window.open(action);
        } else {
            window.location = action;
        }
    } else {
        for (var key in postData) {
            var hiddenField = document.createElement('input');
            hiddenField.setAttribute('type', 'hidden');
            hiddenField.setAttribute('name', key);
            hiddenField.setAttribute('value', postData[key]);
            form.appendChild(hiddenField);
        }
        document.body.appendChild(form);
        form.submit();
    }
};

// -------------------------------
