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

/**
 * String Map Field View
 */
class FieldViewReal extends FieldViewString
{

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('real', $value, ['pk' => $packedPK]);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvReal';
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'f';
    }
    // --------------------------------------------------------------------
}
