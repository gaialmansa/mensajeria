<?php
/*
 * Fichero de configuración local usado por ZAF, la uso en el propio desarrollo de ZAF.
 * OJO!!!!!  Este fichero NO se debe incluir en implantaciones o actualizaciones de ningún tipo.
 */

 $cfg['rootUrl']    = 'http://localhost/mensajeria/';

$cfg['pg']         = array(
    'default' => array(
        'dbHost'     => 'localhost',
        'dbUser'     => 'pofenas',
        'dbPass'     => 'botijo',
        'dbDatabase' => 'mensajeria',
        'dbPort'     => '5432'
    )
);
$cfg['showErrors'] = TRUE;