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

use DateTime;

/**
 * String Map Field View
 */
class FieldViewTime extends FieldViewString
{

    public function __construct()
    {
        parent::__construct();
        $this->maxLength = 10;
    }

    // --------------------------------------------------------------------

    /**
     * To be used in child classes
     * @param FieldViewString $old
     * @return mixed
     */
    public static function promote(FieldViewString $old)
    {
        // This is really weird. PHP hasn't class type casting...
        $new = new static();
        $new->setCssClass($old->getCssClass());
        $new->setEditable($old->getEditable());
        $new->setDisplayOnly($old->getDisplayOnly());
        $new->setElementID($old->getElementID());
        $new->setElementName($old->getElementName());
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->set_localizer($old->getLocalizer());
        $new->setSortable($old->getSortable());
        $new->setInlineBackend($old->getInlineBackend());
        $new->setInlineEdit($old->isInlineEdit());
        $new->setMinLength($old->getMinLength());
        return $new;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        if (!is_a($value, '\DateTime')) {
            $value = DateTime::createFromFormat(Config::get('dbTimeFormat'), (string)$value);
            if (!$value) {
                $value = NULL;
            }
        }
        $value = $this->getLocalizer()->getTime($value);
        $this->renderView('time', $value, ['pk' => $packedPK]);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return 'zjFvTime';
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'tm';
    }
    // --------------------------------------------------------------------
}
