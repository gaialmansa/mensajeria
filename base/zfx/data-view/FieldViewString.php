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

class FieldViewString extends FieldViewFormElement
{

    /**
     *
     * @var int $minLength
     */
    protected $minLength;

    /**
     *
     * @var int $maxLength
     */
    protected $maxLength;

    /**
     *
     * @var int $displayLength
     */
    protected $displayLength;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->maxLength     = 128;
        $this->minLength     = 1;
        $this->displayLength = 0;
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
        $new->setMaxLength($old->getMaxLength());
        $new->setMinLength($old->getMinLength());
        return $new;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param int $val
     */
    public function setMaxLength($val)
    {
        $this->maxLength = (int)$val;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param int $val
     */
    public function setMinLength($val)
    {
        $this->minLength = (int)$val;
    }

    // --------------------------------------------------------------------

    public function render($value, $packedPK = '')
    {
        $value = StrFilter::filter($value, array('spaceClear', 'HTMLencode'));
        $this->renderView('string', $value, ['pk' => $packedPK]);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvString';
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return int
     */
    public function getDisplayLength()
    {
        return $this->displayLength;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param int $val
     */
    public function setDisplayLength($val)
    {
        $this->displayLength = (int)$val;
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 's';
    }
    // --------------------------------------------------------------------
}
