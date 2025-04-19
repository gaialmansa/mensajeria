/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */

function _init() {

    // Calculamos si es mobile o no
    zfx.Zaf.mobile = ($("#zjM").css('display') === "block");

    $.datepicker.setDefaults({
            showOn: 'both',
            changeYear: true,
            constrainInput: true,
            onSelect: function () {
                $(this).trigger('input');
                checkReq($(this));
                checkForms();
            }
        }
    );
    $.timepicker.setDefaults({
            showOn: 'both',
            controlType: 'select',
            oneLine: true,
            timeFormat: 'HH:mm:ss',
            onSelect: function () {
                $(this).trigger('input');
                checkReq($(this));
                checkForms();
            }
        }
    );
}


/*
 * Get JQuery object using an ID selector (#foo)
 * If the element is not found then creates a floating div.
 */
function getTarget(selectorID, expand) {
    var target = $(selectorID);
    if (target.length === 0) {
        var id = selectorID.substring(1);
        var dialogDiv = $('<div class="zjOverlay" id="' + id + '">' +
            '<div class="zjOverlayHeader">' +
            '<a class="zbfIcon zjOverlayCloseButton" href="javascript:void(0)" data-adm-action="close" data-adm-close-target="#' + id + '">' +
            '<span class="fas fa-window-close"></span>' +
            '</a>' +
            '</div>' +
            '<div class="zjDesktop">' +
            '</div>' +
            '</div>');
        // count older divs
        var num = $('.zjOverlay').length + 1;
        zindex = num;
        if (num > 3) num = 3;
        dialogDiv.css('z-index', zindex);
        if (!zfx.Zaf.mobile) {
            dialogDiv.css('top', num + 'rem');
            dialogDiv.css('left', num + 'rem');
            dialogDiv.css('bottom', (4 - num) + 'rem');
            dialogDiv.css('right', (4 - num) + 'rem');
        }
        $('.zjMain').append(dialogDiv);
        if (!zfx.Zaf.mobile) {
            dialogDiv.resizable({
                stop: function (event, ui) {
                    $(this).draggable({
                        handle: '.zjOverlayHeader',
                        containment: 'parent',
                        cancel: '.zjOverlayCloseButton',
                        cursor: 'move',
                        opacity: 0.35
                    });
                }
            });
        }
        return dialogDiv.find('.zjDesktop');
    } else {
        if (target.hasClass('zjOverlay')) {
            return target.find('.zjDesktop');
        } else {
            return target;
        }
    }
}
