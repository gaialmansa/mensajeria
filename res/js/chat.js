$(document).ready(function() {
    var $mensajesCuerpo = $('#mensajes-cuerpo');
    var $botonEnviar = $('#boton-enviar');
    var $inputMensaje = $('#input-mensaje');
    var $checkboxes = $('.destinatario-checkbox'); 
    var $chatContainer = $('.chat-container');
    var $tabButtons = $('.tab-button');
    var $tabContents = $('.tab-content');
    var $plantillas = $('.plantilla-texto');
    
    // Elementos del menú contextual
    var $contextMenu = $('#message-context-menu');
    var $contextToggleAction = $('#context-action-toggle');
    var $currentMsgId = null; 

    var lastMsgId = parseInt($chatContainer.data('last-msg-id')) || 0;

    // --- Funciones de Control ---

    function checkDestinatario() {
        var destinatarioSeleccionado = $checkboxes.filter(':checked').length === 1;
        $botonEnviar.prop('disabled', !destinatarioSeleccionado);
    }

   // Función para renderizar un mensaje individual
function renderMessage(mensaje) {
    var isEnviado;

    // 1. Lógica robusta para mensajes que vienen del servidor (polling/actualizaciones)
    //    Si mensaje.id_de tiene un valor, lo usamos para la comparación segura.
    if (mensaje.id_de !== undefined && mensaje.id_de !== null) {
        isEnviado = (parseInt(mensaje.id_de) === parseInt(ID_REMITENTE));
    } else {
        // 2. Lógica de respaldo (fallback) para mensajes creados localmente
        //    Si falta id_de, asumimos que el mensaje es localmente enviado 
        //    si tiene la bandera 'enviado' (t/true).
        isEnviado = (mensaje.enviado === true || mensaje.enviado === 't');
    }
    
    // --- El resto del código se mantiene igual ---
    
    var clasesFila = isEnviado ? 'enviado' : 'recibido';
    var atendidoHtml = '';

    // Asignar el nombre
    var nombreMostrar = isEnviado ? mensaje.nombre_par : mensaje.nombre_de;
    var nombrePar = nombreMostrar || (isEnviado ? 'Destinatario' : 'Remitente');
    
    // Si mensaje.atendido contiene una fecha (es truthy), se aplica la clase 'atendido'
    if (mensaje.atendido) {
        clasesFila += ' atendido';
        
        var dateObj = new Date(Date.parse(mensaje.atendido)); 
        var atendidoTime = dateObj.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

        atendidoHtml = `
            <div class="atendido-info">
                Atendido por: ${mensaje.useratt}<br>  a las ${atendidoTime}
            </div>
        `;
    }

    var mensajeTime = new Date(Date.parse(mensaje.fecha)).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

    var mensajeHtml = `
        <div class="mensaje-fila ${clasesFila}" data-msg-id="${mensaje.id}">
            <div class="par-burbuja">
                ${nombrePar}
                ${atendidoHtml}
            </div>
            <div class="mensaje-burbuja">
                <p>${mensaje.contenido}</p>
                <span class="mensaje-fecha">${mensajeTime}</span>
            </div>
        </div>
    `;

    return mensajeHtml; 
}
    // Función para manejar la actualización de un mensaje dado su ID
    function updateMessageHtml(msgId, $msgElement, dataToSend) {
        $msgElement.addClass('processing-update');

        $.ajax({
            url: '/api2/msgatender',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dataToSend), 
            success: function(response) {
                var nuevoMensaje = (typeof response === 'string') ? JSON.parse(response) : response;
                
                if (nuevoMensaje && parseInt(nuevoMensaje.id) === parseInt(msgId)) {
                    var nuevoHtml = renderMessage(nuevoMensaje);
                    var $newElement = $(nuevoHtml);
                    
                    $msgElement.replaceWith($newElement);
                } else {
                     console.warn("Respuesta de actualización inesperada para ID:", msgId);
                }
            },
            error: function(xhr) {
                console.error("Error al actualizar el mensaje:", xhr.status, xhr.responseText);
            },
            complete: function() {
                $('.mensaje-fila[data-msg-id="' + msgId + '"]').removeClass('processing-update');
            }
        });
    }

    /**
     * Función unificada para mostrar el menú contextual.
     */
    function showContextMenu($msgElement, e) {
        $currentMsgId = $msgElement.data('msg-id'); 

        var isAtendido = $msgElement.hasClass('atendido');
        var esRecibido = $msgElement.hasClass('recibido');

        if (esRecibido) {
            $contextToggleAction.text(isAtendido ? '❌ Desmarcar como Atendido' : '✅ Marcar como Atendido');
            $contextToggleAction.show();
        } else {
            $contextToggleAction.hide();
        }

        $contextMenu.css({
            top: e.pageY + 'px',
            left: e.pageX + 'px'
        }).show();
    }
    
    // --- LÓGICA DE EVENTOS DELEGADOS ---

    // 1. CLIC SIMPLE (Escritorio/Móvil) para abrir el Menú Contextual
    $mensajesCuerpo.on('click', '.mensaje-fila', function(e) {
        e.preventDefault(); 
        e.stopPropagation();
        
        showContextMenu($(this), e); 
    });

    // 2. CLIC DERECHO (Escritorio) para abrir el Menú Contextual
    $mensajesCuerpo.on('contextmenu', '.mensaje-fila', function(e) {
        e.preventDefault(); 
        e.stopPropagation();
        
        showContextMenu($(this), e); 
    });

    // --- Lógica del Menú Contextual (Acciones) ---

    // Ocultar menú al hacer clic fuera
    $(document).on('click', function(e) { 
        if (!$(e.target).closest('#message-context-menu').length) {
            $contextMenu.hide();
            if ($currentMsgId !== null) {
                $currentMsgId = null;
            }
        }
    });

    // Acción de Marcar/Desmarcar como Atendido
    $(document).on('click', '#context-action-toggle', function(e) {
        e.stopPropagation(); 
        
        if (!$currentMsgId) return;

        var $msgElement = $('.mensaje-fila[data-msg-id="' + $currentMsgId + '"]');
        var isCurrentlyAtendido = $msgElement.hasClass('atendido');

        var datosAtender = { 
            id_mensaje: $currentMsgId,
            user_id: currentUserId, 
            action: isCurrentlyAtendido ? 'desatender' : 'atender'
        };
        
        updateMessageHtml($currentMsgId, $msgElement, datosAtender);

        $contextMenu.hide();
        $currentMsgId = null;
    });

    // Acción de Refrescar Estado (Manual)
    $(document).on('click', '[data-action="refresh"]', function(e) { 
        e.stopPropagation();
        
        if (!$currentMsgId) return;
        
        var $msgElement = $('.mensaje-fila[data-msg-id="' + $currentMsgId + '"]');
        updateMessageHtml($currentMsgId, $msgElement, { id_mensaje: $currentMsgId });
        
        $contextMenu.hide();
        $currentMsgId = null;
    });
 

    // --- Polling (Consulta Periódica) ---

    function pollNewMessages() {
    
    var visibleMsgsStatus = $('.mensaje-fila').map(function() {
        var $msgElement = $(this);
        var isAtendido = $msgElement.hasClass('atendido');
        
        return {
            id: $msgElement.data('msg-id'),
            atendido: isAtendido ? 't' : 'f' 
        };
    }).get();

    var dataToSend = { 
        lastMsgId: lastMsgId,
        visibleMsgsStatus: visibleMsgsStatus,
        // 🔑 CAMBIO CLAVE: Incluir la ID del rol (ID_REMITENTE)
        rolId: ID_REMITENTE
    };

    $.ajax({
        url: '/api2/getnewmsg',
        type: 'POST',
        contentType: 'application/json', 
        data: JSON.stringify(dataToSend),
        success: function(response) {
            // ... (Lógica de procesamiento de la respuesta) ...

            var datos = (typeof response === 'string') ? JSON.parse(response) : response;
            
            var mensajesNuevos = datos.nuevos || [];
            var mensajesActualizados = datos.actualizados || [];
            
            // ... (Lógica de actualización de DOM) ...

            if (mensajesActualizados.length > 0) {
                mensajesActualizados.forEach(function(mensaje) {
                    var msgId = mensaje.id;
                    var $msgElement = $('.mensaje-fila[data-msg-id="' + msgId + '"]');
                    
                    if ($msgElement.length) {
                        var nuevoHtml = renderMessage(mensaje);
                        var $newElement = $(nuevoHtml);
                        $msgElement.replaceWith($newElement);
                    }
                });
            }

            if (mensajesNuevos.length > 0) {
                mensajesNuevos.forEach(function(mensaje) {
                    $mensajesCuerpo.append(renderMessage(mensaje));
                });

                lastMsgId = mensajesNuevos[mensajesNuevos.length - 1].id;
                $mensajesCuerpo.scrollTop($mensajesCuerpo.prop("scrollHeight"));
            }
            
            setTimeout(pollNewMessages, 3000); 
        },
        error: function(xhr) {
            console.error("Error en la consulta AJAX de mensajes:", xhr.status, xhr.responseText);
            
            setTimeout(pollNewMessages, 3000); 
        }
    });
}

    // --- Inicialización y Eventos ---

    // Scroll inicial
    if ($mensajesCuerpo.length) {
        $mensajesCuerpo.scrollTop($mensajesCuerpo.prop("scrollHeight"));
    }
    checkDestinatario();
    
    // 🔑 INICIAR EL POLLING INMEDIATAMENTE: Ejecutamos la función directamente.
    pollNewMessages(); 
    
    // Lógica para selección única de destinatario
    $checkboxes.on('change', function() {
        var $this = $(this);
        var isChecked = $this.prop('checked');
        
        if (isChecked) {
            $checkboxes.not(this).prop('checked', false);
            $('#error-destinatario').hide();
        }

        checkDestinatario();
    });

    // Lógica de Pestañas
    $tabButtons.on('click', function() {
        var tabId = $(this).data('tab');

        $tabButtons.removeClass('active');
        $tabContents.addClass('hidden');

        $(this).addClass('active');
        $('#tab-' + tabId).removeClass('hidden');
    });
    
    // Lógica de Plantillas
    $plantillas.on('click', function() {
        var plantillaTexto = $(this).data('plantilla');
        
        $inputMensaje.val(plantillaTexto);
        
        $inputMensaje.focus();
        if ($inputMensaje.length) {
            var val = $inputMensaje.val();
            $inputMensaje[0].setSelectionRange(val.length, val.length);
        }
    });


    // Envío del formulario por AJAX
    $('#form-mensaje').on('submit', function(e) {
        e.preventDefault(); 

        var $form = $(this);
        var mensajeContenido = $inputMensaje.val().trim();
        var $destinatarioSeleccionado = $checkboxes.filter(':checked');

        if (mensajeContenido === "" || $destinatarioSeleccionado.length !== 1) {
            alert("Error de validación.");
            return;
        }
        
        var id_rol = null;
        var id_team = null;

        $('input[name="id_rol"]').each(function(index) {
        if ($(this).is(':checked')) {
            id_rol = $(this).val();
            }
        });

        $('input[name="id_team"]').each(function(index) {
        if ($(this).is(':checked')) {
            id_team = $(this).val();
            }
        });
        var datosAEnviar = {
            id_remitente: ID_REMITENTE,
            id_rol: id_rol || null,
            id_team: id_team || null,
            mensaje: mensajeContenido
        };

        $botonEnviar.prop('disabled', true).text('Enviando...');

        $.ajax({
            url: '/api2/mensaje',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(datosAEnviar),
            success: function(respuesta) {
                
                var datos = typeof respuesta === 'string' ? JSON.parse(respuesta) : respuesta;
                var id = datos.id_mensaje;
                var destinatario = datos.destinatario;
                var simulatedMessage = {
                    id: id,
                    contenido: datosAEnviar.mensaje,
                    nombre_par: destinatario,
                    enviado: true,
                    fecha: new Date().toISOString(),
                    atendido: null,
                    user_atendido: null
                };
                $mensajesCuerpo.append(renderMessage(simulatedMessage));
                lastMsgId = simulatedMessage.id;
                
                $mensajesCuerpo.scrollTop($mensajesCuerpo.prop("scrollHeight"));

                $inputMensaje.val('');
                $destinatarioSeleccionado.prop('checked', false);
                
                $botonEnviar.text('Enviar');
                checkDestinatario();
            },
            error: function(xhr) {
                alert("❌ Error al enviar el mensaje. Revisa la consola para más detalles.");
                console.error("Error AJAX:", xhr.status, xhr.responseText);
                $botonEnviar.text('Enviar');
                checkDestinatario();
            }
        });
    });
});