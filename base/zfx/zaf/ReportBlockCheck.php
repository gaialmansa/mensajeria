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

namespace zfx;

/**
 * Class ReportBlockCheck
 *
 * Bloque para marcar un check
 *
 * @package zfx
 */
class ReportBlockCheck extends ReportBlock
{

    // --------------------------------------------------------------------

    public function __construct($varCheck, $title, $label)
    {
        $this->data['_varCheck']   = $varCheck;
        $this->data['_checkTitle'] = $title;
        $this->data['_checkLabel'] = $label;
    }

    // --------------------------------------------------------------------

    public function mapVars(array $data)
    {
        $this->data['_valCheck'] = a($data, $this->data['_varCheck']);
    }

    // --------------------------------------------------------------------

    public function getViewFile()
    {
        return 'zaf/' . Config::get('admTheme') . '/report/block-check';
    }

    // --------------------------------------------------------------------

}
