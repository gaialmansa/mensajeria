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
 * Class ReportBlockDateRange
 *
 * Bloque para solicitar un rango de fechas
 *
 * @package zfx
 */
class ReportBlockDateRange extends ReportBlock
{

    // --------------------------------------------------------------------

    public function __construct($varStartDate, $varEndDate)
    {
        $this->data['_varStartDate'] = $varStartDate;
        $this->data['_varEndDate']   = $varEndDate;
    }

    // --------------------------------------------------------------------

    public function mapVars(array $data)
    {
        $this->data['_valStartDate'] = a($data, $this->data['_varStartDate']);
        $this->data['_valEndDate']   = a($data, $this->data['_varEndDate']);
    }

    // --------------------------------------------------------------------

    public function getViewFile()
    {
        return 'zaf/' . Config::get('admTheme') . '/report/block-daterange';
    }

    // --------------------------------------------------------------------

}
