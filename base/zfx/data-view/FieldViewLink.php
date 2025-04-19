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


/**
 * @package data-view
 */

namespace zfx;

class FieldViewLink extends FieldViewString
{

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('link', $value);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvLink';
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 's';
    }
    // --------------------------------------------------------------------
}
