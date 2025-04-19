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
 * AutoTableBase: abstract base class for AutoTable
 *
 * An AutoTable class builds itself from a given Scheme instance.
 */
abstract class AutoTableBase extends Table
{

    /**
     * DB connection profile
     * @var string $profile
     */
    protected $profile;

    // Generated SQL sentences

    /**
     * SQL Query for couting rows
     * @var string $sqlCount
     */
    protected $sqlCount;

    /**
     * SQL Query for reading a record set
     * @var string $sqlList
     */
    protected $sqlList;

    /**
     * SQL Query for deleting a record
     * @var string $sqlDelete
     */
    protected $sqlDelete;

    /**
     * Quoted table name
     * @var string
     */
    protected $qtable;

    // --------------------------------------------------------------------

    /**
     * Contructor
     *
     * @param Schema $schema Schema object needed
     * @param string $profile DB connection profile
     */
    public function __construct(Schema $schema, $profile = NULL)
    {
        parent::__construct();
        $this->setProfile($profile);
        $this->setSchema($schema);
        $this->createFromSchema();
    }
    // --------------------------------------------------------------------

    /**
     * Self-initialize using the current Scheme
     */
    abstract protected function createFromSchema();
    // --------------------------------------------------------------------

    /**
     * Get DB connection profile
     * @return string|NULL
     */
    public function getProfile()
    {
        return $this->profile;
    }
    // --------------------------------------------------------------------

