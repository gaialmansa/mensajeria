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

    // Asume que currentUserId está inyectado desde PHP (ver chat_view.php)
    // Usaremos la variable global inyectada en el HTML.
    // var currentUserId; // Ya está disponible si se inyectó correctamente

    var lastMsgId = parseInt($chatContainer.data('last-msg-id')) || 0;

    // --- Funciones de Control ---

    function checkDestinatario() {
        var destinatarioSeleccionado = $checkboxes.filter(':checked').length === 1;
        $botonEnviar.prop('disabled', !destinatarioSeleccionado);
    }

    // Función para renderizar un mensaje individual
function renderMessage(mensaje) {
    // 🔑 Paso 1: Determinar si es enviado ('t' o true)
    var isEnviado = (mensaje.enviado === true || mensaje.enviado === 't');
    
    var clasesFila = isEnviado ? 'enviado' : 'recibido';
    var atendidoHtml = '';

    // 🔑 Paso 2: Asignar el nombre basado en isEnviado
    // Si es enviado (t), usa nombre_par (destinatario).
    // Si NO es enviado (f), usa nombre_de (remitente).
    var nombreMostrar = isEnviado ? mensaje.nombre_par : mensaje.nombre_de;
    
    // Si el nombre real es null/undefined, usa un fallback (Opcional, pero bueno tenerlo)
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
                //alert(msgId === nuevoMensaje.id);
                if (nuevoMensaje && parseInt(nuevoMensaje.id) === parseInt(msgId)) {
                    var nuevoHtml = renderMessage(nuevoMensaje);
                    var $newElement = $(nuevoHtml);
                    
                    // Al reemplazar el elemento, el nuevo elemento ya NO tendrá la clase 'processing-update'
                    // y tampoco tendrá el estilo de opacidad, pero sí las clases 'atendido' o 'recibido' correctas.
                    $msgElement.replaceWith($newElement);
                    
                    // Ya que hemos reemplazado el elemento, es necesario reasignar los eventos
                    bindMessageClickEvents();
                } else {
                     console.warn("Respuesta de actualización inesperada para ID:", msgId);
                }
            },
            error: function(xhr) {
                console.error("Error al actualizar el mensaje:", xhr.status, xhr.responseText);
            },
            complete: function() {
                // No es necesario restaurar opacidad aquí, ya se reemplazó el elemento
            }
        });
    }

    /**
     * Añade los event listeners (clic simple y clic derecho) a los mensajes.
     */
    function bindMessageClickEvents() {
        $('.mensaje-fila').not('.bound').each(function() {
            var $msgElement = $(this);
            var msgId = $msgElement.data('msg-id');
            $msgElement.addClass('bound'); 
            
            // 1. Evento de Clic Simple (Actualización de estado general)
            $msgElement.on('click', function() {
                updateMessageHtml(msgId, $msgElement, { id_mensaje: msgId });
            });
            
            // 2. Evento de CLIC DERECHO (Mostrar menú contextual)
            $msgElement.on('contextmenu', function(e) {
                e.preventDefault(); 

                e.stopPropagation(); // Detiene la propagación del evento 'contextmenu'

                   $currentMsgId = $(this).data('msg-id');      
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
            });
        });
    }
    
    // --- Lógica del Menú Contextual ---

    // Ocultar menú al hacer clic fuera

    $(document).on('click', function(e) { 
    // Si el clic NO fue dentro del menú contextual, lo ocultamos y reseteamos.
    if (!$(e.target).closest('#message-context-menu').length) {
        $contextMenu.hide();
        
        if ($currentMsgId !== null) {
            $currentMsgId = null;
        }
    }
});

    $(document).on('click', '#context-action-toggle', function(e) {


    e.stopPropagation(); 
    
    
    if (!$currentMsgId) {
        
        return;
    }

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

// También para el otro botón del menú:
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
        $.ajax({
            url: '/index/getnewmsg',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
            data: { lastMsgId: lastMsgId },
            success: function(response) {
                var mensajesNuevos = (typeof response === 'string') ? JSON.parse(response) : response;
                
                if (mensajesNuevos && mensajesNuevos.length > 0) {
                    mensajesNuevos.forEach(function(mensaje) {
                        $mensajesCuerpo.append(renderMessage(mensaje));
                    });

                    lastMsgId = mensajesNuevos[mensajesNuevos.length - 1].id;
                    $mensajesCuerpo.scrollTop($mensajesCuerpo.prop("scrollHeight"));
                    
                    bindMessageClickEvents(); 
                }
            },
            error: function(xhr) {
                console.error("Error en la consulta AJAX de mensajes nuevos:", xhr.status, xhr.responseText);
            },
            complete: function() {
                setTimeout(pollNewMessages, 3000);
            }
        });
    }

    // --- Inicialización y Eventos ---

    // Scroll inicial y atado de eventos a mensajes iniciales
    if ($mensajesCuerpo.length) {
        $mensajesCuerpo.scrollTop($mensajesCuerpo.prop("scrollHeight"));
    }
    checkDestinatario();
    bindMessageClickEvents(); 

    // Iniciar el polling
    setTimeout(pollNewMessages, 3000);
    
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
        //var idRemitente = $form.find('input[name="id_remitente"]').val();
        var mensajeContenido = $inputMensaje.val().trim();
        var $destinatarioSeleccionado = $checkboxes.filter(':checked');

        if (mensajeContenido === "" || $destinatarioSeleccionado.length !== 1) {
            alert("Error de validación.");
            return;
        }
        // Inicializamos a null
        var id_rol = null;
        var id_team = null;

        // Verifica manualmente cada checkbox
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
            url: 'api2/mensaje',
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
                bindMessageClickEvents(); 

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