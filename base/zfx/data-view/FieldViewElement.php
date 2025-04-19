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

/**
 * HTML Element Field View
 *
 * This view assumes that a html element code (like an iframe) is contained
 */
class FieldViewElement extends FieldViewString
{

    // --------------------------------------------------------------------

    /**
     *
     * @param FieldViewString $old
     * @return FieldViewImageUrl
     */
    public static function promote(FieldViewString $old)
    {
        // This is really weird. PHP hasn't class type casting...
        $new = new FieldViewElement();
        $new->setDisplayOnly($old->getDisplayOnly());
        $new->setCssClass($old->getCssClass());
        $new->setEditable($old->getEditable());
        $new->setElementID($old->getElementID());
        $new->setElementName($old->getElementName());
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->set_localizer($old->getLocalizer());
        $new->setMaxLength($old->getMaxLength());
        $new->setMinLength($old->getMinLength());
        $new->setSortable($old->getSortable());
        return $new;
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvElement';
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        if ($this->displayOnly) {
            echo $value;
            return;
        }
        if (!$this->editable) {
            echo $value;
            return;
        }
        else {
            parent::render($value);
            return;
        }
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'el';
    }
    // --------------------------------------------------------------------
}
