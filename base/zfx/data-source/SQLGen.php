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
 * SQL Generator
 */
class SQLGen
{

    /**
     * "ORDER BY" clause generator
     *
     * @param array $fieldList like this:
     *     fieldName => (bool) ascending = true | descending = false
     * @return string
     */
    public static function getOrderBy(array $fieldList, array $mapping = NULL)
    {
        if (va($fieldList)) {
            $sqlPieces = array();
            foreach ($fieldList as $fieldName => $mode) {
                if ($mapping && array_key_exists($fieldName, $mapping)) {
                    $sqlPieces[] = $mapping[$fieldName] . ' ' . ((bool)$mode ? 'ASC' : 'DESC');
                }
                else {
                    $sqlPieces[] = DB::quote($fieldName) . ' ' . ((bool)$mode ? 'ASC' : 'DESC');
                }
            }
            if ($sqlPieces) {
                return ' ORDER BY ' . implode(', ', $sqlPieces);
            }
            else {
                return '';
            }
        }
        else {
            return '';
        }
    }
    // --------------------------------------------------------------------

    /**
     * Make sure that given array contains integers only
     *
     * @param array $a
     */
    public static function arrayInt(array &$a)
    {
        array_walk($a, function (&$val) {
            $val = (int)$val;
        });
    }
}
