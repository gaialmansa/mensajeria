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
 * Image Field View
 */
class FieldViewImage extends FieldViewString
{

    protected $baseUrl          = '';
    protected $table            = '';
    protected $column           = '';
    protected $keepOriginalName = FALSE;

    // --------------------------------------------------------------------

    /**
     *
     * @param FieldViewString $old
     * @return FieldViewImage
     */
    public static function promote(FieldViewString $old)
    {
        // This is really weird. PHP hasn't class type casting...
        $new = new FieldViewImage();
        $new->setDisplayOnly($old->getDisplayOnly());
        $new->setCssClass($old->getCssClass());
        $new->setEditable($old->getEditable());
        $new->setElementID($old->getElementID());
        $new->setElementName($old->getElementName());
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->set_localizer($old->getLocalizer());
        $new->setMaxLength($old->getMaxLength());
        $new->setMinLength($old->getMinLength());
        $new->setSortable($old->getSortable());
        return $new;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $this->renderView('image', $value);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return 'zjFvImage';
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 's';
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = (string)$baseUrl;
    }

    // --------------------------------------------------------------------


    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    // --------------------------------------------------------------------


    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    // --------------------------------------------------------------------

    /**
     * Genera un nombre de directorio a partir del nombre de la tabla
     *
     * Se usa para construir rutas, aunque no es de uso obligatorio.
     *
     * @param $table
     * @return string
     */
    public static function genTableName($table)
    {
        return StrFilter::getID($table);
    }

    // --------------------------------------------------------------------

    /**
     * Genera un nombre de directorio a partir del nombre del campo
     *
     * Se usa para construir rutas, aunque no es de uso obligatorio.
     *
     * @param $field
     * @return string
     */
    public static function genFieldName($field)
    {
        return StrFilter::getID($field);
    }

    // --------------------------------------------------------------------

    /**
     * Generar el path de búsqueda de un fichero según una tabla y campo proporcionados
     *
     * @param $table
     * @param $field
     * @return string
     */
    public static function genBasePath($table, $field)
    {
        $dbFilePath = Config::Get('data-view_filePath');
        $table      = self::genTableName($table);
        $field      = self::genFieldName($field);
        return $dbFilePath . $table . DIRECTORY_SEPARATOR . $field . DIRECTORY_SEPARATOR;
    }

    // --------------------------------------------------------------------

    /**
     * Generar la URL raíz de un fichero según una tabla y campos proporcionados
     *
     * @param $table
     * @param $field
     * @return string
     */
    public static function genBaseUrl($table, $field)
    {
        $dbFileUrl = Config::get('rootUrl') . Config::Get('data-view_fileUrlPath');
        $table     = self::genTableName($table);
        $field     = self::genFieldName($field);
        return $dbFileUrl . $table . '/' . $field . '/';
    }

    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function keepOriginalName()
    {
        return $this->keepOriginalName;
    }

    // --------------------------------------------------------------------

    /**
     * @param bool $keepOriginalName
     */
    public function setKeepOriginalName($keepOriginalName)
    {
        $this->keepOriginalName = (bool)$keepOriginalName;
    }

    // --------------------------------------------------------------------

    public static function extractNames($value)
    {
        if (preg_match('%^\[([0-9a-zA-Z]+)\](.+)%siu', $value, $res)) {
            $original = $res[1];
            $stored   = $res[2];
        }
        else {
            $original = '';
            $stored   = $value;
        }
        return
            [
                'original' => $original,
                'stored'   => $stored
            ];
    }

    // --------------------------------------------------------------------

    public static function getLocalFile($table, $field, $value)
    {
        if (!trueEmpty($value)) {
            return self::genBasePath($table, $field) . \zfx\a(self::extractNames($value), 'stored');
        }
        else {
            return '';
        }
    }

    // --------------------------------------------------------------------

    public static function getLocalUrl($table, $field, $value)
    {
        if (!trueEmpty($value)) {
            return self::genBaseUrl($table, $field) . \zfx\a(self::extractNames($value), 'stored');
        }
        else {
            return '';
        }
    }

    // --------------------------------------------------------------------


    public static function duplicate($table, $field, $value)
    {
        if (trueEmpty($value)) return '';
        $names  = self::extractNames($value);
        $source = self::genBasePath($table, $field) . \zfx\a($names, 'stored');
        if (is_readable($source)) {
            // Primero copiar
            $parts   = pathinfo($source);
            $newFile = $parts['filename'] . '-' . uniqid('clone') . '.' . $parts['extension'];
            $res     = copy($source, self::genBasePath($table, $field) . $newFile);
            if ($res === FALSE) return '';
            // Componer un nombre nuevo, aunque el original lo conservamos si había uno.
            if ($names['original'] == '') {
                return $newFile;
            }
            else return "[" . $names['original'] . "]" . $newFile;
        }
        else return '';
    }

    // --------------------------------------------------------------------


}
