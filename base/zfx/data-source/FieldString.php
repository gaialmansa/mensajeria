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
 * @package data-source
 */

namespace zfx;

/**
 * String and text Field
 */
class FieldString extends Field
{

    /**
     * Maximum length
     * @var integer
     */
    private $max;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->max = 0;
    }
    // --------------------------------------------------------------------

    /**
     * Generate equality test condition
     *
     * Returns a SQL equality test expression (column=value) according the Field
     * type
     *
     * @param string $column Column name
     * @param mixed $data Value
     * @param boolean $required If FALSE blank or null values will generate
     * NULL literals; blank literals otherwise
     */
    public static function cond($column, $data, $required = FALSE, $qualifier = '')
    {
        $cast      = self::cast($data, $required);
        $txtSearch = preg_replace('%[aeiou]%su', '_', DB::escape($data));

        if ($cast === 'NULL') {
            return DB::quote($column) . ' IS ' . $cast;
        }
        else {
            return 'UPPER(' . ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . ") LIKE UPPER('%" . $txtSearch . "%')";
        }
    }
    // --------------------------------------------------------------------

    /**
     * PHP Data cast
     *
     * Convert a PHP value to a SQL literal according the Field type
     *
     * @param mixed $data Value to be converted
     * @param boolean $required If FALSE blank or null values will generate
     * NULL literals; blank literals otherwise
     */
    public static function cast($data, $required = FALSE)
    {
        if ($required) {
            return "'" . DB::escape((string)$data) . "'";
        }
        else {
            if ($data === NULL || trueEmpty($data)) {
                return "NULL";
            }
            else {
                return "'" . DB::escape((string)$data) . "'";
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     * HTTP Data cast
     *
     * Convert a HTML value (represented as a PHP string) to a PHP value
     * according the Field type
     *
     * @param $string $data
     * @param Localizer $loc Localizer.
     * @param boolean $required If FALSE blank or null values will generate
     * NULL literals; blank literals otherwise
     * @return $mixed
     */
    public static function castHttp($data, Localizer $loc, $required = FALSE)
    {
        if ($required) {
            return (string)$data;
        }
        else {
            if ($data === NULL || trueEmpty($data)) {
                return NULL;
            }
            else {
                return (string)$data;
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     * Can be used on ranges?
     *
     * @return mixed
     */
    public static function getBounded()
    {
        return FALSE;
    }
    // --------------------------------------------------------------------

    /**
     * Get maximum length
     *
     * @return integer
     */
    public function getMax()
    {
        return $this->max;
    }
    // --------------------------------------------------------------------

    /**
     * Set maximum length
     *
     * @param integer $value
     */
    public function setMax($value = 0)
    {
        $this->max = (int)$value;
    }
    // --------------------------------------------------------------------

    /**
     * Set default value
     *
     * The default value is stablished at PHP level. This is not the database
     * DEFAULT subclause.
     *
     * @param mixed $val
     */
    public function setDefaultValue($value)
    {
        if ($this->max == 0) {
            $this->defaultValue = (string)$value;
        }
        else {
            $this->defaultValue = mb_substr((string)$value, 0, $this->max, 'UTF-8');
        }
    }

    // --------------------------------------------------------------------

    public static function value($data)
    {
        return (string)$data;
    }

    // --------------------------------------------------------------------

    public static function toString($data, Localizer $loc)
    {
        return self::value($data);
    }

    // --------------------------------------------------------------------

    public function getSize()
    {
        return $this->getMax();
    }

    // --------------------------------------------------------------------

}
