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

class PKView
{
    // --------------------------------------------------------------------

    /**
     * Pack a primary key value to be used in HTML forms
     *
     * @param array $pk
     * @return string
     */
    static function pack(array $pk = NULL)
    {
        if (!va($pk)) {
            return '';
        }
        $r = array();
        foreach ($pk as $k => $v) {
            $r[] = StrFilter::safeEncode($k) . 'x' . StrFilter::safeEncode($v);
        }
        return implode('-', $r);
    }
    // --------------------------------------------------------------------

    /**
     * Unpacks a primary key value packed with PKView::pack()
     *
     * @param string $pk
     * @return array
     */
    static function unpack($pk, $returnKey = '')
    {
        $pk  = (string)$pk;
        $ret = array();
        $r   = explode('-', $pk);
        if (!$r) {
            return NULL;
        }
        foreach ($r as $pair) {
            $p = explode('x', $pair);
            if (nwcount($p) != 2) {
                return NULL;
            }
            $key   = StrFilter::safeDecode($p[0]);
            $value = StrFilter::safeDecode($p[1]);
            if (trueEmpty($key)) {
                return NULL;
            }
            $ret[$key] = $value;
        }
        if (trueEmpty($returnKey)) {
            return $ret;
        }
        else {
            return a($ret, $returnKey);
        }
    }
    // --------------------------------------------------------------------
}
