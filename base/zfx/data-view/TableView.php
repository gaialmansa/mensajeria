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

class TableView extends Localized
{

    /**
     * La tabla
     * @var Table $table
     */
    private $table;

    /**
     * Los campos que van a intervenir
     * @var array $fieldViews
     */
    private $fieldViews;

    /**
     *
     * @var OrderedFieldSet
     */
    private $orderedFieldSet;

    /**
     *
     * @var array $orderMapping ;
     */
    private $orderMapping;

    /**
     * @var array $groups
     */
    private $groups;


    // --------------------------------------------------------------------

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table           = NULL;
        $this->fieldViews      = NULL;
        $this->orderedFieldSet = NULL;
        $this->orderMapping    = NULL;
    }
    // --------------------------------------------------------------------
    // Constructores estáticos
    // --------------------------------------------------------------------

    /**
     *
     * @param Table $table
     * @param Localizer $localizer
     * @return TableView
     */
    public static function auto(Table $table, $localizer = NULL)
    {
        // La tabla será la especificada
        $tv = new TableView();
        $tv->set_localizer($localizer);
        $tv->setTable($table);


        // A partir del esquema de la tabla vamos a construir un conjunto de FieldViews
        $fields = $table->getSchema()->getFields();
        foreach ($fields as $field) {
            $tv->addfieldView(MapperFieldView::viewize($field, $tv->getLocalizer()));
        }


        // Devolvemos el objeto creado
        return $tv;
    }
    // --------------------------------------------------------------------
    // Métodos de acceso
    // --------------------------------------------------------------------

    /**
     * Add a FieldView object to the list
     *
     * @param FieldView $fv
     */
    public function addfieldView(FieldView $fv, $id = NULL)
    {
        if ($fv->getField() && !$id) {
            $this->fieldViews[$fv->getField()->getColumn()] = $fv;
        }
        else {
            $this->fieldViews[$id] = $fv;
        }
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param Table $value
     */
    public function setTable(Table $value = NULL)
    {
        $this->table = $value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    public function getFieldViews()
    {
        return $this->fieldViews;
    }
    // --------------------------------------------------------------------
    // Shortcuts
    // --------------------------------------------------------------------

    /**
     *
     * @param array $value
     */
    public function setFieldViews(array $value = NULL)
    {
        $this->fieldViews = $value;
    }
    // --------------------------------------------------------------------

    /**
     * Set all fields editable TRUE/FALSE.
     * @param boolean $value
     * @param boolean $disableAutoKeys if TRUE, always set automatic primary keys as not editable
     */
    public function setAllEditable($value, $disableAutoKeys = FALSE)
    {
        if ($this->fieldViews) {
            foreach ($this->fieldViews as &$field) {
                $field->setEditable((boolean)$value);
            }
            if ($disableAutoKeys) {
                foreach ($this->table->getSchema()->getPrimaryKey() as $pk) {
                    if ($this->table->getSchema()->getField($pk)->getAuto() && av($this->fieldViews, $pk)) {
                        $this->fieldViews[$pk]->setEditable(FALSE);
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------

    public function disableInline()
    {
        foreach ($this->fieldViews as $field) {
            if ($field instanceof FieldViewFormElement) {
                $field->setInlineEdit(FALSE);
            }
        }
    }

    // --------------------------------------------------------------------

    public function setAllDisplayOnly($value)
    {
        if ($this->fieldViews) {
            foreach ($this->fieldViews as &$field) {
                $field->setDisplayOnly((boolean)$value);
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param array $fieldIds
     */
    public function removeFields(array $fieldIds = NULL)
    {
        if ($fieldIds) {
            foreach ($fieldIds as $f) {
                unset($this->fieldViews[$f]);
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param array $fieldIds
     */
    public function removeFieldsExcept(array $fieldIds)
    {
        foreach (array_keys($this->fieldViews) as $k) {
            if (!in_array($k, $fieldIds)) {
                unset($this->fieldViews[$k]);
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param array $fieldKeys
     */
    public function setFieldOrder(array $fieldKeys = NULL)
    {
        if (va($fieldKeys)) {
            $newOrder = array();
            foreach ($fieldKeys as $fieldKey) {
                $fieldView = a($this->fieldViews, $fieldKey);
                if ($fieldView) {
                    $newOrder[$fieldKey] = $fieldView;
                    unset($this->fieldViews[$fieldKey]);
                }
            }
            $this->fieldViews = array_merge($newOrder, $this->fieldViews);
        }
    }
    // --------------------------------------------------------------------

    /**
     * Count fields
     *
     * @return integer
     */
    public function getFieldCount()
    {
        return nwcount($this->fieldViews);
    }
    // --------------------------------------------------------------------

    /**
     * Set fieldviews labels
     */
    public function setLabels(array $labels)
    {
        foreach ($labels as $id => $label) {
            $fv = a($this->fieldViews, $id);
            if ($fv) {
                $fv->setLabel($label);
            }
        }
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return OrderedFieldSet
     */
    public function getOrderedFieldSet()
    {
        return $this->orderedFieldSet;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param OrderedFieldSet $value
     */
    public function setOrderedFieldSet(OrderedFieldSet $value = NULL)
    {
        $this->orderedFieldSet = $value;
    }
    // --------------------------------------------------------------------

    /**
     * A common operation: promote field to FieldViewStringMap and fill
     * with data from a table
     */
    public function stringMapFromTable(
        $idField,
        $tableName,
        $colForID,
        $colForName,
        $sortByID = FALSE,
        $ascending = TRUE,
        $filter = ''
    )
    {
        $field = FieldViewStringMap::promote($this->getFieldView($idField));
        if (is_array($tableName)) {
            $table = new AutoTable(new AutoSchema($tableName[0], $tableName[1]), $tableName[1]);
        }
        else {
            $table = new AutoTable(new AutoSchema($tableName));
        }
        if ($sortByID) {
            $table->setSqlSortBy(" ORDER BY " . DB::quote($colForID) . ($ascending ? ' ASC ' : 'DESC'));
        }
        else {
            $table->setSqlSortBy(" ORDER BY " . DB::quote($colForName) . ($ascending ? ' ASC ' : 'DESC'));
        }
        if (!trueEmpty($filter)) {
            $table->setSqlFilter($filter);
        }
        $field->setMap($table->readRS(0, 0, FALSE, $colForID, $colForName));
        $this->setFieldView($idField, $field);
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $key
     * @return FieldView
     */
    public function getFieldView($key)
    {
        if (array_key_exists($key, $this->fieldViews)) {
            return $this->fieldViews[$key];
        }
    }

    // --------------------------------------------------------------------

    /**
     *
     * @param string $key
     * @param FieldView $f
     */
    public function setFieldView($key, FieldView $f)
    {
        $this->fieldViews[$key] = $f;
    }
    // --------------------------------------------------------------------

    /**
     * A common operation: promote field to FieldViewStringMap and fill
     * with data from an array
     */
    public function stringMapFromArray($idField, array $data = NULL, $size = 0)
    {
        $field = FieldViewStringMap::promote($this->getFieldView($idField));
        $field->setMap($data);
        $field->getField()->setDefaultSize($size);
        $this->setFieldView($idField, $field);
        return $field;
    }

    // --------------------------------------------------------------------


    public function toImageField($idField, $baseUrl, $table = '', $column = '')
    {
        $field = FieldViewImage::promote($this->getFieldView($idField));
        if ($table == '') {
            $table = $this->getTable()->getSchema()->getRelationName();
        }
        if ($column == '') {
            $column = $idField;
        }
        $table  = StrFilter::getID($table);
        $column = StrFilter::getID($column);
        $field->setTable($table);
        $field->setColumn($column);
        $field->setBaseUrl($baseUrl);
        $this->setFieldView($idField, $field);
    }

    // --------------------------------------------------------------------


    public function toFileField($idField, $baseUrl, $table = '', $column = '')
    {
        $field = FieldViewFile::promote($this->getFieldView($idField));
        if ($table == '') {
            $table = $this->getTable()->getSchema()->getRelationName();
        }
        if ($column == '') {
            $column = $idField;
        }
        $table  = StrFilter::getID($table);
        $column = StrFilter::getID($column);
        $field->setTable($table);
        $field->setColumn($column);
        $field->setBaseUrl($baseUrl);
        $this->setFieldView($idField, $field);
    }

    // --------------------------------------------------------------------

    public function toSketchField($idField, $baseUrl, $table = '', $column = '')
    {
        $field = FieldViewSketch::promote($this->getFieldView($idField));
        if ($table == '') {
            $table = $this->getTable()->getSchema()->getRelationName();
        }
        if ($column == '') {
            $column = $idField;
        }
        $table  = StrFilter::getID($table);
        $column = StrFilter::getID($column);
        $field->setTable($table);
        $field->setColumn($column);
        $field->setBaseUrl($baseUrl);
        $this->setFieldView($idField, $field);
        $this->promote($idField . '_vec', '\zfx\FieldViewHidden');
    }

    // --------------------------------------------------------------------

    /**
     * Promover un campo de un tipo a otro.
     *
     * @param $idField ID Del campo a ser
     * @param $fieldView Nueva clase (por ejemplo FieldViewVideoStream)
     * @return FieldView la instancia del campo
     */
    public function promote($idField, $fieldView)
    {
        $field = $fieldView::promote($this->getFieldView($idField));
        $this->setFieldView($idField, $field);
        return $field;
    }

    // --------------------------------------------------------------------

    /**
     * Set field as pickable from other table.
     * @param type $idField
     * @param type $sourceTable
     * @param type $sourceDisplay
     * @param type $loadSourceURL
     */
    public function pickFromTable($idField, $sourceTable, $sourceDisplay, $loadSourceURL, $expand = FALSE, $profile = '')
    {
        $pf        = FieldViewDBPicker::promote($this->getFieldView($idField));
        $elementID = '_DBPicker_' . StrFilter::safeEncode($idField);
        $pf->setElementID($elementID);
        $pf->setData(array(
            'adm-action'      => 'load',
            'adm-load-target' => '#' . $elementID . '_target',
            'adm-load-source' => $loadSourceURL,
            'adm-options'     => ($expand ? 'expand' : '')
        ));
        $pf->setSource($sourceTable, $sourceDisplay, $profile);
        if (is_string($sourceDisplay)) {
            $fieldDisplay = $pf->getSourceTable()->getSchema()->getField($sourceDisplay);
            if (is_a($fieldDisplay, '\zfx\FieldString')) {
                $pf->setMaxLength($fieldDisplay->getMax());
            }
        }
        else {
            $l = (int)a($sourceDisplay, 'length');
            if ($l) {
                $pf->setMaxLength($l);
            }
        }
        $this->setFieldView($idField, $pf);
    }

    // --------------------------------------------------------------------

    public function getOrderMapping()
    {
        return $this->orderMapping;
    }

    // --------------------------------------------------------------------

    public function setOrderMapping(array $value = NULL)
    {
        $this->orderMapping = $value;
    }

    // --------------------------------------------------------------------

    public function getFieldOrder()
    {
        if (is_array($this->fieldViews)) {
            return array_keys($this->fieldViews);
        }
        else {
            return array();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set groups
     */
    public function setGroups(array $groups = NULL)
    {
        $this->groups = $groups;
    }

    // --------------------------------------------------------------------

    /**
     * Get groups
     *
     * @return array|null
     */
    public function getGroups()
    {
        return $this->groups;
    }

    // --------------------------------------------------------------------

    /**
     * Get single group
     *
     * @param string $group
     * @return array
     */
    public function getGroup($group)
    {
        if ($this->groups === NULL) {
            return NULL;
        }
        if (array_key_exists($group, $this->groups)) {
            return $this->groups[$group];
        }
        else {
            return NULL;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Returns the group set or an array of a unique groups if no groups were defined.
     * @return array
     */
    public function getEffectiveGroups()
    {
        if ($this->groups === NULL) {
            $uniqueGroup = new TableViewGroup(array_keys($this->fieldViews));
            return array($uniqueGroup);
        }
        else {
            return $this->groups;
        }
    }

    // --------------------------------------------------------------------

    public function hasExpandedGroups()
    {
        if ($this->groups) {
            foreach ($this->groups as $g) {
                if ($g->expanded) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * Ordena los campos usando un callback por su valor.
     *
     * La función callback(a,b) debe devolver:
     * -1 si a<b
     *  0 si a=b
     *  1 si a>b
     *
     * @param callBack $sortCallBack
     */
    public function usortFields($sortCallBack)
    {
        usort($this->fieldViews, $sortCallBack);
    }

    // --------------------------------------------------------------------

    /**
     * Ordena los campos usando un callback por su clave (id)
     *
     * La función callback(a,b) debe devolver:
     * -1 si a<b
     *  0 si a=b
     *  1 si a>b
     *
     * @param callBack $sortCallBack
     */
    public function uksortFields($sortCallBack)
    {
        uksort($this->fieldViews, $sortCallBack);
    }

    // --------------------------------------------------------------------

    /**
     * Devuelve la lista de IDs de los FieldViews activos.
     *
     * @param array|null $exclude Si se le pasa una lista, éstos son excluidos del resultado.
     * @return array
     */
    public function getFieldList(array $exclude = NULL): array
    {
        if (is_null($exclude)) {
            return array_keys($this->fieldViews);
        }
        else {
            return array_diff(array_keys($this->fieldViews), $exclude);
        }
    }

    // --------------------------------------------------------------------

}
