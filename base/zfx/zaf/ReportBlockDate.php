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
 * Class ReportBlockDate
 *
 * Bloque para solicitar una fecha
 *
 * @package zfx
 */
class ReportBlockDate extends ReportBlock
{

    // --------------------------------------------------------------------

    public function __construct($varDate)
    {
        $this->data['_varDate'] = $varDate;
    }

    // --------------------------------------------------------------------

    public function mapVars(array $data)
    {
        $this->data['_valDate'] = a($data, $this->data['_varDate']);
    }

    // --------------------------------------------------------------------

    public function getViewFile()
    {
        return 'zaf/' . Config::get('admTheme') . '/report/block-date';
    }

    // --------------------------------------------------------------------

}
