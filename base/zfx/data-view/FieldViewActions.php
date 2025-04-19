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

class FieldViewActions extends FieldView
{

    private $actions;
    private $valueVisible;

    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->actions      = NULL;
        $this->valueVisible = FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * To be used in child classes
     * @param FieldViewString $old
     * @return FiewViewActions;
     */
    public static function promote(FieldViewString $old)
    {
        $new = new static();
        $new->setCssClass($old->getCssClass());
        $new->setEditable($old->getEditable());
        $new->setField($old->getField());
        $new->setLabel($old->getLabel());
        $new->setSortable($old->getSortable());
        return $new;
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvActions';
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @param array $value
     */
    public function setActions(array $value = NULL)
    {
        $this->actions = $value;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        if ($this->valueVisible) {
            RowActions::renderTag($this->actions, StrFilter::safeEncode($value), $this->getCssClass(FALSE), $value);
        }
        else {
            RowActions::renderTag($this->actions, StrFilter::safeEncode($value), $this->getCssClass(FALSE));
        }
    }
    // --------------------------------------------------------------------

    /**
     * @return boolean
     */
    public function getValueVisible()
    {
        return $this->valueVisible;
    }
    // --------------------------------------------------------------------

    /**
     * @param boolean $val
     */
    public function setValueVisible($val)
    {
        $this->valueVisible = (bool)$val;
    }

    // --------------------------------------------------------------------


    public function getJSH()
    {
        return 'ac';
    }
    // --------------------------------------------------------------------
}
