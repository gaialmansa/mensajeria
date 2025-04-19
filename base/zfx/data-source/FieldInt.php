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
 * Integer Field
 */
class FieldInt extends Field
{

    protected $lowerLimit;
    protected $upperLimit;

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
        if ($required) {
            return (int)$data;
        }
        else {
            if ($data === NULL || trueEmpty($data)) {
                return "NULL";
            }
            else {
                return (int)$data;
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
            return (int)$data;
        }
        else {
            if ($data === NULL || trueEmpty($data)) {
                return NULL;
            }
            else {
                return (int)$data;
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
     * Get lower limit
     *
     * @return integer
     */
    public function getLowerLimit()
    {
        return $this->lowerLimit;
    }
    // --------------------------------------------------------------------

    /**
     * Set lower limit
     *
     * @param integer $val
     */
    public function setLowerLimit($val)
    {
        $this->lowerLimit = (int)$val;
    }
    // --------------------------------------------------------------------

    /**
     * Get upper limit
     *
     * @return integer
     */
    public function getUpperLimit()
    {
        return $this->upperLimit;
    }

    // --------------------------------------------------------------------

    /**
     * Set upper limit
     *
     * @param integer $val
     */
    public function setUpperLimit($val)
    {
        $this->upperLimit = (int)$val;
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
        $this->defaultValue = (int)$value;
    }

    // --------------------------------------------------------------------

    public static function value($data)
    {
        return (int)$data;
    }

    // --------------------------------------------------------------------
    public static function toString($data, Localizer $loc)
    {
        return $loc->getInteger(self::value($data));
    }

    // --------------------------------------------------------------------

}
