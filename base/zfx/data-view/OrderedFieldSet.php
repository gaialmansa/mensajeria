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

class OrderedFieldSet
{

    const ORDER_ASCENDING  = TRUE;
    const ORDER_DESCENDING = FALSE;

    // --------------------------------------------------------------------

    private $fieldSet;
    private $single;
    private $default;

    // --------------------------------------------------------------------


    public function __construct($single = FALSE)
    {
        $this->fieldSet = array();
        $this->default  = array();
        $this->single   = (bool)$single;
    }

    // --------------------------------------------------------------------

    public function setField($fieldId, $ascending = TRUE)
    {
        if ($this->single) {
            $this->fieldSet = array();
        }
        $this->fieldSet[$fieldId] = (bool)$ascending;
    }

    // --------------------------------------------------------------------

    public function getfield($fieldId)
    {
        return a($this->fieldSet, $fieldId);
    }

    // --------------------------------------------------------------------

    public function removeField($fieldId)
    {
        if (isset($this->fieldSet[$fieldId])) {
            unset($this->fieldSet[$fieldId]);
            // If empty set defaults
            if (!$this->fieldSet) {
                $this->fieldSet = $this->default;
            }
            if ($this->single && nwcount($this->fieldSet) > 1) {
                $v              = reset($this->fieldSet);
                $k              = key($this->fieldSet);
                $this->fieldSet = array($k => $v);
            }
        }
    }

    // --------------------------------------------------------------------

    public function getFieldSet()
    {
        return $this->fieldSet;
    }

    // --------------------------------------------------------------------

    public function setFieldSet(array $fieldSet = NULL)
    {
        if (!is_array($fieldSet)) {
            $fieldSet = array();
        }
        $this->fieldSet = $fieldSet;

        // If empty set defaults
        if (!$this->fieldSet) {
            $this->fieldSet = $this->default;
        }
        if ($this->single && nwcount($this->fieldSet) > 1) {
            $v              = reset($this->fieldSet);
            $k              = key($this->fieldSet);
            $this->fieldSet = array($k => $v);
        }
    }

    // --------------------------------------------------------------------
    public function getFieldPos($fieldId)
    {
        $res = array_search($fieldId, array_keys($this->fieldSet));
        if ($res === FALSE) {
            return 0;
        }
        else {
            return $res + 1;
        }
    }

    // --------------------------------------------------------------------

    public function getDefault()
    {
        return $this->default;
    }

    // --------------------------------------------------------------------

    public function setDefault(array $fieldSet = NULL)
    {
        if (!is_array($fieldSet)) {
            $fieldSet = array();
        }
        $this->default = $fieldSet;
    }

    // --------------------------------------------------------------------

    public function getSingle()
    {
        return $this->single;
    }

    // --------------------------------------------------------------------

    public function setSingle($value)
    {
        $this->single = (bool)$value;
    }
    // --------------------------------------------------------------------
}
