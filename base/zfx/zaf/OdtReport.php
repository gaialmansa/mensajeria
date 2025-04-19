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

use Phpodex;

abstract class OdtReport extends Report
{
    /**
     * @var Phpodex $phpodex
     */
    protected $phpodex;

    public function __construct()
    {
        parent::__construct();
        $this->phpodex = NULL;
    }

    // --------------------------------------------------------------------

    /**
     * @param int $outputFormat
     */
    public function setOutputFormat($outputFormat)
    {
        if ((int)$outputFormat >= self::FORMAT_ODT && (int)$outputFormat <= self::FORMAT_PDF) {
            $this->outputFormat = (int)$outputFormat;
        }
    }

    // --------------------------------------------------------------------

    private function getOutputFilter()
    {
        switch ($this->outputFormat) {
            case self::FORMAT_ODT:
            {
                $filter = 'odt';
                break;
            }
            case self::FORMAT_DOC:
            {
                $filter = 'doc';
                break;
            }
            case self::FORMAT_DOCX:
            {
                $filter = 'docx';
                break;
            }
            case self::FORMAT_HTML:
            {
                $filter = 'html';
                break;
            }
            case self::FORMAT_PDF:
            {
                $filter = 'pdf';
                break;
            }
        }
        return $filter;
    }

    // --------------------------------------------------------------------


    public function templateOpen()
    {
        // Hacemos unas comprobaciones
        if ($this->getTemplatePath() == '') {
            $this->addError("La ruta de las plantillas está vacía.");
            return FALSE;
        }

        if ($this->getOutputDirectory() == '') {
            $this->addError("El directorio de salida está vacío.");
            return FALSE;
        }

        $this->phpodex = new Phpodex($this->getTemplatePath());
        $this->phpodex->setTempdir($this->getOutputDirectory());
        return TRUE;
    }

    // --------------------------------------------------------------------


    public function templateClose()
    {
        $fich = $this->getOutputDirectory() . $this->getOutputName() . '.odt';
        if (is_file($fich) && is_writable($fich)) {
            unlink($fich);
        }
        $this->phpodex->close($fich);
        /*
         * Convertir, si hace falta
         */
        if ($this->getOutputFormat() != self::FORMAT_ODT) {
            $filter    = $this->getOutputFilter();
            $outdir    = $this->getOutputDirectory();
            $inputfile = $fich;
            $fichFinal = $this->getOutputDirectory() . $this->getOutputName() . '.' . $this->getOutputExtension();
            if (is_file($fichFinal) && is_writable($fichFinal)) {
                unlink($fichFinal);
            }
            @ob_start();
            $command = "nice -n 15 libreoffice --headless --convert-to $filter --outdir $outdir $inputfile";
            exec($command);
            @ob_end_clean();
        }

        return $this->getOutputDirectory() . $this->getOutputName() . '.' . $this->getOutputExtension();
    }

    // --------------------------------------------------------------------

    /**
     * Genera un registro o bloque en blanco a partir de una tabla y unos campos adicionales opcionales.
     * Atención, no se escapa nada.
     *
     * @param boolean $block Si debe generarse un bloque o un registro.
     * @param $table Nombre de la tabla a usar como plantilla
     * @param array|null $additionalFields Lista de campos adicionales que se añadirán a los de la tabla
     * @param string $profile Perfil de la base de datos
     */
    public static function genBlankFromTable($block, $table, array $additionalFields = NULL, $profile = NULL)
    {
        $reg    = [];
        $schema = new AutoSchema($table, $profile);
        if ($schema->getFieldsKeys()) {
            foreach ($schema->getFieldsKeys() as $field) {
                $reg[$field] = '';
            }
        }
        if (va($additionalFields)) {
            foreach ($additionalFields as $field) {
                $reg[$field] = '';
            }
        }
        // Aquí es donde devolvemos un registro...
        if (!$block) {
            return $reg;
        }
        // ...o un bloque que no es más que una lista de registros
        else {
            return [0 => $reg];
        }
    }

    // --------------------------------------------------------------------

}