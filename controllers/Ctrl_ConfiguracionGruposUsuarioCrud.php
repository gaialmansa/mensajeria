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

include_once('Abs_AppCrudController.php');

class Ctrl_ConfiguracionGruposUsuarioCrud extends Abs_AppCrudController
{

    protected function initData()
    {
        // Required
        $this->auto('zfx_user_group');
        // Por favor, hacer que la descripción de un grupo sea NOT NULL.
        $this->relName('zfx_user_group_relGroup', 'description');
        $this->relName('zfx_user_group_relUser', 'login');

        // Field translations
        $this->tableView->setLabels(array(
            'id_user'  => $this->loc->getString('ctl', 'col-usergroups-id_user'),
            'id_group' => $this->loc->getString('ctl', 'col-usergroups-id_group')
        ));


    }

    // --------------------------------------------------------------------


    protected function _checkPermission()
    {
        $res = parent::_checkPermission();
        if (!$res) {
            return FALSE;
        }
        return ($this->_getUser()->checkMenuPermission(Config::get('appPermConfUser')));
    }
    // --------------------------------------------------------------------
}
