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
 * Class Report
 *
 * Clase abstracta que sirve de base para informes como OdtReport o LatexReport.
 * @package zfx
 */
abstract class Report
{

    const FORMAT_min  = 1;
    const FORMAT_ODT  = 1;
    const FORMAT_DOC  = 2;
    const FORMAT_DOCX = 3;
    const FORMAT_HTML = 4;
    //...
    const FORMAT_XLSX = 50;
    // ....
    const FORMAT_PDF = 100;
    const FORMAT_max = 100;


    // Acciones: qué hacer con el informe
    const ACTION_min      = 1;
    const ACTION_VIEW     = 1;
    const ACTION_DOWNLOAD = 2;
    const ACTION_SHARE    = 3;
    const ACTION_max      = 3;


    const TYPE_ALL = 0; // Todos
    const TYPE_DOC = 1; // Documentos de texto como PDF o Word
    const TYPE_TAB = 2; // Datos tabulados como Excel, CSV, etc.


    protected $outputDirectory;
    protected $outputName;
    protected $outputFormat;
    protected $templatePath;
    protected $forceDownload;


    public $reg;
    public $data;
    public $summ;
    public $error;
    public $templateRequired;
    public $merge;

    public function __construct()
    {
        $this->setOutputDirectory(Config::get('downloadPath'));
        $this->outputName    = '';
        $this->outputFormat  = self::FORMAT_PDF;
        $this->templatePath  = '';
        $this->forceDownload = TRUE;
        $this->error         = '';
        $this->reg           = [];
        $this->data          = [];
        $this->summ          = [];
        $this->merge         = [];
        // Por defecto la plantilla es requerida
        $this->templateRequired = TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getOutputDirectory()
    {
        return $this->outputDirectory;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $outputDirectory
     */
    public function setOutputDirectory($outputDirectory)
    {
        $this->outputDirectory = (string)$outputDirectory;
        if (substr($this->outputDirectory, -1) != DIRECTORY_SEPARATOR) {
            $this->outputDirectory = $this->outputDirectory . DIRECTORY_SEPARATOR;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getOutputName()
    {
        if ($this->outputName === '') {
            if ($this->templatePath !== '') {
                $parts            = pathinfo($this->templatePath);
                $this->outputName = (string)a($parts, 'filename');
            }
        }
        return $this->outputName;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $outputName
     */
    public function setOutputName($outputName)
    {
        $this->outputName = (string)$outputName;
    }

    // --------------------------------------------------------------------

    /**
     * @return int
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    // --------------------------------------------------------------------

    /**
     * @param int $outputFormat
     */
    public abstract function setOutputFormat($outputFormat);

    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isForceDownload()
    {
        return $this->forceDownload;
    }

    // --------------------------------------------------------------------

    /**
     * @param bool $forceDownload
     */
    public function setForceDownload($forceDownload)
    {
        $this->forceDownload = (bool)$forceDownload;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = (string)$templatePath;
    }

    // --------------------------------------------------------------------

    public function getOutputExtension()
    {
        switch ($this->outputFormat) {
            case self::FORMAT_ODT:
            {
                $ext = 'odt';
                break;
            }
            case self::FORMAT_DOC:
            {
                $ext = 'doc';
                break;
            }
            case self::FORMAT_DOCX:
            {
                $ext = 'docx';
                break;
            }
            case self::FORMAT_PDF:
            {
                $ext = 'pdf';
                break;
            }
            case self::FORMAT_HTML:
            {
                $ext = 'html';
                break;
            }
            case self::FORMAT_XLSX:
            {
                $ext = 'xlsx';
                break;
            }
        }
        return $ext;
    }

    // --------------------------------------------------------------------

    public function getMimeType()
    {
        switch ($this->outputFormat) {
            case self::FORMAT_ODT:
            {
                $type = 'application/vnd.oasis.opendocument.text';
                break;
            }
            case self::FORMAT_DOC:
            {
                $type = 'application/msword';
                break;
            }
            case self::FORMAT_DOCX:
            {
                $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            }
            case self::FORMAT_HTML:
            {
                $type = 'text/html';
                break;
            }
            case self::FORMAT_PDF:
            {
                $type = 'application/pdf';
                break;
            }
            case self::FORMAT_XLSX:
            {
                $type = 'application/ms-excel';
            }
        }
        return $type;
    }

    // --------------------------------------------------------------------

    public abstract function templateOpen();

    // --------------------------------------------------------------------

    public abstract function templateWrite();

    // --------------------------------------------------------------------

    public abstract function templateClose();

    // --------------------------------------------------------------------

    public function render()
    {
        /*
         * Enviar para su descarga o vista online.
         */
        if ($this->isForceDownload()) {
            HttpTools::downloadBinFile($this->getOutputDirectory() . $this->getOutputName() . '.' . $this->getOutputExtension(), $this->getMimeType());
        }
        else {
            HttpTools::outputBinFile($this->getOutputDirectory() . $this->getOutputName() . '.' . $this->getOutputExtension(), $this->getMimeType());
        }
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = (string)$error;
    }

    // --------------------------------------------------------------------

    public function addError(string $error): void
    {
        $this->error .= (string)$error . "\n";
    }

    // --------------------------------------------------------------------

    public static function getActions()
    {
        return [
            self::ACTION_VIEW     => 'Ver',
            self::ACTION_DOWNLOAD => 'Descargar',
            // self::ACTION_SHARE => 'Compartir' // Esta opción de momento no va a estar disponible
        ];
    }

    // --------------------------------------------------------------------

    public static function getFormats($type = self::TYPE_DOC)
    {
        $docs = [
            self::FORMAT_ODT  => 'OpenDocument (.odt)',
            self::FORMAT_DOC  => 'Word antiguo (.doc)',
            self::FORMAT_DOCX => 'Word (.docx)',
            self::FORMAT_HTML => 'HTML (.html)',
            self::FORMAT_PDF  => 'PDF (.pdf)'
        ];

        $tabs = [
            self::FORMAT_XLSX => 'Excel (.xlsx)',
        ];

        if ($type == self::TYPE_DOC)
            return $docs;
        elseif ($type == self::TYPE_TAB)
            return $tabs;
        else return $docs + $tabs;
    }

    // --------------------------------------------------------------------

}
