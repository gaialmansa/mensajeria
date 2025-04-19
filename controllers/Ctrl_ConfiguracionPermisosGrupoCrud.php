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

class Ctrl_ConfiguracionPermisosGrupoCrud extends Abs_AppCrudController
{

    protected function initData()
    {
        // Required
        $this->auto('zfx_group_permission');
        $this->relName('zfx_group_permission_relPermission', 'description');

        // Field translations
        $this->tableView->setLabels(array(
            'id_group'      => $this->loc->getString('ctl', 'col-groupperms-id_group'),
            'id_permission' => $this->loc->getString('ctl', 'col-groupperms-id_permission')
        ));


    }

    // --------------------------------------------------------------------


    protected function _checkPermission()
    {
        $res = parent::_checkPermission();
        if (!$res) {
            return FALSE;
        }
        return ($this->_getUser()->checkMenuPermission(Config::get('appPermConfGroups')));
    }
    // --------------------------------------------------------------------
}
