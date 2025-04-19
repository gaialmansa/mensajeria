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

class MapperFieldView
{

    /**
     * Creates a specialized FieldView object from a Field object
     *
     * @param Field $f
     * @param Localizer $loc
     * @return FieldView
     */
    public static function viewize(Field $f, Localizer $loc)
    {
        $fv = NULL;
        if (is_a($f, '\zfx\FieldString')) {
            if ($f->getMax() == -1) {
                $fv = new FieldViewText();
            }
            else {
                $fv = new FieldViewString();
                $fv->setMaxLength($f->getMax());
            }
        }
        elseif (is_a($f, '\zfx\FieldDate')) {
            $fv = new FieldViewDate();
            $fv->setMaxLength(10);
        }
        elseif (is_a($f, '\zfx\FieldTime')) {
            $fv = new FieldViewTime();
            $fv->setMaxLength(8);
        }
        elseif (is_a($f, '\zfx\FieldDateTime')) {
            $fv = new FieldViewDateTime();
            $fv->setMaxLength(20);
        }
        elseif (is_a($f, '\zfx\FieldInt')) {
            $fv = new FieldViewString();
            $fv->setMaxLength(max(strlen((string)$f->getLowerLimit()), strlen((string)$f->getUpperLimit())));
        }
        elseif (is_a($f, '\zfx\FieldReal')) {
            $fv = new FieldViewReal();
            $fv->setMaxLength(20);
        }
        elseif (is_a($f, '\zfx\FieldBoolean')) {
            $fv = new FieldViewBoolean();
        }
        elseif (is_a($f, '\zfx\FieldNum')) {
            $fv = new FieldViewNum();
        }
        $fv->setField($f);
        $fv->set_localizer($loc);
        $fv->setLabel(StrFilter::upperCase($f->getColumn()));
        if (is_a($fv, '\zfx\FieldViewFormElement')) {
            $fv->setElementName(self::getMappedFieldName($f->getColumn()));
        }
        return $fv;
    }
    // --------------------------------------------------------------------

    /**
     * Obtener el nombre que tendrán elementos como los <input> en HTML
     * correspondientes a la columna.
     *
     * De momento hacemos algo tan simple como un guión bajo "_" y
     * el nombre de la columna en formato SafeEncode.
     *
     * @param $column
     * @return string
     */
    public static function getMappedFieldName($column)
    {
        return '_' . StrFilter::safeEncode($column);
    }

    // --------------------------------------------------------------------

}
