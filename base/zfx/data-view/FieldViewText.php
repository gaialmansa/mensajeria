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

class FieldViewText extends FieldViewString
{

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $value = StrFilter::filter($value, array('spaceClear', 'HTMLencode'));
        $this->renderView('text', $value);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        if ($this->displayLength == 0) {
            return '_fvText';
        }
        else {
            return '_fvTextLimited';
        }
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 's';
    }
    // --------------------------------------------------------------------
}
