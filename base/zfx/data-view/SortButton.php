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

class SortButton
{

    // --------------------------------------------------------------------

    private $ascUrl;
    private $desUrl;
    private $nulUrl;
    private $textToASC;
    private $textToDESC;
    private $textToNUL;
    public  $ascData;
    public  $desData;
    public  $nulData;

    /**
     *
     * @var TableView $tableView ;
     */
    private $tableView;

    // --------------------------------------------------------------------


    public function __construct()
    {
        $this->ascUrl     = 'javascript:void(0)';
        $this->desUrl     = 'javascript:void(0)';
        $this->nulUrl     = 'javascript:void(0)';
        $this->textToASC  = 'A';
        $this->textToDESC = 'D';
        $this->textToNUL  = 'X';
        $this->ascData    = NULL;
        $this->desData    = NULL;
        $this->nulData    = NULL;
        $this->tableView  = NULL;
    }

    // --------------------------------------------------------------------

    public function getAscUrl()
    {
        return $this->ascUrl;
    }

    // --------------------------------------------------------------------

    public function setAscUrl($value)
    {
        $this->ascUrl = (string)$value;
    }

    // --------------------------------------------------------------------

    public function getDesUrl()
    {
        return $this->desUrl;
    }

    // --------------------------------------------------------------------

    public function setDesUrl($value)
    {
        $this->desUrl = (string)$value;
    }

    // --------------------------------------------------------------------

    public function getNulUrl()
    {
        return $this->nulUrl;
    }

    // --------------------------------------------------------------------

    public function setNulUrl($value)
    {
        $this->nulUrl = (string)$value;
    }

    // --------------------------------------------------------------------

    public function setAscData(array $data = NULL)
    {
        $this->ascData = $data;
    }

    // --------------------------------------------------------------------

    public function setDesData(array $data = NULL)
    {
        $this->desData = $data;
    }

    // --------------------------------------------------------------------

    public function setNulData(array $data = NULL)
    {
        $this->nulData = $data;
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

    public function renderButton($fieldId)
    {
        if ($this->tableView->getFieldView($fieldId)) {
            $sortable = $this->tableView->getFieldView($fieldId)->getSortable();
        }
        else {
            return;
        }
        if (!$sortable) {
            return;
        }
        if ($this->tableView->getOrderedFieldSet()) {
            $pos = $this->tableView->getOrderedFieldSet()->getFieldPos($fieldId);
        }
        else {
            $pos = 0;
        }
        if ($pos > 0) {
            if ($this->tableView->getOrderedFieldSet()->getField($fieldId)) {
                $sortButtonClass = '_ascSort';
                $href            = sprintf($this->desUrl, StrFilter::safeEncode($fieldId)); // Next action is "descending order"
                $data            = sprintf(HtmlTools::dataAttr($this->getDesData()), StrFilter::safeEncode($fieldId));
                $text            = $this->textToDESC;
            }
            else {
                $sortButtonClass = '_descSort';
                $href            = sprintf($this->nulUrl, StrFilter::safeEncode($fieldId)); // Next action is "no order"
                $data            = sprintf(HtmlTools::dataAttr($this->getNulData()), StrFilter::safeEncode($fieldId));
                $text            = $this->textToNUL;
            }
        }
        else {
            $sortButtonClass = '_noSort';
            $href            = sprintf($this->ascUrl, StrFilter::safeEncode($fieldId)); // Next action is "ascending order"
            $data            = sprintf(HtmlTools::dataAttr($this->getAscData()), StrFilter::safeEncode($fieldId));
            $text            = $this->textToASC;
        }
        $viewData = array(
            'attrData'        => $data,
            'href'            => $href,
            'sortButtonClass' => $sortButtonClass,
            'text'            => $text,
            'pos'             => $pos
        );
        View::direct('zaf/' . Config::get('admTheme') . '/sortbutton', $viewData);
    }

    // --------------------------------------------------------------------

    public function getDesData()
    {
        return $this->desData;
    }

    // --------------------------------------------------------------------

    public function getNulData()
    {
        return $this->nulData;
    }

    // --------------------------------------------------------------------

    public function getAscData()
    {
        return $this->ascData;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTextToASC()
    {
        return $this->textToASC;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $textToASC
     */
    public function setTextToASC($textToASC)
    {
        $this->textToASC = (string)$textToASC;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTextToDESC()
    {
        return $this->textToDESC;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $textToDESC
     */
    public function setTextToDESC($textToDESC)
    {
        $this->textToDESC = (string)$textToDESC;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTextToNUL()
    {
        return $this->textToNUL;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $textToNUL
     */
    public function setTextToNUL($textToNUL)
    {
        $this->textToNUL = (string)$textToNUL;
    }

    // --------------------------------------------------------------------

}
