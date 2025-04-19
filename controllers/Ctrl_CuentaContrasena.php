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

use zfx\Config;
use function zfx\a;

include_once('Abs_AppController.php');

class Ctrl_CuentaContrasena extends Abs_AppController
{

    public function _main()
    {
        $msg = '';
        $err = '';
        if ($post = a($_SESSION, '_post')) {
            unset($_SESSION['_post']);
            $currentLogin = $this->_getUser()->getLogin();
            $res          = User::logIn($currentLogin, $post['c1']);
            if (!$res) {
                $err = "Contraseña actual incorrecta.";
            }
            else {
                if ($post['c2'] == '' || $post['c3'] == '') {
                    $err = "Contraseña especificada en blanco.";
                }
                elseif (mb_strlen($post['c2'], 'utf-8') < 6) {
                    $err = "Las contraseñas necesitan al menos 6 caracteres de longitud.";

                }
                elseif ($post['c2'] == $post['c3']) {
                    $this->_getUser()->setPassword($post['c2']);
                    $msg = 'Contraseña cambiada. Se ha cerrado la sesión.';
                    $this->_logout();
                }
                else {
                    $err = "Las nuevas contraseñas no son iguales.";
                }

            }
        }

        $this->_view->addSection('body', 'zaf/' . Config::get('admTheme') . '/cambio-contrasena', array('msg' => $msg, 'err' => $err));
        $this->_view->show();
    }

    // --------------------------------------------------------------------

    public function _getCurrentSection()
    {
        return 'configuracion';
    }

    // --------------------------------------------------------------------

    public function _getCurrentSubSection()
    {
        return 'contraseña';
    }

    // --------------------------------------------------------------------

}
