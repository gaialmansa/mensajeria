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

class FieldViewInfo extends FieldView
{

    protected $infoText = '';

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvInfo';
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $data = array(
            'fv' => $this
        );
        if ($this->getDisplayOnly()) {
            View::direct('zaf/' . Config::get('admTheme') . '/fieldviews/display/info', $data);
        }
        else {
            View::direct('zaf/' . Config::get('admTheme') . '/fieldviews/edit/info', $data);
        }
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'inf';
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getInfoText(): string
    {
        return $this->infoText;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $infoText
     */
    public function setInfoText(string $infoText): void
    {
        $this->infoText = $infoText;
    }

    // --------------------------------------------------------------------


}
