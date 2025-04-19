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

class FieldViewSelectorCheckBox extends FieldViewBoolean
{

    /**
     * To be used in child classes
     * @param FieldViewString $old
     * @return mixed
     */
    public static function promote(FieldViewBoolean $old)
    {
        // This is really weird. PHP hasn't class type casting...
        $new = new static();

        // For FieldViewFormElement
        $new->setElementID($old->getElementID());
        $new->setElementName($old->getElementName());

        // For FieldView
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->setEditable($old->getEditable());
        $new->setCssClass($old->getCssClass());
        $new->setSortable($old->getSortable());
        $new->setData($old->getData());
        $new->setDisplayOnly($old->getDisplayOnly());

        return $new;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        SelectorCheckBox::renderTag($value, $packedPK, $this->getElementID(), $this->getOwnCssClass() . ' ' . $this->getCssClass(TRUE), $this->getData());
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvSelectorCheckBox';
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'scb';
    }
    // --------------------------------------------------------------------
}
