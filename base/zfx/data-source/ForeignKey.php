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
 * Foreign Key class
 */
class ForeignKey
{

    /**
     * Relation name (usually the name of the table)
     * @var string $relation
     */
    private $relation;

    /**
     * Foreign key name
     * @var string $name
     */
    private $name;

    /**
     * List of local columns implied in the foreign key
     * @var array $localColumns
     */
    private $localColumns;

    /**
     * List of foreign columns implied in the foreign key
     * @var array $foreignColumns
     */
    private $foreignColumns;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * The FK will be empty by default.
     */
    public function __construct()
    {
        $this->relation       = NULL;
        $this->name           = NULL;
        $this->localColumns   = array();
        $this->foreignColumns = array();
    }
    // --------------------------------------------------------------------

    /**
     * Get relation name
     *
     * The relation name is usually the name of the table.
     *
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }
    // --------------------------------------------------------------------

    /**
     * Set relation name
     *
     * The relation name is usually the name of the table.
     *
     * @param string $val
     */
    public function setRelation($val)
    {
        $this->relation = (string)$val;
    }
    // --------------------------------------------------------------------

    /**
     * Get foreign key name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    // --------------------------------------------------------------------

    /**
     * Set foreign key name
     *
     * @param string $val
     */
    public function setName($val)
    {
        $this->name = (string)$val;
    }
    // --------------------------------------------------------------------

    /**
     * Add column to local columns list
     *
     * @param string value
     */
    public function addLocalColumn($value)
    {
        $this->localColumns[] = $value;
    }
    // --------------------------------------------------------------------

    /**
     * Get the list of local columns
     *
     * @return array
     */
    public function getLocalColumns()
    {
        return $this->localColumns;
    }
    // --------------------------------------------------------------------

    /**
     * Set the local columns list
     *
     * @param array $value
     */
    public function setLocalColumns(array $value = NULL)
    {
        $this->localColumns = $value;
    }
    // --------------------------------------------------------------------

    /**
     * Add column to foreign columns list
     *
     * @param string value
     */
    public function addForeignColumn($value)
    {
        $this->foreignColumns[] = $value;
    }
    // --------------------------------------------------------------------

    /**
     * Get the list of foreign columns
     *
     * @return array
     */
    public function getForeignColumns()
    {
        return $this->foreignColumns;
    }
    // --------------------------------------------------------------------

    /**
     * Set the foreign columns list
     *
     * @param array $value
     */
    public function setForeignColumns(array $value = NULL)
    {
        $this->foreignColumns = $value;
    }
    // --------------------------------------------------------------------

    /**
     * Is a field included on my list?
     *
     * Checks if a column is on the list
     *
     * @param string $columnName
     * @return boolean
     */
    public function includesField($columnName)
    {
        return in_array($columnName, $this->localColumns);
    }
    // --------------------------------------------------------------------

    /**
     * Get the related local column of a provided foreign column
     *
     * @param string $fCol
     * @return string
     */
    public function getLocalOf($fCol)
    {
        $pos = array_search($fCol, $this->foreignColumns);
        if ($pos !== FALSE) {
            return a($this->localColumns, $pos);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Get the related foreign column of a provided local column
     *
     * @param string $fCol
     * @return string
     */
    public function getForeignOf($lCol)
    {
        $pos = array_search($lCol, $this->localColumns);
        if ($pos !== FALSE) {
            return a($this->foreignColumns, $pos);
        }
    }
    // --------------------------------------------------------------------
}
