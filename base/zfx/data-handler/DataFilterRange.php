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
 * @package data-handler
 */

namespace zfx;

/**
 * Class DataFilterRange
 *
 * Esta clase representa un control de búsqueda de un rango en múltiples campos de la forma:
 *
 * Desde [________]  hasta  [________]  en  [_______|V]
 *
 * @package zfx
 */
class DataFilterRange
{
    // Propiedades
    public $fields;
    public $searchFromValue;
    public $searchToValue;
    public $selected;
    public $action;
    public $class;
    public $editClass;
    public $datas;
    public $fromLabel;
    public $toLabel;
    public $inLabel;
    public $id;
    public $jsh;

    // --------------------------------------------------------------------

    public function __construct($id)
    {
        $this->id  = $id;
        $this->jsh = array();
    }

    // --------------------------------------------------------------------

    public function render()
    {
        if ($this->getDatas()) {
            $attrData = HtmlTools::dataAttr($this->getDatas());
        }
        else {
            $attrData = '';
        }
        if (trueEmpty($this->getSelected())) {
            $classActive = '';
        }
        else {
            $classActive = 'zjDhFilterActive';
        }

        $viewData = array(
            'attrData'    => $attrData,
            'classActive' => $classActive,
            'df'          => $this
        );
        View::direct('zaf/' . Config::get('admTheme') . '/data-filter-range', $viewData);
    }

    // --------------------------------------------------------------------

    public function getFormID()
    {
        return '_dhfr_' . $this->id;
    }

    // --------------------------------------------------------------------

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $fields
     */
    public function setFields(array $fields = NULL)
    {
        $this->fields = $fields;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getSearchFromValue()
    {
        return $this->searchFromValue;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $searchFromValue
     */
    public function setSearchFromValue($searchFromValue)
    {
        $this->searchFromValue = (string)$searchFromValue;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getSearchToValue()
    {
        return $this->searchToValue;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $searchToValue
     */
    public function setSearchToValue($searchToValue)
    {
        $this->searchToValue = (string)$searchToValue;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getSelected()
    {
        return $this->selected;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $selected
     */
    public function setSelected($selected)
    {
        $this->selected = (string)$selected;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = (string)$action;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = (string)$class;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getEditClass()
    {
        return $this->editClass;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $editClass
     */
    public function setEditClass($editClass)
    {
        $this->editClass = (string)$editClass;
    }

    // --------------------------------------------------------------------

    /**
     * @return array
     */
    public function getDatas()
    {
        return $this->datas;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $datas
     */
    public function setDatas(array $datas = NULL)
    {
        $this->datas = $datas;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getFromLabel()
    {
        return $this->fromLabel;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $fromLabel
     */
    public function setFromLabel($fromLabel)
    {
        $this->fromLabel = (string)$fromLabel;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getToLabel()
    {
        return $this->toLabel;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $toLabel
     */
    public function setToLabel($toLabel)
    {
        $this->toLabel = (string)$toLabel;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getInLabel()
    {
        return $this->inLabel;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $inLabel
     */
    public function setInLabel($inLabel)
    {
        $this->inLabel = (string)$inLabel;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = (string)$id;
    }

    // --------------------------------------------------------------------

    /**
     * @return array
     */
    public function getJsh()
    {
        return $this->jsh;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $jsh
     */
    public function setJsh($jsh)
    {
        $this->jsh = $jsh;
    }

    // --------------------------------------------------------------------
}
