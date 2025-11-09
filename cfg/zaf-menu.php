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

// Menú mínimo.

// Grupo sistema. Aquí se gestionan usuarios, grupos y permisos
use zfx\Config;

$groupSystem = array(
    'es'       => array('Sistema', '<i class="fas fa-server"></i> '),
    'en'       => array('System', '<i class="fas fa-server"></i> '),
    'sections' => array(
        'cuentas' => array(
            'es'          => array('Cuentas de acceso', '<i class="fas fa-wrench"></i> '),
            'en'          => array('System accounts', '<i class="fas fa-wrench"></i> '),
            'subsections' => array(
                'usuarios' => array(
                    'es'         => array('Usuarios', '<i class="fas fa-user"></i>'),
                    'en'         => array('Users', '<i class="fas fa-user"></i>'),
                    'perm'       => 'menu-sis-cuentas-permisos',
                    'controller' => 'configuracion-usuarios'
                ),
                'grupos'   => array(
                    'es'         => array('Grupos de usuarios', '<i class="fas fa-users"></i>'),
                    'en'         => array('User groups', '<i class="fas fa-users"></i>'),
                    'perm'       => 'menu-sis-cuentas-permisos',
                    'controller' => 'configuracion-grupos'
                ),
                'permisos' => array(
                    'es'         => array('Permisos', '<i class="fas fa-stamp"></i>'),
                    'en'         => array('Permissions', '<i class="fas fa-stamp"></i>'),
                    'perm'       => 'menu-sis-cuentas-permisos',
                    'controller' => 'configuracion-permisos'
                )
            )
        )
    )
);


// Grupo Cuenta. Aquí uno puede cambiar su contraseña, salir, etc.
$groupAccount = array(
    'es'       => array('Mi cuenta', '<i class="fas fa-user"></i> '),
    'en'       => array('My account', '<i class="fas fa-user"></i> '),
    'sections' => array(
        'configuracion' => array(
            'es'          => array('Configuración', '<i class="fas fa-wrench"></i> '),
            'en'          => array('Configuration', '<i class="fas fa-wrench"></i> '),
            'subsections' => array(
                'contraseña' => array(
                    'es'         => array('Cambiar contraseña', '<i class="fas fa-passport"></i>'),
                    'en'         => array('Change password', '<i class="fas fa-passport"></i>'),
                    'controller' => 'cuenta-contrasena',
                    'perm'       => Config::get('appPermAccountChangePass'),
                )
            )
        ),
        'logout'        => array(
            'es'   => array('Desconectar', '<i class="fas fa-door-open"></i> '),
            'en'   => array('Log out', '<i class="fas fa-door-open"></i> '),
            'url'  => Config::get('rootUrl') . 'index/logout/',
            'perm' => Config::get('appPermAccountLogout'),
        )
    )
);


// Menu básico de ejemplo. Realmente se usa appMenu, así que en app-menu admMenu es usado para referencia.
$cfg['admMenu'] = array(
    'system'  => $groupSystem,
    'account' => $groupAccount
);
$cfg['appMenu'] = $cfg['admMenu'];

// El enlace de la casita
$cfg['admHomeUrl'] = Config::get('rootUrl');
