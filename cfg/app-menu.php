<?php
/*
 * Fichero de menú de la aplicación
 */
//echo('<pre>');
// print_r(\zfx\Config::getInstance());
// die;

$menu = \zfx\Config::get('admMenu');



// Grupo Cuenta. Aquí uno puede cambiar su contraseña, salir, etc.
$pofemenu = array(
    'es'       => array('Mensajería', '<i class="fas fa-comments"></i> '),
    'perm'     => '',
    'sections' => array(
        'pofemenu-tablas' => array(
            'es'          => array('Mensajería - Administración', '<i class="fas fa-comments"></i> '),
            'perm'        => 'administracion_mensajeria',
            'subsections' => array
            (
                'pofemenu-tablas-roles' => array(
                    'es'         => array('Roles', '<i class="fas fa-user-nurse"></i>'),
                    'controller' => 'roles',
                    'perm'       => 'menu-mensajeria-usuarios',
                ),
                'pofemenu-tablas-equipos' => array(
                    'es'         => array('Equipos', '<i class="fas fa-users"></i>'),
                    'controller' => 'equipos',
                    'perm'       => '|',
                ),
                'pofemenu-enviar-mensaje' => array(
                    'es'         => array('Enviar mensaje', '<i class="fas fa-pager"></i>'),
                    'controller' => 'enviar-mensaje',
                    'perm'       => '|',
                )
            )
        ),
    )
);


$menu['pofemenu'] = $pofemenu;





$cfg['appMenu'] = $menu;
