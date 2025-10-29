<?php
// ==========================================================
// Variables de ejemplo (DEBEN ser provistas por el Controlador)
// ==========================================================
/*$mensajes = [
    ['id' => 1, 'contenido' => 'Hola, ¿cómo estás? Esto es un mensaje enviado por mí.', 'enviado' => true, 'nombre_par' => 'Equipo Desarrollo', 'fecha' => '2025-10-26 10:00:00', 'atendido' => '2025-10-26 10:01:00', 'user_atendido' => 'Sistema A.'], 
    ['id' => 2, 'contenido' => 'Estoy bien, gracias. ¿Y tú? Este es un mensaje que he recibido.', 'enviado' => false, 'nombre_par' => 'Juan Pérez (Admin)', 'fecha' => '2025-10-26 10:05:00', 'atendido' => null, 'user_atendido' => null],
    ['id' => 3, 'contenido' => 'Todo genial. ¿Hablamos de la reunión de mañana?', 'enviado' => true, 'nombre_par' => 'Rol: Técnico', 'fecha' => '2025-10-26 10:15:00', 'atendido' => null, 'user_atendido' => null],
    ['id' => 4, 'contenido' => 'Claro, dime a qué hora. Me parece una excelente idea.', 'enviado' => false, 'nombre_par' => 'María García', 'fecha' => '2025-10-26 10:20:00', 'atendido' => '2025-10-26 10:25:00', 'user_atendido' => 'Soporte B.'],
    ['id' => 1, 'contenido' => 'Hola, ¿cómo estás? Esto es un mensaje enviado por mí.', 'enviado' => true, 'nombre_par' => 'Equipo Desarrollo', 'fecha' => '2025-10-26 10:00:00', 'atendido' => '2025-10-26 10:01:00', 'user_atendido' => 'Sistema A.'], 
    ['id' => 2, 'contenido' => 'Estoy bien, gracias. ¿Y tú? Este es un mensaje que he recibido.', 'enviado' => false, 'nombre_par' => 'Juan Pérez (Admin)', 'fecha' => '2025-10-26 10:05:00', 'atendido' => null, 'user_atendido' => null],
    ['id' => 3, 'contenido' => 'Todo genial. ¿Hablamos de la reunión de mañana?', 'enviado' => true, 'nombre_par' => 'Rol: Técnico', 'fecha' => '2025-10-26 10:15:00', 'atendido' => null, 'user_atendido' => null],
    ['id' => 4, 'contenido' => 'Claro, dime a qué hora. Me parece una excelente idea.', 'enviado' => false, 'nombre_par' => 'María García', 'fecha' => '2025-10-26 10:20:00', 'atendido' => '2025-10-26 10:25:00', 'user_atendido' => 'Soporte B.'],
];*/
/*$equipos = [
    ['id' => 201, 'nombre' => 'Equipo Desarrollo'],
    ['id' => 202, 'nombre' => 'Equipo Soporte'],
    ['id' => 203, 'nombre' => 'Equipo Ventas'],
];*/

// Plantillas de Mensajes
$plantillas = [
    'He pedido placas/mandado analitica/puesto tratamiento a ',
    'Hay que quitar la via a ',
    
];

$id_remitente = 1;

