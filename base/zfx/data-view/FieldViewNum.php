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
 * Num Field View
 */
class FieldViewNum extends FieldViewString
{
    protected $precision  = 6;
    protected $separation = FALSE;

    /**
     * @return bool
     */
    public function getSeparation(): bool
    {
        return $this->separation;
    }

    /**
     * @param bool $separation
     */
    public function setSeparation(bool $separation): void
    {
        $this->separation = $separation;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('num', $value, ['pk' => $packedPK]);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvNum';
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'f';
    }

    // --------------------------------------------------------------------

    /**
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    // --------------------------------------------------------------------

    /**
     * @param int $precision
     */
    public function setPrecision(int $precision): void
    {
        $this->precision = $precision;
    }

    // --------------------------------------------------------------------
}
