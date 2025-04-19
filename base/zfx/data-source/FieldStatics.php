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
 * Static functions interface for Fields
 */
interface FieldStatics
{

    /**
     * PHP Data cast
     *
     * Convert a PHP value to a SQL literal according the Field type
     *
     * @param mixed $data Value to be converted
     * @param boolean $required If FALSE blank or null values will generate
     * NULL literals; blank literals otherwise
     */
    public static function cast($data, $required = FALSE);

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
    public static function castHttp($data, Localizer $loc, $required = FALSE);

    // --------------------------------------------------------------------

    /**
     * Generate equality test condition
     *
     * Returns a SQL equality test expression (column=value) according the Field
     * type
     *
     * @param string $column Column name
     * @param mixed $data Value
     * @param boolean $required If FALSE blank or null values will generate NULL literals; blank literals otherwise
     * @param string $qualifier Optional column cualifier
     */
    public static function cond($column, $data, $required = FALSE, $qualifier = '');

    // --------------------------------------------------------------------

    /**
     * Convierte un valor SQL a un valor PHP
     *
     * @param $data
     * @return mixed
     */
    public static function value($data);

    // --------------------------------------------------------------------

    /**
     * Convierte un valor SQL a una cadena.
     * @param $data
     * @param Localizer $loc
     * @return mixed
     */
    public static function toString($data, Localizer $loc);

    // --------------------------------------------------------------------
}
