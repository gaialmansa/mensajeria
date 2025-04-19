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
 * Abstract table field
 */
abstract class Field implements FieldStatics
{

    protected $index;
    protected $required;
    protected $column;
    protected $defaultValue;
    protected $auto;
    protected $defaultSize;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->index        = FALSE;
        $this->required     = FALSE;
        $this->auto         = FALSE;
        $this->column       = NULL;
        $this->defaultValue = NULL;
        $this->defaultSize  = 0;
    }
    // --------------------------------------------------------------------

    /**
     * Generate in range test condition
     *
     * Returns a SQL BETWEEN test expression (column BETWEEN valueFrom AND valueTo) according the Field
     * type
     *
     * @param string $column Column name
     * @param mixed $dataFrom Value
     * @param mixed $dataTo Value
     * @param boolean $required If FALSE blank or null values will generate NULL literals; blank literals otherwise
     * @param string $qualifier Optional column cualifier
     */
    public static function condRange($column, $dataFrom, $dataTo, $required = FALSE, $qualifier = '')
    {
        return ''; // By default the abstract base field returns nothing.
    }
    // --------------------------------------------------------------------

    /**
     * Can be used on ranges?
     *
     * @return mixed
     */
    public static function getBounded()
    {
        return NULL;
    }
    // --------------------------------------------------------------------

    /**
     * Get indexed field status
     *
     * @return boolean
     */
    public function getIndex()
    {
        return $this->index;
    }
    // --------------------------------------------------------------------

    /**
     * Set field as indexed true/false
     *
     * @param boolean $val
     */
    public function setIndex($val)
    {
        $this->index = (bool)$val;
    }
    // --------------------------------------------------------------------

    /**
     * Get required field status
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }
    // --------------------------------------------------------------------

    /**
     * Set field as required true/false
     *
     * A required field is a field that does not allow NULLs.
     *
     * @param boolean $val
     */
    public function setRequired($val)
    {
        $this->required = (bool)$val;
    }
    // --------------------------------------------------------------------

    /**
     * Get column name
     *
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }
    // --------------------------------------------------------------------

    /**
     * Set column name
     *
     * @param string $val
     */
    public function setColumn($val)
    {
        $this->column = (string)$val;
    }

    // --------------------------------------------------------------------

    /**
     * Get default value
     *
     * The default value is stablished at PHP level, not database.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
    // --------------------------------------------------------------------

    /**
     * Set default value
     *
     * The default value is stablished at PHP level. This is not the database
     * DEFAULT subclause.
     *
     * @param mixed $val
     */
    public abstract function setDefaultValue($val);
    // --------------------------------------------------------------------

    /**
     * Get auto field status.
     * 'Serial' primary keys are good examples of this.
     *
     * @return boolean
     */
    public function getAuto()
    {
        return $this->auto;
    }
    // --------------------------------------------------------------------

    /**
     * Set auto field status.
     *
     * @param boolean $val
     */
    public function setAuto($val)
    {
        $this->auto = (bool)$val;
    }

    // --------------------------------------------------------------------

    public function getSize()
    {
        return $this->defaultSize;
    }

    // --------------------------------------------------------------------

    /**
     * @return int
     */
    public function getDefaultSize()
    {
        return $this->defaultSize;
    }

    // --------------------------------------------------------------------

    /**
     * @param int $defaultSize
     */
    public function setDefaultSize($defaultSize)
    {
        $this->defaultSize = (int)$defaultSize;
    }

    // --------------------------------------------------------------------


}
