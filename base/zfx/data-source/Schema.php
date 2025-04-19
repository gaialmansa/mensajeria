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
 * Relation Schema
 *
 * This class serves as a database table or view scheme representation
 */
class Schema
{

    /**
     * Primary keys list
     *
     * @var array $primaryKey
     */
    private $primaryKey;

    /**
     * List of Fields (columns)
     *
     * @var array $fields
     */
    private $fields;

    /**
     * List of Foreign Keys
     *
     * @var array $fks
     */
    private $fks;

    /**
     * List of related tables (tables that have foreign keys pointing to us)
     *
     * @var array $relTables
     */
    private $relTables;

    /**
     * Name of the table or view (relation)
     *
     * @var string $relation
     */
    private $relation;

    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->primaryKey = array();
        $this->fields     = array();
        $this->relTables  = array();
        $this->fks        = array();
    }

    // --------------------------------------------------------------------
    // Access methods
    // --------------------------------------------------------------------

    /**
     * Get relation name (name of the table or view)
     *
     * @return string
     */
    public function getRelationName()
    {
        return $this->relation;
    }

    // --------------------------------------------------------------------

    /**
     * Set relation name
     *
     * @param string $value
     */
    public function setRelationName($value)
    {
        $this->relation = (string)$value;
    }

    // --------------------------------------------------------------------

    /**
     * Get field info list
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    // --------------------------------------------------------------------

    public function getFieldsKeys($requiredOnly = FALSE)
    {
        if (!$requiredOnly) {
            return array_keys($this->fields);
        }
        else {
            $res = [];
            foreach ($this->fields as $k => $field) {
                if ($field->getRequired() && !$field->getAuto()) {
                    $res[] = $k;
                }
            }
            return $res;
        }
    }

    // --------------------------------------------------------------------

    public function getFieldsKeysExcept(array $fieldList = NULL)
    {
        return array_diff(array_keys($this->fields), (array)$fieldList);
    }

    // --------------------------------------------------------------------

    /**
     * Set field info list
     *
     * @param $array Map array. Use column names as keys
     */
    public function setFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);
        // Do not accept NULL field IDs
        if (isset($this->fields[''])) {
            unset($this->fields['']);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Get foreign keys list
     *
     * @return array
     */
    public function getFks()
    {
        return $this->fks;
    }

    // --------------------------------------------------------------------

    /**
     * Set foreign keys list
     *
     * @param array $value
     */
    public function setFks(array $value)
    {
        $this->fks = $value;
    }

    // --------------------------------------------------------------------

    /**
     * Get related tables list. A related table is a table that has foreign
     * key restrictions pointing to us.
     *
     * @return array of \zfx\ForeignKey
     */
    public function getRelTables()
    {
        return $this->relTables;
    }

    // --------------------------------------------------------------------

    /**
     * Set related tables list. A related table is a table that has foreign
     * key restrictions pointing to us.
     *
     * @param array $val
     */
    public function setRelTables(array $val)
    {
        $this->relTables = $val;
    }

    // --------------------------------------------------------------------

    /**
     * Set field definition
     *
     * @param string $fieldId Field ID (usually name of column)
     *
     * @param Field $fieldInfo Field information object
     */
    public function setField($fieldId, Field $fieldInfo)
    {
        if (!trueEmpty($fieldId) && $fieldInfo) {
            $this->fields[$fieldId] = $fieldInfo;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Get field definition
     *
     * Use this method in order to access a field without getting the whole list
     * @param string $fieldId
     * @return Field
     */
    public function getField($fieldId)
    {
        return a($this->fields, $fieldId);
    }

    // --------------------------------------------------------------------

    /**
     * Set field as indexed
     *
     * @param string $fieldId Field ID
     */
    public function setIndex($fieldId)
    {
        if (a($this->fields, $fieldId)) {
            $this->fields[$fieldId]->setIndex(TRUE);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Count fields
     *
     * @return integer
     */
    public function count()
    {
        return nwcount($this->fields);
    }

    // --------------------------------------------------------------------
    // Delegation and convenience methods
    // --------------------------------------------------------------------

    /**
     * Remove some fields
     *
     * @param array $fieldIds List of field keys to be deleted
     *
     * @see removeFieldsBut()
     */
    public function removeFields(array $fieldIds)
    {
        foreach ($fieldIds as $f) {
            unset($this->fields[$f]);
        }
    }

    /**
     * Remove all fields except some
     *
     * @param array $fieldIds List of field keys to keep
     */
    public function removeFieldsExcept(array $fieldIds)
    {
        foreach (array_keys($this->fields) as $k) {
            if (!in_array($k, $fieldIds)) {
                unset($this->fields[$k]);
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Check if field exists
     *
     * @param string $fieldID Field ID
     * @return boolean
     */
    public function checkField($fieldID)
    {
        return (isset($this->fields[$fieldID]));
    }

    // --------------------------------------------------------------------

    /**
     * Required field test
     *
     * Tests a input record. If there are in scheme required fields that
     * are not present in that record, returns an array of missing fields IDs.
     *
     * @param array $fieldSet Record set
     * @return array Empty if all required fields are present
     */
    public function checkReqFieldSet($fieldSet)
    {
        $required = array();
        foreach ($this->fields as $id => $field) {
            if ($field->getRequired() && !in_array($id, $fieldSet) && !$field->getAuto()) {
                $required[] = $id;
            }
        }
        return $required;
    }

    // --------------------------------------------------------------------

    /**
     * Extract primary key list from record
     *
     * @param array $recordSet
     * @return array subrecord
     */
    public function extractPk($recordSet)
    {
        $pk = array();
        foreach ($this->getPrimaryKey() as $k) {
            if (a($recordSet, $k)) {
                $pk[$k] = $recordSet[$k];
            }
            else {
                return NULL;
            }
        }
        return $pk;
    }

    // --------------------------------------------------------------------

    /**
     * Remove all keys (primary, unique) from record
     *
     * @param array|null $recordSet
     */
    public function removeKeys(array $recordSet = NULL)
    {
        foreach ($this->getPrimaryKey() as $kc) {
            if (array_key_exists($kc, $recordSet)) {
                unset($recordSet[$kc]);
            }
            // #### TODO: uniques
            /*foreach ($this->fields as $id=>$f) {
                if (array_key_exists($id, $recordSet) && ...) {
                    unset($recordSet[$id]);
                }
            */
        }
        return $recordSet;
    }

    // --------------------------------------------------------------------

    /**
     * Get primary keys list
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    // --------------------------------------------------------------------

    /**
     * Set primary keys list
     *
     * @param array $val
     */
    public function setPrimaryKey(array $val)
    {
        $this->primaryKey = $val;
    }

    // --------------------------------------------------------------------

    /**
     * Get indexed fields field from our field list
     *
     * @return array field list
     */
    public function getIndexedFields()
    {
        $res = array();
        foreach ($this->fields as $k => $f) {
            if ($f->getIndex()) {
                $res[$k] = $f;
            }
        }
        return $res;
    }

    // --------------------------------------------------------------------

    /**
     * Importar propiedades importantes como claves primarias, relaciones e índices desde otro schema.
     * Es útil cuando estamos construyendo un esquema correspondiente a una VIEW que es un superconjunto de una TABLE.
     *
     * @param Schema $schema
     * @return void
     */
    public function importViewInfo(Schema $schema)
    {
        $this->setPrimaryKey($schema->getPrimaryKey());
        $this->setFks($schema->getFks());
        foreach ($schema->getIndexedFields() as $k => $f) {
            $this->getField($k)->setIndex(TRUE);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Devuelve si la clave primaria es automática o no.
     */
    public function pkIsAuto()
    {
        if ($this->primaryKey) foreach ($this->primaryKey as $fieldId) {
            if ($this->getField($fieldId)->getAuto()) return TRUE;
        }
        return FALSE;
    }
}
