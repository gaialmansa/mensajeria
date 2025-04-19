/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */

/*
 * Get JQuery object using an ID selector (#foo)
 * If the element is not found then creates a floating div.
 */
function getTarget(selectorID, expand) {
    var target = $(selectorID);
    if (target.length === 0) {
        var id = selectorID.substring(1);
        var container = $('<div class="row zjOverlay" id="' + id + '"><div class="col p-0 m-0">' +
            '<div class="card m-0"><button type="button" class="close" data-adm-action="close" data-adm-close-target="#' + id + '"><span aria-hidden="true">&times;</span></button><div class="zjDesktop"></div></div>' +
            '</div></div>');
        $('.zjMain').append(container);
        return container.find('.zjDesktop');
    } else {
        if (target.hasClass('zjOverlay')) {
            return target.find('.zjDesktop');
        } else {
            return target;
        }
    }
}