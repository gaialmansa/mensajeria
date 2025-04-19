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

class FieldViewDBPicker extends FieldViewFormElement
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
    protected $sourceTable;
    protected $sourceDisplay;
    protected $profile;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->maxLength     = 128;
        $this->minLength     = 1;
        $this->sourceTable   = NULL;
        $this->sourceDisplay = '';
        $this->profile       = '';
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

    public function render($value, $packedPK = '')
    {
        $this->renderView('dbpicker', $value);
    }

    // --------------------------------------------------------------------

    public function getDisplayValue($value)
    {
        if (is_string($this->sourceDisplay)) {
            return $this->sourceTable->readR($value, $this->sourceDisplay);
        }
        else {
            if (is_array($this->sourceDisplay)) {
                $fields = a($this->sourceDisplay, 'fields');
                $format = a($this->sourceDisplay, 'format');
                if (!va($fields) || !$format) {
                    return;
                }
                $r            = $this->sourceTable->readR($value);
                $s            = array();
                $emptyChecker = '';
                foreach ($fields as $f) {
                    $s[]          = \zfx\a($r, $f);
                    $emptyChecker .= \zfx\a($r, $f);
                }
                if (trueEmpty($emptyChecker)) {
                    return '';
                }
                else {
                    return vsprintf($format, $s);
                }
            }
        }
    }

    // --------------------------------------------------------------------

    public function value($data)
    {
        /*
         * De forma predeterminada lo obtenemos a través de su field, pero en algunos FieldView lo cambiaremos.
         */
        return (string)$this->getDisplayValue($data);
    }

    // --------------------------------------------------------------------

    public function toString($data)
    {
        /*
         * De forma predeterminada lo obtenemos a través de su field, pero en algunos FieldView lo cambiaremos.
         */
        return (string)$this->getDisplayValue($data);
    }

    // --------------------------------------------------------------------

    public function getOwnCssClass()
    {
        return '_fvDBPicker';
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

    public function setSource($table, $display, $profile = '')
    {
        $this->sourceTable   = new AutoTable(new AutoSchema($table, $profile), $profile);
        $this->sourceDisplay = $display;
        $this->profile       = (string)$profile;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return Table
     */
    public function getSourceTable()
    {
        return $this->sourceTable;
    }

    // --------------------------------------------------------------------

    public function getJSH()
    {
        return 'dbp';
    }
    // --------------------------------------------------------------------
}
