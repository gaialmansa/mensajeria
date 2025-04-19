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
 * Class DataFilterBox
 *
 * Esta clase representa un control de búsqueda en múltiples campos de la forma:
 *
 * Buscar [____________]  en  [_______|V]
 *
 * @package zfx
 */
class DataFilterBox
{
    /**
     * @var array
     */
    public $fields;
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $searchValue;
    /**
     * @var string
     */
    public $selected;
    /**
     * @var string
     */
    public $action;
    /**
     * @var string
     */
    public $class;
    /**
     * @var array
     */
    public $datas;
    /**
     * @var string
     */
    public $searchLabel;
    /**
     * @var string
     */
    public $inLabel;
    /**
     * @var array
     */
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
        $formID = $this->getFormID();

        /*
         * Atributos 'data-' que nos pueden haber especificado.
         */
        if ($this->getDatas()) {
            $attrData = HtmlTools::dataAttr($this->getDatas());
        }
        else {
            $attrData = '';
        }

        /*
         * Si el buscador tiene un campo seleccionado entonces lo damos como activo.
         */
        $classActive = '';
        if (trueEmpty($this->selected)) {
            $classActive = '';
        }
        elseif (!trueEmpty($this->searchValue)) {
            $classActive = 'zjDhFilterActive';
        }

        $viewData = array(
            'attrData'    => $attrData,
            'classActive' => $classActive,
            'formID'      => $formID,
            'df'          => $this,
        );
        View::direct('zaf/' . Config::get('admTheme') . '/data-filter-box', $viewData);
    }

    // --------------------------------------------------------------------

    public function getFormID()
    {
        return '_dhfb_' . $this->id;
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
     * @return string
     */
    public function getSearchValue()
    {
        return $this->searchValue;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $searchValue
     */
    public function setSearchValue($searchValue)
    {
        $this->searchValue = (string)$searchValue;
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
    public function getSearchLabel()
    {
        return $this->searchLabel;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $searchLabel
     */
    public function setSearchLabel($searchLabel)
    {
        $this->searchLabel = (string)$searchLabel;
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
    public function setJsh(array $jsh = NULL)
    {
        $this->jsh = $jsh;
    }

    // --------------------------------------------------------------------
}
