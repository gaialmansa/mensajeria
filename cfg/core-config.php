<?php
/*
 * Fichero de configuración para ZFX proporcionado por ZAF
 *
 * SE RECOMIENDA NO MODIFICAR para evitar problemas al actualizar ZWF o ZAF.
 * Si se quiere modificar algo de aquí, se recomienda hacerlo en app-preconfig, local-config o app-config.
 */


$cfg['autoLoadConfig']     = [
    'zaf-config',       // Configuración principal de ZAF
    'app-preconfig',    // Configuración principal de la Aplicación
    'local-config',     // Configuración local de la Aplicación, fuera de código, que puede sobreescribir app-preconfig
    'app-config',       // Configuración secundaria de la Aplicación que necesita hacer uso de algún valor de local-config
    'zaf-menu',         // Configuración de menú de ZAF
    'app-menu',         // Configuración de menú de la Aplicación
    'zaf-last'          // Configuración final de ZAF
];
$cfg['enabledCoreModules'] = [
    'zfx' => [
        'core',
        'dev',
        'data-access',
        'data-source',
        'data-view',
        'data-handler',
        'zaf'
    ]
];
