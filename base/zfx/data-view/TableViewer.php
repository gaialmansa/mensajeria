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

/**
 * Table Viewer
 */
class TableViewer extends DataViewer
{

    const SELECTOR_TYPE_NONE     = 0;
    const SELECTOR_TYPE_SINGLE   = 1;
    const SELECTOR_TYPE_MULTIPLE = 2;
    /**
     *
     * @var Paginator $paginator
     */
    protected $paginator;
    /**
     *
     * @var SortButton $sortButton
     */
    protected $sortButton;
    /**
     *
     * @var integer $selectorType ;
     */
    protected $selectorType;
    /**
     *
     * @var boolean $allSelected ;
     */
    protected $allSelected;
    /**
     *
     * @var RowActions $rowActions ;
     */
    protected $rowActions;
    /**
     *
     * @var callable $rowSelectionChecker
     */
    protected $rowSelectionChecker;
    protected $stayOnLast;

    // --------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->paginator           = NULL;
        $this->sortButton          = NULL;
        $this->selectorType        = self::SELECTOR_TYPE_NONE;
        $this->allSelected         = FALSE;
        $this->rowActions          = NULL;
        $this->rowSelectionChecker = NULL;
        $this->stayOnLast          = FALSE;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return integer
     */
    public function getSelectorType()
    {
        return $this->selectorType;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param integer $value
     */
    public function setSelectorType($value)
    {
        $this->selectorType = (int)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return boolean
     */
    public function getAllSelected()
    {
        if ($this->selectorType == self::SELECTOR_TYPE_MULTIPLE) {
            return $this->allSelected;
        }
        else {
            return NULL;
        }
    }
// --------------------------------------------------------------------

    /**
     *
     * @param boolean $value
     */
    public function setAllSelected($value)
    {
        $this->allSelected = (boolean)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param array $row
     * @return boolean
     */
    public function rowIsSelected(array &$row = NULL)
    {
        if (is_callable($this->rowSelectionChecker)) {
            return call_user_func($this->rowSelectionChecker, $row);
        }
        else {
            return FALSE;
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get row selection checker callback
     * @return callable
     */
    public function getRowSelectionChecker()
    {
        return $this->rowSelectionChecker;
    }
// --------------------------------------------------------------------

    /**
     * Set row selection checker callback
     * @param callable $value
     */
    public function setRowSelectionChecker($value)
    {
        $this->rowSelectionChecker = $value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return SortButton
     */
    public function getSortButton()
    {
        return $this->sortButton;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param SortButton $value
     */
    public function setSortButton(SortButton $value = NULL)
    {
        $this->sortButton = $value;
        if ($value && $this->tableView) {
            $value->setTableView($this->tableView);
        }
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param Paginator $value
     */
    public function setPaginator(Paginator $value = NULL)
    {
        $this->paginator = $value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param TableView $value
     */
    public function setTableView(TableView $value = NULL)
    {
        parent::setTableView($value);
        if ($value && $this->sortButton) {
            $this->sortButton->setTableView($this->tableView);
        }
    }
    // --------------------------------------------------------------------

    /**
     * Get a valid string for attribute value="..." in order to
     * identify that row.
     *
     * Returns the complete PK packed.
     * It's used in selecting rows using, for example, checkboxes.
     *
     * @param array $row Record Set
     * @return null | string
     */
    public function getKeyValue(array $row = NULL)
    {
        $pk = $this->tableView->getTable()->getSchema()->extractPk($row);
        return PKView::pack($pk);
    }

    // --------------------------------------------------------------------

    public function getRowActions()
    {
        return $this->rowActions;
    }

    // --------------------------------------------------------------------

    public function setRowActions(RowActions $actions)
    {
        $this->rowActions = $actions;
    }

    // --------------------------------------------------------------------

    public function createRowActions($actionList)
    {
        $this->rowActions = new RowActions();
        $this->rowActions->setActions($actionList);
        $this->rowActions->setSchema($this->tableView->getTable()->getSchema());
    }

    // --------------------------------------------------------------------

    public function getStayOnLast()
    {
        return $this->stayOnLast;
    }

    // --------------------------------------------------------------------

    public function setStayOnLast($value)
    {
        $this->stayOnLast = (bool)$value;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    protected function calcViewData()
    {
        if ($this->tableView) {
            $data = array();

            $data['_viewer'] = $this;

            // Total nah!
            if ($this->tableView->getOrderedFieldSet()) {
                $this->tableView->getTable()->setSqlSortBy(SQLGen::getOrderBy($this->tableView->getOrderedFieldSet()->getFieldSet(),
                    $this->tableView->getOrderMapping()));
            }


            if ($this->paginator) {
                $rowCount = $this->tableView->getTable()->count();
                $this->paginator->setNumItems($this->tableView->getTable()->count());
                if ($this->stayOnLast) {
                    $this->paginator->setCurrentPage($this->paginator->getNumPages());
                }
                $data['_paginator'] = $this->paginator->generate();
                $data['_data']      = $this->tableView->getTable()->readRS($this->paginator->getOffsetForCurrentPage(),
                    $this->paginator->numItemsForCurrentPage());
                $data['_rowCount']  = $rowCount;
            }
            else {
                $data['_data']     = $this->tableView->getTable()->readRS();
                $data['_rowCount'] = nwcount($data['_data']);
            }

            if ($this->rowActions) {
                $data['_rowActions'] = $this->rowActions;
            }

            return $data;
        }
        else {
            return NULL;
        }
    }
    // --------------------------------------------------------------------
}
