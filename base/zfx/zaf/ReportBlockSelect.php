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
 * Class ReportBlockSelect
 *
 * Bloque para solicitar una elección a partir de una lista (ejemplo dropdown)
 *
 * @package zfx
 */
class ReportBlockSelect extends ReportBlock
{

    // --------------------------------------------------------------------

    public function __construct($varList, $varValue, $title, $label)
    {
        $this->data['_varList']  = $varList;
        $this->data['_varValue'] = $varValue;
        $this->data['_selTitle'] = $title;
        $this->data['_selLabel'] = $label;
    }

    // --------------------------------------------------------------------

    public function mapVars(array $data)
    {
        $this->data['_valList'] = a($data, $this->data['_varList']);
        $this->data['_value']   = a($data, $this->data['_varValue']);
    }


    // --------------------------------------------------------------------

    public function getViewFile()
    {
        return 'zaf/' . Config::get('admTheme') . '/report/block-select';
    }

    // --------------------------------------------------------------------

}
