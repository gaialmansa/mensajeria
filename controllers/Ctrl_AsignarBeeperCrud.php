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
use function zfx\va;

include_once('Abs_AppCrudController.php');

class Ctrl_AsignarBeeperCrud extends Abs_AppCrudController
{

    protected function initData()
    {
        $this->auto('beepers');
        $this->lstHideFields = array('id_beeper');
        $this->relName('beepers_id_usuario_fkey','nombre');
        
    }
    // --------------------------------------------------------------------

}