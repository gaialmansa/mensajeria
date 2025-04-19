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

class ExcelReport extends Report
{


    public function __construct()
    {
        parent::__construct();
        $this->outputFormat     = self::FORMAT_XLSX;
        $this->templateRequired = FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * @param int $outputFormat
     */
    public function setOutputFormat($outputFormat)
    {
    }

    // --------------------------------------------------------------------
    public function templateOpen()
    {
    }


    // --------------------------------------------------------------------

    public function templateClose()
    {
        // Esto tiene que devolver el fichero para luego ser descargado o lo que sea.
        return $this->getOutputDirectory() . $this->getOutputName() . '.' . $this->getOutputExtension();
    }

    // --------------------------------------------------------------------

    public function templateWrite()
    {
        // Aquí generamos el EXCEL, pero no le damos la orden de bajar, solo lo componemos y lo guardamos en disco.
        // Montar el array
        $excel = \SimpleXLSXGen::fromArray($this->data);
        if ($this->merge) {
            foreach ($this->merge as $m) {
                $excel->mergeCells($m);
            }
        }

        $excel->saveAs($this->getOutputDirectory() . $this->getOutputName() . '.' . $this->getOutputExtension());
    }

    // --------------------------------------------------------------------

}
