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
 * Data Viewer
 */
abstract class DataViewer
{

    /**
     *
     * @var TableView $tv ;
     */
    protected $tableView;

    /**
     *
     * @var string $sectionId
     */
    protected $sectionId;

    /**
     *
     * @var string $sectionTemplate
     */
    protected $sectionTemplate;

    /**
     *
     * @var array $formData
     */
    protected $formData;

    /**
     * @var array $viewData
     */
    protected $viewData;

    // --------------------------------------------------------------------

    public function __construct()
    {
        $this->tableView       = NULL;
        $this->sectionId       = NULL;
        $this->sectionTemplate = NULL;
        $this->viewData        = NULL;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return TableView
     */
    public function getTableView()
    {
        return $this->tableView;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param TableView $value
     */
    public function setTableView(TableView $value = NULL)
    {
        $this->tableView = $value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setSectionId($value)
    {
        $this->sectionId = (string)$value;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public function getSectionTemplate()
    {
        return $this->sectionTemplate;
    }
    // --------------------------------------------------------------------

    /**
     *
     * @param string $value
     */
    public function setSectionTemplate($value)
    {
        $this->sectionTemplate = (string)$value;
    }

    // --------------------------------------------------------------------

    public function getViewData()
    {
        return $this->viewData;
    }

    // --------------------------------------------------------------------

    public function setViewData(array $viewData = NULL)
    {
        $this->viewData = $viewData;
    }

    // --------------------------------------------------------------------

    public function addViewData($key, $data)
    {
        $this->viewData[$key] = $data;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @param View $view
     */
    public function addSection(View &$view)
    {
        if (va($this->viewData)) {
            $data = array_merge($this->calcViewData(), $this->viewData);
        }
        else {
            $data = $this->calcViewData();
        }

        if (!trueEmpty($this->sectionTemplate)) {
            $view->addSection($this->sectionId, $this->sectionTemplate, $data);
        }
    }

    // --------------------------------------------------------------------

    /**
     *
     * @return array
     */
    abstract protected function calcViewData();

    // --------------------------------------------------------------------

    public function direct(array $moreData = NULL)
    {
        $data = array_merge((array)$this->calcViewData(), (array)$this->viewData, (array)$moreData);
        if (!trueEmpty($this->sectionTemplate)) {
            View::direct($this->sectionTemplate, $data);
        }
    }

    // --------------------------------------------------------------------
}
