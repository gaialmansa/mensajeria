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
 * Boolean Field
 */
class FieldBoolean extends Field
{
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
            return ((bool)$data ? 'TRUE' : 'FALSE');
        }
        else {
            if ($data === NULL) {
                return "NULL";
            }
            else {
                return ((bool)$data ? 'TRUE' : 'FALSE');
            }
        }
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
        if ($required) {
            return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . " = " . ((bool)$data ? 'TRUE' : 'FALSE');
        }
        else {
            if ($data === NULL) {
                return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . ' IS NULL';
            }
            else {
                return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . " = " . ((bool)$data ? 'TRUE' : 'FALSE');
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
     * @return $mixed
     */
    public static function castHttp($data, Localizer $loc, $required = FALSE)
    {
        if (trueEmpty($data) || $data == '0') {
            return FALSE;
        }
        else {
            return TRUE;
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
     * Set default value
     *
     * The default value is stablished at PHP level. This is not the database
     * DEFAULT subclause.
     *
     * @param mixed $val
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = (bool)$value;
    }

    // --------------------------------------------------------------------

    public static function value($data)
    {
        if ($data == 'T' || $data == 'TRUE' || $data == '1') {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    // --------------------------------------------------------------------

    public static function toString($data, Localizer $loc)
    {
        return $loc->getBoolean(self::value($data));
    }

    // --------------------------------------------------------------------

}
