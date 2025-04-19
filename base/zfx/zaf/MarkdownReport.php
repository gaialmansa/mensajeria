<?php

/*
 * En un servidor Debian el usuario www-data que es el que habitualmente ejecuta Apache2 debe tener en su $HOME
 * los directorios .cache y .config con los permisos adecuados para que se pueda ejecutar libreoffice.
 */

namespace zfx;

class MarkdownReport extends Report
{
    public function setOutputFormat($outputFormat)
    {
        $this->outputFormat = (int)$outputFormat;
    }

    // --------------------------------------------------------------------

    public function templateOpen()
    {
    }

    // --------------------------------------------------------------------

    public function templateWrite()
    {
        $inputfile    = $this->getTemplatePath();
        $outputfilter = $this->getOutputFilter();
        $workingdir   = $this->getOutputDirectory();
        $outputfile   = $this->getOutputDirectory() . $this->getOutputName() . '.' . $this->getOutputExtension();
        @ob_start();
        $command = "cd $workingdir ; pandoc --quiet $outputfilter $inputfile -o $outputfile";
        $salida  = [];
        $codigo  = 0;
        exec($command, $salida, $codigo);
        @ob_end_clean();
//        \zfx\Debug::show($command);
//        \zfx\Debug::show($codigo);
//        \zfx\Debug::show($salida); die;
    }

    // --------------------------------------------------------------------

    public function templateClose()
    {
    }

    // --------------------------------------------------------------------

    private function getOutputFilter()
    {
        $filter = '';
        switch ($this->outputFormat) {
            case self::FORMAT_ODT:
            {
                $filter = '-t odt';
                break;
            }
            case self::FORMAT_DOC:
            case self::FORMAT_DOCX:
            {
                $filter = '-t docx';
                break;
            }
            case self::FORMAT_PDF:
            {
                $filter = '';
                break;
            }
            case self::FORMAT_HTML:
            {
                $filter = '-t html';
                break;
            }
        }
        return $filter;
    }

    // --------------------------------------------------------------------

}
