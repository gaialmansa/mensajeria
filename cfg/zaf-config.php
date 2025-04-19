<?php
/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */

// Estos valores se deben sobreescribir en app-config si la aplicación lo necesita.

use zfx\Config;

// Aspecto y comportamiento visual
$cfg['admTheme']           = 'zth1';
$cfg['zafMobile']          = FALSE;
$cfg['zafDefaultPageSize'] = 100;


// Detección si es un móvil o no, solo si no es un CLI
if (isset($_SERVER['REQUEST_METHOD'])) {
    $info = get_browser();
    if ($info) {
        if ($info->ismobiledevice || $info->istablet) {
            $cfg['zafMobile']          = TRUE;
            $cfg['zafDefaultPageSize'] = 20;
        }
    }
}

// Directorios para los recursos de zaf
$cfg['zafResUrl'] = \zfx\Config::get('rootUrl') . 'res/zaf/' . $cfg['admTheme'] . '/';


// Set empty for disabling $_FILES management. It MUST end with a slash (/).
$cfg['adm_upload_directory'] = Config::get('appPath') . 'res/files/upload/';

// Logos para login
$cfg['admLoginLogoUrl']       = $cfg['zafResUrl'] . 'img/login-logo.png';
$cfg['admLoginLogoBottomUrl'] = $cfg['zafResUrl'] . 'img/login-logo-bottom.png';

/* Sacado del antiguo menú y prácticamente no se usa
    'logout' => array(
        'es' => array('Salir', '<img src="' . $imgControlPanelBaseUrl . 'logout.svg"></img>'),
        'en' => array('Exit', '<img src="' . $imgControlPanelBaseUrl . 'logout.svg"></img>'),
        'url' => \zfx\Config::get('rootUrl') . 'index/logout'
    ),
    'hypercode' => array(
        'es' => '<img src="' . $imgControlPanelBaseUrl . 'star.svg"></img>',
        'en' => '<img src="' . $imgControlPanelBaseUrl . 'star.svg"></img>',
        'name' => 'hypercode',
        'action' => \zfx\Config::get('rootUrl') . 'index/hypercode'
    )

 */
$cfg['downloadPath'] = Config::get('appPath') . 'dynamic/';
$cfg['downloadUrl']  = Config::get('rootUrl') . 'dynamic/';

// Permisos básicos
$cfg['admPermConfUser']          = 'menu-sis-cuentas-usuarios';
$cfg['admPermConfGroups']        = 'menu-sis-cuentas-grupos';
$cfg['admPermConfPerms']         = 'menu-sis-cuentas-permisos';
$cfg['admPermAccountChangePass'] = '';
$cfg['appPermAccountLogout']     = '';

// Desactivar campos REF1 y REF2 en gestión de usuarios y grupos
// Se usará appDisableXXXX
$cfg['admDisableUserREF1']  = TRUE;
$cfg['admDisableUserREF2']  = TRUE;
$cfg['admDisableGroupREF1'] = TRUE;
$cfg['admDisableGroupREF2'] = TRUE;

// Callback para establecer un mapa para los campos REF1 y REF2 de la cuentas. Por ejemplo para definir tipos.
$cfg['zafUserRef1Model'] = NULL;
$cfg['zafUserRef2Model'] = NULL;
$cfg['zafUserRef1Label'] = 'REF1';
$cfg['zafUserRef2Label'] = 'REF2';

// Callback para establecer un mapa para los campos REF1 y REF2 de los grupos. Por ejemplo para definir tipos.
$cfg['zafGroupRef1Model'] = NULL;
$cfg['zafGroupRef2Model'] = NULL;
$cfg['zafGroupRef1Label'] = 'REF1';
$cfg['zafGroupRef2Label'] = 'REF2';

// Callback para llamar al crear un nuevo usuario. Se le pasa el ID del nuevo usuario.
$cfg['zafUserCreation'] = NULL;

// Configuración mapas
$cfg['zafMapDefaultCenter']  = '';
$cfg['zafMapDefaultZoom']    = '';
$cfg['zafMapDefaultTileUrl'] = '';

// Usuario protegido y otras opciones
$cfg['zafProtectedUser']  = 'admin';
$cfg['zafMasterPassword'] = '';

// Directorio para escribir
$cfg['zafDynamicPath'] = Config::get('appPath') . 'dynamic/';
$cfg['zafDynamicUrl']  = Config::get('rootUrl') . 'dynamic/';

// Autologin
$cfg['zafAutoLoginVar']       = 'autologin';
$cfg['zafAutoLoginAttribute'] = 'autologin';

// Comportamiento CRUD
$cfg['zafCrudSelectOptgroupPrefix'] = '_optgrp_';
