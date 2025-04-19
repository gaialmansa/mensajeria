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
 * Hidden string Field View
 */
class FieldViewHidden extends FieldViewFormElement
{

    // --------------------------------------------------------------------

    /**
     * To be used in child classes
     * @param FieldViewString $old
     * @return mixed
     */
    public static function promote(FieldViewFormElement $old)
    {
        // This is really weird. PHP hasn't class type casting...
        $new = new static();
        $new->setDisplayOnly($old->getDisplayOnly());
        $new->setCssClass($old->getCssClass());
        $new->setEditable($old->getEditable());
        $new->setElementID($old->getElementID());
        $new->setElementName($old->getElementName());
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->set_localizer($old->getLocalizer());
        $new->setSortable($old->getSortable());
        return $new;
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '';
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        // Un FieldViewHidden solo se mostrará en un formulario.
        if (!$this->getDisplayOnly()) {
            $value = StrFilter::filter((string)$value, array('spaceClear', 'HTMLencode'));
            echo '<input' .
                 ' type="hidden"' .
                 " value=\"$value\"" .
                 (!trueEmpty($this->getElementID()) ? " id=\"{$this->getElementID()}\"" : '') .
                 (!trueEmpty($this->getElementName()) ? " name=\"{$this->getElementName()}\"" : '') .
                 'class="' . $this->getOwnCssClass() . ' ' . $this->getCssClass(TRUE) . '"' .
                 '/>';
        }
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 's';
    }

    // --------------------------------------------------------------------

    public function isHidden()
    {
        return TRUE;
    }

}
