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

use zfx\Localizer;

include_once('Abs_SessionController.php');

abstract class Abs_AdmAjaxController extends Abs_SessionController
{

    /**
     * Localizer instance
     * @var Localizer
     */
    protected $loc;

    public function __construct()
    {
        parent::__construct();


        // Is there a logged user?
        $user = $this->_getUser();

        // We really need it
        if (!$user) {
            die;
        }

        // No permissions?
        if (!$this->_checkPermission()) {
            die;
        }

        // Get user or system language
        $this->loc = new Localizer($user->getLanguage());
    }
    // --------------------------------------------------------------------

    /**
     * All registered users are able to get in by default
     * @return boolean
     */
    protected function _checkPermission()
    {
        if ($this->_getUser()) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }


    // --------------------------------------------------------------------

    // @@@ TODO ¿En qué uso esto?
    protected function responseActionLoad($url, $selector)
    {
        echo "<script>actionLoad('$url', '$selector');</script>";
    }
    // --------------------------------------------------------------------
}