    /**
     * Set DB connection profile
     * @param string $value
     */
    public function setProfile($value)
    {
        $this->profile = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     * Count rows
     *
     * Return the number of rows available.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->sqlCount) {
            $db     = new DB($this->profile);
            $filter = '';
            if ($this->sqlFilter) {
                $filter = "\nWHERE " . $this->sqlFilter . "\n";
            }
            return $db->qr($this->sqlCount . $filter, 'rowcount');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Read row
     *
     * Reads a row from the table. The key or keys must be specified.
     * Returns a map or a single value.
     *
     * @param mixed $ids Scalar or array that identifies the row (matches primary keys)
     * @param string $field If specified, return the value of that field only.
     * @return mixed
     */
    public function readR($ids, $field = NULL)
    {
        if ($this->sqlList) {
            $db    = new DB($this->profile);
            $where = $this->genSQL_whereBy($ids);
            if (!$where) {
                return;
            }
            return $db->qr($this->sqlList . $where, $field);
        }
    }
    // --------------------------------------------------------------------

    /**
     * Primary keys to WHERE clause
     *
     * Gets a WHERE... clause from a single primary key or an array
     *
     * @param mixed $ids
     * @return string
     */
    protected function genSQL_whereBy($ids)
    {
        $ids = $this->sanitizePk($ids);
        if (!$ids) {
            return '';
        }
        $conditions = array();
        foreach ($this->schema->getPrimaryKey() as $pk) {
            $condition    = $this->schema->getField($pk)->cast($ids[$pk]);
            $conditions[] = "({$this->qtable}.$pk = $condition)";
        }
        if ($conditions) {
            return "\nWHERE " . implode(' AND ', $conditions);
        }
        else {
            return '';
        }
    }
    // --------------------------------------------------------------------

    /**
     * Sanitize Primary Key
     *
     * Checks against the current scheme the provided primary key.
     *
     * @param array|string $ids The primary key. A single value or an array.
     * @return array Always returns an array with the same provided primary key values. Empty if error.
     */
    protected function sanitizePk($ids)
    {
        // Let's see how many PK are there
        $pkCount = nwcount($this->schema->getPrimaryKey());
        if ($pkCount > 0) {
            // First check: the number of the specified PK must match the Scheme PK number.
            if (is_array($ids)) {
                if (array_diff($this->schema->getPrimaryKey(), array_keys($ids))) {
                    return array();
                }
                else {
                    return $ids;
                }
            } // If a scalar was provided and there is a single Scheme PK, we will adapt it as an array.
            else {
                if ($pkCount == 1) {
                    return array(a($this->schema->getPrimaryKey(), 0) => $ids);
                }
                else {
                    return array();
                }
            }
        }
        else {
            return array();
        }
    }

    // --------------------------------------------------------------------

    public function updateR($ids, array $data)
    {
        if (!$data) {
            return 0;
        }
        $db = new DB($this->profile);
        $db->setIgnoreErrors(TRUE);
        $where = $this->genSQL_whereBy($ids);
        if ($where == '') {
            return FALSE;
        }
        $qtable = DB::quote($this->schema->getRelationName());
        $sql    = "
            UPDATE $qtable
            SET
        ";
        $set    = $this->genSQL_set($data);
        if ($set) {
            $sql .= $set . $where;
            return $db->q($sql);
        }
    }
    // --------------------------------------------------------------------

    /**
     * Generate SET... subclause
     *
     * Creates a SET a=b, c=d, ... subclause using a Record Set (actually a hash map).
     * It uses the current Scheme for column names and data types.
     * @param array $data Record Set
     * @return string
     */
    protected function genSQL_set(array $data)
    {
        $set = array();
        foreach ($data as $k => $v) {
            if ($this->schema->checkField($k)) {
                $set[] = DB::quote($k) . " = " . $this->schema->getField($k)->cast($v);
            }
        }
        if ($set) {
            return implode(",\n", $set);
        }
        else {
            return '';
        }
    }
    // --------------------------------------------------------------------

    /**
     * Borrar fila
     *
     * Ejecuta el comando DELETE en una fila única
     *
     * @param mixed $ids Escalar o array que identifica la fila (debería ser la clave primaria)
     * @return int|boolean 0 si no hay datos, FALSE en caso de error. TRUE si todo fue bien.
     */
    public function deleteR($ids)
    {
        if ($this->sqlDelete) {
            $db    = new DB($this->profile);
            $where = $this->genSQL_whereBy($ids);
            if ($where == '') {
                return FALSE;
            }
            $db->setIgnoreErrors(TRUE);
            return $db->q($this->sqlDelete . $where);
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get current query string for reading and listing
     *
     * @return string
     */
    public function getSqlList()
    {
        return $this->sqlList;
    }
    // --------------------------------------------------------------------

    /**
     * Set current query string for reading and listing
     * @param string $sql
     */
    public function setSqlList($sql)
    {
        $this->sqlList = (string)$sql;
    }


    // --------------------------------------------------------------------

    /**
     * Get current query string for deleting
     *
     * @return string
     */
    public function getSqlDelete()
    {
        return $this->sqlDelete;
    }
    // --------------------------------------------------------------------

    /**
     * Set current query string for deleting
     * @param string $sql
     */
    public function setSqlDelete($sql)
    {
        $this->sqlDelete = (string)$sql;
    }
    // --------------------------------------------------------------------

    /**
     * Get current query string for counting
     *
     * @return string
     */
    public function getSqlCount()
    {
        return $this->sqlCount;
    }
    // --------------------------------------------------------------------

    /**
     * Set current query string for counting
     * @param string $sql
     */
    public function setSqlCount($sql)
    {
        $this->sqlCount = (string)$sql;
    }
    // --------------------------------------------------------------------

    /**
     * Generate VALUES subclause
     *
     * Creates a (a,b,c) VALUES (v1,v2,v3) ... subclause using a Record Set (actually a hash map).
     * It uses the current Scheme for column names and data types.
     *
     * @param array $data Record Set
     * @return string
     */
    protected function genSQL_values(array $data)
    {
        $requiredFields = $this->schema->checkReqFieldSet(array_keys($data));
        if ($requiredFields) {
            return $requiredFields;
        }
        $cols   = array();
        $values = array();
        foreach ($data as $k => $v) {
            if ($this->schema->checkField($k)) {
                $cols[] = DB::quote($k);
                // @@@ Fix
                $values[] = $this->schema->getField($k)->cast($v, $this->schema->getField($k)->getRequired());
            }
        }
        if ($values) {
            return '(' . implode(',', $cols) . ') VALUES (' . implode(',', $values) . ')';
        }
        else {
            return NULL;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Generate WHERE condition
     *
     * Creates a ('field' = value) expression intended to be used on clauses
     * like WHERE.
     *
     * If no $field is provided, a chained OR expression will be returned
     * using all indexed columns available in the Scheme.
     *
     * @param mixed $value Right part
     * @param string $field Left part
     */
    protected function genSQL_cond($value, $field = NULL)
    {
        $sql = '';
        if ($field && $this->schema->checkField($field)) {
            $sql = '( ' . $this->schema->getField($field)->getFilter($value) . ' )';
        }
        else {
            $chunks = array();
            foreach ($this->schema->getFields() as $field) {
                if ($field->getIndex()) {
                    $chunks[] = $field->getFilter($value);
                }
            }
            $sql = '( ' . implode(' OR ', $chunks) . ' )';
        }
        return $sql;
    }
    // --------------------------------------------------------------------
}
