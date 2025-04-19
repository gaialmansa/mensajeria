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

use DateTime;

/**
 * Time Field
 */
class FieldTime extends Field
{
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
        $cast = self::cast($data, $required);
        if ($cast === 'NULL') {
            return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . ' IS ' . $cast;
        }
        else {
            return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . '=' . $cast;
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
        if (is_a($data, '\DateTime')) {
            $value = $data->format(Config::get('dbTimeFormat'));
        }
        else {
            if (!DateTime::createFromFormat(Config::get('dbTimeFormat'), $data)) {
                $value = NULL;
            }
            else {
                $value = $data;
            }
        }

        if ($value === NULL || trueEmpty($value)) {
            return "NULL";
        }
        else {
            return "'" . DB::escape((string)$value) . "'";
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
            return $loc->interpretTime($data, TRUE);
        }
        else {
            if ($data === NULL || trueEmpty($data)) {
                return NULL;
            }
            else {
                return $loc->interpretTime($data, TRUE);
            }
        }
    }

    // --------------------------------------------------------------------

    public static function condRange($column, $dataFrom, $dataTo, $required = FALSE, $qualifier = '')
    {
        $castFrom = self::cast($dataFrom, $required);
        $castTo   = self::cast($dataTo, $required);
        if (($castFrom == 'NULL' || $castFrom == '') && ($castTo == 'NULL' || $castTo == '')) {
            return ''; // no range
        }
        elseif (($castFrom == 'NULL' || $castFrom == '')) {
            return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . ' <= ' . $castTo;
        }
        elseif (($castTo == 'NULL' || $castTo == '')) {
            return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . ' >= ' . $castFrom;
        }
        else {
            return ($qualifier !== '' ? DB::quote($qualifier) . '.' : '') . DB::quote($column) . ' BETWEEN ' . $castFrom . ' AND ' . $castTo;
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
        return TRUE;
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
        if (is_a($value, '\DateTime')) {
            $this->defaultValue = $value;
        }
        else {
            $this->defaultValue = DateTime::createFromFormat(Config::get('dbTimeFormat'), $value);
        }
    }

    // --------------------------------------------------------------------

    public static function value($data)
    {
        $d = date_create_from_format(Config::get('dbTimeFormat'), $data);
        if (!$d instanceof DateTime) {
            return NULL;
        }
        else {
            return $d;
        }
    }

    // --------------------------------------------------------------------

    public static function toString($data, Localizer $loc)
    {
        return $loc->getTime(self::value($data));
    }

    // --------------------------------------------------------------------

}
