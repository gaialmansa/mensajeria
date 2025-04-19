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
 * Abstract class Data Source Table
 */
abstract class Table
{

    /**
     * Table or view scheme
     * @var Schema $schema
     */
    protected $schema;

    /**
     * SQL filter
     * @var string $sqlFilter
     */
    protected $sqlFilter;

    /**
     * SORT BY SQL clause to be applied
     * @var string $sqlSortBy
     */
    protected $sqlSortBy;

    /**
     * @var string $error Descripción del error cometido en la última operación
     */
    public $error;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Schema $schema A Schema object can be applied
     */
    public function __construct(Schema $schema = NULL)
    {
        if ($schema) {
            $this->setSchema($schema);
        }
        else {
            $this->schema = new Schema();
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get reference of the Schema used
     *
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }
    // --------------------------------------------------------------------

    /**
     * Set Schema
     *
     * @param Schema $value
     */
    public function setSchema(Schema $value)
    {
        $this->schema = $value;
    }
    // --------------------------------------------------------------------

    /**
     * Count all available rows
     */
    abstract public function count();


    // --------------------------------------------------------------------

    /**
     * Read Record
     *
     * Read row from the table.
     * Returns a simple map as "Record Set".
     * If $field is specified then return that field only.
     *
     * @param $ids Primary key ID or array of IDs
     */
    abstract public function readR($ids, $field = NULL);


    // --------------------------------------------------------------------

    /**
     * Update row
     *
     * Performs an UPDATE command of a single row using given data.
     *
     * @param mixed $ids Scalar or array that identifies the row (matches primary keys)
     * @param array $data RecordSet.
     * @return int|boolean 0 if no data. FALSE if provided $ids are wrong or if there was an error. TRUE if success.
     */
    abstract public function updateR($ids, array $data);


    // --------------------------------------------------------------------

    /**
     * Insert record
     *
     * Performs an INSERT command of a single row using given data.
     *
     * @param array $data RecordSet.
     * @param string $returnColumn Name of column to be returned
     *
     * @return mixed
     * If no data were provided it will return NULL.
     * If there were missing required fields returns an array of them.
     * FALSE on error.
     * On success and a return column were specified, it will return its value.
     * Otherwise it will return TRUE.
     */
    // @@@ ¿Y si devuelve un array porque el campo es un array?
    abstract public function insertR($data, $col = NULL);


    // --------------------------------------------------------------------

    /**
     * Delete Record
     *
     * Delete a row from the table.
     *
     * @param mixed | array $ids Primary key ID or array of IDs
     */
    abstract public function deleteR($ids);

    /**
     * Read Record Set
     *
     * Read a row set from the table.
     *
     * @param integer $offset Offset (0 = first)
     * @param integer $limit Number of rows to be read (0 = all)
     * @return BD|array A DB Object to iterate results or a complete record set as map
     */
    abstract public function readRS($offset = 0, $limit = 0, $iterate = FALSE);

    // --------------------------------------------------------------------

    /**
     * Get current SQL filter set
     *
     * @return string
     */
    public function getSqlFilter()
    {
        return $this->sqlFilter;
    }
    // --------------------------------------------------------------------

    /**
     * Set SQL filter to be used in multi-row operations
     *
     * @param string $val SQL "WHERE" clause but without "WHERE" token
     */
    public function setSqlFilter($val)
    {
        $this->sqlFilter = $val;
    }
    // --------------------------------------------------------------------

    /**
     * Get current SQL order by clause
     *
     * @return string
     */
    public function getSqlSortBy()
    {
        return $this->sqlSortBy;
    }
    // --------------------------------------------------------------------

    /**
     * Set SQL "ORDER BY" clause to be used in multi-row operations
     *
     * @param string $value
     */
    public function setSqlSortBy($value)
    {
        $this->sqlSortBy = (string)$value;
    }
    // --------------------------------------------------------------------
}
