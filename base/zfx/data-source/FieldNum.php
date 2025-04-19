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
 * Num Field
 */
class FieldNum extends Field
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
        if (is_a($data, '\zfx\Num')) {
            $n = $data;
        }
        else {
            $n = new Num($data);
        }
        if ($required) {
            return $n->getVal();
        }
        else {
            if ($data === NULL || trueEmpty($data)) {
                return "NULL";
            }
            else {
                return $n->getVal();
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
     * @param string $data
     * @param Localizer $loc Localizer.
     * @param boolean $required If FALSE blank or null values will generate
     * NULL literals; blank literals otherwise
     * @return \zfx\Num
     */
    public static function castHttp($data, Localizer $loc, $required = FALSE)
    {
        if ($required) {
            return new Num($data);
        }
        else {
            if ($data === NULL || trueEmpty($data)) {
                return NULL;
            }
            else {
                return new Num($data);
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
        $this->defaultValue = new Num();
    }

    // --------------------------------------------------------------------

    public static function value($data)
    {
        if (is_a($data, '\zfx\Num')) {
            return $data;
        }
        else {
            return new Num($data);

        }
    }

    // --------------------------------------------------------------------

    public static function toString($data, Localizer $loc)
    {
        return $loc->getNum(self::value($data));
    }

    // --------------------------------------------------------------------

}
