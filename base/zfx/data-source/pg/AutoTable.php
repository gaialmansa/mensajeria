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
 * AutoTable for PostgreSQL
 *
 * An AutoTable class builds itself from a given Scheme instance.
 */
class AutoTable extends AutoTableBase
{
    // --------------------------------------------------------------------

    /**
     * Read a set of records
     *
     * Returns a list of rows. An offset and limit can be specified.
     *
     * @param integer $offset
     * @param integet $limit
     * @param boolean $iterate Controls how the set of records will be returned:
     * If TRUE, return an interable object. If FALSE, returns a map.
     * @param string $key
     * @param string $pack Params for qa()
     */
    public function readRS($offset = 0, $limit = 0, $iterate = FALSE, $key = '', $pack = '')
    {
        if ($this->sqlList) {
            $offset = (int)$offset;
            $limit  = (int)$limit;
            if ($limit > 0) {
                $limitSQL = "\nOFFSET $offset\nLIMIT $limit";
            }
            else {
                $limitSQL = '';
            }

            $filter = '';
            if ($this->sqlFilter) {
                $filter = "\nWHERE " . $this->sqlFilter . "\n";
            }

            $db = new DB($this->profile);
            if (!$iterate) {
                return $db->qa($this->sqlList . ' ' . $filter . ' ' . $this->sqlSortBy . ' ' . $limitSQL, $key, $pack);
            }
            else {
                $db->qs($this->sqlList . ' ' . $filter . ' ' . $this->sqlSortBy . ' ' . $limitSQL);
                return $db;
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Insert record
     *
     * Performs an INSERT command of a single row using given data.
     *
     * @param array $data RecordSet.
     * @param string|array $returnColumn Name of column to be returned
     *
     * @return mixed
     * If no data were provided it will return NULL.
     * If there were missing required fields returns an array of them.
     * FALSE on error.
     * On success and a return column were specified, it will return its value.
     * Otherwise it will return TRUE.
     */
    public function insertR($data, $returnColumn = NULL)
    {
        $this->error = '';
        if (!$data) {
            return NULL;
        }
        $db = new DB($this->profile);
        $db->setIgnoreErrors(TRUE);
        $qtable = DB::quote($this->schema->getRelationName());
        $sql    = "
            INSERT INTO $qtable
        ";
        $values = $this->genSQL_values($data);
        if (is_array($values)) {
            $this->error = "Faltan campos: [" . implode(',', $values) . "]";
            return $values;
        }
        if ($values) {
            $sql .= $values;
            if (is_string($returnColumn)) {
                $sql .= " RETURNING " . $db->quote($returnColumn);
                $res = $db->qr($sql, $returnColumn);
            }
            elseif (is_array($returnColumn) && count($returnColumn)) {
                $cols = '';
                foreach ($returnColumn as $r) {
                    $cols .= $db->quote($r);
                    $cols .= ',';
                }
                $cols = rtrim($cols, ',');
                $sql  .= " RETURNING $cols";
                $res  = $db->qr($sql);
            }
            else {
                $res = $db->q($sql);
            }
            $this->error = $db->getLastError();
            return $res;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Self-initialize using the current Scheme
     */
    protected function createFromSchema()
    {
        // Exit on no fields
        if (!$this->schema->getFields()) {
            return;
        }
        $db           = new DB($this->profile);
        $this->qtable = $db->quote($this->schema->getRelationName());

        // Construct SQL query for listing
        $this->sqlList = "SELECT ";
        $numCols       = $this->schema->count();
        $i             = 0;
        foreach ($this->schema->getFields() as $column) {
            if (is_a($column, '\zfx\FieldBoolean')) {
                $this->sqlList .= $db->quote($column->getColumn()) . '::integer';
            }
            else {
                if (is_a($column, '\zfx\FieldDateTime')) {
                    $this->sqlList .= 'to_char(' . $db->quote($column->getColumn()) . ",'YYYY-MM-DD HH24:MI:SS') AS " . $db->quote($column->getColumn());
                }
                else {
                    if (is_a($column, '\zfx\FieldTime')) {
                        $this->sqlList .= 'to_char(' . $db->quote($column->getColumn()) . ",'HH24:MI:SS') AS " . $db->quote($column->getColumn());
                    }
                    else {
                        $this->sqlList .= $db->quote($column->getColumn());
                    }
                }
            }
            if ($i < $numCols - 1) {
                $this->sqlList .= ', ';
            }
            $i++;
        }
        $this->sqlList .= " FROM {$this->qtable}\n";

        // Construct SQL query for counting rows
        $rowcount       = $db->quote('rowcount');
        $this->sqlCount = "SELECT COUNT(*) AS $rowcount FROM {$this->qtable}\n";

        // Construct SQL query for deletion
        $this->sqlDelete = "DELETE FROM {$this->qtable}\n";
    }

    // --------------------------------------------------------------------
}
