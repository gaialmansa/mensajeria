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

use Exception;

/**
 * AutoSchema for PostgreSQL
 *
 * Schema that can build itself from database catalog.
 */
class AutoSchema extends AutoSchemaBase
{

    /**
     * @var string $tableOid
     */
    private $tableOid;

    // --------------------------------------------------------------------

    /**
     * Get inverse foreign keys
     *
     * Searches system catalog for tables that have foreign keys pointing to us.
     *
     * @return array List of \zfx\ForeignKey
     */
    public function calculateRelTables()
    {
        if ($this->tableOid === NULL) {
            return;
        }
        $db   = new DB($this->profile);
        $rels = $db->qa("
                    SELECT      pg_constraint.*,
                                pg_class.relname,
                                array(SELECT attname FROM pg_attribute WHERE pg_attribute.attrelid = pg_constraint.conrelid  AND pg_attribute.attnum = ANY (pg_constraint.conkey)) AS \"lcols\",
                                array(SELECT attname FROM pg_attribute WHERE pg_attribute.attrelid = pg_constraint.confrelid  AND pg_attribute.attnum = ANY (pg_constraint.confkey)) AS \"fcols\"
                    FROM        pg_constraint, pg_class
                    WHERE       pg_constraint.confrelid = $this->tableOid
                    AND         pg_constraint.contype = 'f'
                    AND         pg_class.oid = pg_constraint.conrelid
                ");

        $fks = array();
        if ($rels) {
            foreach ($rels as $rel) {
                $fk = new ForeignKey();
                $fk->setRelation($rel['relname']);
                $fk->setName($rel['conname']);
                $fk->setLocalColumns(DB::parseArray($rel['lcols']));
                $fk->setForeignColumns(DB::parseArray($rel['fcols']));
                $fks[$rel['relname']] = $fk;
            }
        }
        $this->setRelTables($fks);
    }

    // --------------------------------------------------------------------

    /**
     * Automatic self construction
     *
     * Initializes object and builds from the system catalog by loading
     * the table definition.
     */
    protected function autoConstruct($raw = FALSE)
    {
        if ($raw) {
            $this->raw['table'] = $this->getRelationName();
        }

        // Get column names
        $db     = new DB($this->profile);
        $etable = $db->escape($this->getRelationName());

        $dbconf = a(Config::get('pg'), $this->profile);
        if (va($dbconf) && array_key_exists('dbNamespace', $dbconf)) {
            $nsp  = $db->escape($dbconf['dbNamespace']);
            $cond = "AND pg_class.relnamespace = (select pg_namespace.oid from pg_namespace where pg_namespace.nspname = '$nsp')";
        }
        else {
            $cond = '';
        }

        $columns = $db->qa("
                SELECT      pg_attribute.*,
                            format_type(pg_attribute.atttypid, pg_attribute.atttypmod) AS \"sql_type\",
                            pg_class.oid AS \"table_oid\",
                            pg_class.relnamespace
                FROM        pg_attribute, pg_class
                WHERE       pg_class.relname = '$etable'
                AND         pg_class.oid = pg_attribute.attrelid
                $cond
                AND         pg_attribute.attisdropped = FALSE
                AND         pg_attribute.attnum > 0
            ", 'attnum');

        if ($columns) {
            $this->tableOid = (int)a(current($columns), 'table_oid');

            // Get fields
            foreach ($columns as $field) {
                $this->setField($field['attname'], $this->createField($field));
                if ($raw) {
                    $this->raw['columns'][$field['attname']] = [
                        'type'    => $field['sql_type'],
                        'null'    => ($field['attnotnull'] != 't'),
                        'default' => ($field['atthasdef'] == 't'),
                        'indexed' => FALSE
                    ];
                }
            }

            // Get primary keys and indexes
            $pk  = array();
            $ind = $db->qa("
                    SELECT      pg_index.*
                    FROM        pg_index
                    WHERE       pg_index.indrelid = {$this->tableOid};
                ");
            if ($ind) {
                foreach ($ind as $i) {
                    $keysArray = explode(' ', $i['indkey']);
                    foreach ($keysArray as $key) {
                        if ($key > 0) {
                            $this->setIndex(a(a($columns, $key), 'attname'));
                            if ($raw) {
                                $this->raw['columns'][a(a($columns, $key), 'attname')]['indexed'] = TRUE;
                            }
                            // Primary key? Add to PK list
                            if ($i['indisprimary'] == 't') {
                                $pk[] = a(a($columns, $key), 'attname');
                            }
                        }
                    }
                }
            }
            $this->setPrimaryKey($pk);
            if ($raw) {
                $this->raw['pk'] = $pk;
            }

            // Get foreign keys
            $foreignKeys = $db->qa("
                    SELECT      pg_constraint.*,
                                pg_class.relname,
                                array(SELECT attname FROM pg_attribute WHERE pg_attribute.attrelid = pg_constraint.conrelid  AND pg_attribute.attnum = ANY (pg_constraint.conkey)) AS \"lcols\",
                                array(SELECT attname FROM pg_attribute WHERE pg_attribute.attrelid = pg_constraint.confrelid  AND pg_attribute.attnum = ANY (pg_constraint.confkey)) AS \"fcols\"
                    FROM        pg_constraint, pg_class
                    WHERE       pg_constraint.conrelid = {$this->tableOid}
                    AND         pg_constraint.contype = 'f'
                    AND         pg_class.oid = pg_constraint.confrelid
                ");

            if ($foreignKeys) {
                $fks = array();
                foreach ($foreignKeys as $fk) {
                    $constraintID = $fk['conname'];
                    if (!a($fks, $constraintID)) {
                        $fks[$constraintID] = new ForeignKey();
                    }
                    $fks[$constraintID]->setName($constraintID);
                    $fks[$constraintID]->setRelation($fk['relname']);
                    $fks[$constraintID]->setLocalColumns(DB::parseArray($fk['lcols']));
                    $fks[$constraintID]->setForeignColumns(DB::parseArray($fk['fcols']));
                }
                $this->setFks($fks);
                if ($raw) {
                    $this->raw['fk'] = $fks;
                }
            }
        }
        else {
            \zfx\Debug::devError('Sin columnas en AutoSchema. ¿Existe la tabla?');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Create Field from system catalog info
     *
     * Creates a child of \zfx\Field object using provided info from system
     * catalog.
     *
     * @param array $fieldInfo Info data from pg_attribute table
     * @return null | Field
     */
    private function createField(array $fieldInfo)
    {
        $res = NULL;
        preg_match('/^([a-z ]+)(?:\((.+?)\))?/u', $fieldInfo['sql_type'], $res);
        switch (a($res, 1)) {
            case 'smallint':
            {
                $f = new FieldInt();
                $f->setLowerLimit(-32768);
                $f->setUpperLimit(32767);
                break;
            }
            case 'integer':
            {
                $f = new FieldInt();
                $f->setLowerLimit(-2147483648);
                $f->setUpperLimit(2147483647);
                break;
            }
            case 'bigint':
            {
                $f = new FieldInt();
                $f->setLowerLimit(-9223372036854775808);
                $f->setUpperLimit(9223372036854775807);
                break;
            }
            case 'boolean':
            {
                $f = new FieldBoolean();
                break;
            }
            case 'character varying':
            case 'character':
            {
                $f = new FieldString();
                $f->setMax((int)a($res, 2));
                break;
            }
            case 'double precision':
            {
                $f = new FieldReal();
                break;
            }
            case 'numeric':
            {
                $f = new FieldNum();
                break;
            }
            case 'real':
            {
                $f = new FieldReal();
                break;
            }
            case 'text':
            {
                $f = new FieldString();
                $f->setMax(-1);
                break;
            }
            case 'date':
            {
                $f = new FieldDate();
                break;
            }
            case 'time':
            case 'time without time zone':
            case 'time with time zone':
            {
                $f = new FieldTime();
                break;
            }
            case 'timestamp':
            case 'timestamp without time zone':
            case 'timestamp with time zone':
            {
                $f = new FieldDateTime();
                break;
            }
            case 'bytea': // @@@ Esto se puede mejorar creando un nuevo tipo en vez de Fieldstring
            {
                $f = new FieldString();
                $f->setMax(-1);
                break;
            }
            case 'geometry':
            case 'geography':
                $f = new FieldString();
                $f->setMax(-1);
                break;
            default:
            {
                Debug::show('Tipo de columna no admitida:');
                Debug::show(a($res, 1));
                Debug::show($fieldInfo);
                die;
            }
        }

        // The ID will be the column name
        $f->setColumn($fieldInfo['attname']);

        // Columns with a defined default value generates fields with not-required attribute set
        if ($fieldInfo['atthasdef'] == 't') {
            $f->setAuto(TRUE);
        }

        // Is NULL allowed?
        if ($fieldInfo['attnotnull'] == 't') {
            $f->setRequired(TRUE);
        }

        return $f;
    }

    // --------------------------------------------------------------------


}
