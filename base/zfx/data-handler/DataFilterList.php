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
 * Class DataFilterList
 *
 * Esta clase representa un control de búsqueda consistente en una lista de valores a elegir para cierto campo.
 *
 * Se representa normalmente como
 *
 * [X] Nombre campo [______|V]  Donde el drop-down es una lista de valores que puede adoptar el campo.
 *
 * @package zfx
 */
class DataFilterList
{

    public $label;
    public $values;
    public $id;
    public $col;
    public $selected;
    public $class;
    public $action;
    public $datas;
    public $loc;
    public $selectClass;

    /**
     * @var string If found, generate an option group.
     */
    public $groupId;

    // --------------------------------------------------------------------

    public function __construct($id)
    {
        $this->id      = $id;
        $this->groupId = Config::get('zafCrudSelectOptgroupPrefix');
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
        if (!trueEmpty($this->getSelected())) {
            $attrChecked   = 'checked="checked"';
            $selectedClass = 'zjDhFilterActive';
        }
        else {
            $attrChecked   = 'disabled';
            $selectedClass = '';
        }
        if (count($this->getValues()) > 10) {
            $selectClass = 'zjExtSelect';
        }
        else {
            $selectClass = '';
        }
        $selectClass .= ' ' . $this->selectClass;


        $viewData = array(
            'attrData'      => $attrData,
            'attrChecked'   => $attrChecked,
            'selectedClass' => $selectedClass,
            'selectClass'   => $selectClass,
            'df'            => $this
        );
        View::direct('zaf/' . Config::get('admTheme') . '/data-filter-list', $viewData);
    }

    // --------------------------------------------------------------------

    public function getFormID()
    {
        return '_dhf_' . $this->id;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = (string)$label;
    }

    // --------------------------------------------------------------------

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    // --------------------------------------------------------------------

    /**
     * @param array $values
     */
    public function setValues(array $values = NULL)
    {
        $this->values = $values;
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
    public function getCol()
    {
        return $this->col;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $col
     */
    public function setCol($col)
    {
        $this->col = (string)$col;
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
     * @return Localizer
     */
    public function getLoc()
    {
        return $this->loc;
    }

    // --------------------------------------------------------------------

    /**
     * @param Localizer $loc
     */
    public function setLoc(Localizer $loc)
    {
        $this->loc = $loc;
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getSelectClass()
    {
        return $this->selectClass;
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $selectClass
     */
    public function setSelectClass($selectClass): void
    {
        $this->selectClass = $selectClass;
    }

    // --------------------------------------------------------------------


}