$last_msg_id = 0;
if (!empty($mensajes)) {
    $last_message = end($mensajes);
    $last_msg_id = $last_message['id'];
}
// ==========================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat de Mensajería</title>
    <link rel="stylesheet" href="/res/css/chat.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="layout-chat">
    <aside class="sidebar-izquierda">
        <div class="logo-container">
            <img src="/res/img/logo.svg" alt="Logo Mensajería" class="logo-chat">
        </div>
        
        <hr class="separator-menu">

        <div class="menu-config">
            <a href="/index/chgrol" class="menu-link">Cambiar de Rol</a>
            <a href="/index/chgpsw" class="menu-link">Cambiar de contraseña</a>
        </div>
        
        <hr class="separator-menu">

        <div class="logout-container">
            <a href="/index/logout" title="Cerrar Sesión" class="logout-link">
                <img src="/res/img/icons/arrow-go-back-fill.svg" alt="Salir" class="icon-logout">
                <span class="logout-text">Cerrar sesión</span>
            </a>
        </div>
    </aside>

    <main class="chat-container" data-last-msg-id="<?= $last_msg_id ?>">
        <div id="mensajes-cuerpo" class="mensajes-cuerpo">
            <?php foreach ($mensajes as $mensaje): ?>
                <?php 
                    $clases_fila = $mensaje['enviado'] == 't' ? 'enviado' : 'recibido';
                    $es_atendido = !empty($mensaje['atendido']);
                    if ($es_atendido) {
                        $clases_fila .= ' atendido';
                    }
                ?>
                <div class="mensaje-fila <?= $clases_fila ?>" data-msg-id="<?= $mensaje['id'] ?>">
                    
                    <?php 
                        $atendido_html = '';
                        if ($es_atendido) {
                            $atendido_html = '<div class="atendido-info">';
                            $atendido_html .= 'Atendido por: ' . htmlspecialchars($mensaje['user_atendido']) . '<br>';
                            $atendido_html .= 'a las ' . date('H:i', strtotime($mensaje['atendido']));
                            $atendido_html .= '</div>';
                        }
                    ?>
                    
                    <div class="par-burbuja">
                        <?= htmlspecialchars($mensaje['nombre_par']) ?>
                        <?= $atendido_html ?>
                    </div>
                    
                    <div class="mensaje-burbuja">
                        <p><?= htmlspecialchars($mensaje['contenido']) ?></p>
                        <span class="mensaje-fecha"><?= date('H:i', strtotime($mensaje['fecha'])) ?></span>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        </div>

        <form id="form-mensaje" class="form-mensaje">
            <textarea id="input-mensaje" name="mensaje" placeholder="Escribe tu mensaje..." required></textarea>
            <input type="hidden" name="id_remitente" value="<?= $id_remitente ?>">
            <button type="submit" id="boton-enviar">Enviar</button>
        </form>
    </main>

    <aside class="destinatarios-columna">
        
        <div class="destinatarios-tabs">
            <div class="tab-header">
                <button class="tab-button active" data-tab="equipos">Equipos</button>
                <button class="tab-button" data-tab="roles">Roles</button>
            </div>
            
            <div id="tab-equipos" class="tab-content active">
                <h4>Seleccionar Equipo:</h4>
                <div class="opciones-grupo">
                    <?php foreach ($equipos as $equipo): ?>
                        <label>
                            <input type="checkbox" name="id_team" value="<?= $equipo['id_equipo'] ?>" data-tipo="equipo" class="destinatario-checkbox">
                            <?= htmlspecialchars($equipo['nombre']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="tab-roles" class="tab-content hidden">
                <h4>Seleccionar Rol:</h4>
                <div class="opciones-grupo">
                    <?php foreach ($roles as $rol): ?>
                        <label>
                            <input type="checkbox" name="id_rol" value="<?= $rol['id_rol'] ?>" data-tipo="rol" class="destinatario-checkbox">
                            <?= htmlspecialchars($rol['nombre']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <p id="error-destinatario" class="error-mensaje" style="display:none;">⚠️ Selecciona un único destinatario.</p>
        
        <div class="plantillas-container">
            <h4>Plantillas Rápidas:</h4>
            <div class="plantillas-grupo">
                <?php foreach ($plantillas as $plantilla): ?>
                    <div class="plantilla-texto" data-plantilla="<?= htmlspecialchars($plantilla) ?>">
                        <?= htmlspecialchars($plantilla) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </aside>
</div>
<div id="message-context-menu" class="context-menu" style="display:none;">
    <ul>
        <li data-action="refresh">Actualizar estado (Clic simple)</li>
        <li id="context-action-toggle">Marcar / Desmarcar como Atendido</li>
    </ul>
</div>
<script>
    // ESTO SE GENERA CON PHP ANTES DE LA EJECUCIÓN DEL JS
    const ID_REMITENTE = <?php echo json_encode($rolId); ?>; 
    const currentUserId = "<?php echo $userId; ?>"
</script>
<script src="/res/js/chat.js"></script>

</body>
</html>