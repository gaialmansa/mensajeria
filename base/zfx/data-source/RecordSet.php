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
 * Service class for record sets
 *
 * Actually a "record set" is represented as an array map.
 * This class provides services that work on those arrays.
 * In the future this class would provide all record set pattern features.
 */
class RecordSet
{
    // --------------------------------------------------------------------

    /**
     * Convert safe-encoded POST data to record set
     *
     * @param array $postData
     * @param Schema $schema
     * @param Localizer $loc
     * @return null
     */
    static function processPOST(array $postData, Schema $schema, Localizer $loc)
    {
        if (!va($postData) || !$schema) {
            return NULL;
        }

        $rs = array();

        foreach ($postData as $key => $data) {
            if (substr($key, -4, 1) == '_') {
                continue;
            } // Ignore all fields with a '_xxx' suffix
            $decKey = StrFilter::safeDecode(substr($key, 1));
            $field  = $schema->getField($decKey);
            if ($field) {
                $rs[$decKey] = $field::castHttp($data, $loc);
            }
        }

        return $rs;
    }
    // --------------------------------------------------------------------

    /**
     * Convertir un array obtenido de la BD (p.ejemplo con qa)
     * a un array de valores de PHP, usando el Schema
     */
    static function processRSSchema(array $rs = NULL, Schema $schema = NULL)
    {
        $res = array();
        if (!$rs) {
            return $res;
        }
        if (!$schema) {
            return $res;
        }
        foreach ($rs as $key => $value) {
            $field = $schema->getField($key);
            if ($field) {
                $res[$key] = $field::value($value);
            }
        }
        return $res;
    }

    // --------------------------------------------------------------------

    /**
     * Convertir un array obtenido de la BD (p.ejemplo con qa)
     * a un array de cadenas, usando el Schema
     */
    static function processRSSchemaString(array $rs = NULL, Schema $schema = NULL)
    {
        $res = array();
        if (!$rs) {
            return $res;
        }
        if (!$schema) {
            return $res;
        }
        foreach ($rs as $key => $value) {
            $field = $schema->getField($key);
            if ($field) {
                $res[$key] = $field::toString($value);
            }
        }
        return $res;
    }

    // --------------------------------------------------------------------

    /**
     * Convertir un array obtenido de la BD (p.ejemplo con qa)
     * a un array de valores de PHP, usando un TableView
     */
    static function processRSTableView(array $rs = NULL, TableView $tv = NULL)
    {
        $res = array();
        if (!$rs) {
            return $res;
        }
        if (!$tv) {
            return $res;
        }
        foreach ($rs as $key => $value) {
            $fieldview = $tv->getFieldView($key);
            if ($fieldview) {
                $res[$key] = $fieldview->value($value);
            }
        }
        return $res;
    }

    // --------------------------------------------------------------------

    /**
     * Convertir un array obtenido de la BD (p.ejemplo con qa)
     * a un array de cadenas, usando un TableView
     */
    static function processRSTableViewString(array $rs = NULL, TableView $tv = NULL)
    {
        $res = array();
        if (!$rs) {
            return $res;
        }
        if (!$tv) {
            return $res;
        }
        foreach ($rs as $key => $value) {
            $fieldview = $tv->getFieldView($key);
            if ($fieldview) {
                $res[$key] = $fieldview->toString($value);
            }
        }
        return $res;
    }

    // --------------------------------------------------------------------

}
