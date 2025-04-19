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

/*
 * En un servidor Debian el usuario www-data que es el que habitualmente ejecuta Apache2 debe tener en su $HOME
 * los directorios .cache y .config con los permisos adecuados para que se pueda ejecutar libreoffice.
 */

namespace zfx;

abstract class LatexReport extends Report
{

    protected $file;
    public    $iterations;
    public    $landscape;

    public function __construct()
    {
        parent::__construct();
        $this->file       = NULL;
        $this->iterations = 1;
        $this->landscape  = FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * @param int $outputFormat
     */
    public function setOutputFormat($outputFormat)
    {
        if ((int)$outputFormat == self::FORMAT_PDF) {
            $this->outputFormat = (int)$outputFormat;
        }
    }

    // --------------------------------------------------------------------

    public function templateOpen() // Esto no vale para nada
    {
        $outputDirectory = $this->getOutputDirectory();
        $outputName      = $this->getOutputName();

        @unlink("{$outputDirectory}{$outputName}.tex");
        @unlink("{$outputDirectory}{$outputName}.log");
        @unlink("{$outputDirectory}{$outputName}.pdf");
        $this->file = fopen("{$outputDirectory}{$outputName}.tex", 'wb');

        $landscape = '';
        if ($this->landscape == TRUE) {
            $landscape = ',landscape';
        }

        // La cabecera
        $ltxHeader = '\\documentclass[a4paper,10pt' . $landscape . ']{report}
\\usepackage[spanish,es-tabla]{babel}
\\usepackage[margin=5mm]{geometry}
\\usepackage[utf8]{inputenc}
\\usepackage[scaled]{helvet}
\\renewcommand{\\familydefault}{\\sfdefault}
\\usepackage[T1]{fontenc}
\\usepackage{array}
\\usepackage{longtable}
\\usepackage{booktabs}
\\usepackage{eurosym}
\\usepackage{hyperref}
\\usepackage{graphicx}
\\begin{document}
';
        fwrite($this->file, $ltxHeader);
    }

    // --------------------------------------------------------------------

    public function templateClose()
    {
        $outputDirectory = $this->getOutputDirectory();
        $outputName      = $this->getOutputName();

        // Terminamos
        fwrite($this->file, "\n" . '\\end{document}');
        fclose($this->file);
        @ob_start();
        $command = "pdflatex -interaction=batchmode -output-directory=$outputDirectory $outputDirectory/$outputName.tex";
        for ($i = 1; $i <= $this->iterations; $i++) {
            exec($command);
        }
        @ob_end_clean();
    }

    // --------------------------------------------------------------------

}
